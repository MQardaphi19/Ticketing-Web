<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => 'SuperAdmin123!',
                'nip' => '0000000000',
                'department' => 'Administrasi',
                'phone' => '081234567890',
                'role' => 'super-admin',
                'status' => 'active',
            ]
        );
    }
}
