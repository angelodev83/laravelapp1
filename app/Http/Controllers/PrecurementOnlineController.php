<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrecurementOnlineController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('/precurement/online/index', compact('user'));
    }
}
