<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplianceAndRegulatoryBopController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('/complianceAndRegulatory/bop/index', compact('user'));
    }
}
