<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DivisionTwoAB2BController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:division-2a.b2b.index', ['only' => ['index', 'get_data']]);
    }

    public function index()
    {
        $user = Auth::user();

        return view('/division2a/b2b/index', compact('user'));
    }
}
