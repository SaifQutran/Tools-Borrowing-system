<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolType;
use App\Models\Hall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Generator;

class ToolController extends Controller
{
    public function index()
    {
        $tools = Tool::with('toolType')->latest()->paginate(20);
        return view('admin.tools.index', compact('tools'));
    }

    public function create()
    {
        $toolTypes = ToolType::all();
        $halls = Hall::all();
        return view('admin.tools.create', compact('toolTypes', 'halls'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tool_type_id' => 'required|exists:tool_types,id',
            'attributes' => 'nullable|array',
        ]);
        $toolType = ToolType::findOrFail($validated['tool_type_id']);

        $count = Tool::where('tool_type_id', $validated['tool_type_id'])->count() + 1;

        $validated['code'] = $toolType->shortcut .'-'. $count;

        $tool = Tool::create($validated);
        
        // Generate QR code
        $this->generateQRCode($tool);

        return redirect()->route('admin.tools.index')->with('success', 'تم إضافة الأداة بنجاح');
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'tools_file' => 'required|file|max:10240',
        ]);

        $file = $validated['tools_file'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, ['xlsx', 'csv'], true)) {
            return back()->withErrors(['tools_file' => 'Please upload an .xlsx or .csv file.']);
        }

        try {
            $rows = $this->readImportRows($file->getRealPath(), $extension);
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['tools_file' => $exception->getMessage()]);
        }

        if (count($rows) === 0) {
            return back()->withErrors(['tools_file' => 'The selected file does not contain any rows.']);
        }

        $columnMap = $this->detectImportColumns($rows[0]);
        $startIndex = $columnMap ? 1 : 0;
        $columnMap ??= ['name' => 0, 'type' => 1];
        $imported = 0;
        $skipped = [];
        $nextNumbers = [];

        DB::transaction(function () use ($rows, $startIndex, $columnMap, &$imported, &$skipped, &$nextNumbers) {
            for ($index = $startIndex; $index < count($rows); $index++) {
                $rowNumber = $index + 1;
                $toolName = trim((string) ($rows[$index][$columnMap['name']] ?? ''));
                $toolTypeName = trim((string) ($rows[$index][$columnMap['type']] ?? ''));

                if ($toolName === '' && $toolTypeName === '') {
                    continue;
                }

                if ($toolName === '' || $toolTypeName === '') {
                    $skipped[] = "Row {$rowNumber}: missing tool name or tool type.";
                    continue;
                }

                $toolType = $this->findToolType($toolTypeName);
                if (! $toolType) {
                    $skipped[] = "Row {$rowNumber}: tool type '{$toolTypeName}' does not exist.";
                    continue;
                }

                $tool = Tool::create([
                    'name' => $toolName,
                    'tool_type_id' => $toolType->id,
                    'code' => $this->generateToolCode($toolType, $nextNumbers),
                    'status' => 'available',
                ]);

                $this->generateQRCode($tool);
                $imported++;
            }
        });

        if ($imported === 0) {
            return back()
                ->withErrors(['tools_file' => 'No tools were imported.'])
                ->with('import_errors', array_slice($skipped, 0, 20));
        }

        return redirect()
            ->route('admin.tools.index')
            ->with('success', "Imported {$imported} tools successfully.")
            ->with('import_errors', array_slice($skipped, 0, 20));
    }

    public function downloadImportTemplate()
    {
        if (! class_exists(\ZipArchive::class)) {
            return back()->withErrors(['tools_file' => 'The ZipArchive PHP extension is required to download the Excel template.']);
        }

        $toolTypes = ToolType::orderBy('name')->pluck('name')->all();
        $path = storage_path('app/tools_import_template_' . uniqid() . '.xlsx');

        $this->createToolsImportTemplate($path, $toolTypes);

        return response()
            ->download($path, 'tools_import_template.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend(true);
    }

    public function edit(Tool $tool)
    {
        $toolTypes = ToolType::all();
        $halls = Hall::all();
        return view('admin.tools.edit', compact('tool', 'toolTypes', 'halls'));
    }

    public function update(Request $request, Tool $tool)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tool_type_id' => 'required|exists:tool_types,id',
            'code' => 'required|string|unique:tools,code,' . $tool->id,
            'attributes' => 'nullable|array',
            'status' => 'required|in:available,borrowed',
        ]);

        $tool->update($validated);

        return redirect()->route('admin.tools.index')->with('success', 'تم تحديث الأداة بنجاح');
    }

    public function destroy(Tool $tool)
    {
        $tool->delete();
        return redirect()->route('admin.tools.index')->with('success', 'تم حذف الأداة بنجاح');
    }

    public function downloadQr(Tool $tool)
    {
        $this->generateQRCode($tool);
        $path = storage_path('app/public/' . $tool->qr_code_path);

        return response()->streamDownload(function () use ($path) {
            readfile($path);
        }, 'QR_' . $tool->name . '.png', [
            'Content-Type' => 'image/png',
        ]);
    }

    public function showQr(Tool $tool)
    {
        $this->generateQRCode($tool);
        
        $path = storage_path('app/public/' . $tool->qr_code_path);
        
        if (file_exists($path)) {
            return response()->file($path, [
                'Content-Type' => 'image/png',
            ]);
        }
        
        abort(404);
    }

    private function createToolsImportTemplate(string $path, array $toolTypes): void
    {
        $zip = new \ZipArchive();

        if ($zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create the Excel template.');
        }

        $zip->addFromString('[Content_Types].xml', $this->xlsxContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->xlsxRootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->xlsxWorkbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->xlsxWorkbookRelsXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->xlsxTemplateSheetXml($toolTypes));
        $zip->close();
    }

    private function xlsxTemplateSheetXml(array $toolTypes): string
    {
        $rows = [
            '<row r="1">' .
            $this->xlsxInlineCell('A1', 'tool name') .
            $this->xlsxInlineCell('B1', 'tool type') .
            $this->xlsxInlineCell('C1', 'tool types') .
            '</row>',
        ];

        foreach (array_values($toolTypes) as $index => $toolType) {
            $rowNumber = $index + 2;
            $rows[] = '<row r="' . $rowNumber . '">' . $this->xlsxInlineCell('C' . $rowNumber, $toolType) . '</row>';
        }

        $lastToolTypeRow = max(count($toolTypes) + 1, 2);
        $dataValidation = count($toolTypes) > 0
            ? '<dataValidations count="1"><dataValidation type="list" allowBlank="1" showErrorMessage="1" sqref="B2:B1000"><formula1>$C$2:$C$' . $lastToolTypeRow . '</formula1></dataValidation></dataValidations>'
            : '';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<dimension ref="A1:C' . $lastToolTypeRow . '"/>' .
            '<sheetViews><sheetView workbookViewId="0"/></sheetViews>' .
            '<sheetFormatPr defaultRowHeight="15"/>' .
            '<cols><col min="1" max="1" width="28" customWidth="1"/><col min="2" max="2" width="28" customWidth="1"/><col min="3" max="3" width="32" customWidth="1"/></cols>' .
            '<sheetData>' . implode('', $rows) . '</sheetData>' .
            $dataValidation .
            '</worksheet>';
    }

    private function xlsxInlineCell(string $cell, string $value): string
    {
        return '<c r="' . $cell . '" t="inlineStr"><is><t>' . $this->escapeXml($value) . '</t></is></c>';
    }

    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    private function xlsxContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
            '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
            '</Types>';
    }

    private function xlsxRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
            '</Relationships>';
    }

    private function xlsxWorkbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
            '<sheets><sheet name="Tools Import" sheetId="1" r:id="rId1"/></sheets>' .
            '</workbook>';
    }

    private function xlsxWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' .
            '</Relationships>';
    }

    private function readImportRows(string $path, string $extension): array
    {
        return $extension === 'csv'
            ? $this->readCsvRows($path)
            : $this->readXlsxRows($path);
    }

    private function readCsvRows(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return $rows;
        }

        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function readXlsxRows(string $path): array
    {
        if (! class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('The ZipArchive PHP extension is required to import .xlsx files.');
        }

        $zip = new \ZipArchive();

        if ($zip->open($path) !== true) {
            return [];
        }

        $sharedStrings = $this->readXlsxSharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            return [];
        }

        $sheet = simplexml_load_string($sheetXml);
        if ($sheet === false) {
            return [];
        }

        $rows = [];
        foreach ($sheet->sheetData->row as $row) {
            $values = [];

            foreach ($row->c as $cell) {
                $cellRef = (string) $cell['r'];
                $columnIndex = $this->xlsxColumnIndex($cellRef);
                $type = (string) $cell['t'];
                $rawValue = (string) ($cell->v ?? '');

                if ($type === 's') {
                    $values[$columnIndex] = $sharedStrings[(int) $rawValue] ?? '';
                } elseif ($type === 'inlineStr') {
                    $values[$columnIndex] = trim((string) ($cell->is->t ?? ''));
                } else {
                    $values[$columnIndex] = $rawValue;
                }
            }

            if ($values !== []) {
                ksort($values);
                $rows[] = $values;
            }
        }

        return $rows;
    }

    private function readXlsxSharedStrings(\ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');

        if ($xml === false) {
            return [];
        }

        $sharedStringsXml = simplexml_load_string($xml);
        if ($sharedStringsXml === false) {
            return [];
        }

        $strings = [];

        foreach ($sharedStringsXml->si as $stringItem) {
            if (isset($stringItem->t)) {
                $strings[] = trim((string) $stringItem->t);
                continue;
            }

            $text = '';
            foreach ($stringItem->r as $run) {
                $text .= (string) ($run->t ?? '');
            }

            $strings[] = trim($text);
        }

        return $strings;
    }

    private function xlsxColumnIndex(string $cellRef): int
    {
        preg_match('/^[A-Z]+/i', $cellRef, $matches);
        $letters = strtoupper($matches[0] ?? 'A');
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return $index - 1;
    }

    private function detectImportColumns(array $headers): ?array
    {
        $map = [];

        foreach ($headers as $index => $header) {
            $normalized = Str::of((string) $header)->lower()->replace([' ', '_', '-'], '')->toString();

            if (in_array($normalized, ['toolname', 'name', 'اسمالأداة', 'اسم'], true)) {
                $map['name'] = $index;
            }

            if (in_array($normalized, ['tooltype', 'type', 'نوعالأداة', 'النوع', 'نوع'], true)) {
                $map['type'] = $index;
            }
        }

        return isset($map['name'], $map['type']) ? $map : null;
    }

    private function findToolType(string $name): ?ToolType
    {
        return ToolType::get()->first(function (ToolType $toolType) use ($name) {
            return Str::lower(trim($toolType->name)) === Str::lower(trim($name));
        });
    }

    private function generateToolCode(ToolType $toolType, array &$nextNumbers): string
    {
        if (! isset($nextNumbers[$toolType->id])) {
            $nextNumbers[$toolType->id] = Tool::where('tool_type_id', $toolType->id)->count() + 1;
        }

        do {
            $code = $toolType->shortcut . '-' . $nextNumbers[$toolType->id];
            $nextNumbers[$toolType->id]++;
        } while (Tool::where('code', $code)->exists());

        return $code;
    }

    private function generateQRCode(Tool $tool)
    {
        $url = route('tool.show', $tool->id);
        
        $generator = new Generator();
        $qrCode = (string) $generator->format('png')->size(400)->margin(1)->generate($url);

        $qrImage = imagecreatefromstring($qrCode);
        if ($qrImage === false) {
            throw new \RuntimeException('Unable to generate QR code image.');
        }

        $qrWidth = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);
        $padding = 20;
        $textPadding = 12;
        $lineHeight = 24;
        $canvasWidth = $qrWidth + ($padding * 2);
        $canvasHeight = $qrHeight + ($padding * 2) + ($lineHeight * 2) + ($textPadding * 2);

        $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $black = imagecolorallocate($canvas, 0, 0, 0);
        imagefilledrectangle($canvas, 0, 0, $canvasWidth, $canvasHeight, $white);

        imagecopy($canvas, $qrImage, $padding, $padding, 0, 0, $qrWidth, $qrHeight);

        $codeText = $tool->code;
        $textY = $padding + $qrHeight + $textPadding;

        $fontPathCandidates = [
            storage_path('app/fonts/arial.ttf'),
            storage_path('app/fonts/DejaVuSans.ttf'),
            'C:\\Windows\\Fonts\\arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            '/usr/share/fonts/truetype/freefont/FreeSans.ttf',
        ];
        $fontPath = null;
        foreach ($fontPathCandidates as $candidate) {
            if (file_exists($candidate)) {
                $fontPath = $candidate;
                break;
            }
        }

        if ($fontPath && function_exists('imagettftext')) {
            $fontSize = 24;
            $codeBox = imagettfbbox($fontSize, 0, $fontPath, $codeText);
            $codeWidth = abs($codeBox[2] - $codeBox[0]);
            $codeX = max(0, intval(($canvasWidth - $codeWidth) / 2));

            imagettftext($canvas, $fontSize, 0, $codeX, $textY + 16 + $lineHeight, $black, $fontPath, $codeText);
        } else {
            $font = 5;
            $codeLength = function_exists('mb_strlen') ? mb_strlen($codeText) : strlen($codeText);
            $codeX = max(0, intval(($canvasWidth - imagefontwidth($font) * $codeLength) / 2));

            imagestring($canvas, $font, $codeX, $textY + $lineHeight, $codeText, $black);
        }

        ob_start();
        imagepng($canvas);
        $imageData = ob_get_clean();

        imagedestroy($canvas);
        imagedestroy($qrImage);

        $path = 'qr_codes/tool_' . $tool->id . '.png';
        \Storage::disk('public')->put($path, $imageData);

        $tool->update(['qr_code_path' => $path]);
    }
}
