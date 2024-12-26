<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', '600'); //300 seconds = 10 minutes

use App\Http\Helpers\Helper;
use App\Models\Employee;
use App\Models\Oig_Exclusion_List;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ComplianceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $breadCrumb = ['Compliance & Regulatory', 'OIG Check'];
        return view('/cs/compliance/index', compact('user', 'breadCrumb'));
    }

    public function add_employee(Request $request)
    {
        if($request->ajax()){
            
            $helper =  new Helper;
            $input = $request->all();

            $validation = Validator::make($input, [
                'firstname' => 'required|max:30|min:1',
                'lastname' => 'required|max:30|min:1',
                //'email' => 'required|email|unique:employees',
                'position' => 'min:1|max:30',
            ]);

            if ($validation->passes()){
                $emp = new Employee;
                $emp->firstname = $helper->ProperNamingCase($input['firstname']);
                $emp->lastname = $helper->ProperNamingCase($input['lastname']);
                $emp->email = $input['email'];
                @$emp->position = $input['position'];
                $emp->location = $input['location'];
                $emp->initials_random_color = rand(1, 10);
                // Storage::disk('local')->append('file.txt', json_encode(date('Y-m-d H:m:i', strtotime($input['startdate']))));
                if(date('Y-m-d', strtotime($input['startdate'])) != '1970-01-01'){$emp->start_date = date('Y-m-d', strtotime($input['startdate']));}
                if(date('Y-m-d', strtotime($input['enddate'])) != '1970-01-01'){$emp->end_date = date('Y-m-d', strtotime($input['enddate']));}
                $emp->save();

                $newEmp = Employee::find($emp->id);
                $result = Oig_Exclusion_List::where('lastname', $newEmp->lastname)
                        ->where('firstname', $newEmp->firstname)
                        ->where('dob', str_replace('-', '', $newEmp->date_of_birth))
                        ->get();
                if($result->count() > 0){
                    Employee::where("id", $emp->id)->update(["oig_status" => "Match"]);
                }

                return json_encode([
                    'data'=> $emp,
                    'status'=>'success',
                    'message'=>'Employee Saved.']);
           }
           else{
                return json_encode(
                    ['status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Employee saving failed.']);
            }

            
        }
    }

    public function get_data(Request $request)
    {   
        if($request->ajax()){
            // Page Length

            //dd(date("Y-m-d H:i"));
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // get data from products table
            $query = Employee::where('id','>',19);

            // Search //input all searchable fields
            $search = $request->search;
            $query = $query->where(function($query) use ($search){
                $query->orWhere('firstname', 'like', "%".$search."%");
                $query->orWhere('lastname', 'like', "%".$search."%");
                $query->orWhere('email', 'like', "%".$search."%");
                $query->orWhere('position', 'like', "%".$search."%");
                $query->orWhere('status', 'like', "%".$search."%");
                $query->orWhere('location', 'like', "%".$search."%");
                $query->orWhere('start_date', 'like', "%".$search."%");
                $query->orWhere('end_date', 'like', "%".$search."%");
                $query->orWhere('oig_status', 'like', "%".$search."%"); 
                $query->orWhere('updated_at', 'like', "%".$search."%");    
            });

            //default field for order
            $orderByCol = 'id';
        
            //input all orderable fields
            switch($orderColumnIndex){
                case '0':
                    $orderByCol = 'id';
                    break;
                case '1':
                    $orderByCol = 'oig_status';
                    break;
                case '2':
                    $orderByCol = 'lastname';
                    break;
                case '3':
                    $orderByCol = 'firstname';
                    break;
                case '4':
                    $orderByCol = 'email';
                    break;
                case '5':
                    $orderByCol = 'position';
                    break;
                case '6':
                    $orderByCol = 'status';
                    break;
                case '7':
                    $orderByCol = 'location';
                    break;
                case '8':
                    $orderByCol = 'updated_at';
                    break;
                case '9':
                    $orderByCol = 'end_date';
                    break;
            }

            $query = $query
                            // ->where('user_id', '!=', 1)
                           ->where('status', 'Active')
                           ->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {
                if($value->oig_status != "No Match"){
                    $class = "badge bg-danger";
                }
                else{
                    $class="badge bg-success";
                }

                if($value->status == 'Active'){
                    $status_class = 'primary';
                }
                else{
                    $status_class = 'danger';
                }

                $newStartDate = ($value->start_date === null)?'':date('Y-m-d', strtotime($value->start_date));
                $newEndDate = ($value->end_date === null)?'':date('Y-m-d', strtotime($value->end_date));

                $newData[] = [
                    'id' => $value->id,
                    'firstname' => $value->firstname,
                    'lastname' => $value->lastname,
                    'email' => $value->email,
                    'position' => $value->position,
                    'start_date' => ($value->start_date === null)?'':date('Y-m-d', strtotime($value->start_date)),
                    'end_date' => ($value->end_date === null)?'':date('Y-m-d', strtotime($value->end_date)),
                    'updated_at' => ($value->updated_at === null)?'':date('Y-m-d H:i:s', strtotime($value->updated_at)),
                    'oig_status' => '<span class="'.$class.'">'.$value->oig_status.'</span>',
                    'status' => $value->status,
                    'location' => $value->location,
                    'actions' =>  '<div class="d-flex order-actions">
                            <a data-bs-toggle="modal" data-bs-target="#updateEmployee_modal" 
                            data-lastname="'.$value->lastname.'" data-firstname="'.$value->firstname.'" 
                            data-dob="'.$value->date_of_birth.'"
                            data-email="'.$value->email.'" data-position="'.$value->position.'" data-id="'.$value->id.'"
                            data-location="'.$value->location.'" data-status="'.$value->status.'"
                            data-startdate="'.$newStartDate.'" data-enddate="'.$newEndDate.'" data-oigstatus="'.$value->oig_status.'"
                            class="btn-primary" style="background-color:#8833ff"><i class="bx bxs-edit"></i></a>
                            <a onclick="ShowConfirmDeleteForm(' . $value->id . ')" class="btn-danger ms-3" style="background-color:#dc362e"><i class="bx bxs-trash"></i></a>
                        </div>'
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }

    public function delete_employee(Request $request)
    {
        if($request->ajax()){
            $input = $request->all();
            $emp = Employee::find($input['id']);

            //delete user who is assign to specific employee
            if($emp->user_id !=0){
                $user = User::find($emp->user_id);
                $user->delete();
            }

            $emp->delete();

            return json_encode([
                'data'=>$emp,
                'status'=>'success',
                'message'=>'Employee Deleted.']);
        }
    }

    public function update_employee(Request $request)
    {
        if($request->ajax()){
            

            $helper =  new Helper;
            $input = $request->all();
            //Storage::disk('local')->append('file.txt', json_encode($input));
                
            $validation = Validator::make($input, [
                'firstname' => 'required|max:30|min:1',
                'lastname' => 'required|max:30|min:1',
                //'email' => 'required|email|unique:employees,email,'.$input['id'].',id',
                'position' => 'required|min:1|max:30',
            ]);

            if ($validation->passes()){

                $emp = Employee::where('id', $input['id'])->first();
                $emp->firstname = $helper->ProperNamingCase($input['firstname']);
                $emp->lastname = $helper->ProperNamingCase($input['lastname']);
                $emp->email = $input['email'];
                $emp->date_of_birth = $input['date_of_birth'];
                $emp->oig_status = $input['oig_status'];
                $emp->location = $input['location'];
                @$emp->position = $input['position'];

                ($input['status'] == true)?$emp->status = "Active":$emp->status = "Inactive";

                //Storage::disk('local')->append('file.txt', json_encode($input['status']));
                if(date('Y-m-d', strtotime($input['startdate'])) != '1970-01-01'){$emp->start_date = date('Y-m-d', strtotime($input['startdate']));}
                if(date('Y-m-d', strtotime($input['enddate'])) != '1970-01-01'){$emp->end_date = date('Y-m-d', strtotime($input['enddate']));}
                $emp->save();


               
                //recheck OIG status
                if($input['oig_status'] === $input['old_oig_status'])
                {
                    $newEmp = Employee::find($input['id']);
                    $result = Oig_Exclusion_List::where('lastname', $newEmp->lastname)
                            ->where('firstname', $newEmp->firstname)
                            ->where('dob', str_replace('-', '', $newEmp->date_of_birth))
                            ->get();
                    if($result->count() > 0){
                        Employee::where("id", $input['id'])->update(["oig_status" => "Match"]);
                    }
                    else{
                        Employee::where("id", $input['id'])->update(["oig_status" => "No Match"]);
                    }
                }
                

                return json_encode([
                    'data'=> $emp->id,
                    'status'=>'success',
                    'message'=>'Employee Updated.']);
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
