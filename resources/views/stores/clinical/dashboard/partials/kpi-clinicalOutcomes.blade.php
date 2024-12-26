<style>
    .card-graph{
        padding-bottom: 0px;
        padding-left: 0px;
        padding-right: 0px;
    }

    .bg-custom{
        background-color: #BFF4E8 !important;
        padding: 20px 50px;
        font-size: 20px;
        margin: 30px !important;
        color: #58585A;
    }

    .bg-custom2{
        background-color: #DDDFFF !important;
        padding: 20px 50px;
        font-size: 20px;
        margin: 30px !important;
        color: #58585A;
    }

    .toa-custom{
        color: #58585A;
        background-color: #C7CAFF !important;
        margin: 0 20px;
    }

    h6{
        margin-left: 32px !important;
    }
</style>

<div class="d-flex align-items-center">
    <div class="tab-icon" style="color: #438F9D;"><i class="fa-solid fa-chart-pie font-20 ms-2 me-1"></i>
    </div>
    <h4 style="color: #438F9D;">Clinical Outcomes</h4>
    <div class="row mb-2 ms-auto me-1">
        <div class="col">
            <label label for="search_fromdate" class="form-label">Month Range Start</label>
            <input type='text' readonly id='search_fromdate' class="form-control datepicker" placeholder='From date'>
        </div>
        <div class="col">
            <label label for="search_todate" class="form-label">Month Range End</label>
            <input type='text' readonly id='search_todate' class="form-control datepicker" placeholder='To date'>
        </div>
    </div>
</div>
<!-- 1st card -->
<div class="card ms-2 me-2" style="border: 2px solid #438F9D; padding: 30px;">
    <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-2 mt-3">
        <div class="col">
            <div class="card ms-2 me-2" style="border: 2px solid #438F9D; min-height: 580px;">
                <div class="card-body card-graph">
                    <h6>Total Number of Opportunities</h6>
                    <span class="d-flex justify-content-center badge badge-pill bg-custom ms-auto"></span>
                    <div id="total_opportunities"></div>
                </div>	
        
            </div>
        </div>
        
        <div class="col">
            <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-1">
                <div class="col">
                    <div class="card ms-2 me-2" style="border: 2px solid #438F9D;">
                        <div class="card-body card-graph">
                            <div class="row row-cols-2 row-cols-lg-2 row-cols-xl-2">
                                <div class="col">
                                    <div  class="ms-3">
                                        <p class="mb-0 text-black font-18">
                                            <h6>FFS Count</h6>
                                            <h4></h4>
                                            <!-- <i class="fa-solid fa-up-long text-success"></i><span class="text-success"> 0%</span> -->
                                            <!-- <span>vs last month</span> -->
                                        </p>
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <div id="ffsCountChart" style="min-height: 65px;"></div>
                                </div>
                            </div>
                            
                        </div>	
                    </div>
                </div>
                
                <div class="col">
                    <div class="card ms-2 me-2" style="border: 2px solid #438F9D;">
                        <div class="card-body card-graph">
                            <div class="row row-cols-2 row-cols-lg-2 row-cols-xl-2">
                                <div class="col">
                                    <div  class="ms-3">
                                        <p class="mb-0 text-black font-18">
                                            <h6>CCO Count</h6>
                                            <h4></h4>
                                            <!-- <i class="fa-solid fa-down-long text-danger"></i><span class="text-danger"> 0%</span> -->
                                            <!-- <span>vs last month</span> -->
                                        </p>
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <div id="ccoCountChart" style="min-height: 65px;"></div>
                                </div>
                            </div>
                            
                        </div>	
                    </div>
                </div>

                <div class="col">
                    <div class="card ms-2 me-2" style="border: 2px solid #438F9D;">
                        <div class="card-body card-graph">
                            <div class="row row-cols-2 row-cols-lg-2 row-cols-xl-2">
                                <div class="col">
                                    <div  class="ms-3">
                                        <p class="mb-0 text-black font-18">
                                            <h6>PBM 1 Count</h6>
                                            <h4></h4>
                                            <!-- <i class="fa-solid fa-down-long text-danger"></i><span class="text-danger"> 0%</span> -->
                                            <!-- <span>vs last month</span> -->
                                        </p>
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <div id="pbm1CountChart" style="min-height: 65px;"></div>
                                </div>
                            </div>
                            
                        </div>	
                    </div>
                </div>

                <div class="col">
                    <div class="card ms-2 me-2" style="border: 2px solid #438F9D;">
                        <div class="card-body card-graph">
                            <div class="row row-cols-2 row-cols-lg-2 row-cols-xl-2">
                                <div class="col">
                                    <div  class="ms-3">
                                        <p class="mb-0 text-black font-18">
                                            <h6>PBM 2 Count</h6>
                                            <h4></h4>
                                            <!-- <i class="fa-solid fa-up-long text-success"></i><span class="text-success"> 0%</span> -->
                                            <!-- <span>vs last month</span> -->
                                        </p>
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <div id="pbm2CountChart" style="min-height: 65px;"></div>
                                </div>
                            </div>
                            
                        </div>	
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<!-- 2nd card -->
<div class="card ms-2 me-2" style="border: 2px solid #B834AF; padding: 30px;">
    <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-2 mt-3">
        <div class="col">
            <div class="card ms-2 me-2" style="border: 2px solid #B834AF; min-height: 580px;">
                <div class="card-body card-graph">
                    <h6>Total Number of Patients with Opportunities</h6>
                    <span class="d-flex justify-content-center badge badge-pill bg-custom2 ms-auto"></span>
                    <div id="total_Popportunities"></div>
                </div>	
        
            </div>
        </div>
        
        <div class="col">
            <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-1">
                <div class="col">
                    <div class="card ms-2 me-2" style="border: 2px solid #B834AF;">
                        <div class="card-body card-graph">
                            <div class="row row-cols-2 row-cols-lg-2 row-cols-xl-2">
                                <div class="col">
                                    <div  class="ms-3">
                                        <p class="mb-0 text-black font-18">
                                            <h6>FFS $ Count</h6>
                                            <h4></h4>
                                            <!-- <i class="fa-solid fa-up-long text-success"></i><span class="text-success"> 0%</span> -->
                                            <!-- <span>vs last month</span> -->
                                        </p>
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <div id="ffspCountChart" style="min-height: 65px;"></div>
                                </div>
                            </div>
                            
                        </div>	
                    </div>
                </div>
                
                <div class="col">
                    <div class="card ms-2 me-2" style="border: 2px solid #B834AF;">
                        <div class="card-body card-graph">
                            <div class="row row-cols-2 row-cols-lg-2 row-cols-xl-2">
                                <div class="col">
                                    <div  class="ms-3">
                                        <p class="mb-0 text-black font-18">
                                            <h6>CCO $ Count</h6>
                                            <h4></h4>
                                            <!-- <i class="fa-solid fa-down-long text-danger"></i><span class="text-danger"> 0%</span> -->
                                            <!-- <span>vs last month</span> -->
                                        </p>
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <div id="ccopCountChart" style="min-height: 65px;"></div>
                                </div>
                            </div>
                            
                        </div>	
                    </div>
                </div>

                <div class="col">
                    <div class="card ms-2 me-2" style="border: 2px solid #B834AF;">
                        <div class="card-body card-graph">
                            <div class="row row-cols-2 row-cols-lg-2 row-cols-xl-2">
                                <div class="col">
                                    <div  class="ms-3">
                                        <p class="mb-0 text-black font-18">
                                            <h6>PBM 1 $ Count</h6>
                                            <h4></h4>
                                            <!-- <i class="fa-solid fa-down-long text-danger"></i><span class="text-danger"> 0%</span> -->
                                            <!-- <span>vs last month</span> -->
                                        </p>
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <div id="pbm1pCountChart" style="min-height: 65px;"></div>
                                </div>
                            </div>
                            
                        </div>	
                    </div>
                </div>

                <div class="col">
                    <div class="card ms-2 me-2" style="border: 2px solid #B834AF;">
                        <div class="card-body card-graph">
                            <div class="row row-cols-2 row-cols-lg-2 row-cols-xl-2">
                                <div class="col">
                                    <div  class="ms-3">
                                        <p class="mb-0 text-black font-18">
                                            <h6>PBM 2 $ Count</h6>
                                            <h4></h4>
                                            <!-- <i class="fa-solid fa-up-long text-success"></i><span class="text-success"> 0%</span>
                                            <span>vs last month</span> -->
                                        </p>
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <div id="pbm2pCountChart" style="min-height: 65px;"></div>
                                </div>
                            </div>
                            
                        </div>	
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<!-- 3rd -->
<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-2 mt-3">
    <div class="col">
        <div class="card ms-2 me-2" style="border: 2px solid #C092D6; min-height: 250px;">
            <div class="card-body card-graph">
                <h6>Total Opportunities $ Amount</h6>
                <div class="bg-light-primary p-3 radius-10 text-center mt-3 toa-custom">
                    <h4 class="mb-0 font-weight-bold">$</h4>
                    <p class="mb-0">Total Opportunities $ Amount</p>
                </div>
                <div id="total_opportunities_amount" style="padding-right: 20px;"></div>
            </div>	
    
        </div>
    </div>
    
    <div class="col">
        <div class="card ms-2 me-2" style="border: 2px solid #C092D6; min-height: 250px;">
            <div class="card-body card-graph">
                <h6>Average % Opportunities Completed per Patient</h6>
                <div id="averageOpportunitiesCpp"></div>
            </div>	
    
        </div>
    </div>
</div>
<!-- 4th -->
<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-2 mt-3">
    <div class="col">
        <div class="card ms-2 me-2" style="border: 2px solid #C092D6; min-height: 250px;">
            <div class="card-body card-graph">
                <h6>Total Billed Amount</h6>
                <div class="bg-light-primary p-3 radius-10 text-center mt-3 toa-custom">
                    <h4 class="mb-0 font-weight-bold">$</h4>
                    <p class="mb-0">Total Billed $ Amount</p>
                </div>
                <div id="total_billed_amount" style="padding-right: 20px;"></div>
            </div>	
    
        </div>
    </div>
    
    <div class="col">
        <div class="card ms-2 me-2" style="border: 2px solid #C092D6; min-height: 250px;">
            <div class="card-body card-graph">
                <h6>$ Billed vs. Total Opportunities $ Amount Ratio</h6>
                <div id="billedVsTotalOar"></div>
            </div>	
    
        </div>
    </div>
</div>
                