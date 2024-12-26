@extends('layouts.master')
@section('content')
@include('components/calendar-custom-css')

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
                            @include('components/calendar-custom')
                        </div>
                    </div>

                    @include('stores/clinical/therapyChangeAndReco/partials/summary')
                </div>

                <div class="col-xl-8 col-md-12">
                    <div class="card">
                        <div class="card-header dt-card-header">

                            <div class="row mx-2">
                                <div class="col-3 mt-2">
                                    <h6><i class="fa fa-calendar-day me-2 text-danger"></i><span class="text-danger" id="titleDate"></span></h6>
                                </div>
                                <div class="col-9">
                                    @can('menu_store.clinical.therapy_change_and_reco.create')
                                    <a style="width: fit-content;" class="btn btn-info2 ms-2 table_search_input" onclick="clickSendMailBtn()">Send <i class="bx bx-mail-send"></i></a>
                                    @endcan

                                    @can('menu_store.clinical.therapy_change_and_reco.delete')
                                        <div class="dropdown ms-2 table_search_input" style="width: fit-content;">
                                            <button class="btn btn-danger dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item d-flex text-danger" href="javascript:deleteSelected();">
                                                        Delete <i class="fa-solid fa-trash-can ms-auto text-danger"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    @endcan

                                    <a style="width: fit-content;" class="btn btn-primary ms-2 table_search_input" onclick="clickExportBtn()">Export <i class="fa fa-download ms-2"></i></a>
                                    
                                    @can('menu_store.clinical.therapy_change_and_reco.create')
                                    <a style="width: fit-content;" class="btn btn-success ms-2 table_search_input" onclick="clickImportBtn()">Import <i class="fa fa-upload ms-2"></i></a>
                                    @endcan

                                </div>
                            </div>
                            
                        </div>
                        <div class="card-body">
                            <div class="row border-bottom pb-2">
                                <div class="col-md-9">
                                    <div class="fm-search">
                                        <div class="mb-0">
                                            <div class="input-group input-group-md">
                                                <span class="input-group-text"><i class='fa fa-search'></i></span>
                                                <input type="text" id="search_input" class="form-control" placeholder="Type here to search ..." autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name='length_change' id='length_change' class="form-select">
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="table" class="table row-border table-hover" style="width:100%">
                                    <thead></thead>
                                    <tbody>
                                            <tr>
                                            <td>
                                                <div class="text-center dt-loading-spinner">
                                                    <i class="fas fa-spinner fa-spin fa-3x"></i>
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
        @include('stores/clinical/therapyChangeAndReco/modal/add')
        @include('stores/clinical/therapyChangeAndReco/modal/edit')
        @include('stores/clinical/therapyChangeAndReco/modal/delete')
        @include('stores/clinical/therapyChangeAndReco/modal/delete-all')
        @include('components/modal/import-single-excel')
    </div>
<!--end page wrapper -->
@stop

@section('pages_specific_scripts')

<script>
    let dt_table;
    let file_table;
    let menu_store_id = {{request()->id}};
    let selectedDate = '';
    let $_CALENDAR_URL = '/store/clinical/'+{{request()->id}}+'/therapy-change-and-reco/date-with';
    let selectedIds = {}; 

    function searchSelect2Api(_select_id, _modal_id, _url, condition = null){
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

    function getTodayDate() {
        let dt = new Date();
        let today = new Date(dt.toLocaleString('en-US', { timeZone: 'America/Los_Angeles' }));
        let dd = String(today.getDate()).padStart(2, '0');
        let mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
        let yyyy = today.getFullYear();
        return yyyy + '-' + mm + '-' + dd;
    }

    $(document).ready(function() {
        selectedIds = {}; 
        const today = getTodayDate();
        
        selectedDate = today;
        $('#titleDate').text(selectedDate);
        const table = $('#table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            processing: true,
            language: {
                loadingRecords: '&nbsp;',
                processing: '<div class="spinner"></div>'
            },
            dom: 'fBtip',
            order: [[1, 'asc']],
            buttons: [
                @can('menu_store.clinical.therapy_change_and_reco.create')
                @endcan
            ],
            pageLength: 50,
            searching: true,
            ajax: {
                url: `/store/clinical/therapy-change-and-reco/data`,
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    console.log(selectedDate);
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                    data.date_filter = selectedDate;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { 
                    data: 'checkbox', 
                    name: 'checkbox',
                    defaultContent: '',
                    title: '<input type="checkbox" id="select_all" />', 
                    render: function(data, type, row){
                        return `<input type="checkbox" class="row-checkbox" name="id[]" value="${row.id}">`;
                    },
                    orderable: false,
                    searchable: false,
                    className: 'select-checkbox',
                    width: "1%"
                },
                // { data: 'rx_number', name: 'rx_number', title: 'Script No.'},
                { data: 'patient_name', name: 'patient_name', title: 'Patient Name', orderable: false, render: function(data, type, row) {
                    return `${row.formatted_patient_name}`;
                } },
                { data: 'medication_description', name: 'medication_description', title: 'Brand Reco'},
                { data: 'status_name', name: 'status_name', title: 'Status'},
                { data: 'last_provider_that_sent_rx', name: 'last_provider_that_sent_rx', title: 'Provider'},
                { data: 'is_switched', name: 'is_switched', title: 'Is Switched'},
                { data: 'pertinent_financial_info', name: 'pertinent_financial_info', title: 'Pertinent Financial Info'},
                { data: 'remarks', name: 'remarks', title: 'Remarks'},
                // { data: 'created_at', name: 'created_at', title: 'Date Created', render: function(data, type, row) {
                //     return `${row.formatted_created_at}`;
                // } },
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                generateSummary();
                selected_len = table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));

                $('#select_all').on('click', function() {
                    // Only toggle checkboxes on the current page
                    let rows = table.rows({ 'page': 'current' }).nodes();
                    let checkboxes = $('input[type="checkbox"].row-checkbox', rows);
                    let isChecked = this.checked;

                    checkboxes.each(function() {
                        let id = $(this).val();

                        if (isChecked) {
                            // If select all is checked, add IDs to selectedIds
                            selectedIds[id] = true;
                        }
                    });

                    // Toggle checkboxes
                    checkboxes.prop('checked', isChecked);
                });

                $('#table tbody').on('change', 'input[type="checkbox"].row-checkbox', function() {
                    let id = $(this).val();
                    let isChecked = this.checked;

                    if (isChecked) {
                        // If the checkbox is checked
                        // Add ID to selectedIds object if it's not already there
                        if (!selectedIds[id]) {
                            selectedIds[id] = true;
                        }
                    }
                });

                // Store the state of checkboxes when pagination changes
                table.on('draw.dt', function() {
                    updateCheckboxesState();
                });

                // Update checkboxes state immediately after DataTable is initialized
                updateCheckboxesState();

                table.on('page.dt', function() {
                    $('#select_all').prop('checked', false);
                });
            },
        });

        dt_table = table;
        // Placement controls for Table filters and buttons
		table.buttons().container().appendTo( '.dt-card-header' ); 
        $('#search_input').val(table.search());
		$('#search_input').keyup(function(){ table.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table.page.len($(this).val()).draw() });

    });

    function updateCheckboxesState() {
        let currentPageCheckboxes = $('input[type="checkbox"].row-checkbox', dt_table.rows({ 'page': 'current' }).nodes());

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
            }
        });

        // Identify unchecked checkboxes that were checked initially
        $('input[type="checkbox"].row-checkbox', dt_table.rows().nodes()).each(function() {
            let id = $(this).val();
            let isChecked = $(this).prop('checked');
        });
    }

    function clearSelection() {
        selectedIds = {};
    }

    function deleteSelected(){
        var checkedIds = Object.keys(selectedIds).filter(function(id) {
            return selectedIds[id];
        });
        let data = {
            selectedIds: checkedIds
        };
        
        console.log('data',data);

        $('#delete_all_form_modal').modal('show');
        $('#delete_all_form_modal #id').val('');
        $('#title_delete_all_id_text').empty();
        $('#title_delete_all_id_text').append('Unique IDs: ');
        for(let i =0; i < data.selectedIds.length; i++ ) {
            $('#title_delete_all_id_text').append(data.selectedIds[i]  + ', ')
        }
    }

    function reloadDataTable(data)
    {
        renderDate();
        dt_table.ajax.reload(null, false);
        sweetAlert2(data.status, data.message);
        $('#import_single_excel_modal').modal('hide');
        generateSummary();
        clearSelection();
    }

    function clickExportBtn() {
        const date = $('#titleDate').text();
        const search = $('#search_input').val();

        let url = `therapy-change-and-reco/export`;

        // Initialize an array to store query parameters
        let params = [];

        // Add parameters to the array if they are not empty
        if (date) {
            params.push(`date=${encodeURIComponent(date)}`);
        }
        if (search) {
            params.push(`search=${encodeURIComponent(search)}`);
        }

        // If there are any parameters, append them to the URL
        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        // Open the URL in a new tab
        window.open(url, '_blank');
    }

    function clickImportBtn() {
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
        
        customizeImportModal();
        $('#import_single_excel_modal').modal('show');
    }

    function saveImportSingleExcel()
    {
        let data = {
            date: $('#titleDate').html()
        };
        proceedImportSingleExcel('/store/clinical/'+menu_store_id+'/therapy-change-and-reco/import', data);
    }

    // Event listener for calendar date click
    $(document).on('click', '.icalendar__date, .icalendar__today, .icalendar__prev-date, .icalendar__next-date', function() {
        selectedDate = $(this).attr('data-date');
        $('#titleDate').text(selectedDate);
        dt_table.ajax.reload(); // Reload DataTable with new date filter
        generateSummary();
        clearSelection();

        const today = getTodayDate();
        $(`#${today}`).css('background-color', 'white');
        $(`#${today}`).css('color', 'black');
        $('.icalendar__date').css('background-color', 'white');
        $('.icalendar__date').css('color', 'black');
        $(`#${selectedDate}`).css('background-color', '#15a0a3');
        $(`#${selectedDate}`).css('color', 'white');
    });

    function generateSummary()
    {
        let data = {
            date: $('#titleDate').text(),
            pharmacy_store_id: menu_store_id
        };
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/clinical/therapy-change-and-reco/summary`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            success: function(response) {
                const res = response.data;

                $('#summary_title').text('Summary: '+res.formatted_date);
                $('#summary_recommended').text(res.count_rx_numbers);
                $('#summary_patients').text(res.count_patient_names);
                $('#summary_is_switched_yes').text(res.count_is_switched_yes);
                $('#summary_is_switched_no').text(res.count_is_switched_no);
                $('#summary_profits').text('$ '+res.formatted_computed_total_profit);

            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }

        });
    }

    function clickSendMailBtn()
    {
        let data = {
            date: $('#titleDate').text(),
            pharmacy_store_id: menu_store_id
        };
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/clinical/therapy-change-and-reco/send-mail`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            success: function(response) {
                const res = response.data;

                sweetAlert2(response.status, response.message);

            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }

        });
    }

    function customizeImportModal()
    {
        $('#import_single_excel_modal #guide_table').removeClass('d-none');
        $('#import_single_excel_modal #guide_table').html(`
            <p class="my-1 py-0">
                ** Check file <i>Clinical Import Excel Template - Therapy Change and Reco.xlsx</i> from <a href="/store/knowledge-base/1/process-documents?folder_id=90" target="_blank">Knowledge Base > Process Documents > <u>Templates</u></a>
            </p>
            <label class="form-label">** Please follow this Excel File Format (Sample ONLY Template)</label>
            <div class="table-responsive mb-0 pb-0" style="scrollbar-width: thin;">
                <table class="table table-border" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Last Provider that sent Rx</th>
                            <th>Meds</th>
                            <th>Recommendation</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Lastname, Firstname</td>
                            <td>Franklin, Laura</td>
                            <td>Metformin Hcl 1,000 Mg Tablet</td>
                            <td>Switch to Januvia</td>
                            <td>TRHC patient</td>
                        </tr>    
                        <tr>
                            <td>Doe, John Test</td>
                            <td>Dodson, Anthony</td>
                            <td>Metformin 500 mg tablet</td>
                            <td>Switch to Januvia</td>
                            <td>(Optional)</td>
                        </tr>                               
                    </tbody>
                </table>
            </div>
        `);
    }
</script>

@include('components/calendar-custom-js')

@stop