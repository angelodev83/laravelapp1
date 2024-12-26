<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $response = Http::get('https://restcountries.com/v3.1/all?fields=name')->json();

        // Check if data is not empty
        if (!empty($response)) {
            // Truncate the Country table
            Country::truncate();

            $data = [];
            foreach ($response as $val) {
                array_push($data, ['name' => $val['name']['common']]);
            }

            // Insert the new data
            Country::insert($data);

            
        }
    }
}
