<?php

namespace App\Http\Controllers\Clinical;

use App\Exports\ClinicalBridgedPatientCustomExport;
use App\Http\Controllers\Controller;
use App\Imports\ClinicalBridgedPatientImport;
use App\Jobs\NotifyClinicalBridgedPatientToCustom;
use App\Mail\ClinicalBridgedPatientMail;
use App\Models\ClinicalDiagnosis;
use App\Models\ClinicalBridgedPatient;
use App\Models\Patient;
use App\Models\User;
use Aws\Api\Validator;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class BridgedPatientController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:menu_store.clinical.bridged_patients.index|menu_store.clinical.bridged_patients.create|menu_store.clinical.bridged_patients.update|menu_store.clinical.bridged_patients.delete');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Clinical', 'Bridged Patients'];
            return view('/stores/clinical/bridgedPatients/index', compact('breadCrumb'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function import($id, Request $request)
    {
        try {
            $data = $request->data ? json_decode($request->data) : [];
            $params = [
                'pharmacy_store_id' => $id,
                'date' => $data->date,
                'user_id' => auth()->user()->id
            ];

            $file = $request->file('upload_file');
            $ext = $file->getClientOriginalExtension();
            $current = file_get_contents($file);
            $file_name = "bridged_patients.".$ext;
            $save_name = str_replace('\\', '/' , storage_path())."/$file_name";
        
            file_put_contents($save_name, $current);

            $absolute_path = str_replace('\\', '/' , storage_path());

            $filePath = $absolute_path.'/'.$file_name;

            Excel::import(new ClinicalBridgedPatientImport($params), $request->file('upload_file'));

            $response = [
                'data' => $params,
                'status'=>'success',
                'message'=>'Record has been imported.'
            ];

            if($request->ajax()){
                return json_encode($response);
            }
            return $response;
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in BridgedPatientController.import.'
            ]);
        }
    }

    public function dateWith($id, Request $request)
    {
        try {
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
                $clinicalKpiDates = ClinicalBridgedPatient::with('patient')
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('pharmacy_store_id', $id)
                    ->get();

                $modifiedData = [];
                foreach ($clinicalKpiDates as $value) {
                    $dFirstname = $value->patient ? Crypt::decryptString($value->patient->firstname) : '';
                    $dLastname = $value->patient ?  Crypt::decryptString($value->patient->lastname) : '';

                    $is_existing = true;

                    if(empty($dFirstname) || empty($dLastname)) {
                        $is_existing = false;
                        $patient_name = isset($value->patient_name) ? $value->patient_name : '';

                        $patientArr = [];
                        if(!empty($patient_name)) {
                            $patientArr = explode(',', $patient_name);

                            $dFirstname = isset($patientArr[1]) ? $patientArr[1] : '';
                            $dLastname = isset($patientArr[0]) ? $patientArr[0] : '';
                        }
                    }
                    
                    $value->dFirstname = $dFirstname;
                    $value->dLastname = $dLastname;
                    $value->is_existing = $is_existing;
                    
                    $modifiedData[] = $value;
                }

                return response()->json([
                    'dates' => $clinicalKpiDates->pluck('date')->toArray(),
                    'data' => $modifiedData
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in BridgedPatientController.date_with.'
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
                ]);

                if ($validation->passes()){

                    $co = new ClinicalBridgedPatient();
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
                'message' => 'Something went wrong in BridgedPatientController.store.'
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();
            if($request->ajax()){
            
                $co = ClinicalBridgedPatient::findOrFail($request->id);
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
                'message' => 'Something went wrong in BridgedPatientController.delete.'
            ]);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            DB::beginTransaction();
            if($request->ajax()){
            
                $selectedIds = $request->selectedIds ?? [];

                $count = 0;
                if(count($selectedIds) > 0) {
                    $count = ClinicalBridgedPatient::whereIn('id', $selectedIds)->delete();
                    if(!$count) {
                        throw new Exception("Not deleted all selected");
                    }
                }

                DB::commit();
    
                return json_encode([
                    'status'=>'success',
                    'message'=> $count.' Record/s has been deleted.'
                ]);
            }
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in BridgedPatientController.deleteAll'
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            if($request->ajax()){
                $data = $request->all();
                    
                $co = ClinicalBridgedPatient::findOrFail($data['id']);
                $co->date = $data['date'];
                $co->rx_number = $data['rx_number'];
                $co->medication_description = $data['medication_description'];
                $co->pharmacy_store_id = $data['pharmacy_store_id'];
                $co->remarks = $data['remarks'];
    
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
                'message' => 'Something went wrong in BridgedPatientController.update.'
            ]);
        }
    }

    public function data(Request $request)
    {
        try {
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
                $query = ClinicalBridgedPatient::with('user.employee', 'patient')
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
                    $query->orWhereHas('patient', function ($query) use ($search) {
                        $encryptedQuery = [];
                        if(!empty($search)) {
                            $encryptedQuery = Patient::where('source', 'pioneer')->get()->filter(function ($encryptedQuery) use ($search) {
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
                    $patient = $value->patient ? $value->patient : null;
                    $dFirstname = $patient ? Crypt::decryptString($patient->firstname) : '';
                    $dLastname = $patient ? Crypt::decryptString($patient->lastname) : '';
                    
                    $value->dFirstname = $dFirstname;
                    $value->dLastname = $dLastname;
    
                    $actions = '<div class="d-flex order-actions">';
                    if(auth()->user()->can('menu_store.clinical.bridged_patients.update')) {
                        $actions .= '
                            <button class="btn btn-sm btn-primary me-1" 
                                id="data-edit-btn-'.$value->id.'"
                                data-id="'.$value->id.'" 
                                data-array="'.htmlspecialchars(json_encode($value)).'"
                                onclick="showEditForm('.$value->id.');"><i class="fa fa-pencil"></i>
                            </button>';
                    }
                    if(auth()->user()->can('menu_store.clinical.bridged_patients.delete')) {
                        $actions .= '<button class="btn btn-sm btn-danger" onclick="ShowConfirmDeleteForm(' . $value->id . ')"><i class="fa fa-trash-can"></i></button>';
                    }             
                    $actions .= '</div>';
                    
                    $newData[] = [
                        'id' => $value->id,
                        'date' => !empty($value->date) ? date('Y-m-d', strtotime($value->date)) : '',
                        'rx_number' => $value->rx_number,
                        'patient_name' => $value->patient_name,
                        'formatted_patient_name' => $patient ? $dLastname.', '.$dFirstname : '<span title="Not existing in Intranet Pioneer Patients Masterlist" class="text-danger">'.$value->patient_name.'<i class="fa fa-triangle-exclamation ms-2"></i></span>',
                        'medication_description'    => $value->medication_description,
                        'remarks'   => $value->remarks,
                        'created_by' => $value->user->employee->firstname.' '.$value->user->employee->lastname,
                        'created_at' => $value->created_at,
                        'formatted_created_at' => date('M d, Y g:i A', strtotime($value->pst_created_at)),
                        'actions' =>  $actions
                    ];
                }   
                
                return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
            }
        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in BridgedPatientController.data.'
            ]);
        }
    }

    public function summary(Request $request)
    {
        try {
            DB::beginTransaction();

            $date = $request->date;
            $pharmacy_store_id = $request->pharmacy_store_id;

            $uniquesObject = ClinicalBridgedPatient::select(
                DB::raw('COUNT(DISTINCT rx_number) as count_rx_numbers'), 
                DB::raw('COUNT(DISTINCT patient_name) as count_patient_names'),
            )
                ->where('date', $date)
                ->where('pharmacy_store_id', $pharmacy_store_id)
                ->first();

            $data = [
                'date' => $date,
                'formatted_date' => date('F d, Y', strtotime($date)),
                'count_rx_numbers' => isset($uniquesObject->count_rx_numbers) ? $uniquesObject->count_rx_numbers : 0,
                'count_patient_names' => isset($uniquesObject->count_patient_names) ? $uniquesObject->count_patient_names : 0
            ];

            DB::commit();
            
            if($request->ajax()){    
                return json_encode([
                    'data'=>$data,
                    'status'=>'success',
                    'message'=>'Record has been retrieved.'
                ]);
            }
            return [
                'data'=>$data,
                'status'=>'success',
                'message'=>'Record has been retrieved.'
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in BridgedPatientController.summary.'
            ]);
        }
    }

    public function sendMail(Request $request)
    {
        try {
            if($request->ajax()){

                $summary = $this->summary($request);
                $summary = json_decode($summary);
                $summary = isset($summary->data) ? $summary->data : [];

                $query = ClinicalBridgedPatient::with('user.employee', 'patient')
                    ->where('pharmacy_store_id', $request->pharmacy_store_id)
                    ->where('date', $request->date)
                    ->orderBy('patient_name', 'asc')
                    ->get();

                // $user = User::where('email', 'info@mgmt88.com')->first();

                $userEmail = auth()->user()->email;
                // $docEmail = 'vee@mgmt88.com';

                // $emails = [
                //     'erwin@mgmt88.com',
                //     'cgarcia@mgmt88.com',
                //     $userEmail
                // ];

                // if($userEmail != $docEmail) {
                //     array_push($emails, $docEmail);
                // }
            
                // if(config('mail.maintenance') != "ON") {
                //     Bus::dispatch(new NotifyClinicalBridgedPatientToCustom($query, $summary, $emails, $user));
                // }

                $data = [
                    'collections' => $query,
                    'summary'     => $summary
                ];

                $emails = [
                    'erwin@mgmt88.com',
                    'cgarcia@mgmt88.com',
                    'chris@mgmt88.com',
                    'info@mgmt88.com',
                    'vee@mgmt88.com'
                ];
                $cc = array_filter($emails, function($item) use ($userEmail) {
                    return $item !== $userEmail;
                });
                
                if(config('mail.maintenance') != "ON") {
                    Mail::to($userEmail)
                        ->cc($cc)
                        ->send(new ClinicalBridgedPatientMail($data));
                }
    
                return json_encode([
                    'status'=>'success',
                    'message'=>'Mail has been sent.'
                ]);
            }
        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in BridgedPatientController.sendMail.'
            ]);
        }
    }

    public function export(Request $request)
    {
        $search = $request->query('search', '');
        $pharmacy_store_id = $request->id ? $request->id : null;
        $date = $request->date ? $request->date : null;

        $filterArray = [];

        if(!empty($search)) {
            $filterArray['search'] = $search;
        }

        if(!empty($pharmacy_store_id)) {
            $filterArray['pharmacy_store_id'] = $pharmacy_store_id;
        }

        if(!empty($date)) {
            $filterArray['date'] = $date;
        }

        $date_today = $this->getCurrentPSTDate('YmdHis');

        return Excel::download(new ClinicalBridgedPatientCustomExport($filterArray), 'Clinical Bridged Patients for Date '.$date.' [Intranet'.$date_today.'].xlsx');
    }

}
