@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">

            <div class="page-content">
                <!-- PAGE-HEADER -->
                @include('layouts/pageContentHeader/index')
                <!-- PAGE-HEADER END -->

                <!--start row-->
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-12 col-lg-9">
                                <div class="fm-search">
                                    <div class="mb-0">
                                        <div class="input-group input-group-lg">
                                            <span class="bg-transparent input-group-text"><i class='fa fa-search'></i></span>
                                            <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search the files" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
            
                                @include('humanResources/fileManager/partials/folders')
                            </div>
                            <div class="col-12 col-lg-3">
                                @canany($permissions['create'])
                                @if(!empty($page_id))
                                <div class="mb-3 d-grid w-100">
                                    <button class="btn btn-default d-flex align-items-center justify-content-center" style="background-color: #b834af; color: white;" onclick="clickUploadBtn()">
                                        <i class="p-2 fa fa-cloud-arrow-up me-2"></i>Upload Documents
                                    </button>
                                </div>
                                @endif
                                @endcanany
                                @include('humanResources/fileManager/partials/recent-upload-files')
                            </div>
                        </div>
                        <div class="row ms-2 me-3">
                            <div class="col-12 col-lg-12">
                                @include('humanResources/fileManager/partials/table')
                            </div>
                        </div>

                    </div>
                </div>
                <!--end row-->
                
                
            </div>
            @include('sweetalert2/script')
            @include('humanResources/fileManager/modals/delete')
            @include('humanResources/fileManager/modals/add')
            @include('humanResources/fileManager/modals/edit-folder')
            @include('components/modal/delete-store-folder')
        
		</div>
		<!--end page wrapper -->
@stop

@section('pages_specific_scripts')  
<script>
    let table_document;
    let folder_id;
    let page_id = 90;

    $(document).ready(function() {
        page_id = 90;

        $('#knowledge-base-lgi-sops').addClass('selected-sop');

        new PerfectScrollbar('#knowledge-base-icon-lists');

        loadRecentFiles(90);
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
                url: "file-manager/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.page_id = page_id;
                    data.page_code = 'file_manager';
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