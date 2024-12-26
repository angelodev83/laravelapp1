<div class="card">
    <div class="card-body" id="clinical-fm-menu">
        <input type="file" id="import_csv_xlsx_file" name="file" hidden>
        <div class="d-grid mb-3">
            <button class="btn btn-success" onclick="syncJotForm()">
                <i class="fa fa-arrows-rotate me-2"></i>Sync Jot Form
            </button>
        </div>
        <h6 class="my-3 mt-0">Facilities</h6>
        <div class="fm-menu">
            <div class="list-group list-group-flush"> 
                <a id="clinical-lgi-all selected" href="#" class="clinical-lgi list-group-item py-2 selected" onclick="clickSideMenuFilter('', 'all')" ><i class='fa-regular fa-folder-open me-2'></i>All Forms</a>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h6 class="mb-0 font-weight-bold">Form Count</h6>
        <h6 class="mb-0 text-success font-weight-bold mt-2" id="total_pioneer_patients">{{ $patientCounts['all_count'] }}</h6>
        <p class="mb-0">
            <span class="text-secondary">Total Forms</span>
        </p>
    </div>
</div>

<script>

    function syncJotForm()
    {
        let data = {};
        sweetAlertLoading();

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/store/jot-form/"+menu_store_id+"/release-of-information/sync",
            data: data,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {
                table_patients.ajax.reload(null, false);
                sweetAlert2(res.status, res.message);
            }
        });

    }

</script>