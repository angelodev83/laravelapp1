<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Operations', 'Dashboard'];
            return view('/stores/operations/dashboard/index', compact('breadCrumb'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }
}
