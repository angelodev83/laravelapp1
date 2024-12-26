<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Oig_Exclusion_List;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use Illuminate\Support\Facades\Storage;

class OigListImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $employees = Employee::get();
        $concatenatedDataArray = [];
        
        foreach ($employees as $employee) {
            // Trim and remove special characters from each attribute
            $trimmedFirstName = preg_replace('/[^A-Za-z0-9]/', '', strtolower(trim($employee->firstname)));
            $trimmedLastName = preg_replace('/[^A-Za-z0-9]/', '', strtolower(trim($employee->lastname)));
            $trimmedDateOfBirth = preg_replace('/[^0-9-]/', '', strtolower(trim($employee->date_of_birth)));

            // Remove spaces and hyphens
            $trimmedFirstName = str_replace(' ', '', $trimmedFirstName);
            $trimmedLastName = str_replace(' ', '', $trimmedLastName);
            $trimmedDateOfBirth = str_replace('-', '', $trimmedDateOfBirth);

            // Concatenate the trimmed attributes
            $concatenatedData = $trimmedFirstName . $trimmedLastName . $trimmedDateOfBirth;

            // Add the concatenated data to the array
            $concatenatedDataArray[] = $concatenatedData;
        }
        //dd($concatenatedDataArray);
        //$searchedConcatenatedData = 'chitofernandez19821120';
        foreach ($rows as $k => $row) 
        {
            if($k > 0) {
                Storage::disk('local')->append('file.txt', json_encode($row[3]));
                $trimmedFirstName = preg_replace('/[^A-Za-z0-9]/', '', strtolower(trim($row[1])));
                $trimmedLastName = preg_replace('/[^A-Za-z0-9]/', '', strtolower(trim($row[0])));
                $trimmedDateOfBirth = preg_replace('/[^0-9-]/', '', strtolower(trim($row[8])));

                $trimmedFirstName = str_replace(' ', '', $trimmedFirstName);
                $trimmedLastName = str_replace(' ', '', $trimmedLastName);
                $trimmedDateOfBirth = str_replace('-', '', $trimmedDateOfBirth);

                $searchedConcatenatedData = $trimmedFirstName . $trimmedLastName . $trimmedDateOfBirth;
                // dd($searchedConcatenatedData);
                if (in_array($searchedConcatenatedData, $concatenatedDataArray)) {
                    $lastname = trim($row[0]);
                    $firstname = trim($row[1]);
                    $midname = trim($row[2]);
                    $busname = trim($row[3]);
                    $general = trim($row[4]);
                    $specialty = trim($row[5]);
                    $upin = trim($row[6]);
                    $npi = trim($row[7]);
                    $dob = !empty(trim($row[8])) ? date('Y-m-d', strtotime(trim($row[8]))) : null;
                    $address = trim($row[9]);
                    $city = trim($row[10]);
                    $state = trim($row[11]);
                    $zip = trim($row[12]);
                    $excltype = trim($row[13]);
                    $excldate = trim($row[14]);
                    $reindate = trim($row[15]);
                    $waiverdate = trim($row[16]);
                    $wvrstate = trim($row[17]);

                    Storage::disk('local')->append('file.txt', json_encode($row[0]));
                }

                // Oig_Exclusion_List::create([
                //     'date_reported' => $mysqlFormattedDate,
                //     'patients'  => $patients,
                //     'tips_completed' => $tips_completed,
                //     'cmrs_completed' => $cmrs_completed,
                //     'mtm_score'   => $mtm_score,
                // ]); 
            }
        }
    }
}