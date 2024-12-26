<?php

namespace App\Imports;

use App\Models\ClinicalRxDailyCensus;
use App\Models\Patient;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ClinicalRxDailyCensusImport implements ToCollection
{
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $count = 0;
        foreach ($rows as $k => $row) 
        {
            if(isset($row[0])) {
                $patient_name = trim($row[0]);

                if(strtolower($patient_name) == 'patient') {
                    continue;
                }

                if(empty($patient_name)) {
                    continue;
                }

                $birth_date = isset($row[1]) ? trim($row[1]) : null;

                if(!empty($birth_date)) {
                    if(is_numeric($birth_date)) {
                        $birth_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($birth_date)->format('Y-m-d');
                    } else {
                        $birthDateString = DateTime::createFromFormat('m/d/Y', $birth_date);
                        $birth_date = $birthDateString->format('Y-m-d');
                    }
                } else {
                    $birth_date = null;
                }

                $scripts_received = isset($row[2]) ? trim($row[2]) : null;
                $provider = isset($row[3]) ? trim($row[3]) : null;
                $remarks = isset($row[4]) ? trim($row[4]) : null;

                $patient_id = null;
                $patientNameArr = explode(',', $patient_name);

                $fname = isset($patientNameArr[1]) ? trim($patientNameArr[1]) : null;
                $lname = isset($patientNameArr[0]) ? trim($patientNameArr[0]) : null;

                $patients = Patient::all()->filter(function ($patients) use ($fname, $lname) {
                    return strtolower($patients->getDecryptedFirstname()) === strtolower(trim($fname))
                        && strtolower($patients->getDecryptedLastname()) === strtolower(trim($lname));
                });

                if($patients->count() > 0) {
                    foreach($patients as $p) {
                        $patient_id = $p->id;
                        if(empty($birth_date)) {
                            $birth_date = $p->getDecryptedBirthdate();
                        }
                    }
                }

                $save = ClinicalRxDailyCensus::insertOrIgnore([
                    'patient_id'                    => $patient_id,
                    'patient_name'                  => $patient_name,
                    'birth_date'                    => $birth_date,
                    'scripts_received'              => $scripts_received,
                    'provider'                      => $provider,
                    'remarks'                       => $remarks,
                    'created_at'                    => Carbon::now(),
                    'user_id'                       => isset($this->params['user_id']) ? $this->params['user_id'] : auth()->user()->id,
                    'date'                          => isset($this->params['date']) ? $this->params['date'] : null,
                    'pharmacy_store_id'             => isset($this->params['pharmacy_store_id']) ? $this->params['pharmacy_store_id'] : null,
                ]);
                if($save) {
                    $count++;
                }
            }
        }
        return $count;
    }
}
