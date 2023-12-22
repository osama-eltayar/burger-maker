<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Merchant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    const INGREDIENTS = [
        [
            'name' => 'Beef',
            'needed_stock' => 20000,
            'current_stock' => 20000,
        ],
        [
            'name' => 'Cheese',
            'needed_stock' => 5000,
            'current_stock' => 5000,
        ],
        [
            'name' => 'Onion',
            'needed_stock' => 1000,
            'current_stock' => 1000,
        ],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::INGREDIENTS as $INGREDIENT)
            Ingredient::firstOrCreate($INGREDIENT,
                ['merchant_id' => Merchant::query()->inRandomOrder()->first('id')->id]);
    }
}
