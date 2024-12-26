<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrecurementUbcareController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('/precurement/ubcare/index', compact('user'));
    }
}
