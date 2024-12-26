<?php

namespace App\Http\Controllers;
use Validator;
use Auth;
use DataTables;
use App\Http\Helpers\Helper;
use App\Models\Patient;
use App\Models\Status;
use App\Models\Stage;
use App\Models\Prescription;
use App\Models\File;

use League\Flysystem\Filesystem;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use League\Csv\Writer;




class PrescriptionController extends Controller
{

    public function add_prescription_via_ajax(Request $request)
    {

        $user = auth()->check() ? Auth::user() : redirect()->route('login');
        $helper =  new Helper;
        $input = $request->all();
 
        $prescription_validation = Validator::make($input, [
            'order_number' => 'required|int',
            'medications' => 'required|max:30'
         ]);

        if ($prescription_validation->passes())
        {
        
                    $prescription = new Prescription;
              
                    $prescription->patient_id = $input['patient_id'];
                    $prescription->order_number = $input['order_number'];
                    $prescription->request_type = $input['request_type'];
                    $prescription->telemed_bridge = $input['telemed_bridge'];
                    $prescription->prescriber_name = $input['prescriber_name'];
                    $prescription->prescriber_phone = $input['prescriber_phone'];
                    $prescription->prescriber_fax = $input['prescriber_fax'];
                    $prescription->npi = $input['npi'];
                    $prescription->medications = $input['medications'];
                    
                    $prescription->dosage = $input['dosage'];
                    $prescription->qty = $input['qty'];
                    $prescription->days_supply = $input['days_supply'];
                    $prescription->refills_remaining = $input['refills_remaining'];

                    
                    $prescription->requested_for = $input['requested_for'];
                    $prescription->is_addon_applied = $input['is_addon_applied'];
                
                    $prescription->submitted_at = date('Y-m-d H:i:s', strtotime($input['submitted_at']));
                    $prescription->submitted_by = $input['submitted_by'];
                    $prescription->sent_at = date('Y-m-d H:i:s', strtotime($input['sent_at']));
                    $prescription->received_at = date('Y-m-d H:i:s', strtotime($input['received_at']));
                    
                    $prescription->stage_id = $input['stage'];
                    $prescription->status_id = $input['status'];
                    $prescription->special_instructions = $input['special_instructions'];
                   

               
                    $prescription->save();
                    
                    
                    return json_encode([
                        'data'=> $prescription,
                        'status'=>'success',
                        'message'=>'Prescription Saved.'
                    ]);
        }else{

          return json_encode(
            ['status'=>'error',
            'errors'=> $prescription_validation->errors(),
            'message'=>'Saving failed.']);
        }

        return redirect()->back()
            ->withInput()
            ->withErrors($validation)
            ->with('message', 'There were validation errors.');

    }

    public function delete_prescription_via_ajax(Request $request)
    {
		$user = auth()->check() ? Auth::user() : redirect()->route('login');
        $input = $request->all();
        $prescription =  Prescription::where('id','=',  $input['prescription_id']);
        
        if($prescription == null){
                return json_encode(
                ['status'=>'error',
                'message'=>'Prescription delete failed.']);
        }else{
                      
            $prescription->delete();
            return json_encode(['status'=>'success','message'=>'Prescription deleted succesfully.']);
            
        }
        
    }

    public function edit_prescription_via_ajax(Request $request)
    {

        $user = auth()->check() ? Auth::user() : redirect()->route('login');
        $helper =  new Helper;
        $input = $request->all();
 
        $messages = [
            'edit_order_number.required' => 'The order number is required.',
            'edit_order_number.int' => 'The order number must be an integer.',
            'edit_medications.required' => 'The medications field is required.',
            'edit_medications.max' => 'The medications field should not exceed :max characters.',
            'edit_days_supply.int' => 'The days supply must be an integer.',
        ];
        
        $prescription_validation = Validator::make($input, [
            'edit_order_number' => 'required|int',
            'edit_days_supply' => 'int',
            'edit_medications' => 'required|max:30'
        ], $messages);


        if ($prescription_validation->passes())
        {
        
                   
                    $prescription =  Prescription::where('id','=', $input['prescription_id'])->first();
              
                    $prescription->order_number = $input['edit_order_number'];
                    $prescription->request_type = $input['edit_request_type'];

                    $prescription->prescriber_name = $input['edit_prescriber_name'];
                    $prescription->prescriber_phone = $input['edit_prescriber_phone'];
                    $prescription->prescriber_fax = $input['edit_prescriber_fax'];
                    $prescription->npi = $input['edit_npi'];

                    $prescription->medications = $input['edit_medications'];
                    $prescription->sig = $input['edit_sig'];
                   
                    $prescription->days_supply = $input['edit_days_supply'];
                    $prescription->refills_requested = $input['edit_refills_requested'];
                   
                   
                
                    $prescription->submitted_at = date('Y-m-d H:i:s', strtotime($input['edit_submitted_at']));
                    $prescription->submitted_by = $input['edit_submitted_by'];
                    $prescription->sent_at = date('Y-m-d H:i:s', strtotime($input['edit_sent_at']));
                    $prescription->received_at = date('Y-m-d H:i:s', strtotime($input['edit_received_at']));
                    
                    $prescription->stage_id = $input['edit_stage'];
                    $prescription->status_id = $input['edit_status'];
                    $prescription->special_instructions = $input['edit_special_instructions'];
                

               
                    $prescription->save();
                    
                    
                    return json_encode([
                        'data'=> $prescription,
                        'status'=>'success',
                        'message'=>'Prescription Saved.'
                    ]);
        }else{

          return json_encode(
            ['status'=>'error',
            'errors'=> $prescription_validation->errors(),
            'message'=>'Saving failed.']);
        }

        return redirect()->back()
            ->withInput()
            ->withErrors($validation)
            ->with('message', 'There were validation errors.');

    }

    public function upload(Request $request)
{

    $user = auth()->check() ? Auth::user() : redirect()->route('login');
    if ($request->hasFile('pdf')) {
        $file = $request->file('pdf');
        
        if ($file->isValid() && $file->getClientOriginalExtension() === 'pdf') {

            $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $fileExtension = $file->getClientOriginalExtension();
            $formattedDate = now()->format('Ymd');
    
            $newFileName = $fileName  . '.' . $fileExtension;

            $uploadedFile = $request->file('file');

            // Provide a dynamic path or use a specific directory in your S3 bucket
            $path = 'prescriptions/' . $newFileName;

            // Store the file in S3
            Storage::disk('s3')->put($path, file_get_contents($file));

            // Optionally, get the URL of the uploaded file
            $s3url = Storage::disk('s3')->url($path);


            //Upload  $file->move(public_path('uploads'), $newFileName);

            $save_file = new File;

            $prescription =  Prescription::where('order_number','=',$fileName)->first();
            if($prescription != null){
                $save_file->prescription_id = $prescription->id;

                if ($prescription && $prescription->file()->exists()) {
                    // Prescription has related file
                    $prescription->file->path = $path;
                    $prescription->save();

                } else {
                    // Prescription either doesn't exist or doesn't have related files
                    $save_file->filename = $newFileName;
                    $save_file->path = $path;
                    $save_file->mime_type = $s3url;
                    $save_file->user_id = $user->id; 
                    $save_file->save();
                }
            }

         

    
            return response()->json([
                'file' => $s3url ,
                'message' => 'File uploaded successfully'
            ]);

        } else {
            return response()->json([
                'file' => $file->getClientOriginalName(),
                'error' => 'Invalid file or not a PDF'
            ]);
        }
    }

    // Handle if no file was uploaded or other errors
    return response()->json(['error' => 'No file uploaded'], 400);
}

public function testUpload()
{

    // Get the file path within your S3 bucket
    $filePath = 'uploads/big.pdf';

    $s3Client = new S3Client([
       'version' => 'latest',
       'region' => 'us-west-1', // Replace with your S3 region
       'credentials' => [
           'key' => 'AKIAZYXPMORALSKFAPJVs',
           'secret' => 'qbpZYmbhm8NdCpHpHyMZpoQps0xTLRKlrwG8S+Bj',
       ],
   ]);

// Generate a presigned URL with read permissions and a limited expiration time (e.g., 1 hour)
$cmd = $s3Client->getCommand('GetObject', [
   'Bucket' => 'tinrxbucket',
   'Key' => $filePath,
]);
$request = $s3Client->createPresignedRequest($cmd, '+1 hour');
$presignedUrl = (string) $request->getUri();


    return view('test.upload');
}

public function handleUpload(Request $request)
{
    // Validate the uploaded file
    $request->validate([
        'file' => 'required|file|mimes:pdf|max:10240', // PDF file, max 10MB
    ]);

    $file = $request->file('file');

    // Rename the file
    $newFileName = 'your_custom_prefix_' . time() . '.' . $file->getClientOriginalExtension();

    // Upload the file to S3
    $path = Storage::disk('s3')->putFileAs('uploads', $file, $newFileName, 'public');

    // Get the URL of the uploaded file in the S3 bucket
    $uploadedFileUrl = Storage::disk('s3')->url('uploads/' . $newFileName);


    //dd($uploadedFileUrl);


     // Get the file path within your S3 bucket
     $filePath = 'uploads/big.pdf';

     $s3Client = new S3Client([
        'version' => 'latest',
        'region' => 'us-west-1', // Replace with your S3 region
        'credentials' => [
            'key' => 'AKIAZYXPMORALSKFAPJV',
            'secret' => 'qbpZYmbhm8NdCpHpHyMZpoQps0xTLRKlrwG8S+Bj',
        ],
    ]);

// Generate a presigned URL with read permissions and a limited expiration time (e.g., 1 hour)
$cmd = $s3Client->getCommand('GetObject', [
    'Bucket' => 'tinrxbucket',
    'Key' => $filePath,
]);
$request = $s3Client->createPresignedRequest($cmd, '+1 hour');
$presignedUrl = (string) $request->getUri();


    return redirect()->back()->with('success', 'File uploaded successfullyss.')->with('uploadedFileUrl', $presignedUrl);
}

public function exportPrescriptionsToCSV(Request $request)
{
    // Define the CSV headers based on the fields you want to include
    $headers = [
        'Order Number',
        'Medications',
        'Patient first name',
        'Patient last name',
        'NPI',
        'Request Type',
        'Telemed Bridge',
        'Prescriber Name',
        'Prescriber Phone',
        'Prescriber Fax',
        'Requested For',
        'Is Addon Applied',
        'Submitted At',
        'Sent At',
        'Received At',
        'Submitted By',
    ];

    // Fetch prescriptions data based on the date range in the request or retrieve all prescriptions
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $prescriptions = Prescription::whereBetween('submitted_at', [$startDate, $endDate])->get();
    } else {
        $prescriptions = Prescription::all();
    }

    // Create a new CSV writer instance
    $csv = Writer::createFromFileObject(new \SplTempFileObject());

    // Insert headers to the CSV
    $csv->insertOne($headers);

    // Insert prescription data to the CSV
    foreach ($prescriptions as $prescription) {
        if(isset($prescription->patient)){
                $csv->insertOne([
                    $prescription->order_number,
                    $prescription->medications,
                    $prescription->patient->firstname,
                    $prescription->patient->lastname,
                    $prescription->npi,
                    $prescription->request_type,
                    $prescription->telemed_bridge ? 'Yes' : 'No',
                    $prescription->prescriber_name,
                    $prescription->prescriber_phone,
                    $prescription->prescriber_fax,
                    $prescription->requested_for,
                    $prescription->is_addon_applied ? 'Yes' : 'No',
                    $prescription->submitted_at,
                    $prescription->sent_at,
                    $prescription->received_at,
                    $prescription->submitted_by,
                ]);
         }
    }

    // Set CSV file name
    $fileName = 'prescriptions.csv';

    // Set CSV headers for the HTTP response
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    ];

    // Create HTTP response with CSV file
    return Response::make($csv->getContent(), 200, $headers);
    
    }

    public function data(Request $request)
    {
        // Query the prescriptions table with related patient, stage, and status data
        $model = Prescription::with(['patient', 'stage', 'status'])
            ->select('prescriptions.*', 'patients.firstname', 'patients.lastname', 'stages.name as stage_name', 'statuses.name as status_name')
            ->join('patients', 'prescriptions.patient_id', '=', 'patients.id')
            ->join('stages', 'prescriptions.stage_id', '=', 'stages.id')
            ->join('statuses', 'prescriptions.status_id', '=', 'statuses.id');

             // Add date filter if minDate and maxDate are provided
            if ($request->filled('minDate') && $request->filled('maxDate')) {
                $minDate = date('Y-m-d', strtotime($request->get('minDate')));
                $maxDate = date('Y-m-d', strtotime($request->get('maxDate')));
                $model->whereBetween('prescriptions.submitted_at', [$minDate, $maxDate]);
            }

            // Add stage filter if stage is provided
            if ($request->filled('stage')) {
                $stageId = $request->get('stage');
                $model->where('prescriptions.stage_id', $stageId);
            }

            // Add status filter if status is provided
            if ($request->filled('status')) {
                $statusId = $request->get('status');
                $model->where('prescriptions.status_id', $statusId);
            }

    
            if ($request->has('order')) {
                $order = $request->get('order');
                if ($order[0]['column'] == 1) { // patient_name column
                    $direction = $order[0]['dir'];
                    $model->orderBy(DB::raw('patients.firstname'), $direction);
                } elseif ($order[0]['column'] == 4) { // stage column
                    $direction = $order[0]['dir'];
                    $model->orderBy('stages.name', $direction);
                } elseif ($order[0]['column'] == 5) { // status column
                    $direction = $order[0]['dir'];
                    $model->orderBy('statuses.name', $direction);
                }
            }

            



        // Initialize DataTables with the query
        $dataTable = DataTables::of($model)
            ->addColumn('rowid', function($model) {
                return $model->id;
            })
            // Add a column for the patient's full name
            ->addColumn('patient_name', function($model) {
                return $model->patient->firstname . ' ' . $model->patient->lastname;
            })
            // Add a column for the stage, with a custom HTML badge
            ->addColumn('stage', function($model) {
                return '<span class="badge p-3" style="color:' . $model->stage->text_color . '; background-color:' . $model->stage->color . '">' . $model->stage->name . '</span>';
            })
            // Add a column for the status, with a custom HTML badge
            ->addColumn('status', function($model) {
                return '<span class="badge p-3" style="color:' . $model->status->text_color . '; background-color:' . $model->status->color . '">' . $model->status->name . '</span>';
            })
            ->addColumn('pdf', function($model) {
                if ($model->file && $model->file->path) {
                    $presignedUrl = Storage::disk('s3')->temporaryUrl(
                        $model->file->path,
                        now()->addMinutes(30)
                    );
                    return '<a target="_blank" href="'.$presignedUrl.'" class="btn btn-info btn-sm"><i class="fa-solid fa-file-pdf" ></i></a>';
                }
                return '';


            })
            ->addColumn('actions', function($model) {
                return '
                    <button 
                        data-order_number="' . $model->order_number . '"
                        data-medications="' . $model->medications . '"
                        data-sig="' . $model->sig . '"
                        data-refills_requested="' . $model->refills_requested . '"
                        data-patient_name="' . $model->patient->firstname . ' ' . $model->patient->lastname . '"
                        data-patient_id="' . $model->patient->id . '"
                        data-stage="' . ($model->stage_id ?? '1') . '"
                        data-status="' . ($model->status_id ?? '1') . '"
                        data-npi="' . $model->npi . '"
                        data-request_type="' . $model->request_type . '"
                        data-prescriber_name="' . $model->prescriber_name . '"
                        data-prescriber_phone="' . $model->prescriber_phone . '"
                        data-prescriber_fax="' . $model->prescriber_fax . '"
                        data-special_instructions="' . $model->special_instructions . '"
                        data-submitted_at="' . date('m/d/Y',strtotime($model->submitted_at)) . '"
                        data-received_at="' . date('m/d/Y',strtotime($model->received_at)) . '"
                        data-sent_at="' . date('m/d/Y',strtotime($model->sent_at)) . '"
                        data-submitted_by="' . $model->submitted_by . '"
                        type="button" class="btn btn-primary btn-sm"
                        onclick="ShowEditPrescriptionForm(' . $model->id . ')"
                        id="edit_btn_' . $model->id . '"
                    >
                        <i class="fa-solid fa-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" id="confirm_delete_product_btn" onclick="ShowConfirmDeleteForm(' . $model->id . ')"><i class="fa-solid fa-trash-can"></i></button>
                ';
            })

        
            // Add a filter for the patient_name column
            ->filterColumn('patient_name', function($query, $keyword) {
                $sql = "CONCAT(patients.firstname, ' ', patients.lastname) like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            // Add a filter for the stage column
            ->filterColumn('stage', function($query, $keyword) {
                $query->where('stages.name', 'like', "%{$keyword}%");
            })
            // Add a filter for the status column
            ->filterColumn('status', function($query, $keyword) {
                $query->where('statuses.name', 'like', "%{$keyword}%");
            })
            // Allow HTML in the stage and status columns
            ->rawColumns(['stage','status','actions','pdf']);

    
        // Return the DataTable as JSON
        return $dataTable->toJson();
    }


}
