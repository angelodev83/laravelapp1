<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Helper;
use App\Models\ClinicalReportsDaily;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DivisionThreeTaskController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:division-3.tasks.index', ['only' => ['index', 'get_data']]);
        $this->middleware('permission:division-3.tasks.create', ['only' => ['store']]);
        $this->middleware('permission:division-3.tasks.update', ['only' => ['update']]);
        $this->middleware('permission:division-3.tasks.delete', ['only' => ['destroy']]);
        $this->middleware('permission:division-3.tasks.export', ['only' => ['upload_csv']]);
        $this->middleware('permission:division-3.tasks.import', ['only' => ['upload_csv']]);
    }

    public function index()
    {
        $user = Auth::user();

        $breadCrumb = ['Division 3', 'Tasks'];
        return view('/division3/task/index', compact('user', 'breadCrumb'));
    }

    public function get_dropdown_data(Request $request)
    {
        if($request->ajax()){
           $task = explode(',', env('CLINICAL_REPORTS_TASKS'));
           $outlier = explode(',', env('CLINICAL_REPORTS_OUTLIER_TYPE'));

            return response()->json(['task'=> $task,'outlier'=> $outlier,]);
        }
    }

    public function get_outlier_type(Request $request)
    {
        if($request->ajax()){
           $data = explode(',', env('CLINICAL_REPORTS_OUTLIER_TYPE'));

            return response()->json(['data'=> $data,]);
        }
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            
            $helper =  new Helper;
            $input = $request->all();
            
            switch ($input['task_name']) {
                case 'Post Antibiotics Follow-Up':
                    $validation = Validator::make($input, [
                        'patient_name' => 'required|max:50|min:1',
                        'patient_birthdate' => 'required|date',
                        'completed_date' => 'required|date',
                        'date_of_interaction' => 'required|date',
                        'medications' => 'required|max:200|min:1',
                    ]);

                    if ($validation->passes()){
                
                        $data = new ClinicalReportsDaily();
                        $data->name = $input['task_name'];
                        $data->patient_name = $helper->ProperNamingCase($input['patient_name']);
                        $data->patient_birthdate = $input['patient_birthdate'];
                        $data->medications = $input['medications'];
                        $data->completed_date = $input['completed_date'];
                        $data->date_of_interaction = $input['date_of_interaction'];
                        $data->comments = $input['comments'];
                        $data->save();

                        //store history
                        $history_body = array(
                            'task' => $data
                        );
                        $history_header = array(
                            'class' => 'DIVISIONTHREETASK ',
                            'method' => 'CREATED ',
                            'name' => $input['task_name'],
                            'id' => $data->id
                        );
                        // $this->store_historyV2($history_header, $history_body, 'invoice', $inmar->id);
        
                        return json_encode([
                            'data'=> $data->id,
                            'status'=>'success',
                            'message'=>'Record has been saved.']);
                    }
                    else{
                        return json_encode(
                            ['status'=>'error',
                            'errors'=> $validation->errors(),
                            'message'=>'Record saving failed.']);
                    }
                    break;
                case 'New Medication Initiation':
                    $validation = Validator::make($input, [
                        'patient_name' => 'required|max:50|min:1',
                        'patient_birthdate' => 'required|date',
                        'date_of_initiation' => 'required|date',
                        'date_of_initiation' => 'required|date',
                    ]);

                    if ($validation->passes()){
                
                        $data = new ClinicalReportsDaily();
                        $data->name = $input['task_name'];
                        $data->patient_name = $helper->ProperNamingCase($input['patient_name']);
                        $data->patient_birthdate = $input['patient_birthdate'];
                        $data->date_of_initiation = $input['date_of_initiation'];
                        $data->comments = $input['comments'];
                        $data->save();

                        //store history
                        $history_body = array(
                            'task' => $data
                        );
                        $history_header = array(
                            'class' => 'DIVISIONTHREETASK ',
                            'method' => 'CREATED ',
                            'name' => $input['task_name'],
                            'id' => $data->id
                        );
                        // $this->store_historyV2($history_header, $history_body, 'invoice', $inmar->id);
        
                        return json_encode([
                            'data'=> $data->id,
                            'status'=>'success',
                            'message'=>'Record has been saved.']);
                    }
                    else{
                        return json_encode(
                            ['status'=>'error',
                            'errors'=> $validation->errors(),
                            'message'=>'Record saving failed.']);
                    }
                    break;
                case 'Side Effects Monitoring':
                    $validation = Validator::make($input, [
                        'patient_name' => 'required|max:50|min:1',
                        'patient_birthdate' => 'required|date',
                        'side_effects' => 'required',
                        'date_side_effects' => 'required|date',
                        'date_follow_up' => 'required|date',
                    ]);

                    if ($validation->passes()){
                
                        $data = new ClinicalReportsDaily();
                        $data->name = $input['task_name'];
                        $data->patient_name = $helper->ProperNamingCase($input['patient_name']);
                        $data->patient_birthdate = $input['patient_birthdate'];
                        $data->side_effects = $input['side_effects'];
                        $data->date_side_effects = $input['date_side_effects'];
                        $data->date_follow_up = $input['date_follow_up'];
                        $data->comments = $input['comments'];
                        $data->save();

                        //store history
                        $history_body = array(
                            'task' => $data
                        );
                        $history_header = array(
                            'class' => 'DIVISIONTHREETASK ',
                            'method' => 'CREATED ',
                            'name' => $input['task_name'],
                            'id' => $data->id
                        );
                        // $this->store_historyV2($history_header, $history_body, 'invoice', $inmar->id);
        
                        return json_encode([
                            'data'=> $data->id,
                            'status'=>'success',
                            'message'=>'Record has been saved.']);
                    }
                    else{
                        return json_encode(
                            ['status'=>'error',
                            'errors'=> $validation->errors(),
                            'message'=>'Record saving failed.']);
                    }
                    break;
                case 'Vitamin Deficiency Mangement':
                    $validation = Validator::make($input, [
                        'patient_name' => 'required|max:50|min:1',
                        'patient_birthdate' => 'required|date',
                        'date_of_interaction' => 'required|date',
                        'recommended_vitamins' => 'required',
                    ]);

                    if ($validation->passes()){
                
                        $data = new ClinicalReportsDaily();
                        $data->name = $input['task_name'];
                        $data->patient_name = $helper->ProperNamingCase($input['patient_name']);
                        $data->patient_birthdate = $input['patient_birthdate'];
                        $data->date_of_interaction = $input['date_of_interaction'];
                        $data->recommended_vitamins = $input['recommended_vitamins'];
                        $data->comments = $input['comments'];
                        $data->save();

                        //store history
                        $history_body = array(
                            'task' => $data
                        );
                        $history_header = array(
                            'class' => 'DIVISIONTHREETASK ',
                            'method' => 'CREATED ',
                            'name' => $input['task_name'],
                            'id' => $data->id
                        );
                        // $this->store_historyV2($history_header, $history_body, 'invoice', $inmar->id);
        
                        return json_encode([
                            'data'=> $data->id,
                            'status'=>'success',
                            'message'=>'Record has been saved.']);
                    }
                    else{
                        return json_encode(
                            ['status'=>'error',
                            'errors'=> $validation->errors(),
                            'message'=>'Record saving failed.']);
                    }
                    break;
                case 'Adherence Promotion':
                    $validation = Validator::make($input, [
                        'patient_name' => 'required|max:50|min:1',
                        'patient_birthdate' => 'required|date',
                        'outlier_type' => 'required',
                        'date_of_initiation' => 'required|date',
                        'pdc_rate' => 'required',
                        'medications' => 'required|max:200|min:1',
                    ]);

                    if ($validation->passes()){
                
                        $data = new ClinicalReportsDaily();
                        $data->name = $input['task_name'];
                        $data->patient_name = $helper->ProperNamingCase($input['patient_name']);
                        $data->patient_birthdate = $input['patient_birthdate'];
                        $data->medications = $input['medications'];
                        $data->outlier_type = $input['outlier_type'];
                        $data->date_of_initiation = $input['date_of_initiation'];
                        $data->pdc_rate = $input['pdc_rate'];
                        $data->comments = $input['comments'];
                        $data->save();

                        //store history
                        $history_body = array(
                            'task' => $data
                        );
                        $history_header = array(
                            'class' => 'DIVISIONTHREETASK ',
                            'method' => 'CREATED ',
                            'name' => $input['task_name'],
                            'id' => $data->id
                        );
                        // $this->store_historyV2($history_header, $history_body, 'invoice', $inmar->id);
        
                        return json_encode([
                            'data'=> $data->id,
                            'status'=>'success',
                            'message'=>'Record has been saved.']);
                    }
                    else{
                        return json_encode(
                            ['status'=>'error',
                            'errors'=> $validation->errors(),
                            'message'=>'Record saving failed.']);
                    }
                    break;
                default:
                    $validation = Validator::make($input, [
                        'task_name' => 'required',
                    ]);

                    if (!$validation->passes()){
                        return json_encode(
                            ['status'=>'error',
                            'errors'=> $validation->errors(),
                            'message'=>'Record saving failed.']);
                    }
                    break;
            }
        
        }
    }

    public function update(Request $request)
    {
        if($request->ajax()){
            
            $helper =  new Helper;
            $input = $request->all();
            
            $data_old = ClinicalReportsDaily::where('id', $input['id'])->first();
            $data = ClinicalReportsDaily::where('id', $input['id'])->first();

            switch ($input['task_name']) {
                case 'Post Antibiotics Follow-Up':
                    $validation = Validator::make($input, [
                        'patient_name' => 'required|max:50|min:1',
                        'patient_birthdate' => 'required|date',
                        'completed_date' => 'required|date',
                        'date_of_interaction' => 'required|date',
                        'medications' => 'required|max:200|min:1',
                    ]);

                    if ($validation->passes()){
                
                        
                        $data->name = $input['task_name'];
                        $data->patient_name = $helper->ProperNamingCase($input['patient_name']);
                        $data->patient_birthdate = $input['patient_birthdate'];
                        $data->medications = $input['medications'];
                        $data->completed_date = $input['completed_date'];
                        $data->date_of_interaction = $input['date_of_interaction'];
                        $data->comments = $input['comments'];
                        $data->save();

                    }
                    else{
                        return json_encode(
                            ['status'=>'error',
                            'errors'=> $validation->errors(),
                            'message'=>'Record saving failed.']);
                    }
                    break;
                case 'New Medication Initiation':
                    $validation = Validator::make($input, [
                        'patient_name' => 'required|max:50|min:1',
                        'patient_birthdate' => 'required|date',
                        'date_of_initiation' => 'required|date',
                        'date_of_initiation' => 'required|date',
                    ]);

                    if ($validation->passes()){
                
                        $data->name = $input['task_name'];
                        $data->patient_name = $helper->ProperNamingCase($input['patient_name']);
                        $data->patient_birthdate = $input['patient_birthdate'];
                        $data->date_of_initiation = $input['date_of_initiation'];
                        $data->comments = $input['comments'];
                        $data->save();
                    }
                    else{
                        return json_encode(
                            ['status'=>'error',
                            'errors'=> $validation->errors(),
                            'message'=>'Record saving failed.']);
                    }
                    break;
                case 'Side Effects Monitoring':
                    $validation = Validator::make($input, [
                        'patient_name' => 'required|max:50|min:1',
                        'patient_birthdate' => 'required|date',
                        'side_effects' => 'required',
                        'date_side_effects' => 'required|date',
                        'date_follow_up' => 'required|date',
                    ]);

                    if ($validation->passes()){
                
                        $data->name = $input['task_name'];
                        $data->patient_name = $helper->ProperNamingCase($input['patient_name']);
                        $data->patient_birthdate = $input['patient_birthdate'];
                        $data->side_effects = $input['side_effects'];
                        $data->date_side_effects = $input['date_side_effects'];
                        $data->date_follow_up = $input['date_follow_up'];
                        $data->comments = $input['comments'];
                        $data->save();

                    }
                    else{
                        return json_encode(
                            ['status'=>'error',
                            'errors'=> $validation->errors(),
                            'message'=>'Record saving failed.']);
                    }
                    break;
                case 'Vitamin Deficiency Mangement':
                    $validation = Validator::make($input, [
                        'patient_name' => 'required|max:50|min:1',
                        'patient_birthdate' => 'required|date',
                        'date_of_interaction' => 'required|date',
                        'recommended_vitamins' => 'required',
                    ]);

                    if ($validation->passes()){
                
                        $data->name = $input['task_name'];
                        $data->patient_name = $helper->ProperNamingCase($input['patient_name']);
                        $data->patient_birthdate = $input['patient_birthdate'];
                        $data->date_of_interaction = $input['date_of_interaction'];
                        $data->recommended_vitamins = $input['recommended_vitamins'];
                        $data->comments = $input['comments'];
                        $data->save();

                    }
                    else{
                        return json_encode(
                            ['status'=>'error',
                            'errors'=> $validation->errors(),
                            'message'=>'Record saving failed.']);
                    }
                    break;
                case 'Adherence Promotion':
                    $validation = Validator::make($input, [
                        'patient_name' => 'required|max:50|min:1',
                        'patient_birthdate' => 'required|date',
                        'outlier_type' => 'required',
                        'date_of_initiation' => 'required|date',
                        'pdc_rate' => 'required',
                        'medications' => 'required|max:200|min:1',
                    ]);

                    if ($validation->passes()){
                
                        $data->name = $input['task_name'];
                        $data->patient_name = $helper->ProperNamingCase($input['patient_name']);
                        $data->patient_birthdate = $input['patient_birthdate'];
                        $data->medications = $input['medications'];
                        $data->outlier_type = $input['outlier_type'];
                        $data->date_of_initiation = $input['date_of_initiation'];
                        $data->pdc_rate = $input['pdc_rate'];
                        $data->comments = $input['comments'];
                        $data->save();

                    }
                    else{
                        return json_encode(
                            ['status'=>'error',
                            'errors'=> $validation->errors(),
                            'message'=>'Record saving failed.']);
                    }
                    break;
                default:
                    $validation = Validator::make($input, [
                        'task_name' => 'required',
                    ]);

                    if (!$validation->passes()){
                        return json_encode(
                            ['status'=>'error',
                            'errors'=> $validation->errors(),
                            'message'=>'Record saving failed.']);
                    }
                    break;
            }

             //store history
            $history_body = array(
                'task_new' => $data,
                'task_old' => $data_old
            );
            $history_header = array(
                'class' => 'DIVISIONTHREETASK ',
                'method' => 'UPDATED ',
                'name' => $input['task_name'],
                'id' => $data->id
            );
            //$this->update_historyV2($history_header, $history_body, 'inmar', $inmar->id);

            return json_encode([
                'data'=> $data->id,
                'status'=>'success',
                'message'=>'Record has been updated.'
            ]);
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){

            $input = $request->all();
            
            $id = $input['id'];
            
            $data = ClinicalReportsDaily::where('id', $id)->first();
            $data_old = $data;
            $data->delete();

            //delete history
            $history_body = array(
                'task' => $data_old,
            );
            $history_header = array(
                'class' => 'DIVISIONTHREETASK',
                'method' => 'DELETED task ',
                'name' => $data_old->name,
                'id' => $data_old->id,
            );
            //$this->delete_history($history_header, $history_body, 'inmar', $inmar_old->id);
            

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    public function upload_csv(Request $request)
    {
        if($request->ajax()){
            $file = $request->file('file');
            $input = $request->all();
            
            $validation = Validator::make($input, [
                'file' => 'required|mimes:csv',
            ]);

            if ($validation->passes()){
                
                $current = file_get_contents($file);
                $save_name = str_replace('\\', '/' , storage_path())."/daily_clinical_reports.csv";
            
                file_put_contents($save_name, $current);

                $this->loadCsvMysql();
                                
                return response()->json([
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);

            }
            else{
                return response()->json([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Saving failed.'
                ]);
            }
        }
    }

    public function loadCsvMysql()
    {
        //Storage::disk('local')->append('file.txt', json_encode($file));
       
        
        try {
            DB::beginTransaction();
            //code...
                
        $absolute_path = str_replace('\\', '/' , storage_path());

        DB::statement('TRUNCATE table temp_clinical_reports_dailies');
        DB::statement('LOAD DATA INFILE "'.$absolute_path.'/daily_clinical_reports.csv"
            INTO TABLE temp_clinical_reports_dailies
            FIELDS TERMINATED BY \',\'
            ENCLOSED BY \'"\'
            LINES TERMINATED BY \'\n\'
            IGNORE 1 ROWS
            (name,patient_name,patient_birthdate,medications,completed_date,date_of_interaction,date_of_initiation,side_effects,date_side_effects,date_follow_up,recommended_vitamins,outlier_type,pdc_rate,comments)');

        DB::statement("INSERT INTO clinical_reports_dailies (name, patient_name, patient_birthdate, medications, completed_date, 
            date_of_interaction, date_of_initiation, side_effects, date_side_effects, date_follow_up, recommended_vitamins, 
            pdc_rate, outlier_type, comments, created_at, updated_at)
            SELECT name, patient_name, if(patient_birthdate = '', null,STR_TO_DATE(patient_birthdate, '%m/%d/%Y')), REPLACE(medications,',','\n'), 
            if(completed_date = '', null,STR_TO_DATE(completed_date, '%m/%d/%Y')), if(date_of_interaction = '', null,STR_TO_DATE(date_of_interaction, '%m/%d/%Y')), 
            if(date_of_initiation = '', null,STR_TO_DATE(date_of_initiation, '%m/%d/%Y')), side_effects, if(date_side_effects = '', null,STR_TO_DATE(date_side_effects, '%m/%d/%Y')), 
            if(date_follow_up = '', null,STR_TO_DATE(date_follow_up, '%m/%d/%Y')), recommended_vitamins, pdc_rate, outlier_type, 
            comments, '".date("Y-m-d H:i:s")."', '".CARBON::now()."'
            FROM temp_clinical_reports_dailies");

            DB::commit();
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Invalid File!'
                ]);
            }

    }

    public function get_data(Request $request)
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
            $query = new ClinicalReportsDaily;

            // Search //input all searchable fields
            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                // $query->orWhere('name', 'like', "%".$search."%");
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        $query->orWhere($column['name'], 'like', "%".$search."%");
                    }  
                }
            });

            //default field for order
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            
            

            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $val) {
                // $type_color = explode(',', env('RETURN_TYPE_COLOR'));
                // $type_array = explode(',', env('RETURN_TYPE'));
                // $type_key = array_search($val->type, $type_array);
                
                // $status_color = explode(',', env('INMAR_STATUS_COLOR'));
                // $status_array = explode(',', env('INMAR_STATUS'));
                // $status_key = array_search($val->status, $status_array);

                $patient_birthdate = ($val->patient_birthdate)?date('Y-m-d', strtotime($val->patient_birthdate)):'';
                $completed_date = ($val->completed_date)?date('Y-m-d', strtotime($val->completed_date)):'';
                $date_of_interaction = ($val->date_of_interaction)?date('Y-m-d', strtotime($val->date_of_interaction)):'';
                $date_of_initiation = ($val->date_of_initiation)?date('Y-m-d', strtotime($val->date_of_initiation)):'';
                $date_side_effects = ($val->date_side_effects)?date('Y-m-d', strtotime($val->date_side_effects)):'';
                $date_follow_up = ($val->date_follow_up)?date('Y-m-d', strtotime($val->date_follow_up)):'';

                if(strlen($val->medications) > 10)
                {
                    $medicationList = '<div>'.substr($val->medications,0,10).'...<br><a data-id="'.$val->id.'" data-name="'.$val->name.'"
                                data-patientname="'.$val->patient_name.'"
                                data-patientbirthdate="'.$patient_birthdate.'" data-medications="'.$val->medications.'"
                                data-completeddate="'.$completed_date.'" data-dateofinteraction="'.$date_of_interaction.'"
                                data-dateofinitiation="'.$date_of_initiation.'" data-sideeffects="'.$val->side_effects.'"
                                data-datesideeffects="'.$date_side_effects.'" data-datefollowup="'.$date_follow_up.'"
                                data-recommendedvitamins="'.$val->recommended_vitamins.'" data-pdcrate="'.$val->pdc_rate.'"
                                data-outliertype="'.$val->outlier_type.'" data-comments="'.$val->comments.'"
                                onclick="showViewForm(this);"href="#">(Read More)</a></div>';
                }
                else
                {
                    $medicationList = $val->medications;
                }

                $actions = '<div class="d-flex order-actions"><a data-id="'.$val->id.'" data-name="'.$val->name.'" data-patientname="'.$val->patient_name.'"
                data-patientbirthdate="'.$patient_birthdate.'" data-medications="'.$val->medications.'"
                data-completeddate="'.$completed_date.'" data-dateofinteraction="'.$date_of_interaction.'"
                data-dateofinitiation="'.$date_of_initiation.'" data-sideeffects="'.$val->side_effects.'"
                data-datesideeffects="'.$date_side_effects.'" data-datefollowup="'.$date_follow_up.'"
                data-recommendedvitamins="'.$val->recommended_vitamins.'" data-pdcrate="'.$val->pdc_rate.'"
                data-outliertype="'.$val->outlier_type.'" data-comments="'.$val->comments.'"
                onclick="showViewForm(this);"
                class="btn-primary" style="background-color:#6c757d"><i class="bx bxs-show"></i></a>';
                if(Auth::user()->can('division-3.tasks.update')) {
                    $actions .= '<a data-id="'.$val->id.'" data-name="'.$val->name.'" data-patientname="'.$val->patient_name.'"
                    data-patientbirthdate="'.$patient_birthdate.'" data-medications="'.$val->medications.'"
                    data-completeddate="'.$completed_date.'" data-dateofinteraction="'.$date_of_interaction.'"
                    data-dateofinitiation="'.$date_of_initiation.'" data-sideeffects="'.$val->side_effects.'"
                    data-datesideeffects="'.$date_side_effects.'" data-datefollowup="'.$date_follow_up.'"
                    data-recommendedvitamins="'.$val->recommended_vitamins.'" data-pdcrate="'.$val->pdc_rate.'"
                    data-outliertype="'.$val->outlier_type.'" data-comments="'.$val->comments.'"
                    onclick="showEditForm(this);"
                    class="btn-primary" style="background-color:#8833ff"><i class="bx bxs-edit"></i></a>';
                }
                if(Auth::user()->can('division-3.tasks.delete')) {
                    $actions .= '<a onclick="ShowConfirmDeleteForm(' . $val->id . ')" class="btn-danger" style="background-color:#dc362e"><i class="bx bxs-trash"></i></a>';
                }
                $actions .= '</div>';

                $newData[] = [
                    'id' => $val->id,
                    'name' => $val->name,
                    'patient_name' => $val->patient_name,
                    'patient_birthdate' => $patient_birthdate,
                    'medications' => $medicationList,
                    'medications2' => $val->medications,
                    'completed_date' => $completed_date,
                    'date_of_interaction' => $date_of_interaction,
                    'date_of_initiation' => $date_of_initiation,
                    'side_effects' => $val->side_effects,
                    'date_side_effects' => $date_side_effects,
                    'date_follow_up' => $date_follow_up,
                    'recommended_vitamins' => $val->recommended_vitamins,
                    'outlier_type' => $val->outlier_type,
                    'pdc_rate' => $val->pdc_rate,
                    'comments' => $val->comments,
                    'actions' => $actions
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }
}
