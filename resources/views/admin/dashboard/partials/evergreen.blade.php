<div class="card">
    <div class="card-body">
        <div class="row pb-2">
            <div class="col-md-8">
                <h6 class="text-custom-evergreen">
                    <i class="fa fa-chart-column me-2"></i>
                    Evergreen Metrics
                </h6>
            </div>
            <div class="col-md-4 pe-4 text-end">
                <select id="pharmacy_store_id" class="form-select" onchange="filterStore()">
                    <option value="">-- All Stores --</option>
                    @foreach ($menuStores as $store)
                        <option value="{{$store->id}}">{{$store->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- row starts -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 mt-2 mx-1">
            <!-- gross sales -->
            <div class="col">
                <div id="evergreen-widget-gross-sales" class="card radius-15 bg-gradient-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0 text-black">Gross Sales (Billed)</h6>
                                <h4 class="my-1 text-black">$ 0</h4>
                                <p class="mb-0 font-13 text-black"><span class="text-secondary">--%</span> <span>from last month</span></p>
                            </div>
                            <div class="widgets-icons text-success ms-auto">
                                {{-- <i class="bx bxs-wallet"></i> --}}
                                <img width="45" height="45" src="/source-images/ceo-dashboard/evergreen/grossSales.png" alt="Gross Sales Icon">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- collected payments -->
            <div class="col">
                <div id="evergreen-widget-collected-payments" class="card radius-15 bg-gradient-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0 text-black">Collected Payments</h6>
                                <h4 class="my-1 text-black">$ 0</h4>
                                <p class="mb-0 font-13 text-black"><span class="text-secondary">--%</span> <span>from last month</span></p>
                            </div>
                            <div class="widgets-icons text-success ms-auto">
                                <img width="45" height="45" src="/source-images/ceo-dashboard/evergreen/collectedPayments.png" alt="Gross Sales Icon">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- account receivbables -->
            <div class="col">
                <div id="evergreen-widget-account-receivables" class="card radius-15 bg-gradient-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0 text-black">Account Receivables</h6>
                                <h4 class="my-1 text-black">$ 0</h4>
                                <p class="mb-0 font-13 text-black"><span class="text-secondary">--%</span> <span>from last month</span></p>
                            </div>
                            <div class="widgets-icons text-success ms-auto">
                                <img width="45" height="45" src="/source-images/ceo-dashboard/evergreen/accountReceivables.png" alt="Gross Sales Icon">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- revenue per employee -->
            <div class="col">
                <div id="evergreen-widget-revenue-per-employee" class="card radius-15 bg-gradient-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0 text-black">Revenue Per Employee</h6>
                                <h4 class="my-1 text-black">$ 0</h4>
                                <p class="mb-0 font-13 text-black"><span class="text-secondary">--%</span> <span>from last month</span></p>
                            </div>
                            <div class="widgets-icons text-success ms-auto">
                                <img width="45" height="45" src="/source-images/ceo-dashboard/evergreen/revenueEmployee.png" alt="Gross Sales Icon">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- row - ends -->

        <div class="row mx-1">
            <!-- chart starts -->
            <div class="col-12 col-xl-4 col-xxl-4 d-flex">
                <div class="card radius-15 w-100 overflow-hidden bg-custom-danger">
                    <div class="card-body">
                        <h6 class="mb-1">Gross Sales (Billed) by Month</h6>
                    </div>
                    <div class="" id="evergreen-monthly-gross-sales-chart"></div>
                </div>
            </div>
            <div class="col-12 col-xl-8 col-xxl-8">
                <div class="card radius-15 w-100 overflow-hidden bg-custom-info">
                    <div class="card-body">
                        <h6 class="mb-1">Collected Payments by Month</h6>
                    </div>
                    <div class="" id="evergreen-monthly-collected-payments-chart"></div>
                </div>
                <div class="card radius-15 w-100 overflow-hidden bg-custom-warning">
                    <div class="card-body">
                        <h6 class="mb-1">Account Receivables by Month</h6>
                    </div>
                    <div class="" id="evergreen-monthly-account-receivables-chart"></div>
                </div>
                <div class="card radius-15 w-100 overflow-hidden bg-custom-success">
                    <div class="card-body">
                        <h6 class="mb-1">Revenue per Employee by Month</h6>
                    </div>
                    <div class="" id="evergreen-monthly-revenue-per-employee-chart"></div>
                </div>
            </div>
        </div>
    </div>
</div>