<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CURL\TebraController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Helpers\Helper;
use App\Models\Order;
use App\Models\Item;
use App\Models\Patient;
use App\Models\Employee;
use App\Models\File;
use App\Models\Status;
use App\Models\ShipmentStatus;
use App\Models\ShipmentStatusLog;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class DivisionTwoBMailOrderController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        // $this->middleware('permission:division-2b.mail-orders.index', ['only' => ['index', 'patients_get_data', 'get_data']]);
        // $this->middleware('permission:division-2b.mail-orders.create', ['only' => ['create','add_patient']]);
        // $this->middleware('permission:division-2b.mail-orders.update', ['only' => ['edit','update_patient']]);
        // $this->middleware('permission:division-2b.mail-orders.delete', ['only' => ['delete_patient']]);
    }

    public function index()
    {
        $user = Auth::user();
        $breadCrumb = ['Division 2B', 'Mail Orders'];
        $shipment_statuses = ShipmentStatus::orderBy('sort', 'asc')->get();
        $stores = explode(',', env('STORES'));

        $logs = ShipmentStatusLog::where('order_id', 62)
            ->orderBy('id', 'asc')
            ->get();

        $durations = [];

        for ($i = 0; $i < count($logs); $i++) {
            $currentLog = $logs[$i];
            $nextLog = isset($logs[$i + 1]) ? $logs[$i + 1] : null;

            $duration = $nextLog 
                ? \Carbon\Carbon::parse($currentLog->changed_at)->diffInMinutes(\Carbon\Carbon::parse($nextLog->changed_at))
                : \Carbon\Carbon::parse($currentLog->changed_at)->diffInMinutes(\Carbon\Carbon::now());

            $durations[] = [
                'order_id' => $currentLog->order_id,
                'changed_at' => \Carbon\Carbon::parse($currentLog->changed_at)->format('M d, h:i A'),
                'status_name' => $currentLog->shipmentStatus->name,
                'duration_from_previous_status' => $duration,
            ];
        }

        foreach ($durations as $duration) {
           // echo $duration['order_id'] .', ' . $duration['status_name'] . ', ---- '.$duration['changed_at']. ' ---- : ' . $duration['duration_from_previous_status'] . ' minutes<br>';
        }

      // exit;


        return view('/division2b/mail_orders/index', compact('user','breadCrumb','shipment_statuses','stores'));
    }

    public function get_tebra(TebraController $tebra)
    {
        return $tebra->get_patient();
    }

    public function patients(Request $request)
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
        
        $breadCrumb = ['Division 2B', 'Patients'];
        switch ($user->role->name) {
            case 'Admin':
                return view('/division2b/patients', compact('user', 'patients','breadCrumb'));
            case 'Division2B':
                return view('/division2b/patients', compact('user', 'patients','breadCrumb'));
            default:
                return view('/division2b/patients', compact('user', 'patients','breadCrumb'));
        }

    }

    public function download($id, $did)
    {   
       $file = File::where('id', $did)->first();
        
        $headers = [
            'Content-Type'        => 'Content-Type: '.$file->mime_type.' ',
            'Content-Disposition' => 'attachment; filename="'. $file->filename .'"',
        ];

        $path = $file->path.$file->filename;

        
        return Response::make(Storage::disk('s3')->get($path), 200, $headers);
    }

    public function get_data(Request $request, $id)
    {   
        if($request->ajax()){

            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            $columns = ['order_number', 'patient.firstname', 'shipment_status', 'shipment_tracking_number', 'created_at', 'shipment_from_store', 'requested_by_store', 'actions'];

            $orderByCol =  $columns[$orderColumnIndex];

            $query = Order::with('patient');

            if ($orderByCol == 'patient.firstname') {
                $query = $query->join('patients', 'patients.id', '=', 'orders.patient_id')
                               ->orderBy('patients.firstname', $orderBy)
                               ->select('orders.*'); // avoid getting all columns from the patients table
            } elseif ($orderByCol == 'shipment_status') {
                $query = $query->join('shipment_statuses', 'shipment_statuses.id', '=', 'orders.shipment_status_id')
                               ->orderBy('shipment_statuses.id', $orderBy)
                               ->select('orders.*'); // avoid getting all columns from the shipment_statuses table
            } else {
                $query = $query->orderBy($orderByCol, $orderBy);
            }

            if($request->has('pharmacy_store_id')) {
                $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
            }

            // Search //input all searchable fields
            $search = $request->search;
            $query = $query->where(function($query) use ($search){
                $query->orWhere('order_number', 'like', "%".$search."%");
                $query->orWhereHas('patient', function($query) use ($search) {
                    $query->where('firstname', 'like', "%".$search."%")
                          ->orWhere('lastname', 'like', "%".$search."%");
                });
            });

            // Add status filter if status is provided
            if( $request->input('columns.6.search.value')){
                $query->where('shipment_from_store', $request->input('columns.6.search.value'));
            }
            // Add status filter if status is provided
            if( $request->input('columns.7.search.value')){
                $query->where('requested_by_store', $request->input('columns.7.search.value'));
            }

            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {
                if($value->oig_status != "No Match"){
                    $class = "badge bg-danger";
                }
                else{
                    $class="badge bg-success";
                }

                if($value->status == 'Active'){
                    $status_class = 'primary';
                }
                else{
                    $status_class = 'danger';
                }

                $newStartDate = ($value->start_date === null)?'':date('Y-m-d', strtotime($value->start_date));
                $newEndDate = ($value->end_date === null)?'':date('Y-m-d', strtotime($value->end_date));

                $actions = '<div class="d-flex order-actions">
                    <button type="button" onclick="ViewMailOrder('.$value->id.', \''.$value->order_number.'\')" class="btn btn-primary btn-sm me-2" ><i class="fa-solid fa-eye"></i></button>
                    ';
                if(Auth::user()->can('menu_store.operations.mail_orders.update')) {
                    $actions .= '<button type="button" onclick="ShowEditOrderForm(' . $value->id . ',\'' . $value->firstname . '\',\'' . $value->lastname . '\',\'' . $value->birthdate . '\',\'' . $value->address . '\',\'' . $value->city . '\',\'' . $value->state . '\',\'' . $value->zip_code . '\',\'' . $value->phone_number . '\')" class="btn btn-primary btn-sm me-2" ><i class="fa-solid fa-pencil"></i></button>';
                }
                if(Auth::user()->can('menu_store.operations.mail_orders.delete')) {
                    $actions .= '<button type="button" onclick="ShowConfirmDeleteForm(' . $value->id . ', \'' . $value->order_number . '\',0)" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>';
                }
                $actions .= '</div>';

                

            
                $hidden='';
                $s3Url='';
                $download_hidden='';
                $file_id_holder=0;
                if($value->file_id != ""){
                    $file = File::where('id', $value->file_id)->first();
                    if($file){
                        $s3Url = Storage::disk('s3')->temporaryUrl(
                            $file->path.$file->filename,
                            now()->addMinutes(30)
                        );
                        $file_id_holder = $value->file_id;
                        ($value->mime_type != 'application/pdf')?$hidden="d-none":'';
                    }
                    else{
                        $hidden = "d-none";
                        $download_hidden = "d-none";
                    }
                }
                else{
                    $hidden = "d-none";
                    $download_hidden = "d-none";
                }
                $ship_label = '<div class="d-flex order-actions">';
                if(Auth::user()->can('menu_store.operations.mail_orders.download')) {
                    $ship_label .= '<a href="/store/operations/'.$id.'/mail_orders/download/'.$value->file_id.'"
                                class="btn-light '.$download_hidden.' me-2" style="background-color:#dee2e6"><i class="bx bxs-download"></i></a>';
                }
                if(Auth::user()->can('menu_store.operations.mail_orders.upload')) {
                    $ship_label .= '<button type="button" onclick="showUploadForm(' . $value->id . ')" class="btn btn-primary btn-sm me-2" ><i class="fa-solid fa-cloud-arrow-up"></i></button>';
                }
                $ship_label .= '</div>';


                
                $newData[] = [
                    'id' => $value->id,
                    'file' => $ship_label,
                    'order_number' => $value->order_number,
                    'patient' => $value->patient->firstname.' '.$value->patient->lastname,
                    'shipment_status' => '<button type="button" class="btn btn-'.$value->shipmentStatus->class.' btn-sm">'.$value->shipmentStatus->name.'</button>',
                    'shipment_tracking_number' => $value->shipment_tracking_number,
                    'created_at' => $value->created_at->format('M. d, Y h:iA'),
                    'requested_by_store' => $value->requested_by_store,
                    'shipment_from_store' => $value->shipment_from_store,
                    'actions' => $actions
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }
   
    public function patients_get_data(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // get data from patients table
            $query = new Patient();

            // Search //input all searchable fields
            $search = $request->search;
            $query = $query->where(function($query) use ($search){
                $query->orWhere('firstname', 'like', "%".$search."%")
                      ->orWhere('lastname', 'like', "%".$search."%")
                      ->orWhere('patientid', 'like', "%".$search."%");
            });

            $orderByCol =  'id';
            $query = $query->orderBy($orderByCol, $orderBy);

            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {
                $newData[] = [
                    'id' => $value->id,
                    'firstname' => $value->firstname,
                    'lastname' => $value->lastname,
                    'birthdate' => $value->birthdate,
                    'created_at' => ($value->created_at)?$value->created_at->format('M. d, Y h:iA'):'',
                    'updated_at' => $value->updated_at->format('M. d, Y h:iA'),
                    'address' => $value->address,
                    'city' => $value->city,
                    'state' => $value->state,
                    'zip_code' => $value->zip_code,
                    'phone_number' => $value->phone_number,
                    
                    'actions' =>  '<div class="d-flex order-actions">
                        <button type="button" onclick="ShowEditForm(' . $value->id . ',\'' . $value->firstname . '\',\'' . $value->lastname . '\',\'' . $value->birthdate . '\',\'' . $value->address . '\',\'' . $value->city . '\',\'' . $value->state . '\',\'' . $value->zip_code . '\',\'' . $value->phone_number . '\')" class="btn btn-primary btn-sm me-2" ><i class="fa-solid fa-pencil"></i></button>
                        <button type="button" onclick="ShowConfirmDeleteForm(' . $value->id . ', \'' . $value->firstname . ' ' . $value->lastname . '\')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                    </div>',
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    public function getOrder($id)
    {
        $user = Auth::user();
        $order = Order::with(['patient','shipmentStatus','items'])->find($id);

        if (!$order) abort(404);
        return response()->json($order);
    }

}
