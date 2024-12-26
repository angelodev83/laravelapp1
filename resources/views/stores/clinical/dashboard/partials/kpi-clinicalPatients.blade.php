<style>
.highcharts-color-0 {
  fill: #E3CBF8;
  stroke: #E3CBF8;
}

.highcharts-color-1 {
  fill: #FCAE7C;
  stroke: #FCAE7C;
}

.highcharts-color-2 {
  fill: #EE69A;
  stroke: #EE69A;
}

.highcharts-color-3 {
  fill: #B3F5BC;
  stroke: #B3F5BC;
}

.highcharts-color-4 {
  fill: #D6F6FF;
  stroke: #D6F6FF;
}

</style>
<div class="d-flex align-items-center">
    <div class="tab-icon" style="color: #F1237B;"><i class="fa fa-stethoscope font-20 ms-2 me-1"></i>
    </div>
    <h4 style="color: #F1237B;">Clinical Patients</h4>
</div>
<!-- 1st -->
<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-2 mt-3">
    <div class="col">
        <div class="card ms-2 me-2" style="border: 2px solid #C092D6; min-height: 250px;">
            <div class="card-body card-graph">
                <h6>Total Refill Renewal Count and Total Renewed Count</h6>
                <div id="totalRrcAndTotalRc" style="padding-right: 20px;"></div>
            </div>	
    
        </div>
    </div>
    
    <div class="col">
        <div class="card ms-2 me-2" style="border: 2px solid #C092D6; min-height: 250px;">
            <div class="card-body card-graph">
                <h6>Refill Renewed %</h6>
                <div id="refillRenewed"></div>
            </div>	
    
        </div>
    </div>
</div>
<!-- 2nd -->
<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-2 mt-3">
    <div class="col">
        <div class="card ms-2 me-2" style="border: 2px solid #C092D6; min-height: 250px;">
            <div class="card-body card-graph">
                <h6>Pending Renewal</h6>
                <div id="pendingRenewal" style="padding-right: 20px;"></div>
            </div>	
    
        </div>
    </div>
    
    <div class="col">
        <div class="card ms-2 me-2" style="border: 2px solid #C092D6; min-height: 250px;">
            <div class="card-body card-graph">
                <h6>Pending Renewal %</h6>
                <div id="pendingRenewalPercent"></div>
            </div>	
    
        </div>
    </div>
</div>
<!-- 3rd -->
<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-2 mt-3">
    <div class="col">
        <div class="card ms-2 me-2" style="border: 2px solid #C092D6; min-height: 250px;">
            <div class="card-body card-graph">
                <h6>Denied Renewal</h6>
                <div id="deniedRenewal" style="padding-right: 20px;"></div>
            </div>	
    
        </div>
    </div>
    
    <div class="col">
        <div class="card ms-2 me-2" style="border: 2px solid #C092D6; min-height: 250px;">
            <div class="card-body card-graph">
                <h6>Denied Renewal %</h6>
                <div id="deniedRenewalPercent"></div>
            </div>	
    
        </div>
    </div>
</div>
<!-- 4th -->
<div class="row row-cols-1 row-cols-lg-1 row-cols-xl-1 mt-3">
    <div class="col">
        <div class="card ms-2 me-2" style="border: 2px solid #C092D6; min-height: 250px;">
            <div class="card-body card-graph">
                <h6>Common Reasons for Denial</h6>
                <div id="denialReasons" style="padding-right: 20px;"></div>
            </div>	
    
        </div>
    </div>
</div>