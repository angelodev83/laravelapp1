<?php

namespace App\Http\Controllers\EODRegisterReport;

use App\Http\Controllers\Controller;
use App\Models\EodDeposit;
use Barryvdh\DomPDF\PDF;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

ini_set('max_execution_time', '3600');

class DepositController extends Controller
{
    private $pdf;

    public function __construct(PDF $pdf)
    {
        $this->pdf = $pdf;
        $this->middleware('permission:menu_store.eod_register_report.deposit.index|menu_store.eod_register_report.deposit.create|menu_store.eod_register_report.deposit.update|menu_store.eod_register_report.deposit.delete');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['EOD Register Report', 'Deposit'];
            return view('/stores/eodRegisterReport/deposit/index', compact('breadCrumb'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function pdf($id, Request $request)
    {
        try {
            $data = EodDeposit::where('id',$id)->first();
            if(!isset($data->id) || empty($request->id)) {
                $data = [
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'amount' => $request->amount,
                    'date' => $request->date, //date('Y-m-d', strtotime($request->date)),
                    'time' => date('H:i:s', strtotime($request->time)),
                    'signature' => $request->signature
                ];
                $id = uniqid();
            } else {
                $data = $data->toArray();
            }
            $pdf = $this->pdf->loadView('stores/eodRegisterReport/deposit/pdf/index', $data);

            $filename = 'deposit-'.$id.'.pdf';

            $path = 'upload/temp/'.$filename;

            $directoryPath = public_path('upload/temp');

            if (!File::exists($directoryPath)) {
                File::makeDirectory($directoryPath, 0755, true);
            }

            $filePath = public_path($path);
            $pdf->save($filePath);

            $pdfPath = asset($path);

            return response()->json(['pdf_url' => asset($pdfPath)]);
            
        } catch (Exception $e) {

            $this->download($id);

            return response()->json([
                'error' => $e->getMessage(),
                'message' => $e->getMessage()
            ]);
        }
        
    }

    public function download($id)
    {
        $data = EodDeposit::findOrFail($id)->toArray();
        $pdf = $this->pdf->loadView('stores/eodRegisterReport/deposit/pdf/index', $data);
        return $pdf->download('deposit-'.$id.'.pdf');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if($request->ajax()){
                 
                $data = json_decode($request->data, true);
                
                $deposit = new EodDeposit();
                $deposit->firstname = $data['firstname'];
                $deposit->lastname = $data['lastname'];
                $deposit->amount = $data['amount'];
                $deposit->date = date('Y-m-d', strtotime($data['date']));
                $deposit->time = date('H:i:s', strtotime($data['time']));
                $deposit->pharmacy_store_id = $data['pharmacy_store_id'];
                $deposit->signature = $request->input('signature');
                $deposit->user_id = auth()->user()->id;
    
                $deposit->save();

                DB::commit();
    
                return json_encode([
                    'data' => $deposit,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in DepositController.store.'
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            if($request->ajax()){
            
                $deposit = EodDeposit::findOrFail($request->id);
                if(isset($deposit->id)) {
                    $deposit->firstname = $request->firstname;
                    $deposit->lastname = $request->lastname;
                    $deposit->amount = $request->amount;
                    $deposit->date = date('Y-m-d', strtotime($request->date));
                    $deposit->time = date('H:i:s', strtotime($request->time));
                    $deposit->pharmacy_store_id = $request->pharmacy_store_id;
                    if($request->has('signature')) {
                        $deposit->signature = $request->signature;
                    }
                    $deposit->save();
                }

                DB::commit();
    
                return json_encode([
                    'data' => $deposit,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in DepositController.update.'
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();
            if($request->ajax()){
            
                $deposit = EodDeposit::findOrFail($request->id);
                $deposit->delete();

                DB::commit();
    
                return json_encode([
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
            }
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in DepositController.delete.'
            ]);
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

            // get data from products table
            $query = EodDeposit::with('user.employee')->where('pharmacy_store_id', $request->pharmacy_store_id);

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
                $actions .= '<button class="btn btn-sm btn-outline-danger me-1"
                        title="Generate PDF"
                        onclick="generatePDF('.$value->id.');"><i class="fa fa-file-pdf"></i>
                    </button>';
                $actions .= '<button class="btn btn-sm btn-primary print-btn me-1"
                    title="Download"
                    onclick="downloadPDF('.$value->id.');"><i class="fa fa-download"></i>
                </button>';
                if(auth()->user()->can('menu_store.eod_register_report.deposit.update')) {
                    $actions .= '
                        <button class="btn btn-sm btn-primary me-1" 
                            id="data-show-btn-'.$value->id.'"
                            data-id="'.$value->id.'" 
                            data-array="'.htmlspecialchars(json_encode($value)).'"
                            onclick="showEditForm('.$value->id.');"><i class="fa fa-pencil"></i>
                        </button>';
                }
                if(auth()->user()->can('menu_store.eod_register_report.deposit.delete')) {
                    $actions .= '<button class="btn btn-sm btn-danger" onclick="ShowConfirmDeleteForm(' . $value->id . ')"><i class="fa fa-trash-can"></i></button>';
                }             
                $actions .= '</div>';
                
                $newData[] = [
                    'id' => $value->id,
                    'date' => $value->date,
                    'formatted_date' => !empty($value->date) ? date('M d, Y', strtotime($value->date)) : '',
                    'time' => $value->time,
                    'formatted_time' => !empty($value->time) ? date('H:i A', strtotime($value->time)) : '',
                    'firstname' => $value->firstname,
                    'lastname' => $value->lastname,
                    'amount' => $value->amount,
                    'formatted_amount' => number_format($value->amount, 2),
                    'created_by' => $value->user->employee->firstname.' '.$value->user->employee->lastname,
                    'created_at' => $value->created_at,
                    'formatted_created_at' => date('M d, Y g:i A', strtotime($value->pst_created_at)),
                    'actions' =>  $actions
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }
}
