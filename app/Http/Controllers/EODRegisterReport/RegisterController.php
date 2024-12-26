<?php

namespace App\Http\Controllers\EODRegisterReport;

use App\Http\Controllers\Controller;
use App\Models\EodCash;
use App\Models\EodCashFile;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{   
    private $eod_cash;

    public function __construct(EodCash $eod_cash)
    {
        $this->eod_cash = $eod_cash;

        $this->middleware('permission:menu_store.eod_register_report.register.index|menu_store.eod_register_report.register.create|menu_store.eod_register_report.register.update|menu_store.eod_register_report.register.delete');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['EOD Register Report', 'Register'];
            return view('/stores/eodRegisterReport/register/index', compact('breadCrumb'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    private function saveFiles($file, $id, $pharmacy_store_id)
    {   
        $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $fileExtension = $file->getClientOriginalExtension();
        $mime_type = $file->getMimeType();
        
        $newFileName = date("Ymdhis").Auth::id() .'_'. $fileName  . '.' . $fileExtension;
        $doc_type = $fileExtension;
        
        $path = 'eod/register/'.$pharmacy_store_id.'/files/'.$id.'/';
        
        // Provide a dynamic path or use a specific directory in your S3 bucket
        $path_file = $path . $newFileName;

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

        $eodCashFile = new EodCashFile();
        $eodCashFile->eod_cash_id = $id;
        $eodCashFile->file_id = $document->id;
        $eodCashFile->save();
    }

    public function fileUpload(Request $request)
    {
        if($request->ajax()){
            $file_flag = false;
            $input = $request->all();
            //$inputFile = $input['file'];
            
            $fileValidation = Validator::make($input, [
                'file.*' => 'mimes:pdf,png,jpeg',
            ]);
            
            $input = json_decode($input['data'], true);

            if ($fileValidation->passes()){
                if ($request->file('file')) {
                    $files = $request->file('file');

                    foreach($files as $file) { 
                        $this->saveFiles($file, $input['id'], $input['menu_store_id']);
                    }
                }

                return json_encode([
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
            else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $fileValidation->errors()->merge($validation->errors()),
                    'message'=>'Record saving failed.'
                ]);
            }

        }
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            $file_flag = false;
            $input = $request->all();
            //$inputFile = $input['file'];
            
            $fileValidation = Validator::make($input, [
                'file.*' => 'mimes:pdf,png,jpeg',
            ]);
            
            $input = json_decode($input['data'], true);
            
            $validation = Validator::make($input, [
                'date' => 'required',
                'total_cash_received' => 'numeric|decimal:0,2',
                'total_cash_deposited_to_bank' => 'numeric|decimal:0,2',
                'total_check_received' => 'numeric|decimal:0,2',
                // 'register_number' => 'required'
            ]);

            if ($fileValidation->passes() && $validation->passes()){
                $eod = new EodCash();
                $eod->date = $input['date'];
                $eod->register_number = ($input['register_number'] == '')?null:$input['register_number'];
                $eod->register_page_id = ($input['register_page_id'] == '')?null:$input['register_page_id'];
                $eod->total_cash_received = ($input['total_cash_received'] == '')?0:$input['total_cash_received'];
                $eod->total_cash_deposited_to_bank = ($input['total_cash_deposited_to_bank'] == '')?0:$input['total_cash_deposited_to_bank'];
                $eod->total_check_received = ($input['total_check_received'] == '')?0:$input['total_check_received'];
                $eod->pharmacy_store_id = $input['menu_store_id'];
                $eod->user_id = auth()->user()->id;
                $eod->save();

                if ($request->file('file')) {
                    $files = $request->file('file');

                    foreach($files as $file) { 
                        $this->saveFiles($file, $eod->id, $input['menu_store_id']);
                    }
                }

                return json_encode([
                    'status'=>'success',
                    'message'=>'Record has been saved.']);
            }
            else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $fileValidation->errors()->merge($validation->errors()),
                    'message'=>'Record saving failed.'
                ]);
            }

        }
    }

    public function update(Request $request)
    {
        if($request->ajax()){
            
            $input = $request->all();
            
            $dataArray = json_decode($input['data'], true);
            
            $validation = Validator::make($dataArray, [
                'date' => 'required',
                'total_cash_received' => 'numeric|decimal:0,2',
                'total_cash_deposited_to_bank' => 'numeric|decimal:0,2',
                'total_check_received' => 'numeric|decimal:0,2',
                // 'register_number' => 'required',
            ]);

            $fileValidation = Validator::make($input, [
                'file' => 'mimes:pdf,png,jpeg',
            ]);
            
            if ($fileValidation->passes() && $validation->passes()){

                if ($request->file('file')) {
                    $file = $request->file('file');

                    $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $fileExtension = $file->getClientOriginalExtension();
                    $mime_type = $file->getMimeType();
                    
                    $newFileName = date("Ymdhis").Auth::id() .'_'. $fileName  . '.' . $fileExtension;
                    $doc_type = $fileExtension;
                    
                    $path = 'eod/register/';
                    
                    // Provide a dynamic path or use a specific directory in your S3 bucket
                    $path_file = 'eod/register/'. $newFileName;

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


                    $edoFile = EodCash::where('id', $dataArray['id'])->first();
                    $edoFile->file_id = $document->id;
                    $edoFile->save();
                }

                $eod = EodCash::where('id', $dataArray['id'])->first();
                $eod->date = $dataArray['date'];
                $eod->register_number = ($dataArray['register_number'] == '')?0:$dataArray['register_number'];
                $eod->register_page_id = ($dataArray['register_page_id'] == '')?0:$dataArray['register_page_id'];
                $eod->total_cash_received = ($dataArray['total_cash_received'] == '')?0:$dataArray['total_cash_received'];
                $eod->total_cash_deposited_to_bank = ($dataArray['total_cash_deposited_to_bank'] == '')?0:$dataArray['total_cash_deposited_to_bank'];
                $eod->total_check_received = ($dataArray['total_check_received'] == '')?0:$dataArray['total_check_received'];
                $eod->pharmacy_store_id = $dataArray['pharmacy_store_id'];
                $eod->user_id = auth()->user()->id;
                $eod->save();
                

                return json_encode([
                    'file_id' => $eod->file_id,
                    'status'=> 'success',
                    'message'=> 'Successfully updated.'
                ]);
            } else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $fileValidation->errors()->merge($validation->errors()),
                    'message'=>'Record saving failed.'
                ]);
            }

        }
    } 

    public function delete_file(Request $request)
    {
        if($request->ajax()){

            $input = $request->all();
            $id = $input['id'];
            
            $file = File::where('id', $id)->first();
            $file_id = $file->id;
            $path = $file->path.$file->filename;

            if($path != ''){
                if(Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }

                $file->delete();   
            }

            $eodFile = EodCashFile::where('file_id', $file_id)->first();
            $eodFile->delete();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            $input = $request->all();

            $id = $input['id'];
            $eod = EodCash::where('id', $id)->first();
            if ($eod) {
                $eodFiles = EodCashFile::where('eod_cash_id', $eod->id)->get();

                foreach ($eodFiles as $eodFile) {
                    // Assuming 'file_id' is a column in 'EodCashFile' that references 'id' in 'File'
                    $file = File::find($eodFile->file_id);

                    if ($file) {
                        $path = $file->path . $file->filename;

                        // Check if the file exists in S3 and delete it
                        if (Storage::disk('s3')->exists($path)) {
                            Storage::disk('s3')->delete($path);
                        }

                        // Delete the file record from database
                        $file->delete();
                        $eodFile->delete();
                    }
                }

                // Delete the EOD record if no longer needed
                $eod->delete();
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
            // $query = DB::table('view_inmars')->where('pharmacy_store_id', $id)->groupBy('name');
            $query = $this->eod_cash->with('file', 'user.employee')->where('pharmacy_store_id', $id);

            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }  
                }   
                $query->orWhereHas('user.employee', function ($query) use ($search) {
                        $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $search . '%']);
                    });
            });

            $orderByCol =  $columns[$orderColumnIndex]['name'];

            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();
            
            $newData = [];
            foreach ($data as $value) {
                

                $actions = '<div class="d-flex order-actions">';
                // if(Auth::user()->can('menu_store.eod_register_report.register.index')) {
                //     $actions .= '<a title="View" href="javascript:void(0)" data-id="'.$value->id.'" 
                //                 data-array="'.htmlspecialchars(json_encode($value)).'" class="me-1"
                //                 id="inmar-show-btn-'.$value->id.'"><button class="btn btn-sm btn-primary" onclick="showViewForm('.$value->id.');"><i class="fa fa-eye"></i></button></a>';
                // }
                if(Auth::user()->can('menu_store.eod_register_report.register.update')) {
                    $actions .= '<a title="Files" href="javascript:void(0)" data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"  class="me-1"
                                id="data-file-btn-'.$value->id.'"><button class="btn btn-sm btn-secondary" onclick="showFileModal('.$value->id.');"><i style="padding-left: 2px; padding-right: 2px;" class="fa-solid fa-file"></i></button></a>';
                } 
                if(Auth::user()->can('menu_store.eod_register_report.register.update')) {
                    $actions .= '<a title="Edit" href="javascript:void(0)" data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"  class="me-1"
                                id="data-show-btn-'.$value->id.'"><button class="btn btn-sm btn-primary" onclick="showEditForm('.$value->id.');"><i class="fa fa-pencil"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.eod_register_report.register.delete')) {
                    $actions .= '<a title="Delete" href="javascript:void(0)" class="me-1"><button class="btn btn-sm btn-danger" onclick="ShowConfirmDeleteForm(' . $value->id . ')"><i class="fa fa-trash-can"></i></button></a>';
                }             
                $actions .= '</div>';
                
                $newData[] = [
                    'id' => $value->id,
                    'date' => $value->date,
                    'total_cash_received' => $value->total_cash_received,
                    'total_cash_deposited_to_bank' => $value->total_cash_deposited_to_bank,
                    'total_check_received' => $value->total_check_received,
                    'user' => $value->user->employee->firstname.' '.$value->user->employee->lastname,
                    'register_number' => ($value->register_number == null)?'':$value->register_number,
                    //'created_at' => $value->created_at->format('M d, Y h:iA'),
                    'actions' =>  $actions
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }

    public function get_fileData(Request $request, $id)
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
            $query = EodCashFile::with('file')->where('eod_cash_id', $request->id);

            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }  
                }   
                // $query->orWhereHas('file', function ($query) use ($search) {
                //         $query->whereRaw("filename LIKE ?", ['%' . $search . '%']);
                //     });
                $query->orWhereHas('file', function ($query) use ($search) {
                    $query->where("filename", 'like', '%' . $search . '%');
                });
            });


            $orderByCol =  $columns[$orderColumnIndex]['name'];

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

                $actions = '<div class="d-flex order-actions">';
                if(Auth::user()->can('menu_store.eod_register_report.register.download')) {
                    $actions .= '<a class="'.$download_hidden.' me-1" href="/admin/file/download/'.$value->file_id.'" title="Download File"><button class="btn btn-sm btn-secondary"><i class="fa fa-download"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.eod_register_report.register.pdfview')) {
                    $actions .= '<a target="_blank" href="'.$s3Url.'" class="'.$hidden.' me-1"
                    title="View PDF"><button class="btn btn-sm btn-secondary"><i class="fa-regular fa-file-pdf"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.eod_register_report.register.delete')) {
                    $actions .= '<a title="Delete" href="javascript:void(0)" class="me-1"><button class="btn btn-sm btn-danger" onclick="ShowDeleteFile(' . $value->file_id . ')"><i class="fa fa-trash-can"></i></button></a>';
                }
                
                $actions .= '</div>';
                
                $newData[] = [
                    'id' => $value->file_id,
                    'name' => $value->file->filename,
                    'actions' =>  $actions
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }
}
