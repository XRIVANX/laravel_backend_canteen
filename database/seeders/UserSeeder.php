<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@canteen.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Cashiers
        User::create([
            'name' => 'Cashier One',
            'email' => 'cashier1@canteen.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Cashier Two',
            'email' => 'cashier2@canteen.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'email_verified_at' => now(),
        ]);

        // Customers
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Customer {$i}",
                'email' => "customer{$i}@canteen.com",
                'password' => Hash::make('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);
        }
    }
}