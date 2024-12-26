<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PharmacyStore;
use App\Models\PharmacyStaff;
use App\Models\Employee;

class DivisionTwoBPharmacyController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:division-2b.pharmacy.index', ['only' => ['index', 'get_data']]);
    }
    
    public function index()
    {
        $user = Auth::user();
        $breadCrumb = ['Pharmacy and Pharmacy Support', 'Pharmacy'];

        $stores = PharmacyStore::all();

        return view('/division2b/pharmacy/index', compact('user', 'breadCrumb', 'stores'));
    }

    public function get_staff_data(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            $query = PharmacyStaff::with('employee', 'store')->where('employee_id', '>', 19);


            // Search //input all searchable fields
            $search = $request->search;
            $query = $query->where(function($query) use ($search){
                $query->whereHas('employee', function($query) use ($search) {
                    $query->where('status', '!=', "Terminated");
                });
            });
            $query = $query->where(function($query) use ($search){
                $query->orWhereHas('employee', function($query) use ($search) {
                    $query->where('firstname', 'like', "%".$search."%")
                          ->orWhere('lastname', 'like', "%".$search."%");
                });
            });
            
            if($request->has('pharmacy_store_id')) {
                $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
            }
            
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {
                $empName = $value->employee ? ($value->employee->firstname . ' ' . $value->employee->lastname) : '';
                $storeCode = $value->store ? ($value->store->code) : '';
                $storeName = $value->store ? ($value->store->name) : '';
                $newData[] = [
                    'id' => $value->id,
                    'pharmacy_store_id' => $value->pharmacy_store_id,
                    'employee_name' => $empName,
                    'store_code' => $storeCode,
                    'store_name' => $storeName,
                    'schedule' => $value->schedule,
                    'actions' =>  '<div class="d-flex order-actions">
                        <button type="button" class="btn btn-primary btn-sm me-2" onclick="showEditStaffForm('.$value->id.','.$value->pharmacy_store_id.','.$value->employee_id.',\'' . addslashes($empName) . '\',\'' . addslashes($value->schedule) . '\');"><i class="fa-solid fa-pencil"></i></button>
                        <button type="button" onclick="ShowConfirmDeleteStaffForm(' . $value->id . ')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                    </div>'
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    public function get_employees(Request $request)
    {
        if($request->ajax()){
            // $data = Employee::doesntHave('pharmacyStaffs')->where('id','!=',1)->get();
            $data = Employee::where('id','>',19)->where("status","!=","Terminated")->orderBy('lastname', 'asc')->orderBy('firstname', 'asc')->get();

            return json_encode([
                'data'=> $data,
            ]);
        }

    }

    public function get_stores(Request $request)
    {
        if($request->ajax()){
            $data = PharmacyStore::all();

            return json_encode([
                'data'=> $data,
            ]);
        }

    }
}
