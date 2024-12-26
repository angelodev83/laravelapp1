<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountingPartnershipReconcillationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:accounting.partnership-reconcillation.index', ['only' => ['index', 'get_data']]);
    }

    public function index()
    {
        $user = Auth::user();

        return view('/accounting/partnershipReconcillation/index', compact('user'));
    }
}
