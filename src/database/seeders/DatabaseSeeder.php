<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condition;
use App\Models\Sell;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            ConditionsTableSeeder::class,
            SellsTableSeeder::class,
            CategorySeeder::class,
            ProfileSeeder::class,
        ]);
    }
}
