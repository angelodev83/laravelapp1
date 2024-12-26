@extends('layouts.master')
@section('content')
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!-- PAGE-HEADER -->
        @include('layouts/pageContentHeader/store')
        <!-- PAGE-HEADER END -->

        <div class="m-4">
            <ul class="border border-0 nav nav-tabs" id="views">
                <li class="nav-item">
                    <a href="#list-view" class="nav-link active" data-bs-toggle="tab">
                        <i class="fa-solid fa-list"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#grid-view" class="nav-link" data-bs-toggle="tab">
                        <i class="bx bx-grid-alt"></i>
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                
                <div class="tab-pane fade show active" id="list-view">
                    <!-- list view -->
                    <div id="list-view" class="p-3 bg-white border border-top-0 rounded-bottom-4">
                        <div class="card-header task-card-header">
                            <select name='length_change' id='length_change' class="table_length_change form-select">
                            </select>
                            <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
                            
                            <div class="dropdown table_length_change">
                                <button class="btn btn-outline-danger dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item d-flex" href="javascript:archiveSelectedTask();">
                                            Archive <i class="fa-solid fa-box-archive ms-auto text-danger"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <select class="table_length_change form-select me-2" id="filter_status_select"></select>
                            <select class="table_length_change form-select" id="filter_status_type_select" onchange="selectStatusCategory(this.value)">
                                <option value="task">General</option>
                                <option value="procurement_order">Procurement</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="dt_table" class="table row-border table-hover dt-table-fixed-first-column" style="width:100%">
                                    <thead></thead>
                                    <tbody>                                   
                                    </tbody>
                                    <tfooter></tfooter>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="grid-view">
                <!-- grid view -->
                    <!-- General -->
                    <div id="grid-view" class="p-3 bg-white rounded-bottom-4">
                        <!-- Date Filters -->
                        <div class="mb-3 row">
                            <div id="result" class="col-7"></div>
                            <div class="gap-2 d-flex col align-items-center">
                                <h6 class="mb-0">Date Filter: </h6>
                                <select id="months" class="col form-select">
                                    @foreach ($months as $key => $month)
                                    <option value="{{ $key }}"@if ($key == $currentMonth) selected @endif>{{ $month }}</option>
                                    @endforeach
                                </select>
                                <select id="years" class="col form-select">
                                    @foreach ($years as $year)
                                    <option value="{{ $year }}"@if ($key == $currentYear) selected @endif>{{ $year }}</option>
                                    @endforeach
                                </select>
                                <button id="filter" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                        <div id="task-container" class="gap-3 d-flex container-lists">
                            <!-- TO DO SECTION -->
                            <div class="card rounded-4 bg-body-secondary" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white rounded-3 card-title fs-6 bg-secondary">TO DO</h6>
                                            <span id="task-201-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal(201)" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            Add New Task
                                        </button>
                                    </div>
                                    <div id="task-201" class="px-3 mb-3 task-content lists"></div>
                                </div>
                            </div>
                            <!-- IN PROGRESS SECTION -->
                            <div class="bg-blue-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-blue-500 rounded-3 card-title fs-6">IN PROGRESS</h6>
                                            <span id="task-202-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal(202)" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            Add New Task
                                        </button>
                                    </div>
                                    <div id="task-202" class="px-3 mb-3 task-content lists"></div>
                                </div>
                            </div>
                            <!-- TO ANALYZE SECTION -->
                            <div class="bg-jade-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-jade-500 rounded-3 card-title fs-6">TO ANALYZE</h6>
                                            <span id="task-203-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal(203)" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            Add New Task
                                        </button>
                                    </div>
                                    <div id="task-203" class="px-3 mb-3 task-content lists"></div>
                                </div>
                            </div>
                            <!-- TO VERIFY SECTION -->
                            <div class="bg-yellow-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-yellow-500 rounded-3 card-title fs-6">TO VERIFY</h6>
                                            <span id="task-204-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal(204)" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            Add New Task
                                        </button>
                                    </div>
                                    <div id="task-204" class="px-3 mb-3 task-content lists"></div>
                                </div>
                            </div>
                            <!-- WAITING SECTION -->
                            <div class="bg-red-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-red-500 rounded-3 card-title fs-6">WAITING</h6>
                                            <span id="task-205-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal(205)" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            Add New Task
                                        </button>
                                    </div>
                                    <div id="task-205" class="px-3 mb-3 task-content lists"></div>
                                </div>
                            </div>
                            <!-- COMPLETE SECTION -->
                            <div class="bg-lawn-green-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-lawn-green-500 rounded-3 card-title fs-6">COMPLETE</h6>
                                            <span id="task-206-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal(206)" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            Add New Task
                                        </button>
                                    </div>
                                    <div id="task-206" class="px-3 mb-3 task-content lists"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Procurement -->
                    <div id="grid-view" class="p-3 mt-3 bg-white rounded-4">
                        <div id="order-container" class="gap-3 d-flex container-lists">
                            <!-- NEW REQUEST SECTION -->
                            <div class="card rounded-4 bg-body-secondary" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white rounded-3 card-title fs-6 bg-secondary">NEW REQUEST</h6>
                                            <span id="order-701-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal()" class="btn fw-medium d-none">
                                            <i class="fa-solid fa-plus"></i>
                                            Add New Order
                                        </button>
                                    </div>
                                    <div id="order-701" class="px-3 mb-3 order-content lists"></div>
                                </div>
                            </div>
                            <!-- RECEIVED SECTION -->
                            <div class="bg-blue-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-blue-500 rounded-3 card-title fs-6">RECEIVED</h6>
                                            <span id="order-702-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal()" class="btn fw-medium d-none">
                                            <i class="fa-solid fa-plus"></i>
                                            Add New Order
                                        </button>
                                    </div>
                                    <div id="order-702" class="px-3 mb-3 order-content lists"></div>
                                </div>
                            </div>
                            <!-- IN TRANSIT SECTION -->
                            <div class="bg-jade-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-jade-500 rounded-3 card-title fs-6">IN TRANSIT</h6>
                                            <span id="order-703-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal()" class="btn fw-medium d-none">
                                            <i class="fa-solid fa-plus"></i>
                                            Add New Order
                                        </button>
                                    </div>
                                    <div id="order-703" class="px-3 mb-3 order-content lists"></div>
                                </div>
                            </div>
                            <!-- SUBMITTED SECTION -->
                            <div class="bg-yellow-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-yellow-500 rounded-3 card-title fs-6">SUBMITTED</h6>
                                            <span id="order-704-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal()" class="btn fw-medium d-none">
                                            <i class="fa-solid fa-plus"></i>
                                            Add New Order
                                        </button>
                                    </div>
                                    <div id="order-704" class="px-3 mb-3 order-content lists"></div>
                                </div>
                            </div>
                            <!-- MISSING ORDER SECTION -->
                            <div class="bg-red-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-red-500 rounded-3 card-title fs-6">MISSING ORDER</h6>
                                            <span id="order-705-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal()" class="btn fw-medium d-none">
                                            <i class="fa-solid fa-plus"></i>
                                            Add New Order
                                        </button>
                                    </div>
                                    <div id="order-705" class="px-3 mb-3 order-content lists"></div>
                                </div>
                            </div>
                            <!-- COMPLETED SECTION -->
                            <div class="bg-lawn-green-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-lawn-green-500 rounded-3 card-title fs-6">COMPLETED</h6>
                                            <span id="order-706-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal()" class="btn fw-medium d-none">
                                            <i class="fa-solid fa-plus"></i>
                                            Add New Order
                                        </button>
                                    </div>
                                    <div id="order-706" class="px-3 mb-3 order-content lists"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
		<!--end page wrapper -->
        @include('sweetalert2/script')
        {{-- @include('stores/bulletin/tasks/modals/show') --}}
        @include('stores/bulletin/tasks/modals/add')
        {{-- @include('stores/bulletin/tasks/modals/edit') --}}
        @include('stores/bulletin/tasks/modals/delete')
        @include('stores/bulletin/tasks/modals/archive')
        {{-- @include('components/modal/delete-store-document') --}}
        @include('stores/bulletin/tasks/modals/assignee')
        @include('stores/bulletin/tasks/modals/watcher')
        @include('stores/bulletin/tasks/modals/edit/form')
        @include('components/modal/delete-store-document')
        @include('stores/bulletin/tasks/partials/task_board_view')
        @include('stores/bulletin/tasks/partials/order_board_view')
@stop

@section('pages_specific_scripts')  

<style>
    /* MAKE LEFT COLUMN FIXEZ */
    .dt-table-fixed-first-column thead th:nth-child(1),
    .dt-table-fixed-first-column tbody td:nth-child(2) {
        left: 0 !important;
        z-index: 1 !important;
        position: -webkit-sticky !important;
        position: sticky !important;
        left: 0 !important;
        /* background-color: #c1c1c1 !important; */
        /* width: 100% !important; */
        /* min-width: 10rem !important; */
    }
    /* MAKE LEFT COLUMN FIXEZ */
    /* .dt-table-fixed-first-column thead th:nth-child(9), */
    .dt-table-fixed-first-column tbody td:nth-child(12) {
        right: 0 !important;
        z-index: 1 !important;
        position: -webkit-sticky !important;
        position: sticky !important;
        right: 0 !important;
        background-color: white !important;
        /* width: 100% !important; */
        /* min-width: 10rem !important; */
        box-shadow: -13px 7px 14px 0px rgb(0 0 0 / 33%);
    }

    .popover-header {
        background-color: #6c757d;
        color: white;
    }

    .bxs-cloud-upload,
    .imageuploadify-message {
        display: none !important;
    }

    #edit_task_modal .modal-content {
        background-color: #f0f0f0 !important;
    }

    .drop-area {
        border: 3px dashed #ECECEC;
        padding: 10px 20px 20px 20px;
        text-align: center;
        /* cursor: pointer; */
    }

    .file-label {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }

    .efile-label {
        background-color: white;
        border-color: #15a0a3;
        color: #15a0a3;
        padding: 10px 20px;
        border-radius: 15px;
        cursor: pointer;
    }
    .efile-label:hover {
        background-color: #15a0a3;
        border-color: #15a0a3;
        color: white;
    }

    #fileList {
        margin-top: 20px;
    }

    #efileList {
        margin-top: 20px;
    }

    .file-item {
        margin-bottom: 10px;
    }

    .efile-item {
        margin-bottom: 10px;
    }

    .remove-file-btn {
        background-color: red;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
        cursor: pointer;
    }

    .eremove-file-btn {
        background-color: red;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
        cursor: pointer;
    }

    .dropdown .dropdown-toggle::after {
        display: inline-block;
        margin-left: 0.5em;
        vertical-align: 0.25em;
        content: "";
        border-top: 0.3em solid;
        border-right: 0.3em solid transparent;
        border-bottom: 0;
        border-left: 0.3em solid transparent;
        float: inline-end !important;
        margin-top: 10px !important;
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
        height: 408px !important;
    }

    #taskAttachmentsList .card {
        border-color: #A8A7A7 !important;
        border-radius: 5px !important;
    }

    .btn-group-xs>.btn, .btn-xs {
        --bs-btn-padding-y: 0.05rem;
        --bs-btn-padding-x: 0.3rem;
        --bs-btn-font-size: 0.675rem;
        --bs-btn-border-radius: var(--bs-border-radius-sm);
    }

    #edit_task_modal .subject_text:hover {
        color: #15a0a3;
    }
    #edit_task_modal .assigned_to:hover {
        color: #15a0a3;
    }
    #edit_task_modal .edit-icon {
        color:#a8a7a7;
    }

    #edit_task_modal .edit-icon:hover {
        color:#15a0a3;
    }
    .dots .dropdown-toggle::after {
        display: none;
    }
</style>

<script>
    let table_task;
    let table_task_watcher;
    let table_task_watcher_selected_id = null;
    let menu_store_id = {{request()->id}};

    let table_task_assignee;
    let table_task_assignee_selected_id = null;
    let original_edited_task_subject = '';
    let original_edited_task_description = '';
    // let component_user_id = {{auth()->user()->id}};
    let comment_task_files;
    let is_task_modal_loading = true;
    let show_edit_modal = 'task';

    
    const urlSearchParams = new URLSearchParams(window.location.search);
    const urlGlobalTaskID = urlSearchParams.get('task-id');
    let urlGlobalTaskIndex = urlSearchParams.get('task-index');
    if(!urlGlobalTaskIndex) {
        urlGlobalTaskIndex = 0;
    }
    let urlGlobalTaskLength = urlSearchParams.get('task-length');
    if(!urlGlobalTaskLength) {
        urlGlobalTaskLength = 10;
    }

    let is_archived = 0;
    let selectedIds = {};  
    let unselectedIds = [];

    function emitCopyText(value = '', _selector)
    {
        $(_selector).html(`<div class="px-4 py-2 m-0 border-0 alert alert-success alert-dismissible fade show">Copied ID <b>${value}</b></div>`);
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }

    // onload
    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        original_edited_task_subject = '';
        original_edited_task_description = '';
        $(".form-control").removeClass("is-invalid");
        is_archived = 0;
        selectedIds = {};  
        unselectedIds = [];

        new PerfectScrollbar('#taskAttachmentsList');
        new PerfectScrollbar('#taskCommentsList');

        new PerfectScrollbar('#order-container');
        new PerfectScrollbar('#order-701');
        new PerfectScrollbar('#order-702');
        new PerfectScrollbar('#order-703');
        new PerfectScrollbar('#order-704');
        new PerfectScrollbar('#order-705');
        new PerfectScrollbar('#order-706');
        
        new PerfectScrollbar('#task-container');
        new PerfectScrollbar('#task-201');
        new PerfectScrollbar('#task-202');
        new PerfectScrollbar('#task-203');
        new PerfectScrollbar('#task-204');
        new PerfectScrollbar('#task-205');
        new PerfectScrollbar('#task-206');


        $('#edit_task_modal').on('show.bs.modal', function () {
            $('#watcher_task_modal').css('z-index', parseInt($('#edit_task_modal').css('z-index')) + 2);
            $('#assignee_task_modal').css('z-index', parseInt($('#edit_task_modal').css('z-index')) + 2);
        });
        
        tinymce.init({
            selector: '#add_task_modal #description',
            toolbar: 'undo redo print spellcheckdialog formatpainter | blocks fontfamily fontsize | bold italic underline forecolor backcolor | link image | alignleft aligncenter alignright alignjustify lineheight | checklist bullist numlist indent outdent | removeformat',
            plugins: 'textcolor link',
            height: 400,
            branding: false
		});

        tinymce.init({
            selector: '#edit_task_modal #eTaskDescription',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline forecolor backcolor | link image | alignleft aligncenter alignright alignjustify lineheight | checklist bullist numlist indent outdent | removeformat',
            plugins: 'textcolor link',
            height: 220,
            branding: false,
            menubar: '',
            // setup: (editor) => {
            //     editor.on('change', function (e) {
            //         var description = editor.getContent();
            //         // Your custom function here
            //         alert(editor.getContent());
            //     });
            // }
		});

        $('#edit_task_modal #show-documents').imageuploadify();

        // new PerfectScrollbar('#bulletin-tasks-recent');

        $('#add_task_modal #due_date').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5',
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true,
        });

        $('#edit_task_modal #edue_date').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5',
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true
        });

        
        $('#edit_task_modal').on('click', function (e) {
            if (!$(e.target).closest('#eTaskDescription').length && is_task_modal_loading === false) {
                var description = tinymce.get("eTaskDescription").getContent();
                if(original_edited_task_description != description)
                {
                    if(!description) {
                        description = '';
                    }
                    original_edited_task_description = description;
                    updateTaskDetails(e, 'description', description);
                }
            }

            if (!$(e.target).closest('#edit_task_modal #esubject').length && is_task_modal_loading === false) {
                var subject = $('#edit_task_modal #esubject').val();
                console.log("task subject", subject)
                if(original_edited_task_subject != subject)
                {
                    if(!subject) {
                        // $("#edit_task_modal #esubject").addClass("is-invalid");
                        return;
                    }
                    original_edited_task_subject = subject;
                    console.log("task original_edited_task_subject", original_edited_task_subject)
                    $(".form-control").removeClass("is-invalid");

                    $('#edit_task_modal #esubject').addClass('d-none');
                    $('#edit_task_modal #esubject_text').removeClass('d-none');

                    $('#edit_task_modal #subject_text').html(subject);
                    updateTaskDetails(e, 'subject', subject);
                }
            }
        });

        // $("#add_toggle_has_tag").click(function(){
        //     $('#tags').val([]).trigger('change');
        //     $("#add_toggle_document_tag").toggle();
        // });

        // $("#edit_toggle_has_tag").click(function(){
        //     $('#etags').val([]).trigger('change');
        //     $("#edit_toggle_document_tag").toggle();
        // });

        // $( '#tags' ).select2( {
        //     theme: "bootstrap-5",
        //     width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
        //     placeholder: 'Choose Self-Audit Document Tags',
        //     closeOnSelect: false,
        //     allowClear: true,
        // } );  

        // $( '#etags' ).select2( {
        //     theme: "bootstrap-5",
        //     width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
        //     placeholder: 'Choose Self-Audit Document Tags',
        //     closeOnSelect: false,
        //     allowClear: true,
        // } ); 
        
        // $( '#show_task_modal #show_assign_to_select' ).select2( {
        //     theme: "bootstrap-5",
        //     width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
        //     placeholder: $( this ).data( 'placeholder' ),
        //     closeOnSelect: true,
        //     dropdownParent: $('#show_task_modal .modal-content'),
        // });

        // $('#show_task_modal #due_date').datepicker({
        //     format: "yyyy-mm-dd",
        //     todayHighlight: true,
        //     uiLibrary: 'bootstrap5',
        //     modal: true,
        //     icons: {
        //         rightIcon: '<i class="material-icons"></i>'
        //     },
        //     showRightIcon: false,
        //     autoclose: true,
        // });

        // loadDocumentTags();

        getStatusDropDown('filter_status_select', 201, {category: 'task'}, true);
        loadTasks();
        loadTaskWatchers();
        loadTaskAssignees();

        loadCard();

        $('#filter').click(function(e) {
            e.preventDefault();
            loadCard();
        });

        // Retrieve the last active tab from local storage
        var lastActiveTab = localStorage.getItem('activeTab');
        if (lastActiveTab) {
            $('.nav-link[href="' + lastActiveTab + '"]').tab('show');
        }

        // Save the active tab to local storage when a tab is clicked
        $('.nav-link').on('shown.bs.tab', function(e) {
            var activeTab = $(e.target).attr('href');
            localStorage.setItem('activeTab', activeTab);
        });
    });

    function loadDocumentTags()
    {
        let data = {type:'audit'};

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/search/tag",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
                var len = data.data.length;
                
                $("#tags").empty();
                $("#etags").empty();
                for( var i = 0; i<len; i++){
                    var id = data.data[i]['id'];
                    var name = data.data[i]['name'];
                    $("#tags").append("<option value='"+id+"'>"+name+"</option>");
                    $("#etags").append("<option value='"+id+"'>"+name+"</option>");
                }
            },
            error: function (msg) {
                handleErrorResponse(msg);
            }
        });
    }


    // functions
    function loadTasks() {
        let data = {};        
        const dt_table = $('#dt_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            pageLength: urlGlobalTaskLength,
            dom: 'fBtip',
            order: [[8, 'desc']],
            buttons: [
                @can('menu_store.bulletin.task_reminders.create')
                    {
                        text: '+ Add New Task', 
                        className: 'btn btn-primary', 
                        action: function ( e, dt, node, config ) {
                            showAddModal();
                        }
                    },
                @endcan
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/store/bulletin/task/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                    data.status_id = $('#filter_status_select').val();
                    data.permission = 'bulletin.task';
                    data.is_archived = is_archived;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                // { data: 'subject', name: 'subject', title: 'Subject'},
                { 
                    data: 'is_archived', 
                    name: 'is_archived',
                    defaultContent: '',
                    title: '<input type="checkbox" id="select_all" />', 
                    render: function(data, type, row){
                        let checked = '';
                        let disabled = '';
                        if(data === 1) {
                            checked = 'checked';
                            disabled = 'disabled';
                        }
                        return `<input type="checkbox" class="row-checkbox" name="id[]" value="${row.id}" ${checked} ${disabled}>`;
                    },
                    orderable: false,
                    searchable: false,
                    className: 'select-checkbox',
                    width: "1%"
                },
                { data: 'subject', name: 'subject', title: 'Subject', orderable: true, render: function(data, type, row) {
                    return `<div class="task-subject datatable-long-description-field-truncate" title="${data}" onclick="showTaskEditModal(${row.id})" style="cursor: pointer;">${data}</div>`;
                } },
                { data: 'assigned_to', name: 'assigned_to', title: 'Assigned To', render: function(data, type, row) {
                    return `${row.avatar}`;
                } },
                { data: 'status', name: 'status', title: 'Status' , orderable: false, searchable: false},
                { data: 'priorityStatus', name: 'priorityStatus', title: 'Priority' , orderable: false, searchable: false},
                { data: 'due_date', name: 'due_date', title: 'Due Date', render: function(data, type, row) {
                    return `${row.formatted_due_date}`;
                } },
                { data: 'created_by', name: 'created_by', title: 'Created By', render: function(data, type, row) {
                    return `${row.empAvatar}`;
                }  },
                { data: 'watcherList', name: 'watcherList', title: 'Watchers' , width: '15%',  orderable: false, searchable: false},
                { data: 'created_at', name: 'created_at', title: 'Date Created (PST)', render: function(data, type, row) {
                    return `${row.formatted_pst_created_at}`;
                } },
                { data: 'completed_by', name: 'completed_by', title: 'Completed By', render: function(data, type, row) {
                    return `${row.completedByAvatar}`;
                } },
                { data: 'completed_at', name: 'completed_at', title: 'Completed At (PST)', render: function(data, type, row) {
                    return `${row.formatted_pst_completed_at}`;
                } },
                { data: 'actions', name: 'actions', title: '' , orderable: false, searchable: false},
            ],
            columnDefs: [
                {
                    targets: 1, // Target the first column
                    maxWidth: '50px' // Set the maximum width for the first column
                }
            ],
            initComplete: function( settings, json ) {
                selected_len = dt_table.page.len();
                $('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
                $('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
                $('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
                $('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
                $('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));

                if(urlGlobalTaskIndex !== 0) {
                    // // Calculate the page number based on the row index and page length
                    var pageNumber = Math.floor(urlGlobalTaskIndex / urlGlobalTaskLength);
    
                    // Go to the calculated page
                    table_task.page.len(urlGlobalTaskLength).draw()
                    table_task.page(pageNumber).draw('page')
    
                    drawCallbackFunction();
                }

                $('#select_all').on('click', function() {
                    // Only toggle checkboxes on the current page
                    let rows = dt_table.rows({ 'page': 'current' }).nodes();
                    let checkboxes = $('input[type="checkbox"].row-checkbox', rows);
                    let isChecked = this.checked;

                    checkboxes.each(function() {
                        let id = $(this).val();

                        if (isChecked) {
                            // If select all is checked, add IDs to selectedIds
                            selectedIds[id] = true;
                            // Remove ID from unselectedIds if it's there
                            let index = unselectedIds.indexOf(id);
                            if (index !== -1) {
                                unselectedIds.splice(index, 1);
                            }
                        } else {
                            // If select all is unchecked, remove IDs from selectedIds
                            delete selectedIds[id];
                            // Add ID to unselectedIds if it's not already there
                            if (!unselectedIds.includes(id)) {
                                unselectedIds.push(id);
                            }
                        }
                    });

                    // Toggle checkboxes
                    checkboxes.prop('checked', isChecked);
                });

                $('#dt_table tbody').on('change', 'input[type="checkbox"].row-checkbox', function() {
                    let id = $(this).val();
                    let isChecked = this.checked;

                    if (!isChecked) {
                        // If the checkbox is unchecked
                        // Remove ID from selectedIds if it's unchecked again
                        delete selectedIds[id];
                        // Add ID to unselectedIds array if it becomes unchecked
                        if (!unselectedIds.includes(id)) {
                            unselectedIds.push(id);
                        }
                    } else {
                        // If the checkbox is checked
                        // Add ID to selectedIds object if it's not already there
                        if (!selectedIds[id]) {
                            selectedIds[id] = true;
                        }
                        // Remove ID from unselectedIds array if it becomes checked again
                        let index = unselectedIds.indexOf(id);
                        if (index !== -1) {
                            unselectedIds.splice(index, 1);
                        }
                    }
                });

                // Store the state of checkboxes when pagination changes
                dt_table.on('draw.dt', function() {
                    updateCheckboxesState();
                });

                // Update checkboxes state immediately after DataTable is initialized
                updateCheckboxesState();

                dt_table.on('page.dt', function() {
                    $('#select_all').prop('checked', false);
                });

            }
        });

        table_task = dt_table;
        table_task.buttons().container().appendTo( '.task-card-header' );
        $('#search_input').val(table_task.search());
		$('#search_input').keyup(function(){ table_task.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_task.page.len($(this).val()).draw() });
	    $('#filter_status_select').change( function() { 
            table_task.draw();
        });
    }

    function drawCallbackFunction()
    {
        if(urlGlobalTaskID) {
            showTaskEditModal(urlGlobalTaskID);
        }
    }
    setTimeout(drawCallbackFunction, 4000);

    function updateCheckboxesState() {
        let currentPageCheckboxes = $('input[type="checkbox"].row-checkbox', table_task.rows({ 'page': 'current' }).nodes());

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
            } else if (unselectedIds.includes(id)) {
                // If the ID is in unselectedIds, uncheck the checkbox
                $(this).prop('checked', false);
            }
        });

        // Identify unchecked checkboxes that were checked initially
        $('input[type="checkbox"].row-checkbox', table_task.rows().nodes()).each(function() {
            let id = $(this).val();
            let isChecked = $(this).prop('checked');
        });
    }

    function clearSelection() {
        selectedIds = {};
        unselectedIds = [];
    }

    // function clickArchiveBtn(id) {
    //     let data = {
    //         selectedIds: [id],
    //         unselectedIds: []
    //     };
    //     console.log('data',data);
    //     doArchiveTask(data);
    // }

    function archiveSelectedTask(){
        var checkedIds = Object.keys(selectedIds).filter(function(id) {
            return selectedIds[id];
        });
        let data = {
            selectedIds: checkedIds,
            unselectedIds: unselectedIds
        };
        
        console.log('data',data);
        // doArchiveTask(data);

        $('#archive_task_form_modal').modal('show');
        $('#archive_task_form_modal #id').val('');
        $('#title_archive_task_id_text').empty();
        $('#title_archive_task_id_text').append('Tasks: ');
        for(let i =0; i < data.selectedIds.length; i++ ) {
            $('#title_archive_task_id_text').append(data.selectedIds[i]  + ', ')
        }
    }

    function doArchiveTask(data) {
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/bulletin/task/archive`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    // sweetAlert2(data.status, data.message);
                    Swal.fire({
                        position: 'center',
                        icon: data.status,
                        title: data.message,
                        showConfirmButton: false,
                        timer: 800
                    });
                    $('#archive_task_form_modal').modal('hide');
                    reloadDataTable();
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }
        });
    }

    function clickUnarchiveBtn(id) {
        let data = {
            selectedIds: [id]
        };
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/bulletin/task/unarchive`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    Swal.fire({
                        position: 'center',
                        icon: data.status,
                        title: data.message,
                        showConfirmButton: false,
                        timer: 800
                    });
                    reloadDataTable();
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }
        });
    }

    // ------------------------------

    function showAddModal(status_id = 201){
        $(".form-control").removeClass("is-invalid");

        tinymce.get("description").setContent('');

        searchSelect2Api('assigned_to_employee_id', '#add_task_modal','/admin/search/user-employee');

        getStatusDropDown('status_id', status_id);

        $('#add_task_modal #due_date').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5', 
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true,
        });

        let fileInput = $('<input/>', {
            id: 'documents',
            class: 'imageuploadify-file-general-class',
            name: 'documents[]',
            type: 'file',
            accept: '*',
            multiple: ''
        });
        $('#add_task_modal #for-file').html(fileInput); 
        $('#add_task_modal #documents').imageuploadify();
        
        $("#add_task_modal .imageuploadify-container").remove();
        $('#add_task_modal .imageuploadify-message').html('Drag&Drop Your File(s) Here To Upload');     
        
        $('#add_task_modal').modal('show');
    }

    function searchSelect2Api(_select_id, _modal_id, _url) {
        $(`#${_select_id}`).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $(`${_modal_id}`),

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
                        limit: 10,
                        pharmacy_store_id: menu_store_id
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

    function clickStatusBtn(task_id) {

    }

    function changeTaskSelectedStatus(event, id)
    {
        event.preventDefault();
        updateTaskDetails(event, 'status_id', id);
    }

    function changeTaskSelectedPriorityStatus(event, id)
    {
        event.preventDefault();
        updateTaskDetails(event, 'priority_status_id', id);
    }

    function getStatusDropDown(_dropdown_id, _status_id, filter = {category: 'task'}, hasAll = false) {
        const data = filter;
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/search/store-status",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {
            
                var data = res.data;
                var len = data.length;
                
                $(`#${_dropdown_id}`).empty();

                let selected = "selected";
                if(hasAll === true) {
                    selected = "";
                    $(`#${_dropdown_id}`).append("<option selected value=''>Select ALL</option>");
                }
                
                for( var k = 0; k<len; k++){
                    var kid = data[k]['id'];
                    var kname = data[k]['name'];
                    if(kid==_status_id){$(`#${_dropdown_id}`).append("<option "+selected+" value='"+kid+"'>"+kname+"</option>");}
                    else{
                        $(`#${_dropdown_id}`).append("<option value='"+kid+"'>"+kname+"</option>");
                    }
                }
                $(`#${_dropdown_id}`).append("<option value='archived'>ARCHIVED</option>");
            },
            error: function (msg) {
                handleErrorResponse(msg);
            }
        });
    }

    
    // event listeners
    $('#add_task_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(".imageuploadify-container").remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();   
        $('#add_task_modal #documents').remove();
        $('#add_task_modal .imageuploadify').remove(); 
    });

    $('#edit_task_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(".imageuploadify-container").remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
        $('#edit_task_modal #show-documents').remove();
        $('#edit_task_modal .imageuploadify').remove(); 
    });

    $('#show_task_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(".imageuploadify-container").remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
        $('#show_task_modal #show-documents').remove();
        $('#show_task_modal .imageuploadify').remove(); 
    });

    $('.imageuploadify-file-general-class').click(function () {
        $('.imageuploadify-container').remove();
    });

    function selectStatusCategory(value = "task")
    {
        getStatusDropDown('filter_status_select', null, {category: value}, true);
        table_task.draw()
    }

    function populateNormalSelect(_selector, _model_id, _url, params = {}, _id = null)
    {
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
                    if(kid==_id){$(_selector).append("<option selected value='"+kid+"'>"+kname+"</option>");}
                    else{
                        $(_selector).append("<option value='"+kid+"'>"+kname+"</option>");
                    }
                }
            },
            error: function (msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function searchInmarItem(_selector, _modal_id, _i = null, _id = null, _new = null)
    {
        //console.log("fire")
        $(_selector).select2( {
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
                url: "/store/procurement/pharmacy/inmar-returns/get_medication_data",
                dataType: "json",
                type: "POST",
                data: function (params) {
                    var queryParameters = {
                        term: params.term
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            return {
                                text: item.name,
                                id: item.med_id
                            }   
                        })
                    };
                }
            },
        });
        // }).on('select2:select', function(e) {
        //     // Get the selected option data
        //     var selectedData = e.params.data;

        //     // Call your function with the selected data
        //     console.log("selected", selectedData)
        //     displaySupplyItem(selectedData, _i, _id, _new);
        // });
    }

    function searchSelect2ApiDrug(_selector, _modal_id, _med_id = null)
    {
        $(_selector).select2( {
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
                url: "/admin/medications/getNames",
                dataType: "json",
                type: "POST",
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        limit: 10
                    }
                    if(_med_id != null) {
                        var q = { med_id: _med_id, not: 'med_id' };
                        queryParameters = {...queryParameters, ...q}
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            return {
                                text: item.name,
                                id: item.med_id
                            }   
                        })
                    };
                }  
            },
        });
    }

    function reloadDataTable()
    {
        table_task.ajax.reload(null, false);
        loadCard();
        clearSelection();
    }

    function searchSupplyItem(_selector, _modal_id, _i = null, _id = null, _new = null)
    {
        console.log("fire")
        $(_selector).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $(`#${_modal_id}`),

            multiple: false,
            minimumInputLength: 1,
            minimumResultsForSearch: 10,
            ajax: {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/admin/search/supply-item',
                dataType: "json",
                type: "POST",
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        limit: 10
                    }
                    if(_id != null) {
                        var q = { id: _id, not: 'id' };
                        queryParameters = {...queryParameters, ...q}
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            return {
                                text: item.item_number,
                                id: item.id,
                                size: item.size,
                                description: item.description,
                                model_number: item.model_number
                            }   
                        })
                    };
                }  
            },
        }).on('select2:select', function(e) {
            // Get the selected option data
            var selectedData = e.params.data;

            // Call your function with the selected data
            console.log("selected", selectedData)
            displaySupplyItem(selectedData, _i, _id, _new);
        });
    }

    function displaySupplyItem(data, i = null, _id = null,  _new = null)
    {
        const is_number = Number.isInteger(i);
        console.log('--fire display item',is_number, data)
        if(is_number) {
            if(Number.isInteger(_new)) {
                console.log("---FIRE for edit modal - editing new items",$(`#new_number_${_new}`).val())
                $(`#show_task_modal #new_item_${_new}`).val(data.text);
                $(`#show_task_modal #new_code_${_new}`).val(data.model_number);
                $(`#show_task_modal #new_description_${_new}`).val(data.description);
            } else {
                console.log("---FIRE for add modal - items")
                $(`#show_task_modal #item-${i}`).val(data.text);
                $(`#show_task_modal #code-${i}`).val(data.model_number);
                $(`#show_task_modal #description-${i}`).val(data.description);
            }
        } else {
            console.log("---FIRE for edit modal - editing exsiting items")
            $(`#show_task_modal #item_${_id}`).val(data.text);
            $(`#show_task_modal #code_${_id}`).val(data.model_number);
            $(`#show_task_modal #description_${_id}`).val(data.description);
        }
    }

    function searchItem(_selector, _modal_id, _i = null, _id = null, _new = null)
    {
        $(_selector).select2( {
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
                url: "/store/procurement/pharmacy/inmar-returns/get_medication_data",
                dataType: "json",
                type: "POST",
                data: function (params) {
                    var queryParameters = {
                        term: params.term
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            return {
                                text: item.name,
                                id: item.med_id
                            }   
                        })
                    };
                }
            },
        });
    }

    $('#edit_task_modal_upload_documents').change(function () {
        comment_task_files = null;
        let task_comment_attachment_chip_card_list = '';
        if(this.files.length){

            $('.popover').remove();

            var uploadFiles = event.target.files;  
            var formData = new FormData();

            let attachedFileNames = [];

            for (let i = 0; i < uploadFiles.length; i++) {
                formData.append("files[]", uploadFiles[i]);
                var kbSize = uploadFiles[i].size/1024;
                if(kbSize > 100000) {
                    sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                    return;
                }
                attachedFileNames.push(uploadFiles[i].name);
                task_comment_attachment_chip_card_list += `<li class="list-group-item"><i class="fa fa-paperclip me-2"></i>${uploadFiles[i].name}</li>`;
            }
            
            comment_task_files = uploadFiles;

            $('#edit_task_modal #task_comment_attachment_chip_span').empty();
            $('#edit_task_modal #task_comment_attachment_chip_span').html(`
                <div class="chip chip-lg" id="task_comment_attachment_chip" data-bs-toggle="popover" data-bs-placement="top" data-bs-html="true" title="Comment Attachment(s)">
                    <span class="badge bg-secondary">${uploadFiles.length}</span> attached <span class="closebtn" onclick="resetTaskCommentChipAttachment()" title="Remove (${uploadFiles.length}) Comment Attachment(s)">×</span>
                </div>
            `);

            if(task_comment_attachment_chip_card_list != '')
            {
                $('#edit_task_modal #task_comment_attachment_chip_card').empty();
                $('#edit_task_modal #task_comment_attachment_chip_card').html(`
                    <ul class="list-group">
                        ${task_comment_attachment_chip_card_list}
                    </ul>
                `);
            }

            const popoverTrigger = $('#edit_task_modal #task_comment_attachment_chip')
            const popoverContent = $('#edit_task_modal #task_comment_attachment_chip_card')

            new bootstrap.Popover(popoverTrigger, {
                content: popoverContent.innerHTML,
                boundary: 'viewport', // Optional: Ensure popover stays within the viewport
            });

        }
    });

    $('#edit_task_modal #edue_date').on('changeDate', function(event) {
        updateTaskDetails(event, 'due_date', $(this).val())
    });

    function ehandleTaskFiles(files) {
        const task_id = $('#edit_task_modal #show-id').val();

        var formData = new FormData();
        formData.append("task_id", task_id);
        
        // Append new files
        for (var j = 0; j < files.length; j++) {
            formData.append("files[]", files[j]);
            var kbSize = files[j].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/bulletin/task/store-attachments`,
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(res) {
                if(res.errors){
                    $.each(res.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#e"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    table_task.ajax.reload(null, false);                        
                    loadTaskAttachments();
                    var container = $('#edit_task_modal #taskAttachmentsList');
                    container.animate({
                        scrollTop: container.prop("scrollHeight")
                    }, 500);
                    Swal.fire({
                        position: 'center',
                        icon: res.status,
                        title: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                if(msg.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }

    function reloadDocumentDataTable(id)
    {
        $(`#attachment_card_${id}`).remove();
        $(`#task_comment_document_li_${id}`).remove();
        reloadDataTable();
    }

    function clickTaskUploadBtn() {
        $('#edit_task_modal #upload_documents').click();
    }

    $('#edit_task_modal #upload_documents').change(function () {
        if(this.files.length){
            var uploadFiles = event.target.files;  

            const task_id = $('#edit_task_modal #eid').val();

            var formData = new FormData();
            formData.append("task_id", task_id);

            for (let i = 0; i < uploadFiles.length; i++) {
                formData.append("files[]", uploadFiles[i]);
                var kbSize = uploadFiles[i].size/1024;
                if(kbSize > 100000) {
                    sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                    return;
                }
            }

            sweetAlertLoading();
            $.ajax({
                //laravel requires this thing, it fetches it from the meta up in the head
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: `/store/bulletin/task/store-attachments`,
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(res) {
                    if(res.errors){
                        $.each(res.errors,function (key , val){
                            sweetAlert2('warning', 'Check field inputs.');
                            $("#e"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                            console.log(key);
                        });
                    }
                    else{
                        table_task.ajax.reload(null, false);                        
                        loadTaskAttachments();
                        var container = $('#edit_task_modal #taskAttachmentsList');
                        container.animate({
                            scrollTop: container.prop("scrollHeight")
                        }, 500);
                        Swal.fire({
                            position: 'center',
                            icon: res.status,
                            title: res.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },error: function(msg) {
                    handleErrorResponse(msg);
                    if(msg.status == 403) {
                        sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                    }
                    console.log("Error");
                    console.log(msg.responseText);
                }


            });

        }
    });


    // activate tooltip
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    function loadCard() {
        var month = $('#months').val();
        var year = $('#years').val();
        
        var data = { month: month, year: year };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: "{{ route('tasks.index') }}",
            type: "POST",
            dataType: "json",
            data: data,
            success: function(response) {
                // console.log(response);
                task201(response.data['task201'], response.data['selectedYear'], response.data['selectedMonth']);
                task202(response.data['task202'], response.data['selectedYear'], response.data['selectedMonth']);
                task203(response.data['task203'], response.data['selectedYear'], response.data['selectedMonth']);
                task204(response.data['task204'], response.data['selectedYear'], response.data['selectedMonth']);
                task205(response.data['task205'], response.data['selectedYear'], response.data['selectedMonth']);
                task206(response.data['task206'], response.data['selectedYear'], response.data['selectedMonth']);
                // task order
                order701(response.data['order701'], response.data['selectedYear'], response.data['selectedMonth']);
                order702(response.data['order702'], response.data['selectedYear'], response.data['selectedMonth']);
                order703(response.data['order703'], response.data['selectedYear'], response.data['selectedMonth']);
                order704(response.data['order704'], response.data['selectedYear'], response.data['selectedMonth']);
                order705(response.data['order705'], response.data['selectedYear'], response.data['selectedMonth']);
                order706(response.data['order706'], response.data['selectedYear'], response.data['selectedMonth']);
            },
            error: function(xhr, status, error) {
                handleErrorResponse(error);
                console.log('Error:', error);
            }
        });
    };

    // // draggable card function for task
    $(document).ready(function() {
        let requestInProgress = false; // Flag variable

        $('.task-content').sortable({
            connectWith: '.task-content',
            cursor: 'grabbing',
            opacity: 0.6,
            placeholder: 'placeholder',
            update: function(event, ui) {
                const taskId = ui.item.data('task-id');
                let statusId = $(ui.item[0]).closest('.task-content').attr('id');
                statusId = statusId.slice(5);
                console.log('status id: ', statusId);
                console.log('task id: ', taskId);

                let data = {
                    id: taskId,
                    status_id: statusId
                };

                // Check if a request is already in progress
                if (!requestInProgress) {
                    requestInProgress = true; // Set the flag to true

                    sweetAlertLoading();
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: "PATCH",
                        url: "/store/bulletin/task/update-details",
                        data: JSON.stringify(data),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success: function(res) {
                            reloadDataTable();
                            Swal.fire({
                                position: 'center',
                                icon: res.status,
                                title: res.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            requestInProgress = false; // Reset the flag
                        },
                        error: function(res) {
                            handleErrorResponse(msg);
                            if (res.status == 403) {
                                sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                            }
                            console.log(res.responseText);
                            requestInProgress = false; // Reset the flag
                        }
                    });
                }
            }
        }).disableSelection();
    });

    // draggable card function for task order
    $(document).ready(function() {
        let requestInProgress = false; // Flag variable

        $('.order-content').sortable({
            connectWith: '.order-content',
            cursor: 'grabbing',
            opacity: 0.6,
            placeholder: 'placeholder',
            update: function(event, ui) {
                const taskId = ui.item.data('task-id');
                let statusId = $(ui.item[0]).closest('.order-content').attr('id');
                statusId = statusId.slice(6);
                console.log('status id: ', statusId);
                console.log('task id: ', taskId);

                let data = {
                    id: taskId,
                    status_id: statusId
                };

                // Check if a request is already in progress
                if (!requestInProgress) {
                    requestInProgress = true; // Set the flag to true

                    sweetAlertLoading();
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: "PATCH",
                        url: "/store/bulletin/task/update-details",
                        data: JSON.stringify(data),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success: function(res) {
                            reloadDataTable();
                            Swal.fire({
                                position: 'center',
                                icon: res.status,
                                title: res.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            requestInProgress = false; // Reset the flag
                        },
                        error: function(res) {
                            handleErrorResponse(msg);
                            if (res.status == 403) {
                                sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                            }
                            console.log(res.responseText);
                            requestInProgress = false; // Reset the flag
                        }
                    });
                }
            }
        }).disableSelection();
    });
</script>
@stop
