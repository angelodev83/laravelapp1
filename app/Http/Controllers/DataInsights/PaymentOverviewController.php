<?php

namespace App\Http\Controllers\DataInsights;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Invoice;
use App\Models\PharmacyStore;
use App\Models\StoreStatus;
use App\Traits\HistoryTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class PaymentOverviewController extends Controller
{
    public function index($id)
    {
        try{
            $this->checkStorePermission($id);
            
            $user = Auth::user();

            $breadCrumb = ['Data Insights', 'Payments Overview'];
            
            return view('/stores/dataInsights/paymentOverview/index', compact('user','breadCrumb'));
        }
        catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }

        // $user = Auth::user();

        // $stores = json_encode(explode(',', env('STORES')));

        // $breadCrumb = ['Operation', 'Payments Overview'];
        // return view('/stores/operations/paymentOverview/index', compact('user', 'breadCrumb', 'stores'));
    }

    public function get_stores(Request $request)
    {
        if($request->ajax()){
           $data = explode(',', env('STORES'));

            return json_encode([
                'data'=> $data,
            ]);
        }
    }

    public function get_statuses(Request $request)
    {
        if($request->ajax()){
            $data = StoreStatus::select('id', 'name')
                ->where('category', 'invoice')
                ->orderBy('sort', 'asc')
                ->get();
            $store = PharmacyStore::select('id','code')->get()->toArray();

            return json_encode([
                'data'=> $data,
                'store' => $store,
            ]);
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

    public function store(Request $request)
    {   
        if($request->ajax()){

            $user = auth()->check() ? Auth::user() : redirect()->route('login');
            
            $file = $request->file('file');

            $input = $request->all();
            
            $validation = Validator::make($input, [
                'file' => 'required|mimes:pdf,csv,xlsx',
                'amount' => 'required|numeric|between:-9999999999.99,9999999999.99',
                'month' => 'required',
            ]);
            if ($validation->passes()){
                if ($file->isValid()) {

                    $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $fileExtension = $file->getClientOriginalExtension();
                    $mime_type = $file->getMimeType();
                    
                    $newFileName = date("Ymdhis").Auth::id() .'_'. $fileName  . '.' . $fileExtension;
                    $doc_type = $fileExtension;
                    
                    $path = 'financials/invoice/';
                    
                    // Provide a dynamic path or use a specific directory in your S3 bucket
                    $path_file = 'financials/invoice/'  . $newFileName;

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

                    $invoice = new Invoice();
                    $invoice->name = $fileName;
                    $invoice->store_status_id = $request['status'];
                    $invoice->store = $request['store_id'];
                    $invoice->amount = $request['amount'];
                    $invoice->month = $request['month']."-01";
                    $invoice->created_by = Auth::id();
                    $invoice->updated_by = Auth::id();
                    $invoice->file_id = $file_id;
                    $invoice->type = "Payment";
                    $invoice->save();

                    return response()->json([
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ], 200);

                } else {
                    return response()->json([
                        'file' => $file->getClientOriginalName(),
                        'error' => 'Invalid file',
                        'status'=>'error',
                        'message'=>'Invalid file'
                    ], 400);
                }
            }
            else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Employee saving failed.'
                ], 422);
            }
        }
    }

    public function update(Request $request)
    {
        if($request->ajax()){
            $input = $request->all();

            // $old_invoice = Invoice::where('id', $input['id'])->first();
            // $old_status = DB::table('invoice_statuses')->where('id', $old_invoice->invoice_status_id)->first();

            if ($request->hasFile('file')) {
                $file = $request->file('file');

                //$input = $request->all();
                
                $validation = Validator::make($input, [
                    'file' => 'required|mimes:pdf,csv,xlsx',
                    'amount' => 'required|numeric|between:-9999999999.99,9999999999.99',
                    'name' => 'required|max:50|min:1',
                    'month' => 'required',
                ]);

                if ($validation->passes()){
                    if ($file->isValid()) {
                        
                        $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                        $fileExtension = $file->getClientOriginalExtension();
                        $mime_type = $file->getMimeType();
                        
                        $newFileName = date("Ymdhis").Auth::id() .'_'. $fileName  . '.' . $fileExtension;
                        $doc_type = $fileExtension;
                        
                        $path = 'financials/invoice/';
                        
                        // Provide a dynamic path or use a specific directory in your S3 bucket
                        $path_file = 'financials/invoice/'  . $newFileName;

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

                        $invoice = Invoice::where('id', $input['id'])->first();
                        $invoice->name = $input['name'];
                        $invoice->store_status_id = $input['status'];
                        $invoice->amount = $input['amount'];
                        $invoice->month = $input['month']."-01";
                        $invoice->updated_by = Auth::id();
                        $invoice->file_id = $file_id;
                        $invoice->save();

                        
                        return response()->json([
                            'status'=>'success',
                            'message'=>'Record has been saved.'
                        ], 200);

                    } else {
                        return response()->json([
                            'file' => $file->getClientOriginalName(),
                            'error' => 'Invalid file',
                            'status'=>'error',
                            'message'=>'Invalid file'
                        ], 404);
                    }
                }
                else{
                    return json_encode([
                        'status'=>'error',
                        'errors'=> $validation->errors(),
                        'message'=>'Employee saving failed.'
                    ], 422);
                }
            }
            else{
                $validation = Validator::make($input, [
                    'amount' => 'required|numeric|between:-9999999999.99,9999999999.99',
                    'name' => 'required|max:50|min:1',
                    'month' => 'required',
                ]);

                if ($validation->passes()){
                    
                    $invoice = Invoice::where('id', $input['id'])->first();
                    $invoice->name = $input['name'];
                    $invoice->store_status_id = $input['status'];
                    $invoice->amount = $input['amount'];
                    $invoice->month = $input['month']."-01";
                    $invoice->updated_by = Auth::id();
                    $invoice->save();
                    
                    
                    return response()->json([
                        'status'=>'success',
                        'message'=>'Record has been updated.'
                    ], 200);
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
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){

            $input = $request->all();
            $id = $input['id'];
            $file = Invoice::select(
                    'invoices.id AS id', 'invoices.name', 'invoices.store', 'invoices.month',
                    'invoices.updated_at', 'invoices.amount', 'store_statuses.id AS status_id',
                    'store_statuses.name AS status_name', 
                    DB::raw('MONTHNAME(invoices.month) AS month_name'),
                    'store_statuses.color', 'store_statuses.text_color', 'files.id AS file_id', 'files.filename',
                    'files.mime_type', 'files.path', 
                    DB::raw('(SELECT name FROM users WHERE id = invoices.created_by) AS created_by'),
                    DB::raw('(SELECT name FROM users WHERE id = invoices.updated_by) AS updated_by')
                )
                ->join('store_statuses', 'invoices.store_status_id', '=', 'store_statuses.id')
                ->leftJoin('files', 'invoices.file_id', '=', 'files.id')
                ->where('invoices.type', '=', 'payment')
                ->where('store_statuses.category', 'invoice')
                ->where('invoices.id', $id)->first();
                
            $path = $file->path.$file->filename;
            
            if($path != ''){
                if(Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }

                $file_data = File::where('id', $file->file_id)->first();
                $file_data->delete();
            }

            if($input['delete_file_only'] == 0){
                $invoice = Invoice::where('id', $id)->first();
                $invoice->delete();
            }

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
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
            $query = Invoice::select(
                    'invoices.id AS id', 'invoices.name', 'invoices.store', 'invoices.month',
                    'invoices.updated_at', 'invoices.amount', 'store_statuses.id AS status_id',
                    'store_statuses.name AS status_name', 
                    DB::raw('MONTHNAME(invoices.month) AS month_name'),
                    'store_statuses.color', 'store_statuses.text_color', 'files.id AS file_id', 'files.filename',
                    'files.mime_type', 'files.path', 
                    DB::raw('(SELECT name FROM users WHERE id = invoices.created_by) AS created_by'),
                    DB::raw('(SELECT name FROM users WHERE id = invoices.updated_by) AS updated_by')
                )
                ->join('store_statuses', 'invoices.store_status_id', '=', 'store_statuses.id')
                ->leftJoin('files', 'invoices.file_id', '=', 'files.id')
                ->where('invoices.type', '=', 'payment')
                ->where('store_statuses.category', 'invoice')
                ->where('invoices.store', $id);

            // $dateFrom = $request->searchByFromDate;

            // Search //input all searchable fields
            $search = $request->search;
            $from = $request->searchByFromDate;
            $to = $request->searchByToDate;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        // Default search behavior for other columns
                        $query->orWhere("$column[name]", 'like', "%".$search."%");   
                    }  
                }  
            });
            if($from != "" && $to != ""){
                $query->whereBetween('invoices.month', [$from,$to]);   
            }
            
             //default field for order
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];

            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();
            
            
            $newData = [];
            foreach ($data as $value) {
                $hidden='';
                $s3Url='';
                $download_hidden='';
                $file_id_holder=0;
                if($value->file_id != ""){
                    $s3Url = Storage::disk('s3')->temporaryUrl(
                        $value->path.$value->filename,
                        now()->addMinutes(30)
                    );
                    $file_id_holder = $value->file_id;
                    ($value->mime_type != 'application/pdf')?$hidden="d-none":'';
                }
                else{
                    $hidden = "d-none";
                    $download_hidden = "d-none";
                }
                $user_id = Auth::id();
                $newData[] = [
                    'id' => $value->id,
                    'name' => '<a href="javascript:;" data-id="'.$value->id.'" data-fileid="'.$value->file_id.'" data-amount="'.$value->amount.'"
                        data-filename="'.$value->filename.'" data-name="'.$value->name.'" data-store="'.$value->branch.'"
                        data-statusid="'.$value->status_id.'" data-month="'.date('Y-m', strtotime($value->month)).'"
                        onclick="showEditForm(this);">'.$value->name.'</a>',
                    'store' => $value->branch,
                    'amount' => $value->amount,
                    'month' =>   $value->month_name,
                    'created_by' => $value->created_by,
                    'updated_by' => $value->updated_by,
                    'updated_at' => date('Y-m-d h:i A', strtotime($value->updated_at)),
                    'status_name' => $value->status_name,
                    'actions' =>  '<div class="d-flex order-actions">
                            <a href="javascript:;" onclick="ShowConfirmDeleteForm(' . $value->id . ',' . $file_id_holder . ', 0)""
                                class="btn-light" style="background-color:#dee2e6"><i class="bx bxs-trash"></i></a>
                            <a href="/store/financial-reports/'.$id.'/payments-overview/download/'.$value->file_id.'"
                                class="btn-light '.$download_hidden.'" style="background-color:#dee2e6"><i class="bx bxs-download"></i></a>
                            <a target="_blank" href="'.$s3Url.'" class="btn-light '.$hidden.'" style="background-color:#dee2e6"><i class="bx bxs-show"></i></a>
                        </div>'
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }


}
