<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Major, Level, Department, Hall, ToolType, LoanDetailKey};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index()
    {
        $majors = Major::all();
        $levels = Level::all();
        $departments = Department::all();
        $halls = Hall::all();
        $toolTypes = ToolType::all();
        $loanDetailKeys = LoanDetailKey::all();

        return view('admin.settings.index', compact('majors', 'levels', 'departments', 'halls', 'toolTypes', 'loanDetailKeys'));
    }

    // Majors
    public function storeMajor(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Major::create($request->only('name'));
        return back()->with('success', 'تم إضافة التخصص بنجاح');
    }

    public function deleteMajor(Major $major)
    {
        $major->delete();
        return back()->with('success', 'تم حذف التخصص بنجاح');
    }

    // Levels
    public function storeLevel(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Level::create($request->only('name'));
        return back()->with('success', 'تم إضافة المستوى بنجاح');
    }

    public function deleteLevel(Level $level)
    {
        $level->delete();
        return back()->with('success', 'تم حذف المستوى بنجاح');
    }

    // Departments
    public function storeDepartment(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Department::create($request->only('name'));
        return back()->with('success', 'تم إضافة القسم بنجاح');
    }

    public function deleteDepartment(Department $department)
    {
        $department->delete();
        return back()->with('success', 'تم حذف القسم بنجاح');
    }

    // Halls
    public function storeHall(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Hall::create($request->only('name'));
        return back()->with('success', 'تم إضافة القاعة بنجاح');
    }

    public function deleteHall(Hall $hall)
    {
        $hall->delete();
        return back()->with('success', 'تم حذف القاعة بنجاح');
    }

    // Tool Types
    public function storeToolType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shortcut' => 'required|string|max:10',
        ]);
        
        ToolType::create($validated);
        return back()->with('success', 'تم إضافة نوع الأداة بنجاح');
    }

    public function deleteToolType(ToolType $toolType)
    {
        $toolType->delete();
        return back()->with('success', 'تم حذف نوع الأداة بنجاح');
    }
    public function storeLoanDetailKey(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'value_type' => 'required|in:text,hall,number',
        ]);

        LoanDetailKey::create($validated);

        return back()->with('success', 'تمت إضافة حقل تفاصيل الطلب بنجاح');
    }

    public function deleteLoanDetailKey(LoanDetailKey $loanDetailKey)
    {
        $loanDetailKey->delete();

        return back()->with('success', 'تم حذف حقل تفاصيل الطلب بنجاح');
    }

    public function import(Request $request, string $type)
    {
        $models = $this->importableSettingsModels();

        if (! isset($models[$type])) {
            abort(404);
        }

        $validated = $request->validate([
            'settings_file' => 'required|file|max:10240',
        ]);

        $file = $validated['settings_file'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, ['xlsx', 'csv'], true)) {
            return back()->withErrors(['settings_file' => 'Please upload an .xlsx or .csv file.']);
        }

        try {
            $rows = $this->readSettingsImportRows($file->getRealPath(), $extension);
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['settings_file' => $exception->getMessage()]);
        }

        $modelClass = $models[$type];
        $imported = 0;
        $skipped = 0;

        foreach ($rows as $index => $row) {
            if ($index === 0 && $this->isSettingsImportHeaderRow($row, $modelClass === ToolType::class)) {
                continue;
            }

            $name = trim((string) ($row[0] ?? ''));

            if ($name === '') {
                continue;
            }

            $shortcut = null;
            if ($modelClass === ToolType::class) {
                $shortcut = trim((string) ($row[1] ?? ''));

                if ($shortcut === '' || strlen($shortcut) > 10) {
                    $skipped++;
                    continue;
                }
            }

            $exists = $modelClass::query()
                ->get()
                ->contains(fn ($record) => Str::lower(trim($record->name)) === Str::lower($name));

            if ($exists) {
                $skipped++;
                continue;
            }

            $data = ['name' => $name];

            if ($modelClass === ToolType::class) {
                $shortcutExists = ToolType::query()
                    ->get()
                    ->contains(fn (ToolType $toolType) => Str::lower(trim($toolType->shortcut)) === Str::lower($shortcut));

                if ($shortcutExists) {
                    $skipped++;
                    continue;
                }

                $data['shortcut'] = $shortcut;
            }

            if ($modelClass === LoanDetailKey::class) {
                $valueType = trim((string) ($row[1] ?? 'text'));
                $data['value_type'] = in_array($valueType, ['text', 'hall'], true) ? $valueType : 'text';
            }

            $modelClass::create($data);
            $imported++;
        }

        if ($imported === 0) {
            return back()->withErrors(['settings_file' => 'No new records were imported.']);
        }

        return back()->with('success', "Imported {$imported} records successfully. Skipped {$skipped} duplicates.");
    }

    private function importableSettingsModels(): array
    {
        return [
            'majors' => Major::class,
            'levels' => Level::class,
            'departments' => Department::class,
            'tool-types' => ToolType::class,
            'halls' => Hall::class,
            'loan-detail-keys' => LoanDetailKey::class,
        ];
    }

    private function readSettingsImportRows(string $path, string $extension): array
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
                $columnIndex = $this->xlsxColumnIndex((string) $cell['r']);
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

    private function isSettingsImportHeaderRow(array $row, bool $expectsShortcut): bool
    {
        $firstColumn = Str::of((string) ($row[0] ?? ''))->lower()->replace([' ', '_', '-'], '')->toString();
        $secondColumn = Str::of((string) ($row[1] ?? ''))->lower()->replace([' ', '_', '-'], '')->toString();

        if (! $expectsShortcut) {
            return in_array($firstColumn, ['name', 'الاسم'], true);
        }

        return in_array($firstColumn, ['name', 'tooltypename', 'tooltype', 'الاسم', 'نوعالأداة'], true)
            && in_array($secondColumn, ['shortcut', 'shortcode', 'الكود', 'الاختصار'], true);
    }
}
