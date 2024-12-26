<?php

namespace App\Http\Controllers\CURL;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Models\Employee;
use App\Models\PharmacyStaff;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ZenefitsController extends Controller
{
    public function fetchApiData($id = null)
    {   
        $helper = new Helper;
        $token = 'Waa2GUVhTY2rgHrajaFe';
        $client = new Client();
        $baseUrl = 'https://api.zenefits.com/core/people?limit=100';
        $nextUrl = $baseUrl;
        
        try {
            do {
                $response = $client->request('GET', $nextUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token.'',
                        'Accept' => 'application/json',
                    ],
                ]);

                $statusCode = $response->getStatusCode();

                if ($statusCode == 200) {
                    $data = json_decode($response->getBody()->getContents(), true);
                    // Process the data as needed
                    //Storage::disk('local')->append('file.txt', json_encode($data['data']['data'][0]['last_name']));
                    foreach($data['data']['data'] as $row){

                        if($helper->ProperNamingCase($row['status']) != 'Setup') {
                            $count = Employee::where('zen_id', $row['id'])
                                ->count();
                            if($count > 0){
                                $update = Employee::where('zen_id', $row['id'])
                                    ->first();
                                $update->firstname = $row['first_name'];
                                $update->lastname = $row['last_name'];
                                $update->nickname = $row['preferred_name'];
                                $update->date_of_birth = $row['date_of_birth'];
                                $update->position = $row['title'];
                                $update->status = $helper->ProperNamingCase($row['status']);
                                $update->email = Str::lower($row['work_email']);
                                $update->zen_id = $row['id'];
                                $update->save();
    
                                $employee_id = $update->id;
                            }
                            else{
                                $emp = new Employee();
                                $emp->firstname = $row['first_name'];
                                $emp->lastname = $row['last_name'];
                                $emp->nickname = $row['preferred_name'];
                                $emp->date_of_birth = $row['date_of_birth'];
                                $emp->position = $row['title'];
                                $emp->status = $helper->ProperNamingCase($row['status']);
                                $emp->email = Str::lower($row['work_email']);
                                $emp->zen_id = $row['id'];
                                $emp->initials_random_color = rand(1, 10);
                                $emp->save();
                                $employee_id = $emp->id;
                            }
    
                            if(!empty($id)) {
                                $pharma = PharmacyStaff::where('employee_id', $employee_id)->count();
                                if($pharma == 0) {
                                    $staff = new PharmacyStaff();
                                    $staff->pharmacy_store_id = $id;
                                    $staff->employee_id = $employee_id;
                                    $staff->save();
                                }
                            }
                        }

                    }

                    // If there's a next_url, update $nextUrl for the next iteration
                    if (!empty($data['data']['next_url'])) {
                        $nextUrl = $data['data']['next_url'];
                    } else {
                        // No next_url, break out of the loop
                        break;
                    }
                } else {
                    // Handle other status codes
                    return response()->json(['status'=>'warning','message' => 'Failed to fetch data.'], $statusCode);
                }
            } while (!empty($nextUrl));

                return response()->json(['status' => 'success', 'message' => 'Trinet HR synchronization successfully completed.'], 200);
            } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['status' => 'error','message' => $e->getMessage()], 500);
        }
    }

}
