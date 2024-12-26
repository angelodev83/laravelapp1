<?php

namespace App\Imports;

use App\Models\ClinicalTherapyChangeAndReco;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ClinicalTherapyChangeAndRecoImport implements ToCollection
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
            if($k > 0 && isset($row[0])) {
                $patient_name = trim($row[0]);
                $last_provider_that_sent_rx = trim($row[1]);
                $medication_description = trim($row[2]);
                $recommendation = trim($row[3]);
                $remarks = trim($row[4]);

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

                $save = ClinicalTherapyChangeAndReco::insertOrIgnore([
                    'patient_id'                    => $patient_id,
                    'patient_name'                  => $patient_name,
                    'last_provider_that_sent_rx'    => $last_provider_that_sent_rx,
                    'medication_description'        => $medication_description,
                    'recommendation'                => $recommendation,
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
