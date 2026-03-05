<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Téléphone', 'icon' => 'phone'],
            ['name' => 'Ordinateur', 'icon' => 'laptop'],
            ['name' => 'Sac', 'icon' => 'bag'],
            ['name' => 'Documents', 'icon' => 'file-text'],
            ['name' => 'Clés', 'icon' => 'key'],
            ['name' => 'Bijoux', 'icon' => 'gem'],
            ['name' => 'Vêtements', 'icon' => 'shirt'],
            ['name' => 'Autres', 'icon' => 'package'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                ['icon' => $category['icon']]
            );
        }
    }
}
