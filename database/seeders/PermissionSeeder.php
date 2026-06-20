<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            "view dashboard",

            "view tickets",
            "create tickets",
            "view my tickets",
            "assign tickets",
            "change status tickets",

            "view kategori",
            "create kategori",
            "edit kategori",
            "delete kategori",

            "view knowledge base",
            "create knowledge base",
            "edit knowledge base",
            "delete knowledge base",
            "train",

            "view log chatbot",
            "validate log chatbot",

            "view pengguna",
            "create pengguna",
            "edit pengguna",
            "delete pengguna",

            'view role permission',

            'view dashboard tickets menu',
            'view dashboard model menu',
            'view dashboard my tickets menu'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $dashboardPermissions = [
            "view dashboard"
        ];

        $ticketsPermissions = [
            "view tickets",
            "create tickets",
            "view my tickets",
            "assign tickets",
            "change status tickets"
        ];

        $kategoriPermissions = [
            "view kategori",
            "create kategori",
            "edit kategori",
            "delete kategori"
        ];

        $knowledgeBasePermissions = [
            "view knowledge base",
            "create knowledge base",
            "edit knowledge base",
            "delete knowledge base",
            "train"
        ];

        $logChatbotPermissions = [
            "view log chatbot",
            "validate log chatbot"
        ];

        $penggunaPermissions = [
            "view pengguna",
            "create pengguna",
            "edit pengguna",
            "delete pengguna"
        ];

        $admin = Role::findByName('admin');
        $admin->givePermissionTo([
            'view dashboard',
            "view tickets",
            "assign tickets",
            "change status tickets",
            "view kategori",
            "create kategori",
            "edit kategori",
            "delete kategori",
            "view knowledge base",
            "create knowledge base",
            "edit knowledge base",
            "delete knowledge base",
            "train",
            "view log chatbot",
            "validate log chatbot",
            "view pengguna",
            "create pengguna",
            "edit pengguna",
            "delete pengguna",
            'view role permission',
            'view dashboard tickets menu',
            'view dashboard model menu',
        ]);

        $kepalaDiskominfo = Role::findByName('kepala-diskominfo');
        $kepalaDiskominfo->givePermissionTo([
            'view dashboard',
            "view tickets",
            "assign tickets",
            "change status tickets",
            'view dashboard tickets menu',
        ]);

        $pegawaiDinas = Role::findByName('pegawai-dinas');
        $pegawaiDinas->givePermissionTo([
            'view dashboard',
            "create tickets",
            'view my tickets',
            'view dashboard my tickets menu'
        ]);
    }
}
