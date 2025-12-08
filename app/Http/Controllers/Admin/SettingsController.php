<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Major, Level, Department, Hall, ToolType};
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $majors = Major::all();
        $levels = Level::all();
        $departments = Department::all();
        $halls = Hall::all();
        $toolTypes = ToolType::all();

        return view('admin.settings.index', compact('majors', 'levels', 'departments', 'halls', 'toolTypes'));
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
        $request->validate(['name' => 'required|string|max:255']);
        ToolType::create($request->only('name'));
        return back()->with('success', 'تم إضافة نوع الأداة بنجاح');
    }

    public function deleteToolType(ToolType $toolType)
    {
        $toolType->delete();
        return back()->with('success', 'تم حذف نوع الأداة بنجاح');
    }
}
