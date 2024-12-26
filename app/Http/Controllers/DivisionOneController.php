<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DivisionOneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:division-1.index', ['only' => ['index']]);
    }

    public function index()
    {
        $user = Auth::user();

        return view('/division1/index', compact('user'));
    }
}
