@extends('layouts.master')
@section('content')
<!--start page wrapper -->
<style>
    hr{
        color: #9a9595;
    }
</style>
<div class="page-wrapper">
    <div class="page-content">
        <!-- PAGE-HEADER -->
        @include('layouts/pageContentHeader/store')
        <!-- PAGE-HEADER END -->
        
        <ul class="mb-3 nav nav-pills" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/facesheet/{{$id}}" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-regular fa-user"></i>&nbsp;
                        </div>
                        <div class="tab-title">Facesheet</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/medications/{{$id}}" role="tab" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-capsules"></i></i>&nbsp;
                        </div>
                        <div class="tab-title">Medications</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-primary" data-bs-toggle="pill" href="javascript:;" role="tab" aria-selected="true">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-person-dots-from-line"></i>&nbsp;
                        </div>
                        <div class="tab-title">Allergies</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/demographics/{{$id}}" role="tab" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-id-card"></i>&nbsp;
                        </div>
                        <div class="tab-title">Demographics</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/notes/{{$id}}" role="tab" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-pencil"></i>&nbsp;
                        </div>
                        <div class="tab-title">Notes</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/immunization/{{$id}}" role="tab" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-syringe"></i>&nbsp;
                        </div>
                        <div class="tab-title">Immunization</div>
                    </div>
                </a>
            </li>
        </ul>

        <div class="card">
            <div class="card-header" style="padding-top: -10px;">
            <select name='length_change' id='length_change' class="table_length_change form-select">
            </select>
            <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
            </div>
            <div class="card-body">
                <h6 id="patient_name"></h6>
                <span id="patient_ka"></span>
                <br>
                <span id="patient_ma"></span>
                <hr>
                <span id="patient_bdate"></span><span id="patient_age"></span>
                <br>
                <span id="patient_gender"></span><span id="patient_contact"></span>
                <hr style="margin-bottom: -30px;">
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allergies_table" class="table row-border table-hover" style="width:100%;">
                        <thead></thead>
                        <tbody>                                   
                        </tbody> 
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('sweetalert2/script')
    @include('stores/clinical/tebra/modalAllergies/add-form')
    @include('stores/clinical/tebra/modalAllergies/delete-form')
    @include('stores/clinical/tebra/modalAllergies/edit-form')
</div>
<!--end page wrapper -->
@stop
@section('pages_specific_scripts')
<script>
    let table_allergies = '';
    // Get the URL
    let url = window.location.href;
    // Split the URL by '/'
    let parts = url.split('/');
    // Get the last part of the URL
    let urlPatientId = parts[parts.length - 1];

    let patientId = {{$id}};

    let medCount = 0;

    function moreMedication()
    {
        let tableRow = $('#drugname'+medCount+'');
        medCount++;
        tableRow.closest('tr').after('<tr class="additional_row"><td><input type="text" class="form-control auto_width" name="items['+medCount+'][drugname]" id="drugname'+medCount+'"></td><td><input class="form-control number_only auto_width" name="items['+medCount+'][quantity]" id="quantity'+medCount+'"></td><td><input type="text" class="form-control number_only auto_width" name="items['+medCount+'][refills]" id="refills'+medCount+'"></td><td><input type="text" class="form-control auto_width" name="items['+medCount+'][store_location]" id="store_location'+medCount+'"></td></tr>');

        $('.number_only').keyup(function(e){
            if (/\D/g.test(this.value))
            {
                // Filter non-digits from input value.
                this.value = this.value.replace(/\D/g, '');
            }
        });

        $(".auto_width").on('keyup', function(){
        
            elementId = $(this).prop('id');

            let width = $(this).val().length * 10 + 25;

            $(this).css('width', width +"px");
        });
    }

    $(document).ready(function(){
        
        //alert({{$id}});
        const allergies_table = $('#allergies_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBtip',
            buttons: [
                { text: '+ Add Allergy', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
            ],
            pageLength: 50,
            order: [[0, 'desc']],
            searching: true,
            ajax: {
                url: "/store/clinical/tebra-patients/get_patient_allergies_data",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.patient_id = "{{$id}}";
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'id', name: 'id', title: 'ID', visible: true},
                { data: 'name', name: 'name', title: 'Allergy' },
                { data: 'description', name: 'description', title: 'Description' },
                { data: 'actions', name: 'actions', title: 'Actions', orderable: false, searchable: false }
            
            ],
            initComplete: function( settings, json ) {
                selected_len = allergies_table.page.len();
                $('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
                $('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
                $('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
                $('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
                $('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_allergies = allergies_table;
        
        // Placement controls for Table filters and buttons
        table_allergies.buttons().container().appendTo( '.card-header' );
        $('#search_input').val(table_allergies.search());
        $('#search_input').keyup(function(){ table_allergies.search($(this).val()).draw() ; })
        $('#length_change').change( function() { table_allergies.page.len($(this).val()).draw() });

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: `/store/clinical/tebra-patients/get_patient_data/`+patientId,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                $('#patient_name').text(data.data.firstname+' '+data.data.lastname);
                $('#patient_bdate').text(data.data.birthdate);
                $('#patient_age').text(' ('+data.data.age+' yo)');
                if(data.data.gender == 'M'){
                    gender = "Male";
                }
                else if(data.data.gender == 'F'){
                    gender = "Female";
                }
                else{
                    gender = data.data.gender;
                }
                $('#patient_gender').text(gender+', ');
                $('#patient_contact').text(data.data.phone_number);
                if(data.data.known_allergies == '1'){
                    ka = "This patient has known allergies.";
                }
                else{
                    ka = "This patient has no known allergies.";
                }
                if(data.data.medication_allergies == '1'){
                    ma = "This patient has known medication allergies.";
                }
                else{
                    ma = "This patient has known medication allergies.";
                }
                $('#patient_ka').text(ka);
                $('#patient_ma').text(ma);
            },
            error: function (msg) {
                handleErrorResponse(msg);
            }
        });
    });

    //textarea auto height
    $("textarea").keyup(function(e) {
		$(this).height(2);
		$(this).height(($(this).val().split("\n").length)*25);
	});

    $('#add_modal').on('hidden.bs.modal', function(){
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
        $('.error_txt').remove();
    });

    $('#edit_modal').on('hidden.bs.modal', function(){
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
        $('.error_txt').remove();
    });

    function showAddNewForm(){
        $('#add_modal').modal('show');
        $('#modal_title').text('ALLERGY FORM');
        $('#add_modal #patient_id').val(patientId);
    }

    function showEditForm(data){
        let id = $(data).data('id');
        let name = $(data).data('name');
        let description = $(data).data('description');
        $('#edit_modal #modal_title').html('Allergy ID: '+id);
        $('#edit_modal').modal('show');

        $('#edit_modal #allergy_id').val(id);
        $('#edit_modal #name').val(name);
        $('#edit_modal #description').val(description);
    }
</script>
@stop
