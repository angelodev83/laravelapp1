<?php

namespace App\Http\Controllers\Procurement;

use App\Models\ClinicalOrder;
use App\Models\Prescription;
use App\Models\Item;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Http\Helpers\Helper;
use App\Http\Controllers\Controller;

use App\Interfaces\IHistoriesRepository;
use App\Models\Clinic;
use App\Models\ClinicOrderItem;
use App\Models\File;
use App\Models\Medication;
use App\Models\ShipmentStatus;
use App\Models\StoreStatus;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

use App\Interfaces\ITaskRepository;

class ClinicalOrderController extends Controller
{
    private $clinicalOrder;
    private IHistoriesRepository $historiesRepository;
    private ITaskRepository $taskRepository;

    public function __construct(ClinicalOrder $clinicalOrder
        , IHistoriesRepository $historiesRepository
        , ITaskRepository $taskRepository
    ) {
        $this->clinicalOrder = $clinicalOrder;
        $this->historiesRepository = $historiesRepository;
        $this->taskRepository = $taskRepository;

        $this->middleware('permission:menu_store.procurement.clinical_orders.index|menu_store.procurement.clinical_orders.create|menu_store.procurement.clinical_orders.update|menu_store.procurement.clinical_orders.delete');
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $this->checkStorePermission($id);
            $years = $this->getYears();
            $months = $this->getMonths();
            $currentYear = now()->year;
            $currentMonth = now()->month;
            $breadCrumb = ['Procurement', 'Clinical Orders'];
            return view('/stores/procurement/clinicalOrders/index', [
                'breadCrumb' => $breadCrumb,
                'years' => $years,
                'months' => $months,
                'currentYear' => $currentYear,
                'currentMonth' => $currentMonth,
            ]);
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function filteredData(Request $request) {
        if (request()->ajax()) {
            $selectedYear = $request->input('year');
            $selectedMonth = $request->input('month');

            $orderStatuses = $this->orderStatuses();
            
            $data = [
                'selectedYear' => $selectedYear,
                'selectedMonth' => $selectedMonth,
            ];

            foreach ($orderStatuses as $statusId => $statusName) {
                $data['order' . $statusId] = $this->getTasksByStatusId($statusId, $selectedYear, $selectedMonth);
            }

            return response()->json(['data' => $data], 200);
        }
    }
    
    private function getTasksByStatusId($statusId, $year = null, $month = null) {
        $query = ClinicalOrder::with('task.assignedTo', 'items', 'shipmentStatus', 'clinic', 'status')
                ->where('status_id', $statusId);

        if ($year && $month) {
            $query->whereYear('created_at', $year)
                ->whereMonth('created_at', $month);
        } elseif ($year) {
            $query->whereYear('created_at', $year);
        } elseif ($month) {
            $query->whereMonth('created_at', $month);
        }

        $values = $query->latest()->get();
        $newData = [];
        foreach ($values as $value) {
            $newData[] = [
                'id' => $value->id,
                'assigned_to' => $value->task->assignedTo->firstname.' '.$value->task->assignedTo->lastname,
                'clinic' => $value->clinic->name,
                'comments' => $value->comments,
                'image' =>$value->task->assignedTo->image,
                'initials' => $value->task->assignedTo->firstname[0].$value->task->assignedTo->lastname[0],
                'initials_random_color' => $value->task->assignedTo->initials_random_color,
                'medications' => $value->items,
                'order_date' => $value->order_date,
                'order_number' => $value->order_number,
                'prescriber_name' => $value->prescriber_name,
                'shipment_tracking_number' => $value->shipment_tracking_number,
                'status' => $value->status->name,
            ];
        }
        return $newData;
    }

    public function statusUpdate(Request $request) {
        if($request->ajax()) {
            $id = $request->input('id');
            $statusId = $request->input('status_id');
            $order = ClinicalOrder::findOrFail($id);
            $order->update(['status_id' => $statusId]);

            return response()->json(['message' => 'Record has been saved.'], 200);
        }
        return response()->json(['message' => 'Something went wrong.'], 400);
    }

    protected function orderStatuses() {
        return [
            '701' => 'NewRequests',
            '702' => 'Received',
            '703' => 'InTransit',
            '704' => 'Submitted',
            '705' => 'MissingOrder',
            '706' => 'Completed',
        ];
    }
    
    protected function getMonths() {
        return [
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];
    }

    protected function getYears() {
        return [
            '2024' => '2024',
            '2023' => '2023',
            '2022' => '2022',
            '2021' => '2021',
        ];
    }
    
    public function data(Request $request)
    {
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // get data from products table
            // $query = DB::table('view_clinical_orders');

            $query = ClinicalOrder::select([
                'clinical_orders.id',
                'clinical_orders.order_number',
                'clinical_orders.shipment_tracking_number',
                'clinical_orders.order_date',
                'clinical_orders.comments',
                'clinical_orders.prescriber_name',
                'clinical_orders.created_at',
                'store_statuses.name as status',
                'store_statuses.color',
                'store_statuses.text_color',
                'store_statuses.id as shipment_status_id',
                'store_statuses.class as statuses_class',
                'clinics.name as clinic',
                'clinics.id as clinic_id',
                'files.path AS file_path',
                'files.filename AS file_name',
                'files.mime_type AS mime_type',
                'files.id AS file_id'
            ])
            ->join('store_statuses', 'clinical_orders.status_id', '=', 'store_statuses.id')
            ->leftJoin('files', 'files.id', '=', 'clinical_orders.file_id')
            ->join('clinics', 'clinical_orders.clinic_id', '=', 'clinics.id');


            if($request->has("pharmacy_store_id")) {
                $query = $query->where("pharmacy_store_id", $request->pharmacy_store_id);
            }

            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }  
                }   
            });

            $orderByCol =  $columns[$orderColumnIndex]['name'];

            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $aStatuses = StoreStatus::where('category', 'procurement_order')->orderBy('sort')->get()->toArray();
            $clinics = Clinic::get()->toArray();

            $newData = [];
            // dd($data);
            foreach ($data as $value) {
                $items = $value->items()->get()->toArray();
                $hidden='';
                $s3Url='';
                $download_hidden='';
                if($value->file_name != ""){
                    $s3Url = Storage::disk('s3')->temporaryUrl(
                        $value->file_path.$value->file_name,
                        now()->addMinutes(30)
                    );
                    ($value->mime_type != 'application/pdf')?$hidden="d-none":'';
                }
                else{
                    $hidden = "d-none";
                    $download_hidden = "d-none";
                }

                $actions = '<div class="d-flex order-actions">';
                if(Auth::user()->can('menu_store.procurement.clinical_orders.index')) {
                    $actions .= '<a title="View" href="javascript:void(0)" class="me-1"><button class="btn btn-sm btn-primary" data-id="'.$value->id.'" 
                    data-array="'.htmlspecialchars(json_encode($value)).'"
                    id="inmar-show-btn-'.$value->id.'"
                    onclick="showViewForm('.$value->id.','.htmlspecialchars(json_encode($items)).');"><i class="fa fa-eye"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.clinical_orders.update')) {
                    $actions .= '<a title="Edit" href="javascript:void(0)" class="me-1"><button class="btn btn-sm btn-primary" data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"
                    id="inmar-show-btn-'.$value->id.'"
                    onclick="showEditForm('.$value->id.','.htmlspecialchars(json_encode($clinics)).','.htmlspecialchars(json_encode($aStatuses)).','.htmlspecialchars(json_encode($items)).');"><i class="fa fa-pencil"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.clinical_orders.delete')) {
                    $actions .= '<a title="Delete" href="javascript:void(0)" class="me-1"><button class="btn btn-sm btn-danger" onclick="ShowConfirmDeleteForm(' . $value->id . ')"><i class="fa fa-trash-can"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.clinical_orders.upload')) {
                    $actions .= '<a title="Upload" href="javascript:void(0)" class="me-1"><button data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"
                    id="clinicaorder-show-btn-'.$value->id.'" onclick="showUploadForm(' . $value->id . ')" 
                    class="btn btn-sm btn-secondary" ><i class="fa-solid fa-cloud-arrow-up"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.clinical_orders.download')) {
                    $actions .= '<a class="'.$download_hidden.' me-1" href="/admin/file/download/'.$value->file_id.'" title="Download File"><button class="btn btn-sm btn-secondary"><i class="fa fa-download"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.clinical_orders.pdfview')) {
                    $actions .= '<a target="_blank" href="'.$s3Url.'" class="'.$hidden.' me-1"
                    title="View PDF"><button class="btn btn-sm btn-secondary"><i class="fa-regular fa-file-pdf"></i></button></a>';
                }
                $actions .= '</div>';
                
                $newData[] = [
                    'id' => $value->id,
                    'order_number' => $value->order_number,
                    'order_date' => $value->order_date,
                    'order_by' => $value->clinic,
                    'created_at' => date('M d, Y g:i A', strtotime($value->pst_created_at)),
                    'shipment_tracking_number' => $value->shipment_tracking_number,
                    'status' => '<button class="btn btn-'.$value->statuses_class.' btn-sm w-100">'.$value->status.'</button>',
                    'shipment_tracking_number' => $value->shipment_tracking_number,
                    'actions' => $actions
                ];
            }   

            //  '<div class="d-flex order-actions" '.$hideAll.'>
            //                 <a data-ordernumber="'.$value->order_number.'" data-orderdate="'.$value->order_date.'" data-ndc="'.$value->ndc.'" 
            //                     data-id="'.$value->id.'" data-drugid="'.$value->drug_id.'" data-inventorytype="'.$value->inventory_type.'"
            //                     data-quantity="'.$value->quantity.'" data-clinicid="'.$value->clinic_id.'"
            //                     data-clinic="'.$value->clinic.'" data-prescriber="'.$value->prescriber.'"
            //                     data-shipmentstatus="'.$value->shipment_status.'" data-shipmenttrackingnumber="'.$value->shipment_tracking_number.'"
            //                     data-comments="'.$value->comments.'" data-shipmentstatusid="'.$value->shipment_status_id.'" data-drugname="'.$value->drugname.'"
            //                     onclick="showEditForm(this);"
            //                     class="btn-primary" style="background-color:#8833ff" '.$hideU.'><i class="bx bxs-edit"></i></a>
            //                 <a onclick="ShowConfirmDeleteForm(' . $value->id . ',' . $value->order_number . ')" class="btn-danger ms-1" style="background-color:#dc362e" '.$hideD.'><i class="bx bxs-trash"></i></a>
            //             </div>'
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    public function file_upload(Request $request)
    {
        if($request->ajax()){
            $file = $request->file('file');

            $input = $request->all();
            
            $validation = Validator::make($input, [
                // 'file' => 'required|mimes:pdf',
                'id' => 'required',
            ]);
            if ($validation->passes()){
                $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $fileExtension = $file->getClientOriginalExtension();
                $mime_type = $file->getMimeType();
                
                $newFileName = date("Ymdhis").Auth::id() .'_'. $fileName  . '.' . $fileExtension;
                $doc_type = $fileExtension;
                
                $path = 'procurement/clinicOrders/';
                
                // Provide a dynamic path or use a specific directory in your S3 bucket
                $path_file = 'procurement/clinicOrders/'  . $newFileName;

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

                $document = ClinicalOrder::where('id', $input['id'])->first();
                $document->file_id = $save_file->id;
                $document->save();
                

                return response()->json([
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ], 201);

            }
            else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Check input fields.'
                ], 422);
            }

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if($request->ajax()){
            
            $helper =  new Helper;
            $input = $request->all();
            //2147483647 max of int
            $validation = Validator::make($input, [
                'order_date' => 'required',
                'clinic_id' => 'required',
            ]);

            if ($validation->passes()){
                
                $order = new ClinicalOrder();
                $order->order_number = $input['order_number'];
                $order->clinic_id = $input['clinic_id'];
                $order->shipment_status_id = 301;
                $order->status_id = 701;
                $order->order_date =  $input['order_date'];
                $order->comments = $input['comments'];
                $order->pharmacy_store_id = $input['pharmacy_store_id'];
                $order->prescriber_name = $input['prescriber_name'];
                $order->user_id = auth()->user()->id;
                $order->save();

                // Sync to TASK
                $assigned_to_employee_id = $this->getProcurementAssignee();
                $task = new Task();
                $task->subject = "Clinical Order Number: ".$order->order_number;
                $task->description = "<p>Please process this order until completion.</p>";
                $task->assigned_to_employee_id = $assigned_to_employee_id;
                $task->pharmacy_store_id = $order->pharmacy_store_id;
                $task->user_id = $order->user_id;
                $task->status_id = $order->status_id;
                $save = $task->save();
                if($save) {
                    $order->task_id = $task->id;
                    $order->save();
                }

                $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $task->status, null);

                // $prescription = new Prescription();
                // $prescription->order_number = $input['order_number'];
                // $prescription->prescriber_name = $input['prescriber_name'];
                // // $prescription->patient_id = $input['patient_id'];
                // $prescription->save();

                // Create Item entries
                $check_entry = 0;
                $item_data = [];
                for ($i = 0; $i <= $input['med_count']; $i++) {
                    if (!empty($request->input("drugname$i")&&$request->input("quantity$i")&&$request->input("ndc$i"))) {

                        $med_name = Medication::where('med_id', $request->input("drugname$i"))->first();

                        $item = new ClinicOrderItem();
                        $item->clinic_order_id = $order->id;
                        $item->drugname = $med_name->name;
                        $item->drug_id = $request->input("drugname$i");
                        $item->quantity = $request->input("quantity$i");
                        $item->ndc = $request->input("ndc$i");
                        
                        $item->save();
                        array_push($item_data, $item);
                        $check_entry = 1;
                    }
                }
                //return no entry of medication
                if($check_entry === 0)
                {
                    $del_order = ClinicalOrder::findOrFail($order->id);
                    $del_order->delete();

                    // $del_prescription = Prescription::findOrFail($prescription->id);
                    // $del_prescription->delete();

                    $medication_validate = [
                        "medication_holder" => ["Input at least one medication field."],
                        "message" => "Employee saving failed."
                    ];'{"medication_holder":["The prescriber name field is required."]},"message":"Employee saving failed."}';

                    return json_encode(['status'=>'error',
                        'errors'=> $medication_validate,
                        'message'=>'Employee saving failed.']);
                }

                // //store history
                // $history_body = array(
                //     'clinic_order' => $order
                // );
                // $history_header = array(
                //     'class' => 'CLINIC ORDER ',
                //     'method' => 'CREATED ',
                //     'name' => $input['order_number'],
                //     'id' => $order->id
                // );

                // $this->historiesRepository->store_historyV2($history_header, $history_body, 'order', $order->id);            
 
                return json_encode([
                    'data'=> $order->id,
                    'status'=>'success',
                    'message'=>'Record has been saved.']);
           }
           else{
                return json_encode(
                    ['status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Employee saving failed.']);
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ClinicalOrder $clinicalOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if($request->ajax()){
            
            $input = $request->all();
            
            $dataArray = json_decode($input['data'], true);
            //2147483647 max of int
            // $order_old = ClinicalOrder::where('id', $input['id'])->first();
            $id = $dataArray['id'];
            
            $validation = Validator::make($dataArray, [
                'order_date' => 'required',
                'clinic_id' => 'required',
            ]);

            if ($validation->passes()){

                if ($request->file('file')) {
                    $file = $request->file('file');

                    $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $fileExtension = $file->getClientOriginalExtension();
                    $mime_type = $file->getMimeType();
                    
                    $newFileName = date("Ymdhis").Auth::id() .'_'. $fileName  . '.' . $fileExtension;
                    $doc_type = $fileExtension;
                    
                    $path = 'procurement/clinicOrders/';
                    
                    // Provide a dynamic path or use a specific directory in your S3 bucket
                    $path_file = 'procurement/clinicOrders/'  . $newFileName;

                    // Store the file in S3
                    Storage::disk('s3')->put($path_file, file_get_contents($file));

                    // Optionally, get the URL of the uploaded file
                    $s3url = Storage::disk('s3')->url($path_file);

                    $document = new File();

                    $document->filename = $newFileName;
                    $document->path = $path;
                    $document->mime_type = $mime_type;
                    $document->document_type = $doc_type;
                    $document->save();


                    $orderFile = ClinicalOrder::where('id', $dataArray['id'])->first();
                    $orderFile->file_id = $document->id;
                    $orderFile->save();

                }
                
                $order = ClinicalOrder::where('id', $id)->first();
                $previousStatus = $order->status;
                $order->order_number = $dataArray['order_number'];
                $order->clinic_id = $dataArray['clinic_id'];
                // $order->shipment_status_id = $dataArray['status_id'];
                if(isset($dataArray['status_id'])) {
                    $order->status_id = $dataArray['status_id'];
                }
                $order->order_date =  $dataArray['order_date'];
                $order->comments = $dataArray['comments'];
                $order->pharmacy_store_id = $dataArray['pharmacy_store_id'];
                $order->prescriber_name = $dataArray['prescriber_name'];
                $order->shipment_tracking_number = $dataArray['tracking_number'];
                $order->user_id = auth()->user()->id;
                $order->save();

                $task = Task::findOrFail($order->task_id);
                if(isset($task->id)) {
                    $task->status_id = $order->status_id;
                    if($order->status_id == 706) {
                        $task->completed_by = auth()->user()->id;
                        $task->completed_at = Carbon::now();
                    }
                    $task->save();

                    if($order->status_id != $previousStatus->id) {
                        $currentStatus = StoreStatus::findOrFail($order->status_id);
                        $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $currentStatus, $previousStatus);
                    }
                }
                // //update history
                // $history_body = array(
                //     'clinicorder_old' => $order_old,
                //     'clinicorder_new' => $order,
                // );
                // $history_header = array(
                //     'class' => 'CLINIC ORDER',
                //     'method' => 'UPDATED order ',
                //     'name' => $order->order_number,
                //     'id' => $order->id
                // );
                // $this->historiesRepository->update_historyV2($history_header, $history_body, 'order', $order->id);
            
 
                return json_encode([
                    'file_id'=> $order->file_id,
                    'status'=>'success',
                    'message'=>'Record has been saved.']);
           }
           else{
                return json_encode(
                    ['status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Record saving failed.']);
            }

        }
    }

    public function delete_file(Request $request)
    {
        if($request->ajax()){

            $input = $request->all();
            $id = $input['id'];
            
            // $doc = StoreDocument::where('id', $id)->first();
            // $inmar_id =  $doc->parent_id;
            // $directoryPath = dirname($doc->path);
            // $directory = public_path($directoryPath);
            // File::deleteDirectory($directory);
            // $doc->delete();
            $file = File::where('id', $id)->first();
            $file_id = $file->id;
            $path = $file->path.$file->filename;

            if($path != ''){
                if(Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }

                $file->delete();   
            }

            $inmar = ClinicalOrder::where('file_id', $file_id)->first();
            $inmar->file_id = '';
            $inmar->save();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    public function update_item(Request $request)
    {
        if($request->ajax()){
            
            
            $input = $request->all();
            
            $validation = Validator::make($input, [
                'quantity' => 'required',
                'med_id' => 'required',
            ],
            [
                'med_id.required' => 'The drug name field is required.',
            ]);

            if ($validation->passes()){
                if($input['id'] == 0)
                {
                    $med_name = Medication::where('med_id', $request->input("med_id"))->first();
                    
                    $item = new ClinicOrderItem();
                    $item->clinic_order_id = $request->input("clinic_order_id");
                    $item->drug_id = $request->input("med_id");
                    $item->quantity = $request->input("quantity");
                    $item->ndc = $request->input("ndc");
                    $item->drugname = $med_name->name;
                    $item->save();
                }
                else{
                    $med_name = Medication::where('med_id', $request->input("med_id"))->first();
                    
                    $item = ClinicOrderItem::where('id', $input['id'])->first();
                    
                    $item->drug_id = $request->input("med_id");
                    $item->quantity = $request->input("quantity");
                    $item->ndc = $request->input("ndc");
                    $item->drugname = $med_name->name;
                    $item->save();
                }
                
                

                return json_encode([
                    'data'=> $item->id,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } else{
                return json_encode([
                        'status'=>'error',
                        'errors'=> $validation->errors(),
                        'message'=>''
                    ]);
            }

        }
    }

    public function delete(Request $request)
    {
        if($request->ajax()){

            $input = $request->all();
            
            $id = $input['id'];
            
            $item = ClinicOrderItem::where('id', $id)->first();
            
            $item->delete();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if($request->ajax()){
            $user = auth()->check() ? auth()->user() : redirect()->route('login');
            
            $input = $request->all();
            $item = ClinicalOrder::findOrFail($input['id']);
            $file_id = $item->file_id;

            if($file_id){
                $file = File::where('id', $file_id)->first();
                $path = $file->path.$file->filename;

                if($path != ''){
                    if(Storage::disk('s3')->exists($path)) {
                        Storage::disk('s3')->delete($path);
                    }

                    $file->delete();   
                }
            }
            
            if($item == null){
                return json_encode(
                    ['status'=>'error',
                        'message'=>'Order delete failed.']);
            } else {
                // Delete the items inside the order

                // Delete the order-item

                //delete history
                // $history_body = array(
                //     'clinicorder' => $item_old,
                // );
                // $history_header = array(
                //     'class' => 'CLINIC ORDER',
                //     'method' => 'DELETED order ',
                //     'name' => $item->clinicalOrder->order_number,
                //     'id' => $item_old->id,
                // );


                // $this->historiesRepository->delete_history($history_header, $history_body, 'order', $item_old->id);
                
                ClinicOrderItem::where('clinic_order_id', $item->id)->delete();
                $item->delete();

                return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
            }
        }
    }

}