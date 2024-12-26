<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RXStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('r_x_statuses')->insert([
            [
                'name' => 'Waiting For Data Entry',
                'color' => 'lightblue'
            ],
            [
                'name' => 'Waiting for Fill',
                'color' => 'lightblue'
            ],
            [
                'name' => 'Waiting for Script',
                'color' => 'lightblue'
            ],
            [
                'name' => 'Filled',
                'color' => 'yellow'
            ],
            [
                'name' => 'Processed in Pioneer',
                'color' => 'green'
            ],
            [
                'name' => 'IOU',
                'color' => 'gray'
            ]
        ]);
    }
}
