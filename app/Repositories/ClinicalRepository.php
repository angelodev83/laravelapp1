<?php

namespace App\Repositories;

use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class ClinicalRepository extends ChartRepository
{
    private function getClinicalDataNames()
    {
        return [
            'monthly_patient_growth_and_shrinkage'
        ];
    }

    public function getMonthlyPatientsGrowthByYear($year = null, $params = [])
    {
        if(empty($year)) {
            $year = date('Y');
        }

        $data = Patient::select(
                DB::raw('YEAR(created_at) as year_number'),
                DB::raw('MONTH(created_at) as month_number'),
                DB::raw('COUNT(id) as pg_count_row'),
            )
            ->whereYear('created_at', $year)
            ->whereNotNull('created_at');
        
        // if(isset($params['pharmacy_store_id'])) {
        //     if(!empty($params['pharmacy_store_id'])) {
        //         $data = $data->where('pharmacy_store_id', $params['pharmacy_store_id']);
        //     }
        // }

        $data = $data->groupBy(DB::raw('MONTH(created_at)'))
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->orderBy('created_at', 'asc')
            ->get()->keyBy('month_number')->toArray();

        return $data;
    }

    public function computeClinicalByYear($year = null, $params = [])
    {
        if(empty($year)) {
            $year = date('Y');
        }

        $currentMonth = date('n');
        $previousMonth = $currentMonth-1;

        $dataArray = $this->getMergedClinicalData($year, $params);

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

        $attributes = $this->getClinicalDataNames();

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


        $patientsTotalAsOfGrowthAndShrinkage = $this->getCountAllPreviousYearTotalPatients((int) $year);
        for($i = 1; $i<=12; $i++) {
            $data[$i] = [
                'year_number' => (int) $year,
                'month_number' => $i
            ];

            array_merge($data[$i], $defaultAttributes);
            
            
            if(isset($dataArray[$i])) {
                $j = $i-1;

                $pg_count_row = isset($dataArray[$i]['pg_count_row']) ? $dataArray[$i]['pg_count_row'] : 0;
                $pg_count_row = (int) $pg_count_row;

                foreach($categories as $category => $cname) 
                {
                    $absolutePercent = true;
                    $count_row = $pg_count_row;
                    $customData = [];

                    $monthly_sum = isset($dataArray[$i][$cname]) ? $dataArray[$i][$cname] : 0;

                    $data[$i][$cname] = $monthly_sum;
                    $previous_sum = $j==0 ? 0 : $data[$j][$cname];

                    $p = $this->resolvePercentageGrowthAndShrinkageData($previous_sum, $monthly_sum);
                    $percentage = $p['percentage'];
                    $percentage_mark = $p['percentage_mark'];
                    $percentage_class = $p['percentage_class'];

                    if($cname == 'monthly_patient_growth_and_shrinkage') {
                        $patientsTotalAsOfGrowthAndShrinkage += $monthly_sum;
                        
                        $customData['as_of_total'] = $patientsTotalAsOfGrowthAndShrinkage;
                        $absolutePercent = false;

                        $p = $this->resolvePercentageGrowthAndShrinkageData($patientsTotalAsOfGrowthAndShrinkage, $monthly_sum);
                        $percentage = $p['percentage'];
                        $percentage_mark = $p['percentage_mark'];
                        $percentage_class = $p['percentage_class'];
                    }

                    $data[$i][$category] = [
                        'raw' => round($monthly_sum, 2),
                        'raw_with_comma' => number_format(round($monthly_sum, 2), 2, '.', ','),
                        'formatted' => $this->short_number_format($monthly_sum, 2),
                        'percentage_mark' => $percentage_mark,
                        'percentage_class' => $percentage_class,
                        'percentage' => $this->short_number_format($percentage, 2, $absolutePercent),
                        'count_row' => $count_row,
                        'custom' => $customData
                    ];
                }

            }
        }
        
        return $data;
    }

    private function getMergedClinicalData($year = null, $params = [])
    {
        $patientGrowthArray = $this->getMonthlyPatientsGrowthByYear($year, $params);

        $data = [];

        $attributes = $this->getClinicalDataNames();

        $defaultAttributes = [];
        foreach($attributes as $a)
        {
            $defaultAttributes[$a] = 0;
        }

        for($i = 1; $i <= 12; $i++) {
            $data[$i] = [
                'year_number' => (int) $year,
                'month_number' => $i,

                'pg_count_row' => 0,
                // 'ps_count_row' => 0,
            ];

            array_merge($data[$i], $defaultAttributes);

        }

        foreach($patientGrowthArray as $val) {
            if(isset($data[$val['month_number']])) {
                $data[$val['month_number']]['monthly_patient_growth_and_shrinkage'] = $val['pg_count_row'];
                $data[$val['month_number']]['pg_count_row'] = $val['pg_count_row'];
            }
        }

        return $data;
    }

    private function getCountAllPreviousYearTotalPatients(int $year)
    {
        $data = Patient::select(
            DB::raw('COUNT(id) as count_row'),
        )
        ->where(DB::raw('YEAR(created_at)'), "<", $year)
        ->whereNotNull('created_at')
        ->first();

        return $data->count_row;
    }
    
}