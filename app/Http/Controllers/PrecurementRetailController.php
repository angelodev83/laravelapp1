<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrecurementRetailController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('/precurement/retail/index', compact('user'));
    }
}
