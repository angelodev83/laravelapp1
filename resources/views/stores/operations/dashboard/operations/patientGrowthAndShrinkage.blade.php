<div class="card">
    <div class="card-body mx-2">
        <b class="" style="font-size: medium; color: #29ABE2;">
            <i class="fa-solid fa-arrow-up-right-dots me-2"></i>
            Patient Growth and Shrinkage
        </b>

        <div class="mt-3 row row-cols-1">
            <!-- new patients -->
            <div class="col-md-3">
                <div id="newPatientsCard" class="card radius-15 bg-custom-operations-patient-growth-and-shrinkage">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-3 text-black">New Patients</h6>
                                <h4 class="mb-2 text-black">0</h4>
                                <p class="mb-1 font-13 text-black" id="newPatientsCardSubtext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="" id="newPatientsCardChart"></div>
                </div>
            </div>

            <!-- trp rx revenue per script -->
            <div class="col-md-3" hidden>
                <div id="totalNewPatientsRxCard" class="card radius-15 bg-custom-operations-patient-growth-and-shrinkage">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-3 text-black">Total New Patients Rx</h6>
                                <h4 class="mb-2 text-black">0</h4>
                                <p class="mb-1 font-13 text-black" id="totalNewPatientsRxCardSubtext"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- avg turnaround time -->
            <div class="col-md-6" hidden>
                <div id="newPatientsAmountCard" class="card radius-15 bg-custom-operations-patient-growth-and-shrinkage">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-3 text-black">New Patients $ Amount</h6>
                                <h4 class="mb-2 text-black">0</h4>
                                <p class="mb-1 font-13 text-black"  id="newPatientsAmountCardSubtext"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-3 row-cols-xl-3" hidden>
            <!-- total transferred out patients -->
            <div class="col">
                <div id="totalTransferredOutPatientsCard" class="card radius-15 bg-custom-operations-patient-growth-and-shrinkage">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0 text-black">Total Transferred Out Patients</h6>
                                <h4 class="my-1 text-black">$ 0</h4>
                                <p class="mb-0 font-13 text-black"  id="totalTransferredOutPatientsCardSubtext"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- total transferred out rx -->
            <div class="col">
                <div id="totalTransferredOutRxCard" class="card radius-15 bg-custom-operations-patient-growth-and-shrinkage">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0 text-black">Total Transferred Out Rx</h6>
                                <h4 class="my-1 text-black">$ 0</h4>
                                <p class="mb-0 font-13 text-black"  id="totalTransferredOutRxCardSubtext"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- total transferred out rx amount -->
            <div class="col">
                <div id="totalTransferredOutRxAmountCard" class="card radius-15 bg-custom-operations-patient-growth-and-shrinkage">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0 text-black">Total Transferred Out Rx Amount</h6>
                                <h4 class="my-1 text-black">$ 0</h4>
                                <p class="mb-0 font-13 text-black"  id="totalTransferredOutRxAmountCardSubtext"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>