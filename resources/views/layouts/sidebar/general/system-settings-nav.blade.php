@canany([array_keys($menuSettingsGroupPermissions, 'user'),array_keys($menuSettingsGroupPermissions, 'role'),array_keys($menuSettingsGroupPermissions, 'rbac')])
<li class="menu-label">System Settings</li>

<!-- System Users -->
<li>
    <a href="javascript:;" class="has-arrow">
        <div class="parent-icon"><i class='bx bx-user' ></i></div>
        <div class="menu-title">System Users</div>
    </a>
    <ul>
        @canany(array_keys($menuSettingsGroupPermissions, 'user'))
            <li>
                <a href="/admin/user"><i class="bx bx-right-arrow-alt"></i>Users</a>
            </li>
        @endcanany
        @canany(array_keys($menuSettingsGroupPermissions, 'role'))
            <li>
                <a href="/admin/role"><i class="bx bx-right-arrow-alt"></i>Roles</a>
            </li>
        @endcanany
        @canany(array_keys($menuSettingsGroupPermissions, 'rbac'))
            <li>
                <a href="/admin/rbac"><i class="bx bx-right-arrow-alt"></i>RBAC</a>
            </li>
        @endcanany
    </ul>
</li>
<!--  -->
<!-- Pharmacy Settings -->
<li>
    <a href="javascript:;" class="has-arrow">
        <div class="parent-icon"><i class='bx bx-store'></i></div>
        <div class="menu-title">Pharmacy Settings</div>
    </a>
    <ul>
        @canany(array_keys($menuSettingsGroupPermissions, 'pharmacy_staff'))
            <li>
                <a href="/admin/divisiontwob/pharmacy"><i class="bx bx-right-arrow-alt"></i>Pharmacy</a>
            </li>
        @endcanany
        @canany(array_keys($menuSettingsGroupPermissions, 'pharmacy_store'))
            <li>
                <a href="/admin/divisiontwob/pharmacy_store"><i class="bx bx-right-arrow-alt"></i>Pharmacy Stores</a>
            </li>
        @endcanany
        @canany(array_keys($menuSettingsGroupPermissions, 'pharmacy_operation'))
            <li>
                <a href="/admin/divisiontwob/pharmacy_operation"><i class="bx bx-right-arrow-alt"></i>Pharmacy Operations</a>
            </li>
        @endcanany
    </ul>  
</li>
<!--  -->
<!-- Patient Support Settings -->
<li>
    <a href="javascript:;" class="has-arrow">
        <div class="parent-icon"><i class='bx bx-store'></i></div>
        <div class="menu-title">Patient Support Settings</div>
    </a>
    <ul>
       
        <li>
            <a onclick="showDefaultAutomationModal({{ request()->id }})"><i class="bx bx-right-arrow-alt"></i>Default Automation</a>
        </li>
        
    </ul>  
</li>
<!--  -->
@endcanany