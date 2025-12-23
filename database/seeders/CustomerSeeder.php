<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            'name' => 'John Doe',
            'phone' => '1234567890',
            'email' => 'john@example.com',
            'address' => '123 Main St',
            'status' => 'active',
        ]);

        Customer::create([
            'name' => 'Jane Smith',
            'phone' => '0987654321',
            'email' => 'jane@example.com',
            'address' => '456 Oak Ave',
            'status' => 'active',
        ]);

        Customer::create([
            'name' => 'Bob Johnson',
            'phone' => '5556667777',
            'email' => 'bob@example.com',
            'address' => '789 Pine Rd',
            'status' => 'inactive',
        ]);
    }
}
