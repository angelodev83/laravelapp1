<?php

namespace App\Imports;

use App\Models\Patient;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Crypt;

class PioneerPatientsImport implements ToCollection
{
    private $pharmacyStoreId;

    public function __construct($pharmacyStoreId)
    {
        $this->pharmacyStoreId = $pharmacyStoreId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $k => $row) 
        {
            if($k > 0 && isset($row[1])) {

                $fullname = explode(',', $row[1]);

                if(!empty($fullname)) {

                    $pioneer_id = trim($row[0]);

                    $fname = trim($fullname[1]);
                    $lname = trim($fullname[0]);
                    $bdate = trim($row[2]);
                    if(!empty($bdate)) {
                        if(is_numeric($bdate)) {
                            $bdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($bdate)->format('Y-m-d');
                        } else {
                            $bdateString = DateTime::createFromFormat('d/m/Y', $bdate);
                            $bdate = $bdateString->format('Y-m-d');
                        }
                    } else {
                        $bdate = null;
                    }
                    $phone = trim($row[3]);
                    $email = trim($row[4]);
                    $facility_name = trim($row[5]);
                    $address = trim($row[6]);
                    $city = trim($row[7]);
                    $state = trim($row[8]);
                    $zip = trim($row[9]);

                    $pharmacy_store_id = $this->pharmacyStoreId;
                    
                    $request = [
                        'firstname' => $fname,
                        'lastname' => $lname,
                        'birthdate' => $bdate,
                    ];
                    $patients = Patient::all()->filter(function ($patients) use ($request) {
                            return strtolower($patients->getDecryptedFirstname()) === strtolower(trim($request['firstname']))
                                && strtolower($patients->getDecryptedLastname()) === strtolower(trim($request['lastname']))
                                && strtolower($patients->getDecryptedBirthdate()) === date('Y-m-d', strtotime(strtolower(trim($request['birthdate']))));
                        });
                    if($patients->count() === 0) {
                        Patient::create([
                            'firstname'     => Crypt::encryptString($fname),
                            'lastname'      => Crypt::encryptString($lname),
                            'birthdate'     => Crypt::encryptString($bdate),
                            'phone_number'  => $phone,
                            'address'       => Crypt::encryptString($address),
                            'city'          => Crypt::encryptString($city),
                            'state'         => Crypt::encryptString($state),
                            'zip_code'      => trim($zip),
                            'email'         => $email,
                            'facility_name' => $facility_name,
                            'patientid'     => '-',
                            'pioneer_id'     => $pioneer_id,
                            'source'        => 'pioneer',
                            'created_at'    => Carbon::now(),
                            'updated_at'    => Carbon::now(),
                            'pharmacy_store_id'  => $pharmacy_store_id
                        ]);
                    } else {
                        foreach($patients as $patient) {
                            $patient->facility_name = $facility_name;
                            $patient->source = 'pioneer';
                            $patient->pioneer_id = $pioneer_id;
                            $patient->save();
                        }
                    }

                }
            }
        }
    }
}