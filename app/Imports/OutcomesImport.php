<?php

namespace App\Imports;

use App\Models\Outcome;
use App\Models\Patient;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;

class OutcomesImport implements ToCollection
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
                
                $date = intval(trim($row[0]));
                $patients = trim($row[1]);
                $tips_completed = trim($row[2]);
                $cmrs_completed = trim($row[4]);
                $mtm_score = trim($row[6]);
                $mysqlFormattedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
                $pharmacy_store_id = $this->pharmacyStoreId;
                
                Outcome::create([
                    'date_reported' => $mysqlFormattedDate,
                    'patients'  => $patients,
                    'tips_completed' => $tips_completed,
                    'cmrs_completed' => $cmrs_completed,
                    'mtm_score'   => $mtm_score,
                    'pharmacy_store_id'  => $pharmacy_store_id
                ]); 
            }
        }
    }
}