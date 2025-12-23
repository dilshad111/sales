<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Item::create([
            'name' => 'Laptop',
            'uom' => 'Piece',
            'price' => 1000.00,
        ]);

        Item::create([
            'name' => 'Mouse',
            'uom' => 'Piece',
            'price' => 20.00,
        ]);

        Item::create([
            'name' => 'Keyboard',
            'uom' => 'Piece',
            'price' => 50.00,
        ]);

        Item::create([
            'name' => 'Monitor',
            'uom' => 'Piece',
            'price' => 200.00,
        ]);
    }
}
