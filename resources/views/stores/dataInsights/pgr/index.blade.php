@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!-- PAGE-HEADER -->
				@include('layouts/pageContentHeader/store')
				<!-- PAGE-HEADER END -->
                <div class="col-lg-12">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <input type='text' readonly id='search_fromdate' class="form-control datepicker" placeholder='From date'>
                        </div>
                        <div class="col-md-2">
                            <input type='text' readonly id='search_todate' class="form-control datepicker" placeholder='To date'>
                        </div>
                        <div class="col-md-2">
                            <input type='button' class="btn btn-primary" id="btn_search" value="Search">
                        </div>
                    </div>
                </div>     
                
                </br>
				<div class="card">
                    <div class="card-header dt-card-header">
                        <select name='length_change' id='length_change' class="table_length_change form-select">
                        </select>
                        <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id="monthlyRevenue_table" class="table row-border hover" style="width:100%">
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
			@include('stores/dataInsights/pgr/modal/add-monthlyrevenue-form')
    		@include('stores/dataInsights/pgr/modal/edit-monthlyrevenue-form')
    		@include('stores/dataInsights/pgr/modal/delete-monthlyrevenue-form')
		</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts')

<script>
    let table_monthlyRevenue;
    let menu_store_id = {{request()->id}};

    $('.amount_only').keyup(function(e) {
        // Remove non-digit characters
        this.value = this.value.replace(/[^\d.-]/g, '');

        // Check for multiple negative signs
        var negativeIndex = this.value.indexOf('-');
        if (negativeIndex !== -1 && negativeIndex !== 0) {
            // If negative sign is not at the start, remove it
            this.value = this.value.slice(0, negativeIndex) + this.value.slice(negativeIndex + 1);
        }

        // Check for multiple decimal points
        var decimalIndex = this.value.indexOf('.');
        if (decimalIndex !== -1) {
            var parts = this.value.split('.');
            if (parts.length > 2 || parts[1].length > 2) {
                // If more than one decimal point or more than two decimal digits after the point, keep only the valid portion
                this.value = parts[0] + '.' + parts[1].slice(0, 2);
            }
        }
    });



    $('#addMonthlyRevenue_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $('#editMonthlyRevenue_modal').on('hidden.bs.modal', function(){
        $("#efile").val(null);
    });

    function showAddNewForm(){
        $('#addMonthlyRevenue_modal').modal('show');
        $('#month').datepicker({
            format: "yyyy-mm",
            startView: "months", 
            minViewMode: "months",
            autoclose:true
        }).on("keydown cut copy paste",function(e) {
            e.preventDefault();
        });

        $('#store_select').select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#addMonthlyRevenue_modal .modal-content'),
        });

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: `/store/data-insights/${menu_store_id}/pgr/get_store_data`,
            
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
                var len = data.data.length;
                
                // $("#store_select").empty();
                // $("#store_select").append("<option value='' selected></option>");
                for( var i = 0; i<len; i++){
                    var name = data.data[i].code;
                    var id = data.data[i].id;
                    if(menu_store_id === id){
                        $("#addMonthlyRevenue_modal .modal-title").text("Add Pharmacy Gross Revenue "+name);
                        $("#addMonthlyRevenue_modal #store").val(id);
                    }
                
                }
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function showEditForm(data){
        $('#editMonthlyRevenue_modal').modal('show');
        $('#emonth').datepicker({
            format: "yyyy-mm",
            startView: "months", 
            minViewMode: "months",
            autoclose:true
        }).on("keydown cut copy paste",function(e) {
            e.preventDefault();
        });
        $('#estore_select').select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#editMonthlyRevenue_modal .modal-content'),
        });

        let id = $(data).data('id');
        let name = $(data).data('name');
        let filename = $(data).data('filename');
        let fileid = $(data).data('fileid');
        let storeid = $(data).data('storeid');
        let month = $(data).data('month');
        let amount = $(data).data('amount');

        const startDateInput = document.getElementById('emonth');
        startDateInput.value = month;

        if(fileid == ''){
            $("#chip_controller").hide();
            $("#efile").show();
        }
        else{
            $("#chip_controller").show();
            $("#efile").hide();
        }
        
        $('.file_name').text(filename);
        $("input#eid").val(id);
        $("input#efile_id").val(fileid);
        $("input#ename").val(name);
        $("input#eamount").val(amount);
        
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: `/store/data-insights/${menu_store_id}/pgr/get_store_data`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
                var len = data.data.length;
                
                $("#estore_select").empty();
                $("#estore_select").append("<option selected value=''></option>");
                for( var j = 0; j<len; j++){
                    var store = data.data[j].code;
                    let id = data.data[j].id;
                    if(id==storeid){
                        $("#estore_select").append("<option selected value='"+id+"'>"+store+"</option>");
                        $("#editMonthlyRevenue_modal .modal-title").text("Edit Pharmacy Gross Revenue "+store);
                    }
                    else{
                        $("#estore_select").append("<option value='"+id+"'>"+store+"</option>");
                    }
                
                }
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });

        

    }

    $(document).ready(function() {

        // Datepicker 
        $( ".datepicker" ).datepicker({
            format: "yyyy-mm-dd",
            changeYear: true,
            todayHighlight: true,
            uiLibrary: 'bootstrap5', modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false
        }).on('changeDate', function(e){
            $(this).datepicker('hide');
        });

        let monthlyRevenue_table = $('#monthlyRevenue_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            dom: 'fBtp',
            pageLength: 50,
            order: [[0, 'desc']],
            searching: true,
            buttons: [
                { text: 'Upload', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
            ],
            ajax: {
                url: `/store/data-insights/${menu_store_id}/pgr/data`,
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    //read values
                    var from_date = $('#search_fromdate').val();
                    var to_date = $('#search_todate').val();
                    //append to data
                    data.searchByFromDate = from_date;
                    data.searchByToDate = to_date;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'id', name: 'monthly_revenues.id', title: 'ID' },
                { data: 'name', name: 'name', title: 'FILE NAME'},
                { data: 'month', name: 'month', title: 'MONTH COVERED' },
                { data: 'amount', name: 'amount', title: 'AMOUNT' },
                { data: 'created_by', name: 'created_by', title: 'CREATED BY' },
                { data: 'updated_by', name: 'updated_by', title: 'MODIFIED BY' },
                { data: 'updated_at', name: 'monthly_revenues.updated_at', title: 'DATE MODIFIED' },
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable:false, orderable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = monthlyRevenue_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        // Search button
        $('#btn_search').click(function(){
            monthlyRevenue_table.draw();
        });

        table_monthlyRevenue = monthlyRevenue_table;

        // Placement controls for Table filters and buttons
		monthlyRevenue_table.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(monthlyRevenue_table.search());
		$('#search_input').keyup(function(){ monthlyRevenue_table.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { monthlyRevenue_table.page.len($(this).val()).draw() });

    });
</script>
@stop