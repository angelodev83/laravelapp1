@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
			<!-- PAGE-HEADER -->
            @include('layouts/pageContentHeader/index')
            <!-- PAGE-HEADER END -->

            @if (count($stores) > 0)
                <div class="card-group card-group-scroll">
                    @foreach ($stores as $item)
                        <div id="card-store-id-<?=$item->id?>" class="mb-3 card card-store">
                            <div class="image-box-hover" onclick="loadStaffs(<?=$item->id?>)">
                                <img src="{{(!empty($item->cover_image) ? $item->cover_image : '/assets/images/errors-images/404-error.png')}}" class="card-img-top" alt="{{$item->name}}"/>
                            </div>
                            <div class="text-center card-body" onclick="loadStaffs(<?=$item->id?>)">
                                <h6 class="mb-0 card-title text-primary">{{$item->code}}</h6>
                                <b>{{$item->name}}</b>
                                <p class="card-text">
                                    {{ $item->address}}
                                </p>
                            </div>
                            <div class="text-center card-footer bg-light" style="!important; cursor: auto !important;">
                                <button type="button" onclick="loadStaffs(<?=$item->id?>)" class="px-5 btn btn-primary btn-sm radius-15 me-2" ><small>Open</small></button>
                                {{-- <button type="button" onclick="showDeleteStore(<?=$item->id?>)" class="btn btn-outline-danger btn-sm radius-0 float-end" ><small>Delete</small></button> --}}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 card">
                    <div class="card-header card-header-index">
                        <select name='length_change' id='length_change' class="table_length_change form-select">
                        </select>
                        <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
                    </div>
                    <div class="card-body">
                        <h6 class="m-0 card-title text-primary" id="store_name">Store Name</h6>
                        <div class="table-responsive">
                            <table id="dt_table" class="table row-border hover" style="width:100%">
                                <thead></thead>
                                <tbody>                                   
                                </tbody>
                                <tfooter></tfooter>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                
            @endif
		</div>
		<!--end page wrapper -->
        @include('sweetalert2/script')
        @include('division2b/pharmacy/modals/add-staff-form')
        @include('division2b/pharmacy/modals/edit-staff-form')
        @include('division2b/pharmacy/modals/delete-staff')
        @include('division2b/pharmacyStores/modals/delete-store')

        @include('admin/employees/modals/add')
@stop

<style>
    @media (min-width: 576px) {
        .card-group.card-group-scroll {
            overflow-x: auto;
            flex-wrap: nowrap;
        }
    }

    .card-group.card-group-scroll > .card {
        flex-basis: 24%;
    }

    .card-img-top {
        height: 160px !important;
        width: 100% !important;
        object-fit: cover;
    }

    .btn-outline-danger {
        border-color: white !important;
        text-decoration: underline !important;
    }

</style>

@section('pages_specific_scripts')   
<script>

    var stores = {{ Js::from($stores) }};
    let table_staff;
    
    let firstStoreId = stores.length > 0 ? stores[0].id : null;

    function showEditStaffForm(id, pharmacy_store_id, employee_id, employee_name, schedule){
        $(".form-control").removeClass("is-invalid");
        $('#editPharmacyStaff_modal').modal('show');

         $('#epharmacy_store_id').select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#editPharmacyStaff_modal'),
		});

        
        $("input#eid").attr("value", id);
        $("input#eschedule").attr("value", schedule);
        $("input#original_store_id").attr("value", pharmacy_store_id);
        $("input#eemployee_id").attr("value", employee_id);
        $("span#eemployee_name").html(employee_name);

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/admin/divisiontwob/pharmacy/get_stores",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
                var len = data.data.length;
                
                $("#epharmacy_store_id").empty();
                for( var i = 0; i<len; i++){
                    var id = data.data[i]['id'];
                    var name = data.data[i]['code'] + ' - ' + data.data[i]['name'];
                    var selected = '';
                    if(pharmacy_store_id == id) {
                        selected = 'selected';
                        $("h6#eestore_name").html(name);
                    }
                    $("#epharmacy_store_id").append("<option "+selected+" value='"+id+"'>"+name+"</option>");
                }
            },
            error: function (msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function showAddNewStaffForm(pharmacy_store_id){
        $(".form-control").removeClass("is-invalid");
        $('#addPharmacyStaff_modal').modal('show');
        let data = {};

        $("input#pharmacy_store_id").val(pharmacy_store_id);
        $('#employee_id').select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#addPharmacyStaff_modal'),
		});

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/admin/divisiontwob/pharmacy/get_employees",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
                var len = data.data.length;
                
                $("#employee_id").empty();
                for( var i = 0; i<len; i++){
                    var id = data.data[i]['id'];
                    var name = data.data[i]['lastname']+', '+data.data[i]['firstname'];
                    $("#employee_id").append("<option value='"+id+"'>"+name+"</option>");
                }
            },
            error: function (msg) {
                handleErrorResponse(msg);
            }
        });

        
    }

    function showDeleteStore(id) {
        $('#delete_store_form_modal').modal('show');
        $('#delete_store_form_modal #id').val(id);
        $('#title_store_id_text').html('Pharmacy Store: '+id);
        $('#delete_store_form_modal #reload').val(true);
    }

    $('#addPharmacyStaff_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $(document).ready(function() {
        $('.upload_image').imageuploadify();
        
        if(firstStoreId != null) {
            loadStaffs(firstStoreId);
        }

    });

    function loadStaffs(pharmacy_store_id) {
        $(`#bs-stepper-circle-1`).css('color', '#ffffff');
        $(`#bs-stepper-circle-1`).css('background-color', '#15a0a3');

        let obj = stores.find(o => o.id == pharmacy_store_id);
        $("h6#store_name").html('Pharmacy Roster - ' + obj['code']);

        let data = {};
        firstStoreId = pharmacy_store_id;

        $(".card-store").removeClass("card-store-selected");
        $(`#card-store-id-${pharmacy_store_id}`).addClass("card-store-selected");

        
        $("input#pharmacy_store_id").val(pharmacy_store_id);
        
        const staff_table = $('#dt_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            pageLength: 50,
            dom: 'fBtip',
            buttons: [
                { text: 'Add Staff', className: 'btn btn-sm btn-success px-4', action: function ( e, dt, node, config ) {
                    showAddNewStaffForm(pharmacy_store_id);
                }},
                { text: '+ New Employee', className: 'btn btn-sm btn-primary px-4', action: function ( e, dt, node, config ) {
                    showAddNewEmployeeForm(pharmacy_store_id);
                }},
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/admin/divisiontwob/get_staff_data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = pharmacy_store_id;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'id', name: 'pharmacy_staff.id', title: 'ID' },
                { data: 'employee_name', name: 'employee_name', title: 'Name' },
                // { data: 'schedule', name: 'schedule', title: 'Schedule' },
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = staff_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_staff = staff_table;
        table_staff.buttons().container().appendTo( '.card-header-index' );
        $('#search_input').val(table_staff.search());
		$('#search_input').keyup(function(){ table_staff.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_staff.page.len($(this).val()).draw() });
    }

    function showAddNewEmployeeForm(pharmacy_store_id){
        $(".form-control").removeClass("is-invalid");
        $('#pharmacy_store_id').val(pharmacy_store_id);
        let obj = stores.find(o => o.id == pharmacy_store_id);
        $("#pharmacy_name").html(obj['name']);
        $('#add_employee_modal').modal('show');
    }

    function reloadDataTable()
    {
        table_staff.ajax.reload(null, false);
    }

</script>
@stop
