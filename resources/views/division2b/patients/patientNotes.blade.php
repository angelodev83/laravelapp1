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
        @include('layouts/pageContentHeader/index')
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
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/allergies/{{$id}}" role="tab" tabindex="-1">
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
                <a class="btn btn-primary" data-bs-toggle="pill" href="/store/clinical/{{request()->id}}/tebra-patients/notes/{{$id}}" role="tab" aria-selected="true">
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
            <div class="card-header dt-card-header" style="padding-top: -10px;">
            <select name='length_change' id='length_change' class="table_length_change form-select">
            </select>
            <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
            </div>
            <div class="card-body">
                <h6 id="patient_name"></h6>
                <hr>
                <span id="patient_bdate"></span><span id="patient_age"></span>
                <br>
                <span id="patient_gender"></span><span id="patient_contact"></span>
                <hr style="margin-bottom: -30px;">
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="notes_table" class="table row-border table-hover" style="width:100%;">
                        <thead></thead>
                        <tbody>                                   
                        </tbody> 
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('sweetalert2/script')
    @include('division2b/patients/modalNotes/add-form')
    @include('division2b/patients/modalNotes/edit-form')
    @include('division2b/patients/modalNotes/delete-form')
</div>
<!--end page wrapper -->
@stop
@section('pages_specific_scripts')
<script>
    let table_notes = '';
    // Get the URL
    let url = window.location.href;
    // Split the URL by '/'
    let parts = url.split('/');
    // Get the last part of the URL
    let urlPatientId = parts[parts.length - 1];

    let patientId = {{$id}};


    $(document).ready(function(){
        
        //alert({{$id}});
        const notes_table = $('#notes_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBtip',
            buttons: [
                { text: '+ Add Notes', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
            ],
            pageLength: 50,
            searching: true,
            ajax: {
                url: "/admin/divisiontwob/patients/get_patient_notes_data",
                type: "POST",
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
                { data: 'name', name: 'name', title: 'Note' },
                { data: 'actions', name: 'actions', title: 'Actions', orderable: false, searchable: false }
            
            ],
            initComplete: function( settings, json ) {
                selected_len = notes_table.page.len();
                $('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
                $('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
                $('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
                $('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
                $('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_notes = notes_table;
        
        // Placement controls for Table filters and buttons
        table_notes.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(table_notes.search());
        $('#search_input').keyup(function(){ table_notes.search($(this).val()).draw() ; })
        $('#length_change').change( function() { table_notes.page.len($(this).val()).draw() });

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/admin/divisiontwob/patients/get_patient_data/"+patientId,
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

    $('#add_modal #fileDropArea').on('click', function (e) {
        e.preventDefault();
        $('#add_modal #file').trigger('click');//open file selection
        e.stopPropagation();
    });

    $('#edit_modal #fileDropArea').on('click', function (e) {
        e.preventDefault();
        $('#edit_modal #file').trigger('click');//open file selection
        e.stopPropagation();
    });

    function showAddNewForm(){
        $('#add_modal').modal('show');
        $('#add_modal #modal_title').text('NOTE FORM');
        $('#add_modal #patient_id').val(patientId);

        $("#add_modal #file").hide();
        $('.file_title').remove();
        $('#add_modal #droparea_text').text('');
        $('#add_modal #droparea_text').append('<i style="font-size: 50px;" class="file_title fw-bold lead bx bx-cloud-upload"></i><p class="file_title">DROP FILE OR CLICK TO UPLOAD INVOICE ORDERS</p>');
        
        $('#add_modal #fileDropArea').on('dragover', function (e) {
            e.preventDefault();
            $(this).css('border-style', 'dotted'); // Change border style to dotted on dragover
            $(this).css('background', '#eee'); // Change border style to dotted on dragover
            e.stopPropagation();
        }).on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            $(this).css('background', '#fff'); // Change border style to dotted on dragover
            e.stopPropagation();
        });

        $('#add_modal #fileDropArea').on('dragenter', function (e) {
            e.preventDefault();
            e.stopPropagation();
        })

        $('#add_modal #fileDropArea').on('drop', function (e) {
            e.preventDefault();
            // Get a reference to our file input
            const fileInput = document.querySelector('#file');

            var file = e.originalEvent.dataTransfer.files;
            
            $('#droparea_text').text('' + file[0].name + '');
            //transfer file in input file
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file[0]);
            fileInput.files = dataTransfer.files;

        });

        $('#add_modal #file').change(function (){
            $('#add_modal #droparea_text').text('' + $('#add_modal #file')[0].files[0].name + '');
        });
    }

    function showEditForm(data){
        let id = $(data).data('id');
        let name = $(data).data('name');
        let body = $(data).data('body');
        let fileid = $(data).data('fileid');
        let filename = $(data).data('filename');
        $('#edit_modal #modal_title').html('Note ID: '+id);
        $('#edit_modal').modal('show');

        $('#edit_modal .file_name').text(filename);
        $('#edit_modal #note_id').val(id);
        $('#edit_modal #file_id').val(fileid);
        $('#edit_modal #name').val(name);
        $('#edit_modal #body').val(body);

        $("#edit_modal #file").hide();
        $('.file_title').remove();
        $('#edit_modal #droparea_text').text('');
        $('#edit_modal #droparea_text').append('<i style="font-size: 50px;" class="file_title fw-bold lead bx bx-cloud-upload"></i><p class="file_title">DROP FILE OR CLICK TO UPLOAD INVOICE ORDERS</p>');
        
        $('#edit_modal #fileDropArea').on('dragover', function (e) {
            e.preventDefault();
            $(this).css('border-style', 'dotted'); // Change border style to dotted on dragover
            $(this).css('background', '#eee'); // Change border style to dotted on dragover
            e.stopPropagation();
        }).on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            $(this).css('background', '#fff'); // Change border style to dotted on dragover
            e.stopPropagation();
        });

        $('#edit_modal #fileDropArea').on('dragenter', function (e) {
            e.preventDefault();
            e.stopPropagation();
        })

        $('#edit_modal #fileDropArea').on('drop', function (e) {
            e.preventDefault();
            // Get a reference to our file input
            const fileInput = document.querySelector('#file');

            var file = e.originalEvent.dataTransfer.files;
            
            $('#droparea_text').text('' + file[0].name + '');
            //transfer file in input file
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file[0]);
            fileInput.files = dataTransfer.files;

        });

        $('#edit_modal #file').change(function (){
            $('#edit_modal #droparea_text').text('' + $('#edit_modal #file')[0].files[0].name + '');
        });

        if(fileid == ''){
            $("#edit_modal #chip_controller").hide();
            $("#edit_modal #fileDropArea").removeClass('d-none');
        }
        else{
            $("#edit_modal #chip_controller").show();
            $("#edit_modal #fileDropArea").addClass('d-none');
        }
    }
</script>
@stop
