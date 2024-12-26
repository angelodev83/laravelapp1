@extends('layouts.master')
@section('content')
<style>
  /* CSS to hide the number input spinners */
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }
  input[type=number] {
    -moz-appearance: textfield;
  }
</style>
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!-- PAGE-HEADER -->
				@include('layouts/pageContentHeader/store')
				<!-- PAGE-HEADER END -->
				<div class="card">
                    <div class="card-header dt-card-header">
                        <select name='length_change' id='length_change' class="table_length_change form-select">
                        </select>
                        <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id="table" class="table row-border table-hover" style="width:100%">
								<thead></thead>
								<tbody>                                   
								</tbody>
								<tfooter></tfooter>
							</table>
						</div>
					</div>
				</div>
			</div>
			@include('sweetalert2/script')
            @include('stores/eodRegisterReport/register/modal/add-form')
    		@include('stores/eodRegisterReport/register/modal/edit-form')
    		@include('stores/eodRegisterReport/register/modal/delete-form')
            @include('stores/eodRegisterReport/register/modal/file')
            @include('stores/eodRegisterReport/register/modal/bulk-upload')
           
            <!-- include('stores/procurement/pharmacy/inmarReturns/modal/view-form') -->
		</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts')

<script>
    let dt_table;
    let file_table;
    let menu_store_id = {{request()->id}};

    function showAddNewForm(){  
        let fileInput = $('<input/>', {
            id: 'file',
            class: 'imageuploadify-file-general-class',
            name: 'file',
            type: 'file',
            accept: '*',
            multiple: ''
        });
        $('#add_modal #for-file').html(fileInput); 
        $('#add_modal #file').imageuploadify();
        
        $("#add_modal .imageuploadify-container").remove();
        $('#add_modal .imageuploadify-message').html('Drag&Drop Your File(s) Here To Upload');     
        $('#add_modal').modal('show');
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5',
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true,
   			orientation: "right",
        });

        $('#add_modal #register_page_id').val(65);

        // Set the default date to today
        $('.datepicker').datepicker('setDate', new Date());
    }

    $('#edit_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $('#file_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
        $('#file_modal #table').DataTable().destroy();
    });

    $('#add_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();   
        $('#add_modal #file').remove();
        $('#add_modal .imageuploadify').remove();
    });

    $('#bulk_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();   
        $('#bulk_modal #file').remove();
        $('#bulk_modal .imageuploadify').remove();
    });

    $(document).ready(function() {
        
        const table = $('#table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            dom: 'fBtip',
            order: [[0, 'desc']],
            buttons: [
                @can('menu_store.eod_register_report.register.create')
                { text: '+ New', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
                @endcan
            ],
            pageLength: 50,
            searching: true,
            ajax: {
                url: `/store/eod-register-report/${menu_store_id}/register/get_data`,
                type: "GET",
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
                { data: 'id', name: 'id', title: 'ID'},
                { data: 'date', name: 'date', title: 'Date'},
                { data: 'total_cash_received', name: 'total_cash_received', title: 'Total Cash Received' },
                { data: 'total_cash_deposited_to_bank', name: 'total_cash_deposited_to_bank', title: 'Total Cash Deposited to Bank' },
                { data: 'total_check_received', name: 'total_check_received', title: 'Total Check Received' },
                { data: 'register_number', name: 'register_number', title: 'Register #'},
                { data: 'user', name: 'user_id', title: 'Created By'},
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        dt_table = table;

        // Placement controls for Table filters and buttons
		table.buttons().container().appendTo( '.dt-card-header' ); 
        $('#search_input').val(table.search());
		$('#search_input').keyup(function(){ table.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table.page.len($(this).val()).draw() });

        populateNormalSelect(`#add_modal #register_page_id`, '#add_modal', '/admin/search/page-by-parent-id', {parent_id: 60}, 65)
    });

    function reloadDataTable()
    {
        dt_table.ajax.reload(null, false);
    }

    function reloadFileDataTable()
    {
        file_table.ajax.reload(null, false);
    }

    function populateNormalSelect(_selector, _model_id, _url, params = {}, _id = null)
    {
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: _url,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(params),
            success: function(data) {
            
                var len = data.data.length;
                
                for( var k = 0; k<len; k++){
                    var kid = data.data[k]['id'];
                    var kname = data.data[k]['name'];
                    if(kid==_id){$(_selector).append("<option selected value='"+kid+"'>"+kname+"</option>");}
                    else{
                        $(_selector).append("<option value='"+kid+"'>"+kname+"</option>");
                    }
                }
            },
            error: function (msg) {
                handleErrorResponse(msg);
            }
        });
    }

</script>
@stop