<?php

namespace App\Http\Controllers\Transfer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderController;
use App\Http\Helpers\Helper;
use App\Models\Country;
use App\Models\Employee;
use App\Models\File;
use App\Models\TransferPatient;
use App\Models\TransferPatientMedication;
use App\Models\TransferTask;
use App\Models\TransferTaskAssignee;
use App\Models\TransferTaskComment;
use App\Models\TransferTaskDefault;
use App\Models\TransferTaskFile;
use App\Models\TransferTaskStatus;
use App\Models\TransferTaskStatusLog;
use App\Models\User;
use Aws\S3\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Constraint\Count;

class TribeMemberController extends Controller
{
    public function index($id, Request $request)
    {
        try {
            $this->checkStorePermission($id);

            $user = Auth::user();
            $breadCrumb = ['Patient Support', 'Tribes'];
            
            $transferTasks = TransferTask::all()->sortBy('sort');


            return view('/executiveDashboard/trp/patientSupport/tribeMembers/index', compact('user','breadCrumb','transferTasks'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }
    
    public function get_countries(Request $request)
    {
        if($request->ajax()){
            $countries = Country::get()->toArray();

            return response()->json([
                "status" => 'success',
                "countries" => $countries,
            ], 200);
        }
    }

    public function file_download($user_id, $file_id)
    {   
        $file = File::where('id', $file_id)->first();
        
        $headers = [
            'Content-Type'        => 'Content-Type: '.$file->mime_type.' ',
            'Content-Disposition' => 'attachment; filename="'. $file->filename .'"',
        ];

        $path = $file->path.$file->filename;

        return Response::make(Storage::disk('s3')->get($path), 200, $headers);
    }

    public function file_upload(Request $request)
    {
        if ($request->ajax()) {
            $status_id = $request->input('status_id');
            $files = $request->file('files');
            $files_save = [];

            // Check if $files is not null before proceeding
            if ($files !== null) {
                $input = $request->all();
                $validation = Validator::make($input, [
                    'files.*' => 'required|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,csv', // Validate each file in the array
                ]);

                if ($validation->passes()) {
                    foreach ($files as $file) {
                        if ($file->isValid()) {
                            $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                            $fileExtension = $file->getClientOriginalExtension();
                            $mime_type = $file->getMimeType();

                            $newFileName = date("Ymdhis") . Auth::id() . '_' . $fileName . '.' . $fileExtension;
                            $doc_type = $fileExtension;

                            $path = 'patient_support/transfer_patient/';

                            // Provide a dynamic path or use a specific directory in your S3 bucket
                            $path_file = 'patient_support/transfer_patient/' . $newFileName;

                            // Store the file in S3
                            Storage::disk('s3')->put($path_file, file_get_contents($file));

                            // Optionally, get the URL of the uploaded file
                            $s3url = Storage::disk('s3')->url($path_file);

                            $save_file = new File();

                            $save_file->filename = $newFileName;
                            $save_file->path = $path;
                            $save_file->mime_type = $mime_type;
                            $save_file->document_type = $doc_type;
                            $save_file->save();

                            $file_id = $save_file->id;

                            $file = new TransferTaskFile();
                            $file->transfer_task_status_id = $status_id;
                            $file->file_id = $file_id;
                            $file->save();

                            // Push $save_file into $files_save array
                            $files_save[] = $save_file;
                        } else {
                            return response()->json([
                                'error' => 'Invalid file',
                                'status' => 'error',
                                'message' => 'Invalid file'
                            ]);
                        }
                    }

                    // Return $files_save in JSON response
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Records have been saved.',
                        'files_save' => $files_save
                    ]);
                }
                else {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $validation->errors(),
                        'message' => 'Invalid file format.'
                    ]);
                }
            } else {
                // Handle case where no files are selected
                return response()->json([
                    'status' => 'error',
                    'message' => 'No files selected.'
                ]);
            }
        }


    }

    public function delete_file(Request $request)
    {
        if($request->ajax())
        {
            $id = $request->input('id');
            
            $file = File::where('id', $id)->first();
            $path = $file->path.$file->filename;
            
            if($path != ''){
                if(Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }
                $file->delete();

                $transfer_task_file = TransferTaskFile::where('file_id', $id)->first();
                $transfer_task_file->delete();
            }
            

            return response()->json([
                "status" => 'success',
                "message" => 'Record has been deleted.',
            ], 200);
        }
    }

    public function comment_store(Request $request)
    {
        if($request->ajax()){
            $commentInput = $request->input('comment');
            $status_log_id = $request->input('logs_id');
            $status_id = $request->input('status_id');
            $user_id = Auth::id();
            $date = date("Y-m-d H:i:s", strtotime($request->input('today')));

            $transferTaskComment = new TransferTaskComment();
            $transferTaskComment->comment = $commentInput; // Use distinct variable name here
            $transferTaskComment->transfer_task_status_id = $status_id;
            $transferTaskComment->transfer_task_status_log_id = $status_log_id;
            $transferTaskComment->user_id = $user_id;
            $transferTaskComment->created_at = $date;
            $transferTaskComment->updated_at = $date;
            $transferTaskComment->save();

            return response()->json([
                "status"=> 'success',
            ], 200);
        }
    }

    public function patient_store(Request $request, OrderController $orderController)
    {
        if($request->ajax()){
            $data = [];
            $helper =  new Helper;
            $input = $request->all();

            $validation = Validator::make($input, [
                'firstname' => 'required',
                'lastname' => 'required',
                // 'gender' => 'required',
                // 'birthdate' => 'required|date|date_format:Y-m-d',
                // 'home_address' => 'required',
                // 'city' => 'required',
                // 'country' => 'required',
                // 'state' => 'required',
                // 'zip' => 'required',
                // 'phone_number' => 'required',
                // 'affiliated' => 'required',
                // 'email' => 'required',
                // 'communication[]' => 'required',
                // 'prescriber_firstname' => 'required',
                // 'prescriber_lastname' => 'required',
                // 'prescriber_phone_number' => 'required',
                // 'prescriber_fax_number' => 'required',
            ]);

            if ($validation->passes()){
                // Create Item entries
                $check_entry = 0;
                for ($i = 0; $i <= $input['med_count']; $i++) {
                    if (!empty($request->input("drugname$i")&&$request->input("strength$i"))) {
                        $data['medications'][]=array(
                            "drugname" => $input["drugname$i"],
                            "strength" => $input["strength$i"],
                        );
                        $check_entry = 1;
                    }
                }
                //return no entry of medication
                // if($check_entry === 0)
                // {
                //     $medication_validate = [
                //         "medication_holder" => ["Input at least one medication field."],
                //         "message" => "Record saving failed."
                //     ];

                //     return json_encode([
                //         'status'=>'error',
                //         'errors'=> $medication_validate,
                //         'message'=>'Record saving failed.'
                //     ]);
                // }

                $data['target_list'] = $input['target_list'];
                $data['firstname'] = $helper->ProperNamingCase($input['firstname']);
                $data['lastname'] = $helper->ProperNamingCase($input['lastname']);
                $data['gender'] = $input['gender'];
                $data['birthdate'] = $input['birthdate'];
                $data['home_address'] = $input['home_address'];
                $data['city'] = $input['city'];
                $data['county'] = $input['county'];
                $data['state'] = $input['state'];
                $data['zip'] = $input['zip'];
                $data['phone_number'] = $input['phone_number'];
                $data['email'] = $input['email'];
                $data['affiliated'] = $input['affiliated'];
                $data['communication'] = isset($input['communication[]']) ? $input['communication[]'] : null;
                $data['current_pharmacy'] = $input['current_pharmacy'];
                $data['pharmacy_phone_number'] = $input['pharmacy_phone_number'];
                $data['pharmacy_address'] = $input['pharmacy_address'];
                $data['pharmacy_city'] = $input['pharmacy_city'];
                $data['pharmacy_state'] = $input['pharmacy_state'];
                $data['pharmacy_zip'] = $input['pharmacy_zip'];
                $data['prescriber_firstname'] = $input['prescriber_firstname'];
                $data['prescriber_lastname'] = $input['prescriber_lastname'];
                $data['prescriber_phone_number'] = $input['prescriber_phone_number'];
                $data['prescriber_fax_number'] = $input['prescriber_fax_number'];

                $orderController->transfer_rx_array_store($data);

                $default_task = TransferTaskDefault::where('name', 'task')->first();
                if($default_task !== null){
                    $default_task_id = $default_task->default_id;
                } 
                else{
                    $default_task_id = 2;
                }
                
                return json_encode([
                    "task_to" => $default_task_id,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
            else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Record saving failed.'
                ]);
            }
        }
    }

    public function update_task(Request $request)
    {
        if($request->ajax()){
            $id = $request->input('id');
            $task_id = $request->input('task_id');
            $task_from = $request->input('task_from');

            // Find the TransferTaskStatus instance
            $transferTaskStatus = TransferTaskStatus::where('transfer_patient_id', $id)
                ->where('status', 'active')
                ->first();

            if($transferTaskStatus){
                // Update transfer_task_id attribute
                $transferTaskStatus->transfer_task_id = $task_id;

                // Save the changes to the TransferTaskStatus
                $transferTaskStatus->save();

                // Update TransferTaskStatusLog
                $transferTaskStatusLog = TransferTaskStatusLog::where('transfer_task_status_id', $transferTaskStatus->id)
                    ->where('status', 'active')
                    ->first();

                if($transferTaskStatusLog){
                    // Change status to 'done' and update change_from
                    $transferTaskStatusLog->status = 'done';
                    $transferTaskStatusLog->change_from = $task_from;
                    $transferTaskStatusLog->save();

                    // Create new TransferTaskStatusLog
                    $newTransferTaskStatusLog = new TransferTaskStatusLog();
                    $newTransferTaskStatusLog->transfer_task_status_id = $transferTaskStatus->id;
                    $newTransferTaskStatusLog->transfer_task_id = $task_id;
                    $newTransferTaskStatusLog->change_at = Carbon::now(); // Set current datetime
                    $newTransferTaskStatusLog->save();

                    $task = TransferTask::where('id', $task_id)->first();

                    return response()->json([
                        "status" => 'success',
                        "message" => 'Record has been updated.',
                        "task_from" => $task_id,
                        "task_to" => $task_id,
                        "task" => $task,
                    ], 200);
                } 
                else{
                    // Handle case where TransferTaskStatusLog is not found
                    return response()->json([
                        "status" => 'error',
                        "message" => 'TransferTaskStatusLog not found.',
                    ], 404);
                }
            }
            else{
                // Handle case where TransferTaskStatus is not found
                return response()->json([
                    "status" => 'error',
                    "message" => 'TransferTaskStatus not found.',
                ], 404);
            }
        }
    }

    public function update_clicked_column(Request $request)
    {
        if($request->ajax())
        {
            $status_log_id = $request->input('task_log_id');
            $value = $request->input('value');
            $field = $request->input('column_name');
            $validate_data = array(
                $field => $value
            );

            $transferPatient = TransferPatient::join('transfer_task_statuses', 'transfer_patients.id', '=', 'transfer_task_statuses.transfer_patient_id')
                ->join('transfer_task_status_logs', 'transfer_task_statuses.id', '=', 'transfer_task_status_logs.transfer_task_status_id')
                ->select('transfer_patients.id', 'transfer_patients.firstname', 'transfer_task_status_logs.id as status_logs_id', 'transfer_task_status_logs.transfer_task_id as task_id')
                ->where('transfer_task_status_logs.status', 'active')
                ->where('transfer_task_status_logs.id', $status_log_id)
                ->first();

            $messages = [
                'birthdate.required' => 'The Date of Birth field is required.',
                'birthdate.date' => 'The Date of Birth must be a valid date and format: YYYY-MM-DD.',
                'birthdate.date_format' => 'The Date of Birth must be in the format: YYYY-MM-DD.', // Custom message for date_format rule
                'affiliated.required' => 'The Affiliation field is required.',
                'communication.required' => 'The Preferred Communication field is required.',
                'phone_number.required' => 'The CB# field is required.',
                'home_address.required' => 'The Address field is required.',
                'city.required' => 'The City field is required.',
                'state.required' => 'The State field is required.',
                'county.required' => 'The County field is required.',
                'current_pharmacy.required' => 'The Current Pharmacy field is required.',
                'pharmacy_phone_number.required' => 'The Pharnacy Phone # field is required.',
                'pharmacy_address.required' => 'The Pharmacy Address field is required.',
                'pharmacy_city.required' => 'The Pharmacy City field is required.',
                'pharmacy_zip.required' => 'The Pharmacy Zip field is required.',
                'pharmacy_state.required' => 'The Pharmacy State field is required.',
                'prescriber_firstname.required' => 'The Prescriber First Name field is required.',
                'prescriber_lastname.required' => 'The Prescriber Last Name field is required.',
                'prescriber_phone_number.required' => 'The Prescriber # field is required.',
                'prescriber_fax_number.required' => 'The Prescriber Fax # field is required.',
                'due_date.required' => 'The Due Date field is required.',
                'email.required' => 'The Email field is required.',
                'zip.required' => 'The Zip field is required.',
                'due_date.date' => 'The Due Date must be a valid date.',
                'gender.required' => 'The Gender field is required.',
                'notes.required' => 'The Notes field is required.',
                'firstname.required' => 'The First Name field is required.',
                'lastname.required' => 'The Last Name field is required.',   
            ];

            // Define validation rules based on the field name
            if ($field === 'birthdate') {
                $rules = [
                    'birthdate' => 'required|date|date_format:Y-m-d',
                ];
            }
            elseif ($field === 'firstname') {
                $rules = [
                    'firstname' => 'required',
                ];
            }
            elseif ($field === 'lastname') {
                $rules = [
                    'lastname' => 'required',
                ];
            }
            elseif ($field === 'due_date') {
                $rules = [
                    'due_date' => 'required|date',
                ];
            }
            elseif ($field === 'notes') {
                $rules = [
                    'notes' => 'required',
                ];
            }
            elseif ($field === 'gender') {
                $rules = [
                    'gender' => 'required',
                ];
            }
            elseif ($field === 'pharmacy_zip') {
                $rules = [
                    'pharmacy_zip' => 'required',
                ];
            }
            elseif ($field === 'zip') {
                $rules = [
                    'zip' => 'required',
                ];
            }
            elseif ($field === 'affiliated') {
                $rules = [
                    'affiliated' => 'required',
                ];
            }
            elseif ($field === 'communication') {
                $rules = [
                    'communication' => 'required',
                ];
            } 
            elseif ($field === 'phone_number') {
                $rules = [
                    'phone_number' => 'required', 
                ];
            } 
            elseif ($field === 'home_address') {
                $rules = [
                    'home_address' => 'required', 
                ];
            } 
            elseif ($field === 'city') {
                $rules = [
                    'city' => 'required', 
                ];
            } 
            elseif ($field === 'state') {
                $rules = [
                    'state' => 'required', 
                ];
            } 
            elseif ($field === 'county') {
                $rules = [
                    'county' => 'required', 
                ];
            } 
            elseif ($field === 'current_pharmacy') {
                $rules = [
                    'current_pharmacy' => 'required', 
                ];
            } 
            elseif ($field === 'pharmacy_phone_number') {
                $rules = [
                    'pharmacy_phone_number' => 'required', 
                ];
            } 
            elseif ($field === 'pharmacy_address') {
                $rules = [
                    'pharmacy_address' => 'required', 
                ];
            } 
            elseif ($field === 'pharmacy_city') {
                $rules = [
                    'pharmacy_city' => 'required', 
                ];
            } 
            elseif ($field === 'pharmacy_state') {
                $rules = [
                    'pharmacy_state' => 'required', 
                ];
            }
            elseif ($field === 'prescriber_firstname') {
                $rules = [
                    'prescriber_firstname' => 'required', 
                ];
            }
            elseif ($field === 'prescriber_lastname') {
                $rules = [
                    'prescriber_lastname' => 'required', 
                ];
            }
            elseif ($field === 'prescriber_phone_number') {
                $rules = [
                    'prescriber_phone_number' => 'required', 
                ];
            }
            elseif ($field === 'prescriber_fax_number') {
                $rules = [
                    'prescriber_fax_number' => 'required', 
                ];
            }
            elseif ($field === 'email') {
                $rules = [
                    'email' => 'required', 
                ];
            }
            else {
                // Default validation rules for other fields
                $rules = [
                    'value' => 'required', // Default validation rule if no specific rule matches
                ];
            }

            // Run validation
            $validation = Validator::make($validate_data, $rules, $messages);

            // Check if validation fails
            if ($validation->fails()) {
                return response()->json([
                    "status" => 'error',
                    "errors" => $validation->errors()->first(),
                    'message'=>'Record saving failed.',
                    "task_to" => $transferPatient->task_id,
                ], 200); // Return 400 Bad Request status
                // return json_encode([
                //     'status'=>'error',
                //     'errors'=> $validation->errors(),
                //     'message'=>'Employee saving failed.'
                // ]);
            }
            
            if ($transferPatient) {
                if($field === "due_date" || $field === "notes"){
                    if ($field === "due_date") {
                        $new_value = date("Y-m-d", strtotime($value));
                        
                    } else if ($field === "notes") {
                        $new_value = $value;
                    }
                    $transfer_task = TransferTaskStatus::where('transfer_patient_id', $transferPatient->id)->first();
                    $transfer_task->update([$field => $new_value]);
                    return response()->json([
                        "status" => 'success',
                        "message" => 'Record has been updated.',
                        "task_to" => $transferPatient->task_id,
                    ], 200);
                }
                else{
                    $transfer_patient = TransferPatient::find($transferPatient->id);
                    // Update the specific field based on the $field value
                    $transfer_patient->update([$field => $value]);

                    return response()->json([
                        "status" => 'success',
                        "message" => 'Record has been updated.',
                        "task_to" => $transferPatient->task_id,
                    ], 200);
                }
                
            }
        }
    }

    public function get_default_list(Request $request)
    {
        if($request->ajax()){
            $users = User::join('employees', 'users.id', '=', 'employees.user_id')
                ->select('employees.firstname', 'employees.lastname', 'users.id AS id')
                ->get();
            $default_assignee = TransferTaskDefault::where('name','assignee')->first();
            if ($default_assignee !== null) {
                $defaultAssignee = $default_assignee;
            } else {
                $defaultAssignee = 0; 
            }
            $tasks = TransferTask::get();
            $default_task = TransferTaskDefault::where('name', 'task')->first();
            if ($default_task !== null) {
                $defaultTask = $default_task;
            } else {
                $defaultTask = 0; 
            }
            return response()->json([
                "status"=> 'success',
                "users"=> $users,
                "default_assignee" => $defaultAssignee,
                "tasks" => $tasks,
                "default_task" =>$defaultTask,
            ], 200);
        }
    }

    public function update_default(Request $request)
    {
        if($request->ajax()){
            $default_id = $request->input('default_id');
            $default_name = $request->input('default_name');

            $default_assignee = TransferTaskDefault::where('name', $default_name)->first();
            if ($default_assignee !== null) {
                $default_assignee->default_id = $default_id;
                $default_assignee->save();
            } else {
                $new_default_assignee = new TransferTaskDefault();
                $new_default_assignee->name = $default_name;
                $new_default_assignee->default_id = $default_id;
                $new_default_assignee->save();
            }

            return response()->json([
                "status"=> 'success',
                "message" => 'Record has been updated',
            ], 200);
        }
    }

    public function get_shipping_type(Request $request)
    {
        if($request->ajax()){
           $data = explode(',', env('TRANSFER_TASK_SHIPPING_TYPE'));

            return json_encode([
                'data'=> $data,
            ]);
        }
    }

    public function update_shipping_type(Request $request)
    {
        if($request->ajax()){
            $shipping_type = $request->input('shipping_type');
            $task_status_id = $request->input('task_status_id');
           
            // Find the TransferTaskStatus instance
            $transferTaskStatus = TransferTaskStatus::where('id', $task_status_id)->first();
            $transferTaskStatus->shipping_type = $shipping_type;
            $transferTaskStatus->save();
            return response()->json([
                "status" => 'success',
                "message" => 'Record has been updated.',
            ], 200);
        }
    }

    public function get_patient_data(Request $request)
    {
        if($request->ajax()){
            $data = [];
            $task = TransferTask::all()->sortBy('sort_field');
            $task_patient = TransferPatient::select('transfer_patients.id AS id',
                'transfer_task_statuses.transfer_task_id AS transfer_task_id',
                'transfer_task_statuses.id AS task_status_id', 'transfer_task_statuses.notes',
                'transfer_patients.prescriber_firstname', 'transfer_patients.prescriber_lastname',
                'transfer_patients.gender', 'transfer_patients.firstname', 'transfer_patients.lastname',
                DB::raw('CONCAT(transfer_patients.firstname, " ", transfer_patients.lastname) AS patient_name'),
                DB::raw('CONCAT(transfer_patients.prescriber_firstname, " ", transfer_patients.prescriber_lastname) AS prescriber_name'),
                'transfer_patients.birthdate', 'transfer_patients.home_address',
                'transfer_patients.city','transfer_patients.county','transfer_patients.state',
                'transfer_patients.zip','transfer_patients.phone_number','transfer_patients.email',
                'transfer_patients.affiliated','transfer_patients.communication','transfer_patients.current_pharmacy',
                'transfer_patients.pharmacy_phone_number','transfer_patients.pharmacy_address','transfer_patients.pharmacy_city',
                'transfer_patients.pharmacy_state','transfer_patients.pharmacy_zip',
                'transfer_patients.prescriber_phone_number','transfer_patients.prescriber_fax_number',
                DB::raw('GROUP_CONCAT(CONCAT(transfer_patient_medications.name, " ", transfer_patient_medications.strength) SEPARATOR "\n") AS medication_details'))
                ->join('transfer_task_statuses', 'transfer_patients.id', '=', 'transfer_task_statuses.transfer_patient_id')
                ->leftJoin('transfer_patient_medications', 'transfer_patients.id', '=', 'transfer_patient_medications.t_patient_id')
                ->where('transfer_task_statuses.transfer_task_id', $request->input('task_id'))
                ->where('transfer_patients.transfer_list_id', $request->input('list_id'))
                ->where('transfer_patients.id', $request->input('id'))
                ->groupBy('transfer_patients.id', 'transfer_task_statuses.id', 'transfer_patients.firstname', 
                'transfer_patients.lastname', 'transfer_patients.prescriber_firstname', 'transfer_patients.prescriber_lastname', 
                'transfer_patients.birthdate', 'transfer_patients.home_address', 'transfer_patients.city', 
                'transfer_patients.county', 'transfer_patients.state', 'transfer_patients.zip', 'transfer_patients.phone_number', 
                'transfer_patients.email', 'transfer_patients.affiliated', 'transfer_patients.communication', 
                'transfer_patients.current_pharmacy', 'transfer_patients.pharmacy_phone_number', 
                'transfer_patients.pharmacy_address', 'transfer_patients.pharmacy_city', 'transfer_patients.pharmacy_state', 
                'transfer_patients.pharmacy_zip', 'transfer_patients.prescriber_phone_number', 
                'transfer_patients.prescriber_fax_number', 'transfer_task_statuses.transfer_task_id',
                'transfer_patients.gender', 'transfer_task_statuses.notes')
                ->first();

            // $comments = TransferTaskComment::join('users', 'transfer_task_comments.user_id', '=', 'users.id')
            //     ->join('employees', 'users.id', '=', 'employees.user_id')
            //     ->select('transfer_task_comments.*', 'employees.firstname as firstname', 'employees.lastname as lastname')
            //     ->where('transfer_task_comments.transfer_task_status_id', $task_patient->task_status_id)
            //     ->get();
            $comments = TransferTaskComment::join('users', 'transfer_task_comments.user_id', '=', 'users.id')
                ->join('employees', 'users.id', '=', 'employees.user_id')
                ->select('transfer_task_comments.*', 'employees.firstname as firstname', 'employees.lastname as lastname')
                ->where('transfer_task_comments.transfer_task_status_id', $task_patient->task_status_id)
                ->orderBy('transfer_task_comments.created_at', 'asc')
                ->get();

            
            $files = File::join('transfer_task_files', 'transfer_task_files.file_id', '=', 'files.id')
                ->select('files.*', 'transfer_task_files.id as task_file_id', 'transfer_task_files.transfer_task_status_id as status_id')
                ->where('transfer_task_files.transfer_task_status_id', $task_patient->task_status_id)
                ->get()->toArray();

            return response()->json([
                "status"=> 'success',
                //"message" => 'Record has been updated',
                "task"=> $task,
                "task_patient" => $task_patient,
                "comments" => $comments,
                "files" => $files,
            ], 200);
        }
    }

    public function get_assignee_data(Request $request)
    {
        if($request->ajax()){
            $task_status_id = $request->input('task_status_id');

            $assigned = TransferTaskAssignee::where('transfer_task_status_id', $task_status_id)->get();
            $assigned_id = $assigned->pluck('user_id');

            $users = User::join('employees', 'users.id', '=', 'employees.user_id')
                ->select('employees.firstname', 'employees.lastname', 'users.id AS id')
                ->get();

            $removed_users = $users->filter(function ($user) use ($assigned_id) {
                return $assigned_id->contains($user->id);
            })->toArray();

            $filtered_users = $users->reject(function ($user) use ($assigned_id) {
                return $assigned_id->contains($user->id);
            })->values()->toArray();

            return response()->json([
                "status" => 'success',
                "users" => $filtered_users,
                "assignee" => $removed_users,
                "assigned" => $assigned,
            ], 200);



        }
    } 

    public function delete_assignee(Request $request)
    {
        if($request->ajax())
        {
            $task_status_id = $request->input('task_status_id');
            $user_id = $request->input('user_id');
            
            $assignee = TransferTaskAssignee::where('user_id', $user_id)
                ->where('transfer_task_status_id', $task_status_id)
                ->first();
            $assignee->delete();

            $newAssignees = TransferTaskAssignee::where('transfer_task_status_id', $task_status_id)
                ->join('employees', 'transfer_task_assignees.user_id', '=', 'employees.user_id')
                ->select('employees.firstname', 'employees.lastname')
                ->get();

            $namesArray = $newAssignees->map(function ($assignee) {
                return $assignee->firstname . ' ' . $assignee->lastname;
            })->toArray();

            return response()->json([
                "status" => 'success',
                "message" => 'Record has been deleted.',
                "assignees" => $namesArray
            ], 200);
        }
    }

    public function update_assignees(Request $request)
    {
        if($request->ajax())
        {
            $user = Auth::user();
            $task_status_id = $request->input('task_status_id');
            $user_id = $request->input('user_id');
            
            $assignee = new TransferTaskAssignee;
            $assignee->transfer_task_status_id = $task_status_id;
            $assignee->user_id = $user_id;
            $assignee->assigned_by = $user->id;
            $assignee->save();

            $newAssignees = TransferTaskAssignee::where('transfer_task_status_id', $task_status_id)
                ->join('employees', 'transfer_task_assignees.user_id', '=', 'employees.user_id')
                ->select('employees.firstname', 'employees.lastname')
                ->get();

            $namesArray = $newAssignees->map(function ($assignee) {
                return $assignee->firstname . ' ' . $assignee->lastname;
            })->toArray();

            return response()->json([
                "status" => 'success',
                "message" => 'Record has been saved.',
                "assignees" => $namesArray
            ], 200);
        }
    }

    public function medication_store(Request $request)
    {
        if($request->ajax()){
            $patient_id = $request->input('id');
            $drugName = $request->input('drug_name');
            $strength = $request->input('strength');
            
            $medication = new TransferPatientMedication();
            $medication->t_patient_id = $patient_id;
            $medication->name = $drugName;
            $medication->strength = $strength;
            $medication->save();

            $medications = $this->patient_medications($patient_id);

            $med_text = '';
            foreach ($medications as $medication) {
                $med_text .= $medication['name'] . ' ' . $medication['strength'] . PHP_EOL;
            }


            return response()->json([
                "status" => 'success',
                "message" => 'Record has been saved.',
                "medication" => $medication,
                "medications" => $med_text,
            ], 200);
        }
    }

    public function get_patient_medications(Request $request)
    {
        if($request->ajax()){
            $patient_id = $request->input('id');
            return response()->json([
                "status" => 'success',
                "medications" => $this->patient_medications($patient_id),
            ], 200);
        }
    }

    public function update_patient_medication(Request $request)
    {
        if($request->ajax()){
            $med_id = $request->input('med_id');
            $value = $request->input('value');
            $patient_id = $request->input('id');
            $field = $request->input('field');

            $medication = TransferPatientMedication::where('id', $med_id)->first();
            $medication->update([$field => $value]);

            $medications = $this->patient_medications($patient_id);

            $med_text = '';
            foreach ($medications as $medication) {
                $med_text .= $medication['name'] . ' ' . $medication['strength'] . PHP_EOL;
            }


            return response()->json([
                "status" => 'success',
                "message" => 'Record has been updated.',
                "medications" => $med_text,
            ], 200);
        }
    }

    public function delete_patient_medication(Request $request)
    {
        if($request->ajax()){
            $med_id = $request->input('id');
            $patient_id = $request->input('patient_id');

            $medication = TransferPatientMedication::where('id', $med_id)->first();
            $medication->delete();

            $medications = $this->patient_medications($patient_id);

            $med_text = '';
            foreach ($medications as $medication) {
                $med_text .= $medication['name'] . ' ' . $medication['strength'] . PHP_EOL;
            }

            return response()->json([
                "status" => 'success',
                "message" => 'Record has been updated.',
                "medications" => $med_text,
            ], 200);
        }
    }

    private function patient_medications($id)
    {
        $medications = TransferPatientMedication::where('t_patient_id', $id)
            ->orderBy('created_at')
            ->get()
            ->toArray();
        return $medications;
    }

    public function get_data(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';
           
            $id = $request->patient_id;
            // get data from products table
            $query = TransferPatient::select(
                'transfer_patients.id AS id', 'transfer_task_statuses.transfer_task_id AS transfer_task_id',
                'transfer_task_statuses.id AS task_status_id', 'transfer_task_status_logs.id AS logs_id',
                'transfer_patients.firstname', 'transfer_patients.lastname', 'transfer_task_statuses.notes',
                DB::raw('CONCAT(transfer_patients.firstname, " ", transfer_patients.lastname) AS patient_name'),
                DB::raw('CONCAT(transfer_patients.prescriber_firstname, " ", transfer_patients.prescriber_lastname) AS prescriber_name'),
                'transfer_patients.birthdate', 'transfer_patients.home_address', 'transfer_patients.created_at',
                'transfer_patients.prescriber_firstname', 'transfer_patients.prescriber_lastname',
                'transfer_patients.city','transfer_patients.county','transfer_patients.state',
                'transfer_patients.zip','transfer_patients.phone_number','transfer_patients.email',
                'transfer_patients.affiliated','transfer_patients.communication','transfer_patients.current_pharmacy',
                'transfer_patients.pharmacy_phone_number','transfer_patients.pharmacy_address','transfer_patients.pharmacy_city',
                'transfer_patients.pharmacy_state','transfer_patients.pharmacy_zip',
                'transfer_patients.prescriber_phone_number','transfer_patients.prescriber_fax_number',
                'transfer_task_statuses.due_date', 'transfer_task_statuses.shipping_type', 
                DB::raw('GROUP_CONCAT(CONCAT(transfer_patient_medications.name, " ", transfer_patient_medications.strength) SEPARATOR "\n") AS medication_details'),
                DB::raw('CONCAT(
                    FLOOR(TIMESTAMPDIFF(MINUTE, transfer_task_status_logs.created_at, NOW()) / 60),
                    " hr ",
                    MOD(TIMESTAMPDIFF(MINUTE, transfer_task_status_logs.created_at, NOW()), 60),
                    " min"
                    ) AS total_time_status')
                )
                ->join('transfer_task_statuses', 'transfer_patients.id', '=', 'transfer_task_statuses.transfer_patient_id')
                ->leftJoin('transfer_patient_medications', 'transfer_patients.id', '=', 'transfer_patient_medications.t_patient_id')
                ->join('transfer_task_status_logs', 'transfer_task_statuses.id', '=', 'transfer_task_status_logs.transfer_task_status_id')
                ->where('transfer_task_statuses.transfer_task_id', $request->task_id)
                ->where('transfer_patients.transfer_list_id', $request->input('list_id'))
                ->where('transfer_task_status_logs.status', 'active')
                ->groupBy('transfer_patients.id', 'transfer_patients.firstname', 'transfer_patients.lastname', 'transfer_patients.prescriber_firstname', 
                    'transfer_patients.prescriber_lastname', 'transfer_patients.birthdate', 'transfer_patients.home_address', 'transfer_patients.city', 
                    'transfer_patients.county', 'transfer_patients.state', 'transfer_patients.zip', 'transfer_patients.phone_number', 
                    'transfer_patients.email', 'transfer_patients.affiliated', 'transfer_patients.communication', 'transfer_patients.current_pharmacy', 
                    'transfer_patients.pharmacy_phone_number', 'transfer_patients.pharmacy_address', 'transfer_patients.pharmacy_city', 'transfer_patients.pharmacy_state', 
                    'transfer_patients.pharmacy_zip', 'transfer_patients.prescriber_phone_number', 'transfer_patients.prescriber_fax_number', 'transfer_task_statuses.transfer_task_id', 
                    'transfer_task_status_logs.id', 'transfer_patients.created_at', 'transfer_task_statuses.id', 'transfer_task_statuses.due_date',
                    'transfer_task_statuses.shipping_type', 'transfer_task_status_logs.created_at', 'transfer_task_statuses.notes');

            // Search //input all searchable fields
            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        if ($column['name'] === 'patient_name') {
                            // Handle search for 'patient_name' field differently
                            $query->orWhere(DB::raw('CONCAT(transfer_patients.firstname, " ", transfer_patients.lastname)'), 'like', "%{$search}%");
                        } 
                        else if ($column['name'] === 'prescriber_name') {
                            // Handle search for 'prescriber_name' field differently
                            $query->orWhere(DB::raw('CONCAT(transfer_patients.prescriber_firstname, " ", transfer_patients.prescriber_lastname)'), 'like', "%{$search}%");
                        }
                        else if ($column['name'] === 'medication_details') {
                            // Handle search for 'prescriber_name' field differently
                            $query->orWhere(DB::raw('CONCAT(transfer_patient_medications.name, " ", transfer_patient_medications.strength)'), 'like', "%{$search}%");
                        }
                        else {
                            // Default search behavior for other columns
                            $query->orWhere("$column[name]", 'like', "%".$search."%");
                        }
                        $query->orWhereRaw('CONCAT(prescriber_firstname," ", prescriber_lastname) like  "%'.$search.'%"');  
                        $query->orWhereRaw('CONCAT(prescriber_lastname," ", prescriber_firstname) like  "%'.$search.'%"');       
                    }  
                }  
                
            });

            //default field for order
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];

            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $val) {
                $assignees = Employee::whereHas('user', function ($query) use ($val) {
                    $query->whereHas('transferTaskAssignees', function ($query) use ($val) {
                        $query->where('transfer_task_status_id', $val->task_status_id);
                    });
                })->get();

                // $assignees = $assigneeLastName->pluck('lastname')->implode(', ');
                $assigneeNames = $assignees->map(function ($assignee) {
                    return $assignee->firstname . ' ' . $assignee->lastname;
                })->implode(', ');

                $user = User::join('employees', 'users.id', '=', 'employees.user_id')
                ->select('employees.firstname', 'employees.lastname', 'users.id')
                ->where('users.id', Auth::id())
                ->first();

                $due_date = ($val->due_date)?date('M d, Y', strtotime($val->due_date)):"";

                $patient_name = '<div>';
                $patient_name .= '<button class="circle-button" onclick="ShowChangeTaskModal('.$val->id.', '.$val->transfer_task_id.')"><i class="fa-solid fa-circle tr-circle-icon"></i></button><span>
                    <a data-id="'.$val->id.'" data-logsid="'.$val->logs_id.'" data-taskid="'.$val->transfer_task_id.'"
                    data-lastname="'.$val->lastname.'" data-firstname="'.$val->firstname.'" data-assignees="'.$assigneeNames.'" 
                    data-duedate="'.$due_date.'" data-taskstatusid="'.$val->task_status_id.'"
                    data-userfirstname="'.$user->firstname.'" data-userlastname="'.$user->lastname.'" data-userid="'.$user->id.'"
                    onclick="showEditModal(this, '.$request->id.');"
                    >'.$val->patient_name.'</a></span>';
                $patient_name .= '</div>';

                $ship_type = ($val->shipping_type)?$val->shipping_type:'--SELECT--';

                $shipping_type = '<div>';
                $shipping_type .= '<span>
                    <a onclick="showShippingTypeModal('.$val->id.','.$val->transfer_task_id.','.$val->task_status_id.',\''.$val->shipping_type.'\');"
                    >'.$ship_type.'</a></span>';
                $shipping_type .= '</div>';


                $newData[] = [
                    'id' => $val->logs_id,
                    'patient_name' => $patient_name,
                    'assignee' => $assigneeNames,
                    'total_time_in_status' => $val->total_time_status,
                    'due_date' => $due_date,
                    'shipping_type' => $shipping_type,
                    'gender' => $val->gender,
                    'birthdate' => $val->birthdate,
                    'created_at' => ($val->created_at)?date('M d, Y', strtotime($val->created_at)):'',
                    'notes' => $val->notes,
                    'home_address' => $val->home_address,
                    'city' => $val->city,
                    'state' => $val->state,
                    'county' => $val->county,
                    'affiliated' => $val->affiliated,
                    'phone_number' => $val->phone_number,
                    'communication' => $val->communication,
                    'current_pharmacy' => $val->current_pharmacy,
                    'pharmacy_phone_number' => $val->pharmacy_phone_number,
                    'pharmacy_address' => $val->pharmacy_address,
                    'pharmacy_city' => $val->pharmacy_city,
                    'pharmacy_state' => $val->pharmacy_state,
                    'prescriber_firstname' => $val->prescriber_firstname,
                    'prescriber_lastname' => $val->prescriber_lastname,
                    'prescriber_phone_number' => $val->prescriber_phone_number,
                    'prescriber_fax_number' => $val->prescriber_fax_number,
                    'medication_details' => $val->medication_details,

                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }
}
