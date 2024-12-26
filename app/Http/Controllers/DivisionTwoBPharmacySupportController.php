<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PharmacyOperation;
use App\Models\Employee;

class DivisionTwoBPharmacySupportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:division-2b.pharmacy-support.index', ['only' => ['index', 'get_data']]);
    }

    public function index()
    {
        $user = Auth::user();
        $breadCrumb = ['Division 2B - Pharmacy and Pharmacy Support', 'Pharmacy Support'];

        $operations = PharmacyOperation::all();

        return view('/division2b/pharmacySupport/index', compact('user', 'breadCrumb', 'operations'));
    }

    public function get_employees(Request $request)
    {
        if($request->ajax()){
            // $data = Employee::doesntHave('pharmacySupports')->where('id','!=',1)->get();
            $data = Employee::where('id','!=',1)->where("status","!=","Terminated")->get();

            return json_encode([
                'data'=> $data,
            ]);
        }

    }

    public function get_operations(Request $request)
    {
        if($request->ajax()){
            $data = PharmacyOperation::all();

            return json_encode([
                'data'=> $data,
            ]);
        }

    }
}
