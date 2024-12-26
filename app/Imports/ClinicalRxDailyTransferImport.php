<?php

namespace App\Imports;

use App\Models\ClinicalRxDailyTransfer;
use App\Models\Patient;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ClinicalRxDailyTransferImport implements ToCollection
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
                $date_called = isset($row[0]) ? trim($row[0]) : null;

                if(strtolower($date_called) == 'call date' || strtolower($date_called) == 'date called') {
                    continue;
                }

                if(!empty($date_called)) {
                    if(is_numeric($date_called)) {
                        $date_called = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date_called)->format('Y-m-d');
                    } else {
                        $birthDateString = DateTime::createFromFormat('m/d/Y', $date_called);
                        $date_called = $birthDateString->format('Y-m-d');
                    }
                } else {
                    $date_called = null;
                }

                $patient_name = isset($row[1]) ? trim($row[1]) : null;

                if(strtolower($patient_name) == 'name' || strtolower($patient_name) == 'patient') {
                    continue;
                }

                if(empty($patient_name)) {
                    continue;
                }

                $birth_date = isset($row[2]) ? trim($row[2]) : null;
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

                $phone_number = isset($row[3]) ? trim($row[3]) : null;
                $medication_description = isset($row[4]) ? trim($row[4]) : null;
                $previous_pharmacy = isset($row[5]) ? trim($row[5]) : null;

                $provider = isset($row[6]) ? trim($row[6]) : null;
                $is_patient_seen_at_trhc = isset($row[7]) ? trim($row[7]) : null;
                $call_status = isset($row[8]) ? trim($row[8]) : null;
                $transfer_to_trp = isset($row[9]) ? trim($row[9]) : null;
                $fax_pharmacy = isset($row[10]) ? trim($row[10]) : null;
                $is_ma = isset($row[11]) ? trim($row[11]) : null;
                $expected_rx = isset($row[12]) ? trim($row[12]) : null;
                $remarks = isset($row[13]) ? trim($row[13]) : null;

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

                $status = isset($this->params['status']) ? $this->params['status'] : null;

                $insertArray = [
                    'patient_id'                => $patient_id,
                    'patient_name'              => $patient_name,
                    'birth_date'                => $birth_date,
                    'phone_number'              => $phone_number,
                    'medication_description'    => $medication_description,
                    'previous_pharmacy'         => $previous_pharmacy,
                    'provider'                  => $provider,
                    'is_patient_seen_at_trhc'   => $is_patient_seen_at_trhc,
                    'call_status'               => $call_status,
                    'transfer_to_trp'           => $transfer_to_trp,
                    'fax_pharmacy'              => $fax_pharmacy,
                    'is_ma'                     => $is_ma,
                    'expected_rx'               => $expected_rx,
                    'remarks'                   => $remarks,
                    'created_at'                => Carbon::now(),
                    'user_id'                   => isset($this->params['user_id']) ? $this->params['user_id'] : auth()->user()->id,
                    'date'                      => isset($this->params['date']) ? $this->params['date'] : null,
                    'pharmacy_store_id'         => isset($this->params['pharmacy_store_id']) ? $this->params['pharmacy_store_id'] : null,
                    'status'                    => $status,
                ];

                if($status == 'pending') {
                    $insertArray['is_received'] = 'No';
                } else {
                    $insertArray['is_received'] = 'Yes';
                }

                $save = ClinicalRxDailyTransfer::insertOrIgnore($insertArray);
                if($save) {
                    $count++;
                }
            }
        }
        return $count;
    }
}
