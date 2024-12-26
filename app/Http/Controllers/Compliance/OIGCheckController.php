<?php

namespace App\Http\Controllers\Compliance;

ini_set('max_execution_time', '1800'); //300 seconds = 10 minutes

use App\Models\Employee;
use App\Models\Oig_Exclusion_List;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use App\Interfaces\UploadInterface;
use Illuminate\Support\Facades\Validator;

class OIGCheckController extends Controller
{
    private UploadInterface $uploadRepository;

    public function __construct(UploadInterface $uploadRepository) {
        $this->uploadRepository = $uploadRepository;
        $this->middleware('permission:menu_store.cnr.oig_check.index');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Compliance & Regulation', 'OIG Check'];
            return view('/stores/compliance/oigCheck/index', compact('breadCrumb'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
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
            $query = Employee::with('pharmacyStaffs')->where('id',">",19);

            if($request->has('pharmacy_store_id')) {
                $query->whereHas('pharmacyStaffs', function ($query) use(&$request){
                    $query->where('pharmacy_store_id', $request->pharmacy_store_id);
                });
            }

            if($request->has('is_offshore')) {
                $query = $query->where('is_offshore', $request->is_offshore);
            }

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

    public function downloadOigCsv()
    {
        $file = "https://oig.hhs.gov/exclusions/downloadables/UPDATED.csv";

        // dd(storage_path());
        // Retrieve the file //working remote csv download.. comment for fast testing
        $current = file_get_contents($file);

        $save_name = str_replace('\\', '/' , storage_path())."/oig_hit_list.csv";
        
        file_put_contents($save_name, $current);

        $this->uploadRepository->uploadOig($save_name);
        // $this->loadCsvMysql();
        // $this->updateOigStatus();

        return response()->json([
            'result' => 'done'
        ], 200);
    }

    public function reloadOigCsv()
    {
        $this->loadCsvMysql();
        $this->updateOigStatus();

        return response()->json([
            'status'=>'success',
            'message'=>'Reload OIG Check.'], 200);
    }

    public function updateOigStatus()
    {
        $update_at = DB::Select('SELECT employees.id FROM employees');
        foreach ($update_at as $update_all_id) {
            $emp1 = Employee::find($update_all_id->id);
            $emp1->updated_at = date('Y-m-d H:i:s');
            $emp1->oig_status = "No Match";
            $emp1->save();
        }
        $hit_ids = DB::Select('SELECT employees.id FROM employees
            JOIN oig__exclusion__lists
            ON employees.firstname = oig__exclusion__lists.firstname AND employees.lastname = oig__exclusion__lists.lastname
            AND employees.date_of_birth = oig__exclusion__lists.dob');
        foreach ($hit_ids as $hit_id) {
            $emp = Employee::find($hit_id->id);
            $emp->oig_status = "Match";
            $emp->save();
        }
        
    }

    public function loadCsvMysql()
    {
        $absolute_path = str_replace('\\', '/' , storage_path());

        DB::statement('TRUNCATE table oig__exclusion__lists');
        DB::statement('LOAD DATA INFILE "'.$absolute_path.'/oig_hit_list.csv"
            INTO TABLE oig__exclusion__lists
            FIELDS TERMINATED BY \',\'
            ENCLOSED BY \'"\'
            LINES TERMINATED BY \'\n\'
            IGNORE 1 ROWS
            (lastname,firstname,midname,busname,general,specialty,upin,npi,dob,address,city,state,zip,excltype,excldate,reindate,waiverdate,wvrstate)');

    }


}
