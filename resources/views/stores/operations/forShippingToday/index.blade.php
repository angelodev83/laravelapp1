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

                        <div class="input-group table_length_change me-2" style="min-width: 300px"> 
                            <span class="input-group-text" id="icon-shipped-date">
                                To Ship By <i class="fa fa-calendar ms-1"></i>
                            </span>
                            <input type="text" class="form-control datepicker" id="shipped_date" placeholder="YYYY-MM-DD">
                            <span class="input-group-text clear-shipped-date" id="icon-shipped-date" onclick="clearShippedDateFilter()">
                                <small>Clear</small>
                            </span>
                        </div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id="dt_operation_order_items_table" class="table row-border table-hover" style="width:100%">
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
            {{-- @include('stores/operations/forShippingToday/modals/add') --}}
			@include('stores/operations/forShippingToday/modals/delete')
			@include('stores/operations/forShippingToday/modals/edit')
			@include('stores/operations/forShippingToday/modals/bulk-upload')
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
    let table_operation_order;
    let selected_row_id;
    let selectedIds = {};  
    let unselectedIds = [];

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

    $('#add_operation_order_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
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
        $('#shipped_date').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5',
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true
        });
        $('#upload_bulk_fst_shipping_label_modal #ship_by_date').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5',
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true
        });
        // let date = new Date()
        // const year = date.getFullYear();
        // const month = String(date.getMonth() + 1).padStart(2, '0'); // Add leading zero if needed
        // const day = String(date.getDate()).padStart(2, '0'); // Add leading zero if needed
        // const current_date = `${year}-${month}-${day}`;
        // $('#shipped_date').val(current_date);
        loadData();
    });

    function loadData() 
    {
        let data = {};        
        const staff_table = $('#dt_operation_order_items_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            pageLength: 50,
            order: [[ 1, 'desc' ]],
            dom: 'fBtip',
            buttons: [
                // { text: 'New Order', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                //     showAddModal();
                // }},
                {
                    text: '<i class="fa fa-upload me-2"></i>Import Excel', 
                    className: 'btn btn-success btn-sm', 
                    action: function ( e, dt, node, config ) {
                        clickUploadBtn();
                    }
                },
                {
                    text: '<i class="fa fa-cloud-arrow-up me-2"></i>Upload Bulk Shipping Labels', 
                    className: 'btn btn-info2 btn-sm', 
                    action: function ( e, dt, node, config ) {
                        clickUploadBulkShippingLabel();
                    }
                },
                {
                    text: '<i class="fa-solid fa-square-check me-2"></i>Mark as Shipped', 
                    className: 'btn btn-success btn-sm', 
                    action: function ( e, dt, node, config ) {
                        completed();
                    }
                },
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/store/operations/for-shipping-today/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                    data.ship_by_date = $('#shipped_date').val();
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { 
                    data: 'is_completed', 
                    name: 'is_completed',
                    defaultContent: '',
                    title: '<input type="checkbox" id="select_all" />', 
                    render: function(data, type, full, meta){
                        if (data === 1) {
                            return '<input type="checkbox" class="row-checkbox" name="id[]" value="' + full.id + '" checked>';
                        } else {
                            return '<input type="checkbox" class="row-checkbox" name="id[]" value="' + full.id + '">';
                        }
                    },
                    orderable: false,
                    searchable: false,
                    className: 'select-checkbox',
                    width: "1%"
                },
                { data: 'id', name: 'id', title: 'ID'},
                { data: 'patient_name', name: 'patient_name', title: 'Patient'},
                { data: 'dob', name: 'dob', title: 'Birth Date' },
                { data: 'address', name: 'address', title: 'Address' },
                { data: 'city', name: 'city', title: 'City' },
                { data: 'state', name: 'state', title: 'State' },
                { data: 'phone_number', name: 'phone_number', title: 'Phone Number' },
                { data: 'email', name: 'email', title: 'Email' },
                { data: 'rx_number', name: 'rx_number', title: 'RX Number' },
                { data: 'tracking_number', name: 'tracking_number', title: 'Tracking Number', render: function(data, type, row) {
                        return row.formatted_tracking_number;
                }},
                { data: 'ship_by_date', name: 'ship_by_date', title: 'To Ship By Date' },
                { data: 'shipped_date', name: 'shipped_date', title: 'Shipped Date' },
                { 
                    data: 'status', 
                    name: 'status', 
                    title: 'Status',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (data === 1) {
                            return '<button type="button" class="btn btn-success btn-sm w-100"><small>Shipped</small></button>';
                        } else if (data === 0) {
                            return '';  
                        } else {
                            return ''; 
                        }
                    }
                },
                { data: 'shipping_label', name: 'shipping_label', title: 'Shipping Label' },
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = staff_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));

                $('#select_all').on('click', function() {
                    // Only toggle checkboxes on the current page
                    let rows = staff_table.rows({ 'page': 'current' }).nodes();
                    let checkboxes = $('input[type="checkbox"].row-checkbox', rows);
                    let isChecked = this.checked;

                    checkboxes.each(function() {
                        let id = $(this).val();

                        if (isChecked) {
                            // If select all is checked, add IDs to selectedIds
                            selectedIds[id] = true;
                            // Remove ID from unselectedIds if it's there
                            let index = unselectedIds.indexOf(id);
                            if (index !== -1) {
                                unselectedIds.splice(index, 1);
                            }
                        } else {
                            // If select all is unchecked, remove IDs from selectedIds
                            delete selectedIds[id];
                            // Add ID to unselectedIds if it's not already there
                            if (!unselectedIds.includes(id)) {
                                unselectedIds.push(id);
                            }
                        }
                    });

                    // Toggle checkboxes
                    checkboxes.prop('checked', isChecked);
                });

                $('#dt_operation_order_items_table tbody').on('change', 'input[type="checkbox"].row-checkbox', function() {
                    let id = $(this).val();
                    let isChecked = this.checked;

                    if (!isChecked) {
                        // If the checkbox is unchecked
                        // Remove ID from selectedIds if it's unchecked again
                        delete selectedIds[id];
                        // Add ID to unselectedIds array if it becomes unchecked
                        if (!unselectedIds.includes(id)) {
                            unselectedIds.push(id);
                        }
                    } else {
                        // If the checkbox is checked
                        // Add ID to selectedIds object if it's not already there
                        if (!selectedIds[id]) {
                            selectedIds[id] = true;
                        }
                        // Remove ID from unselectedIds array if it becomes checked again
                        let index = unselectedIds.indexOf(id);
                        if (index !== -1) {
                            unselectedIds.splice(index, 1);
                        }
                    }
                });

                // Store the state of checkboxes when pagination changes
                staff_table.on('draw.dt', function() {
                    updateCheckboxesState();
                });

                // Update checkboxes state immediately after DataTable is initialized
                updateCheckboxesState();

                staff_table.on('page.dt', function() {
                    $('#select_all').prop('checked', false);
                });
            }
        });

        table_operation_order = staff_table;
        table_operation_order.buttons().container().appendTo( '.card-index-header' );
        $('#search_input').val(table_operation_order.search());
		$('#search_input').keyup(function(){ table_operation_order.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_operation_order.page.len($(this).val()).draw() });
		$('#shipped_date').change(function(){ table_operation_order.draw()});

        $('.dataTables_scrollBody').scroll(function (){
            let cols = 3 // how many columns should be fixed
            let container = $(this)
            let offset = container.scrollLeft()
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

    function completed(){
        var checkedIds = Object.keys(selectedIds).filter(function(id) {
            return selectedIds[id];
        });
        let data = {
            checked_ids: checkedIds,
            unselected_ids: unselectedIds
        };
        
        console.log(checkedIds);
        console.log(unselectedIds);
        //sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/operations/for-shipping-today/update-completed`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable(data);
                    clearSelection();
                    sweetAlert2(data.status, data.message);
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }
        });

       
    }

    function updateCheckboxesState() {
        let currentPageCheckboxes = $('input[type="checkbox"].row-checkbox', table_operation_order.rows({ 'page': 'current' }).nodes());

        // Update checkboxes state based on selectedIds object
        currentPageCheckboxes.each(function() {
            let id = $(this).val();
            if (selectedIds.hasOwnProperty(id)) {
                $(this).prop('checked', selectedIds[id]);
            }
        });

        currentPageCheckboxes.each(function() {
            let id = $(this).val();
            if (selectedIds.hasOwnProperty(id)) {
                $(this).prop('checked', selectedIds[id]);
            } else if (unselectedIds.includes(id)) {
                // If the ID is in unselectedIds, uncheck the checkbox
                $(this).prop('checked', false);
            }
        });

        // Identify unchecked checkboxes that were checked initially
        $('input[type="checkbox"].row-checkbox', table_operation_order.rows().nodes()).each(function() {
            let id = $(this).val();
            let isChecked = $(this).prop('checked');
        });
    }

    function clickUploadBtn() {
        $(".imageuploadify-container").remove();

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
        proceedImportSingleExcel('/store/operations/for-shipping-today/upload')
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
            $('#import_single_any_modal .file_name').attr("href", "/admin/file/download/"+file_id);           

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
        proceedImportSingleAny('/store/operations/for-shipping-today/upload/shipping-label/'+id)
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
            multiple: false
        });
        $('#upload_bulk_fst_shipping_label_modal #for-file').html(fileInput); 
        $('#upload_bulk_fst_shipping_label_modal #upload_bulk_fst_shipping_label_file').imageuploadify();
        
        $("#upload_bulk_fst_shipping_label_modal .imageuploadify-container").remove();
        $('#upload_bulk_fst_shipping_label_modal .imageuploadify-message').html('Drag&Drop Your File(s) Here To Upload');     
        
        $('#upload_bulk_fst_shipping_label_modal').modal('show');
    }

    function saveUploadBulkShippingLabel()
    {
        proceedUploadBulkShippingLabel('/store/operations/for-shipping-today/bulk-upload/shipping-label')
    }

    function clearShippedDateFilter()
    {
        $('#shipped_date').val('');
        table_operation_order.draw()
    }

    function showDeleteFileOnly(){
        let data = {
            id: $("#import_single_any_modal #file_id").val(),
        };
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/operations/for-shipping-today/upload/shipping-label/delete`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable(data);
                    $("#import_single_any_modal #chip_controller").hide();
                    // $("#import_single_any_modal #file").show();
                    $('#chip_div').hide();
                    $('#attachment_div').show();
                    Swal.close();
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }
        });

        
    }

    function reloadDataTable(data)
    {
        table_operation_order.ajax.reload(null, false);
        $(".imageuploadify-container").remove();
        $('#import_single_excel_modal').modal('hide');
        $('#import_single_any_modal').modal('hide');
        $('#upload_bulk_fst_shipping_label_modal').modal('hide');
        $('#update_for_shipping_today').modal('hide');
        sweetAlert2(data.status, data.message);
    }

</script>
@stop