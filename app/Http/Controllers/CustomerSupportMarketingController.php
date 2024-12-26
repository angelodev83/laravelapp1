<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerSupportMarketingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:customer-support.marketing.index', ['only' => ['index', 'get_data']]);
    }

    public function index()
    {
        $user = Auth::user();

        return view('/customerSupport/marketing/index', compact('user'));
    }
}
