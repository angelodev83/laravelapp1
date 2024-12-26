<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function get_data(Request $request)
    {
        if($request->ajax()){
            $role_id = $request['role_id'] ?? null;
            if(!empty($role_id)){
                $role = Role::where('roles.id', $role_id)
                    ->with('permissions')
                    ->first();
                $permissions = $role->permissions;
                $selected = [];
                foreach($permissions as $permission) {
                    $selected[] = $permission['name'];
                }
                $data = [
                    'permissions' => $selected,
                    'all' => Permission::get()
                ];
            } else {
                $data = Permission::get();
            }

            return json_encode([
                'data'=> $data,
            ]);
        }

    }
}
