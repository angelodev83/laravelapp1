<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HumanResourcesHubController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $lastMonth = $today->copy()->subDays(30)->format('Y-m-d'); 
        $newEmployees = Employee::where(DB::raw('DATE(created_at)'), '>=', $lastMonth)
                ->whereNot('status', 'Terminated')
                ->orderBy('created_at', 'desc')
                ->get();

        $breadCrumb = ['Human Resource', 'HR Hub'];
        return view('/humanResources/hub/index', compact('breadCrumb', 'newEmployees'));
    }

}
