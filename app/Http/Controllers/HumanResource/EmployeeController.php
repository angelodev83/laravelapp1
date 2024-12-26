<?php

namespace App\Http\Controllers\HumanResource;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Models\Employee;
use App\Models\Oig_Exclusion_List;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Instantiate a new EmployeeController instance.
     */
    public function __construct()
    {
        $this->middleware('permission:menu_store.hr.employees.create|menu_store.hr.employees.update|menu_store.hr.employees.delete|menu_store.hr.employees.import|menu_store.hr.employees.index');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Human Resource', 'Offshore Employees'];
            return view('/stores/humanResource/employees/index', compact('breadCrumb'));
        } catch (\Exception $e) {
            if($e->getCode() == 403) {
                return response()->view('/errors/403/index', [], 403);
            }
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in Store EmployeeController.index.'
            ]);
        }
    }

    public function onshore($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Human Resource', 'Onshore', 'Employees'];
            return view('/stores/humanResource/onshore/employees/index', compact('breadCrumb'));
        } catch (\Exception $e) {
            if($e->getCode() == 403) {
                return response()->view('/errors/403/index', [], 403);
            }
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in Store EmployeeController.index.'
            ]);
        }
    }

    public function onshoreOrganizationChart($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Human Resource', 'In Pharmacy', 'CTCLUSI Health Services Department'];
            return view('/stores/humanResource/onshore/orgChart/index', compact('breadCrumb'));
        } catch (\Exception $e) {
            if($e->getCode() == 403) {
                return response()->view('/errors/403/index', [], 403);
            }
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in Store EmployeeController.onshoreOrganizationChart.'
            ]);
        }
    }

    public function offshoreOrganizationChart($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Human Resource', 'Central Team', 'CTCLUSI Health Services Department'];
            return view('/stores/humanResource/offshore/orgChart/index', compact('breadCrumb'));
        } catch (\Exception $e) {
            if($e->getCode() == 403) {
                return response()->view('/errors/403/index', [], 403);
            }
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in Store EmployeeController.offshoreOrganizationChart.'
            ]);
        }
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            
            $helper =  new Helper;
            $input = $request->all();

            $validation = Validator::make($input, [
                'firstname' => 'required|max:30|min:1',
                'lastname' => 'required|max:30|min:1',
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

    public function delete(Request $request)
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

    public function udpate(Request $request)
    {
        if($request->ajax()){
            

            $helper =  new Helper;
            $input = $request->all();
            //Storage::disk('local')->append('file.txt', json_encode($input));
                
            $validation = Validator::make($input, [
                'firstname' => 'required|max:30|min:1',
                'lastname' => 'required|max:30|min:1',
                'position' => 'required|min:1|max:30',
            ]);

            if ($validation->passes()){

                $emp = Employee::where('id', $input['id'])->first();
                $emp->firstname = $helper->ProperNamingCase($input['firstname']);
                $emp->lastname = $helper->ProperNamingCase($input['lastname']);
                $emp->email = $input['email'];
                $emp->date_of_birth = $input['date_of_birth'];
                $emp->location = $input['location'];
                @$emp->position = $input['position'];

                ($input['status'] == true)?$emp->status = "Active":$emp->status = "Inactive";

                //Storage::disk('local')->append('file.txt', json_encode($input['status']));
                if(date('Y-m-d', strtotime($input['startdate'])) != '1970-01-01'){$emp->start_date = date('Y-m-d', strtotime($input['startdate']));}
                if(date('Y-m-d', strtotime($input['enddate'])) != '1970-01-01'){$emp->end_date = date('Y-m-d', strtotime($input['enddate']));}
                $emp->save();


               
                // if(date('Y-m-d', strtotime($input['startdate'])) != '1970-01-01'){$emp->start_date = date('Y-m-d', strtotime($input['startdate']));}
                // if(date('Y-m-d', strtotime($input['enddate'])) != '1970-01-01'){$emp->end_date = date('Y-m-d', strtotime($input['enddate']));}

                $newEmp = Employee::find($input['id']);
                $result = Oig_Exclusion_List::where('lastname', $newEmp->lastname)
                        ->where('firstname', $newEmp->firstname)
                        ->get();
                if($result->count() > 0){
                    Employee::where("id", $input['id'])->update(["oig_status" => "Match"]);
                }
                else{
                    Employee::where("id", $input['id'])->update(["oig_status" => "No Match"]);
                }

                return json_encode([
                    'data'=> $result,
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
            $query = Employee::with('pharmacyStaffs')->where('is_test', 0)->whereNot('status','Terminated');

            if($request->has('pharmacy_store_id')) {
                $pharmacy_store_id = $request->pharmacy_store_id;
                $is_offshore = $request->is_offshore;
                if($is_offshore == 0) {
                    $query = $query->where(function($query) use ($pharmacy_store_id){
                        $query->whereHas('pharmacyStaffs', function($query) use ($pharmacy_store_id) {
                            $query->where('pharmacy_store_id', $pharmacy_store_id);
                        });
                    });
                } else {
                    // $query = $query->where(function($query) use ($pharmacy_store_id){
                    //     $query->doesntHave('pharmacyStaffs');
                    // });
                }
                $query = $query->where('is_offshore', $is_offshore);
            }

            // Search //input all searchable fields
            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        if($column['name'] == 'firstname' ) {
                            $query->orWhere("lastname", 'like', "%".$search."%");
                        }
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }  
                }   
                $query->orWhereRaw('CONCAT(firstname," ", lastname) like  "%'.$search.'%"');  
                $query->orWhereRaw('CONCAT(lastname," ", firstname) like  "%'.$search.'%"');
            });

            //default field for order
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];

            if($orderByCol == 'firstname') {
                $query = $query->orderBy($orderByCol, $orderBy)->orderBy('lastname', $orderBy);
            } else {
                $query = $query->orderBy($orderByCol, $orderBy);
            }
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

                $newStartDate = ($value->start_date === null)?'':date('Y-m-d', strtotime($value->start_date));
                $newEndDate = ($value->end_date === null)?'':date('Y-m-d', strtotime($value->end_date));

                $actions = '<div class="d-flex order-actions">';
                if(Auth::user()->can('human-resource.employees.update')) {
                    $actions .= '<a data-bs-toggle="modal" data-bs-target="#updateEmployee_modal" 
                    data-lastname="'.$value->lastname.'" data-firstname="'.$value->firstname.'" 
                    data-email="'.$value->email.'" data-position="'.$value->position.'" data-id="'.$value->id.'"
                    data-location="'.$value->location.'" data-status="'.$value->status.'" data-dob="'.$value->date_of_birth.'"
                    data-startdate="'.$newStartDate.'" data-enddate="'.$newEndDate.'"
                    class="btn-primary" style="background-color:#8833ff"><i class="bx bxs-edit"></i></a>';
                }
                if(Auth::user()->can('human-resource.employees.delete')) {
                    $actions .= '<a onclick="ShowConfirmDeleteForm(' . $value->id . ')" class="btn-danger ms-3" style="background-color:#dc362e"><i class="bx bxs-trash"></i></a>';
                }
                $actions .= '</div>';

                if(!empty($value->image)) {
                    $avatar = '
                        <div class="d-flex">
                            <img src="/upload/userprofile/'.$value->image.'" width="35" height="35" class="rounded-circle" alt="">
                            <div class="flex-grow-1 ms-3 mt-2">
                                <p class="font-weight-bold mb-0">'.$value->firstname.' '.$value->lastname.'</p>
                            </div>
                        </div>
                    ';
                } else {
                    $avatar = '
                        <div class="d-flex">
                            <div class="employee-avatar-'.$value->initials_random_color.'-initials hr-employee" data-id="'.$value->id.'">
                            '.strtoupper(substr($value->firstname, 0, 1)).strtoupper(substr($value->lastname, 0, 1)).'
                            </div>
                            <p class="font-weight-bold mb-0 ms-3 mt-2">'.$value->firstname.' '.$value->lastname.'</p>
                        </div>
                    ';
                }

                $newData[] = [
                    'id' => $value->zen_id,
                    'fullname' => $value->firstname.' '.$value->lastname,
                    'avatar' => $avatar,
                    'firstname' => $value->firstname,
                    'lastname' => $value->lastname,
                    'email' => $value->email,
                    'position' => $value->position,
                    'start_date' => ($value->start_date === null)?'':date('Y-m-d', strtotime($value->start_date)),
                    'end_date' => ($value->end_date === null)?'':date('Y-m-d', strtotime($value->end_date)),
                    'oig_status' => '<span class="'.$class.'">'.$value->oig_status.'</span>',
                    'status' => $value->status,
                    'location' => $value->location,
                    'actions' =>  $actions
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }

    public function import()
    {   
        $absolute_path = str_replace('\\', '/' , public_path());
        DB::statement('LOAD DATA INFILE "'.$absolute_path.'/active_roster.csv"
            INTO TABLE employees
            FIELDS TERMINATED BY \',\'
            ENCLOSED BY \'"\'
            LINES TERMINATED BY \'\n\'
            IGNORE 1 ROWS
            (firstname,lastname,position,email,status,location) SET initials_random_color = FLOOR(RAND() * 10) + 1');

        return "Done";
    }
}
