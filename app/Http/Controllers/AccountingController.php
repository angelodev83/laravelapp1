<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $breadCrumb = ['Accounting', ''];
        return view('/accounting/index', compact('user', 'breadCrumb'));
    }
}
