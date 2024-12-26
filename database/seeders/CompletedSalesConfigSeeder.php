<?php

namespace Database\Seeders;

use App\Models\CompletedSalesConfiguration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompletedSalesConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompletedSalesConfiguration::create([
            'code' => 'daily_rx',
            'value' => 26,
            'month' => 7,
            'year' => 2024
        ]);
    }
}
