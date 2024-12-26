<?php

namespace App\Imports;

use App\Models\OperationRts;
use App\Models\Patient;
use App\Models\StoreStatus;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class RTSImport implements ToCollection
{
    private $params = [];

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $k => $row) 
        {
            if($k > 0 && isset($row[0])) {
                $serial_number = trim($row[0]);
                $rx_number = trim($row[1]);
                $dispensed_item_name = trim($row[2]);
                $fill_date = trim($row[3]);
                $priority_name = trim($row[4]);
                $patient_paid_amount = trim($row[5]);

                $patient_paid_amount = preg_replace('/[^\d.-]/', '', $patient_paid_amount);
                if ($patient_paid_amount === '') {
                    $patient_paid_amount = 0;
                }
                $patient_paid_amount = (float) $patient_paid_amount;

                if(!empty($fill_date)) {
                    if(is_numeric($fill_date)) {
                        $fill_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fill_date)->format('Y-m-d');
                    } else {
                        $bdateString = DateTime::createFromFormat('d/m/Y', $fill_date);
                        $fill_date = $bdateString->format('Y-m-d');
                    }
                } else {
                    $fill_date = null;
                }

                $patient = Patient::where('pioneer_id', $serial_number)->first();

                $patient_id = isset($patient->id) ? $patient->id : null;


                $date_today = $this->getCurrentPSTDate('Y-m-d');
                $date1 = Carbon::createFromFormat('Y-m-d', $fill_date);
                $date2 = Carbon::createFromFormat('Y-m-d', $date_today);
                $days = $date1->diffInDays($date2);

                $status_id = $days > 7 ? 922 : 921;
                
                $rts = OperationRts::where('rx_number', $rx_number)->where('is_archived', 0)->first();

                if(!isset($rts->id) && !empty($patient_id)) {
                    OperationRts::insertOrIgnore([
                        'patient_id' => $patient_id,
                        'rx_number' => $rx_number,
                        'fill_date' => $fill_date,
                        'call_attempts' => 0,
                        'status_id' => $status_id,
                        'dispensed_item_name' => $dispensed_item_name,
                        'priority_name' => $priority_name,
                        'patient_paid_amount' => $patient_paid_amount,
                        'user_id'   => auth()->user()->id,
                        'pharmacy_store_id' => $this->params['pharmacy_store_id'] ?? null,
                        'created_at' => Carbon::now(),
                    ]);
                }

            }
        }
    }

    protected function getCurrentPSTDate($format = 'Y-m-d', $date = null)
    {

        if(!empty($date)) {
            $pst = Carbon::createFromFormat('Y-m-d', $date);
            $pst = $pst->setTimezone('America/Los_Angeles');
        }else {
            $pst = Carbon::now('America/Los_Angeles');
        }
        
        return $pst->format($format);
    }

}
