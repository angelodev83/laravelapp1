<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


use App\Http\Helpers\Helper;
use App\Imports\MedicationsImport;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Status;
use App\Models\Stage;
use App\Rules\MatchesUserApiKey;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PatientController extends Controller
{
    public function addpatient(Request $request)
    {
        try {
        $helper =  new Helper;
        $input = $request->all();

        $username = $request->header('username');
        $apikey = $request->header('apikey');

        $validator = Validator::make($request->headers->all(), [
            'apikey' => 'required|exists:api_keys,key',
            'username' => ['required', new MatchesUserApiKey],
        ]);

        $validator2 = Validator::make($input, [
            'firstname' => 'required|max:30',
            'lastname' => 'required|max:30',
            'birthdate' => 'required|max:30',
        ]);

        if ($validator->fails() || $validator2->fails()) {
            return response()->json([
                'status' => 'Patient record creation unsuccessful due to validation errors',
                'errors' => array_merge($validator->errors()->all(), $validator2->errors()->all())
            ], 422);
        }

        $patient = new Patient;
        $patient->firstname = $helper->ProperNamingCase($input['firstname']);
        $patient->lastname = $helper->ProperNamingCase($input['lastname']);
        $patient->birthdate = date('Y-m-d', strtotime($input['birthdate']));
        $patient->address = $input['address'];
        $patient->city = $input['city'];
        $patient->state = $input['state'];
        $patient->zip_code = $input['zip_code'];
        $patient->phone_number = $input['phone_number'];
        $patient->save();
        
        if(count($input['prescriptions']) > 0 ){
                foreach($input['prescriptions'] as $prescription_input){
                    
                    $p_validator = Validator::make($prescription_input, [
                        'order_number' => 'required', 
                     ]);

                     if ($p_validator->fails()) {
                        //Delete the patient if there is an error in the prescription
                        $patient->delete();
                        return response()->json([
                            'status' => 'API Insert request failed',
                            'errors' => $p_validator->errors()
                        ], 422); // Return validation errors
                    }

                    $prescription = new Prescription;
                    $prescription->patient_id = $patient->id;
                    $prescription->order_number = $prescription_input['order_number'];
                    $prescription->request_type = $prescription_input['request_type'];
                    $prescription->sig = $prescription_input['sig'];
                    
                   
                    $prescription->prescriber_name = $prescription_input['prescriber_name'];
                    $prescription->prescriber_phone = $prescription_input['prescriber_phone'];
                    $prescription->prescriber_fax = $prescription_input['prescriber_fax'];
                    $prescription->npi = $prescription_input['npi'];
                    $prescription->medications = $prescription_input['medications'];
                    
                    $prescription->days_supply = $prescription_input['days_supply'];
                    $prescription->refills_requested = $prescription_input['refills_requested'];
                
                    $prescription->submitted_at = date('Y-m-d H:i:s', strtotime($prescription_input['submitted_at']));
                    $prescription->submitted_by = $prescription_input['submitted_by'];
                    $prescription->sent_at = date('Y-m-d H:i:s', strtotime($prescription_input['sent_at']));
                    $prescription->received_at = date('Y-m-d H:i:s', strtotime($prescription_input['received_at']));
                    $prescription->save();

                }
        }

        return response()->json([
            'status' => 'API Insert request succeeded',
            'patient' => $patient,
            'prescription' => $prescription
        ], 201);


        } catch (\Illuminate\Database\QueryException $e) {
             // Handle other types of exceptions
             $errorMessage = $e->getMessage();
             return response()->json(['error' => $errorMessage], 500);

        }
    }

    // public function addMed(Request $request)
    // {
    //     // $file = $this->argument('file');
    //     $filePath = public_path("source-images/medication.csv");

    //     // if (!Storage::exists($filePath)) {
    //     //     $this->error("File {$filePath} does not exist in the storage directory.");
    //     //     return 1;
    //     // }

    //     $handle = fopen($filePath, 'r');

    //     if ($handle === false) {
    //         $this->error('Failed to open the file.');
    //         return 1;
    //     }

    //     $header = fgetcsv($handle, 1000, ',');
    //     while (($row = fgetcsv($handle, 1000, ',')) !== false) {
    //         $data = array_combine($header, $row);
    //         $med_id = str_replace('-', '', $data['ndc'] . $data['upc'] . $data['item_number']);

    //         // Check if the medication already exists
    //         $existingMedication = Medication::where('med_id', $med_id)->first();

    //         if (!$existingMedication) {
    //             Medication::create(
    //                 [
    //                     'med_id' => $med_id,
    //                     'name' => $data['name'],
    //                     'ndc' => $data['ndc'],
    //                     'upc' => $data['upc'],
    //                     'item_number' => $data['item_number'],
    //                     'package_size' => $data['package_size'],
    //                     'manufacturer' => $data['manufacturer'],
    //                 ]
    //             );
    //         }
    //     }

    //     fclose($handle);
    //     // $this->info('Medications imported successfully.');
    //     return 'Medications imported successfully.';
    // }

    public function addMed()
    {
        $absolute_path = str_replace('\\', '/' , public_path('source-images'));

        $filePath = $absolute_path.'/'.'medication.csv';

        $destinationPath = storage_path('/medication.csv');
        
        // Copy the file to the storage directory
        if (!File::copy($filePath, $destinationPath)) {
            $this->error("Failed to copy the file to the storage directory.");
            return 1;
        }

        Excel::import(new MedicationsImport(), $destinationPath);
        return 'success';
    }
}
