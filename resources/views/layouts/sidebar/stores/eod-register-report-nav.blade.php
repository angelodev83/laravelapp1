
    <li class="sidebar-store-eod-register-report-nav">
        <a class="sidebar-store-eod-register-report-nav-a has-arrow" href="javascript:;">
            @if($numberOfStorePermissions == 1)
                <div class="parent-icon"><i class='fa-solid fa-calendar-plus fa-xs' ></i></div>
                <div class="menu-title">EOD Register Report</div>
            @else
                <i class="fa-solid fa-calendar-plus me-2"></i>EOD Register Report
            @endif
        </a>
        <ul>
            @canany(['menu_store.eod_register_report.register.index', 'menu_store.eod_register_report.register.create', 'menu_store.eod_register_report.register.update', 'menu_store.eod_register_report.register.delete'])
                <li>
                    <a href="/store/eod-register-report/{{$menu->id}}/register"><i class="fa-solid fa-calendar-check ms-2 me-3"></i>Register</a>
                </li>
            @endcanany
            @canany(['menu_store.eod_register_report.deposit.index', 'menu_store.eod_register_report.deposit.create', 'menu_store.eod_register_report.deposit.update', 'menu_store.eod_register_report.deposit.delete'])
                <li>
                    <a href="/store/eod-register-report/{{$menu->id}}/deposit"><i class="fa-solid fa-file-signature ms-2 me-3"></i>Deposit</a>
                </li>
            @endcanany
        </ul>
    </li>
