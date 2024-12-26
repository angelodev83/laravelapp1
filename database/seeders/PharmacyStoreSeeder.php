<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\PharmacyStore;

class PharmacyStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PharmacyStore::truncate();
        PharmacyStore::insert([
            ['code' => 'TRP', 'name' => 'Three Rivers Pharmacy'],
            ['code' => '801', 'name' => '801 Pharmacy'],
            ['code' => '803', 'name' => '803 Pharmacy'],
            ['code' => '808', 'name' => '808 Pharmacy'],
        ]);
    }
}
