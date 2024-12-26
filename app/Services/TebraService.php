<?php

namespace App\Services;

use App\Models\Patient;
use Carbon\Carbon;
use SoapClient;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use stdClass;

class TebraService
{
    private $client;
    private $user;
    private $password;
    private $customerKey;

    public function __construct()
    {
        $this->client = new SoapClient(env('KAREO_SOAP_URL'));
        $this->user = env('KAREO_USER');
        $this->password = env('KAREO_PASSWORD');
        $this->customerKey = env('KAREO_CUSTOMER_KEY');
    }

    public function createPatient()
    {
        try {
            $request = array (
                'RequestHeader' => array('User' => $this->user, 'Password' => $this->password, 'CustomerKey' => $this->customerKey),
                'Patient' => ['Practice' =>['PracticeID' => '1', 'PracticeName' => 'TIN TeleHealth'],'FirstName' => 'Test2',
                    'LastName' => 'Testing2', 'DateofBirth' => '2011-06-14', 'City' => 'Houston', 'State' => 'TX', 'AddressLine1' => 'Sample Address'],
            );
            $params = ['request' => $request];
            //Make the SOAP call to create the patient
            $response = $this->client->CreatePatient($params);

            // // Create the SOAP request parameters
            // $params = array('CreatePatientReq' => $patientData);

            // // Call the CreatePatient method with the SOAP request parameters
            // $response = $client->CreatePatient($params);


            
            return $response;
        } catch (\SoapFault $fault) {
            // Handle SOAP faults
            return response()->json(['error' => 'SOAP error: ' . $fault->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getEverydayPatient()
    {
        try {
            // Get the current date in the required format (YYYY-MM-DD)
            $this_day = date('Y-m-d');
            // Get today's date
            $today = Carbon::today();

            // Subtract one day
            $oneDayAgo = $today->subDay();

            // Format the date in 'Y-m-d' format
            $oneDayAgoFormatted = $oneDayAgo->format('Y-m-d');
            // Define the request parameters
            $request = array (
                'RequestHeader' => array('User' => $this->user, 'Password' => $this->password, 'CustomerKey' => $this->customerKey),
                'Filter' => array('FromCreatedDate' => $oneDayAgoFormatted, 'To'),
                'Fields' => [],
            );

            $params = ['request' => $request];
            $response = $this->client->GetPatients($params)->GetPatientsResult;

            if (!isset($response->Patients->PatientData)) {
                // No patient data or an error occurred
                // Handle accordingly
            } elseif (is_array($response->Patients->PatientData)) {
                // Multiple patient data objects
                foreach($response->Patients->PatientData as &$value)
                {   
                    $id = $value->ID;
                    $firstname = ucwords(strtolower($value->FirstName));
                    $lastname = ucwords(strtolower($value->LastName));
                    $birthdate = date("Y-m-d", strtotime($value->DOB));
                    $address = $value->AddressLine1;
                    $city = $value->City;
                    $state = $value->State;
                    $zipcode = $value->ZipCode;
                    $created_date = date("Y-m-d H:i:s", strtotime($value->CreatedDate));
                    $phone_number = $value->MobilePhone;
            
                    $patient = Patient::where('tebra_id',$id)->first();
                    if(!$patient){
                        $new_patient = new Patient();
                        $new_patient->tebra_id = $id;
                        $new_patient->firstname = $firstname;
                        $new_patient->lastname = $lastname;
                        $new_patient->birthdate = $birthdate;
                        $new_patient->address = $address;
                        $new_patient->city = $city;
                        $new_patient->state = $state;
                        $new_patient->zip_code = $zipcode;
                        $new_patient->created_at = $created_date;
                        $new_patient->phone_number = $phone_number;
                        $new_patient->source = 'tebra';
                        $new_patient->save();
                    }
                }
            } else {
                if($response->Patients->PatientData->ID != ''){
                    // Only one patient data object
                    $value = $response->Patients->PatientData;
                    $id = $value->ID;
                    $firstname = ucwords(strtolower($value->FirstName));
                    $lastname = ucwords(strtolower($value->LastName));
                    $birthdate = date("Y-m-d", strtotime($value->DOB));
                    $address = $value->AddressLine1;
                    $city = $value->City;
                    $state = $value->State;
                    $zipcode = $value->ZipCode;
                    $created_date = date("Y-m-d H:i:s", strtotime($value->CreatedDate));
                    $phone_number = $value->MobilePhone;
                    
                    $patient = Patient::where('tebra_id',$id)->first();
                    if(!$patient){
                        $new_patient = new Patient();
                        $new_patient->tebra_id = $id;
                        $new_patient->firstname = $firstname;
                        $new_patient->lastname = $lastname;
                        $new_patient->birthdate = $birthdate;
                        $new_patient->address = $address;
                        $new_patient->city = $city;
                        $new_patient->state = $state;
                        $new_patient->zip_code = $zipcode;
                        $new_patient->created_at = $created_date;
                        $new_patient->phone_number = $phone_number;
                        $new_patient->source = 'tebra';
                        $new_patient->save();
                    }
                }
                
            }

            return $response;
        } catch (\SoapFault $fault) {
            // Handle SOAP faults
            return response()->json(['error' => 'SOAP error: ' . $fault->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getEverydayUpdatePatientData()
    {
        try {
            // Get the current date in the required format (YYYY-MM-DD)
            $this_day = date('Y-m-d');

            // Get today's date
            $today = Carbon::today();

            // Get the date one week before today
            $oneWeekAgo = $today->subWeek();

            // Format the date in 'Y-m-d' format
            $oneWeekAgoFormatted = $oneWeekAgo->format('Y-m-d');


            // Define the request parameters
            $request = array (
                'RequestHeader' => array('User' => $this->user, 'Password' => $this->password, 'CustomerKey' => $this->customerKey),
                'Filter' => array('FromLastModifiedDate' => $oneWeekAgoFormatted),
                'Fields' => [],
            );

            $params = ['request' => $request];
            $response = $this->client->GetPatients($params)->GetPatientsResult;

            if (!isset($response->Patients->PatientData)) {
                // No patient data or an error occurred
                // Handle accordingly
            } elseif (is_array($response->Patients->PatientData)) {
                // Multiple patient data objects
                foreach($response->Patients->PatientData as &$value)
                {
                    $id = $value->ID;
                    $firstname = ucwords(strtolower($value->FirstName));
                    $lastname = ucwords(strtolower($value->LastName));
                    $birthdate = date("Y-m-d", strtotime($value->DOB));
                    $address = $value->AddressLine1;
                    $city = $value->City;
                    $state = $value->State;
                    $zipcode = $value->ZipCode;
                    $created_date = date("Y-m-d H:i:s", strtotime($value->CreatedDate));
                    $phone_number = $value->MobilePhone;
                    
                    if($value->CreatedDate != $value->LastModifiedDate){
                        
                        $patient = Patient::where('tebra_id',$id)->first();
                        if($patient){
                            $patient->tebra_id = $id;
                            $patient->firstname = $firstname;
                            $patient->lastname = $lastname;
                            $patient->birthdate = $birthdate;
                            $patient->address = $address;
                            $patient->city = $city;
                            $patient->state = $state;
                            $patient->zip_code = $zipcode;
                            $patient->created_at = $created_date;
                            $patient->phone_number = $phone_number;
                            $patient->source = 'tebra';
                            $patient->save();
                        }
                    }
                }
            } else {
                // Only one patient data object
                if($response->Patients->PatientData->ID != ''){
                    $value = $response->Patients->PatientData;
                    $id = $value->ID;
                    $firstname = ucwords(strtolower($value->FirstName));
                    $lastname = ucwords(strtolower($value->LastName));
                    $birthdate = date("Y-m-d", strtotime($value->DOB));
                    $address = $value->AddressLine1;
                    $city = $value->City;
                    $state = $value->State;
                    $zipcode = $value->ZipCode;
                    $created_date = date("Y-m-d H:i:s", strtotime($value->CreatedDate));
                    $phone_number = $value->MobilePhone;
                    
                    if($value->CreatedDate != $value->LastModifiedDate){
                        //Storage::disk('local')->append('file.txt', json_encode($id));
                        $patient = Patient::where('tebra_id',$id)->first();
                        if($patient){
                            $patient->tebra_id = $id;
                            $patient->firstname = $firstname;
                            $patient->lastname = $lastname;
                            $patient->birthdate = $birthdate;
                            $patient->address = $address;
                            $patient->city = $city;
                            $patient->state = $state;
                            $patient->zip_code = $zipcode;
                            $patient->created_at = $created_date;
                            $patient->phone_number = $phone_number;
                            $patient->source = 'tebra';
                            $patient->save();
                        }
                    }
                }
                
            }

            
            

            return $response;
        } catch (\SoapFault $fault) {
            // Handle SOAP faults
            return response()->json(['error' => 'SOAP error: ' . $fault->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updatePatient($data)
    {
        try {
            $request = array (
                'RequestHeader' => array('User' => $this->user, 'Password' => $this->password, 'CustomerKey' => $this->customerKey),
                'Patient' => ['PatientID' => '25550', 'Practice' =>['PracticeID' => '1', 'PracticeName' => 'TIN TeleHealth'],
                    'FirstName' => $data['firstname'],
                    'LastName' => $data['lastname']],
            );
            $params = ['request' => $request];
            
            $response = $this->client->UpdatePatient($params);


            
            return $response;
        } catch (\SoapFault $fault) {
            // Handle SOAP faults
            return response()->json(['error' => 'SOAP error: ' . $fault->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getPatient($id)
    {
        try {
            $request = [
                'RequestHeader' => ['User' => $this->user, 'Password' => $this->password, 'CustomerKey' => $this->customerKey],
                'Filter' => ['PatientID' => $id],
                'Fields' => [],
            ];

            $params = ['request' => $request];
            $response = $this->client->GetPatient($params)->GetPatientResult;

            // foreach ($response->Patient as $value) {
            //     Storage::disk('local')->append('file.txt', json_encode($value->LastName . ', ' . $value->FirstName));
            // }

            return $response;
        } catch (Exception $err) {
            print "Error: " . $err->getMessage();
        }
    }

    public function getAllPatients()
    {
        try {
            // Initial value of start key
            $startKey = '0';

            // Initialize an array to store all patients
            $allPatients = [];

            do {
                // Construct the request with the current start key
                $request = [
                    'RequestHeader' => ['User' => $this->user, 'Password' => $this->password, 'CustomerKey' => $this->customerKey],
                    'Filter' => ['StartKey' => $startKey],
                    'Fields' => [],
                ];

                // Send the request to get patients
                $params = ['request' => $request];
                $response = $this->client->GetAllPatients($params)->GetAllPatientsResult;

                // Process response
                if (isset($response->Key->nextStartKey) && $response->Key->nextStartKey != $startKey) {
                    // Convert the stdClass object to an associative array
                    $patientsArray = json_decode(json_encode($response->Patients->PatientBatchData), true);
                    
                    // Store patients from this response
                    // Append the converted array to $allPatients
                    foreach ($patientsArray as $patient) {
                        array_push($allPatients,$patient);
                    }

                    // Update start key for the next iteration
                    $startKey = $response->Key->nextStartKey;

                    // Sleep for a short duration to avoid hitting rate limits
                    // Adjust as needed based on Kareo API rate limits
                    //usleep(500000); // Sleep for 0.5 seconds
                } else {
                    // Break the loop if nextStartKey doesn't change
                    break;
                }
            } while (true);

            $file_name = str_replace('\\', '/' , storage_path())."/tebra_patients.csv";
            // Check if patients.csv file exists and delete it if it does
            if (file_exists($file_name)) {
                unlink($file_name);
            }
            // Create the patients.csv file and add the header
            file_put_contents($file_name, 'id,firtname,lastname,birthdate,age,marital_status,gender,email,'.'"'.'address'.'"'.','.'"'.'city'.'"'.','.'"'.'state'.'"'.',zipcode,created_date,phone_number,ssn,mrn,home_phone,work_phone,prefix,suffix,referral_source,referring_provider_fullname,referring_provider_id,practice_name,practice_id,middlename,last_appointment_date,last_diagnosis,last_encounter_date,emergency_phone,emergency_name,employer_name,employment_status,default_case_id,default_case_name,default_case_payer_scenario,default_case_send_patient_statement,dcc_related_to_abuse,dcc_related_to_auto_accident,dcc_related_to_auto_accident_state,dcc_related_to_epsdt,dcc_related_to_emergency,dcc_related_to_employment,dcc_related_to_family_planning,dcc_related_to_other,dcc_related_to_pregnancy' . PHP_EOL);

            foreach($allPatients as &$value)
            {
                $id = $value['ID'];
                $firstname = ucwords(strtolower($value['FirstName']));
                $lastname = ucwords(strtolower($value['LastName']));
                $birthdate = date('Y-m-d', strtotime($value['DOB']));
                $address = $value['AddressLine1'];
                $city = $value['City'];
                $state = $value['State'];
                $zipcode = $value['ZipCode'];
                $created_date = date('Y-m-d', strtotime($value['CreatedDate']));
                $phone_number = $value['MobilePhone'];
                $age = $value['Age'];
                $marital_status = $value['MaritalStatus'];
                $gender = $value['Gender'];
                $email = $value['EmailAddress'];
                $source = "tebra";
                $ssn = $value['SSN'];
                $mrn = $value['MedicalRecordNumber'];
                $home_phone = $value['HomePhone'];
                $work_phone = $value['WorkPhone'];
                $prefix = $value['Prefix'];
                $suffix = $value['Suffix'];
                $referral_source = $value['ReferralSource'];
                $referring_provider_fullname = $value['ReferringProviderFullName'];
                $referring_provider_id = $value['ReferringProviderId'];
                $practice_name = $value['PracticeName'];
                $practice_id = $value['PracticeId'];
                $middlename = $value['MiddleName'];
                $last_appointment_date = $value['LastAppointmentDate'];
                $last_diagnosis = $value['LastDiagnosis'];
                $last_encounter_date = date('Y-m-d', strtotime($value['LastEncounterDate']));
                $emergency_phone = $value['EmergencyPhone'];
                $emergency_name = $value['EmergencyName'];
                $employer_name = $value['EmployerName'];
                $employment_status = $value['EmploymentStatus'];
                $default_case_id = $value['DefaultCaseID'];
                $default_case_name = $value['DefaultCaseName'];
                $default_case_payer_scenario = $value['DefaultCasePayerScenario'];
                $default_case_send_patient_statement = $value['DefaultCaseSendPatientStatements'];
                $dcc_related_to_abuse = $value['DefaultCaseConditionRelatedToAbuse'];
                $dcc_related_to_auto_accident = $value['DefaultCaseConditionRelatedToAutoAccident'];
                $dcc_related_to_auto_accident_state = $value['DefaultCaseConditionRelatedToAutoAccidentState'];
                $dcc_related_to_epsdt = $value['DefaultCaseConditionRelatedToEPSDT'];
                $dcc_related_to_emergency = $value['DefaultCaseConditionRelatedToEmergency'];
                $dcc_related_to_employment = $value['DefaultCaseConditionRelatedToEmployment'];
                $dcc_related_to_family_planning = $value['DefaultCaseConditionRelatedToFamilyPlanning'];
                $dcc_related_to_other = $value['DefaultCaseConditionRelatedToOther'];
                $dcc_related_to_pregnancy = $value['DefaultCaseConditionRelatedToPregnancy'];

                $patient = new Patient();
                $patient->tebra_id = $id;
                $patient->firstname = $firstname;
                $patient->lastname = $lastname;
                $patient->birthdate = $birthdate;
                $patient->address = $address;
                $patient->city = $city;
                $patient->state = $state;
                $patient->zip_code = $zipcode;
                $patient->phone_number = $phone_number;
                $patient->created_at = $created_date;
                $patient->age = $age;
                $patient->marital_status = $marital_status;
                $patient->gender = $gender;
                $patient->email = $email;
                $patient->source = $source;
                $patient->ssn = $ssn;
                $patient->mrn = $mrn;
                $patient->home_phone = $home_phone;
                $patient->work_phone = $work_phone;
                $patient->prefix = $prefix;
                $patient->suffix = $suffix;
                $patient->referral_source = $referral_source;
                $patient->referring_provider_fullname = $referring_provider_fullname;
                $patient->referring_provider_id = $referring_provider_id;
                $patient->practice_name = $practice_name;
                $patient->practice_id = $practice_id;
                $patient->middlename = $middlename;
                $patient->last_appointment_date = $last_appointment_date;
                $patient->last_diagnosis = $last_diagnosis;
                $patient->last_encounter_date = $last_encounter_date;
                $patient->emergency_phone = $emergency_phone;
                $patient->emergency_name = $emergency_name;
                $patient->employer_name = $employer_name;
                $patient->employment_status = $employment_status;
                $patient->default_case_id = $default_case_id;
                $patient->default_case_name = $default_case_name;
                $patient->default_case_payer_scenario = $default_case_payer_scenario;
                $patient->default_case_send_patient_statement = $default_case_send_patient_statement;
                $patient->dcc_related_to_abuse = $dcc_related_to_abuse;
                $patient->dcc_related_to_auto_accident = $dcc_related_to_auto_accident;
                $patient->dcc_related_to_auto_accident_state = $dcc_related_to_auto_accident_state;
                $patient->dcc_related_to_epsdt = $dcc_related_to_epsdt;
                $patient->dcc_related_to_emergency = $dcc_related_to_emergency;
                $patient->dcc_related_to_employment = $dcc_related_to_employment;
                $patient->dcc_related_to_family_planning = $dcc_related_to_family_planning;
                $patient->dcc_related_to_other = $dcc_related_to_other;
                $patient->dcc_related_to_pregnancy = $dcc_related_to_pregnancy;
                $patient->save();

                $line = '"' . $id . '","' . $firstname . '","' . $lastname . '","' . $birthdate . '","' . $age . '","' . $marital_status . '","' . $gender . '","' . $email . '","' . $address . '","' . $city . '","' . $state . '","' . $zipcode . '","' . $created_date . '","' . $phone_number . '","' . $source . '","' . $ssn . '","' . $mrn . '","' . $home_phone . '","' . $work_phone . '","' . $prefix . '","' . $suffix . '","' . $referral_source . '","' . $referring_provider_fullname . '","' . $referring_provider_id . '","' . $practice_name . '","' . $practice_id . '","' . $middlename . '","' . $last_appointment_date . '","' . $last_diagnosis . '","' . $last_encounter_date . '","' . $emergency_phone . '","' . $emergency_name . '","' . $employer_name . '","' . $employment_status . '","' . $default_case_id . '","' . $default_case_name . '","' . $default_case_payer_scenario . '","' . $default_case_send_patient_statement . '","' . $dcc_related_to_abuse . '","' . $dcc_related_to_auto_accident . '","' . $dcc_related_to_auto_accident_state . '","' . $dcc_related_to_epsdt . '","' . $dcc_related_to_emergency . '","' . $dcc_related_to_employment . '","' . $dcc_related_to_family_planning . '","' . $dcc_related_to_other . '","' . $dcc_related_to_pregnancy . '"' . PHP_EOL;
                file_put_contents($file_name, $line, FILE_APPEND);
            }

            return $allPatients;
        } catch (Exception $err) {
            print "Error: " . $err->getMessage();
        }
    }

    public function getPatientsDateStartRange($date)
    {
        try {
            // Get the current date in the required format (YYYY-MM-DD)
            $toDate = date('Y-m-d');

            // Define the request parameters
            $request = array (
                'RequestHeader' => array('User' => $this->user, 'Password' => $this->password, 'CustomerKey' => $this->customerKey),
                'Filter' => array('FromCreatedDate' => $date, 'ToCreatedDate' => $toDate),
                'Fields' => [],
            );

            $params = ['request' => $request];
            $response = $this->client->GetPatients($params)->GetPatientsResult;

            $file_name = str_replace('\\', '/' , storage_path())."/tebra_patients.csv";
            // Check if patients.csv file exists and delete it if it does
            if (file_exists($file_name)) {
                unlink($file_name);
            }
            // Create the patients.csv file and add the header
            file_put_contents($file_name, 'id,firtname,lastname,birthdate,'.'"'.'address'.'"'.','.'"'.'city'.'"'.','.'"'.'state'.'"'.',zipcode,created_date,phone_number' . PHP_EOL);

            foreach($response->Patients->PatientData as &$value)
            {
                $id = $value->ID;
                $firstname = ucwords(strtolower($value->FirstName));
                $lastname = ucwords(strtolower($value->LastName));
                $birthdate = $value->DOB;
                $address = $value->AddressLine1;
                $city = $value->City;
                $state = $value->State;
                $zipcode = $value->ZipCode;
                $created_date = $value->CreatedDate;
                $phone_number = $value->MobilePhone;
                $source = "tebra";

                $line = '"' . $id . '","' . $firstname . '","' . $lastname . '","' . $birthdate . '","' . $address . '","' . $city . '","' . $state . '","' . $zipcode . '","' . $created_date . '","' . $phone_number . '","' . $source . '"' . PHP_EOL;
                file_put_contents($file_name, $line, FILE_APPEND);
            }

            return $response;
        } catch (\SoapFault $fault) {
            // Handle SOAP faults
            return response()->json(['error' => 'SOAP error: ' . $fault->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAppointments()
    {
        try {
            // Get the current date in the required format (YYYY-MM-DD)
            $toDate = '2022-12-31';
            $fromDate = '2022-12-01';
            // Define the request parameters
            $request = array (
                'RequestHeader' => array('User' => $this->user, 'Password' => $this->password, 'CustomerKey' => $this->customerKey, 'PracticeName' => 'TIN TeleHealth'),
                'Filter' => array('FromCreatedDate' => $fromDate, 'ToCreatedDate' => $toDate),
                'Fields' => [],
            );

            $params = ['request' => $request];
            $response = $this->client->GetAppointments($params)->GetAppointmentsResult;

            // $file_name = str_replace('\\', '/' , storage_path())."/tebra_patients.csv";
            // // Check if patients.csv file exists and delete it if it does
            // if (file_exists($file_name)) {
            //     unlink($file_name);
            // }
            // // Create the patients.csv file and add the header
            // file_put_contents($file_name, 'id,firtname,lastname,birthdate,'.'"'.'address'.'"'.','.'"'.'city'.'"'.','.'"'.'state'.'"'.',zipcode,created_date,phone_number' . PHP_EOL);

            // foreach($response->Patients->PatientData as &$value)
            // {
            //     $id = $value->ID;
            //     $firstname = ucwords(strtolower($value->FirstName));
            //     $lastname = ucwords(strtolower($value->LastName));
            //     $birthdate = $value->DOB;
            //     $address = $value->AddressLine1;
            //     $city = $value->City;
            //     $state = $value->State;
            //     $zipcode = $value->ZipCode;
            //     $created_date = $value->CreatedDate;
            //     $phone_number = $value->MobilePhone;
            //     $source = "tebra";
            //     $line = '"' . $id . '","' . $firstname . '","' . $lastname . '","' . $birthdate . '","' . $address . '","' . $city . '","' . $state . '","' . $zipcode . '","' . $created_date . '","' . $phone_number . '","' . $source . '"' . PHP_EOL;
            //     file_put_contents($file_name, $line, FILE_APPEND);
            // }

            return $response;
        } catch (\SoapFault $fault) {
            // Handle SOAP faults
            return response()->json(['error' => 'SOAP error: ' . $fault->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getPayments()
    {
        try {
            // Get the current date in the required format (YYYY-MM-DD)
            $toDate = '2024-01-01';
            $fromDate = '2001-01-01';
            // Define the request parameters
            $request = array (
                'RequestHeader' => array('User' => $this->user, 'Password' => $this->password, 'CustomerKey' => $this->customerKey, 'PracticeName' => 'TIN TeleHealth'),
                'Filter' => array('FromCreatedDate' => $fromDate, 'ToCreatedDate' => $toDate),
                'Fields' => [],
            );

            $params = ['request' => $request];
            $response = $this->client->GetPayments($params);

            // $file_name = str_replace('\\', '/' , storage_path())."/tebra_patients.csv";
            // // Check if patients.csv file exists and delete it if it does
            // if (file_exists($file_name)) {
            //     unlink($file_name);
            // }
            // // Create the patients.csv file and add the header
            // file_put_contents($file_name, 'id,firtname,lastname,birthdate,'.'"'.'address'.'"'.','.'"'.'city'.'"'.','.'"'.'state'.'"'.',zipcode,created_date,phone_number' . PHP_EOL);

            // foreach($response->Patients->PatientData as &$value)
            // {
            //     $id = $value->ID;
            //     $firstname = ucwords(strtolower($value->FirstName));
            //     $lastname = ucwords(strtolower($value->LastName));
            //     $birthdate = $value->DOB;
            //     $address = $value->AddressLine1;
            //     $city = $value->City;
            //     $state = $value->State;
            //     $zipcode = $value->ZipCode;
            //     $created_date = $value->CreatedDate;
            //     $phone_number = $value->MobilePhone;
            //     $source = "tebra";
            //     $line = '"' . $id . '","' . $firstname . '","' . $lastname . '","' . $birthdate . '","' . $address . '","' . $city . '","' . $state . '","' . $zipcode . '","' . $created_date . '","' . $phone_number . '","' . $source . '"' . PHP_EOL;
            //     file_put_contents($file_name, $line, FILE_APPEND);
            // }

            return $response;
        } catch (\SoapFault $fault) {
            // Handle SOAP faults
            return response()->json(['error' => 'SOAP error: ' . $fault->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
