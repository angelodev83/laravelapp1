<!--sidebar wrapper -->
<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="/images/mgmt88-logo.png" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <img src="/images/mgmt88-pharmacy.png"  style="width: 100%; margin-top: 5px; margin-left: -30px;">
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-first-page'></i>
        </div>
    </div>
    <!--navigation-->
    
    <ul class="metismenu" id="menu">
        
        @include('layouts/sidebar/general/dashboard-nav')
        @include('layouts/sidebar/stores/index')
        @include('layouts/sidebar/general/body-nav')
        @include('layouts/sidebar/general/system-settings-nav')
        {{-- @include('layouts/sidebar/general/other-nav') --}}

    </ul>
    

    <!--end navigation-->
</div>
<!--end sidebar wrapper -->
