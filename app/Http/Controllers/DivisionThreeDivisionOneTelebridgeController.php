<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DivisionThreeDivisionOneTelebridgeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:division-3.d1-telebridge.index', ['only' => ['index']]);
    }

    public function index()
    {
        $user = Auth::user();

        return view('/division3/division1Telebridge/index', compact('user'));
    }
}
