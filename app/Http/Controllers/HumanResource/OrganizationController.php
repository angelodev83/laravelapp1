<?php

namespace App\Http\Controllers\HumanResource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:menu_store.hr.organization.index');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Human Resource', 'CTCLUSI - Three Rivers Pharmacy'];
            return view('/stores/humanResource/organization/index', compact('breadCrumb'));
        } catch (\Exception $e) {
            if($e->getCode() == 403) {
                return response()->view('/errors/403/index', [], 403);
            }
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in Store OrganizationController.index.'
            ]);
        }
    }
}
