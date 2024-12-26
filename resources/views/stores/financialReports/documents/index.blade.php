@extends('layouts.master')
@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
        <!-- PAGE-HEADER -->
        @include('layouts/pageContentHeader/store')
        <!-- PAGE-HEADER END -->

        <!--start row-->
        <div class="row">

            <div class="col-12 col-lg-3">
                @include('stores/financialReports/partials/menu')
            </div>

            <div class="col-12 col-lg-9">
                <div class="card">
                    <div class="card-body">

                        <div class="fm-search">
                            <div class="mb-0">
                                <div class="input-group input-group-lg">
                                    <span class="bg-transparent input-group-text"><i class='fa fa-search'></i></span>
                                    <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search the files" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <h6 class="mt-3">Folders</h6>
                        
                        @include('stores/financialReports/partials/folders')

                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">Files</h6>
                            </div>
                        </div>

                        @include('stores/financialReports/partials/table')

                    </div>
                </div>
            </div>
        </div>
        <!--end row-->
        
    </div>
    <!--end page wrapper -->
    @include('sweetalert2/script')
    @include('stores/financialReports/modals/delete')
    @include('stores/financialReports/modals/add')
@stop

@section('pages_specific_scripts')  
<script>

    let table_document;
    let menu_store_id = {{ request()->id }};
    let folder_id;
    let page_id = {{ request()->page_id }};
    let page_code = {!! json_encode(htmlspecialchars_decode($page->code)) !!};
    $(document).ready(function() {
        menu_store_id = {{ request()->id }};
        page_id = {{ request()->page_id }};
        page_code = {!! json_encode(htmlspecialchars_decode($page->code)) !!};

        // $(`#financial-report-lgi-${page_code}`).addClass('selected');

        loadDocuments();

        let url_folder_id = getUrlParamValue('folder_id');
        if(url_folder_id) {
            clickFolder(url_folder_id);
        }
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
                url: `/store/financial-reports/${page_id}/documents/${page_code}/data`,
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                    data.page_id = page_id;
                    data.page_code = page_code;
                    if(folder_id) {
                        data.folder_id = folder_id;
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
                // { data: 'size', name: 'size', title: 'Size', searchable: false,
                //     render: function(data, type, row) {
                //         return `<p>${row.formatted_size}</p>`;
                //     }
                // },
                // { data: 'last_modified', name: 'last_modified', title: 'Last Modified', searchable: false,
                //     render: function(data, type, row) {
                //         return `${row.formatted_last_modified}`;
                //     }
                // },
                // { data: 'created_at', name: 'created_at', title: 'Date Created', searchable: false,
                //     render: function(data, type, row) {
                //         return `${row.formatted_created_at}`;
                //     }
                // },
                // { data: 'created_by', name: 'created_by', title: 'Created By', orderable: false, searchable: false, 
                //     render: function(data, type, row) {
                //         return `${row.empAvatar}`;
                //     } 
                // },
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
    });

    function clickFolder(selected_folder_id) {
        folder_id = selected_folder_id;
        $('.financial-reports-folder-card').removeClass('financial-reports-folder-card-selected');
        $(`#financial-reports-folder-card-id-${selected_folder_id}`).addClass('financial-reports-folder-card-selected');
        $('.financial-reports-folder-card-icon').removeClass('fa-regular fa-folder-open');
        $('.financial-reports-folder-card-icon').addClass('fa-solid fa-folder-closed');
        $(`#financial-reports-folder-card-icon-id-${selected_folder_id}`).addClass('fa-regular fa-folder-open');
        reloadDataTable();
    }

</script>
@stop
