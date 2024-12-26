<?php

namespace Database\Seeders;

use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::insertOrIgnore([
            ['name' => 'Pharmacy', 'color' => '#363a86', 'bg_color' => '#c7caff', 'created_at' => Carbon::now()],
            ['name' => 'Clinical', 'color' => '#5d5069', 'bg_color' => '#e4cdf8', 'created_at' => Carbon::now()],
            ['name' => 'IT/Software', 'color' => '#0d677c', 'bg_color' => '#c2f4f5', 'created_at' => Carbon::now()],
            ['name' => 'Procurement', 'color' => '#a46c26', 'bg_color' => '#ffdcb1', 'created_at' => Carbon::now()],
            ['name' => 'Operation', 'color' => '#34623d', 'bg_color' => '#c8f4d2', 'created_at' => Carbon::now()],
            ['name' => 'Finance', 'color' => '#ab404b', 'bg_color' => '#ffc4ca', 'created_at' => Carbon::now()],
            ['name' => 'Marketing', 'color' => '#5e17eb', 'bg_color' => '#c7caff', 'created_at' => Carbon::now()],
            ['name' => 'Human Resource', 'color' => '#b834af', 'bg_color' => '#e5cdf9', 'created_at' => Carbon::now()]
        ]);
    }
}
