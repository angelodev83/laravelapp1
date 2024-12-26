<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3 poppins-regular">
    @can('menu_store.data_insights.gross_revenue_and_cogs.index')
    <div class="col">
        <div class="card radius-10 overflow-hidden border-2 border-bottom-0 border-danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0 text-secondary">Total Revenue MTD</h6>
                        <p class="mb-0 fs-5 fw-bold text-danger">${{number_format($totalRevenueMTD, 2)}}</p>
                    </div>
                    <div class="ms-auto">	
                        <img height="40" src="/source-images/ceo-dashboard/financial-icons/148.png" alt="">
                    </div>
                </div>
            </div>
            <div class="" id="chart1" style="min-height: 65px;"></div>
        <div class="resize-triggers"><div class="expand-trigger"><div style="width: 368px; height: 143px;"></div></div><div class="contract-trigger"></div></div></div>
    </div>
    @endcan
    @can('menu_store.data_insights.collected_payments.index')
    <div class="col">
        <div class="card radius-10 overflow-hidden border-2 border-bottom-0 border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0 text-secondary">Monthly Prescription Volume</h6>
                        <p class="mb-0 fs-5 fw-bold text-primary">
                            <i class="fa-regular fa-calendar"></i> 
                            {{now('America/Los_Angeles')->toFormattedDateString()}}
                            ({{$monthlyPrescriptionVolume}})
                        </p>
                    </div>
                    <div class="ms-auto">	
                    <img height="40" src="/source-images/ceo-dashboard/financial-icons/149.png" alt="">
                    </div>
                </div>
            </div>
            <div class="" id="monthly_prescription_volume_chart" style="min-height: 65px;"></div>
        <div class="resize-triggers"><div class="expand-trigger"><div style="width: 368px; height: 143px;"></div></div><div class="contract-trigger"></div></div></div>
    </div>
    @endcan
    @can('menu_store.data_insights.gross_revenue_and_cogs.index')
    <div class="col">
        <div class="card radius-10 overflow-hidden border-2 border-bottom-0 border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0 text-secondary">RX Daily Count</h6>
                        <p class="mb-0 fs-5 fw-bold text-warning">
                            <i class="fa-regular fa-calendar-days"></i>
                            {{now('America/Los_Angeles')->format('l')}}    
                            ({{$rxDailyCount}})
                        </p>
                    </div>
                    <div class="ms-auto">	
                    <img height="40" src="/source-images/ceo-dashboard/financial-icons/150.png" alt="">
                    </div>
                </div>
            </div>
            <div class="" id="rx_daily_count_chart" style="min-height: 65px;"></div>
        <div class="resize-triggers"><div class="expand-trigger"><div style="width: 368px; height: 143px;"></div></div><div class="contract-trigger"></div></div></div>
    </div>
    @endcan
</div>