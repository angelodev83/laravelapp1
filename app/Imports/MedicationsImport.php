<?php

namespace App\Imports;

use App\Models\Medication;
use App\Models\Outcome;
use App\Models\Patient;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;

class MedicationsImport implements ToCollection
{

    public function collection(Collection $rows)
    {
        foreach ($rows as $k => $row) 
        {
            if($k > 0 && isset($row[1])) {
                
                $med_id = str_replace('-', '', $row[1] . $row[2] . $row[3]);

                 // Check if the medication already exists
                $existingMedication = Medication::where('med_id', $med_id)->first();

                if (!$existingMedication) {
                    Medication::create(
                        [
                            'med_id' => $med_id,
                            'name' => $row[0],
                            'ndc' => $row[1],
                            'upc' => $row[2],
                            'item_number' => $row[3],
                            'package_size' => $row[4],
                            'manufacturer' => $row[5],
                        ]
                    );
                }
                
            }
        }
    }
}