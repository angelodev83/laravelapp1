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
                        <div class="d-flex gap-2 align-items-center float-end" style="margin-left: 10px">
                            <select name='length_change' id='length_change' class="form-select" style="width: 150px;"></select>
                            <h6 class="mb-0">Date Filter: </h6>
                            <select id="years" class="form-select" style="width: 150px;">
                                @foreach ($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            <select id="months" class="form-select" style="width: 150px;">
                                <option value="">All Months</option>
                                @foreach ($months as $key => $month)
                                    <option value="{{ $key }}">{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
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
			@include('stores/dataInsights/grossRevenueAndCogs/modal/edit-form')
			@include('stores/dataInsights/grossRevenueAndCogs/modal/delete-form')
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
    
    $('.imageuploadify-file-general-class').click(function () {
        $('.imageuploadify-container').remove();
    }); 

    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        
        loadData();
    });

    // Listen for the change event on the year select
    $('#years').on('change', function() {
        var year = $(this).val();
        var month = $('#months').val();
        if (year === '') {
            year = null; // Set year to null if "All Years" is selected
        }
        loadData(); // Reload the DataTable with the new filters
    });

    // Listen for the change event on the month select
    $('#months').on('change', function() {
        var month = $(this).val();
        var year = $('#years').val();
        if (month === '') {
            month = null; // Set month to null if "All Months" is selected
        }
        loadData(); // Reload the DataTable with the new filters
    });

    function loadData() 
    {
        let data = {};       
        var month = $('#months').val();
        var year = $('#years').val();
        const cp_table = $('#table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            pageLength: 50,
            order: [[ 0, 'desc' ]],
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
                url: "/store/data-insights/gross-revenue-and-cogs/data",
                type: "Get",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.month = month;
                    data.year = year;
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'id', name: 'id', title: 'ID'},
                { data: 'rx_number', name: 'rx_number', title: 'Rx #'},
                { data: 'gross_profit', name: 'gross_profit', title: 'Gross Profit'},
                { data: 'acquisition_cost', name: 'acquisition_cost', title: 'Acquisition Cost'},
                { data: 'total_price_submitted', name: 'total_price_submitted', title: 'Total Price Submitted'},
                { data: 'user_id', name: 'user_id', title: 'Created By'},
                { data: 'created_at', name: 'created_at', title: 'Date Created'},
                { data: 'completed_on', name: 'completed_on', title: 'Completed On'},
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

    function clearSelection() {
        selectedIds = {};
        unselectedIds = [];
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
        proceedImportSingleExcel('/store/data-insights/gross-revenue-and-cogs/upload');
    }

    function clickUploadFileIcon(id, filename ='', file_id='') 
    {
        console.log("id",id)
        console.log("filename",filename)
        console.log("file_id",file_id)
        selected_row_id = id;
        $(".imageuploadify-container").remove();

        if(filename != '' && file_id != ''){
            $('#chip_div').show();
            if (filename.length > 75) {
                filename = filename.substring(0, 75) + '...';
            }
            $('#import_single_any_modal .file_name').text(filename);
            $("#import_single_any_modal #file_id").val(file_id);
            $("#import_single_any_modal #chip_controller").show();
            $("#import_single_any_modal #file").hide();
            $('#import_single_any_modal .file_name').attr("href", "/admin/file/download/"+file_id+"");           

            $('#attachment_div').hide();
        } else {
            $('#chip_div').hide();
            $('#attachment_div').show();
            $('.imageuploadify-message').html('Drag&Drop Your File Here To Upload');
        }

        let fileInput = $('<input/>', {
            id: 'upload_single_any_file',
            class: 'imageuploadify-file-general-class',
            name: 'upload_single_any_file',
            type: 'file',
            accept: '*'
        });
        $('#import_single_any_modal #for-file').html(fileInput); 
        $('#import_single_any_modal #upload_single_any_file').imageuploadify();
        
        $("#import_single_any_modal .imageuploadify-container").remove();
        $('#import_single_any_modal .imageuploadify-message').html('Drag&Drop Your File(s) Here To Upload');     
        
        $('#import_single_any_modal').modal('show');
    }

    function saveImportSingleAny()
    {
        const id = selected_row_id;
        proceedImportSingleAny('/store/operations/for-delivery-today/upload/shipping-label/'+id)
    }

    function clickUploadBulkShippingLabel()
    {
        $(".imageuploadify-container").remove();

        let fileInput = $('<input/>', {
            id: 'upload_bulk_fst_shipping_label_file',
            class: 'imageuploadify-file-general-class',
            name: 'upload_bulk_fst_shipping_label_file',
            type: 'file',
            accept: '*',
            multiple: ''
        });
        $('#upload_bulk_fst_shipping_label_modal #for-file').html(fileInput); 
        $('#upload_bulk_fst_shipping_label_modal #upload_bulk_fst_shipping_label_file').imageuploadify();
        
        $("#upload_bulk_fst_shipping_label_modal .imageuploadify-container").remove();
        $('#upload_bulk_fst_shipping_label_modal .imageuploadify-message').html('Drag&Drop Your File(s) Here To Upload');     
        
        $('#upload_bulk_fst_shipping_label_modal').modal('show');
    }

    function saveUploadBulkShippingLabel()
    {
        proceedUploadBulkShippingLabel('/store/operations/for-delivery-today/bulk-upload/shipping-label')
    }

    function clearShippedDateFilter()
    {
        $('#delivered_date').val('');
        table_cp.draw()
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