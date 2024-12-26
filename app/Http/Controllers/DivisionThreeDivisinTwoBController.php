<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DivisionThreeDivisinTwoBController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:division-3.d2b-telebridge.index', ['only' => ['index']]);
    }

    public function index()
    {
        $user = Auth::user();

        return view('/division3/division2b/index', compact('user'));
    }
}
