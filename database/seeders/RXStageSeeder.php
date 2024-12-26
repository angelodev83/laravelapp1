<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RXStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('r_x_stages')->insert([
            ['name' => 'Pending', 'color' => 'lightblue'],
            ['name' => 'Inprogress', 'color' => 'green'],
            ['name' => 'Filled', 'color' => 'blue'],
            ['name' => 'Reviewing intake questionnaire', 'color' => 'gray'],
        ]);
    }
}
