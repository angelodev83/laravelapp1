<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplianceAndRegulatoryAuditsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('/complianceAndRegulatory/audits/index', compact('user'));
    }
}
