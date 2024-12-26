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
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/facesheet/{{$id}}">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-regular fa-user"></i>&nbsp;
                        </div>
                        <div class="tab-title">Facesheet</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-primary" href="javascript:;" role="tab" aria-selected="true" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-capsules"></i></i>&nbsp;
                        </div>
                        <div class="tab-title">Medications</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/allergies/{{$id}}">
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
                <hr>
                <span id="patient_bdate"></span><span id="patient_age"></span>
                <br>
                <span id="patient_gender"></span><span id="patient_contact"></span>
                <hr style="margin-bottom: -30px;">
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="medications_table" class="table row-border table-hover" style="width:100%;">
                        <thead></thead>
                        <tbody>                                   
                        </tbody> 
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('sweetalert2/script')
    @include('stores/clinical/tebra/modalMedications/add-form')
    @include('stores/clinical/tebra/modalMedications/delete-form')
    @include('stores/clinical/tebra/modalMedications/edit-form')
</div>
<!--end page wrapper -->
@stop
@section('pages_specific_scripts')
<script>
    let table_medications = '';
    // Get the URL
    let url = window.location.href;
    // Split the URL by '/'
    let parts = url.split('/');
    // Get the last part of the URL
    let urlPatientId = parts[parts.length - 1];

    let patientId = {{$id}};

    let medCount = 0;

    let menu_store_id = {{request()->id}};

    $('.datetimepicker').each(function() {
        new tempusDominus.TempusDominus(this, {
            useCurrent: false,
            stepping: 1,
            localization: {
                format: 'yyyy-MM-dd hh:mm T', // Modified format
            }
        });
    });

    // $(".auto_width").on('keyup', function(){
        
    //     elementId = $(this).prop('id');

    //     let width = $(this).val().length * 10 + 25;

    //     $(this).css('width', width +"px");
    // });

    $(".auto_width").on('input', function(){
        let elementId = $(this).prop('id');
        let scrollWidth = this.scrollWidth;
        let clientWidth = this.clientWidth;

        if (scrollWidth > clientWidth) {
            $(this).css('width', scrollWidth + "px");
        }
    });

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
        const medications_table = $('#medications_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBtip',
            buttons: [
                { text: '+ Add', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
            ],
            pageLength: 50,
            order: [[0, 'desc']],
            searching: true,
            ajax: {
                url: `/store/clinical/${menu_store_id}/tebra-patients/get_patient_medications_data`,
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
                { data: 'name', name: 'name', title: 'Medication' },
                { data: 'quantity', name: 'quantity', title: 'Quantity' },
                { data: 'refills', name: 'refills', title: 'Refills' },
                { data: 'prescribed_on', name: 'prescribed_on', title: 'Prescribed On' },
                { data: 'prescribed_by', name: 'prescribed_by', title: 'Prescribed By' },
                { data: 'store_location', name: 'store_location', title: 'Store Location' },
                { data: 'actions', name: 'actions', title: 'Actions', orderable: false, searchable: false }
            
            ],
            initComplete: function( settings, json ) {
                selected_len = medications_table.page.len();
                $('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
                $('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
                $('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
                $('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
                $('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_medications = medications_table;
        
        // Placement controls for Table filters and buttons
        table_medications.buttons().container().appendTo( '.card-header' );
        $('#search_input').val(table_medications.search());
        $('#search_input').keyup(function(){ table_medications.search($(this).val()).draw() ; })
        $('#length_change').change( function() { table_medications.page.len($(this).val()).draw() });

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
            },
            error: function (msg) {
                handleErrorResponse(msg);
            }
        });
    });

    //only number input
    $('.number_only').keyup(function(e){
        if (/\D/g.test(this.value))
        {
            // Filter non-digits from input value.
            this.value = this.value.replace(/\D/g, '');
        }
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
        medCount = 0;
        $('.error_txt').remove();
        $('.additional_row').remove();
        $('td > input[type="text"]').css('width', '100%');
    });

    $('#edit_modal').on('hidden.bs.modal', function(){
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
        medCount = 0;
        $('.error_txt').remove();
        $('.additional_row').remove();
        $('td > input[type="text"]').css('width', '100%');
    });

    function showAddNewForm(){
        $('#add_modal').modal('show');
        $('#modal_title').text('MEDICATION FORM');
        $('#patient_id').val(patientId);
    }

    function showEditForm(data){
        let id = $(data).data('id');
        let name = $(data).data('name');
        let quantity = $(data).data('quantity');
        let refills = $(data).data('refills');
        let storeLocation = $(data).data('storelocation');
        let prescribedOn = $(data).data('prescribedon');
        let prescribedBy = $(data).data('prescribedby');
        $('#edit_modal #modal_title').html('Medication ID: '+id);
        $('#edit_modal').modal('show');
		$('#medications').attr('rows', 1);

        $('#edit_modal #med_id').val(id);
        $('#edit_modal #medications').val(name);
        $('#edit_modal #quantity').val(quantity);
        $('#edit_modal #refills').val(refills);
        $('#edit_modal #store_location').val(storeLocation);
        $('#edit_modal #prescribed_on').val(prescribedOn);
        $('#edit_modal #prescribed_by').val(prescribedBy);
        $('#edit_modal #patient_id').val(patientId);
    }
</script>
@stop
