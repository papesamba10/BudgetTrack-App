<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Alimentation',
                'color' => '#C51162',
                'icon' => 'restaurant',
                'is_system' => true,
                'user_id' => null,
            ],
            [
                'name' => 'Transport',
                'color' => '#1565C0',
                'icon' => 'directions_bus',
                'is_system' => true,
                'user_id' => null,
            ],
            [
                'name' => 'Logement',
                'color' => '#4527A0',
                'icon' => 'home',
                'is_system' => true,
                'user_id' => null,
            ],
            [
                'name' => 'Santé',
                'color' => '#00695C',
                'icon' => 'medication',
                'is_system' => true,
                'user_id' => null,
            ],
            [
                'name' => 'Divertissement',
                'color' => '#E65100',
                'icon' => 'movie',
                'is_system' => true,
                'user_id' => null,
            ],
            [
                'name' => 'Éducation',
                'color' => '#0277BD',
                'icon' => 'school',
                'is_system' => true,
                'user_id' => null,
            ],
            [
                'name' => 'Autre',
                'color' => '#546E7A',
                'icon' => 'category',
                'is_system' => true,
                'user_id' => null,
            ],
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::create($cat);
        }
    }
}
