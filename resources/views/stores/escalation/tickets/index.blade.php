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
                <div id="list-view" class="tab-pane fade show active">
                    <!-- list-view -->
                    <div class="p-3 bg-white border border-top-0 rounded-bottom-4">
                        <div class="card-header ticket-header">
                            <select name='length_change' id='length_change' class="table_length_change form-select">
                            </select>
                            <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
                            <select name='is_archived_list' id='is_archived_list' class="table_length_change form-select me-2" onchange="filterArchive('list', this.value)">
                                <option value="0" selected>Active</option>
                                <option value="1">Archived</option>
                            </select>
                            <div class="dropdown table_length_change">
                                <button class="btn btn-outline-danger dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item d-flex" href="javascript:archiveSelectedTicket();">
                                            Archive <i class="fa-solid fa-box-archive ms-auto text-danger"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="dt_table" class="table row-border table-hover dt-table-fixed-first-colum" style="width:100%">
                                    <thead></thead>
                                    <tbody>                                   
                                    </tbody>
                                    <tfooter></tfooter>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="grid-view" class="tab-pane fade">
                    <!-- grid view -->
                    <div class="p-3 bg-white rounded-bottom-4">
                        <!-- Date Filters -->
                        <div class="mb-3 row">
                            <div id="result" class="col-7">
                                <select name='is_archived_board' id='is_archived_board' class="col form-select w-25" onchange="filterArchive('board', this.value)">
                                    <option value="0" selected>Active</option>
                                    <option value="1">Archived</option>
                                </select>
                            </div>
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
                        <div id="ticket-container" class="gap-3 d-flex container-lists">
                            <!-- TO DO SECTION -->
                            <div class="card rounded-4 bg-body-secondary" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white rounded-3 card-title fs-6 bg-secondary">TO DO</h6>
                                            <span id="ticket-201-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal(201)" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Ticket
                                        </button>
                                    </div>
                                    <div id="ticket-201" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- IN PROGRESS SECTION -->
                            <div class="bg-blue-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-blue-500 rounded-3 card-title fs-6">IN PROGRESS</h6>
                                            <span id="ticket-202-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal(202)" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Ticket
                                        </button>
                                    </div>
                                    <div id="ticket-202" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- TO ANALYZE SECTION -->
                            <div class="bg-jade-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-jade-500 rounded-3 card-title fs-6">TO ANALYZE</h6>
                                            <span id="ticket-203-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal(203)" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Ticket
                                        </button>
                                    </div>
                                    <div id="ticket-203" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- TO VERIFY SECTION -->
                            <div class="bg-yellow-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-yellow-500 rounded-3 card-title fs-6">TO VERIFY</h6>
                                            <span id="ticket-204-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal(204)" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Ticket
                                        </button>
                                    </div>
                                    <div id="ticket-204" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- WAITING SECTION -->
                            <div class="bg-red-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-red-500 rounded-3 card-title fs-6">WAITING</h6>
                                            <span id="ticket-205-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal(205)" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Ticket
                                        </button>
                                    </div>
                                    <div id="ticket-205" class="px-3 mb-3 content lists"></div>
                                </div>
                            </div>
                            <!-- COMPLETED SECTION -->
                            <div class="bg-lawn-green-200 card rounded-4" style="min-width: 24rem;">
                                <div class="p-1">
                                    <div class="px-3 pt-3 mb-2 d-flex justify-content-between">
                                        <div class="gap-2 d-flex align-items-center">
                                            <h6 class="p-2 text-white bg-lawn-green-500 rounded-3 card-title fs-6">COMPLETED</h6>
                                            <span id="ticket-206-count" class="fs-6 fw-medium"></span>
                                        </div>
                                        <button onclick="showAddModal(206)" class="btn fw-medium">
                                            <i class="fa-solid fa-plus"></i>
                                            New Ticket
                                        </button>
                                    </div>
                                    <div id="ticket-206" class="px-3 mb-3 content lists"></div>
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
@include('stores/escalation/tickets/modals/add')
{{-- @include('stores/escalation/tickets/modals/edit') --}}
@include('stores/escalation/tickets/modals/edit/form')
@include('stores/escalation/tickets/modals/delete')
@include('stores/escalation/tickets/modals/archive')
@include('stores/escalation/ticketDocuments/modals/delete')
@include('stores/escalation/tickets/modals/watcher')
@include('stores/escalation/tickets/modals/assignee')
@stop

@section('pages_specific_scripts')  
<style>
    /* MAKE LEFT COLUMN FIXEZ */
    .dt-table-fixed-first-column thead th:nth-child(1),
    .dt-table-fixed-first-column tbody td:nth-child(1) {
        left: 0 !important;
        z-index: 1 !important;
        position: -webkit-sticky !important;
        position: sticky !important;
        left: 0 !important;
    }
    .popover {
        max-width: 100%; /* Max Width of the popover */
        box-shadow: 2px 4px 10px rgb(193 193 193);
    }
    .popover-header {
        background-color: #6c757d;
        color: white;
    }

    .bxs-cloud-upload,
    .imageuploadify-message {
        display: none !important;
    }

    #edit_ticket_modal .modal-content {
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

    #attachmentsList .card {
        border-color: #A8A7A7 !important;
        border-radius: 5px !important;
    }

    .btn-group-xs>.btn, .btn-xs {
        --bs-btn-padding-y: 0.05rem;
        --bs-btn-padding-x: 0.3rem;
        --bs-btn-font-size: 0.675rem;
        --bs-btn-border-radius: var(--bs-border-radius-sm);
    }

    #edit_ticket_modal .subject_text:hover {
        color: #15a0a3;
    }
    #edit_ticket_modal .assigned_to:hover {
        color: #15a0a3;
    }
    #edit_ticket_modal .edit-icon {
        color:#a8a7a7;
    }

    #edit_ticket_modal .edit-icon:hover {
        color:#15a0a3;
    }
    .dots .dropdown-toggle::after {
        display: none;
    }

</style>

<script>
    let table_ticket;
    let table_ticket_watcher;
    let table_ticket_assignee;
    let table_ticket_watcher_selected_id = null;
    let table_ticket_assignee_selected_id = null;
    let original_edited_subject = '';
    let original_edited_description = '';
    let menu_store_id = {{request()->id}};
    let component_user_id = {{auth()->user()->id}};
    let comment_files;
    let is_modal_loading = true;
    let show_edit_modal = 'ticket';
    let filter_mine = false;
    let lastActiveTab = '';

    let selectedIds = {};  
    let unselectedIds = [];

    const urlSearchParams = new URLSearchParams(window.location.search);
    const urlGlobalTicketID = urlSearchParams.get('ticket-id');
    let urlGlobalTicketIndex = urlSearchParams.get('ticket-index');
    if(!urlGlobalTicketIndex) {
        urlGlobalTicketIndex = 0;
    }
    let urlGlobalTicketLength = urlSearchParams.get('ticket-length');
    if(!urlGlobalTicketLength) {
        urlGlobalTicketLength = 10;
    }

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
        original_edited_subject = '';
        original_edited_description = '';
        $(".form-control").removeClass("is-invalid");
        selectedIds = {};  
        unselectedIds = [];

        new PerfectScrollbar('#attachmentsList');
        new PerfectScrollbar('#commentsList');

        new PerfectScrollbar('#ticket-container');
        new PerfectScrollbar('#ticket-201');
        new PerfectScrollbar('#ticket-202');
        new PerfectScrollbar('#ticket-203');
        new PerfectScrollbar('#ticket-204');
        new PerfectScrollbar('#ticket-205');
        new PerfectScrollbar('#ticket-206');

        // Retrieve the last active tab from local storage
        lastActiveTab = localStorage.getItem('activeTab');
        if (lastActiveTab) {
            $('.nav-link[href="' + lastActiveTab + '"]').tab('show');
        }

        // Save the active tab to local storage when a tab is clicked
        $('.nav-link').on('shown.bs.tab', function(e) {
            var activeTab = $(e.target).attr('href');
            localStorage.setItem('activeTab', activeTab);
        });

        loadCard();

        $('#filter').click(function(e) {
            e.preventDefault();
            loadCard();
        });
        
        tinymce.init({
            selector: '#add_ticket_modal #description',
            toolbar: 'undo redo print spellcheckdialog formatpainter | blocks fontfamily fontsize | bold italic underline forecolor backcolor | link image | alignleft aligncenter alignright alignjustify lineheight | checklist bullist numlist indent outdent | removeformat',
            plugins: 'textcolor link',
            height: 400,
            branding: false
		});

        tinymce.init({
            selector: '#edit_ticket_modal #edescription',
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

        $('#edit_ticket_modal #show-documents').imageuploadify();

        $('#edit_ticket_modal').on('click', function (e) {
            if (!$(e.target).closest('#edescription').length && is_modal_loading === false) {
                var description = tinymce.get("edescription").getContent();
                if(original_edited_description != description)
                {
                    if(!description) {
                        description = '';
                    }
                    original_edited_description = description;
                    updateDetails(e, 'description', description);
                }
            }

            if (!$(e.target).closest('#esubject').length && is_modal_loading === false) {
                var subject = $('#edit_ticket_modal #esubject').val();
                if(original_edited_subject != subject)
                {
                    if(!subject) {
                        // $("#edit_ticket_modal #esubject").addClass("is-invalid");
                        return;
                    }
                    original_edited_subject = subject;
                    $(".form-control").removeClass("is-invalid");

                    $('#edit_ticket_modal #esubject').addClass('d-none');
                    $('#edit_ticket_modal #esubject_text').removeClass('d-none');

                    $('#edit_ticket_modal #subject_text').html(subject);
                    updateDetails(e, 'subject', subject);
                }
            }
        });


        
        $('#add_ticket_modal #due_date').datepicker({
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

        $('#edit_ticket_modal #edue_date').datepicker({
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

        loadTickets();
        loadWatchers();
        loadAssignees();
    });

    $('#edit_ticket_modal #edue_date').on('changeDate', function(event) {
        updateDetails(event, 'due_date', $(this).val())
    });

    // functions
    function loadTickets() {
        let data = {};        
        const dt_table = $('#dt_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            pageLength: urlGlobalTicketLength,
            dom: 'fBtp',
            order: [[10, 'desc']],
            buttons: [
                @can('menu_store.escalation.tickets.create')
                {
                    text: '+ New Ticket', 
                    className: 'btn btn-primary', 
                    action: function ( e, dt, node, config ) {
                        showAddModal();
                    }
                },
                @endcan
                @canany(['super-admin', 'menu_store.escalation.tickets.view_all'])
                {
                    text: 'Filter Mine', 
                    id: 'show_mine_btn',
                    className: 'btn btn-success show_mine_btn', 
                    value: 0,
                    action: function ( e, dt, node, config ) {
                        filterMine();
                    }
                }
                @endcanany
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/store/escalation/tickets/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                    data.filter_mine = filter_mine;
                    data.is_archived = $('#is_archived_list').val();
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
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
                { data: 'code', name: 'code', title: 'ID' , orderable: true, searchable: true, render: function(data, type, row) {
                    let code = `<b>#${row.code}</b>`;
                    @canany(['menu_store.escalation.tickets.update','menu_store.escalation.tickets.semi_update'])
                        code = `<b title="${data}" onclick="showTicketEditModal(${row.id})" style="cursor: pointer;">#${data}</b>`;
                    @endcan
                    if(row.is_assigned_to_head_category === true) {
                        code = `<b title="${data}" onclick="showTicketEditModal(${row.id})" style="cursor: pointer;">#${data}</b>`;
                    }
                    return code;
                } },
                { data: 'subject', name: 'subject', title: 'Subject', render: function(data, type, row) {
                    let subject = `<div class="datatable-long-description-field-truncate" title="${data}">${data}</div>`;
                    @canany(['menu_store.escalation.tickets.update','menu_store.escalation.tickets.semi_update'])
                        subject = `<div class="task-subject datatable-long-description-field-truncate" title="${data}" onclick="showTicketEditModal(${row.id})" style="cursor: pointer;">${data}</div>`;
                    @endcan
                    if(row.is_assigned_to_head_category === true) {
                        subject = `<div class="task-subject datatable-long-description-field-truncate" title="${data}" onclick="showTicketEditModal(${row.id})" style="cursor: pointer;">${data}</div>`;
                    }
                    return subject;
                } },
                { data: 'assigned_to', name: 'assigned_to', title: 'Assigned To', render: function(data, type, row) {
                    return `${row.avatar}`;
                } },
                { data: 'status', name: 'status', title: 'Status' , orderable: false, searchable: false},
                { data: 'currentStatusLogSpentTimeMinutes', name: 'currentStatusLogSpentTimeMinutes', title: 'Spent Time' , orderable: false, searchable: false},
                { data: 'priority', name: 'priority', title: 'Priority' , orderable: false, searchable: false},
                { data: 'due_date', name: 'due_date', title: 'Due Date', render: function(data, type, row) {
                    return `${row.formatted_due_date}`;
                } },
                { data: 'created_by', name: 'created_by', title: 'Created By', render: function(data, type, row) {
                    return `${row.empAvatar}`;
                } },
                { data: 'watcherList', name: 'watcherList', title: 'Watchers' , width: '15%',  orderable: false, searchable: false},
                { data: 'created_at', name: 'created_at', title: 'Date Created (PST)', render: function(data, type, row) {
                    return `${row.formatted_pst_created_at}`;
                } },
                { data: 'actions', name: 'actions', title: 'Action' , width: '12%', orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = dt_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));

                if(urlGlobalTicketIndex !== 0) {
                    // // Calculate the page number based on the row index and page length
                    var pageNumber = Math.floor(urlGlobalTicketIndex / urlGlobalTicketLength);
    
                    // Go to the calculated page
                    table_ticket.page.len(urlGlobalTicketLength).draw()
                    table_ticket.page(pageNumber).draw('page')
    
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

        table_ticket = dt_table;
        table_ticket.buttons().container().appendTo( '.ticket-header' );
        $('#search_input').val(table_ticket.search());
		$('#search_input').keyup(function(){ table_ticket.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_ticket.page.len($(this).val()).draw() });

        $('.dataTables_scrollBody').scroll(function (){
            let cols = 2 // how many columns should be fixed
            let container = $(this)
            let offset = container.scrollLeft()
            container.add(container.prev()).find('tr').each(function (index,row){ // .add(container.prev()) to include the header
                $(row).find('th').each(function (index, th) {
                    if (index < cols) {
                        $(th).css({ position: 'relative', left: offset + 'px', zIndex: '1' });
                    }
                });
                $(row).find('td, th').each(function (index,cell){
                    if(index>=cols) return
                    $(cell).css({position:'relative',left:offset+'px'})
                })
            })
        });
    }

    function drawCallbackFunction()
    {
        if(urlGlobalTicketID) {
            @can('menu_store.escalation.tickets.update')
                showTicketEditModal(urlGlobalTicketID)
                return;
            @endcan
            @cannot('menu_store.escalation.tickets.update')
                showTicketModal(urlGlobalTicketID)
                return;
            @endcan
        }
    }
    setTimeout(drawCallbackFunction, 4000);

    function updateCheckboxesState() {
        let currentPageCheckboxes = $('input[type="checkbox"].row-checkbox', table_ticket.rows({ 'page': 'current' }).nodes());

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
        $('input[type="checkbox"].row-checkbox', table_ticket.rows().nodes()).each(function() {
            let id = $(this).val();
            let isChecked = $(this).prop('checked');
        });
    }

    function clearSelection() {
        selectedIds = {};
        unselectedIds = [];
    }
    
    function filterMine() {
        let value = $('.show_mine_btn').text();
        
        if(value == "Filter Mine") {
            $('.show_mine_btn').text('Revert Filter');
            $('.show_mine_btn').removeClass('btn-success');
            $('.show_mine_btn').addClass('btn-secondary');
            filter_mine = true;
        } else {
            $('.show_mine_btn').text('Filter Mine');
            $('.show_mine_btn').addClass('btn-success');
            $('.show_mine_btn').removeClass('btn-secondary');
            filter_mine = false;
        }
        table_ticket.draw();
    }

    function showAddModal(status_id = 201){
        
        $(".form-control").removeClass("is-invalid");

        tinymce.get("description").setContent('');

        searchSelect2Api('assigned_to_employee_id', 'add_ticket_modal','/admin/search/user-employee');

        getStatusDropDown('status_id', status_id, 'task');
        getStatusDropDown('priority_status_id', 1, 'priority');

        populateNormalSelect(`#category_id`, 'add_ticket_modal', '/admin/search/support-category');

        

        let modal = $('#add_ticket_modal');

        modal.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modal.addClass('dragover');
        });

        modal.on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modal.removeClass('dragover');
        });

        modal.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modal.removeClass('dragover');

            var files = e.originalEvent.dataTransfer.files;
            handleFiles(files);
        });

        $('#add_ticket_modal #documents').hide();
        $('#add_ticket_modal').modal('show');
    }
    

    function handleFiles(files) {
        var fileInput = $('#add_ticket_modal #documents')[0];
        var dataTransfer = new DataTransfer();
        
        // Append new files
        for (var j = 0; j < files.length; j++) {
            dataTransfer.items.add(files[j]);
        }

        fileInput.files = dataTransfer.files;
        
        updateFileList(fileInput.files);
    }

    function handleFileSelect(files) {
        var dataTransfer = new DataTransfer();
        
        
        // Append new files
        for (var j = 0; j < files.length; j++) {
            dataTransfer.items.add(files[j]);
        }

       
        
        updateFileList(dataTransfer.files);
    }

    function updateFileList(files) {
        var fileList = $('#fileList');
        var existingFiles = $('#fileList .file-item').map(function() {
            return $(this).text().trim();
        }).get();
        fileList.empty(); // Clear previous list

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            if (!existingFiles.includes(file.name)) {
                (function(file) { // Create a closure to preserve the file
                    var fileItem = $('<div class="file-item"></div>').text(file.name);
                    var removeBtn = $('<span class="remove-file-btn">Remove</span>');

                    removeBtn.click(function() {
                        $(this).parent().remove();
                        updateHiddenInput(file.name);
                    });

                    fileItem.append(removeBtn);
                    fileList.append(fileItem);
                })(files[i]);
            }
        }
    }

    function updateHiddenInput(filename) {
        var hiddenInput = $('#add_ticket_modal #documents')[0];
        var dataTransfer = new DataTransfer();

        
        for (var i = 0; i < hiddenInput.files.length; i++) {
            if (hiddenInput.files[i].name !== filename) {
                dataTransfer.items.add(hiddenInput.files[i]);
            }
        }

        hiddenInput.files = dataTransfer.files;

        logUpdatedFiles(hiddenInput);
    }

    function logUpdatedFiles(fileInput) {
        var files = fileInput.files;
        if (files.length === 0) {
            console.log("No files selected.");
            return;
        }
        console.log("Updated files:");
        for (var i = 0; i < files.length; i++) {
            console.log(files[i].name);
        }
    }

    $('#documents').off('change').on('change', function(e) {
        var files = $(this)[0].files;
        handleFileSelect(files);
    });

    $('.file-label').off('click').click(function(e) {
        e.preventDefault();
        $('#documents').click();
    });

    $(document).on('click', '.remove-file-btn', function() {
        var filename = $(this).closest('.file-item').text().trim(); // Get the filename
        $(this).closest('.file-item').remove(); // Remove the file item from the list
        updateHiddenInput(filename);
    });

    function searchSelect2Api(_select_id, _modal_id, _url) {
        $(`#${_select_id}`).select2( {
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

    function clickStatusBtn(ticket_id) {

    }

    function changeTaskSelectedStatus(event, id)
    {
        event.preventDefault();
        updateDetails(event, 'status_id', id);
    }

    function changeTaskSelectedPriorityStatus(event, id)
    {
        event.preventDefault();
        updateDetails(event, 'priority_status_id', id);
    }

    function getStatusDropDown(_dropdown_id, _status_id, _category) {
        const data = {category: _category};
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/store/status/search",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
            
                var len = data.length;
                
                $(`#${_dropdown_id}`).empty();
                
                for( var k = 0; k<len; k++){
                    var kid = data[k]['id'];
                    var kname = data[k]['name'];
                    if(kid==_status_id){$(`#${_dropdown_id}`).append("<option selected value='"+kid+"'>"+kname+"</option>");}
                    else{
                        $(`#${_dropdown_id}`).append("<option value='"+kid+"'>"+kname+"</option>");
                    }
                }
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function populateNormalSelect(_selector, _model_id, _url, params = {}, _id = null)
    {
        $(_selector).empty();
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
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function reloadDataTable()
    {
        table_ticket.ajax.reload(null, false);
        loadCard();
    }
    
    // event listeners
    $('#add_ticket_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();   
        $('#add_ticket_modal #fileList').empty();
        // $('#add_ticket_modal #documents')[0].files = null;
    });

    $('#edit_ticket_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();   
        $('#edit_ticket_modal #efileList').empty();
        // $('#add_ticket_modal #documents')[0].files = null;
    });

    $('#semi_edit_ticket_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    function reloadDocumentDataTable(id)
    {
        $(`#attachment_card_${id}`).remove();
        $(`#ticket_comment_document_li_${id}`).remove();
        reloadDataTable();
    }

    function showTicketEditModal(id){
        // $('.imageuploadify-container').remove();
        let modal = $('#edit_ticket_modal');
        $('#edit_ticket_modal #edocuments').hide();
        modal.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modal.addClass('dragover');
        });

        modal.on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modal.removeClass('dragover');
        });

        modal.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modal.removeClass('dragover');

            var files = e.originalEvent.dataTransfer.files;
            ehandleFiles(files);
        });

        var btn = document.querySelector(`#ticket-edit-btn-${id}`);
        let data = btn.dataset;
        console.log("tix id", id);
        let formatted_due_date = data.formatted_due_date;
        let menuclass = data.menuclass;
        let arr = JSON.parse(data.array);
        console.log("data", data);
        let code = arr.code;
        let subject = arr.subject;
        let description = arr.description;
        let assigned_to_employee_id = arr.assigned_to_employee_id;
        let assigned_to = data.assigned_to;
        let due_date = data.due_date;
        let status_id = arr.status_id;
        let priority_status_id = arr.priority_status_id;
        let statusLogs = arr.status_logs;
        let watcher_list = data.watcher_list;

        if(description == null || description == undefined)
        {
            description = '';
        }

        const loadDiv = `
            <div class="my-2 border shadow-none card">
                <div class="py-2 card-body">
                    <div class="p-4 row">
                        <div class="mb-3 spinner-grow text-primary" role="status"> 
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#edit_ticket_modal #attachmentsList').html(loadDiv);
        $('#edit_ticket_modal #commentsList').html(loadDiv);

        original_edited_subject = subject;
        original_edited_description = description;

        $('#edit_ticket_modal #ewatchers').html(watcher_list);

        $('#edit_ticket_modal #subject_text').html(subject);


        if(assigned_to_employee_id) {
            let assigned_to = arr.assigned_to;

            resolveEmployeeAvatar(assigned_to);

            let status_data = arr.status;
            let priority_data = arr.priority;

            const status = `<button class="btn btn-sm w-100 btn-${status_data.class}">${status_data.name}</button>`;
            const priority = `<button class="btn btn-sm w-100 btn-outline-${priority_data.class}"><i class="fa fa-flag me-2"></i>${priority_data.name}</button>`;

            let dueDateHtml = '';
            if(formatted_due_date) {
                dueDateHtml += ' <span class="me-2">Due Date on '+formatted_due_date+'</span>';
            }
            // $('#edit_ticket_modal #assigned_to').html(selectEmp);
            $('#edit_ticket_modal #status').html(status);
            $('#edit_ticket_modal #priority').html(priority);
            $('#edit_ticket_modal #show_due_date').html(dueDateHtml);
            $('#edit_ticket_modal #codeDiv').html(`
                <button type="button" class="mx-2 btn btn-dark position-relative me-lg-5" title="Copy Code" onclick="copyText('${code}', '#copiedCodeAlert')"> <i class='fa-regular fa-copy me-2'></i> <span class="font-15 fw-bold"><b>#${code}</span></button>
            `);
            $('#assignee_ticket_modal #codeAssigneeDiv').html(`
                <button type="button" class="mx-2 btn btn-dark position-relative me-lg-5" title="Copy Code" onclick="copyText('${code}', '#assignee_ticket_modal #copiedCodeAssigneeAlert')"> <i class='fa-regular fa-copy me-2'></i> <span class="font-15 fw-bold">#${code}</span></button>
            `);
        }

        let createdByEmp = ``;
        if(arr['user']['employee']['image'] != '' && arr['user']['employee']['image'] != null) {
            createdByEmp = `
                <div class="d-flex">
                    <img src="/upload/userprofile/${arr['user']['employee']['image']}" width="35" height="35" class="rounded-circle image-has-border" alt="">
                    <div class="mt-2 flex-grow-1 ms-3">
                        <p id="show-assign-to-fullname" class="mb-0 font-weight-bold">${arr['user']['employee']['firstname']} ${arr['user']['employee']['lastname']}</p>
                    </div>
                </div>
            `;
        } else {
            createdByEmp = `
                <div class="employee-avatar-${arr['user']['employee']['initials_random_color']}-initials hr-employee">
                    ${arr['user']['employee']['firstname'].charAt(0)}${arr['user']['employee']['lastname'].charAt(0)}
                </div>
                <p id="show-assign-to-fullname" class="mt-2 mb-0 font-weight-bold ms-3">${arr['user']['employee']['firstname']} ${arr['user']['employee']['lastname']}</p>
            `;
        }
        $('#edit_ticket_modal #created_by').html(createdByEmp);

        loadStatusLogs(statusLogs);

        tinymce.get("edescription").setContent(description);

        $("#edit_ticket_modal input#esubject").val(subject);
        $("#edit_ticket_modal textarea#edescription").val(description);
        $("#edit_ticket_modal input#eid").val(id);
        $("#edit_ticket_modal #edue_date").val(due_date);

        getTaskStatusDropDownUl('#statusDropDown', '#statusDropDownUl', status_id, 'task');
        getPriorityStatusDropDownUl('#priorityStatusDropDown', '#priorityStatusDropDownUl', priority_status_id, 'priority');

        loadAttachments();
        loadComments();

        $('#edit_ticket_modal').modal('show');

        is_modal_loading = false;
        show_edit_modal = 'ticket';
    }

    function showGridTicketEditModal(ticket, id){
        let modal = $('#edit_ticket_modal');
        $('#edit_ticket_modal #edocuments').hide();
        modal.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modal.addClass('dragover');
        });

        modal.on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modal.removeClass('dragover');
        });

        modal.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modal.removeClass('dragover');

            var files = e.originalEvent.dataTransfer.files;
            ehandleFiles(files);
        });
        
        ticketData = ticket.getAttribute('data-ticket-items');
        let arr = JSON.parse(decodeURIComponent(ticketData));
        let assigned_to = arr.assigned_to;
        let assigned_to_employee_id = arr.assigned_to_employee_id;
        let code = arr.code;
        let description = arr.description;
        let due_date = arr.due_date;
        let formatted_due_date = arr.formatted_due_date;
        let menuclass = arr.menuClass;
        let priority_status_id = arr.priority_status_id;
        let statusLogs = arr.status_logs;
        let status_id = arr.status_id;
        let subject = arr.subject;
        let watcher_list = arr.watcherList;
        console.log("ticket array: ", arr);

        if(description == null || description == undefined) {
            description = '';
        }

        const loadDiv = `
            <div class="my-2 border shadow-none card">
                <div class="py-2 card-body">
                    <div class="p-4 row">
                        <div class="mb-3 spinner-grow text-primary" role="status"> 
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#edit_ticket_modal #attachmentsList').html(loadDiv);
        $('#edit_ticket_modal #commentsList').html(loadDiv);

        original_edited_subject = subject;
        original_edited_description = description;

        $('#edit_ticket_modal #ewatchers').html(watcher_list);

        $('#edit_ticket_modal #subject_text').html(subject);


        if(assigned_to_employee_id) {
            let assigned_to = arr.assigned_to;

            resolveEmployeeAvatar(assigned_to);

            let status_data = arr.status;
            let priority_data = arr.priority;

            const status = `<button class="btn btn-sm w-100 btn-${status_data.class}">${status_data.name}</button>`;
            const priority = `<button class="btn btn-sm w-100 btn-outline-${priority_data.class}"><i class="fa fa-flag me-2"></i>${priority_data.name}</button>`;

            let dueDateHtml = '';
            if(formatted_due_date) {
                dueDateHtml += ' <span class="me-2">Due Date on '+formatted_due_date+'</span>';
            }
            // $('#edit_ticket_modal #assigned_to').html(selectEmp);
            $('#edit_ticket_modal #status').html(status);
            $('#edit_ticket_modal #priority').html(priority);
            $('#edit_ticket_modal #show_due_date').html(dueDateHtml);
            $('#edit_ticket_modal #codeDiv').html(`
                <button type="button" class="mx-2 btn btn-dark position-relative me-lg-5" title="Copy Code" onclick="copyText('${code}', '#copiedCodeAlert')"> <i class='fa-regular fa-copy me-2'></i> <span class="font-15 fw-bold"><b>#${code}</span></button>
            `);
            $('#assignee_ticket_modal #codeAssigneeDiv').html(`
                <button type="button" class="mx-2 btn btn-dark position-relative me-lg-5" title="Copy Code" onclick="copyText('${code}', '#assignee_ticket_modal #copiedCodeAssigneeAlert')"> <i class='fa-regular fa-copy me-2'></i> <span class="font-15 fw-bold">#${code}</span></button>
            `);
        }

        let createdByEmp = ``;
        if(arr['user']['employee']['image'] != '' && arr['user']['employee']['image'] != null) {
            createdByEmp = `
                <div class="d-flex">
                    <img src="/upload/userprofile/${arr['user']['employee']['image']}" width="35" height="35" class="rounded-circle image-has-border" alt="">
                    <div class="mt-2 flex-grow-1 ms-3">
                        <p id="show-assign-to-fullname" class="mb-0 font-weight-bold">${arr['user']['employee']['firstname']} ${arr['user']['employee']['lastname']}</p>
                    </div>
                </div>
            `;
        } else {
            createdByEmp = `
                <div class="employee-avatar-${arr['user']['employee']['initials_random_color']}-initials hr-employee">
                    ${arr['user']['employee']['firstname'].charAt(0)}${arr['user']['employee']['lastname'].charAt(0)}
                </div>
                <p id="show-assign-to-fullname" class="mt-2 mb-0 font-weight-bold ms-3">${arr['user']['employee']['firstname']} ${arr['user']['employee']['lastname']}</p>
            `;
        }
        $('#edit_ticket_modal #created_by').html(createdByEmp);
        
        tinymce.get("edescription").setContent(description);

        $("#edit_ticket_modal input#esubject").val(subject);
        $("#edit_ticket_modal textarea#edescription").val(description);
        $("#edit_ticket_modal input#eid").val(id);
        $("#edit_ticket_modal #edue_date").val(due_date);
        $('#edit_ticket_modal').modal('show');

        getTaskStatusDropDownUl('#statusDropDown', '#statusDropDownUl', status_id, 'task');
        getPriorityStatusDropDownUl('#priorityStatusDropDown', '#priorityStatusDropDownUl', priority_status_id, 'priority');
        
        loadStatusLogs(statusLogs);
        loadAttachments();
        loadComments();

        is_modal_loading = false;
        show_edit_modal = 'ticket';
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleString('en-US', options);
    }

    function loadAttachments()
    {
        const ticket_id = $('#edit_ticket_modal #eid').val();

        let params = {};
        $.ajax({    
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/store/escalation/tickets/load-attachments/"+ticket_id,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(params),
            success: function(res) {
            
                const documents = res.data;

                $('#edit_ticket_modal #attachmentsList').empty();
                $.each(documents, function(i, item) {
                    const url = item.url;
                    const k = i+1;
                    const ext = item.ext.toLowerCase();
                    const filename = item.name;
                    const fileIcon = fileUtil(ext, 'icon');
                    const fileClass = fileUtil(ext, 'class');
                    
                    let _attachment = '';
                    if(ext == 'png' || ext == 'jpeg' || ext == 'jpg' || ext == 'ico') {
                        _attachment = `<div class="mb-2 image-container">
                            <a class="pb-0 mb-0" target="_blank" href="${url}">
                                <img src="${url}" alt="${filename}" title="${filename}" class="responsive-img">
                            </a>
                        </div>`;
                    }

                    $('#edit_ticket_modal #attachmentsList').append(`
                        <div class="mt-2 mb-0 border shadow-none card" id="attachment_card_${item.id}">
                            <div class="py-2 card-body">
                                ${_attachment}
                                <div class="p-0 m-0 row">
                                    <div class="px-0 mx-0 col-md-9">
                                        <a class="pb-0 mb-0" target="_blank" href="${url}">
                                            <i class="bx ${fileIcon} me-1 font-12 ${fileClass}"></i>
                                            <span class="document_filename_link text-primary">${filename}</span>
                                        </a>
                                    </div>
                                    <div class="px-0 mx-0 col-md-3 text-end">
                                        <div class="pb-0 mb-0">
                                            <a href="/admin/store-document/download/s3/${item.id}">
                                                <div class="btn btn-xs btn-primary">
                                                    <i class="fa fa-download"></i>
                                                </div>
                                            </a>
                                            <div class="btn btn-xs btn-danger" onclick="clickDeleteDocumentBtn(event, ${item.id})">
                                                <i class="fa fa-trash-can"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <small class="" style="color: #a9a9a9;">${currentDateToYMDHMS(item.created_at, 'M d, Y H:i A')}</small>
                            </div>
                        </div>
                    `); 
                });

                // var container = $('#edit_ticket_modal #attachmentsList');
                // container.animate({
                //     scrollTop: container.prop("scrollHeight")
                // }, 500);

            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function loadComments()
    {
        const ticket_id = $('#edit_ticket_modal #eid').val();
        const auth_emp_id = {{ $authEmployee->id }};
        const assigned_to_employee_id = $('#edit_ticket_modal #eassigned_to_employee_id').val();

        let params = {};
        $.ajax({    
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/store/escalation/tickets/load-comments/"+ticket_id,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(params),
            success: function(res) {
            
                const comments = res.data;

                $('#edit_ticket_modal #commentsList').empty();
                $.each(comments, function(i, item) {
                    let comment_user = item.user;
                    let comment_employee = comment_user.employee;
                    let comment_created_at = item.formatted_pst_created_at;

                    var comment_employee_firstname = comment_employee['firstname'];
                    var comment_employee_lastname = comment_employee['lastname'];
                    let comment_employee_fullname = `${comment_employee_firstname} ${comment_employee_lastname}`;
                    let comment_employee_initials = comment_employee_firstname.charAt(0) +''+ comment_employee_lastname.charAt(0);
                    comment_employee_initials = comment_employee_initials.toUpperCase();

                    let comment_documents = item.documents ?? [];

                    let comment_document_list = '';
                    $.each(comment_documents, function(d, document) {
                        let document_path = document.path;

                        if(document.ext == 'png' || document.ext == 'jpeg' || document.ext == 'jpg' || document.ext == 'ico') {
                            comment_document_list += `<li class="list-group-item document_filename_link text-primary" id="ticket_comment_document_li_${document.id}">
                                <a class="text-primary" href="${document.url}" target="_blank">
                                    <div class="mb-2 image-container">
                                        <img src="${document.url}" alt="${document.name}" title="${document.name}" class="responsive-img">
                                    </div>
                                    <i class="fa fa-paperclip me-2"></i>${document.name}
                                </a>
                            </li>`;
                        } else {
                            comment_document_list += `<li class="list-group-item document_filename_link text-primary" id="ticket_comment_document_li_${document.id}">
                                <a class="text-primary" href="${document.url}" target="_blank">
                                    <i class="fa fa-paperclip me-2"></i>${document.name}
                                </a>
                            </li>`;
                        }

                    });

                    if(comment_document_list != '')
                    {
                        comment_document_list = `<ul class="mb-2 list-group">${comment_document_list}</ul>`;
                    }

                    let border_color = (auth_emp_id == comment_employee['id']) ? 'ticket_comment_section_body_row_card' : (assigned_to_employee_id == comment_employee['id'] ? 'ticket_comment_section_body_row_card2' : 'ticket_comment_section_body_row_card3');

                    let ticket_comment_section_cols = ``;

                    let comment_description =  item.comment.replace(/\n/g, '<br>');

                    if(comment_employee['image'] != '' && comment_employee['image'] != null) {
                        ticket_comment_section_cols = `
                            <div class="mt-3 col-md-12">
                                <div class="card mb-0 ${border_color}">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <img src="/upload/userprofile/${comment_employee['image']}" width="45" height="45" class="rounded-circle image-has-border" alt="">
                                            <div class="flex-grow-1 ms-3">
                                                <p id="show-assign-to-fullname" class="mb-0 font-weight-bold font-12">
                                                    <b>${comment_employee_fullname}</b>
                                                </p>
                                                <small>${comment_created_at}</small>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            ${comment_description}
                                            ${comment_document_list}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        ticket_comment_section_cols = `
                            <div class="mt-3 col-md-12">
                                <div class="card mb-0 ${border_color}">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="employee-avatar-${comment_employee['initials_random_color']}-initials hr-employee" style="width: 45px !important; height: 45px !important; font-size: 20px !important;">
                                                ${comment_employee_initials}
                                            </div>
                                            <div class="font-weight-bold ms-3 font-12">
                                                <p class="mb-0"><b>${comment_employee_fullname}</b></p>
                                                <small>${comment_created_at}</small>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            ${comment_description}
                                            ${comment_document_list}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    $('#edit_ticket_modal #commentsList').append(ticket_comment_section_cols);
                });

                var container = $('#edit_ticket_modal #commentsList');
                container.animate({
                    scrollTop: container.prop("scrollHeight")
                }, 500);

            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function loadStatusLogs(statusLogs)
    {
        $('#edit_ticket_modal #statusLogsDiv').empty();
        let total_milliseconds = 0;
        $.each(statusLogs, function(i, item) {
            let status_logs_status = item.status ?? null;
            let status_logs_status_name = status_logs_status.name ?? '';
            let time_end = item.time_end ?? null;
            if(time_end == '' || time_end == null) {
                time_end = currentDateToYMDHMS(null);
            }
            let milliseconds = calculateDateTimeDifference(item.time_start, time_end);
            let calc = convertMillisecondsToTime(milliseconds);

            let time_text = '';
            if(calc.days > 0) {
                time_text += `${calc.days}d `;
            }
            if(calc.hours > 0) {
                time_text += `${calc.hours}h `;
            }
            if(calc.minutes > 0) {
                time_text += `${calc.minutes}m `;
            }
            if(calc.seconds > 0) {
                time_text += `${calc.seconds}s`;
            }

            if(status_logs_status_name.toLowerCase() == 'completed') {
                time_text = '--';
                milliseconds = 0;
            }

            total_milliseconds += milliseconds;
            
            $('#edit_ticket_modal #statusLogsDiv').append(`
                <div class="d-flex">
                    <span><i class="fa fa-clock me-2 text-${status_logs_status.class}"></i>${status_logs_status_name}</span>
                    <span class="ms-auto">${time_text}</span>
                </div>
            `);
        });

        let total_calc = convertMillisecondsToTime(total_milliseconds);
        let total_time_text = '';
        if(total_calc.days > 0) {
            total_time_text += `${total_calc.days}d `;
        }
        if(total_calc.hours > 0) {
            total_time_text += `${total_calc.hours}h `;
        }
        if(total_calc.minutes > 0) {
            total_time_text += `${total_calc.minutes}m `;
        }
        if(total_calc.seconds > 0) {
            total_time_text += `${total_calc.seconds}s`;
        }
        $('#edit_ticket_modal #statusLogsTotalDiv').html(`
            <div class="mt-2 d-flex">
                <span class="fw-bold"><i class="fa fa-clock me-2"></i>Total</span>
                <span class="ms-auto fw-bold">${total_time_text}</span>
            </div>
        `);
    }

    function resolveEmployeeAvatar(employee)
    {
        var firstname = employee['firstname'];
        var lastname = employee['lastname'];
        let fullname = `${firstname} ${lastname}`;
        let initials = firstname.charAt(0) +''+ lastname.charAt(0);
        initials = initials.toUpperCase();

        let selectEmp = ``;
        if(employee['image'] != '' && employee['image'] != null) {
            selectEmp = `
                <div class="d-flex">
                    <img src="/upload/userprofile/${employee['image']}" width="35" height="35" class="rounded-circle image-has-border" alt="">
                    <div class="mt-2 flex-grow-1 ms-3">
                        <p id="show-assign-to-fullname" class="mb-0 font-weight-bold font-12"><b>${fullname}</b></p>
                    </div>
                </div>
                <span class="ms-auto"><i class="fa fa-edit ms-5 edit-icon"></i></span>
            `;
        } else {
            selectEmp = `
                <div class="d-flex">
                    <div class="employee-avatar-${employee['initials_random_color']}-initials hr-employee" style="width: 35px !important; height: 35px !important; font-size: 12px !important;">
                    ${initials}
                    </div>
                    <p class="mt-2 mb-0 font-weight-bold ms-3 font-12"><b>${fullname}</b></p>
                </div>
                <span class="ms-auto"><i class="fa fa-edit ms-5 edit-icon"></i></span>
            `;
        }
        $('#edit_ticket_modal #assigned_to').html(selectEmp);
        $('#edit_ticket_modal #eassigned_to_employee_id').val(employee['id']);
    }

    function ehandleFiles(files) {
        const ticket_id = $('#edit_ticket_modal #eid').val();

        var formData = new FormData();
        formData.append("ticket_id", ticket_id);
        
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
            url: `/store/escalation/tickets/store-attachments`,
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
                    table_ticket.ajax.reload(null, false);                        
                    loadAttachments();
                    var container = $('#edit_ticket_modal #attachmentsList');
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


    function clickUploadBtn() {
        $('#edit_ticket_modal #upload_documents').click();
    }

    $('#edit_ticket_modal #upload_documents').change(function () {
        if(this.files.length){
            var uploadFiles = event.target.files;  

            const ticket_id = $('#edit_ticket_modal #eid').val();

            var formData = new FormData();
            formData.append("ticket_id", ticket_id);

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
                url: `/store/escalation/tickets/store-attachments`,
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
                        table_ticket.ajax.reload(null, false);                        
                        loadAttachments();
                        var container = $('#edit_ticket_modal #attachmentsList');
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


    $('#edit_ticket_modal_upload_documents').change(function () {
        comment_files = null;
        let comment_attachment_chip_card_list = '';
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
                comment_attachment_chip_card_list += `<li class="list-group-item"><i class="fa fa-paperclip me-2"></i>${uploadFiles[i].name}</li>`;
            }
            
            comment_files = uploadFiles;

            $('#comment_attachment_chip_span').empty();
            $('#comment_attachment_chip_span').html(`
                <div class="chip chip-lg" id="comment_attachment_chip" data-bs-toggle="popover" data-bs-placement="top" data-bs-html="true" title="Comment Attachment(s)">
                    <span class="badge bg-secondary">${uploadFiles.length}</span> attached <span class="closebtn" onclick="resetCommentChipAttachment()" title="Remove (${uploadFiles.length}) Comment Attachment(s)">×</span>
                </div>
            `);

            if(comment_attachment_chip_card_list != '')
            {
                $('#comment_attachment_chip_card').empty();
                $('#comment_attachment_chip_card').html(`
                    <ul class="list-group">
                        ${comment_attachment_chip_card_list}
                    </ul>
                `);
            }

            const popoverTrigger = document.getElementById('comment_attachment_chip');
            const popoverContent = document.getElementById('comment_attachment_chip_card');

            new bootstrap.Popover(popoverTrigger, {
                content: popoverContent.innerHTML,
                boundary: 'viewport', // Optional: Ensure popover stays within the viewport
            });

        }
    });

    function loadCard() {
        var month = $('#months').val();
        var year = $('#years').val();
        var is_archived = $('#is_archived_board').val();
        
        var data = { month: month, year: year, is_archived: is_archived };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: "{{ route('escalationTicket.index') }}",
            type: "POST",
            dataType: "json",
            data: data,
            success: function(response) {
                ticket201(response.data['ticket201'], response.data['selectedYear'], response.data['selectedMonth']);
                ticket202(response.data['ticket202'], response.data['selectedYear'], response.data['selectedMonth']);
                ticket203(response.data['ticket203'], response.data['selectedYear'], response.data['selectedMonth']);
                ticket204(response.data['ticket204'], response.data['selectedYear'], response.data['selectedMonth']);
                ticket205(response.data['ticket205'], response.data['selectedYear'], response.data['selectedMonth']);
                ticket206(response.data['ticket206'], response.data['selectedYear'], response.data['selectedMonth']);
            },
            error: function(xhr, status, error) {
                handleErrorResponse(error);
                console.log('Error:', error);
            }
        });
    };

    // TICKET - TO DO
    function ticket201(data, year, month) {
        render($('#ticket-201'), $('#ticket-201-count'), data, year, month);
    };
    // TICKET - IN PROGRESS
    function ticket202(data, year, month) {
        render($('#ticket-202'), $('#ticket-202-count'), data, year, month);
    };
    // TICKET - TO ANALYZE
    function ticket203(data, year, month) {
        render($('#ticket-203'), $('#ticket-203-count'), data, year, month);
    };
    // TICKET - TO VERIFY
    function ticket204(data, year, month) {
        render($('#ticket-204'), $('#ticket-204-count'), data, year, month);
    };
    // TICKET - WAITING
    function ticket205(data, year, month) {
        render($('#ticket-205'), $('#ticket-205-count'), data, year, month);
    };
    // TICKET - COMPLETED
    function ticket206(data, year, month) {
        render($('#ticket-206'), $('#ticket-206-count'), data, year, month);
    };

    function render(container, count, data, year, month) {
        const $ticketContainer = container;
        const $ticketCountElement = count;

        let html = '';
        let itemCount = 0;

        data.forEach(data => {
            itemCount++;

            let archiveHtml = '';

            if(data.is_archived == 0) {
                archiveHtml = `
                    <a class="dropdown-item fw-medium d-flex text-warning" href="javascript:clickArchiveBtn(${data.id});"> Archive <i class="fa fa-box-archive ms-auto text-end text-warning"></i></a>
                `;
            } else {
                archiveHtml = `
                    <a class="dropdown-item fw-medium d-flex text-success" href="javascript:clickUnarchiveBtn(${data.id});"> Un-archive <i class="fa fa-arrow-rotate-left ms-auto text-end text-success"></i></a>
                `;
            }

            html += `
                <div class="hover-card card" data-ticket-id="${data.id}">
                    <div class="card-body" data-ticket-items="${encodeURIComponent(JSON.stringify(data))}" onclick="showGridTicketEditModal(this, ${data.id})">
                        <h6 class="card-title">${data.subject}</h6>
                        <div class="d-flex gap-2 align-items-end">
                            ${ 
                                data.image
                                ? `<img src="/upload/userprofile/${data.image}" class="rounded-circle" data-bs-toggle="tooltip" data-bs-title="${data.assigned_to}" style="width: 35px; height: 35px;">`
                                : `<span class="rounded-circle employee-avatar-${data.initials_random_color}-initials" data-bs-toggle="tooltip" data-bs-title="${data.assigned_to}">${data.initials}</span>`
                            }
                            <div class="px-1 border border-2 rounded border-secondary">
                                <i class="fa-solid fa-calendar-day"></i>
                                <span class="text-success">${data.due_date ?? ''}</span>
                            </div>
                            <div class="px-1 text-white rounded" style="background-color: ${data.priority_color}; border: 2px solid ${data.priority_color};">
                            <span><i class="fa-solid fa-flag me-1"></i> ${data.priority_name}</span>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown dots">
                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                        <li>${archiveHtml}</li>
                        <li><a class="dropdown-item fw-medium d-flex text-danger" href="javascript:clickDeleteBtn(${data.id});"> Delete <i class="fa fa-trash-can ms-auto text-end text-danger"></i></a></li>
                        </ul>
                    </div>
                </div>
            `;
        });

        $ticketContainer.html(html || '<p class="fst-italic">No record found.</p>');
        $ticketCountElement.text(itemCount);

        // activate tooltip
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    }

    $(document).ready(function() {
        let isAjaxRequestInProgress = false;
        $('.lists').sortable({
            connectWith: '.lists',
            cursor: 'grabbing',
            opacity: 0.6,
            placeholder: 'placeholder',
            update: function(event, ui) {
                if (!isAjaxRequestInProgress) {
                    isAjaxRequestInProgress = true;

                    const ticketId = ui.item.data('ticket-id');
                    let statusId = $(ui.item[0]).closest('.lists').attr('id');
                    statusId = statusId.slice(7);
                    let data = {
                        id: ticketId,
                        status_id: statusId
                    };
                    sweetAlertLoading();
                    // Send an AJAX request to update the ticket status
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: "PATCH",
                        url: "/store/escalation/tickets/update-details",
                        data: JSON.stringify(data),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success: function(response) {
                            Swal.fire({
                                position: 'center',
                                icon: response.status,
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            // Handle success response
                            console.log(response.message);
                            reloadDataTable();
                            isAjaxRequestInProgress = false;
                        },
                        error: function(xhr, status, error) {
                                // Handle error response
                                console.error(error);
                                handleErrorResponse(error);
                                isAjaxRequestInProgress = false;
                            }
                        });
                    }
                }
            }
        ).disableSelection();
    });

    function filterArchive(view, value)
    {
        if(view == 'board') {
            $('#is_archived_list').val(value);
        } else {
            $('#is_archived_board').val(value);
        }
        table_ticket.draw();
        loadCard();
    }

    function archiveSelectedTicket(){
        var checkedIds = Object.keys(selectedIds).filter(function(id) {
            return selectedIds[id];
        });
        let data = {
            selectedIds: checkedIds,
            unselectedIds: unselectedIds
        };
        
        console.log('data',data);
        // doArchiveTicket(data);

        $('#archive_ticket_form_modal').modal('show');
        $('#archive_ticket_form_modal #id').val('');
        $('#title_archive_ticket_id_text').empty();
        $('#title_archive_ticket_id_text').append('Tickets: ');
        for(let i =0; i < data.selectedIds.length; i++ ) {
            $('#title_archive_ticket_id_text').append(data.selectedIds[i]  + ', ')
        }
    }

    function doArchiveTicket(data) {
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/escalation/tickets/archive`,
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
                    $('#archive_ticket_form_modal').modal('hide');
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
            url: `/store/escalation/tickets/unarchive`,
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
</script>
@stop
