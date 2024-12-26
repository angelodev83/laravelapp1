<style>
    .avg_message_block {
        color: #8833ff;
        background-color: #b4b5f55e;
        border-width: 0px;
        border-radius: 10px;
        margin-top: -31px;
        margin-left: 20%;
        margin-right: 20%;
        margin-bottom: 30px;
    }

    .avg_message_block:hover {
        color: #8833ff;
        background-color: #b4b5f55e;
        border-width: 0px;
        border-radius: 10px;
        margin-top: -31px;
        margin-left: 20%;
        margin-right: 20%;
        margin-bottom: 30px;
    }

    #mtm_average{
	    color: #32393fcf;
		border-width: 0px;
		position: absolute; top: 55%; left: 50%; transform: translate(-50%, -50%);
		margin-top: -8px;
		font-size: calc(1.375rem + 2.5vw);
		/* font-size: 50px; */
	}

    .bg-gradient-outcomes {
        /* background: linear-gradient(to right, rgb(136 51 255 / 57%), rgb(136 51 255 / 81%)) !important; */
        background: linear-gradient(to right, rgb(221, 223, 254), rgb(193, 147, 212)) !important; 
    }

    .sum_avg{
        font-size: 40px;
        color: #32393fcf;
    }

    .sum_avg_text{
        font-size: 12px; 
        font-weight: bolder;
        color: #32393fcf;
    }

    .bx{
        font-weight: bolder;
    }

    .nav-tabs{
        --bs-nav-tabs-border-color: #6610f2;
    }

    .nav-link{
        color: #63bed0;
    }
</style>
<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs nav-primary" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#open-clinical" id="open_clinical" role="tab" aria-selected="true">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa fa-stethoscope font-18 me-1"></i>
                        </div>
                        <div class="tab-title">Clinical</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#open-kpi" role="tab" id="open_kpi" aria-selected="false" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-chart-pie font-18 me-1"></i>
                        </div>
                        <div class="tab-title">Clinical KPI</div>
                    </div>
                </a>
            </li>
        </ul>
        <div class="tab-content py-3">
            <div class="tab-pane fade active show" id="open-clinical" role="tabpanel">
                <div class="row g-2">
                    <div class="col-md-2">
                        <label label for="search_fromdate" class="form-label">Month Range Start</label>
                        <input type='text' readonly id='search_fromdate' class="form-control datepicker" placeholder='From date'>
                    </div>
                    <div class="col-md-2">
                        <label label for="search_todate" class="form-label">Month Range End</label>
                        <input type='text' readonly id='search_todate' class="form-control datepicker" placeholder='To date'>
                    </div>
                </div>
                <div style="height: 250px">
                    <canvas id="lineChart"></canvas>
                </div>
                <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-4">
                    <div class="col"><div class="mt-5" id="diabetesRb"></div></div>
                    <div class="col"><div class="mt-5" id="rasaRb"></div></div>
                    <div class="col"><div class="mt-5" id="cholesterolRb"></div></div>
                    <div class="col"><div class="mt-5" id="statinRb"></div></div>
                </div>
                
            </div>
            <div class="tab-pane fade" id="open-kpi" role="tabpanel">
                @include('stores/clinical/dashboard/partials/kpi-clinicalOutcomes')
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
       
        <div class="tab-content py-3">
            <div class="tab-pane fade active show" id="clinical2" role="tabpanel">
                <div class="d-flex align-items-center">
                    <div class="tab-icon"><i class="fa fa-stethoscope font-18 ms-2 me-1"></i>
                    </div>
                    <h4>Completed Outcomes MTD</h4>
                </div>
                <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-2">
                    <div class="col">
                        <div class="card ms-2 me-2" style="border: 2px solid #D2BDFF;">
                            <div class="card-body">
                                <h4>MTM Performance Score & Metrics</h4>
                                <div id="mtm_rb"></div>
                                <p id="mtm_average"></p>
                            </div>
                            <button class="btn btn-outline-primary btn-lg btn-block avg_message_block" id="avg_message" data-bs-html="true" data-bs-toggle="popover" data-bs-content="0 - No successful intervention completed.<br>
                                1 - Bottom 20% (well below average).<br>
                                2 - 20%-40% (below average).<br>
                                3 - 40%-60% (average).<br>
                                4 - 60%-80% (above average).<br>
                                5 - Top 20% (best in class)." data-bs-original-title="MTM Performance Score Scale" aria-describedby="popover455643">
                            </button>	
                    
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="card radius-10 bg-gradient-outcomes ms-2 me-2">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img src="/source-images/clinical-dashboard/tips.png" width="80" alt="">
                                    <div class="ms-auto text-end">
                                        <!-- <p class="mb-0 text-white font-18"><i id="tips_icon"></i></p> -->
                                        <p class="mb-0 text-white font-20">
                                            <i id="tips_icon"></i><span id="tips_text"></span>
                                            <span>vs last month</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mt-3" style="position: relative;">
                                    <div class="flex-grow-1">
                                        <p class="mb-1 sum_avg_text">TIPS</p>
                                        <h4 class="mb-0 font-weight-bold sum_avg" id="tips_sum"></h4>
                                    </div>
                                    <div id="tipsChart" style="min-height: 65px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card radius-10 bg-gradient-outcomes ms-2 me-2">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img src="/source-images/clinical-dashboard/cmr.png" width="80" alt="">
                                    <div class="ms-auto text-end">
                                        <!-- <p class="mb-0 text-white font-18"><i id="cmr_icon"></i></p> -->
                                        <p class="mb-0 text-white font-20">
                                            <i id="cmr_icon"></i><span id="cmr_text"></span>
                                            <span>vs last month</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mt-3" style="position: relative;">
                                    <div class="flex-grow-1">
                                        <p class="mb-1 sum_avg_text">CMR</p>
                                        <h4 class="mb-0 font-weight-bold sum_avg" id="cmr_sum"></h4>
                                    </div>
                                    <div id="cmrChart" style="min-height: 65px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="tab-pane fade" id="kpi2" role="tabpanel">
                @include('stores/clinical/dashboard/partials/kpi-clinicalPatients')
            </div>
        </div>
    </div>
</div>