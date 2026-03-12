<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run()
    {
        // Meals (Category 1)
        $meals = [
            ['Chicken Rice', 'Steamed chicken with rice', 45.00],
            ['Nasi Lemak', 'Coconut rice with sambal, egg, and anchovies', 50.00],
            ['Fried Rice', 'Indonesian style fried rice with egg', 55.00],
            ['Mee Goreng', 'Spicy fried noodles', 48.00],
            ['Chicken Chop', 'Grilled chicken with fries and coleslaw', 65.00],
            ['Fish & Chips', 'Battered fish with fries', 62.00],
            ['Spaghetti Bolognese', 'Pasta with meat sauce', 58.00],
            ['Beef Steak', 'Grilled beef with vegetables', 85.00],
            ['Vegetable Curry', 'Mixed vegetables in curry sauce', 42.00],
            ['Chicken Curry', 'Spicy chicken curry with rice', 54.00],
        ];

        // Snacks (Category 2)
        $snacks = [
            ['Spring Rolls', 'Crispy vegetable spring rolls (5 pcs)', 25.00],
            ['Chicken Wings', 'Spicy fried chicken wings (4 pcs)', 32.00],
            ['Samosa', 'Potato and pea filled pastries (3 pcs)', 20.00],
            ['French Fries', 'Crispy potato fries', 18.00],
            ['Onion Rings', 'Crispy battered onion rings', 22.00],
            ['Fish Balls', 'Fried fish balls (10 pcs)', 15.00],
            ['Chicken Nuggets', 'Breaded chicken nuggets (6 pcs)', 28.00],
            ['Curry Puff', 'Flaky pastry with curry filling', 12.00],
            ['Popcorn Chicken', 'Bite-sized fried chicken', 30.00],
            ['Cheese Sticks', 'Mozzarella cheese sticks (4 pcs)', 24.00],
        ];

        // Beverages (Category 3)
        $beverages = [
            ['Mineral Water', '500ml bottled water', 5.00],
            ['Coca Cola', '330ml can', 8.00],
            ['Orange Juice', 'Fresh squeezed orange juice', 12.00],
            ['Iced Lemon Tea', 'Fresh brewed tea with lemon', 10.00],
            ['Hot Coffee', 'Fresh brewed coffee', 9.00],
            ['Hot Tea', 'Assorted tea bags', 8.00],
            ['Milkshake', 'Chocolate or vanilla milkshake', 15.00],
            ['Smoothie', 'Mixed fruit smoothie', 18.00],
            ['Milo', 'Hot or iced chocolate malt drink', 11.00],
            ['Thai Milk Tea', 'Sweet and creamy Thai tea', 14.00],
        ];

        // Desserts (Category 4)
        $desserts = [
            ['Ice Cream', 'Vanilla, chocolate, or strawberry scoop', 10.00],
            ['Chocolate Cake', 'Rich chocolate fudge cake slice', 18.00],
            ['Cheesecake', 'New York style cheesecake', 20.00],
            ['Apple Pie', 'Warm apple pie with cinnamon', 16.00],
            ['Brownie', 'Chocolate brownie with nuts', 14.00],
            ['Pudding', 'Vanilla or chocolate pudding', 12.00],
            ['Muffin', 'Blueberry or chocolate chip muffin', 11.00],
            ['Donut', 'Glazed or chocolate donut', 9.00],
            ['Cinnamon Roll', 'Swirled cinnamon pastry', 15.00],
            ['Fruit Salad', 'Mixed fresh fruits', 22.00],
        ];

        // Combos (Category 5)
        $combos = [
            ['Student Meal', 'Chicken rice + drink + snack', 65.00],
            ['Lunch Combo', 'Main meal + drink + dessert', 80.00],
            ['Family Pack', '2 mains + 2 drinks + 2 snacks', 150.00],
            ['Party Platter', 'Assorted snacks for 4-6 pax', 180.00],
            ['Breakfast Set', 'Coffee + pastry + fruit', 45.00],
            ['Value Meal', 'Chicken chop + drink + fries', 92.00],
            ['Snack Pack', '3 assorted snacks + 2 drinks', 70.00],
            ['Dessert Special', 'Dessert + coffee/tea', 38.00],
            ['Healthy Choice', 'Salad + juice + fruit', 58.00],
            ['Business Lunch', 'Premium main + drink + dessert', 120.00],
        ];

        // Create meals
        foreach ($meals as $index => $item) {
            MenuItem::create([
                'category_id' => 1,
                'name' => $item[0],
                'description' => $item[1],
                'price' => $item[2],
                'stock_quantity' => rand(20, 50),
                'low_stock_threshold' => 5,
                'is_available' => true,
            ]);
        }

        // Create snacks
        foreach ($snacks as $item) {
            MenuItem::create([
                'category_id' => 2,
                'name' => $item[0],
                'description' => $item[1],
                'price' => $item[2],
                'stock_quantity' => rand(30, 60),
                'low_stock_threshold' => 10,
                'is_available' => true,
            ]);
        }

        // Create beverages
        foreach ($beverages as $item) {
            MenuItem::create([
                'category_id' => 3,
                'name' => $item[0],
                'description' => $item[1],
                'price' => $item[2],
                'stock_quantity' => rand(50, 100),
                'low_stock_threshold' => 15,
                'is_available' => true,
            ]);
        }

        // Create desserts
        foreach ($desserts as $item) {
            MenuItem::create([
                'category_id' => 4,
                'name' => $item[0],
                'description' => $item[1],
                'price' => $item[2],
                'stock_quantity' => rand(15, 40),
                'low_stock_threshold' => 5,
                'is_available' => true,
            ]);
        }

        // Create combos
        foreach ($combos as $item) {
            MenuItem::create([
                'category_id' => 5,
                'name' => $item[0],
                'description' => $item[1],
                'price' => $item[2],
                'stock_quantity' => rand(10, 25),
                'low_stock_threshold' => 3,
                'is_available' => true,
            ]);
        }
    }
}