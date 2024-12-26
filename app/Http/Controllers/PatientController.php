<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CURL\TebraController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Helper;
use App\Models\File;
use App\Models\Patient;
use App\Models\Status;
use App\Models\Stage;
use App\Models\Order;
use App\Models\Item;
use App\Models\ShipmentStatusLog;
use Validator;
use Auth;
use DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $statuses = Status::withCount('prescriptions')->orderBy('id', 'asc')->get();
    
        $isAdmin = $user->userType->id === 1;
        $isClient = $user->userType->id === 2;
    
        $patientsQuery = Patient::whereNull('withorder')->orderByDesc('created_at');
        $search = $request->input('search');

        if ($search !== null) {
            $patientsQuery->where(function ($query) use ($search) {
                $query->whereRaw("CONCAT(firstname, ' ', IFNULL(lastname, ''), ' ', IFNULL(address, ''), ' ', IFNULL(city, ''), ' ', IFNULL(state, ''), ' ', IFNULL(zip_code, '')) LIKE ?", ["%$search%"]);
            });
        }

        $patients = $patientsQuery->paginate(100);
    
        switch ($user->userType->id) {
            case 1:
                return view('/cs/patients/index', compact('user', 'patients'));
            case 2:
                return view('/cs/patients/index', compact('user', 'patients'));
            case 3:
                return view('/fulfillments/patients', compact('user', 'patients'));
            default:
                // Handle other cases or throw an error
                throw new Exception('Invalid user type');
        }
 
    }

    public function viewpatient(Request $request,$id)
    {
        $user = Auth::user();
        $input = $request->all();
        $patient = Patient::findOrFail($id);   
        $statuses = Status::orderBy('id', 'asc')->get();
        $stages = Stage::orderBy('id', 'asc')->get();

        switch($user->userType->id) {
            case 1:
                return view('/cs/patients/view', compact('user', 'patient', 'statuses', 'stages'));
            case 2:
                return view('/clients/patients/view', compact('user', 'patient', 'statuses', 'stages'));
            default:
                return view('/cs/patients/view', compact('user', 'patient', 'statuses', 'stages'));
            break;
        }
    }


    public function store(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'npi' => 'required',
            'birthdate' => 'required|date',
            'date_upload' => 'required|date',
            'stage' => 'required', // Add validation for stage and status
            'status' => 'required',
        ]);

        // Create and store a new patient
        $patient = Patient::create($validatedData);

        // Redirect after storing
        return redirect()->route('patients.create')->with('success', 'Patient added successfully!');
    }


    public function add_patient_via_ajax(Request $request)
    {

        $user = auth()->check() ? Auth::user() : redirect()->route('login');
        $helper =  new Helper;
        $input = $request->all();
 
        $patient_validation = Validator::make($input, [
            'firstname' => 'required|max:30',
            'lastname' => 'required|max:30',
            'birthdate' => 'required|max:30'
         ]);

        if ($patient_validation->passes())
        {
          
           
            $patient = new Patient;
            $patient->firstname = $helper->ProperNamingCase($input['firstname']);
            $patient->lastname = $helper->ProperNamingCase($input['lastname']);
            $patient->birthdate = date('Y-m-d', strtotime($input['birthdate']));
            $patient->address = $input['address'];
            $patient->city = $input['city'];
            $patient->state = $input['state'];
            $patient->zip_code = $input['zip_code'];
            $patient->phone_number = $input['phone_number'];
            $patient->withorder = 1;
            $patient->save();
          

          $patient->birthday  =  date('M d, Y',strtotime($patient->birthdate));
          $patient->created  =  date('M d, Y H:i:s',strtotime($patient->created_at));
          $patient->updated  =  date('M d, Y H:i:s',strtotime($patient->updated_at));
         
          return json_encode([
            'data'=> $patient,
            'status'=>'success',
            'message'=>'Patient Saved.'
        ]);
        }else{

          return json_encode(
            ['status'=>'error',
            'errors'=> $patient_validation->errors(),
            'message'=>'Patient saving failed.']);
        }

        return redirect()->back()
            ->withInput()
            ->withErrors($validation)
            ->with('message', 'There were validation errors.');

    }
    public function edit_patient_via_ajax(Request $request)
    {

        $user = auth()->check() ? Auth::user() : redirect()->route('login');
        $helper =  new Helper;
        $input = $request->all();
 
        $customMessages = [
            'edit_firstname.required' => 'The first name field is required.',
            'edit_firstname.max' => 'The first name may not be greater than 30 characters.',
            'edit_lastname.required' => 'The last name field is required.',
            'edit_lastname.max' => 'The last name may not be greater than 30 characters.',
            'edit_birthdate.required' => 'The birthdate field is required.',
            'edit_birthdate.max' => 'The birthdate may not be greater than 30 characters.',
        ];
        
        $patient_validation = Validator::make($input, [
            'edit_firstname' => 'required|max:30',
            'edit_lastname' => 'required|max:30',
            'edit_birthdate' => 'required|max:30'
        ], $customMessages);
        if ($patient_validation->passes())
        {
          
         
            $patient = Patient::where('id', $input['patient_id'])->first();

            $patient->firstname = $helper->ProperNamingCase($input['edit_firstname']);
            $patient->lastname = $helper->ProperNamingCase($input['edit_lastname']);
            $patient->birthdate = date('Y-m-d', strtotime($input['edit_birthdate']));
            $patient->address = $input['edit_address']; 
            $patient->city = $input['edit_city']; 
            $patient->state = $input['edit_state']; 
            $patient->zip_code = $input['edit_zip_code'];
            $patient->phone_number = $input['edit_phone_number'];

            $patient->save();


          $patient->birthday  =  date('M d, Y',strtotime($patient->birthdate));
          $patient->created  =  date('M d, Y H:i:s',strtotime($patient->created_at));
          $patient->updated  =  date('M d, Y H:i:s',strtotime($patient->updated_at));
         
          return json_encode([
            'data'=> $patient,
            'status'=>'success',
            'message'=>'Patient Updated.'
        ]);
        }else{

          return json_encode(
            ['status'=>'error',
            'errors'=> $patient_validation->errors(),
            'message'=>'Category saving failed.']);
        }

        return redirect()->back()
            ->withInput()
            ->withErrors($validation)
            ->with('message', 'There were validation errors.');

    }


    public function delete_patient_via_ajax(Request $request)
    {
        $user = auth()->check() ? Auth::user() : redirect()->route('login');
        $input = $request->all();
        $patient =  Patient::find($input['patient_id']);

        if($patient == null){
            return json_encode(
                ['status'=>'error',
                'message'=>'Patient delete failed.']);
        } else {
            
            foreach ($patient->prescriptions as $prescription) {
                $prescription->file()->delete();
            }
            // Delete the prescriptions associated with the patient
            $patient->prescriptions()->delete();
            foreach ($patient->orders as $order) {
                $order->items()->delete();
            }
            $patient->orders()->delete();

            $patient->delete();
            return json_encode(['status'=>'success','message'=>'Patient and associated prescriptions deleted successfully.']);
        }
    }

    public function delete_patients_via_ajax(Request $request)
    {
        $user = auth()->check() ? Auth::user() : redirect()->route('login');
        $input = $request->all();
        $patientIds = $input['patient_ids'];

        if (empty($patientIds)) {
            return json_encode([
                'status' => 'error',
                'message' => 'No patient IDs provided.',
            ]);
        }

        try {
            // Use whereIn to delete multiple records
            $deleted = Patient::whereIn('id', $patientIds)->delete();

            if ($deleted) {
                return json_encode([
                    'status' => 'success',
                    'message' => 'Patients deleted successfully.',
                ]);
            } else {
                return json_encode([
                    'status' => 'error',
                    'message' => 'Failed to delete patients.',
                ]);
            }
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage(), // Provide an error message if an exception occurs
            ]);
        }
    }

    public function search(Request $request)
    {
            
            $input = $request->all();
    
            $patients =  Patient::orderBy('updated_at', 'DESC');
            if($request->has('source')) {
                $patients =  $patients->whereIn('source', [$input['source']]);
            }
            $patients =  $patients
            ->whereRaw("concat(firstname,lastname) like '%".$input['patient_name']."%' ")
            ->take(3)->get();
    
            if($patients != null){
                    return json_encode([
                        'status' => 'success',
                        'patients' => $patients->toArray(),
                    ]);
            }
    
    }

    public function add_order_via_ajax(Request $request)
    {
        $input = $request->all();

        
        $messages = [
            'patient_id.required' => 'The patient value is required.',
            'name0.required' => 'Please provide at least (1) medication.',
        ];
        $order_validation = Validator::make($input, [
            'patient_id' => 'required|int',
            'order_number' => 'required',
         ],$messages);

         // Add a custom rule to check that at least one name field is present
        $order_validation->sometimes('name0', 'required', function ($input) {
            for ($i = 0; $i <= $input['med_count']; $i++) {
                if (!empty($input["name$i"])) {
                    return false;
                }
            }
            return true;
        });

        if ($order_validation->passes())
        {
            $order = new Order;
            $order->patient_id = $request->patient_id;
            $order->order_number = $request->order_number;
            $order->shipment_status_id = 1;
            $order->shipment_from_store = $request->shipment_from_store;
            $order->requested_by_store = $request->requested_by_store;
            if($request->has('pharmacy_store_id')) {
                $order->pharmacy_store_id = $request->pharmacy_store_id;
            }
            $order->save();

            //log this to shippment status history
            // If the shipment status has changed, log it
           
                ShipmentStatusLog::create([
                    'order_id' => $order->id,
                    'shipment_status_id' => 1,
                    'changed_at' => now(),
                ]);
         

            // Create Item entries
            for ($i = 0; $i <= $input['med_count']; $i++) {
                if (!empty($request->input("name$i"))) {
                    $item = new Item;
                    $item->order_id = $order->id;
                    $item->name = $request->input("name$i");
                    $item->sig = $request->input("sig$i");
                    $item->days_supply = $request->input("days_supply$i");
                    $item->refills_remaining = $request->input("refills_left$i");
                    $item->ndc = $request->input("ndc$i");
                    $item->inventory_type = $request->input("inventory_type$i");
                    $item->quantity = 1;
                    $item->medication_id = $request->input("medi_id$i");
                    $item->rx_stage = 1;
                    $item->rx_status = 1;
                 
                    $item->save();
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order and items added successfully',
            ]);

        }else{
            $id = $input['file_id'];
            $file = File::where('id', $id)->first();
            $path = $file->path.$file->filename;

            if($path != ''){
                if(Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }

                $file_data = File::where('id', $id)->first();
                $file_data->delete();
            }

            return json_encode(
                ['status'=>'error',
                'errors'=> $order_validation->errors(),
                'message'=>'Saving failed.']);
        }
    }

    public function data(Request $request)
    {
        // Query the patients table
        $model = Patient::select('*');

        if (request()->has('v') && request()->get('v') == 'div2') {
            $model = $model->where('withorder', 1);
        } else {
            $model = $model->whereNull('withorder');
        }

        $model = $model->orderBy('created_at', 'desc');

        

        // Initialize DataTables with the query
        $dataTable = DataTables::of($model)
            ->addColumn('rowid', function($model) {
                return $model->id;
            })
            ->addColumn('birthdate', function($model) {
                return date('M. d. Y', strtotime($model->birthdate));
            })
            ->addColumn('created_at_date', function($model) {
                return date('M. d. Y', strtotime($model->created_at));
            })
            ->addColumn('updated_at_date', function($model) {
                return date('M. d. Y', strtotime($model->updated_at));
            })
            ->addColumn('actions', function($model) {
                return '
                    <button type="button" class="btn btn-primary btn-sm" id="edit_btn" 
                        onclick="ShowEditForm(
                            ' . $model->id . ',
                            \'' . $model->firstname . '\',
                            \'' . $model->lastname . '\',
                            \'' . date('m/d/Y', strtotime($model->birthdate)) . '\',
                            \'' . $model->address . '\', 
                            \'' . $model->city . '\',   
                            \'' . $model->state . '\',
                            \'' . $model->zip_code . '\', 
                            \'' . $model->phone_number . '\' 
                        )"
                    >
                        <i class="fa-solid fa-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" id="confirm_delete_product_btn" onclick="ShowConfirmDeleteForm(\'' . $model->firstname . '\',\'' . $model->lastname . '\',\'' . $model->id . '\')">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                   
                ';
            })
            
            ->rawColumns(['actions']);

            

        // Return the DataTable as JSON
        return $dataTable->toJson();

        
    }

    public function getNames(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : null;
        $encryptedQuery = [];
        $search = $request->term;
        
        if(!empty($search)) {

            $query = Patient::where('source', 'pioneer');

            // if ($limit !== null) {
            //     $query = $query->limit($limit);
            // }

            $encryptedQuery = $query->get()->filter(function ($encryptedQuery) use ($search) {
                // return stristr($encryptedQuery->getDecryptedFirstname(), trim($search)) !== false
                //     || stristr($encryptedQuery->getDecryptedLastname(), trim($search)) !== false;
                $fullName = $encryptedQuery->getDecryptedFirstname() . ' ' . $encryptedQuery->getDecryptedLastname();
                $revFullName = $encryptedQuery->getDecryptedLastname() . ' ' . $encryptedQuery->getDecryptedFirstname();
                return stristr($fullName, trim($search)) !== false
                    || stristr($revFullName,trim($search)) !== false;
            })->pluck('id');
        }
        
        if(!empty($encryptedQuery)) {  
            $data = Patient::whereIn('id',$encryptedQuery)->orderBy('firstname','asc')
                ->orderBy('lastname','asc')
                ->get();

            $modifiedData = [];
            foreach ($data as $value) {
                $dFirstname = Crypt::decryptString($value->firstname);
                $dLastname = Crypt::decryptString($value->lastname);
                
                $value->name = $dFirstname.' '.$dLastname;
                
                $modifiedData[] = $value;
            }
        }

        // dd($data);
        if($request->ajax()) {
            return json_encode(['data'=> $modifiedData]);
        }
        return $data;
    }

    

}
