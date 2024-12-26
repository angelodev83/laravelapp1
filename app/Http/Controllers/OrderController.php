<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Item;
use App\Models\Order;
use App\Models\RXStage;
use App\Models\RXStatus;
use App\Models\ShipmentStatus;
use App\Models\ShipmentStatusLog;
use App\Models\TransferPatient;
use App\Models\TransferPatientMedication;
use App\Models\TransferTaskAssignee;
use App\Models\TransferTaskDefault;
use App\Models\TransferTaskStatus;
use App\Models\TransferTaskStatusLog;
use Auth;
use Aws\S3\S3Client;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use View;


class OrderController extends Controller
{
    public function index(Request $request, $status_id = null, $stage_id = null)
    {
        $user = Auth::user();
        $statuses = RXStatus::orderBy('id', 'asc')->get();
        $stages = RXStage::orderBy('id', 'asc')->get();
        $shipment_statuses = ShipmentStatus::orderBy('id', 'asc')->get();
        $div3 = null;


        if (Route::currentRouteName() == 'division_three') {
            $div3 = '340b';
        }

        $viewPath = '';
        switch ($user->userType->id) {
            case 1:
                $viewPath = '/cs/orders/index';
                break;
            case 2:
                $viewPath = '/clients/orders/index';
                break;
            // Add more cases as needed
        }

        return view($viewPath, compact('user', 'statuses','shipment_statuses','stages', 'div3'));
    }


    public function getOrder($id)
    {
        $user = Auth::user();

        $order = Order::with(['patient', 'items.rxStatus', 'items.rxStage','shipmentStatus'])->find($id);
        $file = File::where('id', $order->file_id)->first();

        if (!$order) abort(404);
        return response()->json([
            'order' => $order,
            'file' => $file,    
        ]);
    }


    public function delete_order_via_ajax(Request $request)
    {
        $user = auth()->check() ? Auth::user() : redirect()->route('login');
        $input = $request->all();
        $order = Order::with('patient', 'items')->find($input['order_id']);
        
        $file = File::where('id', $input['file_id'])->first();

        if($file) {
            $path = $file->path.$file->filename;

            if($path != ''){
                if(Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }
                $forder = Order::with('patient', 'items')->find($input['order_id']);
                $forder->file_id = "";
                $forder->save();

                $file_data = File::where('id', $input['file_id'])->first();
                $file_data->delete();
            }
        } 

        if($order == null){
            return json_encode(
                ['status'=>'error',
                'message'=>'Order delete failed.']);
        } else {
            if($input['delete_file_only'] == 0){
                // Delete the items inside the order
                $order->items()->delete();

                //Delete all shipment status logs
                ShipmentStatusLog::where('order_id', $order->id)->delete();

                // Delete the order
                $order->delete();
            }

            return json_encode(['status'=>'success','message'=>'Order, its items, and the patient that owns the order have been deleted successfully.']);
        }
    }

    public function update(Request $request)
    {
        $order = Order::find($request->id);
        if ($order) {
            $order->{$request->column} = $request->value;
            $order->save();

            // If the shipment status has changed, log it
            if ($request->column == 'shipment_status_id' ) {
                ShipmentStatusLog::create([
                    'order_id' => $order->id,
                    'shipment_status_id' => $request->value,
                    'changed_at' => now(),
                ]);
            }
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 404);
        }
    }

    
    public function data(Request $request)
    {
        $model = Item::join('orders', 'items.order_id', '=', 'orders.id')
                                 ->join('patients', 'orders.patient_id', '=', 'patients.id')
                                 ->join('r_x_statuses', 'items.rx_status', '=', 'r_x_statuses.id')
                                 ->join('r_x_stages', 'items.rx_stage', '=', 'r_x_stages.id')
                                 ->join('shipment_statuses', 'orders.shipment_status_id', '=', 'shipment_statuses.id');

        if (request('view') == '340B') {
                $model = $model->where('items.inventory_type', '340B');
        }

        $model = $model->select('items.*', 'orders.id as orderid', 
                                'patients.id as patientid', 
                                'patients.firstname',  'patients.lastname', 
                                'r_x_statuses.id as rxstatusid', 
                                'r_x_stages.id as rxstagesid', 
                                'shipment_statuses.id as shipmentstatusid',
                                'r_x_statuses.name as r_x_statuses_name',
                                'r_x_stages.name as r_x_stages_name',
                                'shipment_statuses.name as shipment_statuses_name',
                                'orders.created_at as order_created_at',
                                'orders.updated_at as order_updated_at'
                            )
                        ->orderBy('items.created_at', 'desc');
                     
        // Add date filter if minDate and maxDate are provided
        if ($request->filled('minDate') && $request->filled('maxDate')) {
            $minDate = date('Y-m-d', strtotime($request->get('minDate')));
            $maxDate = date('Y-m-d', strtotime($request->get('maxDate')));
            $model->whereBetween('items.created_at', [$minDate, $maxDate]);
        }

       
        if ($request->filled('stage')) {
            $stageId = $request->get('stage');
            $model->where('items.rx_stage', $stageId);
        }

   
        if ($request->filled('status')) {
            $statusId = $request->get('status');
            $model->where('items.rx_status', $statusId);
        }

        
        if ($request->filled('shipment_status')) {
            $shipment_status_id = $request->get('shipment_status');
            $model->where('orders.shipment_status_id', $shipment_status_id);
        }

        if ($request->has('order')) {
            $order = $request->get('order');
            if ($order[0]['column'] == 0) { // order_number column
                $direction = $order[0]['dir'];
                $model->orderBy(DB::raw('order_number'), $direction);
            }
            if ($order[0]['column'] == 1) { // order_number column
                $direction = $order[0]['dir'];
                $model->orderBy(DB::raw('firstname'), $direction);
            }

            if ($order[0]['column'] == 7) { // rxStatus column
                $direction = $order[0]['dir'];
                $model->orderBy(DB::raw('r_x_statuses_name'), $direction);
            }

            if ($order[0]['column'] == 8) { // rxStage column
                $direction = $order[0]['dir'];
                $model->orderBy(DB::raw('r_x_stages_name'), $direction);
            }
            if ($order[0]['column'] == 9) { // shipmentStatus column
                $direction = $order[0]['dir'];
                $model->orderBy(DB::raw('shipment_statuses_name'), $direction);
            }

            if ($order[0]['column'] == 10) { // shipment_tracking_number column
                $direction = $order[0]['dir'];
                $model->orderBy(DB::raw('shipment_tracking_number'), $direction);
            }
            if ($order[0]['column'] == 11) { // created_at column
                $direction = $order[0]['dir'];
                $model->orderBy(DB::raw('items.created_at'), $direction);
            }

            if ($order[0]['column'] == 12) { // updated_at column
                $direction = $order[0]['dir'];
                $model->orderBy(DB::raw('items.updated_at'), $direction);
            }
            
        }

        // Initialize DataTables with the query
        $dataTable = DataTables::of($model)
        
            ->addColumn('rowid', function($model) {
                return $model->id;
            })
            ->addColumn('order_number', function($model) {
                return $model->order->order_number;
            })
            ->addColumn('tracking_number', function($model) {
                return $model->order->shipment_tracking_number;
            })
            // Add a column for the patient's full name
            ->addColumn('patient_name', function($model) {
                return $model->order->patient->firstname . ' ' . $model->order->patient->lastname;
            })
            ->addColumn('created_at', function($model) {
                return $model->order_created_at;
            })
            ->addColumn('updated_at', function($model) {
                return $model->order_updated_at;
            })
             // Add a column for the stage, with a custom HTML badge
             ->addColumn('rxStage', function($model) {
                return '<span class="badge p-3" style="color:' . $model->rxStage->text_color . '; background-color:' . $model->rxStage->color . '">' . $model->rxStage->name . '</span>';
            })
            // Add a column for the status, with a custom HTML badge
            ->addColumn('rxStatus', function($model) {
                return '<span class="badge p-3" style="color:' . $model->rxStatus->text_color . '; background-color:' . $model->rxStatus->color . '">' . $model->rxStatus->name . '</span>';
            })
             // Add a column for the status, with a custom HTML badge
             ->addColumn('shipment_status', function($model) {
                return '<span class="badge p-3" style="color:' . $model->order->shipmentStatus->text_color . '; background-color:' . $model->order->shipmentStatus->color . '">' . $model->order->shipmentStatus->name . '</span>';
            })
            
          
            
            // Add a column for the RX Image
            ->addColumn('rx_image', function($model) {
                if ($model->rxImage && $model->rxImage->path) {
                    $presignedUrl = Storage::disk('s3')->temporaryUrl(
                        $model->rxImage->path,
                        now()->addMinutes(30)
                    );
                    return '<a target="_blank" href="'.$presignedUrl.'" class="btn btn-info btn-sm"><i class="fa-solid fa-file-pdf" ></i></a>';
                }
                return '';
            })
            // Add a column for the In-take Form
            ->addColumn('intake_form', function($model) {
                if ($model->intakeForm && $model->intakeForm->path) {
                    $presignedUrl = Storage::disk('s3')->temporaryUrl(
                        $model->intakeForm->path,
                        now()->addMinutes(30)
                    );
                    return '<a target="_blank" href="'.$presignedUrl.'" class="btn btn-info btn-sm"><i class="fa-solid fa-file-pdf" ></i></a>';
                }
                return '';
            })
            
            // Add a column for the actions
            ->addColumn('actions', function($model) {
                return '
                    <button type="button" class="btn btn-secondary btn-sm" id="confirm_delete_product_btn" onclick="ShowConfirmDeleteForm(' . $model->order->id . ',' . $model->order->order_number . ')"><i class="fa-solid fa-trash-can"></i></button>
                ';
            })
            ->addColumn('rxImage', function($model) {
                if ($model->order->rxImage && $model->order->rxImage->filename) {
                    $presignedUrl = Storage::disk('s3')->temporaryUrl(
                        $model->order->rxImage->path.$model->order->rxImage->filename,
                        now()->addMinutes(30)
                    );
                    return '<a target="_blank" href="'.$presignedUrl.'" class="btn btn-info btn-sm"><i class="fa-solid fa-file-pdf" ></i></a>';
                }
                return '';


            })
            ->addColumn('intakeForm', function($model) {
                if ($model->order->rxImage && $model->order->intakeForm->filename) {
                    $presignedUrl = Storage::disk('s3')->temporaryUrl(
                        $model->order->intakeForm->path.$model->order->intakeForm->filename,
                        now()->addMinutes(30)
                    );
                    return '<a target="_blank" href="'.$presignedUrl.'" class="btn btn-info btn-sm"><i class="fa-solid fa-file-pdf" ></i></a>';
                }
                return '';


            })


            // Add a filter for the patient_name column
            ->filterColumn('patient_name', function($query, $keyword) {
                $sql = "CONCAT(patients.firstname, ' ', patients.lastname) like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            // Add a filter for the stage column
            ->filterColumn('rxStage', function($query, $keyword) {
                $query->where('r_x_stages.name', 'like', "%{$keyword}%");
            })
            // Add a filter for the status column
            ->filterColumn('rxStatus', function($query, $keyword) {
                $query->where('r_x_statuses.name', 'like', "%{$keyword}%");
            })
            
            // Allow HTML in the rx_image, intake_form, and actions columns
            ->rawColumns(['rx_image', 'intake_form', 'actions','rxStatus','rxStage','shipment_status','rxImage','intakeForm']);

        // Return the DataTable as JSON
        return $dataTable->toJson();
    }



    public function threerivers_transfer(Request $request){
        
            $input = $request->all();
    
            $api_key = 'pk_75398111_Z7KZRKQ57B18W95ZJ54NUH5Q9F60OKUQ';
           
            $list_tribe = '901102731518'; //tribe member
            $list_non_tribe = '901102809708'; //non-tribe member
            $general = '901102809715'; //general

            // Check if 'tribeMember' is set and assign the appropriate list ID
            if (isset($input['target_list'])) {
                if ($input['target_list'] == 'list1') {
                    $list_id = $list_tribe;
                } elseif ($input['target_list'] == 'list2') {
                    $list_id = $list_non_tribe;
                } else {
                    $list_id = $general;
                }
            }else{
                $list_id = $general;
            }
            
         
            // API endpoint for creating an item in a list
            $endpoint = "https://api.clickup.com/api/v2/list/".$list_id."/task";

    
            $combinedDrugs = '';
            for ($i = 0; $i < count($input['drugName']); $i++) {
                $combinedDrugs .= $input['drugName'][$i] . ' ' . $input['strength'][$i] . "\n";
            }

            // Item details
            $item_data = array(
                'name' => $input['firstName'] ." ". $input['lastName'], // Patient Name
                'description' => '', // notes   
                'custom_fields' => array(
                    array(
                        'id' => '21c58545-8c14-4092-813b-d9bd33b75830', //Address
                        'value' => $input['address'] .' '. $input['city'] .' '. $input['state'] .' '. $input['zip']
                    ),
                    array(
                        'id' => '0b28acd2-cb13-4bfd-b5a9-21e898492496', //phone
                        'value' => $input['phone'] 
                    ),
                    array(
                        'id' => 'ec4fba90-713f-4cfa-b079-1bd8dc552f4c', //County
                        'value' => $input['county'] 
                    ),
                    array(
                        'id' => '97d742d9-16d6-46e2-90af-63486e9544ad', //Current Pharmacy
                        'value' => $input['currentPharmacy'] 
                    ),
                    array(
                        'id' => '9d6b2d5f-aaa8-4506-85a0-b877d88efce3', //03/26/1986
                        'value' => $input['year'].'-'.$input['month'].'-'.$input['day']
                    ),
                    array(
                        'id' => 'bf5100d1-4ab4-475b-b173-da3a2a068038', //email
                        'value' => $input['email'] 
                    ),
                    array(
                        'id' => '0bb9c024-92a4-473f-beb7-5f9c741f9cc9', //Male
                        'value' => $input['gender'] 
                    ),
                    array(
                        'id' => '741f78fe-89c2-43a2-88e7-3adc663b92bb', //Pharmacy Address
                        'value' => $input['pharmacyAddress'] .' '. $input['pharmacyCity'] .' '. $input['pharmacyState'] .' '. $input['pharmacyZip']
                    ),
                    array(
                        'id' => '7596a7a2-f08d-4b02-b6e6-8543da34937b', //Pharmacy Zip
                        'value' => $input['pharmacyZip'] 
                    ),
                    array(
                        'id' => 'e5d8d20c-b159-4cec-9212-4433f4d36243', //Pharm phone
                        'value' => $input['pharmacyPhone'] 
                    ),
                    array(
                        'id' => '8e5f8f28-22a7-41ea-97b8-9944b972a6b8', //Pharmacy State
                        'value' => $input['pharmacyState'] 
                    ),
                    array(
                        'id' => 'ecdea673-9308-4fc0-a90d-ce5cfec0c563', //Prescriber name
                        'value' => (isset($input['prescriber_firstname']) ? $input['prescriber_firstname'] : "") .' '. (isset($input['prescriber_lastname']) ? $input['prescriber_lastname'] : "")
                    ),
                    array(
                        'id' => '307a125b-48a2-4435-bc6a-7533ea111c95', //Prescriber #
                        'value' => isset($input['prescriber_phoneNumber']) ? $input['prescriber_phoneNumber'] : ""
                    ),
                    array(
                        'id' => 'e3b84fe1-b5a4-4641-ac1c-0123c38e3330', //Prescriber Fax #
                        'value' =>  isset($input['prescriber_faxNumber']) ? $input['prescriber_faxNumber'] : ""
                    ),
                    array(
                        'id' => '6ba67780-cb20-45cc-a03b-940496a4086f', //State
                        'value' =>  isset($input['state']) ? $input['state'] : ""
                    ),
                    array(
                        'id' => '8d3622e7-3904-4cd1-90ee-e9b4c848e218', //zip
                        'value' => isset($input['zip']) ? $input['zip'] : ""
                    ),
                    array(
                        'id' => '600cf4b1-7a98-413c-a5b8-e6f57627c64c', //notes
                        'value' => 'notes' 
                    ),
                    array(

                        'id' => 'd14fb523-ad72-4e76-879d-58a4ad68ed9a', //Affiliate 
                        'value' =>  isset($input['affiliated_group']) ? $input['affiliated_group'] : ""
                    ),
                    array(
                        'id' => '92fae3d7-a763-4cff-87bb-1a146e587a8f', //Prefered Commmunication
                        'value' => isset($input['communication']) ? implode(", ", $input['communication']) : null
                    ),
                    array(
                        'id' => '1c0dc208-ec0e-4aea-aea0-0ade01a9a178', //Medication Info
                        'value' => $combinedDrugs
                    ),
                )
  
            
            );

            
            // Encode item data as JSON
            $item_json = json_encode($item_data);

            try {
                // cURL request setup
                $ch = curl_init($endpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $item_json);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: ' . $api_key,
                ));

                // Execute the request
                $response = curl_exec($ch);

                 // Check for errors
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                //todo, log this or echo for debug
            } else {

                //Do another CURL to Klaviyo
                
                $apiKey = 'pk_7fcba3c1f71907da4c7f585973ea460fa7'; // Replace with your API key
                $email =  $input['email'];
                $firstName = $input['firstName'];
                $lastName = $input['lastName'];

                $location = [
                    'address1' => $input['address'],
                    'city' => $input['city'],
                    'country' => $input['state'],
                    'zip' => $input['zip']
                ];


                $profileId = $this->createProfile($apiKey, $email, $firstName, $lastName, $location);

                if($profileId != null){
                    //Assign the email in the list
                    if (isset($input['target_list'])) {
                        if ($input['target_list'] == 'list1') {
                            $listId = 'UtSnCL'; //https://www.klaviyo.com/list/UtSnCL/ctclusi-within-5-counties
                        } elseif ($input['target_list'] == 'list2') {
                            $listId = 'XfY6hr'; //https://www.klaviyo.com/list/XfY6hr/ctclusi-outside-5-counties
                        } else {
                            $listId = 'TNmPNJ'; //https://www.klaviyo.com/list/TNmPNJ/ctclusi-general-public
                        }
                    }else{
                        $listId = 'TNmPNJ';
                    }

                    $result = $this->addProfileToList($profileId, $listId, $apiKey);

                }

                return redirect('https://www.tinrx.com/thank-you-success/');
            }

            } catch (Exception $e) {
                // Handle error silently
            }

           

            // Close cURL session
            curl_close($ch);
           
            
    }   

    public function addProfileToList($profileId, $listId, $apiKey) {
        try {
            $curl = curl_init("https://a.klaviyo.com/api/lists/$listId/relationships/profiles/");
    
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode([
                    'data' => [
                        [
                            'type' => 'profile',
                            'id' => $profileId
                        ]
                    ]
                ]),
                CURLOPT_HTTPHEADER => [
                    "Authorization:  Klaviyo-API-Key $apiKey",
                    "accept: application/json",
                    "content-type: application/json",
                    "revision: 2024-02-15"
                ],
            ]);
    
            $response = curl_exec($curl);
            $err = curl_error($curl);
    
            curl_close($curl);
    
            if ($err) {
                return null;
            } else {
                return $response;
            }
        } catch (Exception $e) {
            return null;
        }
    }

    public function createProfile($apiKey, $email, $firstName, $lastName, $location) {
        try {
            $curl = curl_init();
            
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://a.klaviyo.com/api/profiles/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'data' => [
                        'type' => 'profile',
                        'attributes' => [
                            'email' => $email,
                            'first_name' => $firstName,
                            'last_name' => $lastName,        
                            'location' => $location
                        ]
                    ]
                ]),
    
                CURLOPT_HTTPHEADER => [
                    "Authorization:  Klaviyo-API-Key $apiKey",
                    "accept: application/json",
                    "content-type: application/json",
                    "revision: 2024-02-15"
                ],
            ]);
    
            $response = curl_exec($curl);
            $err = curl_error($curl);
    
            curl_close($curl);
    
            if ($err) {
                return null;
            } else {
                $responseArray = json_decode($response, true);  
                return $responseArray['data']['id'] ?? null;
            }
        } catch (Exception $e) {
            return null;
        }
    }

    public function transfer_rx_array_store($array)
    {   
        $list_tribe = '1'; //tribe member
        $list_non_tribe = '2'; //non-tribe member
        $general = '3'; //general


        if (isset($array['target_list'])) {
            if ($array['target_list'] == 'list1') {
                $list_id = $list_tribe;
            } elseif ($array['target_list'] == 'list2') {
                $list_id = $list_non_tribe;
            } else {
                $list_id = $general;
            }
        }else{
            $list_id = $general;
        }

        if ($array['communication'] !== null) {
            $string_communication = implode(",", $array['communication']);
        } else {
            $string_communication = null; // Or any other default value you prefer
        }

        $transfer_patient = new TransferPatient();
        $transfer_patient->firstname = $array['firstname'];
        $transfer_patient->lastname = $array['lastname'];
        $transfer_patient->gender = $array['gender'];
        $transfer_patient->birthdate = $array['birthdate'];
        $transfer_patient->home_address = $array['home_address'];
        $transfer_patient->city = $array['city'];
        $transfer_patient->state = $array['state'];
        $transfer_patient->county = $array['county'];
        $transfer_patient->zip = $array['zip'];
        $transfer_patient->phone_number = $array['phone_number'];
        $transfer_patient->email = $array['email'];
        $transfer_patient->affiliated = $array['affiliated'];
        $transfer_patient->communication = $string_communication;
        $transfer_patient->current_pharmacy = $array['current_pharmacy'];
        $transfer_patient->pharmacy_phone_number = $array['pharmacy_phone_number'];
        $transfer_patient->pharmacy_address = $array['pharmacy_address'];
        $transfer_patient->pharmacy_city = $array['pharmacy_city'];
        $transfer_patient->pharmacy_state = $array['pharmacy_state'];
        $transfer_patient->pharmacy_zip = $array['pharmacy_zip'];
        $transfer_patient->prescriber_firstname = $array['prescriber_firstname'];
        $transfer_patient->prescriber_lastname = $array['prescriber_lastname'];
        $transfer_patient->prescriber_phone_number = $array['prescriber_phone_number'];
        $transfer_patient->prescriber_fax_number = $array['prescriber_fax_number'];
        $transfer_patient->transfer_list_id = $list_id;
        $transfer_patient->save();


        $task_status = new TransferTaskStatus();
        $task_status->transfer_patient_id = $transfer_patient->id;

        $default_task = TransferTaskDefault::where('name', 'task')->first();
        if($default_task !== null){
            $default_task_id = $default_task->default_id;
        } 
        else{
            $default_task_id = 2;
        }
        
        $task_status->transfer_task_id = $default_task_id;
        $task_status->due_date = null;
        $task_status->shipping_type = '';
        $task_status->status = 'active';
        $task_status->save();

        $task_status_log = new TransferTaskStatusLog();
        $task_status_log->transfer_task_status_id = $task_status->id;
        $task_status_log->transfer_task_id = $default_task_id;
        $task_status_log->change_at = null;
        $task_status_log->status = 'active';
        $task_status_log->save();

        $default_assignee = TransferTaskDefault::where('name', 'assignee')->first();
        if ($default_assignee !== null) {
            $task_assignee = new TransferTaskAssignee();
            $task_assignee->transfer_task_status_id = $task_status->id;
            $task_assignee->user_id = $default_assignee->default_id;
            $task_assignee->save();
        }
        
        // foreach ($array['medications'] as $row) {
        //     $medication = new TransferPatientMedication();
        //     $medication->name = $row['drugname'];
        //     $medication->strength = $row['strength'];
        //     $medication->t_patient_id = $transfer_patient->id;
        //     $medication->save();
        // }

        if (isset($array['medications'])) {
            foreach ($array['medications'] as $row) {
                $medication = new TransferPatientMedication();
                $medication->name = $row['drugname'];
                $medication->strength = $row['strength'];
                $medication->t_patient_id = $transfer_patient->id;
                $medication->save();
            }
        }
        
        // Storage::disk('local')->put('sample_array.txt', json_encode($array));
    }

    






}
