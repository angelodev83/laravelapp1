@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!-- PAGE-HEADER -->
				@include('layouts/pageContentHeader/store')

				<!-- PAGE-HEADER END -->
				<div class="card shadow-none">

                    <div class="card-header">
                        <div class="row px-3">
                            <div class="col">
                                @can('menu_store.clinical.renewals.create')
                                    <button class="btn btn-default text-white" style="background-color: #7845bf;" onclick="clickImportBtn()"><i class="fa fa-upload me-2"></i> Import</button>
                                @endcan
                                @can('menu_store.clinical.renewals.export')
                                    <button class="btn btn-default text-white ms-1" style="background-color: #5d5068;" onclick="clickExportBtn()"><i class="fa fa-download me-2"></i> Export</button>
                                @endcan
                            </div>
                            <div class="col-2">
                                <select class="form-select" id="is_archived" onchange="loadBoardData(true)">
                                    <option value="0" selected>Active</option>
                                    <option value="1">Archived</option>
                                </select>
                            </div>
                            <div class="col-3 ms-auto">
                                <div class="input-icon-container">
                                    <i class="fas fa-magnifying-glass"></i>
                                    <input type="text" class="form-control" id="renewal_search" placeholder="Search">
                                </div>
                            </div>
                        </div>
					</div>

					<div class="card-body mx-0 px-0" id="boardCard" style="background-color: #f0f0f0 !important;">
                        @include('stores/clinical/renewals/partials/board')         
					</div>
				</div>
			</div>
			@include('sweetalert2/script')
            @include('stores/clinical/renewals/modals/import')
            @include('stores/clinical/renewals/modals/edit')
            @include('stores/clinical/renewals/modals/archive')
            @include('stores/clinical/renewals/modals/delete')

		</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts')

<style>
    .input-icon-container {
        position: relative;
    }

    .input-icon-container .fa-magnifying-glass {
        position: absolute;
        top: 50%;
        left: 15px;
        transform: translateY(-50%);
        color: #b2b2b2;
    }

    .input-icon-container input {
        padding-left: 32px;
    }
    #edit_renewal_modal .modal-fullscreen {
        width: 100vw;
        height: 100vh;
        max-width: 100vw;
        max-height: 100vh;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #edit_renewal_modal_content {
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

    .store-metrics {
        position: relative;
        height: 350px;
    }
</style>

<script>
    let menu_store_id = {{request()->id}};
    let renewalStatus = {{ Js::from($renewalStatus) }};
    let $_responsive_card_height = 470;
    let $_responsive_scroll_height = 390;

    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        renewalStatus = {{ Js::from($renewalStatus) }};

        new PerfectScrollbar('#renewal-container');

        // computeScreenHeight();

        loadBoardData();
        dragSingleBoardCard();

        document.getElementById('renewal_search').addEventListener('input', function() {
            if (this.value === '' || this.value.length > 2) {
                setTimeout(function() {
                    loadBoardData(true);
                }, 500);
            }
        });
        
    });

    $('#edit_renewal_modal').on('hidden.bs.modal', function(){
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
        console.log('screen height -------- PX ',current_window_height);

        // in pixels
        const default_window_height = 695;
        const default_card_height = 470;
        const default_scroll_height = 390;

        if(current_window_height != default_window_height) {
            $_responsive_card_height = (current_window_height*default_card_height)/default_window_height;
            $_responsive_card_height = Math.round($_responsive_card_height);
            $_responsive_scroll_height = $_responsive_card_height - 80;

            $('.card-renewal-status').height($_responsive_card_height);
            $('.draggable-lists').height($_responsive_scroll_height);

            console.log("set card height---------PX ", $_responsive_card_height);
            console.log("set scroll height-------PX ", $_responsive_scroll_height);
        }

        
    }

    function clickExportBtn() {
        const is_archived = $('#is_archived').val();
        const search = $('#renewal_search').val();

        let url = `renewals/export`;

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
        proceedImportSingleExcel(`/store/clinical/${menu_store_id}/renewals/import`);
    }

    function reloadDataTable(data = null)
    {
        $('#import_single_excel_modal').modal('hide');
        loadBoardData();
    }
</script>
@stop