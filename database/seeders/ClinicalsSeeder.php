<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClinicalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('clinicals')->where('name', 'Diabetes')
            ->update(['color' => '#FCAE7C']);
        DB::table('clinicals')->where('name', 'Rasa')
            ->update(['color' => '#B3F5BC']);
        DB::table('clinicals')->where('name', 'Cholesterol')
            ->update(['color' => '#D6F6FF']);
        DB::table('clinicals')->where('name', 'Statin')
            ->update(['color' => '#D2BDFF']);
        
    }
}
