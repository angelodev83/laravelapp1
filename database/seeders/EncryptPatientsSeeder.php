<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\NameHelper;
use App\Http\Helpers\Helper;

class EncryptPatientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = Patient::all();
        foreach($patients as $p) {
            $firstname = $p->firstname;
            $lastname = $p->lastname;
            $birthdate = $p->birthdate;
            $address = $p->address;
            $city = $p->city;
            $state = $p->state;

            $p->firstname = Crypt::encryptString($firstname);
            $p->lastname = Crypt::encryptString($lastname);
            $p->birthdate = Crypt::encryptString($birthdate);
            $p->address = Crypt::encryptString($address);
            $p->city = Crypt::encryptString($city);
            $p->state = Crypt::encryptString($state);
            
            $p->save();
        }
    }
}
