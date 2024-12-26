<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HumanResourcesRecruitmentAndHiringController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:human-resource.recruitment-and-hiring.index', ['only' => ['index']]);
    }

    public function index()
    {
        $user = Auth::user();

        return view('/humanResources/recruitmentAndHiring/index', compact('user'));
    }
}
