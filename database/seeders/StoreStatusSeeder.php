<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StoreStatus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class StoreStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        Schema::disableForeignKeyConstraints();

        StoreStatus::truncate();

        Schema::enableForeignKeyConstraints();

        Model::reguard();

        $statuses = config('status');
        $storeStatuses = $statuses['stores'];
 
        foreach ($storeStatuses as $status) {
            StoreStatus::insert($status);
        }
    }
}
