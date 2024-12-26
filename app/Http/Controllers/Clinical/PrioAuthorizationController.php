<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\ClinicalDiagnosis;
use App\Models\ClinicalPrioAuthorization;
use App\Models\Patient;
use App\Models\StoreStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PrioAuthorizationController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:menu_store.clinical.prio_authorization.index|menu_store.clinical.prio_authorization.create|menu_store.clinical.prio_authorization.update|menu_store.clinical.prio_authorization.delete');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Clinical', 'Prio Authorization'];
            return view('/stores/clinical/prioAuthorization/index', compact('breadCrumb'));
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
            $clinicalKpiDates = ClinicalPrioAuthorization::with('patient')->whereBetween('date', [$startDate, $endDate])->get();

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
                    // 'employee_id' => 'required',
                    // 'reason' => 'required',
                    // 'soap' => 'required',
                ]);

                if ($validation->passes()){

                    $co = new ClinicalPrioAuthorization();
                    $co->date = $data['date'];
                    $co->patient_id = $data['patient_id'];
                    $co->in_charge = $data['employee_id'];
                    $co->reason = $data['reason'];
                    $co->soap = $data['soap'];
                    $co->ses_adrs = $data['ses_adrs'];
                    $co->store_call_status_id = $data['status_id'];
                    $co->store_provider_status_id = $data['provider_id'];
                    ($data['time_start'])??$co->time_start = $data['time_start'];
                    ($data['time_end'])??$co->time_end = $data['time_end'];
                    ($data['total_time'])??$co->total_time = $data['total_time'];
                    
                    $co->pharmacy_store_id = $data['pharmacy_store_id'];
                    $co->user_id = auth()->user()->id;
        
                    $co->save();

                    ClinicalDiagnosis::where('parent_id', $co->id)->delete();
                    if(count($data['diagnosis']) > 0){
                        foreach ($data['diagnosis'] as $diagnosis) {
                            $diagnoses = new ClinicalDiagnosis();
                            $diagnoses->store_status_id = $diagnosis;
                            $diagnoses->parent_id = $co->id;
                            $diagnoses->type = 'prio-authorization';
                            $diagnoses->save();
                        }
                    }
        

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
                'message' => 'Something went wrong in PrioAuthorizationController.store.'
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();
            if($request->ajax()){
            
                $co = ClinicalPrioAuthorization::findOrFail($request->id);

                ClinicalDiagnosis::where('parent_id', $co->id)->delete();

                $co->delete();

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

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            if($request->ajax()){
                $data = $request->all();
                
                $validation = Validator::make($data, [
                    'patient_id' => 'required',
                    'employee_id' => 'required',
                    'diagnosis' => 'required',
                    'reason' => 'required',
                    'soap' => 'required',
                ]);

                if ($validation->passes()){
                    
                    $co = ClinicalPrioAuthorization::findOrFail($data['id']);
                    $co->date = $data['date'];
                    $co->patient_id = $data['patient_id'];
                    $co->in_charge = $data['employee_id'];
                    $co->reason = $data['reason'];
                    $co->soap = $data['soap'];
                    $co->ses_adrs = $data['ses_adrs'];
                    $co->store_call_status_id = $data['status_id'];
                    $co->store_provider_status_id = $data['provider_id'];
                    $co->time_start = $data['time_start'];
                    $co->time_end = $data['time_end'];
                    $co->total_time = $data['total_time'];
                    
                    $co->pharmacy_store_id = $data['pharmacy_store_id'];
                    // $co->user_id = auth()->user()->id;
        
                    $co->save();
                    ClinicalDiagnosis::where('parent_id', $co->id)->delete();
                    if(count($data['diagnosis']) > 0){
                        foreach ($data['diagnosis'] as $diagnosis) {
                            // $checkDiagnosis = ClinicalDiagnosis::where('parent_id', $co->id)->where('store_status_id', $diagnosis)->exists();
                            
                            $diagnoses = new ClinicalDiagnosis();
                            $diagnoses->store_status_id = $diagnosis;
                            $diagnoses->parent_id = $co->id;
                            $diagnoses->type = 'prio-authorization';
                            $diagnoses->save();
                        
                        }
                    }

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
                'message' => 'Something went wrong in PrioAuthorizationController.update.'
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
            $dateFilter = null;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // get data from products table
            $query = ClinicalPrioAuthorization::with('user.employee', 'inCharge', 'patient', 'diagnoses.status', 'callStatus', 'providerStatus')
                ->where('pharmacy_store_id', $request->pharmacy_store_id);
            
            if($request->has('date_filter')) {
                $dateFilter = $request->date_filter;

                $query = $query->where('date', $dateFilter);
            }
                
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
                $query->orWhereHas('diagnoses.status', function($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
                $query->orWhereHas('callStatus', function($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
                $query->orWhereHas('providerStatus', function($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
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
                // Storage::disk('local')->append('file.txt', json_encode($value->diagnoses));
                $diagnosesByStatusName = [];
                foreach ($value->diagnoses as $diagnosis) {
                    array_push($diagnosesByStatusName, $diagnosis->status->name);
                }
                
                $dFirstname = Crypt::decryptString($value->patient->firstname);
                $dLastname = Crypt::decryptString($value->patient->lastname);
                
                $value->dFirstname = $dFirstname;
                $value->dLastname = $dLastname;

                $actions = '<div class="d-flex order-actions">';
                if(auth()->user()->can('menu_store.clinical.prio_authorization.update')) {
                    $actions .= '
                        <button class="btn btn-sm btn-primary me-1" 
                            id="data-edit-btn-'.$value->id.'"
                            data-id="'.$value->id.'" 
                            data-array="'.htmlspecialchars(json_encode($value)).'"
                            onclick="showEditForm('.$value->id.');"><i class="fa fa-pencil"></i>
                        </button>';
                }
                if(auth()->user()->can('menu_store.clinical.prio_authorization.delete')) {
                    $actions .= '<button class="btn btn-sm btn-danger" onclick="ShowConfirmDeleteForm(' . $value->id . ')"><i class="fa fa-trash-can"></i></button>';
                }             
                $actions .= '</div>';
                
                $newData[] = [
                    'id' => $value->id,
                    'date' => !empty($value->date) ? date('Y-m-d', strtotime($value->date)) : '',
                    'patient_name' => Crypt::decryptString($value->patient->firstname).' '.Crypt::decryptString($value->patient->lastname),
                    'diagnosis' => $diagnosesByStatusName,
                    'created_by' => $value->user->employee->firstname.' '.$value->user->employee->lastname,
                    'created_at' => $value->created_at,
                    'formatted_created_at' => date('M d, Y g:i A', strtotime($value->pst_created_at)),
                    'actions' =>  $actions
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    public function data_schedule(Request $request)
    {
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;
            $p_id = $request->p_id;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // get data from products table
            $query = ClinicalPrioAuthorization::with('user.employee')
                ->where('pharmacy_store_id', $request->pharmacy_store_id)
                ->where('patient_id', $p_id);
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
                // $actions = '<div class="d-flex order-actions">';
                // if(auth()->user()->can('menu_store.clinical.prio_authorization.update')) {
                //     $actions .= '
                //         <button class="btn btn-sm btn-primary me-1" 
                //             id="data-edit-btn-'.$value->id.'"
                //             data-id="'.$value->id.'" 
                //             data-array="'.htmlspecialchars(json_encode($value)).'"
                //             onclick="showEditForm('.$value->id.');"><i class="fa fa-pencil"></i>
                //         </button>';
                // }
                // if(auth()->user()->can('menu_store.clinical.prio_authorization.delete')) {
                //     $actions .= '<button class="btn btn-sm btn-danger" onclick="ShowConfirmDeleteForm(' . $value->id . ')"><i class="fa fa-trash-can"></i></button>';
                // }             
                // $actions .= '</div>';
                
                $newData[] = [
                    'id' => $value->id,
                    'date' => !empty($value->date) ? date('Y-m-d', strtotime($value->date)) : '',
                    'created_by' => $value->user->employee->firstname.' '.$value->user->employee->lastname,
                    'created_at' => date('Y-m-d h:i A', strtotime($value->created_at)),
                    'formatted_created_at' => date('M d, Y H:i A', strtotime($value->created_at)),
                    // 'actions' =>  $actions
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    public function store_schedule(Request $request)
    {
        try {
            DB::beginTransaction();
            if($request->ajax()){
                $data = $request->all();

                    $co = new ClinicalPrioAuthorization();
                    $co->date = $data['date'];
                    $co->patient_id = $data['patient_id'];
                    $co->pharmacy_store_id = $data['pharmacy_store_id'];
                    $co->user_id = auth()->user()->id;
        
                    $co->save();
        

                    DB::commit();
        
                    return json_encode([
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ]);

            }
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in PrioAuthorizationController.store.'
            ]);
        }
    }

    public function searchDiagnosis(Request $request)
    {
        $data = new StoreStatus();
        // if($request->has('category')) {
            $data = $data->where('category', 'diagnosis');
        // }
        $data = $data->orderBy('sort', 'asc')->get();
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }
}
