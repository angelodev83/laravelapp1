<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Wholesaler;
use Carbon\Carbon;

class WholesalerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Wholesaler::truncate();
        Wholesaler::insertOrIgnore([
            ['name' => 'McKesson', 'category' => 'procurement', 'created_at' => Carbon::now()],
            ['name' => 'Amerisource', 'category' => 'procurement', 'created_at' => Carbon::now()],
            ['name' => 'McKesson', 'category' => 'supply', 'created_at' => Carbon::now()],
            ['name' => 'Staples', 'category' => 'supply', 'created_at' => Carbon::now()],
            ['name' => 'Uline', 'category' => 'supply', 'created_at' => Carbon::now()],
            ['name' => 'Cardinal Health', 'category' => 'procurement', 'created_at' => Carbon::now()],
            ['name' => 'Amazon', 'category' => 'supply', 'created_at' => Carbon::now()],
        ]);
    }
}
