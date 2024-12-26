<?php

namespace App\Imports;

use App\Models\ClinicalRenewal;
use App\Models\Patient;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class RenewalImport extends BaseImport implements ToCollection
{
    private $params = [];

    public function __construct(array $params)
    {
        $this->params = $params;
    }
    
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $k => $row) 
        {
            if(isset($row[0])) {
                $serial_number = isset($row[0]) ? trim($row[0]) : null;
                $rx_number = isset($row[1]) ? trim($row[1]) : null;
                $renew_date = isset($row[2]) ? trim($row[2]) : null;

                if(empty($serial_number) || strtolower($serial_number == 'serial number')) {
                    continue;
                }

                if(empty($rx_number) || strtolower($rx_number == 'rx number')) {
                    continue;
                }

                if(strtolower($renew_date == 'renew date')) {
                    continue;
                }

                if(!empty($renew_date)) {
                    if(is_numeric($renew_date)) {
                        $renew_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($renew_date)->format('Y-m-d');
                    } else {
                        $bdateString = DateTime::createFromFormat('d/m/Y', $renew_date);
                        $errors = DateTime::getLastErrors();
                        if ($errors['warning_count'] === 0 && $errors['error_count'] === 0) {
                            $renew_date = $bdateString->format('Y-m-d');
                        } else {
                            continue;
                        }
                    }
                } else {
                    $renew_date = null;
                }

                $telebridge = isset($row[3]) ? trim($row[3]) : null;
                if(!empty($telebridge)) {
                    $telebridge = strtolower($telebridge);
                    $telebridge = ucfirst($telebridge);
                }

                $reason_for_denial = isset($row[4]) ? trim($row[4]) : null;

                $patient = Patient::where('pioneer_id', $serial_number)->first();

                $patient_id = isset($patient->id) ? $patient->id : null;


                // $date_today = $this->getCurrentPSTDate('Y-m-d');
                // $date1 = Carbon::createFromFormat('Y-m-d', $renew_date);
                // $date2 = Carbon::createFromFormat('Y-m-d', $date_today);
                // $days = $date1->diffInDays($date2);

                // $status_id = $days > 7 ? 922 : 921;
                $status_id = 951;
                
                $rts = ClinicalRenewal::where('rx_number', $rx_number)->where('is_archived', 0)->first();

                if(!isset($rts->id) && !empty($patient_id)) {
                    ClinicalRenewal::insertOrIgnore([
                        'patient_id' => $patient_id,
                        'rx_number' => $rx_number,
                        'renew_date' => $renew_date,
                        'call_attempts' => 0,
                        'telebridge' => $telebridge,
                        'reason_for_denial' => $reason_for_denial,
                        'status_id' => $status_id,
                        'user_id'   => auth()->user()->id,
                        'pharmacy_store_id' => $this->params['pharmacy_store_id'] ?? null,
                        'created_at' => Carbon::now(),
                    ]);
                }

            }
        }
    }
}


