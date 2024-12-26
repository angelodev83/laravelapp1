<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MonthlyClinicalReport;
use App\Models\Clinical;
use App\Models\Store;
use App\Models\PharmacyStore;
use Validator;

class AdherenceReportController extends Controller
{
    public function __construct() {
        $this->middleware('permission:menu_store.clinical.adherence_report.index');
    }

    public function index($id, $year = null)
    {
        try {
            $this->checkStorePermission($id);

            $year = empty($year) ? date('Y') : $year;
        
            $clinicals = Clinical::orderBy('sort')->get();
            
            $clinical_reports = Clinical::with(['monthly_reports' => function ($query) use ($year, $id) {
                $query->where('report_year', $year);
                $query->where('pharmacy_store_id', $id);
            }])->get();
            
            $years = array_reverse(range(date('Y') - 20, date('Y')));
            
            $store = PharmacyStore::findOrFail($id);

            $breadCrumb = ['Clinical', 'Adherence Report'];

            return view('/stores/clinical/adherenceReport/index', compact('breadCrumb','clinicals','clinical_reports','year','years','store'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function store(Request $request)
    {
       
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

    }
}
