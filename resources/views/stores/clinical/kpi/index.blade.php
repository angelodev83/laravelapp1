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
								
								<div class="icalendar__month">
									<div class="icalendar__prev" onclick="moveDate('prev')">
										<span>&#10094</span>
									</div>
									<div class="icalendar__current-date">
										<h2 id="icalendarMonth"></h2>
										<div>
											<div id="icalendarDateStr"></div>
										</div>

									</div>
									<div class="icalendar__next" onclick="moveDate('next')">
										<span>&#10095</span>
									</div>
								</div>
								<div class="icalendar__week-days">
									<div>Sun</div>
									<div>Mon</div>
									<div>Tue</div>
									<div>Wed</div>
									<div>Thu</div>
									<div>Fri</div>
									<div>Sat</div>
								</div>
								<div class="icalendar__days"></div>
								
							</div>
						</div>
                    </div>

                    <div class="col-xl-8 col-md-12">
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
            @include('stores/clinical/kpi/modal/selection')
            @include('stores/clinical/kpi/modal/add')
    		@include('stores/clinical/kpi/modal/edit')
    		@include('stores/clinical/kpi/modal/delete')
           
            <!-- include('stores/procurement/pharmacy/inmarReturns/modal/view-form') -->
		</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts')
@include('stores/clinical/kpi/partials/calendar-script')
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
            minimumInputLength: 1,
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
            error: function (data) {
                handleErrorResponse(data);
            }
        });
    }

    function showAddNewForm(){  
        $(`#add_modal #status_id`).empty();
        searchSelect2Api('patient_id', 'add_modal', "/admin/patient/getNames", {source: 'pioneer'});     
        populateNormalSelect(`#status_id`, '#add_modal', '/admin/search/store-status', {category: 'kpi_call_status'}, 811)
        
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

    function getTodayDate() {
        let today = new Date();
        let dd = String(today.getDate()).padStart(2, '0');
        let mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
        let yyyy = today.getFullYear();
        return yyyy + '-' + mm + '-' + dd;
    }

    $(document).ready(function() {
        // dateFilter = getTodayDate();
        const today = getTodayDate();//new Date().toISOString().split('T')[0];
        // Initialize selectedDate with today's date
        selectedDate = today;
        // console.log(today);
        const table = $('#table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            // processing: true,
            // language: {
            //     processing: '<div class="spinner"></div>' // Use custom spinner
            // },
            processing: true,
            language: {
                loadingRecords: '&nbsp;',
                processing: '<div class="spinner"></div>'
            },
            dom: 'fBtip',
            order: [[0, 'desc']],
            buttons: [
                @can('menu_store.clinical.kpi.create')
                { text: '+ New', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
                @endcan
            ],
            pageLength: 50,
            searching: true,
            ajax: {
                url: `/store/clinical/kpi/data`,
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
                { data: 'created_by', name: 'user_id', title: 'Created By' },
                { data: 'created_at', name: 'created_at', title: 'Date Created' },
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
        dt_table.ajax.reload(); // Reload DataTable with new date filter
    });
</script>
@stop