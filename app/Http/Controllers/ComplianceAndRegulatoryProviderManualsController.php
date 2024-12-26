<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplianceAndRegulatoryProviderManualsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('/complianceAndRegulatory/providerManuals/index', compact('user'));
    }
}
