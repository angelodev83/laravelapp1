<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Employee;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Models\PharmacyStore;
use Spatie\Permission\Models\Permission;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if(isset($employee)) {
            if(strtolower($employee->status) == 'terminated') {
                return Redirect::to('admin/logout');
            }
        }

        // dd(auth()->user()->roles);
        $stores = PharmacyStore::select('id',DB::raw('CONCAT("menu_store.",id) as role_name'))->pluck('role_name', 'id')->all();
        $storePermissions = Permission::where('division_name','menu_store.unique')->pluck('name', 'group_name')->all();
        if(auth()->user()->hasAnyRole($stores) || auth()->user()->hasAnyPermission($storePermissions)) {
            $firstKey = key($stores);
            if(empty($firstKey)) {
                $firstKey = key($storePermissions);
            }
            return Redirect::to('store/bulletin/'.$firstKey.'/dashboard');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function lost()
    {
        $user = auth()->user();

        if($user->hasPermissionTo('executive_dashboard.index')) {
            return redirect('/admin');
        }

        $storePermissions = Permission::where('division_name','menu_store.unique')->pluck('name','group_name');
        
        foreach($storePermissions as $id => $permission) {
            if($user->hasPermissionTo($permission))
            {
                return redirect('/store/bulletin/'.$id.'/dashboard');
            }
        }

        if($user->hasPermissionTo('cnr.oig_check.index'))
        {
            return redirect('/admin/oig_check');
        }

        return redirect('/admin');

    }

}
