<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
    @can('menu_store.data_insights.gross_revenue_and_cogs.index')
    <div class="col">
        <div class="card radius-10 overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">Gross Profit MTD</h6>
                        <p class="mb-0">$ {{$grossRevenue}}</p>
                    </div>
                    <div class="ms-auto">	
                        <i class="fa-solid fa-filter-circle-dollar font-30"></i>
                    </div>
                </div>
            </div>
            <div class="" id="chart1" style="min-height: 65px;"></div>
        <div class="resize-triggers"><div class="expand-trigger"><div style="width: 368px; height: 143px;"></div></div><div class="contract-trigger"></div></div></div>
    </div>
    @endcan
    @can('menu_store.data_insights.collected_payments.index')
    <div class="col">
        <div class="card radius-10 overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">Collected Payments MTD</h6>
                        <p class="mb-0">$ {{$collectedPayments}}</p>
                    </div>
                    <div class="ms-auto">	
                        <i class="fa-solid fa-hand-holding-dollar font-30"></i>
                    </div>
                </div>
            </div>
            <div class="" id="chart2" style="min-height: 65px;"></div>
        <div class="resize-triggers"><div class="expand-trigger"><div style="width: 368px; height: 143px;"></div></div><div class="contract-trigger"></div></div></div>
    </div>
    @endcan
    @can('menu_store.data_insights.gross_revenue_and_cogs.index')
    <div class="col">
        <div class="card radius-10 overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">COGS MTD</h6>
                        <p class="mb-0">$ {{$cogs}}</p>
                    </div>
                    <div class="ms-auto">	
                        <i class="fa-solid fa-money-check-dollar font-30"></i>
                    </div>
                </div>
            </div>
            <div class="" id="chart3" style="min-height: 65px;"></div>
        <div class="resize-triggers"><div class="expand-trigger"><div style="width: 368px; height: 143px;"></div></div><div class="contract-trigger"></div></div></div>
    </div>
    @endcan
    <div class="col d-none">
        <div class="card radius-10 overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">Clinical Revenue</h6>
                        <p class="mb-0">$110,000</p>
                    </div>
                    <div class="ms-auto">	
                        <i class="fa-solid fa-money-bill-trend-up font-30"></i>
                    </div>
                </div>
            </div>
            <div class="" id="chart4" style="min-height: 65px;"></div>
        <div class="resize-triggers"><div class="expand-trigger"><div style="width: 368px; height: 143px;"></div></div><div class="contract-trigger"></div></div></div>
    </div>
</div>