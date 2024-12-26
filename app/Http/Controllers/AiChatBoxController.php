<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AiChatBoxController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $breadCrumb = ['Pilli Boy AI', 'AI Chat Assistance'];
        return view('/chatbox/index', compact('user', 'breadCrumb'));
    }
}
