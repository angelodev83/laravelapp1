<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\ClinicalKpi;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KpiController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:menu_store.clinical.kpi.index|menu_store.clinical.kpi.create|menu_store.clinical.kpi.update|menu_store.clinical.kpi.delete');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Clinical', 'KPI'];
            return view('/stores/clinical/kpi/index', compact('breadCrumb'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function date_with(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();
            $prevMonthYear = $request->input('prevMonthYear');
            $prevMonth = $request->input('prevMonth');
            $currentMonthYear = $request->input('currentMonthYear');
            $currentMonth = $request->input('currentMonth');
            $nextMonthYear = $request->input('nextMonthYear');
            $nextMonth = $request->input('nextMonth');

            // Calculate the start and end dates
            $startDate = Carbon::createFromDate($prevMonthYear, $prevMonth, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($nextMonthYear, $nextMonth, 1)->endOfMonth();

            // Fetch ClinicalKpi data between the calculated dates
            $clinicalKpiDates = ClinicalKpi::with('patient')->whereBetween('date', [$startDate, $endDate])->get();

            $modifiedData = [];
            foreach ($clinicalKpiDates as $value) {
                $dFirstname = Crypt::decryptString($value->patient->firstname);
                $dLastname = Crypt::decryptString($value->patient->lastname);
                
                $value->dFirstname = $dFirstname;
                $value->dLastname = $dLastname;
                
                $modifiedData[] = $value;
            }

            // dd(json_encode($modifiedData));
            return response()->json([
                'dates' => $clinicalKpiDates->pluck('date')->toArray(),
                'data' => $modifiedData
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if($request->ajax()){
                $data = $request->all();
                
                $validation = Validator::make($data, [
                    'patient_id' => 'required',
                    'diagnosis' => 'required',
                    'reason' => 'required',
                    'care_goals' => 'required',
                    'biller' => 'required',
                    'profits' => 'required'
                ]);

                if ($validation->passes()){

                    $kpi = new ClinicalKpi();
                    $kpi->date = $data['date'];
                    $kpi->patient_id = $data['patient_id'];
                    $kpi->diagnosis = $data['diagnosis'];
                    $kpi->reason = $data['reason'];
                    $kpi->care_goals = $data['care_goals'];
                    $kpi->store_status_id = $data['status_id'];
                    $kpi->biller = $data['biller'];
                    $kpi->profits = $data['profits'];
                    $kpi->pharmacy_store_id = $data['pharmacy_store_id'];
                    $kpi->user_id = auth()->user()->id;
        
                    $kpi->save();

                    DB::commit();
        
                    return json_encode([
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ]);
                }
                
                else{

                    return json_encode([
                        'status'=>'error',
                        'errors'=> $validation->errors(),
                        'message'=>'Patient saving failed.'
                    ]);
                }
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
                $data = $request->all();
                
                $validation = Validator::make($data, [
                    'patient_id' => 'required',
                    'diagnosis' => 'required',
                    'reason' => 'required',
                    'care_goals' => 'required',
                    'biller' => 'required',
                    'profits' => 'required'
                ]);

                if ($validation->passes()){
                    
                    $kpi = ClinicalKpi::findOrFail($data['id']);
                    $kpi->date = $data['date'];
                    $kpi->patient_id = $data['patient_id'];
                    $kpi->diagnosis = $data['diagnosis'];
                    $kpi->reason = $data['reason'];
                    $kpi->care_goals = $data['care_goals'];
                    $kpi->store_status_id = $data['status_id'];
                    $kpi->biller = $data['biller'];
                    $kpi->profits = $data['profits'];
                    $kpi->pharmacy_store_id = $data['pharmacy_store_id'];
                    $kpi->user_id = auth()->user()->id;
        
                    $kpi->save();

                    DB::commit();
        
                    return json_encode([
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ]);
                }
                
                else{

                    return json_encode([
                        'status'=>'error',
                        'errors'=> $validation->errors(),
                        'message'=>'Patient saving failed.'
                    ]);
                }
            }
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in KpiController.update.'
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();
            if($request->ajax()){
            
                $deposit = ClinicalKpi::findOrFail($request->id);
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
            $dateFilter = $request->date_filter;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // get data from products table
            $query = ClinicalKpi::with('user.employee', 'patient')
                ->where('pharmacy_store_id', $request->pharmacy_store_id)
                ->where('date', $dateFilter);
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
                $query->orWhereHas('patient', function ($query) use ($search) {
                        $encryptedQuery = [];
                        if(!empty($search)) {
                            $encryptedQuery = Patient::where('source', 'pioneer')->get()->filter(function ($encryptedQuery) use ($search) {
                                // return stristr($encryptedQuery->getDecryptedFirstname(), trim($search)) !== false
                                //     || stristr($encryptedQuery->getDecryptedLastname(), trim($search)) !== false;
                                $fullName = $encryptedQuery->getDecryptedFirstname() . ' ' . $encryptedQuery->getDecryptedLastname();
                                $revFullName = $encryptedQuery->getDecryptedLastname() . ' ' . $encryptedQuery->getDecryptedFirstname();
                                return stristr($fullName, trim($search)) !== false
                                    || stristr($revFullName,trim($search)) !== false;
                            })->pluck('id');
                        }
                        
                        if(!empty($encryptedQuery)) {  
                            $query->whereIn('id',$encryptedQuery);
                        }
                    });
            });

            $orderByCol =  $columns[$orderColumnIndex]['name'];

            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();
            
            $newData = [];
            foreach ($data as $value) {
                $dFirstname = Crypt::decryptString($value->patient->firstname);
                $dLastname = Crypt::decryptString($value->patient->lastname);
                
                $value->dFirstname = $dFirstname;
                $value->dLastname = $dLastname;

                $actions = '<div class="d-flex order-actions">';
                if(auth()->user()->can('menu_store.clinical.kpi.update')) {
                    $actions .= '
                        <button class="btn btn-sm btn-primary me-1" 
                            id="data-edit-btn-'.$value->id.'"
                            data-id="'.$value->id.'" 
                            data-array="'.htmlspecialchars(json_encode($value)).'"
                            onclick="showEditForm('.$value->id.');"><i class="fa fa-pencil"></i>
                        </button>';
                }
                if(auth()->user()->can('menu_store.clinical.kpi.delete')) {
                    $actions .= '<button class="btn btn-sm btn-danger" onclick="ShowConfirmDeleteForm(' . $value->id . ')"><i class="fa fa-trash-can"></i></button>';
                }             
                $actions .= '</div>';
                
                $newData[] = [
                    'id' => $value->id,
                    'date' => !empty($value->date) ? date('Y-m-d', strtotime($value->date)) : '',
                    'patient_name' => Crypt::decryptString($value->patient->firstname).' '.Crypt::decryptString($value->patient->lastname),
                    'created_by' => $value->user->employee->firstname.' '.$value->user->employee->lastname,
                    'created_at' => date('Y-m-d h:i A', strtotime($value->created_at)),
                    'formatted_created_at' => date('M d, Y H:i A', strtotime($value->created_at)),
                    'actions' =>  $actions
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }
}
