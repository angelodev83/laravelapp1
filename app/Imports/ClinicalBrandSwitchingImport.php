<?php

namespace App\Imports;

use App\Models\ClinicalBrandSwitching;
use App\Models\Patient;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ClinicalBrandSwitchingImport implements ToCollection
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
                $rx_number = isset($row[1]) ?  trim($row[1]) : null;

                if(empty($rx_number)) {
                    continue;
                }

                $patient_name = isset($row[2]) ? trim($row[2]) : null;

                if(empty($patient_name)) {
                    continue;
                }

                $date = isset($row[0]) ? trim($row[0]) : null;

                if(strtolower($date) == 'date') {
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

                $generic_medication_description = trim($row[3]);
                $branded_medication_description = trim($row[4]);
                $remarks = trim($row[5]);
                $price = trim($row[6]);
                $total_paid_claims = trim($row[7]);
                $status = trim($row[8]);
                $dispensed_medication_description = trim($row[9]);
                $cost = trim($row[10]);
                

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

                $save = ClinicalBrandSwitching::insertOrIgnore([
                    'patient_id'                    => $patient_id,
                    'patient_name'                  => $patient_name,
                    'rx_number'                     => $rx_number,
                    'generic_medication_description'    => $generic_medication_description,
                    'branded_medication_description'    => $branded_medication_description,
                    'dispensed_medication_description'  => $dispensed_medication_description,
                    'remarks'                       => $remarks,
                    'price'                         => $price,
                    'total_paid_claims'             => $total_paid_claims,
                    'cost'                          => $cost,
                    'status'                        => $status,
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
