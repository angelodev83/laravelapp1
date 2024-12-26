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

  .dataTables_processing {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    text-align: center;
    color: #fff;
    background-color: rgba(0, 0, 0, 0.6);
    z-index: 10000;
}

/* .spinner {
    display: inline-block;
    width: 80px;
    height: 80px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
} */

@keyframes spinner {
  to {transform: rotate(360deg);}
}
 
.spinner:before {
  content: '';
  box-sizing: border-box;
  position: absolute;
  top: 50%;
  left: 50%;
  width: 20px;
  height: 20px;
  margin-top: -10px;
  margin-left: -10px;
  border-radius: 50%;
  border: 2px solid #ccc;
  border-top-color: #333;
  animation: spinner .6s linear infinite;
}

</style>
@include('stores/clinical/kpi/partials/calendar')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!-- PAGE-HEADER -->
				@include('layouts/pageContentHeader/store')
				<!-- PAGE-HEADER END -->
                <div class="row">
                    <div class="col-xl-4 col-md-12">
                        <div class="card">
							<div class="card-body">
								@include('stores/clinical/outreach/partials/calendar-html')
							</div>
						</div>
                    </div>

                    <div class="col-xl-8 col-md-12">
                        <div class="card">
                            <div class="card-header dt-card-header">
                                
                                @can('menu_store.clinical.outreach.create')
                                <a style="width: fit-content;" class="btn btn-primary ms-2 table_search_input" onclick="showAddNewForm()">+ New</a>
                                @endcan
                                <select name='length_change' id='length_change' class="table_length_change form-select">
                                </select>
                                <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">

                                <h6 style="float: left;" class="table_search_input ms-3">Outreach for: <span id="titleDate"></span></h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="table" class="table row-border table-hover" style="width:100%">
                                        <thead></thead>
                                        <tbody>
                                             <tr>
                                                <td>
                                                    <div class="text-center dt-loading-spinner">
                                                        <i class="fas fa-spinner fa-spin fa-3x"></i> <!-- Example: Font Awesome spinner icon -->
                                                    </div>
                                                </td>
                                            </tr>                                     
                                        </tbody>
                                        <tfooter></tfooter>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
			@include('sweetalert2/script')
            @include('stores/clinical/outreach/modal/selection')
            @include('stores/clinical/outreach/modal/add')
    		@include('stores/clinical/outreach/modal/edit')
    		@include('stores/clinical/outreach/modal/delete')
           
            <!-- include('stores/procurement/pharmacy/inmarReturns/modal/view-form') -->
		</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts')
@include('stores/clinical/outreach/partials/calendar-script')
<script>
    let dt_table;
    let file_table;
    let menu_store_id = {{request()->id}};
    let selectedDate = '';

    function searchSelect2Api(_select_id, _modal_id, _url, condition = null){
        // console.log("_select_id", _select_id);
        // console.log("_modal_id", _modal_id);
        // console.log("_url", _url);
        $(`#${_modal_id} #${_select_id}`).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $(`#${_modal_id} .modal-content`),

            multiple: false,
            minimumInputLength: 3,
            minimumResultsForSearch: 10,
            ajax: {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: _url,
                dataType: "json",
                type: "POST",
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        limit: 10
                    }
                    if(condition != null) {
                        queryParameters = {...queryParameters, ...condition};
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            console.log("item", item);
                            console.log("===============================================");
                            return {
                                text: item.name,
                                id: item.id
                            }   
                        })
                    };
                }  
            },
        });
    }

    function populateNormalSelect(_selector, _modal_id, _url, params = {}, _id = null){   
        $(`${_modal_id} ${_selector}`).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $(`${_modal_id} .modal-content`),
        });
        
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
                    if(kid==_id){$(_modal_id+' '+_selector).append("<option selected value='"+kid+"'>"+kname+"</option>");}
                    else{
                        $(_modal_id+' '+_selector).append("<option value='"+kid+"'>"+kname+"</option>");
                    }
                }
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function populateCheckBox(_modal_id, _selector, _url, params = {}, arr = []){
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: _url,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(params),
            success: function(data) {
                console.log(arr);
                var len = data.data.length;
                
                for( var k = 0; k<len; k++){
                    var kid = data.data[k]['id'];
                    var kname = data.data[k]['name'];
                    if($.inArray(kid, arr) !== -1){$(_modal_id+' '+_selector).append(`<div class="col-6 form-check">
                                <input class="form-check-input diagnosis-checkbox" checked type="checkbox" value="${kid}" id="${kname}">
                                <label class="form-check-label" for="${kname}">${kname}</label>
                            </div>`);}
                    else{
                        $(_modal_id+' '+_selector).append(`<div class="col-6 form-check">
                                <input class="form-check-input diagnosis-checkbox" type="checkbox" value="${kid}" id="${kname}">
                                <label class="form-check-label" for="${kname}">${kname}</label>
                            </div>`);
                    }
                }
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function showAddNewForm(){  
        $(`#add_modal #status_id`).empty();
        searchSelect2Api('patient_id', 'add_modal', "/admin/patient/getNames", {source: 'pioneer'});     
        populateNormalSelect(`#status_id`, '#add_modal', '/admin/search/store-status', {category: 'kpi_call_status'}, 811);
        populateCheckBox('#add_modal', '#diagnosis', "/admin/search/store-status", {category: 'diagnosis'});
        populateNormalSelect(`#provider_id`, '#add_modal', '/admin/search/store-status', {category: 'clinical_provider'});
        searchSelect2Api('employee_id', 'add_modal','/admin/search/user-employee');
        
        $('#add_modal #time_start').off('click').on('click', function() {
            $(this).attr('readonly', false); // Temporarily remove readonly
            // $(this).focus(); // Open the time picker
            var input = $(this); 
                setTimeout(function() {
                    input[0].showPicker(); // Show the time picker
                }, 10);
        });

        $('#add_modal #time_start').off('blur').on('blur', function() {
            $(this).attr('readonly', true); // Re-add readonly when the input loses focus
        });

        $('#add_modal #time_end').off('click').on('click', function() {
            $(this).attr('readonly', false); // Temporarily remove readonly
            // $(this).focus(); // Open the time picker
            var input = $(this); 
                setTimeout(function() {
                    input[0].showPicker(); // Show the time picker
                }, 10);
        });

        $('#add_modal #time_end').off('blur').on('blur', function() {
            $(this).attr('readonly', true); // Re-add readonly when the input loses focus
        });

        $('#add_modal .timeChange').off('change').on('change', function() {
            computeTotalTime('#add_modal');
        });

        computeTotalTime('#add_modal');
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

        // Set the default date to today
        $('.datepicker').datepicker('setDate', new Date());
    }

    $('#edit_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
        $('#edit_modal #diagnosis').empty();
        $('#edit_modal #provider_id').empty();
        $('#edit_modal #status_id').empty();
    });

    $('#add_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();  
        $('#add_modal #diagnosis').empty();
        $('#add_modal #provider_id').empty();
        $('#add_modal #status_id').empty(); 
    });

    function removeSeconds(timeString) {
        return timeString.slice(0, 5);
    }

    function computeTotalTime(_modal_id){
        console.log('change');
        var timeStart = $(`${_modal_id} #time_start`).val();
        var timeEnd = $(`${_modal_id} #time_end`).val();

        if (timeStart && timeEnd) {
            // Parse hours and minutes from time strings
            var [startHours, startMinutes] = timeStart.split(':').map(Number);
            var [endHours, endMinutes] = timeEnd.split(':').map(Number);

            // Convert time to total minutes since midnight
            var startTimeInMinutes = startHours * 60 + startMinutes;
            var endTimeInMinutes = endHours * 60 + endMinutes;

            // Calculate duration in minutes
            var durationInMinutes = endTimeInMinutes - startTimeInMinutes;

            // Convert duration back to hours and minutes format
            var durationHours = Math.floor(durationInMinutes / 60);
            var durationMinutes = durationInMinutes % 60;

            // Display or log the result
            var formattedDuration = durationHours.toString().padStart(2, '0') + ':' + durationMinutes.toString().padStart(2, '0');
            console.log('Total Time Duration: ' + formattedDuration);
            
            $(`${_modal_id} #total_time`).val(formattedDuration);
        }

    }

    function getTodayDate() {
        let today = new Date();
        let dd = String(today.getDate()).padStart(2, '0');
        let mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
        let yyyy = today.getFullYear();
        return yyyy + '-' + mm + '-' + dd;
    }

    $(document).ready(function() {
        const today = getTodayDate();//new Date().toISOString().split('T')[0];
        
        selectedDate = today;
        $('#titleDate').text(selectedDate);
        const table = $('#table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            processing: true,
            language: {
                loadingRecords: '&nbsp;',
                processing: '<div class="spinner"></div>'
            },
            dom: 'fBtip',
            order: [[0, 'desc']],
            buttons: [
                @can('menu_store.clinical.kpi.create')
                // { text: '+ New', className: 'btn btn-primary ms-auto', action: function ( e, dt, node, config ) {
                //     showAddNewForm();
                // }},
                @endcan
            ],
            pageLength: 50,
            searching: true,
            ajax: {
                url: `/store/clinical/outreach/data`,
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    console.log(selectedDate);
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                    // data.date_filter = dateFilter;
                    data.date_filter = selectedDate;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'id', name: 'id', title: 'ID'},
                { data: 'date', name: 'date', title: 'Date'},
                { data: 'patient_name', name: 'patient_id', title: 'Patient Name', orderable: false },
                { data: 'diagnosis', name: 'diagnosis', title: 'Diagnosis', searchable: false, orderable: false },
                { data: 'created_by', name: 'user_id', title: 'Created By' },
                { data: 'created_at', name: 'created_at', title: 'Date Created', render: function(data, type, row) {
                    return `${row.formatted_created_at}`;
                } },
                // { data: 'user_id', name: 'user_id', title: 'Created By'},
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            },
        });

        dt_table = table;
        // Placement controls for Table filters and buttons
		table.buttons().container().appendTo( '.dt-card-header' ); 
        $('#search_input').val(table.search());
		$('#search_input').keyup(function(){ table.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table.page.len($(this).val()).draw() });

    });

    function reloadDataTable()
    {
        renderDate();
        dt_table.ajax.reload(null, false);
    }

    function reloadFileDataTable()
    {
        file_table.ajax.reload(null, false);
    }

    // Event listener for calendar date click
    $(document).on('click', '.icalendar__date, .icalendar__today, .icalendar__prev-date, .icalendar__next-date', function() {
        selectedDate = $(this).attr('data-date');
        $('#titleDate').text(selectedDate);
        dt_table.ajax.reload(); // Reload DataTable with new date filter
    });
</script>
@stop