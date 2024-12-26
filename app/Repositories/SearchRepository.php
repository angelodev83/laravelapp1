<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Interfaces\SearchInterface;

use App\Models\Employee;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\PharmacyStaff;
use App\Models\StoreStatus;
use App\Models\DrugOrder;
use App\Models\SupplyItem;
use App\Models\Tag;
use App\Models\SupportCategory;
use App\Models\Wholesaler;
use App\Models\Clinic;
use App\Models\ClinicalProvider;
use App\Models\Department;
use App\Models\StorePage;
use App\Models\User;

class SearchRepository implements SearchInterface
{
    public function searchEmployee($request)
    {
        $data = Employee::all();
        return $data;
    }

    public function searchMedication($request)
    {
        $data = Medication::all();
        return $data;
    }

    public function searchPatient($request)
    {
        $data = Patient::all();
        return $data;
    }

    public function searchStoreStatus($request)
    {
        $data = new StoreStatus();
        if($request->has('category')) {
            $data = $data->where('category', $request->category);
        }
        $data = $data->orderBy('sort', 'asc')->get();
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }

    public function searchPharmacyStaff($request)
    {
        $data = PharmacyStaff::join('employees', 'employees.id', '=', 'pharmacy_staff.employee_id')
                ->select(DB::raw("DISTINCT employees.id"), "employees.firstname", "employees.lastname", DB::raw("CONCAT(employees.firstname, ' ', employees.lastname) AS name"), 'employees.is_offshore')
                ->where("employees.id", ">", 19)
                ->whereNot("employees.status", "Terminated");
        if($request->has('pharmacy_store_id')) {
            $data = $data->where('pharmacy_staff.pharmacy_store_id', $request->pharmacy_store_id);
        }
        if($request->has('is_offshore')) {
            $data = $data->where('employees.is_offshore', $request->is_offshore);
        }
        if($request->has('term')) {
            $data = $data->where(function($data) use ($request){
                $data = $data->orWhere('employees.firstname', 'like', "%".$request->term."%");
                $data = $data->orWhere('employees.lastname', 'like', "%".$request->term."%");
            });
        }
        if($request->has('limit')) {
            $data = $data->take($request->limit);
        }

        $data = $data->orderBy('employees.lastname','asc')->orderBy('employees.firstname','asc')->get();
        
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }

    public function searchUserEmployee($request)
    {
        $data = Employee::join('users', 'employees.user_id', '=', 'users.id')
                ->leftJoin('pharmacy_staff', 'employees.id', '=', 'pharmacy_staff.employee_id')
                ->select(
                    'employees.*', 
                    DB::raw("CONCAT(employees.firstname, ' ', employees.lastname) AS name"
                    )
                )
                ->where("employees.id", ">", 19)
                ->whereNot("employees.status", "Terminated");
        if($request->has('pharmacy_store_id')) {
            $data = $data->where(function ($data) use ($request){
                $data->orWhere('pharmacy_staff.pharmacy_store_id', $request->pharmacy_store_id);
                $data->orWhereNull('pharmacy_staff.pharmacy_store_id');
            });
        }
        if($request->has('term')) {
            $data = $data->where(function($data) use ($request){
                $data = $data->orWhere('employees.firstname', 'like', "%".$request->term."%");
                $data = $data->orWhere('employees.lastname', 'like', "%".$request->term."%");
                $data = $data->orWhere(DB::raw('CONCAT(employees.firstname," ",employees.lastname)'), 'like', "%".$request->term."%");
                $data = $data->orWhere(DB::raw('CONCAT(employees.lastname,", ",employees.firstname)'), 'like', "%".$request->term."%");
            });
        }
        if($request->has('limit')) {
            $data = $data->take($request->limit);
        }

        $data = $data->orderBy('employees.lastname','asc')->orderBy('employees.firstname','asc')->get();
        
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }

    public function searchDrugOrder($request)
    {
        $data = DrugOrder::with('items.medication','prescription');
        if($request->has('pharmacy_store_id')) {
            $data = $data->where('pharmacy_store_id', $request->pharmacy_store_id);
        }
        if($request->has('term')) {
            $data = $data->where('order_number', 'like', "%".$request->term."%");
        }
        if($request->has('limit')) {
            $data = $data->take($request->limit);
        }

        $data = $data->orderBy('order_number','asc')->get();
        
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }

    public function searchSupplyItem($request)
    {
        $data = SupplyItem::select('*');
        if($request->has('term')) {
            $data = $data->orWhere('item_number', 'like', "%".$request->term."%");
            $data = $data->orWhere('description', 'like', "%".$request->term."%");
            $data = $data->orWhere('model_number', 'like', "%".$request->term."%");
        }
        if($request->has('limit')) {
            $data = $data->take($request->limit);
        }

        $data = $data->orderBy('description','asc')->get();
        
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }

    public function searchTag($request)
    {
        $data = Tag::query();
        
        if($request->has('type')) {
            $data = $data->where('type', $request->type);
        }
        if($request->has('term')) {
            $data = $data->where('name', 'like', "%".$request->term."%");
        }
        if($request->has('limit')) {
            $data = $data->take($request->limit);
        }

        $data = $data->orderBy('name','asc')->get();
        
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }

    public function searchSupportCategory($request)
    {
        $data = SupportCategory::query();
        
        if($request->has('term')) {
            $data = $data->where('name', 'like', "%".$request->term."%");
        }
        if($request->has('limit')) {
            $data = $data->take($request->limit);
        }

        $data = $data->orderBy('name','asc')->get();
        
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }

    public function searchWholesaler($request)
    {
        $data = Wholesaler::query();
        
        if($request->has('term')) {
            $data = $data->where('name', 'like', "%".$request->term."%");
        }
        if($request->has('category')) {
            $data = $data->where('category', $request->category);
        }
        if($request->has('limit')) {
            $data = $data->take($request->limit);
        }

        $data = $data->orderBy('id','asc')->get();
        
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }

    public function searchClinic($request)
    {
        $data = Clinic::query();
        
        if($request->has('term')) {
            $data = $data->where('name', 'like', "%".$request->term."%");
        }
        if($request->has('limit')) {
            $data = $data->take($request->limit);
        }

        $data = $data->orderBy('name','asc')->get();
        
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }

    public function searchPageByParentId($request)
    {
        $data = StorePage::query();
        
        if($request->has('term')) {
            $data = $data->where('name', 'like', "%".$request->term."%");
        }
        if($request->has('parent_id')) {
            $data = $data->where('parent_id', $request->parent_id);
        }
        if($request->has('limit')) {
            $data = $data->take($request->limit);
        }

        $data = $data->orderBy('id','asc')->get();
        
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }

    public function searchClinicalProvider($request)
    {
        $data = ClinicalProvider::query();
        
        if($request->has('term')) {
            $data = $data->where('firstname', 'like', "%".$request->term."%");
            $data = $data->where('lastname', 'like', "%".$request->term."%");
        }

        if($request->has('limit')) {
            $data = $data->take($request->limit);
        }

        $data = $data->orderBy('lastname','asc')->orderBy('firstname','asc') ->get();
        
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }

    public function searchDepartment($request)
    {
        $data = Department::query();
        
        if($request->has('term')) {
            $data = $data->where('name', 'like', "%".$request->term."%");
        }
        if($request->has('limit')) {
            $data = $data->take($request->limit);
        }

        $data = $data->orderBy('id','asc')->get();
        
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }
    
}