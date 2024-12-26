<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Request $request)
    {
        return view('home');
    }

    public function admin(Request $request)
    {

        exit;
        return view('/admin/index');
    }
    

}
