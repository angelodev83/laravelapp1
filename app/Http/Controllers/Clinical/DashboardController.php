<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Clinical;
use App\Models\Outcome;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct() {
        // $this->middleware('permission:menu_store.clinical.adherence_report.index');
    }

    public function index($id, $year = null)
    {
        try {
            $this->checkStorePermission($id);

            // $year = empty($year) ? date('Y') : $year;
        
            // $clinicals = Clinical::orderBy('sort')->get();
            
            // $clinical_reports = Clinical::with(['monthly_reports' => function ($query) use ($year, $id) {
            //     $query->where('report_year', $year);
            //     $query->where('pharmacy_store_id', $id);
            // }])->get();
            
            // $years = array_reverse(range(date('Y') - 20, date('Y')));
            
            // $store = PharmacyStore::findOrFail($id);

            $breadCrumb = ['Clinical', 'Dashboard'];

            return view('/stores/clinical/dashboard/index', compact('breadCrumb'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function chart(Request $request)
    {
        if($request->ajax()){
            $start = explode('-', $request->start);
            $end = explode('-', $request->end);
            
            $start_year = $start[0];
            $start_month = $start[1];
            $end_year = $end[0];
            $end_month = $end[1];
            $store = $request->store;
            $date_start = '1/'.$start_month.'/'.$start_year;
            $date_end = '1/'.$end_month.'/'.$end_year;
            $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $date_end)->endOfMonth();
            $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $date_start)->startOfMonth();
            
            // $startOfYear = Carbon::createFromFormat('d/m/Y', '1/1/' . date('Y'))->startOfYear();
            // $endOfYear = Carbon::createFromFormat('d/m/Y', '1/12/' . date('Y'))->endOfYear();
            $now = Carbon::now();
            $year = Carbon::now()->year;
            $startOfMonth = $now->copy()->startOfMonth()->format('Y-m-d');
            $endOfMonth = $now->copy()->endOfMonth()->format('Y-m-d');
            $startOfLastMonth = $now->copy()->subMonth()->startOfMonth()->toDateString();
            $endOfLastMonth = $now->copy()->subMonth()->endOfMonth()->toDateString();

            
            $months_label = [];
            $data = [];

            $clinicals = Clinical::with(['monthly_reports' => function ($query) use ($startDate, $endDate, $store) {
                    $query->whereRaw("STR_TO_DATE(CONCAT('1/', report_month, '/', report_year), '%d/%m/%Y') BETWEEN '$startDate' AND '$endDate'");
                    $query->where('pharmacy_store_id', $store);
                    $query->orderByRaw("STR_TO_DATE(CONCAT('1/', report_month, '/', report_year), '%d/%m/%Y') ASC");
                }])
                ->where('data_type', 'percentage')
                ->get();

            $outcomes = Outcome::whereBetween('date_reported', [
                    $startOfMonth,
                    $endOfMonth,
                ])
                ->where('pharmacy_store_id', $store)
                ->select(
                    DB::raw('SUM(tips_completed) AS tips_completed'),
                    DB::raw('SUM(cmrs_completed) AS cmrs_completed'),
                    DB::raw("CONCAT(ROUND((SUM(cmrs_completed) / SUM(patients)) * 100, 2), '%') AS cmrs_completion_rate"),
                    DB::raw("CONCAT(ROUND((SUM(tips_completed) / SUM(patients)) * 100, 2), '%') AS tips_completion_rate"),
                    DB::raw("ROUND(AVG(mtm_score), 0) AS mtm_average")
                )->first();

            $lastMonthOutcomes = Outcome::whereBetween('date_reported', [
                    $startOfLastMonth, $endOfLastMonth
                ])
                ->where('pharmacy_store_id', $store)
                ->select(
                    DB::raw('SUM(tips_completed) AS tips_completed'),
                    DB::raw('SUM(cmrs_completed) AS cmrs_completed')
                )->first();
            
            $currentTipsCompleted = $outcomes->tips_completed ?? 0;
            $lastMonthTipsCompleted = $lastMonthOutcomes->tips_completed ?? 0;

            if ($lastMonthTipsCompleted > 0) {
                $tipsCompletedDifference = (($currentTipsCompleted - $lastMonthTipsCompleted) / $lastMonthTipsCompleted) * 100;
            } else {
                $tipsCompletedDifference = $currentTipsCompleted > 0 ? 100 : 0;
            }

            $currentCmrsCompleted = $outcomes->cmrs_completed ?? 0;
            $lastMonthCmrsCompleted = $lastMonthOutcomes->cmrs_completed ?? 0;

            if ($lastMonthCmrsCompleted > 0) {
                $cmrsCompletedDifference = (($currentCmrsCompleted - $lastMonthCmrsCompleted) / $lastMonthCmrsCompleted) * 100;
            } else {
                $cmrsCompletedDifference = $currentCmrsCompleted > 0 ? 100 : 0;
            }

            $tipsCompletedIcon = $tipsCompletedDifference >= 0 
                ? 'bx bxs-arrow-from-bottom' 
                : 'bx bxs-arrow-from-top';
            $cmrsCompletedIcon = $cmrsCompletedDifference >= 0 
                ? 'bx bxs-arrow-from-bottom' 
                : 'bx bxs-arrow-from-top';

            $mtm_average = $outcomes->mtm_average;
            switch ($outcomes->mtm_average) {
                case 1:
                    $mtm_percent = 20;
                    $mtm_text = 'Well Below Average';
                    break;
                case 2:
                    $mtm_percent = 40;
                    $mtm_text = 'Below Average';
                    break;
                case 3:
                    $mtm_percent = 60;
                    $mtm_text = 'Average';
                    break;
                case 4:
                    $mtm_percent = 80;
                    $mtm_text = 'Above Average';
                    break;
                case 5:
                    $mtm_percent = 100;
                    $mtm_text = 'Best in Class';
                    break;
                default:
                    $mtm_percent = 0;
                    $mtm_text = '';
                    break;
            }

            $monthlyOutcomes = [];

            for ($month = 1; $month <= 12; $month++) {
                $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
                $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

                $outcome = Outcome::whereBetween('date_reported', [
                        $startOfMonth, $endOfMonth
                    ])
                    ->where('pharmacy_store_id', $store)
                    ->select(
                        DB::raw('SUM(tips_completed) AS tips_completed'),
                        DB::raw('SUM(cmrs_completed) AS cmrs_completed'),
                    )
                    ->first();

                if ($outcome->tips_completed === null) {
                    $monthlyTips[] = null;
                } elseif ($outcome->tips_completed == 0) {
                    $monthlyTips[] = 0;
                } else {
                    $monthlyTips[] = $outcome->tips_completed;
                }

                if ($outcome->cmrs_completed === null) {
                    $monthlyCmrs[] = null;
                } elseif ($outcome->cmrs_completed == 0) {
                    $monthlyCmrs[] = 0;
                } else {
                    $monthlyCmrs[] = $outcome->cmrs_completed;
                }
            }

            
            $clinicals = $clinicals->map(function ($clinical) {
                return [
                    'id' => $clinical->id,
                    'name' => $clinical->name,
                    'color' => $clinical->color,
                    'reports' => $clinical->monthly_reports->map(function ($report) {
                        return [
                            'id' => $report->id,
                            'value' => $report->value,
                            'report_month' => $report->report_month,
                            'goal' => $report->goal,
                        ];
                    })->toArray(),
                ];
            })->toArray();
            
            $monthsBetweenDates = $this->getMonthsBetweenDates($request->start, $request->end);
            $clinical_names = Clinical::where('data_type', 'percentage')->get();
            
            foreach ($clinical_names as $clinical_name) {
                $data[''.$clinical_name->name.''] = [
                    'name' => $clinical_name->name,
                    'color' => $clinical_name->color,
                    'data' => [],
                ];
                // $data['month'] = [];
                
                foreach ($clinicals as $category) {
                    if ($clinical_name->id == $category['id']) {

                        foreach ($monthsBetweenDates as $month) {
                            $foundReportForCurrentMonth = false;
                            
                            foreach ($category['reports'] as $report) {
                                if ($report['report_month'] == $month[0]) {
                                    // Append report to the reports array only if it matches the month
                                    $data[$clinical_name->name]['data'][] = $report['value'];
                                    $foundReportForCurrentMonth = true;
                                    break;
                                }
                            }

                            // If no report is found for the current month, add an empty report entry
                            if (!$foundReportForCurrentMonth) {
                            
                                $data[$clinical_name->name]['data'][] = null;
                            }
                        }

                    }
                }     
            }
            foreach ($monthsBetweenDates as $month) {
                array_push($months_label, $month[1]);
            }
            
            $avgDiabetes = $this->getAvg($data['Diabetes']['data']);
            $avgRasa = $this->getAvg($data['Rasa']['data']);
            $avgCholesterol = $this->getAvg($data['Cholesterol']['data']);
            $avgStatin = $this->getAvg($data['Statin']['data']);
            
            $return_data = array(
                'labels' => $months_label,
                'data' => $data,
                'avg_diabetes' => $avgDiabetes,
                'avg_rasa' => $avgRasa,
                'avg_cholesterol' => $avgCholesterol,
                'avg_statin' => $avgStatin,
                'mtm_percent' => $mtm_percent,
                'mtm_score' => $mtm_average,
                'mtm_text' => $mtm_text,
                'tips_sum' => $outcomes->tips_completed,
                'cmr_sum' => $outcomes->cmrs_completed,
                'tips_icon' => $tipsCompletedIcon,
                'cmr_icon' => $cmrsCompletedIcon,
                'cmr_difference' => round($cmrsCompletedDifference,2),
                'tips_difference' => round($tipsCompletedDifference,2),
                'cmr_data' => $monthlyCmrs,
                'tips_data' => $monthlyTips
            );
            return response()->json($return_data);
        }
        
    }

    public function getMonthsBetweenDates($startDate, $endDate)
    {

        $date1 = strtotime($startDate);
        $date2 = strtotime($endDate);
        $months = [];
        while ($date1 <= $date2) {
            $months[(date('m', $date1) != 10)?str_replace('0', '', date('m', $date1)):str_replace('', '', date('m', $date1) )] = [(date('m', $date1) != 10)?str_replace('0', '', date('m', $date1)):str_replace('', '', date('m', $date1) ), date('M', $date1)];
            $date1 = strtotime('+1 month', $date1);
        }

        return $months;
    }

    private function getAvg($data)
    {
        // Filter out null values and ensure all elements are numeric
        $filteredData = array_filter($data, function($value) {
            return ($value !== null) && is_numeric($value);
        });

        // Calculate the average if the array is not empty
        if (count($filteredData) > 0) {
            $average = array_sum($filteredData) / count($filteredData);
            $roundedAverage = (int) round($average);
            return $roundedAverage;
        } else {
            return 0;
        }
    }
}
