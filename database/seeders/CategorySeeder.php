<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            [
                'name' => 'Software',
                'slug' => 'software',
                'description' => 'Permasalahan terkait perangkat lunak seperti instalasi, konfigurasi, dan pemecahan masalah aplikasi.',
                'sla_hours' => 16,
            ],
            [
                'name' => 'Hardware',
                'slug' => 'hardware',
                'description' => 'Permasalahan terkait perangkat keras seperti komputer, laptop, printer, dan perangkat pendukung lainnya.',
                'sla_hours' => 24,
            ],
            [
                'name' => 'Infrastuktur IT',
                'slug' => 'infrastruktur-it',
                'description' => 'Permasalahan terkait jaringan, server, perangkat keras, dan infrastruktur teknologi informasi lainnya.',
                'sla_hours' => 8,
            ],
            [
                'name' => 'Akses',
                'slug' => 'akses',
                'description' => 'Permasalahan terkait akses ke sistem, jaringan, dan sumber daya teknologi informasi.',
                'sla_hours' => 12,
            ],
            [
                'name' => 'Jaringan',
                'slug' => 'jaringan',
                'description' => 'Permasalahan terkait jaringan, server, perangkat keras, dan infrastruktur teknologi informasi lainnya.',
                'sla_hours' => 4,
            ],
            [
                'name' => 'Lainnya',
                'slug' => 'lainnya',
                'description' => 'Permasalahan terkait jaringan, server, perangkat keras, dan infrastruktur teknologi informasi lainnya.',
                'sla_hours' => 48,
            ]
        ]);
    }
}
