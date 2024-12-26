@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!-- PAGE-HEADER -->
				@include('layouts/pageContentHeader/store')

				<!-- PAGE-HEADER END -->
				<div class="card">

                    <div class="card-header">
                        <div class="row px-3">
                            <div class="col-7">
                                @can('menu_store.operations.rts.create')
                                    <button class="btn btn-success" style="background-color: #35623c;" onclick="clickImportBtn()"><i class="fa fa-cloud-upload me-2"></i> Import Excel File</button>
                                @endcan
                                @can('menu_store.operations.rts.export')
                                    <button class="btn btn-default text-white ms-1" style="background-color: #5d5068;" onclick="clickExportBtn()"><i class="fa fa-download me-2"></i> Export</button>
                                @endcan
                            </div>
                            <div class="col-2 ms-auto">
                                <select class="form-select" id="is_archived" onchange="loadBoardData(true)">
                                    <option value="0" selected>Active</option>
                                    <option value="1">Archived</option>
                                </select>
                            </div>
                            <div class="col-3 ms-auto">
                                <input type="text" class="form-control" id="rts_search" placeholder="Search"/>
                            </div>
                        </div>
					</div>

					<div class="card-body" id="boardCard">
                        @include('stores/operations/rtsV2/partials/board')         
					</div>
				</div>
			</div>
			@include('sweetalert2/script')
            @include('stores/operations/rtsV2/modals/import')
            @include('stores/operations/rtsV2/modals/edit')
            @include('stores/operations/rtsV2/modals/archive')
            @include('stores/operations/rtsV2/modals/delete')

		</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts')

<style>
    #edit_rts_modal .modal-fullscreen {
        width: 100vw;
        height: 100vh;
        max-width: 100vw;
        max-height: 100vh;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #edit_rts_modal_content {
        width: 100%; /* Adjust this value to set the fixed width */
        max-height: 100vh;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    /* Custom scrollbar styles */
    .modal-content *::-webkit-scrollbar {
        width: 1px !important; /* Width of the scrollbar */
    }

    .modal-content *::-webkit-scrollbar-track {
        background: #efeff0; /* Background of the scrollbar track */
    }

    .modal-content *::-webkit-scrollbar-thumb {
        background: #a8a7a7; /* Color of the scrollbar thumb */
    }

    .modal-content *::-webkit-scrollbar-thumb:hover {
        background: #555; /* Color of the scrollbar thumb on hover */
    }

    .image-container {
        width: 100%;
        text-align: center;
    }

    .responsive-img {
        height: 130px !important;
        width: 100%;
        object-fit: cover;
        display: block;
        margin: 0 auto;
        border-radius: 5px;
    }
</style>

<script>
    let menu_store_id = {{request()->id}};
    let rtsStatus = {{ Js::from($rtsStatus) }};
    let $_responsive_card_height = 470;
    let $_responsive_scroll_height = 390;

    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        rtsStatus = {{ Js::from($rtsStatus) }};

        new PerfectScrollbar('#rts-container');

        // computeScreenHeight();

        loadBoardData();
        dragSingleBoardCard();

        document.getElementById('rts_search').addEventListener('input', function() {
            if (this.value === '' || this.value.length > 2) {
                setTimeout(function() {
                    loadBoardData(true);
                }, 500);
            }
        });
        
    });

    $('#edit_rts_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();  
        loadBoardData();  
    });

    function computeScreenHeight()
    {
        let current_window_height = $(window).height();
        // console.log('screen height -------- PX ',current_window_height);

        // in pixels
        const default_window_height = 695;
        const default_card_height = 470;
        const default_scroll_height = 390;

        if(current_window_height != default_window_height) {
            $_responsive_card_height = (current_window_height*default_card_height)/default_window_height;
            $_responsive_card_height = Math.round($_responsive_card_height);
            $_responsive_scroll_height = $_responsive_card_height - 80;

            $('.card-rts-status').height($_responsive_card_height);
            $('.draggable-lists').height($_responsive_scroll_height);

            // console.log("set card height---------PX ", $_responsive_card_height);
            // console.log("set scroll height-------PX ", $_responsive_scroll_height);
        }

        
    }

    function clickExportBtn() {
        const is_archived = $('#is_archived').val();
        const search = $('#rts_search').val();

        let url = `rts/export`;

        // Initialize an array to store query parameters
        let params = [];

        // Add parameters to the array if they are not empty
        if (is_archived) {
            params.push(`is_archived=${encodeURIComponent(is_archived)}`);
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
        proceedImportSingleExcel(`/store/operations/${menu_store_id}/rts/import`);
    }

    function reloadDataTable(data = null)
    {
        $('#import_single_excel_modal').modal('hide');
        loadBoardData();
    }
</script>
@stop