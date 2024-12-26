<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DivisionTwoADataAndReportingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:division-2a.data-and-reporting.index', ['only' => ['index', 'get_data']]);
    }

    public function index()
    {
        $user = Auth::user();

        return view('/division2a/dataAndReporting/index', compact('user'));
    }
}
