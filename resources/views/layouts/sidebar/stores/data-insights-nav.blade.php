
    <li class="sidebar-store-data-insight-nav"> 
        <a class="sidebar-store-data-insight-nav-a has-arrow" href="javascript:;">
            @if($numberOfStorePermissions == 1)
                <div class="parent-icon"><i class='fa-solid fa-chart-line fa-xs' ></i></div>
                <div class="menu-title">Data Insights</div>
            @else
                <i class="fa-solid fa-chart-line me-2"></i>Data Insights
            @endif
        </a>
        <ul>
            
            @canany(['menu_store.data_insights.pgr.index', 'menu_store.data_insights.pgr.create', 'menu_store.data_insights.pgr.update', 'menu_store.data_insights.pgr.delete'])
                <li>
                    <a href="/store/data-insights/{{$menu->id}}/pgr"><i class="fa-solid fa-file-invoice-dollar ms-2 me-3"></i>Pharmacy Gross Revenue</a>
                </li>
            @endcanany

            @canany(['menu_store.data_insights.payments_overview.index', 'menu_store.data_insights.payments_overview.create', 'menu_store.data_insights.payments_overview.update', 'menu_store.data_insights.payments_overview.delete'])
                <li>
                    <a href="/store/data-insights/{{$menu->id}}/payments-overview"><i class="fa-solid fa-receipt ms-2 me-3"></i>Payments Overview</a>
                </li>
            @endcanany

            @canany(['menu_store.data_insights.collected_payments.index', 'menu_store.data_insights.collected_payments.create', 'menu_store.data_insights.collected_payments.update', 'menu_store.data_insights.collected_payments.delete'])
                <li>
                    <a href="/store/data-insights/{{$menu->id}}/collected-payments"><i class="fa-solid fa-hand-holding-dollar ms-2 me-2"></i>Collected Payments</a>
                </li>
            @endcanany

            @canany(['menu_store.data_insights.gross_revenue_and_cogs.index', 'menu_store.data_insights.gross_revenue_and_cogs.create', 'menu_store.data_insights.gross_revenue_and_cogs.update', 'menu_store.data_insights.gross_revenue_and_cogs.delete'])
                <li>
                    <a href="/store/data-insights/{{$menu->id}}/gross-revenue-and-cogs"><i class="fa-solid fa-filter-circle-dollar ms-2 me-2"></i>Completed Sales</a>
                </li>
            @endcanany

            @canany(['menu_store.data_insights.account_receivables.index', 'menu_store.data_insights.account_receivables.create', 'menu_store.data_insights.account_receivables.update', 'menu_store.data_insights.account_receivables.delete'])
                <li>
                    <a href="/store/data-insights/{{$menu->id}}/account-receivables"><i class="fa-solid fa-money-bill-1-wave ms-2 me-2"></i>Account Receivables</a>
                </li>
            @endcanany

            @canany(['menu_store.data_insights.gross_sales.index', 'menu_store.data_insights.gross_sales.create', 'menu_store.data_insights.gross_sales.update', 'menu_store.data_insights.gross_sales.delete'])
                <li>
                    <a href="/store/data-insights/{{$menu->id}}/gross-sales"><i class="fa-solid fa-chart-gantt ms-2 me-2"></i>Gross Sales</a>
                </li>
            @endcanany

        </ul>
    </li>
