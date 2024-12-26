<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplianceAndRegulatoryLicensureController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('/complianceAndRegulatory/licensure/index', compact('user'));
    }
}
