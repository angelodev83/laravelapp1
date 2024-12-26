@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
			<!-- PAGE-HEADER -->
            @include('layouts/pageContentHeader/index')
            <!-- PAGE-HEADER END -->

            @if (count($operations) > 0)
            
                <div class="card-group card-group-scroll">
                    @foreach ($operations as $item)
                        <div id="card-operation-id-<?=$item->id?>" class="mb-3 card card-operation">
                            <div class="image-box-hover" onclick="loadSupports(<?=$item->id?>)">
                                <img src="{{(!empty($item->cover_image) ? $item->cover_image : '/assets/images/errors-images/404-error.png')}}" class="card-img-top" alt="{{$item->name}}"/>
                            </div>
                            <div class="text-center card-body" onclick="loadSupports(<?=$item->id?>)">
                                <h6 class="mb-0 card-title text-primary">{{$item->code}}</h6>
                                <b>{{$item->name}}</b>
                                <p class="card-text">
                                    {{ $item->description}}
                                </p>
                            </div>
                            <div class="text-center card-footer bg-light" style="!important; cursor: auto !important;">
                                <button type="button" onclick="loadSupports(<?=$item->id?>)" class="px-5 btn btn-primary btn-sm radius-15 me-2" ><small>Open</small></button>
                                {{-- <button type="button" onclick="showDeleteOperation(<?=$item->id?>)" class="btn btn-outline-danger btn-sm radius-0 float-end" ><small>Delete</small></button> --}}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 card">
                    <div class="card-header dt-card-header">
                        <select name='length_change' id='length_change' class="table_length_change form-select">
                        </select>
                        <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
                    </div>
                    <div class="card-body">
                        <h6 class="m-0 card-title text-primary" id="operation_name">Operation Name</h6>
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
        @include('division2b/pharmacySupport/modals/add-support-form')
        @include('division2b/pharmacySupport/modals/edit-support-form')
        @include('division2b/pharmacySupport/modals/delete-support')
        @include('division2b/pharmacyOperations/modals/delete-operation')
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

    var operations = {{ Js::from($operations) }};
    let table_support;
    
    let firstOperationId = operations.length > 0 ? operations[0].id : null;

    function showEditSupportForm(id, pharmacy_operation_id, employee_id, employee_name, schedule){
        $(".form-control").removeClass("is-invalid");
        $('#editPharmacySupport_modal').modal('show');

         $('#epharmacy_operation_id').select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#editPharmacySupport_modal'),
		});

        
        $("input#eid").attr("value", id);
        $("input#eschedule").attr("value", schedule);
        $("input#original_operation_id").attr("value", pharmacy_operation_id);
        $("input#eemployee_id").attr("value", employee_id);
        $("span#eemployee_name").html(employee_name);

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/admin/divisiontwob/pharmacy_support/get_operations",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
                var len = data.data.length;
                
                $("#epharmacy_operation_id").empty();
                for( var i = 0; i<len; i++){
                    var id = data.data[i]['id'];
                    var name = data.data[i]['code'] + ' - ' + data.data[i]['name'];
                    var selected = '';
                    if(pharmacy_operation_id == id) {
                        selected = 'selected';
                        $("h6#eeoperation_name").html(name);
                    }
                    $("#epharmacy_operation_id").append("<option "+selected+" value='"+id+"'>"+name+"</option>");
                }
            },
            error: function (msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function showAddNewSupportForm(pharmacy_operation_id){
        $(".form-control").removeClass("is-invalid");
        $('#addPharmacySupport_modal').modal('show');
        let data = {};

        $("input#pharmacy_operation_id").val(pharmacy_operation_id);
        $('#employee_id').select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#addPharmacySupport_modal'),
		});

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/admin/divisiontwob/pharmacy_support/get_employees",
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

    function showDeleteOperation(id) {
        $('#delete_operation_form_modal').modal('show');
        $('#delete_operation_form_modal #id').val(id);
        $('#title_operation_id_text').html('Pharmacy Operation: '+id);
        $('#delete_operation_form_modal #reload').val(true);
    }

    $('#addPharmacySupport_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $(document).ready(function() {
        $('.upload_image').imageuploadify();

        if(firstOperationId != null) {
            loadSupports(firstOperationId);
        }

    });

    function loadSupports(pharmacy_operation_id) {
        let obj = operations.find(o => o.id == pharmacy_operation_id);
        $("h6#operation_name").html('Operations Roster - ' + obj['code']);

        let data = {};
        firstOperationId = pharmacy_operation_id;

        $(".card-operation").removeClass("card-operation-selected");
        $(`#card-operation-id-${pharmacy_operation_id}`).addClass("card-operation-selected");

        
        $("input#pharmacy_operation_id").val(pharmacy_operation_id);
        
        const support_table = $('#dt_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            pageLength: 50,
            dom: 'fBtip',
            buttons: [
                { text: 'Add Support', className: 'btn btn-sm btn-primary px-4', action: function ( e, dt, node, config ) {
                    showAddNewSupportForm(pharmacy_operation_id);
                }},
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/admin/divisiontwob/pharmacy_support/get_data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_operation_id = pharmacy_operation_id;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'id', name: 'pharmacy_supports.id', title: 'ID' },
                { data: 'employee_name', name: 'employee_name', title: 'Name' },
                { data: 'schedule', name: 'schedule', title: 'Schedule' },
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = support_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_support = support_table;
        table_support.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(table_support.search());
		$('#search_input').keyup(function(){ table_support.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_support.page.len($(this).val()).draw() });
    }

</script>
@stop
