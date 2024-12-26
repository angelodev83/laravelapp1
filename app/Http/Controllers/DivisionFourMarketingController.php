<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DivisionFourMarketingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:division-4.marketing.index', ['only' => ['index', 'get_data']]);
    }

    public function index()
    {
        $user = Auth::user();

        return view('/division4/marketing/index', compact('user'));
    }
}
