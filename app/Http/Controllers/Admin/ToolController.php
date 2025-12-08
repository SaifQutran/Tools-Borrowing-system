<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolType;
use App\Models\Hall;
use Illuminate\Http\Request;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

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
            'code' => 'required|string|unique:tools,code',
            'attributes' => 'nullable|array',
        ]);

        $tool = Tool::create($validated);
        
        // Generate QR code
        $this->generateQRCode($tool);

        return redirect()->route('admin.tools.index')->with('success', 'تم إضافة الأداة بنجاح');
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
        return response()->download(storage_path('app/public/' . $tool->qr_code_path));
    }

    public function showQr(Tool $tool)
    {
        $this->generateQRCode($tool);
        
        $path = storage_path('app/public/' . $tool->qr_code_path);
        
        if (file_exists($path)) {
            return response()->file($path, [
                'Content-Type' => 'image/svg+xml',
            ]);
        }
        
        abort(404);
    }

    private function generateQRCode(Tool $tool)
    {
        $url = route('tool.show', $tool->id);
        
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($url);

        $path = 'qr_codes/tool_' . $tool->id . '.svg';
        \Storage::disk('public')->put($path, $qrCode);

        $tool->update(['qr_code_path' => $path]);
    }
}
