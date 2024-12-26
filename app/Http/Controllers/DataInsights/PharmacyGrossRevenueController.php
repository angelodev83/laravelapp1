<?php

namespace App\Http\Controllers\DataInsights;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\MonthlyRevenue;
use App\Models\PharmacyStore;
use App\Traits\HistoryTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class PharmacyGrossRevenueController extends Controller
{
    public function index($id)
    {
        try{
            $this->checkStorePermission($id);
            
            $user = Auth::user();

            $breadCrumb = ['Data Insights', 'Pharmacy Gross Revenue'];
            
            return view('/stores/dataInsights/pgr/index', compact('user','breadCrumb'));
        }
        catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function get_stores(Request $request)
    {
        if($request->ajax()){
           
           $data = PharmacyStore::select('id','code')->get()->toArray();


            return json_encode([
                'data'=> $data,
            ], 200);
        }
    }

    public function get_months(Request $request)
    {
        if($request->ajax()){
           $data = explode(',', env('MONTHLY_REVENUE_MONTHS'));

            return json_encode([
                'data'=> $data,
            ]);
        }
    }

    public function store(Request $request)
    {   

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
                
                $path = 'financials/monthlyRevenue/';
                
                // Provide a dynamic path or use a specific directory in your S3 bucket
                $path_file = 'financials/monthlyRevenue/'  . $newFileName;

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

                $m_revenue = new MonthlyRevenue();
                $m_revenue->name = $fileName;
                $m_revenue->store = $request['store_select'];
                $m_revenue->month = $request['month']."-01";
                $m_revenue->amount = $request['amount'];
                $m_revenue->created_by = Auth::id();
                $m_revenue->updated_by = Auth::id();
                $m_revenue->file_id = $file_id;
                $m_revenue->save();
        
                return response()->json([
                    // 'file' => $file_id.' with '.$s3url ,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ], 201);

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
                'message'=>'Check input fields.'
            ], 422);
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

    public function update(Request $request)
    {
        if($request->ajax()){
            $input = $request->all();
            
            $old_m_revenue = MonthlyRevenue::where('id', $input['id'])->first();

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
                        
                        $path = 'financials/monthlyRevenue/';
                        
                        // Provide a dynamic path or use a specific directory in your S3 bucket
                        $path_file = 'financials/monthlyRevenue/'  . $newFileName;

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
                        $nm_revenue = MonthlyRevenue::where('id', $input['id'])->first();
                        $nm_revenue->name = $input['name'];
                        $nm_revenue->store = $input['store_select'];
                        $nm_revenue->month = $input['month'].'-01';
                        $nm_revenue->amount = $input['amount'];
                        $nm_revenue->file_id = $file_id;
                        $nm_revenue->updated_by = Auth::id();
                        $nm_revenue->save();

                        

                        
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
                        'message'=>'Check input fields.'
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
                    $m_revenue = MonthlyRevenue::where('id', $input['id'])->first();
                    $m_revenue->name = $input['name'];
                    $m_revenue->store = $input['store_select'];
                    $m_revenue->month = $input['month'].'-01';
                    $m_revenue->amount = $input['amount'];
                    $m_revenue->updated_by = Auth::id();
                    $m_revenue->save();

                    
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
            $file = MonthlyRevenue::leftJoin('files', 'files.id', '=', 'monthly_revenues.file_id')
                ->select(
                    'monthly_revenues.id AS id',
                    'files.id AS file_id',
                    'files.filename',
                    'files.path',
                    'files.mime_type',
                    'monthly_revenues.name',
                    'monthly_revenues.amount',
                    DB::raw('MONTHNAME(monthly_revenues.month) AS month_name'),
                    'monthly_revenues.store',
                    'monthly_revenues.month AS month',
                    'monthly_revenues.created_by AS created_by_id',
                    'monthly_revenues.updated_by AS updated_by_id',
                    'monthly_revenues.updated_at',
                    DB::raw('(SELECT name FROM users WHERE id = monthly_revenues.created_by) AS created_by'),
                    DB::raw('(SELECT name FROM users WHERE id = monthly_revenues.updated_by) AS updated_by')
                )->where('monthly_revenues.id', $id)->get();
            $path = $file[0]->path.$file[0]->filename;

            if($path != ''){
                if(Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }

                $file_data = File::where('id', $file[0]->file_id)->first();
                $file_data->delete();

                
                
            }

            if($input['delete_file_only'] == 0){
                $m_revenue = MonthlyRevenue::where('id', $id)->first();
                $m_revenue->delete();

            }

            return json_encode(['status'=>'success','message'=>'Record has been deleted.'] ,200);
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
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // get data from products table
            $query = MonthlyRevenue::leftJoin('files', 'files.id', '=', 'monthly_revenues.file_id')
                ->select(
                    'monthly_revenues.id AS id',
                    'files.id AS file_id',
                    'files.filename',
                    'files.path',
                    'files.mime_type',
                    'monthly_revenues.name',
                    'monthly_revenues.amount',
                    DB::raw('MONTHNAME(monthly_revenues.month) AS month_name'),
                    'monthly_revenues.store',
                    'monthly_revenues.month AS month',
                    'monthly_revenues.created_by AS created_by_id',
                    'monthly_revenues.updated_by AS updated_by_id',
                    'monthly_revenues.updated_at',
                    DB::raw('(SELECT name FROM users WHERE id = monthly_revenues.created_by) AS created_by'),
                    DB::raw('(SELECT name FROM users WHERE id = monthly_revenues.updated_by) AS updated_by')
                )->where('monthly_revenues.store', $id);

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
                $query->whereBetween('monthly_revenues.month', [date("Y-m-d", strtotime($from)),date("Y-m-d", strtotime($to))]);   
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
                    'name' => '<a href="javascript:;" data-id="'.$value->id.'" data-fileid="'.$value->file_id.'" data-month="'.date('Y-m', strtotime($value->month)).'"
                        data-amount="'.$value->amount.'"
                        data-filename="'.$value->filename.'" data-name="'.$value->name.'" data-storeid="'.$value->store.'"
                        onclick="showEditForm(this);">'.$value->name.'</a>',
                    'store' => $value->store,
                    'month' => $value->month_name,
                    'amount' => $value->amount,
                    'created_by' => $value->created_by,
                    'created_at' => date('M d, Y g:i A', strtotime($value->pst_created_at)),
                    'updated_by' => $value->updated_by,
                    'updated_at' => $value->updated_at,
                    'actions' =>  '<div class="d-flex order-actions">
                            <a href="javascript:;" onclick="ShowConfirmDeleteForm(' . $value->id . ',' . $file_id_holder . ', 0)""
                                class="btn-light" style="background-color:#dee2e6"><i class="bx bxs-trash"></i></a>
                            <a href="/store/financial-reports/'.$id.'/pgr/download/'.$value->file_id.'"
                                class="btn-light '.$download_hidden.'" style="background-color:#dee2e6"><i class="bx bxs-download"></i></a>
                            <a target="_blank" href="'.$s3Url.'" class="btn-light '.$hidden.'" style="background-color:#dee2e6"><i class="bx bxs-show"></i></a>
                        </div>'
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }
}
