<?php

namespace App\Http\Controllers\Procurement;

use App\Models\Task;
use App\Models\DrugOrder;
use App\Models\DrugOrderItem;
use App\Models\DrugOrderItemsImportData;
use App\Models\PharmacyPrescription;
use App\Models\StoreDocument;
use App\Models\StoreStatus;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use File;

use App\Interfaces\IHistoriesRepository;
use App\Interfaces\UploadInterface;
use App\Models\File as ModelsFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\ITaskRepository;
use Illuminate\Support\Facades\Validator;

class PharmacyDrugOrderController extends Controller
{
    private IHistoriesRepository $historiesRepository;
    private UploadInterface $uploadRepository;
    private ITaskRepository $taskRepository;

    public function __construct(IHistoriesRepository $historiesRepository, UploadInterface $uploadRepository, ITaskRepository $taskRepository) {
        $this->historiesRepository = $historiesRepository;
        $this->uploadRepository = $uploadRepository;
        $this->taskRepository = $taskRepository;
        $this->middleware('permission:menu_store.procurement.pharmacy.drug_orders.index|menu_store.procurement.pharmacy.drug_orders.create|menu_store.procurement.pharmacy.drug_orders.update|menu_store.procurement.pharmacy.drug_orders.delete');
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
            $breadCrumb = ['Procurement', 'Pharmacy', 'Drug Orders'];
            return view('/stores/procurement/pharmacy/drugOrders/index', [
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
        $query = DrugOrder::with('task.assignedTo','status','user.employee','itemsImported','wholesaler', 'file')
                ->where('status_id', $statusId);

        if ($year && $month) {
            $query->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);
        } elseif ($year) {
            $query->whereYear('created_at', $year);
        } elseif ($month) {
            $query->whereMonth('created_at', $month);
        }

        $value = $query->latest()->get();
        $newData = [];
        foreach ($value as $val) {
            $newData[] = [
                'id' => $val->id,
                'account_number' => $val->account_number,
                'assigned_to' => $val->task->assignedTo->firstname.' '.$val->task->assignedTo->lastname,
                'comments' => $val->comments,
                'created_at' => $val->created_at,
                'file' => $val->file,
                'file_id' => $val->file_id,
                'image' => $val->task->assignedTo->image,
                'initials' => $val->task->assignedTo->firstname[0].$val->task->assignedTo->lastname[0],
                'initials_random_color' => $val->task->assignedTo->initials_random_color,
                'items_imported' => $val->itemsImported,
                'order_number' => $val->order_number,
                'order_date' => $val->order_date,
                'po_memo' => $val->po_memo,
                'po_name' => $val->po_name,
                'status' => $val->status,
                'status_id' => $val->status_id,
                'wholesaler' => $val->wholesaler,
                'wholesaler_id' => $val->wholesaler_id
            ];
        }
        return $newData;
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
    
    // public function dataOLD(Request $request)
    // {
    //     if($request->ajax()){
    //         // Page Length
    //         $pageNumber = ( $request->start / $request->length )+1;
    //         $pageLength = $request->length;
    //         $skip       = ($pageNumber-1) * $pageLength;

    //         // Page Order
    //         $orderColumnIndex = $request->order[0]['column'] ?? '0';
    //         $orderBy = $request->order[0]['dir'] ?? 'desc';

    //         $query = DB::table('view_pharmacy_drug_orders');
            
    //         // Search //input all searchable fields
    //         $search = trim($request->search);

    //         if($request->has('pharmacy_store_id')) {
    //             $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
    //         }
            
    //         if(!empty($search)) {
    //             $query = $query->where(function($query) use (&$search){ 
    //                 $query->orWhere('order_number', 'like', "%".$search."%");
    //                 $query->orWhere('order_date', 'like', "%".$search."%");
    //                 $query->orWhere('drugname', 'like', "%".$search."%");   
    //                 $query->orWhere('quantity', 'like', "%".$search."%");
    //                 $query->orWhere('inventory_type', 'like', "%".$search."%");
    //                 $query->orWhere('shipment_status', 'like', "%".$search."%");
    //                 $query->orWhere('prescriber', 'like', "%".$search."%");
    //                 $query->orWhere('patient_firstname', 'like', "%".$search."%");
    //                 $query->orWhere('patient_lastname', 'like', "%".$search."%");
    //                 $query->orWhere('shipment_tracking_number', 'like', "%".$search."%");
    //             });
    //         }

            
    //         $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            
    //         $query = $query->orderBy($orderByCol, $orderBy);
    //         $recordsFiltered = $recordsTotal = $query->count();
    //         $data = $query->skip($skip)->take($pageLength)->get();

    //         $newData = [];
    //         foreach ($data as $value) {
    //             $newData[] = [
    //                 'id'        => $value->id,
    //                 'ndc'       => $value->ndc,
    //                 'order_number'  => $value->order_number,
    //                 'shipment_tracking_number'  => $value->shipment_tracking_number,
    //                 'order_date'    => $value->order_date,
    //                 'comments'      => $value->comments,
    //                 'pharmacy_store_id' => $value->pharmacy_store_id,
    //                 'shipment_status'   => $value->shipment_status,
    //                 'color'         => $value->color,
    //                 'shipment_status_id'    => $value->shipment_status_id,
    //                 'statuses_class'        => $value->statuses_class,
    //                 'drugname'      => $value->drugname,
    //                 'drug_id'       => $value->drug_id,
    //                 'price' => ($value->inventory_type == 'RX')?$value->rx_price:$value->price_340b,
    //                 'inventory_type'    => $value->inventory_type,
    //                 'quantity'      => $value->quantity,
    //                 'prescriber'    => $value->prescriber,
    //                 'patient_firstname' => $value->patient_firstname,
    //                 'patient_lastname'  => $value->patient_lastname,
    //                 'status' =>  '<button type="button" onclick="clickStatusBtn(' . $value->id . ')" class="btn btn-'.$value->statuses_class.' btn-sm radius-15 px-3" ><small>'.$value->shipment_status.'</small></button>',
    //                 'actions' =>  '<div class="d-flex order-actions">
    //                     <button type="button" class="btn btn-primary btn-sm me-2" 
    //                     id="drug-order-edit-btn-'.$value->id.'"
    //                     data-array="'.htmlspecialchars(json_encode($value)).'"
    //                     onclick="showEditModal('.$value->id.');"><i class="fa-solid fa-pencil"></i></button>
    //                     <button type="button" 
    //                     onclick="ShowConfirmDeleteForm(' . $value->id . ')" 
    //                     class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
    //                 </div>'
    //             ];
    //         }   
            
    //         return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
    //     }
    // }

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
            $file = ModelsFile::where('id', $id)->first();
            $file_id = $file->id;
            $path = $file->path.$file->filename;

            if($path != ''){
                if(Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }

                $file->delete();   
            }

            $inmar = DrugOrder::where('file_id', $file_id)->first();
            $inmar->file_id = '';
            $inmar->save();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
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

            $query = DrugOrder::with('task.assignedTo','status','user.employee','itemsImported','wholesaler', 'file');
            
            // Search //input all searchable fields
            $search = trim($request->search);
            $columns = $request->columns;
            if($request->has('pharmacy_store_id')) {
                $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
            }
            
            if(!empty($search)) {
                $query = $query->where(function($query) use (&$search){ 
                    $query->orWhere('order_number', 'like', "%".$search."%");
                    $query->orWhere('order_date', 'like', "%".$search."%");
                    $query->orWhere('po_memo', 'like', "%".$search."%");
                    $query->orWhere('account_number', 'like', "%".$search."%");
                    $query->orWhere('wholesaler_name', 'like', "%".$search."%");
                    $query->orWhere('comments', 'like', "%".$search."%");
                    $query->orWhereHas('status', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
                });
            }

            
            $orderByCol =  $columns[$orderColumnIndex]['name'];
            
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $user = auth()->user();
            $newData = [];
            foreach ($data as $value) {

                $hidden='';
                $s3Url='';
                $download_hidden='';
                if($value->file_id != ""){
                    $s3Url = Storage::disk('s3')->temporaryUrl(
                        $value->file->path.$value->file->filename,
                        now()->addMinutes(30)
                    );
                    ($value->file->mime_type != 'application/pdf')?$hidden="d-none":'';
                }
                else{
                    $hidden = "d-none";
                    $download_hidden = "d-none";
                }

                $actions = '<div class="d-flex order-actions"><a title="View" class="me-1" href="javascript:void(0)"><button type="button" 
                id="drug-order-show-btn-'.$value->id.'"
                data-array="'.htmlspecialchars(json_encode($value)).'"
                onclick="showViewDetailsModal(' . $value->id . ')" 
                class="btn btn-primary btn-sm me-1" ><i class="fa-solid fa-eye"></i></button></a>';
                $empName = isset($value->user->employee) ? $value->user->employee->getFullName() : "NA";
                $assignedName = isset($value->task->assignedTo) ? $value->task->assignedTo->getFullName() : "NA";

                if($user->can('menu_store.procurement.pharmacy.drug_orders.update')) {
                    $actions .= '<a title="Edit" href="javascript:void(0)"><button type="button" class="btn btn-primary btn-sm me-1" 
                    onclick="showEditDetailsModal('.$value->id.');"><i class="fa-solid fa-pencil"></i></button></a>';
                }
                if($user->can('menu_store.procurement.pharmacy.drug_orders.delete')) {
                    $actions .= '<a title="Delete" href="javascript:void(0)"><button type="button" 
                        onclick="ShowConfirmDeleteForm(' . $value->id . ')" 
                        class="btn btn-danger btn-sm me-1" ><i class="fa-solid fa-trash-can"></i></button></a>';
                }
                if($user->can('menu_store.procurement.pharmacy.drug_orders.upload')) {
                    $actions .= '<a title="Upload" href="javascript:void(0)" class="me-1"><button data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"
                    id="upload-show-btn-'.$value->id.'" onclick="showUploadForm(' . $value->id . ')" 
                    class="btn btn-sm btn-secondary" ><i class="fa-solid fa-cloud-arrow-up"></i></button></a>';
                }

                if($user->can('menu_store.procurement.pharmacy.drug_orders.download')) {
                    $actions .= '<a class="'.$download_hidden.' me-1" href="/admin/file/download/'.$value->file_id.'" title="Download File"><button class="btn btn-sm btn-secondary"><i class="fa fa-download"></i></button></a>';
                }

                if($user->can('menu_store.procurement.pharmacy.drug_orders.pdfview')) {
                    $actions .= '<a target="_blank" href="'.$s3Url.'" class="'.$hidden.' me-1"
                    title="View PDF"><button class="btn btn-sm btn-secondary"><i class="fa-regular fa-file-pdf"></i></button></a>';
                }

                $newData[] = [
                    'id'        => $value->id,
                    'order_number'  => $value->order_number,
                    'order_date'    => $value->order_date,
                    'po_name'    => $value->po_name,
                    'po_memo'    => $value->po_memo,
                    'account_number'    => $value->account_number,
                    'wholesaler_name'   => isset($value->wholesaler) ? $value->wholesaler->name : '',
                    'comments'      => $value->comments,
                    'created_at' => date('M d, Y g:i A', strtotime($value->pst_created_at)),
                    'assigned_to' => $assignedName,
                    'status' =>  '<button type="button" class="btn btn-'.$value->status->class.' btn-sm w-100" ><small>'.$value->status->name.'</small></button>',
                    'actions' =>  $actions.'</div>'
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    // public function dataOLD2(Request $request)
    // {
    //     if($request->ajax()){
    //         // Page Length
    //         $pageNumber = ( $request->start / $request->length )+1;
    //         $pageLength = $request->length;
    //         $skip       = ($pageNumber-1) * $pageLength;

    //         // Page Order
    //         $orderColumnIndex = $request->order[0]['column'] ?? '0';
    //         $orderBy = $request->order[0]['dir'] ?? 'desc';

    //         $query = DB::table('view_drug_order_items');
            
    //         // Search //input all searchable fields
    //         $search = trim($request->search);

    //         if($request->has('pharmacy_store_id')) {
    //             $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
    //         }
            
    //         if(!empty($search)) {
    //             $query = $query->where(function($query) use (&$search){ 
    //                 $query->orWhere('do_order_number', 'like', "%".$search."%");
    //                 $query->orWhere('do_order_date', 'like', "%".$search."%");
    //                 $query->orWhere('product_description', 'like', "%".$search."%");   
    //                 $query->orWhere('ndc', 'like', "%".$search."%");
    //                 $query->orWhere('unit_size_code', 'like', "%".$search."%");
    //                 $query->orWhere('unit_size_qty', 'like', "%".$search."%");
    //                 $query->orWhere('task_assignee_fullname', 'like', "%".$search."%");
    //                 $query->orWhere('do_created_by_fullname', 'like', "%".$search."%");
    //                 $query->orWhere('status_name', 'like', "%".$search."%");
    //             });
    //         }

            
    //         // $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            
    //         // $query = $query->orderBy($orderByCol, $orderBy);
    //         $recordsFiltered = $recordsTotal = $query->count();
    //         $data = $query->skip($skip)->take($pageLength)->get()->toArray();
            
    //         return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    //     }
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();
                try {
                    $flag = true;

                    $data = json_decode($request->data);
                    // dd($data);

                    $order = $data->order;
                    $prescription = $data->prescription;
                    $items = $data->items;

                    $type_name = 'drug_order';
                    $document = [];

                    $pharmacyPrescription = [];
                    // if(!empty($prescription['patient_id'])) {
                    //     $pharmacyPrescription = new PharmacyPrescription();
                    //     foreach($prescription as $k => $v) {
                    //         $pharmacyPrescription->$k = $v;
                    //     }
                    //     $save = $pharmacyPrescription->save();
                    // }

                    // if(!$save) {
                    //     throw new \Exception("Something went wrong in PharmacyDrugOrderController.add_drug_order.db_transaction.");
                    // }

                    $drugOrder = new DrugOrder();
                    foreach($order as $k => $v) {
                        $drugOrder->$k = $v;
                    }
                    // if(!empty($pharmacyPrescription)) {
                    //     $drugOrder->pharmacy_prescription_id = $pharmacyPrescription->id;
                    // }
                    $drugOrder->user_id = auth()->user()->id;
                    $flag = $drugOrder->save();

                    $drugOrderItems = [];
                    $task = [];
                    if($flag) {
                        // TODO: temporarily removed
                        // for($i = 0; $i < count($items['med_id']); $i++) {
                        //     if(!empty($items['med_id'][$i]) 
                        //         // && !empty($items['inventory_type'][$i]) 
                        //         // && !empty($items['ndc'][$i]) 
                        //         && !empty($items['quantity'][$i])
                        //     ) {
                        //         $item = [
                        //             'med_id' => $items['med_id'][$i],
                        //             'inventory_type' => $items['inventory_type'][$i],
                        //             'ndc' => $items['ndc'][$i],
                        //             'quantity' => $items['quantity'][$i],
                        //             'order_id' => $drugOrder->id, 
                        //             'user_id' => auth()->user()->id,
                        //             'created_at' => Carbon::now()
                        //         ];
                        //         $drugOrderItems[] = $item;
                        //     }
                        // }
                        // if(count($drugOrderItems) > 0) {
                        //     $save = DrugOrderItem::insert($drugOrderItems);
                        // }

                        // Sync to TASK
                        $assigned_to_employee_id = $this->getProcurementAssignee();
                        $task = new Task();
                        $task->subject = "Drug Order Number: ".$drugOrder->order_number.' (PO Memo: '.$drugOrder->po_memo.')';
                        $task->description = "<p>Please process this order until completion.</p>";
                        $task->assigned_to_employee_id = $assigned_to_employee_id;
                        $task->pharmacy_store_id = $drugOrder->pharmacy_store_id;
                        $task->user_id = $drugOrder->user_id;
                        $task->status_id = $drugOrder->status_id;
                        $save = $task->save();

                        if($save) {
                            $drugOrder->task_id = $task->id;
                            $drugOrder->save();
                        }
                        if(!$save) {
                            $flag = false;
                        } else {

                            // Sync to TASK Document
                            if ($request->file('files')) {
                                $pathUpload = 'upload/stores/'.$task->pharmacy_store_id.'/bulletin/tasks/'.$task->id;
                                $files = $request->file('files');
                                foreach ($files as $key => $file) {
                
                                    $document = new StoreDocument;
                                    $document->user_id = auth()->user()->id;
                                    $document->parent_id = $task->id;
                                    $document->category = 'task';
                                    $document->ext = $file->getClientOriginalExtension();
                
                                    @unlink(public_path($pathUpload.'/'.$document->path));
                                    $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).' imported_'.date('Ymd').'-'.$key.'.'.$file->getClientOriginalExtension();
                                    $file->move(public_path($pathUpload), $fileName);
                                    $path = '/'.$pathUpload.'/'.$fileName;
                                    $document->path = $path;
                                    $save = $document->save();                
                
                                    if(!$save) {
                                        $flag = false;
                                    } else {
                                        // importing
                                        $publicPath = public_path().$path;
                                        $absolute_path = str_replace('\\', '/' , $publicPath);
                                        $filePath = $absolute_path;
                                        $params = [
                                            'importExcelParams' => ['path' => $path
                                                , 'store_document_id' => $document->id
                                                , 'drug_order_id' => $drugOrder->id
                                                , 'wholesaler_id' => $drugOrder->wholesaler_id
                                            ],
                                            'ext' => $document->ext,
                                            'filePath' => $filePath,
                                            'pharmacy_store_id' => $drugOrder->pharmacy_store_id
                                        ];
                                        $bool = $this->uploadRepository->uploadProcurementDrugOrderItems($params);
                                        // if(!$bool) {
                                        //     $flag = false;
                                        //     throw new \Exception("Something went wrong in PharmacyDrugOrderController.add_drug_order_items_import_data.db_transaction.");
                                        // }
                                    }
                                }
                            }

                        }
                    } else {
                        $flag = false;
                    }
                    
                    if(!$flag) {
                        throw new \Exception("Something went wrong in PharmacyDrugOrderController.add_drug_order.db_transaction.");
                    } else {
                        //store history
                        $history_body = array(
                            'drugOrder' => $drugOrder,
                            'task' => $task
                        );
                        $history_header = array(
                            'class' => 'DRUG ORDER ',
                            'method' => 'CREATED ',
                            'name' => $drugOrder->order_number,
                            'id' => $drugOrder->id
                        );
                        $this->historiesRepository->store_historyV2($history_header, $history_body, $type_name, $drugOrder->id);
                    }

                    if($flag) {
                        $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $task->status, null);
                    }


                    DB::commit();

                    return json_encode([
                        'data'=> $drugOrderItems,
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack(); 
                    return response()->json([
                        'error' => $e->getMessage(),
                        'message' => 'Something went wrong in PharmacyDrugOrderController.add_drug_order.db_transaction.'
                    ]);
                }
            }catch(\Exception $e){
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyDrugOrderController.add_drug_order.'
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PharmacyDrugOrder $pharmacyDrugOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();
                try {
                    $flag = true;
                    $input = $request->all();
            
                    $dataArray = json_decode($input['data'], true);
                    $order = $dataArray['order'];
                    //$order = $request->order;
                    // $prescription = $request->prescription;
                    // $items = $request->items;

                    // $drugOrderItem = DrugOrderItem::findOrFail($items['id']);
                    // if(isset($drugOrderItem->id)) {
                    //     foreach($items as $k => $v) {
                    //         if(!empty($v) && $v != 'undefined' && $v != '' && $v != null) {
                    //             $drugOrderItem->$k = $v;
                    //         }
                    //     }
                    //     $save = $drugOrderItem->save();
                    //     if(!$save) {
                    //         throw new \Exception("Something went wrong in PharmacyDrugOrderController.edit_drug_order.db_transaction.");
                    //     }
                    // }
                    
                    if ($request->file('file')) {
                        $file = $request->file('file');

                        $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                        $fileExtension = $file->getClientOriginalExtension();
                        $mime_type = $file->getMimeType();
                        
                        $newFileName = date("Ymdhis").Auth::id() .'_'. $fileName  . '.' . $fileExtension;
                        $doc_type = $fileExtension;
                        
                        $path = 'procurement/drugOrders/';
                        
                        // Provide a dynamic path or use a specific directory in your S3 bucket
                        $path_file = 'procurement/drugOrders/'  . $newFileName;

                        // Store the file in S3
                        Storage::disk('s3')->put($path_file, file_get_contents($file));

                        // Optionally, get the URL of the uploaded file
                        $s3url = Storage::disk('s3')->url($path_file);

                        $document = new ModelsFile();

                        $document->filename = $newFileName;
                        $document->path = $path;
                        $document->mime_type = $mime_type;
                        $document->document_type = $doc_type;
                        $document->save();


                        $orderFile = DrugOrder::where('id', $order['id'])->first();
                        $orderFile->file_id = $document->id;
                        $orderFile->save();

                    }

                    $drugOrder = DrugOrder::findOrFail($order['id']);
                    $previousStatus = $drugOrder->status;
                    foreach($order as $k => $v) {
                        $drugOrder->$k = $v;
                    }
                    $save = $drugOrder->save();

                    if(!$save) {
                        throw new \Exception("Something went wrong in PharmacyDrugOrderController.edit_drug_order.db_transaction.");
                    } else {
                        $task = Task::findOrFail($drugOrder->task_id);
              
                        if(isset($task->id)) {
                            $task->status_id = $drugOrder->status_id;
                            if($drugOrder->status_id == 706) {
                                $task->completed_by = auth()->user()->id;
                                $task->completed_at = Carbon::now();
                            }
                            $task->save();

                            if($drugOrder->status_id != $previousStatus->id) {
                                $currentStatus = StoreStatus::findOrFail($drugOrder->status_id);
                                $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $currentStatus, $previousStatus);
                            }
                        }
                    }

                    // $pharmacyPrescription = PharmacyPrescription::findOrFail($drugOrder->pharmacy_prescription_id);
                    // foreach($prescription as $k => $v) {
                    //     $pharmacyPrescription->$k = $v;
                    // }
                    // $flag = $pharmacyPrescription->save();
                    
                    // if(!$flag) {
                    //     throw new \Exception("Something went wrong in PharmacyDrugOrderController.edit_drug_order.db_transaction.");
                    // }

                    DB::commit();

                    return json_encode([
                        'data'=> $drugOrder,
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack(); 
                    return response()->json([
                        'error' => $e->getMessage(),
                        'message' => 'Something went wrong in PharmacyDrugOrderController.edit_drug_order.db_transaction.'
                    ]);
                }
            }catch(\Exception $e){
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyDrugOrderController.edit_drug_order.'
                ]);
            }
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
                
                $path = 'procurement/drugOrders/';
                
                // Provide a dynamic path or use a specific directory in your S3 bucket
                $path_file = 'procurement/drugOrders/'  . $newFileName;

                // Store the file in S3
                Storage::disk('s3')->put($path_file, file_get_contents($file));

                // Optionally, get the URL of the uploaded file
                $s3url = Storage::disk('s3')->url($path_file);

                $save_file = new ModelsFile();

                $save_file->filename = $newFileName;
                $save_file->path = $path;
                $save_file->mime_type = $mime_type;
                $save_file->document_type = $doc_type;
                $save_file->save();

                $document = DrugOrder::where('id', $input['id'])->first();
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
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        if($request->ajax()){

            try {
                DB::beginTransaction();
                $flag = true;

                
               
                // $item = DrugOrderItem::findOrFail($request->id);
                $order = DrugOrder::findOrFail($request->id);
                $file_id = $order->file_id;
                
                if($file_id){
                    $file = ModelsFile::where('id', $file_id)->first();
                    $path = $file->path.$file->filename;
                    
                    if($path != ''){
                        if(Storage::disk('s3')->exists($path)) {
                            Storage::disk('s3')->delete($path);
                        }
                        
                        $file->delete();   
                    }
                }

                
                
                $order->delete();
                $task = Task::findOrFail($order->task_id);
                if(isset($task->id)) {
                    $flag = $task->delete();
                }
                if($flag) {
                    $flag = DrugOrderItemsImportData::where('drug_order_id', $order->id)->delete();
                    if($flag) {
                        $order->delete();
                    }
                }
                
                // if(!$flag) {
                //     throw new \Exception("Something went wrong in PharmacyDrugOrderController.delete_drug_order.db_transaction.");
                // }

                DB::commit();

                return json_encode([
                    'data'=> $order,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyDrugOrderController.delete_drug_order.db_transaction.'
                ]);
            }
            
        }
    }

}
