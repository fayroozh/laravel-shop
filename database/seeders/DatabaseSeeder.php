<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // تشغيل بذور الأدوار والصلاحيات
        $this->call(RolesAndPermissionsSeeder::class);

        // إنشاء مستخدم مشرف
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        // // تعيين دور المشرف الرئيسي للمستخدم
        // $admin->roles()->attach(1); // Super Admin role

        // إنشاء مستخدم عادي للاختبار
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
