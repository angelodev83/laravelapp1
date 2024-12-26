<?php

namespace App\Http\Controllers\CURL;
// Set maximum execution time to 300 seconds (5 minutes)
ini_set('max_execution_time', 600);
use App\Http\Controllers\Controller;
use App\Services\CsvService;
use App\Services\TebraService;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use SoapClient;
use SoapFault;
use SoapHeader;

class TebraController extends Controller
{
    private $tebraService;

    public function __construct(TebraService $tebraService)
    {
        $this->tebraService = $tebraService;
    }

    public function get_patient()
    {
        $response = $this->tebraService->getPatient(25535);
         echo '<pre>';
        print_r($response);
        //return view('patients.index');
    }

    public function get_patients(CsvService $csvService, Request $request)
    {
        if($request->ajax()){
        
            $response = $this->tebraService->getPatientsDateStartRange('2001-01-01');
        
            $csvService->loadPatientsCsv();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Success.'
            ]);
        }
         
    }

    public function store()
    {
        $response = $this->tebraService->createPatient();
        
        // return response()->json([
        //     'message' => 'Success.'
        // ]);
        echo '<pre>';
        print_r($response);
    }

    public function getPayments()
    {
        $response = $this->tebraService->getPayments();
         echo '<pre>';
        print_r($response);
        //return view('patients.index');
    }

    public function getAppointments()
    {
        $response = $this->tebraService->getAppointments();
        
        // return response()->json([
        //     'message' => 'Success.'
        // ]);
        echo '<pre>';
        print_r($response);
    }

    public function getAllPatients(CsvService $csvService, Request $request)
    {
        if($request->ajax()){
            $response = $this->tebraService->getAllPatients();
            
            $csvService->loadPatientsCsv();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Success.'
            ]);
        }
    }

    public function get_everydayPatients()
    {
        $response = $this->tebraService->getEverydayPatient();
        
        return response()->json([
            'message' => 'Success.'
        ]);
        // echo '<pre>';
        // print_r($response);
    }

    public function get_everydayUpdatePatientData()
    {
        $response = $this->tebraService->getEverydayUpdatePatientData();
        
        return response()->json([
            'message' => 'Success.'
        ]);
        // echo '<pre>';
        // print_r($response);
    }

    public function get_tebra()
    {
        try{
            $user = 'pnachman@tinrx.com';
            $password = 'Summerfun0!';
            $customerKey = 'c42jy85wm69o';

            $wsdl = 'https://webservice.kareo.com/services/soap/2.1/KareoServices.svc?singleWsdl';
            // $client = new SoapClient($wsdl);

            // //Define SOAP request parameters

            // $client = new SoapClient($wsdl);

            // $request = array(
            //     'RequestHeader' => array('User' => $user, 'Password' => $password, 'CustomerKey' => $customerKey),
            //     'Filter' => array('nextStartKey' => '23287'), // NextStartKey should be outside Filter
            //     'Fields' => array() // Fields to retrieve, if any
            // );

            // $params = array('request' => $request);

            // $response = $client->GetAllPatients($params);


            //working
            $client = new SoapClient($wsdl);

            // // Start date set to January 2021
            // $startDate = new DateTime('2021-01-01');
            // $endDate = new DateTime(); // Current date

            // // Loop through each month starting from January 2021 up to the current month
            // while ($startDate <= $endDate) {
            //     // Get the year and month
            //     $year = $startDate->format('Y');
            //     $month = $startDate->format('m');

            //     // Construct the end date for the current month
            //     $lastDayOfMonth = $startDate->format('t');
            //     $endDateOfMonth = new DateTime("$year-$month-$lastDayOfMonth");

            //     // Construct the request
            //     $request = array(
            //         'RequestHeader' => array(
            //             'User' => $user,
            //             'Password' => $password,
            //             'CustomerKey' => $customerKey
            //         ),
            //         'Filter' => array(
            //             'FromCreatedDate' => $startDate->format('Y-m-d'),
            //             'ToCreatedDate' => $endDateOfMonth->format('Y-m-d')
            //         ),
            //         'Fields' => array() // Fields to retrieve, if any
            //     );

            //     $params = array('request' => $request);

            //     try {
            //         // Make the SOAP call for the current month
            //         $response = $client->GetPatients($params)->GetPatientsResult;
            //         // Process the response
            //         if (!empty($response->Patients->PatientData)) {
            //             foreach ($response->Patients->PatientData as &$value) {
            //                 // print($value->ID. ' - '. $value->PatientFullName. '<br />');
            //                 if (!empty($value->LastName) && !empty($value->FirstName)) {
            //                     Storage::disk('local')->append('file.txt', json_encode(@$value->LastName . ', ' . @$value->FirstName));
            //                 }
            //             }
            //         }
            //         // Move to the next month
            //         $startDate->modify('first day of next month');
            //     } catch (SoapFault $fault) {
            //         echo "Error: " . $fault->getMessage();
            //         break; // Exit loop on error
            //     }
            // }
            //working-end

            $request = array (
                'RequestHeader' => array('User' => $user, 'Password' => $password, 'CustomerKey' => $customerKey),
                'Filter' => array('FromCreatedDate' => '2010-01-01', 'ToCreatedDate' => '2023-12-30'),
                'Fields' => [],
            );

            $params = array('request' => $request);
            
            // Call the SOAP operation to fetch data
            $response = $client->GetPatients($params)->GetPatientsResult;//->Patients;//->GetAllPatientsResult;

            // // Convert the response to JSON
            // $jsonResponse = json_encode($response);

            // return $jsonResponse;

            echo '<pre>';
            print_r($response);

            foreach($response->Patients->PatientData as &$value)
            {
                // print($value->ID. ' - '. $value->PatientFullName. '<br />');
                Storage::disk('local')->append('file.txt', json_encode(@$value->LastName.', '.@$value->FirstName));
            }
        } catch (Exception $err) {
            print "Error: ". $err->getMessage();
        }
    }

    public function history()
    {
        // $inmar_old = [
        //     "id" => 20,
        //     "name" => "1243sfga",
        //     "clinic_id" => 2,
        //     "drug_id" => 7908,
        //     "quantity" => 45,
        //     "ndc" => "12313",
        //     "type" => "RETURNS",
        //     "status" => null,
        //     "return_date" => "2024-02-16 00:00:00",
        //     "comments" => "Testing",
        //     "created_at" => "2024-02-15T02:18:23.000000Z",
        //     "updated_at" => "2024-02-15T02:18:23.000000Z",
        //     "prescriber_name" => "Mickey Me"
        // ];

        // $inmar_new = [
        //     "id" => 20,
        //     "name" => "1243sfga",
        //     "clinic_id" => "3",
        //     "drug_id" => "5567",
        //     "quantity" => "5",
        //     "ndc" => "ndc",
        //     "type" => "EXPIRED",
        //     "status" => "PROCESSED",
        //     "return_date" => "2024-02-01",
        //     "comments" => "Testing 2",
        //     "created_at" => "2024-02-15T02:18:23.000000Z",
        //     "updated_at" => "2024-02-15T02:22:55.000000Z",
        //     "prescriber_name" => "Mickey Mouse"
        // ];

        // $differences = [];

        // foreach ($inmar_old as $key => $value) {
        //     if ($inmar_new[$key] != $value) {
        //         $differences[$key] = [
        //             "old" => $value,
        //             "new" => $inmar_new[$key]
        //         ];
        //     }
        // }

        // foreach ($inmar_new as $key => $value) {
        //     if (!isset($inmar_old[$key])) {
        //         $differences[$key] = [
        //             "old" => null,
        //             "new" => $value
        //         ];
        //     }
        // }

        // print_r($differences);

        $inmar_old = [
            "id" => 20,
            "name" => "1243sfga",
            "clinic_id" => 2,
            "drug_id" => 7908,
            "quantity" => 45,
            "ndc" => "12313",
            "type" => "RETURNS",
            "status" => null,
            "return_date" => "2024-02-16 00:00:00",
            "comments" => "Testing",
            "created_at" => "2024-02-15T02:18:23.000000Z",
            "updated_at" => "2024-02-15T02:18:23.000000Z",
            "prescriber_name" => "Mickey Me"
        ];

        $inmar_new = [
            "id" => 20,
            "name" => "1243sfga",
            "clinic_id" => "3",
            "drug_id" => "5567",
            "quantity" => "5",
            "ndc" => "ndc",
            "type" => "EXPIRED",
            "status" => "PROCESSED",
            "return_date" => "2024-02-01",
            "comments" => "Testing 2",
            "created_at" => "2024-02-15T02:18:23.000000Z",
            "updated_at" => "2024-02-15T02:22:55.000000Z",
            "prescriber_name" => "Mickey Mouse"
        ];

        $log_messages = [];

        foreach ($inmar_old as $key => $value) {
            if ($inmar_new[$key] != $value) {
                $log_messages[] = "Changed '$key' from '$value' to '{$inmar_new[$key]}'";
            }
        }

        foreach ($inmar_new as $key => $value) {
            if (!isset($inmar_old[$key])) {
                $log_messages[] = "Added '$key' with value '{$inmar_new[$key]}'";
            }
        }

        foreach ($inmar_old as $key => $value) {
            if (!isset($inmar_new[$key])) {
                $log_messages[] = "Removed '$key' with value '$value'";
            }
        }

        foreach ($log_messages as $log) {
            echo $log . PHP_EOL;
            echo '<br/>';
        }
        //echo implode(PHP_EOL, $log_messages);

    }
}
