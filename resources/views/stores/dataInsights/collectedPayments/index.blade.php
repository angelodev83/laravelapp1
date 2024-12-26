@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!-- PAGE-HEADER -->
				@include('layouts/pageContentHeader/store')

				<!-- PAGE-HEADER END -->
				<div class="card">
                    <div class="card-header card-index-header">
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
			@include('stores/dataInsights/collectedPayments/modal/edit-form')
			@include('stores/dataInsights/collectedPayments/modal/delete-form')
            @include('components/modal/import-single-excel')
            @include('components/modal/import-single-any')
		</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts')

<style>
    .clear-shipped-date:hover {
        background-color: #117b7e; 
    }
    .clear-shipped-date {
        background-color: #15a0a3; 
        color: white; 
        cursor: pointer;
    }
</style>

<script>
    let menu_store_id = {{request()->id}};
    let table_cp;
    let selected_row_id;
    let selectedIds = {};  
    let unselectedIds = [];

    $('#add_operation_order_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $('#upload_bulk_fst_shipping_label_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
        $('#upload_bulk_fst_shipping_label_modal #upload_bulk_fst_shipping_label_file').remove();
        $('#upload_bulk_fst_shipping_label_modal .imageuploadify').remove(); 
    });

    $('#import_single_excel_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
        $('#import_single_excel_modal #upload_file').remove();
        $('#import_single_excel_modal .imageuploadify').remove(); 
    });

    $('#import_single_any_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
        $('#import_single_any_modal #upload_single_any_file').remove();
        $('#import_single_any_modal .imageuploadify').remove(); 
    });

    $('#edit_operation_order_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });
    
    $('.imageuploadify-file-general-class').click(function () {
        $('.imageuploadify-container').remove();
    }); 

    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        
        loadData();
    });

    function loadData() 
    {
        let data = {};        
        const cp_table = $('#table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            pageLength: 50,
            order: [[ 8, 'desc' ]],
            dom: 'fBtip',
            buttons: [
                {
                    text: '<i class="fa fa-upload me-2"></i>Import Excel', 
                    className: 'btn btn-success btn-sm', 
                    action: function ( e, dt, node, config ) {
                        clickUploadBtn();
                    }
                },
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/store/data-insights/collected-payments/data",
                type: "Get",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                // { data: 'id', name: 'id', title: 'ID'},
                { data: 'account_number', name: 'account_number', title: 'Account #'},
                { data: 'account_name', name: 'account_name', title: 'Account Name'},
                { data: 'reconciling_account_name', name: 'reconciling_account_name', title: 'Reconciling Account'},
                { data: 'pos_sales_date', name: 'pos_sales_date', title: 'POS Sales Date', render: function(data, type, row) {
                    return '<div>' + row.f_pos_sales_date + '</div>';
                } },
                { data: 'posting_of_payment_date', name: 'posting_of_payment_date', title: 'Posting of Payment Date', render: function(data, type, row) {
                    return '<div>' + row.f_posting_of_payment_date + '</div>';
                } },
                { data: 'paid_amount', name: 'paid_amount', title: 'Paid Amount'},
                { data: 'rx_number', name: 'rx_number', title: 'Rx Number', render: function(data, type, row) {
                    return '<b>' + row.rx_number + '</b>';
                } },
                // { data: 'running_balance_as_of_date', name: 'running_balance_as_of_date', title: 'Running Balance as of Date'},
                { data: 'user', name: 'user_id', title: 'Created By'},
                { data: 'created_at', name: 'created_at', title: 'Date Created'},
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false,},
            ],
            initComplete: function( settings, json ) {
                selected_len = cp_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_cp = cp_table;
        table_cp.buttons().container().appendTo( '.card-index-header' );
        $('#search_input').val(table_cp.search());
		$('#search_input').keyup(function(){ table_cp.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_cp.page.len($(this).val()).draw() });
		$('#delivered_date').change(function(){ table_cp.draw()})

        $('.dataTables_scrollBody').scroll(function (){
            var cols = 3 // how many columns should be fixed
            var container = $(this)
            var offset = container.scrollLeft()
            container.add(container.prev()).find('tr').each(function (index,row){ // .add(container.prev()) to include the header
                $(row).find('th').each(function (index, th) {
                    if (index < cols) {
                        $(th).css({ position: 'relative', left: offset + 'px', zIndex: '1' });
                    }
                });
                $(row).find('td, th').each(function (index,cell){
                    if(index>=cols) return
                    $(cell).css({position:'relative',left:offset+'px'})
                })
            })
        });
    }
    
    function clickUploadBtn() {
        $(".imageuploadify-container").remove();
        // $('.imageuploadify-message').html('Drag&Drop<br> Only accepts <b>CSV</b> or <b>XLSX</b> Single Excel File Only');

        let fileInput = $('<input/>', {
            id: 'upload_file',
            class: 'imageuploadify-file-general-class',
            name: 'upload_file',
            type: 'file',
            accept: '.xlsx,.xls,.csv'
        });
        $('#import_single_excel_modal #for-file').html(fileInput); 
        $('#import_single_excel_modal #upload_file').imageuploadify();
        
        $("#import_single_excel_modal .imageuploadify-container").remove();
        $('#import_single_excel_modal .imageuploadify-message').html('Drag&Drop<br> Only accepts <b>CSV</b> or <b>XLSX</b> Single Excel File Only');     
        
        $('#import_single_excel_modal').modal('show');
    }

    function saveImportSingleExcel()
    {
        proceedImportSingleExcel('/store/data-insights/collected-payments/upload');
    }

    function reloadDataTable(data)
    {
        table_cp.ajax.reload(null, false);
        $(".imageuploadify-container").remove();
        $('#import_single_excel_modal').modal('hide');
        $('#import_single_any_modal').modal('hide');
        $('#upload_bulk_fst_shipping_label_modal').modal('hide');
        $('#update_for_shipping_today').modal('hide');
        sweetAlert2(data.status, data.message);
    }

</script>
@stop