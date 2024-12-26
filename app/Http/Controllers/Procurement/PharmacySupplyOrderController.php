<?php

namespace App\Http\Controllers\Procurement;

use App\Models\Task;
use App\Models\SupplyOrder;
use App\Models\SupplyOrderItem;
use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\StoreStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

use App\Interfaces\ITaskRepository;
use Illuminate\Support\Facades\Validator;

class PharmacySupplyOrderController extends Controller
{
    private ITaskRepository $taskRepository;

    public function __construct(ITaskRepository $taskRepository) {
        $this->taskRepository = $taskRepository;
        $this->middleware('permission:menu_store.procurement.pharmacy.supplies_orders.index|menu_store.procurement.pharmacy.supplies_orders.create|menu_store.procurement.pharmacy.supplies_orders.update|menu_store.procurement.pharmacy.supplies_orders.delete');
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
            $breadCrumb = ['Procurement', 'Pharmacy', 'Supply Orders'];
            return view('/stores/procurement/pharmacy/supplyOrders/index', [
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

            foreach ($orderStatuses as $statusId => $statuses) {
                $data['order' . $statusId] = $this->getTasksByStatusId($statusId, $selectedYear, $selectedMonth);
            }
            
            return response()->json(['data' => $data], 200);
        }
    }

    private function getTasksByStatusId($statusId, $year = null, $month = null) {
        $query = SupplyOrder::with('task.assignedTo', 'items', 'status', 'wholesaler', 'file')
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
                'assignedTo' => $val->task->assignedTo->firstname.' '.$val->task->assignedTo->lastname,
                'comments' => $val->comments,
                'created_at' => $val->created_at,
                'file' => $val->file,
                'file_id' => $val->file_id,
                'image' => $val->task->assignedTo->image,
                'initials' => $val->task->assignedTo->firstname[0].$val->task->assignedTo->lastname[0],
                'initials_random_color' => $val->task->assignedTo->initials_random_color,
                'items' => $val->items,
                'status' => $val->status,
                'order_number' => $val->order_number,
                'order_date' => $val->order_date,
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

    public function data(Request $request, $id)
    {
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // $query = DB::table('view_supply_orders');
            $query = SupplyOrder::with('items', 'status', 'wholesaler', 'file');
            
            // Search //input all searchable fields
            $search = $request->search;

            $search = trim($request->search);

            if($request->has('pharmacy_store_id')) {
                $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
            }
            
            if(!empty($search)) {
                $query = $query->where(function($query) use ($search){ 
                    $query->orWhere('order_number', 'like', "%".$search."%");
                    $query = $query->whereHas('status', function($query) use ($search){ 
                        $query->orWhere('name', 'like', "%".$search."%");
                    });
                });
            }

            
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

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

                $status = $value->status ?? [];
                $actions = '<button type="button" class="btn btn-primary btn-sm me-1" 
                    id="supply-order-show-btn-'.$value->id.'"
                    data-array="'.htmlspecialchars(json_encode($value)).'"
                onclick="showViewDetailsModal('.$value->id.');"><i class="fa-solid fa-eye"></i></button>';
                if(Auth::user()->canany(['menu_store.procurement.pharmacy.supplies_orders.update', 'menu_store.procurement.pharmacy.supplies_orders.updateactualqty', 'menu_store.procurement.pharmacy.supplies_orders.updateall'])) {
                    $actions .= '<button type="button" class="btn btn-primary btn-sm me-1" 
                            onclick="showEditDetailsModal('.$value->id.');"><i class="fa-solid fa-pencil"></i></button>';
                }
                if(Auth::user()->can('menu_store.procurement.pharmacy.supplies_orders.delete')) {
                    $actions .= '<button type="button" 
                        onclick="ShowConfirmDeleteForm(' . $value->id . ')" 
                        class="btn btn-danger btn-sm me-1" ><i class="fa-solid fa-trash-can"></i></button>';
                }
                if(Auth::user()->can('menu_store.procurement.pharmacy.supplies_orders.upload')) {
                    $actions .= '<a title="Upload" href="javascript:void(0)" class="me-1"><button data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"
                    id="upload-show-btn-'.$value->id.'" onclick="showUploadForm(' . $value->id . ')" 
                    class="btn btn-sm btn-secondary" ><i class="fa-solid fa-cloud-arrow-up"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.pharmacy.supplies_orders.download')) {
                    $actions .= '<a class="'.$download_hidden.' me-1" href="/admin/file/download/'.$value->file_id.'" title="Download File"><button class="btn btn-sm btn-secondary"><i class="fa fa-download"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.pharmacy.supplies_orders.pdfview')) {
                    $actions .= '<a target="_blank" href="'.$s3Url.'" class="'.$hidden.' me-1"
                    title="View PDF"><button class="btn btn-sm btn-secondary"><i class="fa-regular fa-file-pdf"></i></button></a>';
                }
                $actions .= '';


                $newData[] = [
                    'id' => $value->id,
                    'order_number' => $value->order_number,
                    'order_date'    => $value->order_date,
                    'wholesaler' => isset($value->wholesaler) ? $value->wholesaler->name : '',
                    'created_at' => date('M d, Y g:i A', strtotime($value->pst_created_at)),
                    'status' => '<button type="button" class="btn btn-'.$status->class.' btn-sm w-100">'.$status->name.'</button>',
                    'actions' =>  $actions,
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();
                $flag = true;
                $order = $request->order;
                $items = $request->items;

                $supplyOrder = new SupplyOrder();
                $supplyOrder->pharmacy_store_id = $order['pharmacy_store_id'];
                $supplyOrder->order_number = $order['order_number'];
                $supplyOrder->comments = $order['comments'];
                $supplyOrder->status_id = isset($order['status_id']) ? $order['status_id'] : 701;
                $supplyOrder->wholesaler_id = isset($order['wholesaler_id']) ? $order['wholesaler_id'] : 2;
                $supplyOrder->user_id = auth()->user()->id;
                $flag = $supplyOrder->save();

                $task = new Task();
                $task->subject = "Supply Order Number: ".$supplyOrder->order_number;
                $task->description = "<p>Please process this order until completion.</p>";
                $task->assigned_to_employee_id = $this->getProcurementAssignee();
                $task->pharmacy_store_id = $supplyOrder->pharmacy_store_id;
                $task->user_id = $supplyOrder->user_id;
                $task->status_id = $supplyOrder->status_id;
                $save = $task->save();
                $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $task->status, null);
                if($save) {
                    $supplyOrder->task_id = $task->id;
                    $supplyOrder->save();
                }

                $supplyOrderItems = [];
                if($flag) {
                    for($i = 0; $i < count($items['description']); $i++) {
                        if(!empty($items['description'][$i]) 
                            && !empty($items['quantity'][$i])
                        ) {
                            $item = [
                                'code' => empty($items['code'][$i]) ? '' : $items['code'][$i],
                                'number' => $items['item'][$i],
                                'description' => $items['description'][$i],
                                'name' => $items['description'][$i],
                                'quantity' => $items['quantity'][$i],
                                'item_id' => $items['number'][$i], 
                                'order_id' => $supplyOrder->id, 
                                'user_id' => auth()->user()->id,
                                'url' => null
                            ];
                            
                            $supplyOrderItems[] = $item; 
                        }
                        else{
                            if($items['url'][$i] != ''){
                                $item = [
                                    'code' => null,
                                    'number' => null,
                                    'description' => 'url request',
                                    'name' => null,
                                    'quantity' => $items['quantity'][$i],
                                    'item_id' => null, 
                                    'order_id' => $supplyOrder->id, 
                                    'user_id' => auth()->user()->id,
                                    'url' => $items['url'][$i]
                                ];

                                $supplyOrderItems[] = $item; 
                            }
                        }

                        
                    }
                    // dd($supplyOrderItems);
                    $save = SupplyOrderItem::insert($supplyOrderItems);
                    if(!$save) {
                        $flag = false;
                    }
                } else {
                    $flag = false;
                }
                
                if(!$flag) {
                    throw new \Exception("Something went wrong in PharmacySupplyOrderController.add_supply_order.db_transaction.");
                }

                DB::commit();

                return json_encode([
                    'data'=> $supplyOrderItems,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacySupplyOrderController.add_supply_order.db_transaction.'
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PharmacySupplyOrder $pharmacySupplyOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();
                $flag = true;
                $input = $request->all();
            
                $dataArray = json_decode($input['data'], true);
                $order = $dataArray['order'];

                if ($request->file('file')) {
                    $file = $request->file('file');

                    $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $fileExtension = $file->getClientOriginalExtension();
                    $mime_type = $file->getMimeType();
                    
                    $newFileName = date("Ymdhis").Auth::id() .'_'. $fileName  . '.' . $fileExtension;
                    $doc_type = $fileExtension;
                    
                    $path = 'procurement/supplyOrders/';
                    
                    // Provide a dynamic path or use a specific directory in your S3 bucket
                    $path_file = 'procurement/supplyOrders/'  . $newFileName;

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


                    $orderFile = SupplyOrder::where('id', $order['id'])->first();
                    $orderFile->file_id = $document->id;
                    $orderFile->save();

                }

                $supplyOrder = SupplyOrder::findOrFail($order['id']);
                $previousStatus = $supplyOrder->status;
                $supplyOrder->order_number = $order['order_number'];
                $supplyOrder->order_date = $order['order_date'];
                $supplyOrder->comments = $order['comments'];
                if(isset($order['status_id'])) {
                    $supplyOrder->status_id = $order['status_id'];
                }
                $supplyOrder->wholesaler_id = $order['wholesaler_id'];
                $supplyOrder->user_id = auth()->user()->id;
                $supplyOrder->save();

                $task = Task::findOrFail($supplyOrder->task_id);
                if(isset($task->id)) {
                    $task->status_id = $supplyOrder->status_id;
                    if($supplyOrder->status_id == 706) {
                        $task->completed_by = auth()->user()->id;
                        $task->completed_at = Carbon::now();
                    }
                    $task->save();

                    if($supplyOrder->status_id != $previousStatus->id) {
                        $currentStatus = StoreStatus::findOrFail($supplyOrder->status_id);
                        $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $currentStatus, $previousStatus);
                    }
                }

                DB::commit();

                return json_encode([
                    'data'=> $supplyOrder,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacySupplyOrderController.edit_supply_order.db_transaction.'
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
                
                $path = 'procurement/supplyOrders/';
                
                // Provide a dynamic path or use a specific directory in your S3 bucket
                $path_file = 'procurement/supplyOrders/'  . $newFileName;

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

                $document = SupplyOrder::where('id', $input['id'])->first();
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

            $order = SupplyOrder::where('file_id', $file_id)->first();
            $order->file_id = '';
            $order->save();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
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
                
                $order = SupplyOrder::findOrFail($request->id);
                $file_id = $order->file_id;
                
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

                $order = SupplyOrder::findOrFail($request->id);
                $flag = SupplyOrderItem::where('order_id', $order->id)->delete();
                $task = Task::findOrFail($order->task_id)->delete();
                $order->delete();

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
                    'message' => 'Something went wrong in PharmacySupplyOrderController.delete_supply_order.db_transaction.'
                ]);
            }
        }
    }


}
