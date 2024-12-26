<?php

namespace Database\Seeders;

use App\Models\ClinicalProvider;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClinicalProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClinicalProvider::truncate();
        ClinicalProvider::insertOrIgnore([
            ['lastname' => 'Bosak', 'firstname' => 'Kelli', 'pharmacy_store_id' => 1, 'created_at' => Carbon::now()],
            ['lastname' => 'Cressall', 'firstname' => 'Cassandre', 'pharmacy_store_id' => 1, 'created_at' => Carbon::now()],
            ['lastname' => 'Davis', 'firstname' => 'Makenna', 'pharmacy_store_id' => 1, 'created_at' => Carbon::now()],
            ['lastname' => 'Galer', 'firstname' => 'Amanda', 'pharmacy_store_id' => 1, 'created_at' => Carbon::now()],
            ['lastname' => 'Huling', 'firstname' => 'Sean', 'pharmacy_store_id' => 1, 'created_at' => Carbon::now()],
            ['lastname' => 'Mohrbacher', 'firstname' => 'Vanessa', 'pharmacy_store_id' => 1, 'created_at' => Carbon::now()],
            ['lastname' => 'Nag', 'firstname' => 'Pratip', 'pharmacy_store_id' => 1, 'created_at' => Carbon::now()],
            ['lastname' => 'Velez', 'firstname' => 'Alfredo', 'pharmacy_store_id' => 1, 'created_at' => Carbon::now()],
            ['lastname' => 'Vitt', 'firstname' => 'Paul', 'pharmacy_store_id' => 1, 'created_at' => Carbon::now()],
            ['lastname' => 'Willis Welch', 'firstname' => 'Leann', 'pharmacy_store_id' => 1, 'created_at' => Carbon::now()]
        ]);
    }
}
