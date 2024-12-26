<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Helpers\Helper;


use App\Models\Clinic;
use App\Models\Medication;
use App\Models\StoreStatus;

use App\Interfaces\IHistoriesRepository;
use App\Models\Employee;
use App\Models\File as ModelsFile;
use App\Models\Inmar;
use App\Models\InmarItem;
use App\Models\StoreDocument;
use App\Models\Task;
use App\Models\Wholesaler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

use App\Interfaces\ITaskRepository;

use Carbon\Carbon;

class InmarController extends Controller
{
    private $inmar;
    private IHistoriesRepository $historiesRepository;
    private ITaskRepository $taskRepository;

    public function __construct(Inmar $inmar
        , IHistoriesRepository $historiesRepository
        , ITaskRepository $taskRepository
    ) {
        $this->inmar = $inmar;
        $this->historiesRepository = $historiesRepository;
        $this->taskRepository = $taskRepository;

        $this->middleware('permission:menu_store.procurement.pharmacy.inmar_returns.index|menu_store.procurement.pharmacy.inmar_returns.create|menu_store.procurement.pharmacy.inmar_returns.update|menu_store.procurement.pharmacy.inmar_returns.delete');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);
            $years = $this->getYears();
            $months = $this->getMonths();
            $currentYear = now()->year;
            $currentMonth = now()->month;
            $breadCrumb = ['Procurement', 'PharmaLogistics'];
            return view('/stores/procurement/pharmacy/inmarReturns/index', [
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
        $query = Inmar::with('task.assignedTo', 'items', 'status', 'wholesaler', 'file')
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
                'created_at' => $val->created_at,
                'comments' => $val->comments,
                'file' => $val->file,
                'image' => $val->task->assignedTo->image,
                'initials' => $val->task->assignedTo->firstname[0].$val->task->assignedTo->firstname[0],
                'initials_random_color' => $val->task->assignedTo->initials_random_color,
                'medications' => $val->items,
                'name' => $val->name,
                'po_name' => $val->po_name,
                'pharmacy_store_id' => $val->pharmacy_store_id,
                'return_date' => $val->return_date,
                'status' => $val->status,
                'wholesaler_id' => $val->wholesaler_id,
                'wholesaler' => $val->wholesaler,
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

    public function get_return_type(Request $request)
    {
        if($request->ajax()){
           $data = StoreStatus::where('category', 'return_type')->pluck('name')->all();

            return json_encode([
                'data'=> $data,
            ]);
        }
    }

    public function get_status(Request $request)
    {
        if($request->ajax()){
           $data = StoreStatus::where('category', 'procurement_order')->pluck('name')->all();

            return json_encode([
                'data'=> $data,
            ]);
        }
    }

    public function get_medications(Request $request)
    {
        if($request->ajax()){
            $data = Medication::select('med_id', 'name')->where('name', 'like', "%".$request->term."%")->take(10)->get();

            return json_encode([
                'data'=> $data,
            ]);
        }
    }

    public function get_clinics(Request $request)
    {
        if($request->ajax()){
            $data = Clinic::select('id', 'name')->where('name', 'like', "%".$request->term."%")->take(10)->get();

            return json_encode([
                'data'=> $data,
            ]);
        }
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            
            $helper =  new Helper;
            $input = $request->all();
            //2147483647 max of int
            $validation = Validator::make($input, [
                'return_date' => 'required',
                // 'name' => 'required|alpha_num|max:30|min:1',
                // 'prescriber_name' => 'required|max:30|min:1',
                // 'clinic_id' => 'required',
                'account_number' => 'required',
                'po_name' => 'required',
            ]);

            if ($validation->passes()){
                
                $inmar = new Inmar();
                $inmar->name = $input['name'];
                $inmar->po_name = $input['po_name'];
                $inmar->account_number = $input['account_number'];
                $inmar->pharmacy_store_id = $input['menu_store_id'];
                $inmar->comments = $input['comments'];
                $inmar->wholesaler_id = $input['wholesaler_id'];
                $inmar->return_date = $input['return_date'];
                $inmar->user_id = auth()->user()->id;
                if(!isset($input['status_id'])) {
                    $inmar->status_id = 701;
                }
                $inmar->save();
                $inmar_id = $inmar->id;


                // Create Item entries
                $check_entry = 0;
                $inmar_data = [];
                for ($i = 0; $i <= $input['med_count']; $i++) {
                    if (!empty($request->input("drugname$i")&&$request->input("quantity$i"))) {
                        
                        $med_name = Medication::where('med_id', $request->input("drugname$i"))->first();

                        $inmar_item = new InmarItem();
                        $inmar_item->inmar_id = $inmar_id;
                        $inmar_item->drugname = $med_name->name;
                        $inmar_item->drug_id = $request->input("drugname$i");
                        $inmar_item->quantity = $request->input("quantity$i");
                        $inmar_item->ndc = $request->input("ndc$i");
                        
                        $inmar_item->save();
                        array_push($inmar_data, $inmar_item);
                        $check_entry = 1;
                    }
                }

                // $task = new Task();
                // $task->subject = 'New INMAR '.$input['name'];
                // $task->pharmacy_store_id = $input['menu_store_id'];
                // $task->user_id = auth()->user()->id;


                //return no entry of medication
                if($check_entry === 0)
                {
                    $medication_validate = [
                        "medication_holder" => ["Input at least one medication field."],
                        "message" => "Employee saving failed."
                    ];

                    $del_inmar = Inmar::where('id', $inmar_id);
                    $del_inmar->delete();

                    return json_encode(['status'=>'error',
                        'errors'=> $medication_validate,
                        'message'=>'Employee saving failed.']);
                }

                $subject = 'INMAR Return Number: '.$input['name'];
                $store = $input['menu_store_id'];
                $status = 701;
                $task = $this->taskOn($subject,$store,$status);

                if(!empty($task)) {
                    $inmar->task_id = $task->id;
                    $inmar->save();
                }

                $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $task->status, null);

                // //store history
                // $history_body = array(
                //     'inmar' => $inmar_data
                // );
                // $history_header = array(
                //     'class' => 'INMAR ',
                //     'method' => 'CREATED ',
                //     'name' => $input['name'],
                //     'id' => $inmar->id
                // );
                // $this->historiesRepository->store_historyV2($history_header, $history_body, 'invoice', $inmar->id);
            
 
                return json_encode([
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

    public function taskOn($subject, $store, $status_id)
    {
        $emp_id = $this->assignTo();

        $task = new Task();
        $task->subject = $subject;
        $task->pharmacy_store_id = $store;
        $task->user_id = $emp_id->user_id;
        $task->assigned_to_employee_id = $emp_id->emp_id;
        $task->status_id = $status_id;
        $task->save();


        return $task;
    }

    public function assignTo()
    {
        $employees = Employee::join('users', 'employees.user_id', '=', 'users.id')
                      ->select('employees.id AS emp_id', 'users.id AS user_id')
                      ->where('users.email', 'trpadmin@mgmt88.com')
                      ->first();

        return $employees;
    }

    private function pathUpload($pharmacy_store_id, $parent_id) : string
    {
        // $absolute_path = str_replace('\\', '/' , base_path());
        return 'upload/stores/'.$pharmacy_store_id.'/inmar/'.$parent_id;
    }

    public function update(Request $request)
    {
        if($request->ajax()){
            
            $input = $request->all();
            
            $dataArray = json_decode($input['data'], true);
            
            $validation = Validator::make($dataArray, [
                'name' => 'required',
                'file' => 'mimes:pdf,csv,xlsx',
            ]);
            
            if ($validation->passes()){

                if ($request->file('file')) {
                    $file = $request->file('file');

                    $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $fileExtension = $file->getClientOriginalExtension();
                    $mime_type = $file->getMimeType();
                    
                    $newFileName = date("Ymdhis").Auth::id() .'_'. $fileName  . '.' . $fileExtension;
                    $doc_type = $fileExtension;
                    
                    $path = 'procurement/inmarReturns/';
                    
                    // Provide a dynamic path or use a specific directory in your S3 bucket
                    $path_file = 'procurement/inmarReturns/'  . $newFileName;

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


                    $inmarFile = Inmar::where('id', $dataArray['id'])->first();
                    $inmarFile->file_id = $document->id;
                    $inmarFile->save();

                }

                
                    
                $inmar = Inmar::where('id', $dataArray['id'])->first();
                $previousStatus = $inmar->status;
                // $old_status_id = $inmar->status_id;
                // if($inmar->file_id == '' && $dataArray['status_id'] == 34){
                //     $status_id = $old_status_id;
                //     $return_status = 'warning';
                //     $return_message = 'Upload Invoice to change to Completed status.';
                // }
                // else{
                //     $status_id = $dataArray['status_id'];
                //     $return_status = 'success';
                //     $return_message = 'Record has been saved.';
                // }
                $inmar->name = $dataArray['name'];
                $inmar->po_name = $dataArray['po_name'];
                $inmar->account_number = $dataArray['account_number'];
                $inmar->wholesaler_id = $dataArray['wholesaler_id'];
                $inmar->comments = $dataArray['comments'];
                if(isset($dataArray['status_id'])) {
                    $inmar->status_id = $dataArray['status_id'];
                }
                $inmar->return_date =  $dataArray['return_date'];
                $save = $inmar->save();
                
                if($save && isset($dataArray['status_id'])) {
                    $task = Task::findOrFail($inmar->task_id);
                    if(isset($task->id)) {
                        $task->status_id = $inmar->status_id;
                        if($inmar->status_id == 706) {
                            $task->completed_by = auth()->user()->id;
                            $task->completed_at = Carbon::now();
                        }
                        $task->save();

                        if($inmar->status_id != $previousStatus->id) {
                            $currentStatus = StoreStatus::findOrFail($inmar->status_id);
                            $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $currentStatus, $previousStatus);
                        }
                    }

                }

                if($request->file('file')){
                    $doc_id = $document->id;
                }
                else{
                    $doc_id = $inmar->file_id;
                }

                return json_encode([
                    'file_id' => $doc_id,
                    'status_id' => $inmar->status_id,
                    'status'=> 'success',
                    'message'=> 'Successfully updated.'
                ]);
            } else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Employee saving failed.'
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
                
                $path = 'procurement/inmarReturns/';
                
                // Provide a dynamic path or use a specific directory in your S3 bucket
                $path_file = 'procurement/inmarReturns/'  . $newFileName;

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

                $document = Inmar::where('id', $input['id'])->first();
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
                    
                    $inmar = new InmarItem();
                    $inmar->inmar_id = $request->input("inmar_id");
                    $inmar->drug_id = $request->input("med_id");
                    $inmar->quantity = $request->input("quantity");
                    $inmar->ndc = $request->input("ndc");
                    $inmar->drugname = $med_name->name;
                    $inmar->save();
                }
                else{
                    $med_name = Medication::where('med_id', $request->input("med_id"))->first();
                    
                    $inmar = InmarItem::where('id', $input['id'])->first();
                    
                    $inmar->drug_id = $request->input("med_id");
                    $inmar->quantity = $request->input("quantity");
                    $inmar->ndc = $request->input("ndc");
                    $inmar->drugname = $med_name->name;
                    $inmar->save();
                }
                
                

                return json_encode([
                    'data'=> $inmar->id,
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
            
            $inmar = InmarItem::where('id', $id)->first();
            $inmar_old = $inmar;
            $inmar->delete();

            // //delete history
            // $history_body = array(
            //     'inmar' => $inmar_old,
            // );
            // $history_header = array(
            //     'class' => 'INMAR',
            //     'method' => 'DELETED inmar ',
            //     'name' => $inmar_old->name,
            //     'id' => $inmar_old->id,
            // );
            // // $this->delete_history($history_header, $history_body, 'inmar', $inmar_old->id);
            // $this->historiesRepository->delete_history($history_header, $history_body, 'inmar', $inmar_old->id);
            

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
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
            $file = ModelsFile::where('id', $id)->first();
            $file_id = $file->id;
            $path = $file->path.$file->filename;

            if($path != ''){
                if(Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }

                $file->delete();   
            }

            $inmar = Inmar::where('file_id', $file_id)->first();
            $inmar->file_id = '';
            $inmar->save();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){

            $input = $request->all();
            
            $id = $input['id'];
            $inmar = Inmar::where('id', $id)->first();
            $file_id = $inmar->file_id;
            
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
            // $doc = StoreDocument::where('parent_id', $id)->first();
            
            // $directoryPath = dirname($doc->path);
            // $directory = public_path($directoryPath);
            // File::deleteDirectory($directory);
            // $doc->delete();
            
            InmarItem::where('inmar_id', $id)->delete();

            Task::where('id',$inmar->task_id)->delete();
            
            $inmar->delete();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    public function get_inmar_medications(Request $request, $id)
    {
        if($request->ajax()){
            
            $result = Inmar::where('id', $id)->first();
            $data = DB::table('view_inmars')->where('name', $result->name)->get();
    
            return json_encode([
                'data'=> $data,
            ]);
        }
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

            // get data from products table
            // $query = DB::table('view_inmars')->where('pharmacy_store_id', $id)->groupBy('name');
            $query = Inmar::select(
                    'inmars.id',
                    'inmars.name',
                    'inmars.status_id AS status_id',
                    'store_statuses.class AS status_class',
                    'store_statuses.name AS status_name',
                    'inmars.return_date',
                    'inmars.po_name',
                    'inmars.comments',
                    'inmars.account_number',
                    'inmars.created_at',
                    'wholesalers.name as wholesaler_name',
                    'inmars.pharmacy_store_id',
                    'inmars.wholesaler_id',
                    'files.path AS file_path',
                    'files.filename AS file_name',
                    'files.mime_type AS mime_type',
                    'files.id AS file_id'
                    
                )
                ->join('store_statuses', 'store_statuses.id', '=', 'inmars.status_id')
                ->leftJoin('files', 'files.id', '=', 'inmars.file_id')
                ->leftJoin('wholesalers', 'wholesalers.id', '=', 'inmars.wholesaler_id')
                ->where('store_statuses.category', 'procurement_order')
                ->where('pharmacy_store_id', $id);

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
            $wholesalers = Wholesaler::where('category', 'procurement')->orderBy('name')->get()->toArray();
            
            $newData = [];
            foreach ($data as $value) {
                $medications = $value->items()->get()->toArray();

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
                if(Auth::user()->can('menu_store.procurement.pharmacy.inmar_returns.index')) {
                    $actions .= '<a title="View" href="javascript:void(0)" data-id="'.$value->id.'" 
                                data-array="'.htmlspecialchars(json_encode($value)).'" class="me-1"
                                id="inmar-show-btn-'.$value->id.'"><button class="btn btn-sm btn-primary" onclick="showViewForm('.$value->id.','.htmlspecialchars(json_encode($medications)).','.htmlspecialchars(json_encode($wholesalers)).');"><i class="fa fa-eye"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.pharmacy.inmar_returns.update')) {
                    $actions .= '<a title="Edit" href="javascript:void(0)" data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"  class="me-1"
                                id="inmar-show-btn-'.$value->id.'"><button class="btn btn-sm btn-primary" onclick="showEditForm('.$value->id.','.htmlspecialchars(json_encode($aStatuses)).','.htmlspecialchars(json_encode($medications)).','.htmlspecialchars(json_encode($wholesalers)).');"><i class="fa fa-pencil"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.pharmacy.inmar_returns.delete')) {
                    $actions .= '<a title="Delete" href="javascript:void(0)" class="me-1"><button class="btn btn-sm btn-danger" onclick="ShowConfirmDeleteForm(' . $value->id . ')"><i class="fa fa-trash-can"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.pharmacy.inmar_returns.upload')) {
                    $actions .= '<a title="Upload" href="javascript:void(0)" class="me-1"><button data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"
                    id="upload-show-btn-'.$value->id.'" onclick="showUploadForm(' . $value->id . ')" 
                    class="btn btn-sm btn-secondary" ><i class="fa-solid fa-cloud-arrow-up"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.pharmacy.inmar_returns.download')) {
                    $actions .= '<a class="'.$download_hidden.' me-1" href="/admin/file/download/'.$value->file_id.'" title="Download File"><button class="btn btn-sm btn-secondary"><i class="fa fa-download"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.pharmacy.inmar_returns.pdfview')) {
                    $actions .= '<a target="_blank" href="'.$s3Url.'" class="'.$hidden.' me-1"
                    title="View PDF"><button class="btn btn-sm btn-secondary"><i class="fa-regular fa-file-pdf"></i></button></a>';
                }
                $actions .= '</div>';
                
                $newData[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                    'drug_name' => $value->drug_name,
                    'status' => '<button class="btn btn-'.$value->status_class.' btn-sm w-100">'.$value->status_name.'</button>',
                    'return_date' => $value->return_date,
                    'po_name' => $value->po_name,
                    'account_number' => $value->account_number,
                    'wholesaler_name' => $value->wholesaler_name,
                    'created_at' => date('M d, Y g:i A', strtotime($value->pst_created_at)),
                    'actions' =>  $actions
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }


}
