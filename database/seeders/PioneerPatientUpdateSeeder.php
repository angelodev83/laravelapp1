<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class PioneerPatientUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pioneers = Patient::query()->Pioneer()->get();
        foreach($pioneers as $p) {
            if(!empty($p->middlename)) {
                $p->middlename = Crypt::encryptString($p->middlename);
            }
            if(!empty($p->suffix)) {
                $p->suffix = Crypt::encryptString($p->suffix);
            }
            $p->save();
        }
    }
}
