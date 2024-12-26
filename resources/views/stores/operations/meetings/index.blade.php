@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">

            <div class="page-content">
                <!-- PAGE-HEADER -->
                @include('layouts/pageContentHeader/store')
                <!-- PAGE-HEADER END -->


                <div class="row">
                    <div class="col-3">
                        @include('stores/operations/meetings/partials/menu')
                    </div>

                    <div class="col-9">

                        <!--start row-->
                        <div class="card">
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-3 col-lg-3">
                                        <select class="form-select fw-bold" onchange="javascript: window.location.href = `/store/operations/{{request()->id}}/meetings/${this.value}/{{request()->month_number}}`;">
                                            <option value="{{date('Y')+1}}">Year {{date('Y')+1}}</option>
                                            @for($a = date('Y'); $a >= (date('Y') -3); $a--)
                                                @if(request()->year == $a)
                                                    <option value="{{$a}}" selected>Year {{$a}}</option>
                                                @else
                                                    <option value="{{$a}}">Year {{$a}}</option>
                                                @endif
                                            @endfor
                                        </select>
                                    </div>
                                
                                    <div class="col-9 col-lg-9">
                                        <div class="fm-search">
                                            <div class="mb-0">
                                                <div class="input-group input-group-lg">
                                                    <span class="bg-transparent input-group-text"><i class='fa fa-search'></i></span>
                                                    <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search the files" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                    
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        @include('stores/operations/meetings/partials/folders')
                                    </div>
                                </div>
                                <div class="row ms-2 me-3">
                                    <div class="col-12 col-lg-12">
                                        @include('stores/operations/meetings/partials/table')
                                    </div>
                                </div>
        
                            </div>
                        </div>
                        <!--end row-->
                    
                    </div>
                </div>

                
                
            </div>
            @include('sweetalert2/script')
            @include('stores/operations/meetings/modals/delete')
            @include('stores/operations/meetings/modals/add')
            @include('stores/operations/meetings/modals/edit-folder')
            @include('components/modal/delete-store-folder')
        
		</div>
		<!--end page wrapper -->
@stop

<style>
    .selected-month {
        border-left: 1px solid #dee2e6 !important; 
        border-right: 1px solid #dee2e6 !important; 
        cursor: pointer;
    }
    .selected-month-no-border-bottom {
        border-bottom: 0px !important;
    }
    .text-success2 {
        color: #35623d !important;
    }

    .bg-all-files {
        border: solid 2px #81d995 !important;
    }
    .bg-all-files:hover {
        background-color: #c8f4d296 !important;
    }
    .text-all-files, .text-all-files:hover {
        background-color: #c8f4d296 !important;
        color: #5f5f5f !important;  /*#81d995 !important;*/
    }

</style>

@section('pages_specific_scripts')  
<script>
    let table_document;
    let menu_store_id = {{request()->id}};
    let $year = {{request()->year}};
    let $month = {{ isset(request()->month_number) ? request()->month_number : null }};
    let folder_id;
    let page_id = 79;
    let $month_year = null;
    let $month_week = null;

    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        page_id = 79;
        $month_year = null;
        $month_week = null;
        $year = {{request()->year}};
        $month = {{ isset(request()->month_number) ? request()->month_number : null }};
        folder_id;

        new PerfectScrollbar('#knowledge-base-icon-lists');

        loadDocuments();

        let url_folder_id = getUrlParamValue('folder_id');
        if(url_folder_id) {
            clickFolder(url_folder_id);
        }
    });
    

    $('#add_modal #month_year').datepicker({
        format: "yyyy-mm",
        startView: "months", 
        minViewMode: "months",
        autoclose:true,
        // endDate: new Date()
    });

    $('#add_modal #month_year').change(function(){ 
        filterWeeksByMonthYear();
    });

    function loadDocuments() {
        
        let data = {};
        const dt_table = $('#dt_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            pageLength: 10,
            dom: 'fBtp',
            order: [[0, 'asc']],
            buttons: [
                
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/store/operations/meetings/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                    data.page_id = page_id;
                    data.page_code = 'meetings';
                    if(folder_id) {
                        data.folder_id = folder_id;
                    }
                    if($year) {
                        data.year = $year;
                    }
                    if($month) {
                        data.month = $month;
                    }
                    if($month_week) {
                        data.month_week = $month_week;
                    }
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'name', name: 'name', title: 'File', 
                    render: function(data, type, row) {
                        return `${row.file}`;
                    } 
                },
                // { data: 'page_name', name: 'page_name', title: 'Folder', orderable: false, searchable: false},
                { data: 'folder_name', name: 'folder_name', title: 'Folder', orderable: false, searchable: false,
                    render: function(data, type, row) {
                        return `${row.formatted_folder_name}`;
                    }
                },
                { data: 'actions', name: 'actions', title: 'Action', orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = dt_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_document = dt_table;
        table_document.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(table_document.search());
		$('#search_input').keyup(function(){ table_document.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_document.page.len($(this).val()).draw() });
    }

    // Handle change event on the dropdown
    $('#add_modal #folder_id').change(function() {
        // Check if the selected value is 'new'
        if ($(this).val() === '') {
            $('#add_modal #for-new-folder').show();  // Show the div if 'new' is selected
        } else {
            $('#add_modal #for-new-folder').hide();  // Hide the div otherwise
        }
    });

    function reloadDataTable(refresh = false)
    {
        if(refresh === false) {
            table_document.ajax.reload(null, false);
        } else {
            location.reload();
        }
    }

    $('#add_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();   
        $('#add_modal #file').remove();
        $('#add_modal .imageuploadify').remove();
        $('#add_modal #month_year').val($month_year);
        filterWeeksByMonthYear();
    });

    function clickFolder(selected_folder_id) {
        $('.folder_bg_all').addClass('bg-white');
        if(selected_folder_id == folder_id) {
            folder_id = null;
        } else {
            folder_id = selected_folder_id;
            $(`#folder_bg_${folder_id}`).removeClass('bg-white');
        }
        reloadDataTable();
    }
</script>

@stop