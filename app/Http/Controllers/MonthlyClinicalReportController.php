<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\MonthlyClinicalReport;
use App\Models\Clinical;
use App\Models\Store;
use Validator;


class MonthlyClinicalReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:division-3.monthly-clinical-report.index', ['only' => ['index', 'get_data']]);
        $this->middleware('permission:division-3.monthly-clinical-report.create', ['only' => ['add_report']]);
    }

    public function index(Request $request,$year = null, $store = null)
    {
        $user = Auth::user();

        $breadCrumb = ['Division 3', 'Monthly Clinical Report'];
        if ($store !== null) {
            $store = Store::find($store);
        } else {
            $store = Store::first();
        }
        $year = $year ?? $request->input('year', date('Y'));
        
        $clinicals = Clinical::orderBy('sort')->get();
        
        $clinical_reports = Clinical::with(['monthly_reports' => function ($query) use ($year, $store) {
            $query->where('report_year', $year);
            $query->where('store_id', $store->id);
        }])->get();
        
        $years = array_reverse(range(date('Y') - 20, date('Y')));
        $stores = Store::get();

        return view('/division3/monthly_report/index', compact('user','breadCrumb','clinicals','clinical_reports','year','years','store','stores'));
    }

    public function get_stores(Request $request)
    {   
        if($request->ajax()){
            $data = Store::select('id', 'name')->get();

            return response()->json([
                'data'=> $data,
            ]);
        }
    }


    public function add_report(Request $request)
    {

        $user = auth()->check() ? Auth::user() : redirect()->route('login');
       
        $input = $request->all();
 
        $report_validation = Validator::make($input, [
            'report_year' => 'required',
            'store' => 'required',
         ]);

        if ($report_validation->passes())
        {
         
            for ($i = 1; $i <= 7; $i++) {
                if(!empty($request->input('clinical_'. $i))){
                    $count = MonthlyClinicalReport::where('report_year', $request->input('report_year'))
                        ->where('report_month', $request->input('report_month_select'))
                        ->where('clinical_id', $i)
                        ->where('pharmacy_store_id', $request->input('store'))
                        ->count();
                    if($count > 0){
                        $report = MonthlyClinicalReport::where('report_year', $request->input('report_year'))
                        ->where('report_month', $request->input('report_month_select'))
                        ->where('pharmacy_store_id', $request->input('store'))
                        ->where('clinical_id', $i)
                        ->first();
                        $report->report_year = $request->input('report_year');
                        $report->report_month = $request->input('report_month_select');
                        $report->clinical_id = $i;
                        $report->value = $request->input('clinical_' . $i);
                        $report->goal = $request->input('goal_' . $i);
                        $report->pharmacy_store_id = $request->input('store');
                        $report->save();
                    }
                    else{
                        $report = new MonthlyClinicalReport;
                        $report->report_year = $request->input('report_year');
                        $report->report_month = $request->input('report_month_select');
                        $report->clinical_id = $i;
                        $report->value = $request->input('clinical_' . $i);
                        $report->goal = $request->input('goal_' . $i);
                        $report->pharmacy_store_id = $request->input('store');
                        $report->save();
                    }  
                }
            }

            return json_encode([
                'data'=> $report ?? [],
                'status'=>'success',
                'message'=>'Saved.'
            ]); 
        
        }else{

          return json_encode(
            ['status'=>'error',
            'errors'=> $report_validation->errors(),
            'message'=>'Patient saving failed.']);

        }

        return redirect()->back()
            ->withInput()
            ->withErrors($validation)
            ->with('message', 'There were validation errors.');

    }


    public function update_report(Request $request)
    {
        $report = MonthlyClinicalReport::find($request->id);
        
        if ($report) {
            if($request->field == 'value'){
                $report->value = $request->value;
                $report->save();
            }
            else{
                $report->goal = $request->value;
                $report->save();
            }
            
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 404);
        }
    }

    

    
}
