@extends('layouts.master')
@section('content')
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!-- PAGE-HEADER -->
        @include('layouts/pageContentHeader/store')
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
                        @include('stores/knowledgeBase/allFiles/partials/folders')
                    </div>
                    <div class="col-12 col-lg-3">
                        @include('stores/knowledgeBase/partials/menu')
                    </div>
                </div>
                <div class="pt-3 mx-2 row">
                    <div class="col-12 col-lg-12">
                        @include('stores/knowledgeBase/partials/table')
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->
    </div>
</div>
<!--end page wrapper -->
@include('sweetalert2/script')
@include('stores/knowledgeBase/modals/delete')
@include('stores/knowledgeBase/modals/add')
@stop

@section('pages_specific_scripts')  
<script>

let table_document;
    let menu_store_id = {{request()->id}};
    let folder_id;
    let page_id;

    $(document).ready(function() {
        menu_store_id = {{request()->id}};

        $('#knowledge-base-lgi-all-files').addClass('selected');

        loadDocuments();
        loadRecentFiles(34);

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
            // order: [[0, 'asc']],
            buttons: [
                
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/store/knowledge-base/all-files/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                    if(page_id) {
                        data.page_id = page_id;
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
                { data: 'page_name', name: 'page_name', title: 'Folder', orderable: false, searchable: false,
                    render: function(data, type, row) {
                        return `<b>${row.page_name}</b>`;
                    }
                },
                { data: 'folder_name', name: 'folder_name', title: 'Subfolder', orderable: false, searchable: false,
                    render: function(data, type, row) {
                        return `${row.formatted_folder_name}`;
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
        // Check if the selected value is ''
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

    function clickPageFolder(selected_page_id, _id) {
        page_id = selected_page_id;

        $('.knowledge-base-all-folder-card').removeClass('knowledge-base-sop-folder-card-selected');
        $('.knowledge-base-all-folder-card').removeClass('knowledge-base-pnp-folder-card-selected');
        $('.knowledge-base-all-folder-card').removeClass('knowledge-base-process-documents-folder-card-selected');
        $('.knowledge-base-all-folder-card').removeClass('knowledge-base-how-to-guide-folder-card-selected');
        $('.knowledge-base-all-folder-card').removeClass('knowledge-base-board-of-pharmacy-folder-card-selected');
        $('.knowledge-base-all-folder-card').removeClass('knowledge-base-pharmacy-forms-folder-card-selected');

        $(`.knowledge-base-${_id}-folder-card`).addClass(`knowledge-base-${_id}-folder-card-selected`);
        reloadDataTable();
    }

</script>
@stop
