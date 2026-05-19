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
        $admin = User::firstOrCreate(
            ['email' => 'admin@diskominfo.go.id'],
            [
                'name' => 'Admin',
                'password' => 'passwordadmin',
                'nip' => '0000000000',
                'department' => 'Administrasi',
                'phone' => '081234567890',
                'role' => 'admin',
                'status' => 'active',
            ]
        );
        $admin->assignRole('admin');

        $kepala = User::firstOrCreate(
            ['email' => 'kepala@diskominfo.go.id'],
            [
                'name' => 'Kepala',
                'password' => 'passwordkepala',
                'nip' => '1111111111',
                'department' => 'Kepala Dinas',
                'phone' => '081234567890',
                'role' => 'kepala-diskominfo',
                'status' => 'active',
            ]
        );
        $kepala->assignRole('kepala-diskominfo');

        $pertanian = User::firstOrCreate(
            ['email' => 'pertanian@diskominfo.go.id'],
            [
                'name' => 'Pertanian',
                'password' => 'passwordpertanian',
                'nip' => '2222222222',
                'department' => 'Pertanian',
                'phone' => '081234567890',
                'role' => 'pegawai-dinas',
                'status' => 'active',
            ]
        );
        $pertanian->assignRole('pegawai-dinas');
    }
}
