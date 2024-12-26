<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\PharmacyStaff;
use Illuminate\Support\Facades\DB;

class EmployeeRepository
{
    public function countOnshoreEmployeePerStore() {
        $query = PharmacyStaff::with('employee')
            ->select('pharmacy_store_id', DB::raw('COUNT(employee_id) as count_onshore'))
            ->whereHas('employee', function($query){
                $query->whereNot('status', 'Terminated');
            })
            ->groupBy('pharmacy_store_id')
            ->pluck('count_onshore', 'pharmacy_store_id')->toArray();

        $total = array_sum($query);
    
        $data = [
            'total'     => $total,
            'stores'    => $query
        ];

        return $data;
    }
}