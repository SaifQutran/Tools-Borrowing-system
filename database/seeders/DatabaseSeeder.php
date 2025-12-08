<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        \App\Models\User::create([
            'name' => 'المسؤول',
            'email' => 'admin@system.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'phone' => '0500000000',
            'is_approved' => true,
        ]);

        // Create Majors
        $majors = ['هندسة البرمجيات', 'علوم الحاسب', 'نظم المعلومات', 'الأمن السيبراني', 'الذكاء الاصطناعي'];
        foreach ($majors as $major) {
            \App\Models\Major::create(['name' => $major]);
        }

        // Create Levels
        $levels = ['السنة الأولى', 'السنة الثانية', 'السنة الثالثة', 'السنة الرابعة'];
        foreach ($levels as $level) {
            \App\Models\Level::create(['name' => $level]);
        }

        // Create Departments
        $departments = ['قسم تقنية المعلومات', 'قسم الهندسة', 'قسم العلوم', 'قسم الإدارة'];
        foreach ($departments as $dept) {
            \App\Models\Department::create(['name' => $dept]);
        }

        // Create Halls
        $halls = ['القاعة 101', 'القاعة 102', 'القاعة 201', 'القاعة 202', 'القاعة 301'];
        foreach ($halls as $hall) {
            \App\Models\Hall::create(['name' => $hall]);
        }

        // Create Tool Types
        $toolTypes = [
            'ميكروفون' => 1,
            'جهاز عرض' => 2,
            'مكبر صوت' => 3,
            'مؤشر ليزر' => 4,
            'حاسوب محمول' => 5,
        ];
        
        foreach ($toolTypes as $typeName => $id) {
            \App\Models\ToolType::create(['id' => $id, 'name' => $typeName]);
        }

        // Create Sample Tools
        \App\Models\Tool::create([
            'name' => 'ميكروفون لاسلكي 1',
            'tool_type_id' => 1,
            'code' => 'MIC-001',
            'status' => 'available',
            'attributes' => ['hall_number' => 'القاعة 101', 'microphone_type' => 'handle'],
        ]);

        \App\Models\Tool::create([
            'name' => 'جهاز عرض 1',
            'tool_type_id' => 2,
            'code' => 'PROJ-001',
            'status' => 'available',
            'attributes' => ['device_number' => 'PROJ-001', 'notes' => 'HD Resolution'],
        ]);

        \App\Models\Tool::create([
            'name' => 'حاسوب محمول Dell',
            'tool_type_id' => 5,
            'code' => 'LAP-001',
            'status' => 'available',
            'attributes' => ['laptop_number' => 'LAP-001', 'specifications' => 'i7, 16GB RAM, 512GB SSD'],
        ]);
    }
}
