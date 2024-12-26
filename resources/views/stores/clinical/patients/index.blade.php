<div class="card">
    <div class="card-header dt-card-header">
    <select name='length_change' id='length_change' class="table_length_change form-select">
    </select>
    <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="patients_table" class="table row-border table-hover" style="width:100%">
                <thead></thead>
                <tbody>                                   
                </tbody>
            </table>
        </div>
    </div>
</div>
@include('sweetalert2/script')
@include('division2b/patients/modal/add-patient-form')
@include('division2b/patients/modal/edit-patient-form')
@include('division2b/patients/modal/delete-patient-confirmation')
