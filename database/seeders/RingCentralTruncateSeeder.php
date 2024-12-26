<?php

namespace Database\Seeders;

use App\Models\RcSmsRecord;
use App\Models\RcSyncInfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RingCentralTruncateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RcSyncInfo::truncate();
        RcSmsRecord::truncate();
    }
}
