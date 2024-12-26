@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
			<!-- PAGE-HEADER -->
            @include('layouts/pageContentHeader/index')
            <!-- PAGE-HEADER END -->

            <div class="card">
                <div class="card-header dt-card-header">
                    <select name='length_change' id='length_change' class="table_length_change form-select">
                    </select>
                    <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
                </div>
                <div class="card-body">
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
		</div>
		<!--end page wrapper -->
        @include('sweetalert2/script')
        @include('division2b/pharmacyStores/modals/add-store-form')
        @include('division2b/pharmacyStores/modals/edit-store-form')
        @include('division2b/pharmacyStores/modals/delete-store')
@stop

@section('pages_specific_scripts')  
<script>

    let table_store;

    function showEditForm(id, code, name, address, description, cover_image){
        
        $(".form-control").removeClass("is-invalid");

        $('#editPharmacyStore_modal').modal('show');

        document.getElementById("eid").value = id; 
        document.getElementById("ecode").value = code; 
        document.getElementById("ename").value = name; 
        document.getElementById("edescription").value = description; 
        document.getElementById("eaddress").value = address;
        if(cover_image != "") {
            document.getElementById("ecover_image").src = cover_image;
        } else {
            document.getElementById("ecover_image").src = '/assets/images/errors-images/404-error.png';
        }
    }

    function showAddNewForm(){
        $(".form-control").removeClass("is-invalid");
        $('#addPharmacyStore_modal').modal('show');
    }

    $('#addPharmacyStore_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $(document).ready(function() {
        // $('.imageuploadify-file-general-class').imageuploadify();
        loadStores();

    });

    function loadStores() {
        
        let data = {};

        
        const staff_table = $('#dt_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            pageLength: 50,
            dom: 'fBtip',
            buttons: [
                { text: 'Add Store', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/admin/divisiontwob/pharmacy_store/get_data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'id', name: 'id', title: 'ID' },
                { data: 'code', name: 'code', title: 'Code' },
                { data: 'name', name: 'name', title: 'Name' },
                { data: 'address', name: 'address', title: 'Address' },
                { data: 'description', name: 'description', title: 'Description' },
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

        table_store = staff_table;
        table_store.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(table_store.search());
		$('#search_input').keyup(function(){ table_store.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_store.page.len($(this).val()).draw() });
    }

    $('.imageuploadify-file-general-class').click(function () {
        $('.imageuploadify-container').remove();
    });

</script>
@stop
