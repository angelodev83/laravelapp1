<?php

namespace App\Repositories;

use App\Models\AccountReceivable;
use App\Models\CollectedPayment;
use App\Models\GrossRevenueAndCog;
use Illuminate\Support\Facades\DB;

class DataInsightsRepository extends ChartRepository
{
    private $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    private function getDataInsightsDataNames()
    {
        return [
            // ceo dashboard
            'monthly_gross_sales',
            'monthly_gross_profit',
            'monthly_turnaround_time_hours',

            'monthly_revenue_per_employee',
            'monthly_gross_profit_revenue_per_script',
            
            'monthly_collected_payments',

            'monthly_account_receivables',

            // operations dashboard
            'monthly_rx_count'
        ];
    }

    public function getMonthlyGrossRevenueAndCogsByYear($year = null, $params = [])
    {
        if(empty($year)) {
            $year = date('Y');
        }

        $data = GrossRevenueAndCog::select(
                DB::raw('YEAR(completed_on) as year_number'),
                DB::raw('MONTH(completed_on) as month_number'),
                DB::raw('SUM(total_price_submitted) as monthly_gross_sales'),
                DB::raw('SUM(gross_profit) as monthly_gross_profit'),
                DB::raw('SUM(acquisition_cost) as monthly_cogs'),
                DB::raw('SUM(turnaround_time_hours) as monthly_turnaround_time_hours'),
                DB::raw('COUNT(id) as grac_count_row'),
            )
            ->whereYear('completed_on', $year)
            ->whereNotNull('completed_on');

        if(isset($params['pharmacy_store_id'])) {
            if(!empty($params['pharmacy_store_id'])) {
                $data = $data->where('pharmacy_store_id', $params['pharmacy_store_id']);
            }
        }
        
        $data = $data->groupBy(DB::raw('MONTH(completed_on)'))
            ->groupBy(DB::raw('YEAR(completed_on)'))
            ->orderBy('completed_on', 'asc')
            ->get()->keyBy('month_number')->toArray();

        return $data;
    }

    public function getMonthlyColelctedPaymentByYear($year = null, $params = [])
    {
        if(empty($year)) {
            $year = date('Y');
        }

        $data = CollectedPayment::select(
                DB::raw('YEAR(pos_sales_date) as year_number'),
                DB::raw('MONTH(pos_sales_date) as month_number'),
                DB::raw('SUM(paid_amount) as monthly_collected_payments'),
                DB::raw('COUNT(id) as cp_count_row'),
            )
            ->whereYear('pos_sales_date', $year)
            ->whereNotNull('pos_sales_date');
        
        if(isset($params['pharmacy_store_id'])) {
            if(!empty($params['pharmacy_store_id'])) {
                $data = $data->where('pharmacy_store_id', $params['pharmacy_store_id']);
            }
        }

        $data = $data->groupBy(DB::raw('MONTH(pos_sales_date)'))
            ->groupBy(DB::raw('YEAR(pos_sales_date)'))
            ->orderBy('pos_sales_date', 'asc')
            ->get()->keyBy('month_number')->toArray();

        return $data;
    }

    public function getMonthlyAccountReceivableByYear($year = null, $params = [])
    {
        if(empty($year)) {
            $year = date('Y');
        }

        $data = AccountReceivable::select(
                DB::raw('YEAR(as_of_date) as year_number'),
                DB::raw('MONTH(as_of_date) as month_number'),
                DB::raw('SUM(amount_total_balance) as monthly_account_receivables'),
                DB::raw('COUNT(id) as ar_count_row'),
            )
            ->whereYear('as_of_date', $year)
            ->whereNotNull('as_of_date');

        
        if(isset($params['pharmacy_store_id'])) {
            if(!empty($params['pharmacy_store_id'])) {
                $data = $data->where('pharmacy_store_id', $params['pharmacy_store_id']);
            }
        }

        $data = $data->groupBy(DB::raw('MONTH(as_of_date)'))
            ->groupBy(DB::raw('YEAR(as_of_date)'))
            ->orderBy('as_of_date', 'asc')
            ->get()->keyBy('month_number')->toArray();

        return $data;
    }

    public function computeDataInsightsByYear($year = null, $params = [])
    {
        if(empty($year)) {
            $year = date('Y');
        }

        $currentMonth = date('n');
        $previousMonth = $currentMonth-1;

        $dataArray = $this->getMergedDataInsightsData($year, $params);

        $onshoreEmployee = $this->employeeRepository->countOnshoreEmployeePerStore();
        $total_onshore_employees = (int) $onshoreEmployee['total'];

        $data = [];

        $defaultDetails = [
            'raw' => 0,
            'raw_with_comma' => 0,
            'formatted' => 0,
            'percentage_mark' => '',
            'percentage_class' => '',
            'percentage' => 0,
            'count_row' => 0,
            'custom' => []
        ];

        $attributes = $this->getDataInsightsDataNames();
        $countAllPreviousYearsGRAC = $this->getCountAllPreviousYearTotalGRAC((int) $year);

        $categories = [];
        $defaultAttributes = [];
        foreach($attributes as $a)
        {
            $b = str_replace('_', '', ucwords($a, '_'));
            $b = lcfirst($b);
            $categories[$b] = $a;
            $defaultAttributes[$a] = 0;
            $defaultAttributes[$b] = $defaultDetails;
        }

        for($i = 1; $i<=12; $i++) {
            $data[$i] = [
                'year_number' => (int) $year,
                'month_number' => $i
            ];

            array_merge($data[$i], $defaultAttributes);
            
            
            if(isset($dataArray[$i])) {
                $j = $i-1;

                $grac_count_row = isset($dataArray[$i]['grac_count_row']) ? $dataArray[$i]['grac_count_row'] : 0;
                $grac_count_row = (int) $grac_count_row;

                $cp_count_row = isset($dataArray[$i]['cp_count_row']) ? $dataArray[$i]['cp_count_row'] : 0;
                $cp_count_row = (int) $cp_count_row;

                $ar_count_row = isset($dataArray[$i]['ar_count_row']) ? $dataArray[$i]['ar_count_row'] : 0;
                $ar_count_row = (int) $ar_count_row;

                foreach($categories as $category => $cname) 
                {
                    $count_row = 0;
                    $customData = [];

                    $previous_sum = $j==0 ? 0 : $data[$j][$cname];

                    switch($cname) {
                        case 'monthly_revenue_per_employee':
                            $count_row = $grac_count_row;
                            $monthly_gross_sales = $data[$i]['monthly_gross_sales'];
                            $monthly_sum = $monthly_gross_sales/$total_onshore_employees;
                            $p = $this->resolvePercentageMTDData($previous_sum, $monthly_sum);
                            break;
                        case 'monthly_gross_profit_revenue_per_script':
                            $count_row = $grac_count_row;
                            $sum = isset($dataArray[$i]['monthly_gross_profit']) ? $dataArray[$i]['monthly_gross_profit'] : 0;
                            if($sum == 0 || $count_row == 0) {
                                $monthly_sum = $sum;
                            } else {
                                $monthly_sum = $sum/$count_row;
                            }
                            $p = $this->resolvePercentageMTDData($previous_sum, $monthly_sum);
                            break;
                        case 'monthly_turnaround_time_hours':
                            $count_row = $grac_count_row;
                            $sum = isset($dataArray[$i]['monthly_turnaround_time_hours']) ? $dataArray[$i]['monthly_turnaround_time_hours'] : 0;
                            if($sum == 0 || $count_row == 0) {
                                $monthly_sum = $sum;
                                $days = 0;
                            } else {
                                $monthly_sum = $sum/$count_row;
                                $days = $monthly_sum/24;
                            }
                            $customData['days'] = ceil($days);
                            $p = $this->resolvePercentageMTDData($previous_sum, $monthly_sum);
                            break;
                        case 'monthly_collected_payments':
                            $count_row = $cp_count_row;
                            $monthly_sum = isset($dataArray[$i][$cname]) ? $dataArray[$i][$cname] : 0;
                            $p = $this->resolvePercentageMTDData($previous_sum, $monthly_sum);
                            break;
                        case 'monthly_account_receivables':
                            $count_row = $ar_count_row;
                            $monthly_sum = isset($dataArray[$i][$cname]) ? $dataArray[$i][$cname] : 0;
                            $p = $this->resolvePercentageMTDData($previous_sum, $monthly_sum);
                            break;
                        case 'monthly_rx_count':
                            $countAllPreviousYearsGRAC += $grac_count_row;
                            $customData['as_of_total'] = $countAllPreviousYearsGRAC;
                            $count_row = $grac_count_row;
                            $monthly_sum = $grac_count_row;
                            $p = $this->resolvePercentageGrowthAndShrinkageData($countAllPreviousYearsGRAC, $monthly_sum);

                            $customData['p'] = $p;
                            $customData['prev'] = $previous_sum;
                            $customData['curr'] = $monthly_sum;
                            break;
                        default:
                            $count_row = $grac_count_row;
                            $monthly_sum = isset($dataArray[$i][$cname]) ? $dataArray[$i][$cname] : 0;
                            $p = $this->resolvePercentageMTDData($previous_sum, $monthly_sum);
                            break;
                    }

                    $data[$i][$cname] = $monthly_sum;

                    $percentage = $p['percentage'];
                    $percentage_mark = $p['percentage_mark'];
                    $percentage_class = $p['percentage_class'];

                    $data[$i][$category] = [
                        'raw' => round($monthly_sum, 2),
                        'raw_with_comma' => number_format(round($monthly_sum, 2), 2, '.', ','),
                        'formatted' => $this->short_number_format($monthly_sum, 2),
                        'percentage_mark' => $percentage_mark,
                        'percentage_class' => $percentage_class,
                        'percentage' => $this->short_number_format($percentage, 2, true),
                        'count_row' => $count_row,
                        'custom' => $customData
                    ];
                }

            }
        }
        
        return $data;
    }

    private function getMergedDataInsightsData($year = null, $params = [])
    {
        $grossRevenueAndCogsArray = $this->getMonthlyGrossRevenueAndCogsByYear($year, $params);
        $collectedPaymentsArray = $this->getMonthlyColelctedPaymentByYear($year, $params);
        $accountReceivablesArray = $this->getMonthlyAccountReceivableByYear($year, $params); 

        $data = [];

        $attributes = $this->getDataInsightsDataNames();

        $defaultAttributes = [];
        foreach($attributes as $a)
        {
            $defaultAttributes[$a] = 0;
        }

        for($i = 1; $i <= 12; $i++) {
            $data[$i] = [
                'year_number' => (int) $year,
                'month_number' => $i,

                'grac_count_row' => 0,
                'cp_count_row' => 0,
                'ar_count_row' => 0,
            ];

            array_merge($data[$i], $defaultAttributes);

        }

        foreach($grossRevenueAndCogsArray as $val) {
            if(isset($data[$val['month_number']])) {
                $data[$val['month_number']]['monthly_gross_sales'] = $val['monthly_gross_sales'];
                $data[$val['month_number']]['monthly_gross_profit'] = $val['monthly_gross_profit'];
                $data[$val['month_number']]['monthly_turnaround_time_hours'] = $val['monthly_turnaround_time_hours'];
                $data[$val['month_number']]['grac_count_row'] = $val['grac_count_row'];
            }
        }

        foreach($collectedPaymentsArray as $val) {
            if(isset($data[$val['month_number']])) {
                $data[$val['month_number']]['monthly_collected_payments'] = $val['monthly_collected_payments'];
                $data[$val['month_number']]['cp_count_row'] = $val['cp_count_row'];
            }
        }

        foreach($accountReceivablesArray as $val) {
            if(isset($data[$val['month_number']])) {
                $data[$val['month_number']]['monthly_account_receivables'] = $val['monthly_account_receivables'];
                $data[$val['month_number']]['ar_count_row'] = $val['ar_count_row'];
            }
        }
        return $data;
    }

    protected function getCountAllPreviousYearTotalGRAC($year)
    {
        $data = GrossRevenueAndCog::select(
                DB::raw('COUNT(id) as count_row'),
            )
            ->where(DB::raw('YEAR(created_at)'), "<", $year)
            ->whereNotNull('created_at')
            ->first();

        return $data->count_row;
    }
    
}