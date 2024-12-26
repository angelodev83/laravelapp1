<?php

namespace App\Imports;

use App\Models\ClinicalBridgedPatient;
use App\Models\Patient;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ClinicalBridgedPatientImport implements ToCollection
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
            if(isset($row[2])) {
                $date = isset($row[0]) ? trim($row[0]) : null;
                $patient_name = isset($row[2]) ? trim($row[2]) : null;
                
                if(strtolower($date) == 'date written') {
                    continue;
                }
                
                if(empty($patient_name) || strtolower($patient_name) == 'patient full name' || strtolower($patient_name) == 'patient' || strtolower($patient_name) == 'name') {
                    continue;
                }

                if(!empty($date)) {
                    if(is_numeric($date)) {
                        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
                    } else {
                        $dateString = DateTime::createFromFormat('m/d/Y', $date);
                        $date = $dateString->format('Y-m-d');
                    }
                } else {
                    $date = null;
                }

                $rx_number = isset($row[1]) ? trim($row[1]) : null;

                if(empty($rx_number)) {
                    continue;
                }

                $medication_description = isset($row[3]) ? trim($row[3]) : null;
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
                    }
                }

                if(empty($date)) {
                    $date = isset($this->params['date']) ? $this->params['date'] : null;
                }

                $save = ClinicalBridgedPatient::insertOrIgnore([
                    'patient_id'                    => $patient_id,
                    'patient_name'                  => $patient_name,
                    'rx_number'                     => $rx_number,
                    'medication_description'        => $medication_description,
                    'remarks'                       => $remarks,
                    'created_at'                    => Carbon::now(),
                    'user_id'                       => isset($this->params['user_id']) ? $this->params['user_id'] : auth()->user()->id,
                    'date'                          => $date,
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
