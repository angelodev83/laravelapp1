<div class="card">
    <div class="card-body" id="clinical-fm-menu">
        <input type="file" id="import_csv_xlsx_file" name="file" hidden>
        @canany(['menu_store.clinical.pioneer_patients.create'])
            <div class="d-grid mb-3">
                <button class="btn btn-success" onclick="clickUploadBtn()">
                    <i class="fa fa-cloud-upload me-2"></i>Import CSV or XLSX
                </button>
            </div>
        @endcanany
        <h6 class="my-3 mt-0">Facilities</h6>
        <div class="fm-menu">
            <div class="list-group list-group-flush"> 
                <a id="clinical-lgi-all selected" href="#" class="clinical-lgi list-group-item py-2 selected" onclick="clickSideMenuFilter('', 'all')" ><i class='fa-regular fa-folder-open me-2'></i>All Patients</a>
                <a id="clinical-lgi-ctclusi-tm5" href="#" class="clinical-lgi list-group-item py-2" onclick="clickSideMenuFilter('CTCLUSI TM5', 'ctclusi-tm5')"><i class='fa fa-user-shield me-1'></i>CTCLUSI TM5</a>
                <a id="clinical-lgi-tmo5" href="#" class="clinical-lgi list-group-item py-2" onclick="clickSideMenuFilter('TMO5', 'tmo5')"><i class='fa fa-person me-2'></i>TMO5</a>
                <a id="clinical-lgi-unsorted" href="#" class="clinical-lgi list-group-item py-2" onclick="clickSideMenuFilter('Unsorted', 'unsorted')"><i class='fa-regular fa-folder-open me-2'></i>Unsorted</a>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h6 class="mb-0 font-weight-bold">Patient Count</h6>
        <h6 class="mb-0 text-success font-weight-bold mt-2" id="total_pioneer_patients">{{ $patientCounts['all_count'] }}</h6>
        <p class="mb-0">
            <span class="text-secondary">Total Patients</span>
        </p>


        <div class="mt-3"></div>
        <div class="d-flex align-items-center">
            <div class="fm-file-box bg-light-success text-success"><i class='fa fa-sm fa-user-shield'></i>
            </div>
            <div class="flex-grow-1 ms-2">
                <h6 class="mb-0">CTCLUSI TM5</h6>
            </div>
            <h6 class="text-success mb-0">{{ $patientCounts['ctclusi_tm5_count'] }}</h6>
        </div>
        <div class="d-flex align-items-center mt-3">
            <div class="fm-file-box bg-light-warning text-warning"><i class='fa fa-person'></i>
            </div>
            <div class="flex-grow-1 ms-2">
                <h6 class="mb-0">TMO5</h6>
            </div>
            <h6 class="text-success mb-0">{{ $patientCounts['tmo5_count'] }}</h6>
        </div>
        <div class="d-flex align-items-center mt-3">
            <div class="fm-file-box bg-light-info text-info"><i class='fa-regular fa-sm fa-folder-open'></i>
            </div>
            <div class="flex-grow-1 ms-2">
                <h6 class="mb-0">Unsorted</h6>
            </div>
            <h6 class="text-success mb-0">{{ $patientCounts['unsorted_count'] }}</h6>
        </div>
    </div>
</div>