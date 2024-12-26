@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!-- PAGE-HEADER -->
				@include('layouts/pageContentHeader/store')
				<!-- PAGE-HEADER END -->

                <div class="card" id="pharmacy_staff_schedule_calendar_view">
                    <div class="card-body">
                        <input type="hidden" class="form-control" id="download_date" value="{{$current_date}}">

                        <div class="pt-4 pb-5 d-flex justify-content-between align-items-center">
                            <div class="gap-4 d-flex">
                                <div class="input-group" style="min-width: 250px"> 
                                    <span class="input-group-text">
                                        Date
                                    </span>
                                    <input type="text" class="form-control datepicker" id="current_date" value="{{$current_date}}" placeholder="YYYY-MM-DD">
                                    <span class="input-group-text">
                                        <i class="fa fa-calendar-day"></i>
                                    </span>
                                </div>
                                {{-- <div class="input-group" style="min-width: 250px"> 
                                    <span class="input-group-text" id="icon-from-date">
                                        From
                                        <i class="fa fa-calendar-week ms-2"></i>
                                    </span>
                                    <input type="text" class="form-control datepicker" id="current_week_date_from" value="{{$current_week_date_from}}" placeholder="YYYY-MM-DD">
                                    <span class="input-group-text clear-from-date bg-primary" id="icon-from-date" onclick="clearDateFromFilter()">
                                        <small>Clear</small>
                                    </span>
                                </div>
                                <div class="input-group" style="min-width: 250px"> 
                                    <span class="input-group-text" id="icon-to-date">
                                        To
                                        <i class="fa fa-calendar-week ms-2"></i>
                                    </span>
                                    <input type="text" class="form-control datepicker" id="current_week_date_to" value="{{$current_week_date_to}}" placeholder="YYYY-MM-DD" disabled>
                                </div> --}}
                            </div>
                            <div>
                                @canany(['menu_store.hr.leaves.create'])
                                    <button class="btn btn-secondary" onclick="addStaffLeaveModal()">
                                        Request Time Off
                                    </button>
                                @endcanany
                                @canany(['menu_store.hr.schedules.create'])
                                    <button class="btn btn-info2" onclick="addStaffScheduleModal()">
                                        <i class="fa fa-plus me-2"></i>
                                        Add Manually
                                    </button>
                                    <button class="btn btn-success" onclick="clickUploadBtn()">
                                        <i class="fa fa-upload me-2"></i>
                                        Import Excel
                                    </button>
                                @endcanany
                                @canany(['menu_store.hr.schedules.export'])
                                    <button class="btn btn-primary" onclick="clickExportBtn()">
                                        <i class="fa fa-download me-2"></i>
                                        Export Month
                                    </button>
                                @endcanany
                            </div>
                        </div>
                        <div id="schedule_calendar" style="min-height: 500px; min-width: 500px; width: 100% !important;"></div>
                    </div>
                </div>
			</div>
			@include('sweetalert2/script')
            @include('components/modal/import-single-excel')
            @include('admin/schedules/pharmacyStaff/modals/add')
            @include('admin/schedules/pharmacyStaff/modals/edit')
            @include('stores/humanResource/leaves/modals/add')
            @include('stores/humanResource/leaves/modals/show')
		</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts') 

<style>
    .sorting {
        min-width: 80px;
    }
    .sorting_disabled {
        min-width: 130px;
    } 

    .dt-schedule-highlight-row td {
        background-color: #15a0a3 !important;
        color: white !important;
    }

    /* Custom scrollbar styles */
    ::-webkit-scrollbar {
        width: 6px; /* Width of vertical scrollbar */
        height: 6px; /* Height of horizontal scrollbar */
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1; /* Background of the scrollbar track */
    }

    ::-webkit-scrollbar-thumb {
        background: #888; /* Color of the scrollbar thumb */
        border-radius: 10px; /* Rounded corners for the scrollbar thumb */
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #555; /* Color of the scrollbar thumb when hovered */
    }

    .rectangle {
        height: 37px; /* Set the height of the rectangle */
        background-color: #ececec; /* Set the background color of the rectangle */
        border: solid 1px #A8A7A7; /* Set the background color of the rectangle */
        display: flex;
        /* align-items: center;
        justify-content: center; */
        vertical-align: center;
        color: black; /* Text color */
        font-size: 14px; /* Text size */
        border-radius: 5px;
        padding-top: 6px;
    }

    .attachments-container {
        position: relative;
        width: 100px;
        height: 100px;
        overflow: hidden;
        border: 1px solid #5ba6c0;
        border-radius: 5px;
        padding: 0px !important;
    }

    .attachments-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .attachments-text-overlay {
        position: absolute;
        top: 50%;
        left: 40%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 10px;
        background-color: rgb(0 0 0 / 51%);
        padding: 10px;
        border-radius: 5px;
        word-wrap: break-word; word-break: break-all;
        width: 92px;
    }
</style>

<script>
    let menu_store_id = {{request()->id}};
    let is_offshore = {{request()->is_offshore}};
    let table_employee;
    let table_schedule;
    let selected_employee_id;
    let selected_schedule_id;
    let current_date = {{$current_date}};
    let $calendar;

    const _urlSearchParams = new URLSearchParams(window.location.search);
    const __urlGlobalLeaveID = _urlSearchParams.get('leave-id');

    $('#show_edit_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
        $('#import_single_any_modal #upload_single_any_file').remove();
        $('#import_single_any_modal .imageuploadify').remove(); 
    });

    $('#add_staff_leave_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
    });

    $('#show_staff_leave_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
    });

    $('#add_staff_schedule_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
    });

    $('#import_single_any_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
        $('#import_single_any_modal #upload_single_any_file').remove();
        $('#import_single_any_modal .imageuploadify').remove(); 
    });

    $('.imageuploadify-file-general-class').click(function () {
        $('.imageuploadify-container').remove();
    }); 

    $('#current_week_date_from2').change(function(){ 
        var date_from = $('#current_week_date_from2').val();
        let date_from_monday = getWeekDateByDateAndIntegerDay(date_from, 1);
        let date_to_sunday = getWeekDateByDateAndIntegerDay(date_from, 7);

        date_from_monday = formatDateToYYYYMMDD(date_from_monday);
        date_to_sunday = formatDateToYYYYMMDD(date_to_sunday);

        $('#current_week_date_from2').val(date_from_monday);
        $('#current_week_date_to2').val(date_to_sunday);

        table_employee.ajax.reload(null, false);
    });

    $('#add_staff_leave_modal #date_from').change(function(){ 
        computeAddDays();
    });

    $('#add_staff_leave_modal #date_to').change(function(){ 
        computeAddDays();
    });

    $('#current_date').change(function(){ 
        const currentDate = $('#current_date').val();
        if(current_date != currentDate) {
            current_date = currentDate;

            var date = new Date(current_date);
            var year = date.getFullYear();
            var month = date.getMonth();
            var day = date.getDate();

            $('#download_date').val(currentDate);

            // loadEvents(currentDate);
            $calendar.gotoDate(new Date(year, month, day));
        }
    });

    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        is_offshore = {{request()->is_offshore}};
        current_date = {{$current_date}};

        $('#current_week_date_from').datepicker({
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

        $('#add_staff_leave_modal #date_from').datepicker({
            format: "mm/dd/yyyy",
            todayHighlight: true,
            uiLibrary: 'bootstrap5',
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true
        });

        $('#add_staff_leave_modal #date_to').datepicker({
            format: "mm/dd/yyyy",
            todayHighlight: true,
            uiLibrary: 'bootstrap5',
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true
        });

        $('#current_date').datepicker({
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

        // loadSchedules();
        // loadPharmacyStaffSchedules();
        loadEvents();

        setTimeout(() => {
            if(__urlGlobalLeaveID) {
                showStaffLeaveModal(__urlGlobalLeaveID)
            }
        }, 3000);
    });

    function getWeekDateByDateAndIntegerDay(date, day_int = 1) {
        // Ensure 'date' is a JavaScript Date object
        date = new Date(date);

        // Get the day of the week (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
        const dayOfWeek = date.getDay();

        // Calculate the difference (in days) between the current day and Monday
        const diffDays = dayOfWeek - day_int; // '1' to account for Monday being the start of the week

        // Calculate the date of Monday by subtracting the difference
        const weekdayDate = new Date(date);
        weekdayDate.setDate(weekdayDate.getDate() - diffDays);

        return weekdayDate;
    }

    function formatDateToYYYYMMDD(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Adding 1 because getMonth() returns zero-based index
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function clearDateFromFilter()
    {
        window.location.reload(true);
    }

    function loadSchedules()
    {
        const employee_table = $('#employee_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBtp',
            buttons: [
                @can('menu_store.hr.schedules.import')
                    {
                        text: '<i class="fa fa-upload me-2"></i>Import Excel', 
                        className: 'btn btn-success btn-sm', 
                        action: function ( e, dt, node, config ) {
                            clickUploadBtn();
                        }
                    },
                @endcan
            ],
            pageLength: 50,
            searching: true,
            ajax: {
                url: "/store/human-resource/schedules/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                    data.order[0]['column'] = 0;
                    data.date_from = $('#current_week_date_from').val();
                    data.date_to = $('#current_week_date_to').val();
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'firstname', name: 'firstname', title: 'Fullname', render: function(data, type, row) {
                    return '<div>' + row.avatar + '</div>';
                } },
                { data: 'schedule_formatted_date_range', name: 'schedule_formatted_date_range', title: 'Week Date Range', orderable: false, searchable: false},
                { data: 'schedule_monday_formatted_time_range', name: 'schedule_monday_formatted_time_range', title: 'Monday', orderable: false, searchable: false},
                { data: 'schedule_tuesday_formatted_time_range', name: 'schedule_tuesday_formatted_time_range', title: 'Tuesday', orderable: false, searchable: false},
                { data: 'schedule_wednesday_formatted_time_range', name: 'schedule_wednesday_formatted_time_range', title: 'Wednesday', orderable: false, searchable: false},
                { data: 'schedule_thursday_formatted_time_range', name: 'schedule_thursday_formatted_time_range', title: 'Thursday', orderable: false, searchable: false},
                { data: 'schedule_friday_formatted_time_range', name: 'schedule_friday_formatted_time_range', title: 'Friday', orderable: false, searchable: false},
                { data: 'schedule_saturday_formatted_time_range', name: 'schedule_saturday_formatted_time_range', title: 'Saturday', orderable: false, searchable: false},
                { data: 'schedule_sunday_formatted_time_range', name: 'schedule_sunday_formatted_time_range', title: 'Sunday', orderable: false, searchable: false},
                { data: 'actions', name: 'actions', title: 'Actions', orderable: false, searchable: false},
               
            ],
            initComplete: function( settings, json ) {
                selected_len = employee_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_employee = employee_table;
        
        // Placement controls for Table filters and buttons
		table_employee.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(employee_table.search());
		$('#search_input').keyup(function(){ table_employee.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_employee.page.len($(this).val()).draw() });

        $('.dataTables_scrollBody').scroll(function (){
            var cols = 2 // how many columns should be fixed
            var container = $(this)
            var offset = container.scrollLeft()
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

    function reloadDataTable(data)
    {
        // table_employee.ajax.reload(null, false);
        loadEvents();
        $(".imageuploadify-container").remove();
        $('#import_single_excel_modal').modal('hide');
        $('#import_single_any_modal').modal('hide');
        $('#upload_bulk_fst_shipping_label_modal').modal('hide');
        $('#update_for_shipping_today').modal('hide');
        sweetAlert2(data.status, data.message);
    }

    function clickUploadBtn() {
        $('.imageuploadify').remove();

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

    function clickExportBtn() {
        const date = $('#download_date').val();

        window.open(`/store/human-resource/${menu_store_id}/schedules/export-onshore/${date}/`, '_blank');
    }

    function saveImportSingleExcel()
    {
        proceedImportSingleExcel('/store/human-resource/schedules/import')
    }

    function showEditPharmacyStaffScheduleModal(employee_id, schedule_id = null)
    {
        callAPIPharmacyStaffSchedule(employee_id, schedule_id, 'list');
    }

    function loadPharmacyStaffSchedules()
    {
        if(table_schedule) {
            table_schedule.clear().draw();
        }
        const schedule_table = $('#edit_pharmacy_staff_schedule_modal #schedule_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBtp',
            order: [[0, 'desc']],
            buttons: [
            ],
            pageLength: 10,
            searching: true,
            ajax: {
                url: "/store/human-resource/schedules/staff/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                    data.employee_id = selected_employee_id;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'date_from', name: 'date_from', title: 'Date From', render: function(data, type, row) {
                    return `${row.schedule_date_from_formatted}`;
                } },
                { data: 'date_to', name: 'date_to', title: 'Date To', render: function(data, type, row) {
                    return `${row.schedule_date_to_formatted}`;
                } },
                { data: 'schedule_monday_formatted_time_range', name: 'schedule_monday_formatted_time_range', title: 'Monday', orderable: false, searchable: false},
                { data: 'schedule_tuesday_formatted_time_range', name: 'schedule_tuesday_formatted_time_range', title: 'Tuesday', orderable: false, searchable: false},
                { data: 'schedule_wednesday_formatted_time_range', name: 'schedule_wednesday_formatted_time_range', title: 'Wednesday', orderable: false, searchable: false},
                { data: 'schedule_thursday_formatted_time_range', name: 'schedule_thursday_formatted_time_range', title: 'Thursday', orderable: false, searchable: false},
                { data: 'schedule_friday_formatted_time_range', name: 'schedule_friday_formatted_time_range', title: 'Friday', orderable: false, searchable: false},
                { data: 'schedule_saturday_formatted_time_range', name: 'schedule_saturday_formatted_time_range', title: 'Saturday', orderable: false, searchable: false},
                { data: 'schedule_sunday_formatted_time_range', name: 'schedule_sunday_formatted_time_range', title: 'Sunday', orderable: false, searchable: false},
                // { data: 'actions', name: 'actions', title: 'Actions', orderable: false, searchable: false},
               
            ],
            'fnCreatedRow': function (nRow, aData, iDataIndex) {
                $(nRow).attr('id', 'dt-schedule-row-' + aData.id); // or whatever you choose to set as the id
                $(nRow).attr('class', 'dt-schedule-row'); // or whatever you choose to set as the id
                if(aData.id == selected_schedule_id) {
                    $(nRow).attr('class', 'dt-schedule-highlight-row');
                }
            },
            initComplete: function( settings, json ) {
                selected_len = schedule_table.page.len();
				$('#length_schedule_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_schedule_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_schedule_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_schedule_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_schedule_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false})); 
            }
        });

        table_schedule = schedule_table;
        
        // Placement controls for Table filters and buttons
		table_schedule.buttons().container().appendTo( '.dt-card-header-schedule' );
	    $('#length_schedule_change').change( function() { table_schedule.page.len($(this).val()).draw() });

        $('.dataTables_scrollBody').scroll(function (){
            var cols = 2 // how many columns should be fixed
            var container = $(this)
            var offset = container.scrollLeft()
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

    function loadEvents()
    {
        let data = {
            is_offshore: is_offshore
        };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/admin/pharmacy-staff/schedules/events`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(data),
            success: function(res) {
                // console.log('--------res',res);
                // let schedules = res.data.monthly;
                let monthlyv2 = res.data.monthlyv2;
                let dailies = res.data.daily;
                let weekly = res.data.weekly;
                let list = res.data.list;
                let leaves = res.data.leaves;
                // console.log('--------schedules',schedules, typeof schedules, schedules.length, dailies,leaves);
                // let events = [];
                let events = monthlyv2;

                // for (let s in schedules) {
                //     const employees = schedules[s];

                //     for(let e in employees) {
                //         const details = employees[e];

                //         for(let d in details) {
                //             const detail = details[d];
                            
                //             events.push({
                //                 title: detail.title,
                //                 start: detail.start,
                //                 end: detail.end,
                //                 // backgroundColor: detail.backgroundColor,
                //                 // borderColor: detail.borderColor,
                //                 textColor: detail.textColor,
                //                 // borderRadius: '15px'
                //                 classNames: detail.classNames,
                //                 extendedProps: { 
                //                     description: detail.hover_title,
                //                     schedule_id: detail.schedule_id,
                //                     daily_id: detail.daily_id,
                //                     is_present: detail.is_present,
                //                 },
                //             });
                //         }
                //     }
                // }

                for (let l in leaves) {
                    const employees = leaves[l];

                    for(let e in employees) {
                        const details = employees[e];

                        for(let d in details) {
                            const detail = details[d];

                            // console.log("leave",detail);
                            
                            events.push({
                                title: detail.title,
                                start: detail.start,
                                end: detail.end,
                                // backgroundColor: detail.backgroundColor,
                                // borderColor: detail.borderColor,
                                textColor: detail.textColor,
                                classNames: detail.classNames,
                                extendedProps: { 
                                    description: detail.hover_title,
                                    leave_id: detail.leave_id,
                                    schedule_id: detail.schedule_id,
                                    daily_id: detail.daily_id,
                                    is_present: detail.is_present,
                                },
                            });
                        }
                    }
                }

                // console.log("____________________EVENTS", events)

                createCalendarEvents('schedule_calendar', current_date, events, weekly, dailies, list);
            },
            error: function(xhr, status, error) {
                handleErrorResponse(error);
                console.error(error);
            }
        });
    }

    function createCalendarEvents(calendarElementId, initialDate, monthlyEvents, weeklyEvents, dailyEvents, listEvents) {
        var calendarEl = document.getElementById(calendarElementId);

        // Check if the calendar element exists
        if (!calendarEl) {
            console.error(`Element with ID '${calendarElementId}' not found.`);
            return;
        }

        var currentView = 'dayGridMonth'; // Default view

        $calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: function(fetchInfo, successCallback, failureCallback) {
                if (currentView === 'dayGridMonth') {
                    successCallback(monthlyEvents);
                } else if (currentView === 'timeGridWeek') {
                    successCallback(weeklyEvents);
                } else if (currentView === 'timeGridDay') {
                    successCallback(dailyEvents);
                }else if (currentView === 'listWeek') {
                    successCallback(listEvents);
                }
            },
            eventContent: function(arg) {
                let event = arg.event;
                let customHtml = `
                    <div class="fc-event-custom" title="${event.extendedProps.description}">
                        <div class="fc-event-title">${event.title}</div>
                    </div>
                `;
                return { html: customHtml };
            },
            eventClick: function(info) {
                const is_present = info.event.extendedProps.is_present;
                if(is_present == 0) {
                    const leave_id = info.event.extendedProps.leave_id;
                    showStaffLeaveModal(leave_id);
                }
            },
            // dateClick: function(info) {
            //     // Filter events by the clicked date
            //     filteredEvents = allEvents.filter(event => {
            //         // For all-day events, compare only the start date
            //         if (!event.start.includes('T')) {
            //             return event.start === info.dateStr;
            //         }
            //         // For timed events, compare the start date and time
            //         return event.start.startsWith(info.dateStr);
            //     });
            //     $calendar.refetchEvents();
            // },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            datesSet: function(dateInfo) {
                currentView = dateInfo.view.type; // Update the current view type
                $calendar.refetchEvents();
            }
        });

        $calendar.render(); // Render the calendar
    }

    document.addEventListener('DOMContentLoaded', function() {
        var events = [];
        createCalendarEvents('schedule_calendar', current_date, events, events, events, events);

        // Override the default prev button click
        var prevButton = document.querySelector('.fc-prev-button');
        prevButton.addEventListener('click', function() {
            // Get the current date
            var currentDate = $calendar.getDate();

            let year = currentDate.getFullYear();
            let monthNumber = currentDate.getMonth() + 1;
            let formattedMonth = monthNumber.toString().padStart(2, '0');

            const formattedDate = `${year}-${formattedMonth}-01`;
            $('#download_date').val(formattedDate);
        });

        var nextButton = document.querySelector('.fc-next-button');
        nextButton.addEventListener('click', function() {
            // Get the current date
            var currentDate = $calendar.getDate();

            let year = currentDate.getFullYear();
            let monthNumber = currentDate.getMonth() + 1;
            let formattedMonth = monthNumber.toString().padStart(2, '0');

            const formattedDate = `${year}-${formattedMonth}-01`;
            $('#download_date').val(formattedDate);
        });


    });

    function confirmDeleteScheduleDaily(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: `You will not be able to recover it.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it'
        }).then((result) => {
            if (result.isConfirmed) {
                doDeleteScheduleDaily(id);
            }
        });
    }

    function doDeleteScheduleDaily(id) {

        let data = {
            id: id
        };

        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/human-resource/schedules/delete`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {
                //success
                if(res.status == 'success') {
                    Swal.fire({
                        position: 'center',
                        icon: res.status,
                        title: res.message,
                        showConfirmButton: false,
                        timer: 4000
                    });
                } else {
                    Swal.fire({
                        position: 'center',
                        icon: res.status,
                        title: res.message,
                        showConfirmButton: false
                    });
                }
                
                loadEvents();
            },error: function(res) {
                console.log(res);
                handleErrorResponse(res);
            }
        });
    }
    
</script>  
@stop
