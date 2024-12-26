
    <li class="sidebar-store-financial-report-nav"> 
        <a class="sidebar-store-financial-report-nav-a has-arrow" href="javascript:;">
            @if($numberOfStorePermissions == 1)
                <div class="parent-icon"><i class='fa-solid fa-book fa-sm me-1'></i></div>
                <div class="menu-title">Finance</div>
            @else
                <i class="fa-solid fa-book me-2"></i>Finance
            @endif
        </a>
        <ul>
            
            @canany(['menu_store.financial_reports.pharmacy_gross_revenue.index', 'menu_store.financial_reports.pharmacy_gross_revenue.create', 'menu_store.financial_reports.pharmacy_gross_revenue.update', 'menu_store.financial_reports.pharmacy_gross_revenue.delete'])
                <li>
                    <a href="/store/financial-reports/{{$menu->id}}/documents/55"><i class="fa-solid fa-file-invoice-dollar ms-2 me-3"></i>Pharmacy Gross Revenue</a>
                </li>
            @endcanany

            @canany(['menu_store.financial_reports.payments_overview.index', 'menu_store.financial_reports.payments_overview.create', 'menu_store.financial_reports.payments_overview.update', 'menu_store.financial_reports.payments_overview.delete'])
                <li>
                    <a href="/store/financial-reports/{{$menu->id}}/documents/56"><i class="fa-solid fa-receipt ms-2 me-3"></i>Payments Overview</a>
                </li>
            @endcanany

            @canany(['menu_store.financial_reports.collected_payments.index', 'menu_store.financial_reports.collected_payments.create', 'menu_store.financial_reports.collected_payments.update', 'menu_store.financial_reports.collected_payments.delete'])
                <li>
                    <a href="/store/financial-reports/{{$menu->id}}/documents/57"><i class="fa-solid fa-hand-holding-dollar ms-2 me-2"></i>Collected Payments</a>
                </li>
            @endcanany

            @canany(['menu_store.financial_reports.gross_revenue_and_cogs.index', 'menu_store.financial_reports.gross_revenue_and_cogs.create', 'menu_store.financial_reports.gross_revenue_and_cogs.update', 'menu_store.financial_reports.gross_revenue_and_cogs.delete'])
                <li>
                    <a href="/store/financial-reports/{{$menu->id}}/documents/58"><i class="fa-solid fa-filter-circle-dollar ms-2 me-2"></i>Gross Revenue and Cogs</a>
                </li>
            @endcanany

            @canany(['menu_store.financial_reports.account_receivables.index', 'menu_store.financial_reports.account_receivables.create', 'menu_store.financial_reports.account_receivables.update', 'menu_store.financial_reports.account_receivables.delete'])
                <li>
                    <a href="/store/financial-reports/{{$menu->id}}/documents/59"><i class="fa-solid fa-money-bill-1-wave ms-2 me-2"></i>Account Receivables</a>
                </li>
            @endcanany

            @canany(['menu_store.financial_reports.eod_reports.index', 'menu_store.financial_reports.eod_reports.create', 'menu_store.financial_reports.eod_reports.update', 'menu_store.financial_reports.eod_reports.delete'])
                <li>
                    <a href="/store/financial-reports/{{$menu->id}}/documents/all/60"><i class="fa-solid fa-calendar-check ms-2 me-2"></i>EOD Reports</a>
                </li>
            @endcanany

            @canany(['menu_store.financial_reports.transaction_receipts.index', 'menu_store.financial_reports.transaction_receipts.create', 'menu_store.financial_reports.transaction_receipts.update', 'menu_store.financial_reports.transaction_receipts.delete'])
                <li>
                    <a href="/store/financial-reports/{{$menu->id}}/documents/all/68"><i class="fa-solid fa-laptop-file ms-2 me-2"></i>Transaction Receipts</a>
                </li>
            @endcanany

        </ul>
    </li>
