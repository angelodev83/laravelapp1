<?php

namespace App\Imports;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\ToCollection;

class ManualTesterImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $k => $row) 
        {
            if($k > 0 && isset($row[0])) {
                $pioneer_id = trim($row[0]);
                
                $fullname = explode(',', trim($row[1]));
                $fname = trim($fullname[1]);
                $lname = trim($fullname[0]);

                $bdate = trim($row[2]);

                if(!empty($bdate)) {
                    if(is_numeric($bdate)) {
                        $bdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($bdate)->format('Y-m-d');
                    } else {
                        $bdate = date('Y-m-d', strtotime($bdate));
                    }
                } else {
                    $bdate = null;
                }

                $request = [
                    'firstname' => $fname,
                    'lastname' => $lname,
                    'birthdate' => $bdate,
                ];
                $patients = Patient::all()->filter(function ($patient) use ($request) {
                        return strtolower($patient->getDecryptedFirstname()) === strtolower(trim($request['firstname']))
                            && strtolower($patient->getDecryptedLastname()) === strtolower(trim($request['lastname']))
                            && strtolower($patient->getDecryptedBirthdate()) === date('Y-m-d', strtotime(strtolower(trim($request['birthdate']))));
                    });
                
                if($patients->count() > 0) {
                    foreach($patients as $patient) {
                        $patient->pioneer_id = $pioneer_id;
                        $patient->save();
                    }
                // } else {
                //     Patient::create([
                //         'firstname' => Crypt::encryptString($fname),
                //         'lastname'  => Crypt::encryptString($lname),
                //         'birthdate' => Crypt::encryptString($bdate),
                //         'phone_number' => trim($row[3]),
                //         'email'     => trim($row[4]),
                //         'patientid' => '-',
                //         'source'    => 'pioneer',
                //         'created_at'=> Carbon::now(),
                //         'updated_at'=> Carbon::now(),
                //         'pharmacy_store_id'  => 1
                //     ]);
                }
            }
        }
    }

}