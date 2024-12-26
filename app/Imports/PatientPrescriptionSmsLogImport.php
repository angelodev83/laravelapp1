<?php

namespace App\Imports;

use App\Models\Patient;
use App\Models\PatientPrescriptionSmsLog;
use App\Repositories\PatientPrescriptionSmsLogRepository;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Events\AfterImport;

class PatientPrescriptionSmsLogImport implements ToCollection
{
    private $params = [];

    public function __construct(array $params)
    {
        $this->params = $params;

    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $k => $row) 
        {
            if($k > 1 && isset($row[0])) {
                $patient_id = trim($row[0]);
                $rx_number = trim($row[1]);
                $status = trim($row[3]);
                $hidden_comment = trim($row[5]);

                if($this->params['folder'] == 'created') {
                    $date = date('m/d/Y');
                    $status = 'Created';
                } else {
                    $date = substr($hidden_comment, 0, 10);
                }


                $order_number = $patient_id.'-'.$date;
                
                $patient = Patient::where('pioneer_id', $patient_id)->first();
                if(isset($patient->id)) {
                    $send_sms_alert = $patient->send_sms_alert;

                    // check if patient send_sms_alert is 1 (will send), 0 (will not send)
                    if($send_sms_alert == 1) {
                        $upperStatus = strtoupper($status);
                        if(!in_array($upperStatus, $this->params['statuses'])) {
                            $status = 'Waiting For Fill';
                        }
                        $check = PatientPrescriptionSmsLog::where('patient_id',$patient->id)
                            ->where('order_number', $order_number)
                            ->where(DB::raw('UPPER(status)'), strtoupper($status))
                            ->first();
                        if(!isset($check->id)) {
                            PatientPrescriptionSmsLog::insertOrIgnore([
                                'order_number' => $order_number,
                                'rx_number' => $rx_number,
                                'hidden_comment' => $hidden_comment,
                                'patient_id' => $patient->id,
                                'status' => $status,
                                'user_id' => 1,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        }
                    }
                    // end check

                }

            }
        }
    }

    // public static function afterImport(AfterImport $event)
    // {
    //     // Call your custom function
    //     self::runAfterImportFunction();
    // }

    // public function runAfterImportFunction()
    // {

    // }

}