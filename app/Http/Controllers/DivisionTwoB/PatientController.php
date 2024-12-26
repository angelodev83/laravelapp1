<?php

namespace App\Http\Controllers\DivisionTwoB;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Models\Allergy;
use App\Models\File;
use App\Models\Patient;
use App\Models\PatientImmunization;
use App\Models\PatientMedication;
use App\Models\PatientNote;
use App\Services\TebraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function index($id, Request $request)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Clinical', 'Tebra Patients'];
            
            return view('/division2b/patients/index', compact('breadCrumb'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function facesheet($id, $p_id)
    {
        $user = Auth::user();
        $breadCrumb = ['Clinical', 'Tebra Patient Facesheet'];
        $profileData = $this->profileData($p_id);
        
        return view('/division2b/patients/patientData', compact('user','breadCrumb', 'profileData'));
    }

    public function get_immunizations($id, $p_id)
    {
        $user = Auth::user();
        $breadCrumb = ['Clinical', 'Tebra Patient Immunization'];
        $id = $p_id;
        
        return view('/division2b/patients/patientImmunizations', compact('user','breadCrumb', 'id'));
    }

    public function immunization_store(Request $request)
    {
        if($request->ajax()){

            $input = $request->all();
            
            $validation = Validator::make($input, [
                'name' => 'required|max:50|min:1',
                'schedule' => 'required',
            ]);
            
            if ($validation->passes()){

                $note = new PatientImmunization();
                $note->name = $input['name'];
                $note->schedule = date('Y-m-d H:i:s', strtotime($input['schedule']));
                $note->patient_id = $input['patient_id'];
                $note->save();

                return response()->json([
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);               
            }
            else{
                return json_encode(
                    ['status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Employee saving failed.']);
            }
        }
    }

    public function immunization_update(Request $request)
    {
        if($request->ajax()){
            $input = $request->all();

            $validation = Validator::make($input, [
                'name' => 'required|max:50|min:1',
                'schedule' => 'required',
            ]);

            if ($validation->passes()){
                $med = PatientImmunization::where('id', $input['id'])->first();
                $med->name = $input["name"];
                $med->schedule = date('Y-m-d H:i:s', strtotime($input['schedule']));  
                $med->save();

                return json_encode([
                    'data'=> $med->id,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
            else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Record saving failed.'
                ]);
            }
        }
    }

    public function immunization_destroy(Request $request)
    {
        if($request->ajax()){

            $input = $request->all();
            
            $id = $input['id'];
            
            $data = PatientImmunization::where('id', $id)->first();
            $data_old = $data;
            $data->delete();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    public function get_patient_immunizations_data(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            $id = $request->patient_id;
            // get data from products table
            $query = PatientImmunization::whereHas('patient', function ($query) use ($id) {
                $query->where('tebra_id', $id);
            });
        
            // Search //input all searchable fields
            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }  
                }   
            });
            
            //default field for order
            $orderByCol = $columns[$orderColumnIndex]['name'];

            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {
                $newData[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                    'actions' =>  '<div class="d-flex order-actions">
                        <a data-id="'.$value->id.'" data-name="'.$value->name.'" data-schedule="'.date('Y-m-d h:i:s A', strtotime($value->schedule)).'"
                                onclick="showEditForm(this);"
                                class="btn-primary" style="background-color:#8833ff"><i class="bx bxs-edit"></i></a>
                        <button type="button" onclick="ShowConfirmDeleteForm(' . $value->id . ')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                    </div>',
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }

    public function get_notes($id, $p_id)
    {
        $user = Auth::user();
        $breadCrumb = ['Clinical', 'Tebra Patient Note'];
        $id = $p_id;
        
        return view('/division2b/patients/patientNotes', compact('user','breadCrumb', 'id'));
    }

    public function note_store(Request $request)
    {
        if($request->ajax()){
            $file = $request->file('file');

            $input = $request->all();
            
            $validation = Validator::make($input, [
                'file' => 'required|mimes:pdf,csv,xlsx',
                'name' => 'required|max:50|min:1',
                'body' => 'required',
            ]);
            
            if ($validation->passes()){
                if ($file->isValid()) {

                    $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $fileExtension = $file->getClientOriginalExtension();
                    $mime_type = $file->getMimeType();
                    
                    $newFileName = date("Ymdhis").Auth::id() .'_'. $fileName  . '.' . $fileExtension;
                    $doc_type = $fileExtension;
                    
                    $path = 'patients/documents/';
                    
                    // Provide a dynamic path or use a specific directory in your S3 bucket
                    $path_file = 'patients/documents/'  . $newFileName;

                    // Store the file in S3
                    Storage::disk('s3')->put($path_file, file_get_contents($file));

                    $save_file = new File();

                    $save_file->filename = $newFileName;
                    $save_file->path = $path;
                    $save_file->mime_type = $mime_type;
                    $save_file->document_type = $doc_type;
                    $save_file->save();

                    $file_id = $save_file->id;

                    $note = new PatientNote();
                    $note->name = $input['name'];
                    $note->body = $input['body'];
                    $note->user_id = Auth::id();
                    $note->file_id = $file_id;
                    $note->patient_id = $input['patient_id'];
                    $note->save();

                    return response()->json([
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ]);

                } else {
                    return response()->json([
                        'file' => $file->getClientOriginalName(),
                        'error' => 'Invalid file',
                        'status'=>'error',
                        'message'=>'Invalid file'
                    ]);
                }
            }
            else{
                return json_encode(
                    ['status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Employee saving failed.']);
            }
        }
    }

    public function note_destroy(Request $request)
    {
        if($request->ajax()){

            $input = $request->all();
            
            $id = $input['id'];
            $note = PatientNote::where('id', $id)->first();

            if($note->file_id != 0){
                $file = $note->file;
                $path = $file->path.$file->filename;
            
                if($path != ''){
                    if(Storage::disk('s3')->exists($path)) {
                        Storage::disk('s3')->delete($path);
                    }

                    $file_data = File::where('id', $file->id)->first();
                    $file_data->delete();
                }
            }
            
            $note->file_id = 0;
            $note->save();

            if($input['delete_file_only'] == 0){
            
                $note->delete();
            }

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    public function note_update(Request $request)
    {
        if($request->ajax()){
            $input = $request->all();

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                
                $validation = Validator::make($input, [
                    'file' => 'required|mimes:pdf,csv,xlsx',
                    'name' => 'required|max:50|min:1',
                ]);

                if ($validation->passes()){
                    if ($file->isValid()) {
                        
                        $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                        $fileExtension = $file->getClientOriginalExtension();
                        $mime_type = $file->getMimeType();
                        
                        $newFileName = date("Ymdhis").Auth::id() .'_'. $fileName  . '.' . $fileExtension;
                        $doc_type = $fileExtension;
                        
                        $path = 'patients/documents/';
                        
                        // Provide a dynamic path or use a specific directory in your S3 bucket
                        $path_file = 'patients/documents/'  . $newFileName;

                        // Store the file in S3
                        Storage::disk('s3')->put($path_file, file_get_contents($file));

                        $save_file = new File();

                        $save_file->filename = $newFileName;
                        $save_file->path = $path;
                        $save_file->mime_type = $mime_type;
                        $save_file->document_type = $doc_type;
                        $save_file->save();

                        $file_id = $save_file->id;

                        $invoice = PatientNote::where('id', $input['note_id'])->first();
                        $invoice->name = $input['name'];
                        $invoice->body = $input['body'];
                        $invoice->user_id = Auth::id();
                        $invoice->file_id = $file_id;
                        $invoice->save();


                        
                        return response()->json([
                            'status'=>'success',
                            'message'=>'Record has been saved.'
                        ]);

                    } else {
                        return response()->json([
                            'file' => $file->getClientOriginalName(),
                            'error' => 'Invalid file',
                            'status'=>'error',
                            'message'=>'Invalid file'
                        ]);
                    }
                }
                else{
                    return json_encode(
                        ['status'=>'error',
                        'errors'=> $validation->errors(),
                        'message'=>'Employee saving failed.']);
                }
            }
            else{
                $validation = Validator::make($input, [
                    'name' => 'required|max:50|min:1',
                ]);

                if ($validation->passes()){
                    
                    $invoice = PatientNote::where('id', $input['note_id'])->first();
                    $invoice->name = $input['name'];
                    $invoice->body = $input['body'];
                    $invoice->user_id = Auth::id();
                    $invoice->save();

                    return response()->json([
                        'status'=>'success',
                        'message'=>'Record has been updated.'
                    ]);
                }
                else{
                    return json_encode(
                        ['status'=>'error',
                        'errors'=> $validation->errors(),
                        'message'=>'Employee saving failed.']);
                }
            }
        }
    }

    public function note_download($id)
    {   
        $file = File::where('id', $id)->first();
        $headers = [
            'Content-Type'        => 'Content-Type: '.$file->mime_type.' ',
            'Content-Disposition' => 'attachment; filename="'. $file->filename .'"',
        ];

        $path = $file->path.$file->filename;

        return Response::make(Storage::disk('s3')->get($path), 200, $headers);
    }

    public function get_patient_notes_data(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            $id = $request->patient_id;
            // get data from products table
            $query = PatientNote::whereHas('patient', function ($query) use ($id) {
                $query->where('id', $id);
            });
            
            // Search //input all searchable fields
            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }  
                }   
            });

            //default field for order
            $orderByCol = $columns[$orderColumnIndex]['name'];
            
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {
                $hidden='';
                $s3Url='';
                $download_hidden='';

                // Access the file associated with the patient note
                $file = $value->file;
                if($value->file_id != "0"){
                    $s3Url = Storage::disk('s3')->temporaryUrl(
                        $file->path.$file->filename,
                        now()->addMinutes(30)
                    );
                    $file_id_holder = $file->id;
                    ($file->mime_type != 'application/pdf')?$hidden="d-none":'';
                    $file_name_holder = $file->filename;
                }
                else{
                    $hidden = "d-none";
                    $download_hidden = "d-none";
                    $file_name_holder = '';
                    $file_id_holder = '0';
                }
                
                $newData[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                    'actions' =>  '<div class="d-flex order-actions">
                        <a target="_blank" href="'.$s3Url.'" class="btn-light '.$hidden.'" style="background-color:#dee2e6"><i class="bx bxs-show"></i></a>
                        <a href="/admin/divisiontwob/patients/note_download/'.$value->file_id.'"
                                class="btn-light '.$download_hidden.'" style="background-color:#dee2e6"><i class="bx bxs-download"></i></a>
                        <a data-id="'.$value->id.'" data-name="'.$value->name.'" data-fileid="'.$value->file_id.'" data-body="'.$value->body.'"
                                data-filename="'.$file_name_holder.'"
                                onclick="showEditForm(this);"
                                class="btn-primary" style="background-color:#8833ff"><i class="bx bxs-edit"></i></a>
                        <button type="button" onclick="ShowConfirmDeleteForm(' . $value->id . ','.$file_id_holder.', 0)" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                    </div>',
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }

    public function get_demographics($id, $p_id)
    {
        $user = Auth::user();
        $breadCrumb = ['Clinical', 'Tebra Patient Demographic'];
        $id = $p_id;
        
        return view('/division2b/patients/patientDemographics', compact('user','breadCrumb', 'id'));
    }

    public function get_allergies($id, $p_id)
    {
        $user = Auth::user();
        $breadCrumb = ['Clinical', 'Tebra Patient Allergy'];
        $id = $p_id;
        
        return view('/division2b/patients/patientAllergies', compact('user','breadCrumb', 'id'));
    }

    public function allergy_store(Request $request)
    {
        if($request->ajax()){
            $helper =  new Helper;
            $input = $request->all();

            $validation = Validator::make($input, [
                'name' => 'required',
            ]);

            if ($validation->passes()){
                
                $allergy = new Allergy();
                $allergy->description = $input["description"];
                $allergy->name = $input["name"];  
                $allergy->patient_id = $input["patient_id"]; 
                $allergy->save();
                
                return json_encode([
                    'data'=> $allergy->id,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
            else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Record saving failed.'
                ]);
            }
        }
    }

    public function allergy_update(Request $request)
    {
        if($request->ajax()){
            $input = $request->all();

            $validation = Validator::make($input, [
                'name' => 'required',
            ]);

            if ($validation->passes()){
                $allergy = Allergy::where('id', $input['allergy_id'])->first();
                $allergy->name = $input["name"];
                $allergy->description = $input["description"];  
                $allergy->save();

                return json_encode([
                    'data'=> $allergy->id,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
            else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Record saving failed.'
                ]);
            }
        }
    }

    public function allergy_destroy(Request $request)
    {
        if($request->ajax()){

            $input = $request->all();
            
            $id = $input['id'];
            
            $data = Allergy::where('id', $id)->first();
            $data_old = $data;
            $data->delete();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    public function get_patient_allergies_data(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            $id = $request->patient_id;
            // get data from products table
            $query = Allergy::whereHas('patient', function ($query) use ($id) {
                $query->where('id', $id);
            });
        
            // Search //input all searchable fields
            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }  
                }   
            });

            //default field for order
            $orderByCol = $columns[$orderColumnIndex]['name'];
           
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {
                $newData[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                    'description' => $value->description,
                    'actions' =>  '<div class="d-flex order-actions">
                        <a data-id="'.$value->id.'" data-name="'.$value->name.'" data-description="'.$value->description.'"
                                onclick="showEditForm(this);"
                                class="btn-primary" style="background-color:#8833ff"><i class="bx bxs-edit"></i></a>
                        <button type="button" onclick="ShowConfirmDeleteForm(' . $value->id . ')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                    </div>',
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }

    public function get_medications($id, $p_id)
    {
        $user = Auth::user();
        $breadCrumb = ['Clinical', 'Tebra Patient Medication'];
        $id = $id;

        $medications = PatientMedication::whereHas('patient', function ($query) use ($p_id) {
            $query->where('id', $p_id);
        })->get();
        
        return view('/division2b/patients/patientMedications', compact('user','breadCrumb', 'id'));
    }

    public function get_patient_medications_data(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            $id = $request->patient_id;
            // get data from products table
            $query = PatientMedication::whereHas('patient', function ($query) use ($id) {
                $query->where('id', $id);
            });
        
            // Search //input all searchable fields
            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }  
                }   
            });

            //default field for order
            $orderByCol = $columns[$orderColumnIndex]['name'];
            
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {
                $newData[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                    'quantity' => $value->quantity,
                    'refills' => $value->refills,
                    'prescribed_on' => $value->prescribed_on,
                    'prescribed_by' => $value->prescribed_by,
                    'store_location' => $value->store_location,
                    'actions' =>  '<div class="d-flex order-actions">
                        <a data-id="'.$value->id.'" data-name="'.$value->name.'" data-quantity="'.$value->quantity.'"
                                data-refills="'.$value->refills.'" data-storelocation="'.$value->store_location.'"
                                data-prescribedon="'.date('Y-m-d h:i:s A', strtotime($value->prescribed_on)).'" data-prescribedby="'.$value->prescribed_by.'"
                                onclick="showEditForm(this);"
                                class="btn-primary" style="background-color:#8833ff"><i class="bx bxs-edit"></i></a>
                        <button type="button" onclick="ShowConfirmDeleteForm(' . $value->id . ')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                    </div>',
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }

    public function medications_store(Request $request)
    {
        if($request->ajax()){
            $helper =  new Helper;
            $input = $request->all();

            $validation = Validator::make($input, [
                'prescribed_on' => 'required',
                'prescribed_by' => 'required',
            ]);

            if ($validation->passes()){
                // Create Item entries
                $check_entry = 0;
                for ($i = 0; $i <= $input['med_count']; $i++) {
                    if (!empty($request->input("drugname$i")&&$request->input("quantity$i")&&$request->input("store_location$i"))) {
                        $med = new PatientMedication();
                        $med->patient_id = $input['patient_id'];
                        $med->name = $input["drugname$i"];
                        $med->quantity = $input["quantity$i"];
                        $med->refills = $input["refills$i"];
                        $med->prescribed_on = date('Y-m-d H:i:s', strtotime($input['prescribed_on']));
                        $med->prescribed_by = $helper->ProperNamingCase($input['prescribed_by']);
                        $med->store_location = $input["store_location$i"];   
                        $med->save();
                        $check_entry = 1;
                    }
                }
                //return no entry of medication
                if($check_entry === 0)
                {
                    $medication_validate = [
                        "medication_holder" => ["Input at least one medication field."],
                        "message" => "Employee saving failed."
                    ];'{"medication_holder":["The prescriber name field is required."]},"message":"Employee saving failed."}';

                    return json_encode(['status'=>'error',
                        'errors'=> $medication_validate,
                        'message'=>'Employee saving failed.']);
                }

                return json_encode([
                    'data'=> $med->id,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
            else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Record saving failed.'
                ]);
            }
        }
    }

    public function medication_update(Request $request)
    {
        if($request->ajax()){
            $helper =  new Helper;
            $input = $request->all();

            $validation = Validator::make($input, [
                'prescribed_on' => 'required',
                'prescribed_by' => 'required',
                'medications' => 'required',
                'quantity' => 'required',
                'store_location' => 'required'
            ]);

            if ($validation->passes()){
                $med = PatientMedication::where('id', $input['med_id'])->first();
                $med->name = $input["medications"];
                $med->quantity = $input["quantity"];
                $med->refills = $input["refills"];
                $med->prescribed_on = date('Y-m-d H:i:s', strtotime($input['prescribed_on']));
                $med->prescribed_by = $input['prescribed_by'];
                $med->store_location = $input["store_location"];   
                $med->save();

                return json_encode([
                    'data'=> $med->id,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
            else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Record saving failed.'
                ]);
            }
        }
    }

    public function medication_destroy(Request $request)
    {
        if($request->ajax()){

            $input = $request->all();
            
            $id = $input['id'];
            
            $data = PatientMedication::where('id', $id)->first();
            $data_old = $data;
            $data->delete();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    public function get_patient_data(Request $request, $id)
    {
        if($request->ajax()){

            $data = $this->profileData($id);

            return json_encode(['data'=> $data]);
        }
    }

    private function profileData($id)
    {
        try {
            //code...
            $profileData = Patient::where('tebra_id',$id)->first();

            return $profileData;
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in PatientController.profileData.'
            ]);
        }
    }

    public function patient_update(Request $request, TebraService $tebraService)
    {
        if($request->ajax()){
            $helper =  new Helper;
            $input = $request->all();

            $validation = Validator::make($input, [
                // 'edit_firstname' => 'required',
                // 'edit_lastname' => 'required',
            ]);

            if ($validation->passes()){
                // $med = PatientMedication::where('id', $input['med_id'])->first();
                // $med->name = $input["medications"];
                // $med->quantity = $input["quantity"];
                // $med->refills = $input["refills"];
                // $med->prescribed_on = date('Y-m-d H:i:s', strtotime($input['prescribed_on']));
                // $med->prescribed_by = $input['prescribed_by'];
                // $med->store_location = $input["store_location"];   
                // $med->save();
                // $data = array(
                //     'id' => $input["patient_id"],
                //     'firstname' => $input["edit_firstname"],
                //     'lastname' => $input["edit_lastname"],
                //     'known_allergies' => ($input['known_allergies'] == true)?"1":"0",
                //     'medication_allergies' => ($input['medication_allergies'] == true)?"1":"0",
                // );

                $patient = Patient::where('tebra_id', $input['patient_id'])->first();
                $patient->known_allergies = ($input['known_allergies'] == true)?"1":"0";
                $patient->medication_allergies = ($input['medication_allergies'] == true)?"1":"0";
                $patient->save();


                //$response = $tebraService->updatePatient($data);

                // echo '<pre>';
                // print_r($response);

                return json_encode([
                    //'data'=> $med->id,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
            else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Record saving failed.'
                ]);
            }
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

            // get data from patients table
            $query = new Patient();

            $query = $query->where('source', 'tebra');

            // Search //input all searchable fields
            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }  
                }   
                $query->orWhereRaw('CONCAT(firstname," ", lastname) like  "%'.$search.'%"');  
                $query->orWhereRaw('CONCAT(lastname," ", firstname) like  "%'.$search.'%"');
            });
            
            $orderByCol =  $columns[$orderColumnIndex]['name'];
            $query = $query->orderBy($orderByCol, $orderBy);

            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();
            //dd($query->toSql());
            $newData = [];
            foreach ($data as $value) {
                $newData[] = [
                    'id' => $value->tebra_id,
                    'firstname' => $value->firstname,
                    'lastname' => $value->lastname,
                    'birthdate' => $value->birthdate,
                    'created_at' => ($value->created_at)?$value->created_at->format('M. d, Y h:iA'):'',
                    'updated_at' => ($value->updated_at)?$value->updated_at->format('M. d, Y h:iA'):'',
                    'address' => $value->address,
                    'city' => $value->city,
                    'state' => $value->state,
                    'zip_code' => $value->zip_code,
                    'phone_number' => $value->phone_number,
                    
                    'actions' =>  '<div class="d-flex order-actions">
                        <button type="button" onclick="ShowEditForm(' . $value->id . ',\'' . $value->known_allergies . '\',\'' . $value->medication_allergies . '\',\'' . $value->birthdate . '\',\'' . $value->address . '\',\'' . $value->city . '\',\'' . $value->state . '\',\'' . $value->zip_code . '\',\'' . $value->phone_number . '\')" class="btn btn-primary btn-sm me-2" ><i class="fa-solid fa-pencil"></i></button>
                        <button type="button" onclick="ShowConfirmDeleteForm(' . $value->id . ', \'' . $value->firstname . ' ' . $value->lastname . '\')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                    </div>',
                ];
            }   

            $total_count = Patient::where('source', 'tebra')->count();
            
            return response()->json(["totalCount" => $total_count, "draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }
}
