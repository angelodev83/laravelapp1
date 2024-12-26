@extends('layouts.master')
@section('content')
<!--start page wrapper -->
	<div class="page-wrapper">
		<div class="page-content">
			<!-- PAGE-HEADER -->
            @include('layouts/pageContentHeader/store')
            <!-- PAGE-HEADER END -->
            
            <div class="row">
                <div class="col">
                    @include('stores/bulletin/dashboard/partials/announcement-alerts')
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <!-- announcement -->
                    @can('menu_store.marketing.announcements.index')
                        @include('stores/bulletin/dashboard/partials/announcement')
                    @endcan
                    <!-- end-announcement -->
                </div>
    
                <!-- News & Events -->
                <div class="col" class="{{ $newsAndEventsCount > 0 ? '' : 'd-none' }}">
                    @include('stores/bulletin/dashboard/partials/news-and-events') 
                </div>
                <!-- end-News & Events -->
            </div>

            <!-- chart -->          
            @include('stores/bulletin/dashboard/partials/completed-sales')
            @include('stores/bulletin/dashboard/partials/patient-feedback')
            <!-- @include('stores/bulletin/dashboard/partials/data-insights') -->
            <!-- end-chart -->
            
            <!-- task-ticket-links -->
            <div class="row">
                @include('stores/bulletin/dashboard/partials/task-reminders')
                @include('stores/bulletin/dashboard/partials/tickets')
                <!-- {{-- @include('stores/bulletin/dashboard/partials/quick-links') --}} -->
                <!-- task reminders starts -->
                @include('stores/bulletin/dashboard/partials/monthly-task-reminders') 
                <!-- end task reminders -->
            </div>
            <!-- end-task-ticket-links -->

            <!-- launchPad-upcomingEvents-Spaces -->
            <div class="row">
                @include('stores/bulletin/dashboard/partials/launch-pad')
                @include('stores/bulletin/dashboard/partials/knowledge-base')
                @include('stores/bulletin/dashboard/partials/upcoming-events')
                <!-- {{-- @include('stores/bulletin/dashboard/partials/spaces') --}} -->
            </div>
            <!-- end-launchPad-upcomingEvents-Spaces -->
        </div>
	</div>
    <!--end page wrapper -->
    @include('sweetalert2/script')
    @include('stores/bulletin/announcements/modal/show')
    {{-- @include('stores/bulletin/tasks/modals/show') --}}
    @include('stores/bulletin/tasks/modals/edit/form')
    @include('stores/bulletin/tasks/modals/assignee')
    @include('stores/bulletin/tasks/modals/watcher')
    @include('components/modal/delete-store-document')
    {{-- @include('stores/escalation/tickets/modals/edit') --}}
    @include('stores/escalation/tickets/modals/edit/form')
    @include('stores/escalation/ticketDocuments/modals/delete')
    @include('stores/escalation/tickets/modals/watcher')
    @include('stores/escalation/tickets/modals/assignee')
    {{-- @include('components/modal/delete-store-document') --}}
    
    @include('stores/marketing/newsAndEvents/modal/show')
@stop

@section('pages_specific_scripts')  

<style>
    #bulletin-upcoming-events {
        height: 470px; /* Adjust as needed */
        overflow: hidden;
        position: relative;
    }

    .bulletin-announcement-text-truncate {
        max-height: 40px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .bulletin-announcement-time-ago {
        min-width: 20px !important;
    }
    .bulletin-task-text-truncate {
        max-height: 35px; /* Set the maximum height */
        overflow: hidden; /* Hide overflow */
        text-overflow: ellipsis; /* Display ellipsis (...) for truncated text */
    }

    #ticket_reminders_dashboard_view_more_btn {
        border-color: #5d63cf; 
        color: #5d63cf;
    }
    #ticket_reminders_dashboard_view_more_btn:hover {
        background-color: #5d63cf;
        color: white;
    }

    .spaces-icon{
        font-size: 30px;
    }

    .upcoming-events-main-text{
        color: #58585a;
    }
    
    .upcoming-events-date{
        background-color: #438f9d !important;
    }

    .upcoming-events-date h1{
        color: #ffffff;
        margin-top: -5px;
        margin-bottom: -10px;
    }

    .upcoming-events-date span{
        color: #ffffff;
        font-size: 20px;
    }

    .launch-pad-logo{
        max-width: 50%;
    }

    .launch-pad-p{
        font-size: 10px;
        margin-top: 5px;
    }

    .announcement-list {
        position: relative;
        height: 420px;
    }

    .news-and-events-list {
        position: relative;
        height: 670px;
    }

    .announcement-list-items {
        position: relative;
        max-height: 250px;
    }

    .hr-announcement-list{
        margin-bottom: 10px;
    }

    .announcement-list-item{
        background-color: #f8f9fa;
        border-radius: 10px;
        margin-bottom: -5px;
        /* margin-top: -5px; */
        -webkit-box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.2);
        box-shadow: 0 0.25rem 0.5rem 0 rgba(0, 0, 0, 0.2);
    }

    .selected-announcement {
        background-color: #293982; /* Light grey background for selected link */
    }

    .selected-announcement-text{
        color: white !important; /* Optional: change text color if needed */
    }

    .unselected-anouncement-p{
        color: #6c757d !important;
    }

    #selected-announcement-content img {
        max-width: 100%; /* Set maximum width to 100% of parent container */
        height: auto; /* Maintain aspect ratio */
        display: block; /* Ensure proper spacing */
        margin: 0 auto; /* Center the image horizontally */
    }

    /* .card-ne{
        height: 100%;
    } */


    /* .card-img-container img {
        width: 100%;
        height: 100%;
        
        object-fit: cover;
    } */

    /* .card-img-ne {
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover; 
    } */

    .card-img-container{
        max-height: 300px;
        overflow: hidden;
    }

    .card-img-container img {
        width: 100%;
        height: 100%;
        
        object-fit: cover;
    }

    .card-body-ne {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    #btn-holder {
        margin-top: auto; 
        margin-bottom: 1rem; 
    }


    .upcoming-events-main-text{
        color: #58585a;
    }
    
    .upcoming-events-date{
        background-color: #438f9d !important;
    }

    .upcoming-events-date h1{
        color: #ffffff;
        margin-top: -5px;
        margin-bottom: -10px;
    }

    .upcoming-events-date span{
        color: #ffffff;
        font-size: 20px;
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

    #taskAttachmentsList .card,
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

    #edit_task_modal .subject_text:hover,
    #edit_ticket_modal .subject_text:hover {
        color: #15a0a3;
    }
    #edit_task_modal .assigned_to:hover,
    #edit_ticket_modal .assigned_to:hover {
        color: #15a0a3;
    }
    #edit_task_modal .edit-icon,
    #edit_ticket_modal .edit-icon {
        color:#a8a7a7;
    }

    #edit_task_modal .edit-icon:hover,
    #edit_ticket_modal .edit-icon:hover {
        color:#15a0a3;
    }
    .patient-container {
        position: relative;
        width: 100%;
    }
    .patient-thumbnail {
        position: relative;
        height: 32rem;
        width: 98%;
    }
</style>

<script>
    let table_announcement;
    let menu_store_id = {{request()->id}};


    let table_ticket;
    let table_ticket_watcher;
    let table_ticket_assignee;
    let table_ticket_watcher_selected_id = null;
    let table_ticket_assignee_selected_id = null;
    let original_edited_subject = '';
    let original_edited_description = '';
    let component_user_id = {{auth()->user()->id}};
    let comment_files;
    let is_modal_loading = true;

    let table_task;
    let table_task_watcher;
    let table_task_watcher_selected_id = null;
    let table_task_assignee;
    let table_task_assignee_selected_id = null;
    let original_edited_task_subject = '';
    let original_edited_task_description = '';
    let comment_task_files;
    let is_task_modal_loading = true;

    let show_edit_modal = 'task';

    function emitCopyText(value = '', _selector)
    {
        $(_selector).html(`<div class="px-4 py-2 m-0 border-0 alert alert-success alert-dismissible fade show">Copied ID <b>${value}</b></div>`);
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }

    function showProcurement(){
        window.location.href = '/store/procurement/'+menu_store_id+'/clinical-orders';
    }

    function showComplianceReg(){
        window.location.href = '/store/compliance/'+menu_store_id+'/audit';
    }

    function showOperations(){
        window.location.href = '/store/operations/'+menu_store_id+'/rts';
    }

    function showEscalations(){
        window.location.href = '/store/escalation/'+menu_store_id+'/tickets';
    }

    // onload
    $(document).ready(function() {
        original_edited_task_subject = '';
        original_edited_task_description = '';
        show_edit_modal = 'task';
        $(".form-control").removeClass("is-invalid");

        // $('.imageuploadify-file-general-class').imageuploadify();
        tinymce.init({
		  selector: 'textarea.tinymce-content',
          height: 310,
          branding: false
		});
        new PerfectScrollbar('#bulletin-announcements-content',{
            suppressScrollX: true
        });
        // new PerfectScrollbar('#bulletin-spaces');
        // new PerfectScrollbar('#bulletin-quick-links');
        // new PerfectScrollbar('#bulletin-tasks-recent');
        new PerfectScrollbar('#commentsList');
        new PerfectScrollbar('#knowledge-base');
        new PerfectScrollbar('#attachmentsList');
        new PerfectScrollbar('#taskCommentsList');
        new PerfectScrollbar('#patient-feedbacks');
        // new PerfectScrollbar('#bulletin-launch-pad');
        new PerfectScrollbar('#taskAttachmentsList');
        new PerfectScrollbar('#bulletin-upcoming-events');
        new PerfectScrollbar('#bulletin-announcements-recent');
        new PerfectScrollbar('#bulletin-tasks-recent-dashboard');
        new PerfectScrollbar('#bulletin-news-and-events-recent');
        new PerfectScrollbar('#bulletin-tickets-recent-dashboard');
        new PerfectScrollbar('#bulletin-monthly-tasks-recent-dashboard');

        $('.announcement-link:first').trigger('click');

        tinymce.init({
            selector: '#edit_ticket_modal #edescription',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline forecolor backcolor | link image | alignleft aligncenter alignright alignjustify lineheight | checklist bullist numlist indent outdent | removeformat',
            plugins: 'textcolor link',
            height: 220,
            branding: false,
            menubar: '',
		});

        // grossProfit();
        // collectedPayments();
        // cogs();
        // chartData();
        // clinicalRevenue();
        completedSalesChart();
        grossSalesChart();
        reviewsChart();

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

        $('#edit_task_modal').on('show.bs.modal', function () {
            $('#watcher_task_modal').css('z-index', parseInt($('#edit_task_modal').css('z-index')) + 2);
            $('#assignee_task_modal').css('z-index', parseInt($('#edit_task_modal').css('z-index')) + 2);
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

        loadWatchers();
        loadAssignees();
        loadTaskWatchers();
        loadTaskAssignees();
    });

    $('#edit_ticket_modal #edue_date').on('changeDate', function(event) {
        updateDetails(event, 'due_date', $(this).val())
    });

    function changeTaskSelectedStatus(event, id)
    {
        event.preventDefault();
        if(show_edit_modal == 'task') {
            console.log("UPDATE STAT TASK", id)
            updateTaskDetails(event, 'status_id', id);
        }
        if(show_edit_modal == 'ticket') {
            console.log("UPDATE STAT TICKET", id)
            updateDetails(event, 'status_id', id);
        }
    }

    function changeTaskSelectedPriorityStatus(event, id)
    {
        event.preventDefault();
        if(show_edit_modal == 'task') {
            console.log("UPDATE PRIO STAT TASK", id)
            updateTaskDetails(event, 'priority_status_id', id);
        }
        if(show_edit_modal == 'ticket') {
            console.log("UPDATE PRIO STAT TICKET", id)
            updateDetails(event, 'priority_status_id', id);
        }
    }

    function reloadDocumentDataTable(id)
    {
        $(`#attachment_card_${id}`).remove();
        $(`#ticket_comment_document_li_${id}`).remove();
        $(`#task_comment_document_li_${id}`).remove();
        reloadDataTable();
    }

    // function chartData(){
    //     let data = {};

    //     data['pharmacy_store_id'] = menu_store_id;
        
    //     $.ajax({
    //         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //         type: "GET",
    //         url: `/store/data-insights/gross-revenue-and-cogs/chart-data`,
    //         contentType: "application/json; charset=utf-8",
    //         dataType: "json",
    //         data: data,
    //         success: function(data) {
    //             console.log(data);
    //             grossProfit(data.gross_profit_data);
    //             cogs(data.cogs_data);
    //             collectedPayments(data.colleted_payment_data);
    //         },
    //         error: function(xhr, status, error) {
    //             // handleErrorResponse(error);
    //             console.error(error);
    //         }
    //     });
    // }

    // function grossProfit(dataArray){
    //     var options = {
    //         series: [{
    //             name: 'Gross Profit MTD',
    //             data: dataArray // Use the dynamic data here
    //         }],
    //         chart: {
    //             type: 'area',
    //             height: 65,
    //             toolbar: { show: false },
    //             zoom: { enabled: false },
    //             dropShadow: {
    //                 enabled: true,
    //                 top: 3,
    //                 left: 14,
    //                 blur: 4,
    //                 opacity: 0.12,
    //                 color: '#f41127',
    //             },
    //             sparkline: { enabled: true }
    //         },
    //         markers: {
    //             size: 0,
    //             colors: ["#f41127"],
    //             strokeColors: "#fff",
    //             strokeWidth: 2,
    //             hover: { size: 7 },
    //         },
    //         plotOptions: {
    //             bar: {
    //                 horizontal: false,
    //                 columnWidth: '45%',
    //                 endingShape: 'rounded'
    //             },
    //         },
    //         dataLabels: { enabled: false },
    //         stroke: {
    //             show: true,
    //             width: 2.4,
    //             curve: 'smooth'
    //         },
    //         colors: ["#f41127"],
    //         xaxis: {
    //             categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    //         },
    //         fill: { opacity: 1 },
    //         tooltip: {
    //             theme: 'dark',
    //             fixed: { enabled: false },
    //             x: { show: false },
    //             y: {
    //                 title: { formatter: () => '' }
    //             },
    //             marker: { show: false }
    //         }
    //     };

    //     var chart = new ApexCharts(document.querySelector("#chart1"), options);
    //     chart.render();
    // }

    // function collectedPayments(dataArray){
    //     // chart 2
    //     var options = {
    //         series: [{
    //             name: 'Collected Paymetns MTD',
    //             data: dataArray
    //         }],
    //         chart: {
    //             type: 'area',
    //             height: 65,
    //             toolbar: {
    //                 show: false
    //             },
    //             zoom: {
    //                 enabled: false
    //             },
    //             dropShadow: {
    //                 enabled: true,
    //                 top: 3,
    //                 left: 14,
    //                 blur: 4,
    //                 opacity: 0.12,
    //                 color: '#8833ff',
    //             },
    //             sparkline: {
    //                 enabled: true
    //             }
    //         },
    //         markers: {
    //             size: 0,
    //             colors: ["#8833ff"],
    //             strokeColors: "#fff",
    //             strokeWidth: 2,
    //             hover: {
    //                 size: 7,
    //             }
    //         },
    //         plotOptions: {
    //             bar: {
    //                 horizontal: false,
    //                 columnWidth: '45%',
    //                 endingShape: 'rounded'
    //             },
    //         },
    //         dataLabels: {
    //             enabled: false
    //         },
    //         stroke: {
    //             show: true,
    //             width: 2.4,
    //             curve: 'smooth'
    //         },
    //         colors: ["#8833ff"],
    //         xaxis: {
    //             categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    //         },
    //         fill: {
    //             opacity: 1
    //         },
    //         tooltip: {
    //             theme: 'dark',
    //             fixed: {
    //                 enabled: false
    //             },
    //             x: {
    //                 show: false
    //             },
    //             y: {
    //                 title: {
    //                     formatter: function (seriesName) {
    //                         return ''
    //                     }
    //                 }
    //             },
    //             marker: {
    //                 show: false
    //             }
    //         }
    //     };
    //     var chart = new ApexCharts(document.querySelector("#chart2"), options);
    //     chart.render();
    // }

    // function cogs(dataArray){
    //     // chart 3
    //     var options = {
    //         series: [{
    //             name: 'COGS MTD',
    //             data: dataArray
    //         }],
    //         chart: {
    //             type: 'area',
    //             height: 65,
    //             toolbar: {
    //                 show: false
    //             },
    //             zoom: {
    //                 enabled: false
    //             },
    //             dropShadow: {
    //                 enabled: true,
    //                 top: 3,
    //                 left: 14,
    //                 blur: 4,
    //                 opacity: 0.12,
    //                 color: '#ffb207',
    //             },
    //             sparkline: {
    //                 enabled: true
    //             }
    //         },
    //         markers: {
    //             size: 0,
    //             colors: ["#ffb207"],
    //             strokeColors: "#fff",
    //             strokeWidth: 2,
    //             hover: {
    //                 size: 7,
    //             }
    //         },
    //         plotOptions: {
    //             bar: {
    //                 horizontal: false,
    //                 columnWidth: '45%',
    //                 endingShape: 'rounded'
    //             },
    //         },
    //         dataLabels: {
    //             enabled: false
    //         },
    //         stroke: {
    //             show: true,
    //             width: 2.4,
    //             curve: 'smooth'
    //         },
    //         colors: ["#ffb207"],
    //         xaxis: {
    //             categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    //         },
    //         fill: {
    //             opacity: 1
    //         },
    //         tooltip: {
    //             theme: 'dark',
    //             fixed: {
    //                 enabled: false
    //             },
    //             x: {
    //                 show: false
    //             },
    //             y: {
    //                 title: {
    //                     formatter: function (seriesName) {
    //                         return ''
    //                     }
    //                 }
    //             },
    //             marker: {
    //                 show: false
    //             }
    //         }
    //     };
    //     var chart = new ApexCharts(document.querySelector("#chart3"), options);
    //     chart.render();
    // }

    function completedSalesChart() {
        let data = {};
        data['pharmacy_store_id'] = menu_store_id;
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: `/store/data-insights/gross-revenue-and-cogs/chart-data`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: data,
            success: function(data) {
                totalRevenueMTD(data.totalRevenueDate, data.totalRevenueTotal);
            },
            error: function(xhr, status, error) {
                // handleErrorResponse(error);
                console.error(error);
            }
        });
    }

    function grossSalesChart() {
        let data = {};
        data['pharmacy_store_id'] = menu_store_id;
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: `/store/data-insights/gross-sales/chart-data`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: data,
            success: function(result) {
                let res = result.data;
                let monthlyPrescriptionVolume = res.monthlyPrescriptionVolume;
                let rxDailyCount = res.rxDailyCount;

                monthlyPrescriptionVolumeChart(monthlyPrescriptionVolume['categories'], monthlyPrescriptionVolume['data']);
                if(rxDailyCount['data'].length > 0) {
                    rxDailyCountChart(rxDailyCount['categories'], rxDailyCount['data']);
                }
            },
            error: function(xhr, status, error) {
                // handleErrorResponse(error);
                console.error(error);
            }
        });
    }
        
    function totalRevenueMTD(dataArray, dataCount) {
        let options = {
            series: [{
                name: 'Total Revenue MTD',
                data: dataCount
            }],
            chart: {
                type: 'area',
                height: 65,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                dropShadow: {
                    enabled: true,
                    top: 3,
                    left: 14,
                    blur: 4,
                    opacity: 0.12,
                    color: '#f41127',
                },
                sparkline: {
                    enabled: true
                }
            },
            markers: {
                size: 0,
                colors: ["#f41127"],
                strokeColors: "#fff",
                strokeWidth: 2,
                hover: {
                    size: 7,
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2.4,
                curve: 'smooth'
            },
            colors: ["#f41127"],
            xaxis: {
                categories: dataArray
            },
            tooltip: {
                theme: 'dark',
                x: {
                    show: true
                },
                fixed: {
                    enabled: true,
                    // position: 'center',
                    offsetY: -47,
                    offsetX: -110  
 
                }
            },
            fill: {
                opacity: 1,
                type: 'gradient',
                colors: '#f41127'
            },
        };
        let chart = new ApexCharts(document.querySelector('#chart1'), options);
        chart.render();
    }

    function monthlyPrescriptionVolumeChart(_categories, _data) {
        let options = {
            series: [{
                name: 'Monthly Prescription Volume',
                data: _data
            }],
            chart: {
                type: 'area',
                height: 65,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                dropShadow: {
                    enabled: true,
                    top: 3,
                    left: 14,
                    blur: 4,
                    opacity: 0.12,
                    color: '#107f93',
                },
                sparkline: {
                    enabled: true
                }
            },
            markers: {
                size: 0,
                colors: ["#107f93"],
                strokeColors: "#fff",
                strokeWidth: 2,
                hover: {
                    size: 7,
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2.4,
                curve: 'smooth'
            },
            colors: ["#107f93"],
            xaxis: {
                categories: _categories
            },
            tooltip: {
                theme: 'dark',
                x: {
                    show: true
                },
                fixed: {
                    enabled: true,
                    // position: 'center',
                    offsetY: -47,
                    offsetX: -110  
 
                }
            },
            fill: {
                opacity: 1,
                type: 'gradient',
                colors: '#107f93'
            },
        };
        let chart = new ApexCharts(document.querySelector('#monthly_prescription_volume_chart'), options);
        chart.render();
    }

    function rxDailyCountChart(_categories, _data) {
        let options = {
            series: [{
                name: 'RX Daily Count',
                data: _data
            }],
            chart: {
                type: 'area',
                height: 65,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                dropShadow: {
                    enabled: true,
                    top: 3,
                    left: 14,
                    blur: 4,
                    opacity: 0.12,
                    color: '#ffb207',
                },
                sparkline: {
                    enabled: true
                }
            },
            markers: {
                size: 0,
                colors: ["#ffb207"],
                strokeColors: "#fff",
                strokeWidth: 2,
                hover: {
                    size: 7,
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2.4,
                curve: 'smooth'
            },
            colors: ["#ffb207"],
            xaxis: {
                categories: _categories
            },
            tooltip: {
                theme: 'dark',
                x: {
                    show: true
                },
                fixed: {
                    enabled: true,
                    // position: 'center',
                    offsetY: -47,
                    offsetX: -110  
 
                }
            },
            fill: {
                opacity: 1,
                type: 'gradient',
                colors: '#ffb207'
            },
        };
        let chart = new ApexCharts(document.querySelector('#rx_daily_count_chart'), options);
        chart.render();
    }

    var colorPalette = ['#17ab5d','#acd91a',  '#fece2c', '#fc7e1e', '#eb354d'];
    var labels = ['Very Satisfied', 'Satisfied', 'Neutral', 'Dissatisfied', 'Very Dissatisfied']
    
    function reviewsChart() {

        var __overallChart = {{ Js::from($patientFeedbacks['item']['overallChart']) }};

        console.log('__overallChart',__overallChart);

        var options = {
            series: __overallChart,
            chart: {
                foreColor: '#9a9797',
                height: 510,
                type: 'donut',
            },
            legend: {
                // position: 'bottom',
                // show: true,
                show: true,
                position: 'bottom',
                markers: {
                    width: 15,
                    height: 15,
                    radius: 0, // 0 will make the marker square
                    customHTML: function() {
                        return '<div style="width: 15px; height: 15px;"></div>';
                    }
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 5
                },
                fontSize: '14px',
                labels: {
                    colors: '#000' // Change legend text color
                }
            },
            plotOptions: {
                pie: {
                    customScale: 0.8,
                    donut: {
                        labels: {
                            show: true,
                            name: {
                                show: false // Hide the name label
                            },
                            value: {
                                show: true,
                                fontSize: '40px', // Increase font size for the value
                                fontWeight: 'bold',
                                color: '#000'
                            },
                            total: {
                                show: true,
                                showAlways: true,
                                label: '',
                                fontSize: '0px', // Hide the "Total" label
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => {
                                        return a + b;
                                    }, 0);
                                },
                                style: {
                                    fontSize: '40px', // Ensure this is big
                                    fontWeight: 'bold',
                                    color: '#000'
                                }
                            }
                        },
                        size: '50%'
                    }
                }
            },
            colors: colorPalette,
            dataLabels: {
                enabled: false
            },
            labels: labels,
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 300
                    },
                    legend: {
                        position: 'bottom'
                    },
                    plotOptions: {
                        pie: {
                            customScale: 1,
                        }
                    },
                }
            }]
        };
        var chart = new ApexCharts(document.querySelector("#reviews"), options);
        chart.render();
    }

    function clinicalRevenue(){
        // chart 4
        var options = {
            series: [{
                name: 'Comments',
                data: [240, 160, 671, 414, 555, 257, 901, 613, 727, 414, 555, 257]
            }],
            chart: {
                type: 'area',
                height: 65,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                dropShadow: {
                    enabled: true,
                    top: 3,
                    left: 14,
                    blur: 4,
                    opacity: 0.12,
                    color: '#29cc39',
                },
                sparkline: {
                    enabled: true
                }
            },
            markers: {
                size: 0,
                colors: ["#29cc39"],
                strokeColors: "#fff",
                strokeWidth: 2,
                hover: {
                    size: 7,
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2.4,
                curve: 'smooth'
            },
            colors: ["#29cc39"],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                theme: 'dark',
                fixed: {
                    enabled: false
                },
                x: {
                    show: false
                },
                y: {
                    title: {
                        formatter: function (seriesName) {
                            return ''
                        }
                    }
                },
                marker: {
                    show: false
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("#chart4"), options);
        chart.render();
    }

    $('.announcement-link').on('click', function() {
        var content = $(this).data('content'); // This fetches the HTML content
        
        // Update the content of the designated area with HTML
        $('#selected-announcement-content').html(content); // This should render as HTML
        // Remove the 'selected-announcement' class from all announcement links
        $('.announcement-list-item').removeClass('selected-announcement');
        $('.selected-announcement-text').removeClass('selected-announcement-text');
        
        // Add the 'selected-announcement' class to the clicked link
        $(this).find('.announcement-list-item').addClass('selected-announcement');
        $(this).find('h6, p, small, i').addClass('selected-announcement-text');

    });

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
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
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

    function reloadDataTable()
    {
        // table_task.ajax.reload(null, false);
        // window.location.reload(true);
    }

    function searchSelect2Api(_select_id, _modal_id, _url) {
        $(`#${_select_id}`).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $(`${_modal_id} .modal-content`),

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

    function showTicketEditModal(id){
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
                    let comment_created_at = currentDateToYMDHMS(item.created_at, 'M d, Y H:i A');

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

                    let comment_description =  item.comment.replace(/\n/g, '<br>');
                    let ticket_comment_section_cols = ``;
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
                    reloadDataTable();                   
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
                        reloadDataTable();                     
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
</script>
@stop
