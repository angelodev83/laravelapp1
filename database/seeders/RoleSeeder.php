<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\PharmacyStore;
use App\Models\PharmacyStaff;
use App\Models\User;
use App\Models\Employee;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $password = 'gL774c##FxgD';
        $menuStoreGroupPermissions = Permission::where('division_name', 'menu_store')->pluck('group_name','name')->all();
        $menuGeneralGroupPermissions = Permission::where('division_name','general')->pluck('group_name','name')->all();
        $menuSettingsGroupPermissions = Permission::where('division_name','system_settings')->pluck('group_name','name')->all();
        $menuAllStoresGroupPermissions = Permission::where('division_name','menu_store.unique')->pluck('group_name','name')->all();

        $keysMenuStoreGroupPermissions = array_keys($menuStoreGroupPermissions);
        $keysMenuGeneralGroupPermissions = array_keys($menuGeneralGroupPermissions);
        $keysMenuSettingsGroupPermissions = array_keys($menuSettingsGroupPermissions);
        $keysMenuAllStoresGroupPermissions = array_keys($menuAllStoresGroupPermissions);

        // Menu Store Group Permissions
        $bulletin = array_keys($menuStoreGroupPermissions, 'bulletin');
        $operations = array_keys($menuStoreGroupPermissions,'operations');
        $clinical = array_keys($menuStoreGroupPermissions,'clinical');
        $procurement = array_keys($menuStoreGroupPermissions,'procurement');
        $financial_reports = array_keys($menuStoreGroupPermissions,'financial_reports');
        $cnr = array_keys($menuStoreGroupPermissions,'cnr');
        $patient_support = array_keys($menuStoreGroupPermissions,'patient_support');
        $escalation = array_keys($menuStoreGroupPermissions,'escalation');
        $sop = array_keys($menuStoreGroupPermissions,'sop');

        // Menu General Group Permissions
        $accounting = array_keys($menuGeneralGroupPermissions, 'accounting');
        $hr = array_keys($menuGeneralGroupPermissions, 'hr');
        $compliance = array_keys($menuGeneralGroupPermissions, 'cnr');

        // Menu Settings Group Permissions
        $user = array_keys($menuSettingsGroupPermissions, 'user');
        $role = array_keys($menuSettingsGroupPermissions, 'role');
        $rbac = array_keys($menuSettingsGroupPermissions, 'rbac');
        $pharmacy_staff = array_keys($menuSettingsGroupPermissions, 'pharmacy_staff');
        $pharmacy_store = array_keys($menuSettingsGroupPermissions, 'pharmacy_store');
        $pharmacy_operation = array_keys($menuSettingsGroupPermissions, 'pharmacy_operation');

        $insertRoles = Role::insertOrIgnore([
            ['name' => 'super-admin', 'display_name' => 'Super Admin', 'guard_name' => 'web', 'description' => 'Can access ALL PAGES - exempted to all rbac'],
            ['name' => 'admin', 'display_name' =>  'Administrator', 'guard_name' => 'web', 'description' => 'Can access all pages'],
            ['name' => 'accountant', 'display_name' => 'Accountant', 'guard_name' => 'web', 'description' => 'Can access all pages under accounting menu'],
            ['name' => 'human-resource', 'display_name' => 'HR Personnel', 'guard_name' => 'web', 'description' => 'Can access all pages under human resource'],
            ['name' => 'compliance', 'display_name' => 'Compliance', 'guard_name' => 'web', 'description' => 'Limited access to pharmacy pages only'],
            ['name' => 'pharmacy-super-admin', 'display_name' => 'Pharmacy Super Admin', 'guard_name' => 'web', 'description' => 'Can access ALL Store Pages'],
            ['name' => 'pharmacist', 'display_name' => 'Pharmacy User', 'guard_name' => 'web', 'description' => 'Can access Default pharmacy store pages only'],
            ['name' => 'technician', 'display_name' => 'Technician', 'guard_name' => 'web', 'description' => 'Limited access to pharmacy pages only'],
            ['name' => 'lead-technician', 'display_name' => 'Lead Technician', 'guard_name' => 'web', 'description' => 'Limited access to pharmacy pages only'],
            ['name' => 'pcc', 'display_name' => 'PCC', 'guard_name' => 'web', 'description' => 'Limited access to pharmacy pages only'],
            ['name' => 'clerk', 'display_name' => 'Clerks', 'guard_name' => 'web', 'description' => 'Limited access to pharmacy pages only'],
            ['name' => 'finance', 'display_name' => 'Finance', 'guard_name' => 'web', 'description' => 'Limited access to pharmacy pages only'],
            ['name' => 'physician', 'display_name' => 'Physician', 'guard_name' => 'web', 'description' => 'Limited access to pharmacy pages only'],
            ['name' => 'procurement', 'display_name' => 'Procurement', 'guard_name' => 'web', 'description' => 'Limited access to pharmacy pages only']
        ]);

        // set permissions to roles
        $defaultStorePages = array_merge($bulletin, $operations, $escalation, $sop);
        
        $store1 = 'menu_store.1';
        $rolePermissions = [
            'admin' => array_merge($keysMenuStoreGroupPermissions
                    , $keysMenuGeneralGroupPermissions
                    , $keysMenuSettingsGroupPermissions
                    , $keysMenuAllStoresGroupPermissions)
            ,
            'accountant' => $accounting,
            'human-resource' => $hr,
            'compliance' => $compliance,
            'pharmacy-super-admin' => array_merge($keysMenuStoreGroupPermissions, $keysMenuAllStoresGroupPermissions),
            'pharmacist' => array_merge($bulletin, $operations, $escalation, $sop, [$store1]),
            'technician' => array_merge($bulletin, $operations, $procurement, $cnr, $patient_support, $escalation, $sop, [$store1]),
            'lead-technician' => array_merge($bulletin, $operations, $procurement, $cnr, $patient_support, $escalation, $sop, [$store1]),
            'pcc' => array_merge($bulletin, $operations, $clinical, $cnr, $patient_support, $escalation, $sop, [$store1]),
            'clerk' => array_merge($bulletin, $operations, $procurement, $cnr, $patient_support, $escalation, $sop, [$store1]),
            'finance' => array_merge($bulletin, $operations, $procurement, $financial_reports, $cnr, $patient_support, $escalation, $sop, [$store1]),
            'physician' => array_merge($bulletin, $operations, $clinical, $cnr, $patient_support, $escalation, $sop, [$store1]),
            'procurement' => array_merge($bulletin, $operations, $procurement, $cnr, $escalation, $sop, [$store1])
        ];

        $stores = PharmacyStore::all();
        $pharmacyAdminRoles = [];
        foreach($stores as $store) {
            $name = 'pharmacy-admin.'.$store->id;

            $rolePermissions[$name] = array_merge($keysMenuStoreGroupPermissions, ['menu_store.'.$store->id]);
            $pharmacyAdminRoles[$name] = $store;

            Role::insertOrIgnore([
                ['name' => $name, 'display_name' => $store->name.' Admin', 'guard_name' => 'web', 'description' => 'Can access ALL PAGES under Pharmacy Store: '.$store->name],
            ]);
        }

        $pharmacyRoles = Role::all();

        /**
         * DEFAULT ROLES
         */

        User::truncate();
        Employee::truncate();

        $users = [
            'super-admin' => ['firstname' => 'Super', 'lastname' => 'Admin', 'username' => 'superadmin'],
            'admin' => ['firstname' => 'Application', 'lastname' => 'Admin', 'username' => 'admin'],
            'pharmacy-super-admin' => ['firstname' => 'Pharmacy', 'lastname' => 'Super Admin', 'username' => 'pharmacysuperadmin'],
        ];
        foreach($pharmacyRoles as $role) {
            if(isset($pharmacyAdminRoles[$role->name])) {
                $username = strtolower($pharmacyAdminRoles[$role->name]['code']).'admin';
                $firstname = $pharmacyAdminRoles[$role->name]['code'];
                $lastname = 'Admin';
            } else {
                $username = isset($users[$role->name]) ? $users[$role->name]['username'] : $role->name;
                $firstname = isset($users[$role->name]) ? $users[$role->name]['firstname'] : $role->display_name;
                $lastname = isset($users[$role->name]) ? $users[$role->name]['lastname'] : 'User';
            }

            $user = User::create([
                'name' => $username, 
                'email' => $username.'@tinrx.com',
                'password' => Hash::make($password),
                'role_id' => $role->id,
                'type_id' => 1,
            ]);
            $emp = new Employee();
            $emp->firstname = $firstname;
            $emp->lastname = $lastname;
            $emp->user_id = $user->id;
            $emp->initials_random_color = rand(1, 10);
            $emp->save();
            $user->assignRole($role->name);


            if(isset($rolePermissions[$role->name])) {
                $permissions = $rolePermissions[$role->name];
                if(in_array($store1, $permissions)) {
                    $staff = new PharmacyStaff();
                    $staff->pharmacy_store_id = 1;
                    $staff->employee_id = $emp->id;
                    $staff->save();
                }
                foreach($permissions as $pname) {
                    $permission = Permission::findOrCreate($pname);
                    $role->givePermissionTo($permission);
                }
            }
        }

    }
}
