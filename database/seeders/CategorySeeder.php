<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Meals',
                'description' => 'Complete meals including rice and main dishes',
                'image' => 'meals.jpg'
            ],
            [
                'name' => 'Snacks',
                'description' => 'Light bites and finger foods',
                'image' => 'snacks.jpg'
            ],
            [
                'name' => 'Beverages',
                'description' => 'Drinks, juices, and refreshments',
                'image' => 'beverages.jpg'
            ],
            [
                'name' => 'Desserts',
                'description' => 'Sweet treats and pastries',
                'image' => 'desserts.jpg'
            ],
            [
                'name' => 'Combos',
                'description' => 'Meal deals and special combos',
                'image' => 'combos.jpg'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
