<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@canteen.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // Cashiers
        User::create([
            'name' => 'Cashier One',
            'email' => 'cashier1@canteen.com',
            'password' => Hash::make('password'),
            'role' => 'cashier'
        ]);

        User::create([
            'name' => 'Cashier Two',
            'email' => 'cashier2@canteen.com',
            'password' => Hash::make('password'),
            'role' => 'cashier'
        ]);

        // Customers
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "Customer {$i}",
                'email' => "customer{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'customer'
            ]);
        }
    }
}