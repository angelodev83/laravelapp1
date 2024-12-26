<?php

namespace App\Repositories;

class ChartRepository
{
    protected function resolvePercentageGrowthAndShrinkageData($as_of_total_current_month, $monthly_sum, $key = null)
    {
        $percentage = 0;
        if($as_of_total_current_month != 0) {
            $percentage = ($monthly_sum/$as_of_total_current_month)*100;
        }

        $percentage = (int) $percentage;

        $percentage_mark = '';
        $percentage_class = '';

        if($percentage > 0) {
            $percentage_mark = 'arrow-up';
            $percentage_class = 'success';
        }
        if($percentage < 0) {
            $percentage_mark = 'arrow-down';
            $percentage_class = 'danger';
        }
        if(($as_of_total_current_month-$monthly_sum) == $monthly_sum) {
            $percentage_mark = 'arrow-right-arrow-left';
            $percentage_class = 'secondary';
        }

        $data = [
            'percentage' => $percentage,
            'percentage_mark' => $percentage_mark,
            'percentage_class' => $percentage_class,
        ];

        if(!empty($key)) {
            return $data[$key];
        }
        return $data;
    }

    protected function resolvePercentageMTDData($previous_sum, $monthly_sum, $key = null)
    {
        // ((current_month-previous_month)/previous_month)*100
        $percentage = $previous_sum > 0 ? (($monthly_sum-$previous_sum)/$previous_sum)*100 : 0;

        $percentage_mark = '';
        $percentage_class = '';

        if($percentage > 0) {
            $percentage_mark = 'arrow-up';
            $percentage_class = 'success';
        }
        if($percentage < 0) {
            $percentage_mark = 'arrow-down';
            $percentage_class = 'danger';
        }
        if($previous_sum == $monthly_sum) {
            $percentage_mark = 'arrow-right-arrow-left';
            $percentage_class = 'secondary';
        }

        $data = [
            'percentage' => $percentage,
            'percentage_mark' => $percentage_mark,
            'percentage_class' => $percentage_class,
        ];

        if(!empty($key)) {
            return $data[$key];
        }
        return $data;
    }

    protected function custom_number_format($number, $decimals = 2, $absolute = false) {
        if($absolute == true) {
            $number = abs($number);
        }

        $formatted_number = number_format($number, $decimals, '.', ',');
    
        // Check if the formatted number ends with ".00" or ".0" (for 2 or 1 decimal places)
        if (strpos($formatted_number, '.00') !== false) {
            // Remove the decimal part if it's ".00" or ".0"
            $formatted_number = str_replace('.00', '', $formatted_number);
        }
        return $formatted_number;
    }

    protected function short_number_format($number, $decimal = 2, $absolute = false)
    {
        if($absolute == true) {
            $number = abs($number);
        }

        if ($number >= 1000000) {
            // Convert to millions
            $formatted = number_format($number / 1000000, $decimal) . 'M';
        } elseif ($number >= 1000) {
            // Convert to thousands
            $formatted = number_format($number / 1000, $decimal) . 'K';
        } else {
            // No conversion needed
            $formatted = number_format($number, $decimal);
        }

        if (strpos($formatted, '.00') !== false) {
            $formatted = str_replace('.00', '', $formatted);
        }
    
        return $formatted;
    }
}