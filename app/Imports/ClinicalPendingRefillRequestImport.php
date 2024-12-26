<?php

namespace App\Imports;

use App\Models\ClinicalPendingRefillRequest;
use App\Models\Patient;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ClinicalPendingRefillRequestImport implements ToCollection
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
                $send_date = isset($row[0]) ? trim($row[0]) : null;
                $rx_number = isset($row[1]) ? trim($row[1]) : null;

                if(empty($rx_number)) {
                    continue;
                }

                $medication_description = isset($row[2]) ? trim($row[2]) : null;
                $patient_name = isset($row[3]) ? trim($row[3]) : null;
                
                if(empty($patient_name) || strtolower($patient_name) == 'patient full name' || strtolower($patient_name) == 'patient' || strtolower($patient_name) == 'name') {
                    continue;
                }

                if(!empty($send_date)) {
                    if(is_numeric($send_date)) {
                        $send_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($send_date)->format('Y-m-d H:i:s');
                    } else {
                        $dateString = DateTime::createFromFormat('m/d/Y h:i:s A', $send_date);
                        $send_date = $dateString->format('Y-m-d H:i:s');
                    }
                } else {
                    $send_date = null;
                }

                
                $provider = isset($row[4]) ? trim($row[4]) : null;
                $status_name = isset($row[5]) ? trim($row[5]) : null;
                $remarks = isset($row[6]) ? trim($row[6]) : null;

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

                $save = ClinicalPendingRefillRequest::insertOrIgnore([
                    'send_date'                     => $send_date,
                    'patient_id'                    => $patient_id,
                    'patient_name'                  => $patient_name,
                    'rx_number'                     => $rx_number,
                    'medication_description'        => $medication_description,
                    'provider'                      => $provider,
                    'status_name'                   => $status_name,
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
