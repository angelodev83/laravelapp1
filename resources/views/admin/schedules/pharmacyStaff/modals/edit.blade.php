<div class="modal" id="edit_pharmacy_staff_schedule_modal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modal-title-pharmacy-staff-schedule">Employee Form</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            
                    <!--start row-->
                    <div class="row">
                        <form action="" method="POST" id="#pharmacy_staff_schedule_edit_form">

                            <div class="col">

                                <!--start -->
                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <!-- list view start -->
                                        <div class="card" id="pharmacy_staff_schedule_list_view">
                                            <div class="p-3 mb-0 card-header dt-card-header-schedule">
                                                <button type="button" class="btn btn-primary me-1 schedule-icon-btn schedule-list-btn" title="List View" onclick="showPharmacyStaffScheduleListView(event)">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                                <button type="button" class="btn btn-secondary schedule-icon-btn schedule-calendar-btn" title="Calendar View" onclick="showPharmacyStaffScheduleCalendarView(event)">
                                                    <i class="fa fa-calendar-days"></i>
                                                </button>
                                                <select name='length_schedule_change' id='length_schedule_change' class="table_length_change form-select">
                                                </select>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table id="schedule_table" class="table row-border table-hover" style="width:100%">
                                                        <thead></thead>
                                                        <tbody>                                   
                                                        </tbody>
                                                        <tfooter></tfooter>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- list view end -->

                                        <!-- calendar view start -->
                                        <div class="card" id="pharmacy_staff_schedule_calendar_view" style="display:none;">
                                            <div class="p-3 mb-0 card-header">
                                                <button type="button" class="btn btn-primary me-1 schedule-icon-btn schedule-list-btn" title="List View" onclick="showPharmacyStaffScheduleListView(event)">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                                <button type="button" class="btn btn-secondary schedule-icon-btn schedule-calendar-btn" title="Calendar View" onclick="showPharmacyStaffScheduleCalendarView(event)">
                                                    <i class="fa fa-calendar-days"></i>
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                {{-- <div class="table-responsive"> --}}
                                                    <div id="schedule_calendar" style="min-height: 500px; min-width: 500px; width: 100% !important;">
                                                        
                                                    </div>
                                                {{-- </div> --}}
                                            </div>
                                        </div>
                                        <!-- calendar view end -->


                                    </div>
                                    
                                    <div class="col-md-5">
                                        <div class="card">
                                            <div class="p-3 pb-1 mb-0 card-header" style="background-color: #15a0a3 !important;">
                                                <h6 id="date_range" style="color: white !important;"></h6>
                                            </div>
                                            <div class="card-body">   
                                                <input type="text" id="schedule_id" hidden>     
                                                <div class="mb-2 row">
                                                    <div class="col-6 col-lg-6">
                                                        <label for="monday_time_from" class="form-label">Monday From</label>
                                                        <input type="text" class="form-control" id="monday_time_from" placeholder="HH:MM">
                                                    </div>
                                                    <div class="col-6 col-lg-6">
                                                        <label for="monday_time_to" class="form-label">To</label>
                                                        <input type="text" class="form-control" id="monday_time_to" placeholder="HH:MM">
                                                        <span class="text-primary" id="monday_time_to_note">
                                                            {{-- <small>Will cross next day</small> --}}
                                                        </span>
                                                    </div>
                                                </div>
                
                                                <div class="mb-2 row">
                                                    <div class="col-6 col-lg-6">
                                                        <label for="tuesday_time_from" class="form-label">Tuesday From</label>
                                                        <input type="text" class="form-control" id="tuesday_time_from" placeholder="HH:MM">
                                                    </div>
                                                    <div class="col-6 col-lg-6">
                                                        <label for="tuesday_time_to" class="form-label">To</label>
                                                        <input type="text" class="form-control" id="tuesday_time_to" placeholder="HH:MM">
                                                        <span class="text-primary" id="tuesday_time_to_note"></span>
                                                    </div>
                                                </div>
                
                                                <div class="mb-2 row">
                                                    <div class="col-6 col-lg-6">
                                                        <label for="wednesday_time_from" class="form-label">Wednesday From</label>
                                                        <input type="text" class="form-control" id="wednesday_time_from" placeholder="HH:MM">
                                                    </div>
                                                    <div class="col-6 col-lg-6">
                                                        <label for="wednesday_time_to" class="form-label">To</label>
                                                        <input type="text" class="form-control" id="wednesday_time_to" placeholder="HH:MM">
                                                        <span class="text-primary" id="wednesday_time_to_note"></span>
                                                    </div>
                                                </div>
                
                                                <div class="mb-2 row">
                                                    <div class="col-6 col-lg-6">
                                                        <label for="thursday_time_from" class="form-label">Thursday From</label>
                                                        <input type="text" class="form-control" id="thursday_time_from" placeholder="HH:MM">
                                                    </div>
                                                    <div class="col-6 col-lg-6">
                                                        <label for="thursday_time_to" class="form-label">To</label>
                                                        <input type="text" class="form-control" id="thursday_time_to" placeholder="HH:MM">
                                                        <span class="text-primary" id="thursday_time_to_note"></span>
                                                    </div>
                                                </div>
                
                                                <div class="mb-2 row">
                                                    <div class="col-6 col-lg-6">
                                                        <label for="friday_time_from" class="form-label">Friday From</label>
                                                        <input type="text" class="form-control" id="friday_time_from" placeholder="HH:MM">
                                                    </div>
                                                    <div class="col-6 col-lg-6">
                                                        <label for="friday_time_to" class="form-label">To</label>
                                                        <input type="text" class="form-control" id="friday_time_to" placeholder="HH:MM">
                                                        <span class="text-primary" id="friday_time_to_note"></span>
                                                    </div>
                                                </div>
                
                                                <div class="mb-2 row">
                                                    <div class="col-6 col-lg-6">
                                                        <label for="saturday_time_from" class="form-label">Saturday From</label>
                                                        <input type="text" class="form-control" id="saturday_time_from" placeholder="HH:MM">
                                                    </div>
                                                    <div class="col-6 col-lg-6">
                                                        <label for="saturday_time_to" class="form-label">To</label>
                                                        <input type="text" class="form-control" id="saturday_time_to" placeholder="HH:MM">
                                                        <span class="text-primary" id="saturday_time_to_note"></span>
                                                    </div>
                                                </div>
                
                                                <div class="mb-2 row">
                                                    <div class="col-6 col-lg-6">
                                                        <label for="sunday_time_from" class="form-label">Sunday From</label>
                                                        <input type="text" class="form-control" id="sunday_time_from" placeholder="HH:MM">
                                                    </div>
                                                    <div class="col-6 col-lg-6">
                                                        <label for="sunday_time_to" class="form-label">To</label>
                                                        <input type="text" class="form-control" id="sunday_time_to" placeholder="HH:MM">
                                                        <span class="text-primary" id="sunday_time_to_note"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <div class="card-footer">
                                                <button type="button" class="btn btn-primary float-end" id="save_btn" onclick="saveUpdatePharmacyStaffScheduleForm()">
                                                    <i class="fa fa-save me-2"></i>
                                                    Save
                                                </button>
                                            </div> --}}
                                        </div>
        
                                    </div>
                                </div>
                                <!--end-->

                            </div>

                        </form>
                    </div>
                    <!--end row-->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save_btn" onclick="saveUpdatePharmacyStaffScheduleForm()"><i class="fa fa-save me-2"></i>
                    Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    function saveUpdatePharmacyStaffScheduleForm(){
        let data = {
            pharmacy_staff_schedule_id: $('#schedule_id').val(),
            dailies: [
                { week_day: 1, time_from: $('#monday_time_from').val(), time_to: $('#monday_time_to').val() },
                { week_day: 2, time_from: $('#tuesday_time_from').val(), time_to: $('#tuesday_time_to').val() },
                { week_day: 3, time_from: $('#wednesday_time_from').val(), time_to: $('#wednesday_time_to').val() },
                { week_day: 4, time_from: $('#thursday_time_from').val(), time_to: $('#thursday_time_to').val() },
                { week_day: 5, time_from: $('#friday_time_from').val(), time_to: $('#friday_time_to').val() },
                { week_day: 6, time_from: $('#saturday_time_from').val(), time_to: $('#saturday_time_to').val() },
                { week_day: 7, time_from: $('#sunday_time_from').val(), time_to: $('#sunday_time_to').val() }
            ],
        };

        console.log('fire after',data);
        
        sweetAlertLoading();

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/human-resource/schedules/edit`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                $("#save_btn").val('Save');
                $("#save_btn").removeAttr('disabled');
                
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    reloadDataTable(data);
                    sweetAlert2('success', 'Record has been saved.');
                    $('#edit_pharmacy_staff_schedule_modal').modal('hide');
                }
            },error: function(msg) {

                $("#save_btn").val('Save');
                $("#save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                handleErrorResponse(msg);
                console.log(msg.responseText);
            }

        });
    }

    function showPharmacyStaffScheduleListView(event)
    {
        event.preventDefault();
        $('#pharmacy_staff_schedule_list_view').show();
        $('#pharmacy_staff_schedule_calendar_view').hide();

        $('.schedule-icon-btn').removeClass('btn-primary');
        $('.schedule-icon-btn').addClass('btn-secondary');

        $('.schedule-list-btn').removeClass('btn-secondary');
        $('.schedule-list-btn').addClass('btn-primary');
    }

    function showPharmacyStaffScheduleCalendarView(event)
    {
        event.preventDefault();
        $('#pharmacy_staff_schedule_list_view').hide();
        $('#pharmacy_staff_schedule_calendar_view').show();

        $('.schedule-icon-btn').removeClass('btn-primary');
        $('.schedule-icon-btn').addClass('btn-secondary');

        $('.schedule-calendar-btn').removeClass('btn-secondary');
        $('.schedule-calendar-btn').addClass('btn-primary');
    }

    function callAPIPharmacyStaffSchedule(employee_id, schedule_id = null, type = 'list')
    {
        let data = {
            employee_id: employee_id,
            schedule_id: schedule_id,
            pharmacy_store_id: menu_store_id
        };
        selected_employee_id = employee_id;
        selected_schedule_id = schedule_id;
        table_schedule.ajax.reload(null, false);

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/human-resource/schedules/staff`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {

                let data = res.data;

                let employee_name = data.employeeAvatar;
                let currentWeek = data.currentWeek;
                let currentScheduleDateRange = data.currentScheduleDateRange;
                let currentSchedule = data.currentSchedule;
                let currentDateFrom = currentSchedule.date_from;

                let events = data.events;

                if(currentWeek) {
                    // MONDAY -----------------------
                    let monday_time_from = currentWeek.scheduleMonday['formatted_time_from'] ? currentWeek.scheduleMonday['formatted_time_from'] : '';
                    let monday_time_to = currentWeek.scheduleMonday['formatted_time_to'] ? currentWeek.scheduleMonday['formatted_time_to'] : '';
                    $('#edit_pharmacy_staff_schedule_modal #monday_time_to_note').empty();
                    if(monday_time_from >= monday_time_to && (monday_time_from && monday_time_to)) {
                        $('#edit_pharmacy_staff_schedule_modal #monday_time_to_note').html(`<small><i>* Cross to next day</i></small>`);
                    }
                    $('#edit_pharmacy_staff_schedule_modal #monday_time_from').val(monday_time_from);
                    $('#edit_pharmacy_staff_schedule_modal #monday_time_to').val(monday_time_to);

                    // TUESDAY -----------------------
                    let tuesday_time_from = currentWeek.scheduleTuesday['formatted_time_from'] ? currentWeek.scheduleTuesday['formatted_time_from'] : '';
                    let tuesday_time_to = currentWeek.scheduleTuesday['formatted_time_to'] ? currentWeek.scheduleTuesday['formatted_time_to'] : '';
                    $('#edit_pharmacy_staff_schedule_modal #tuesday_time_to_note').empty();
                    if(tuesday_time_from >= tuesday_time_to && (tuesday_time_from && tuesday_time_to)) {
                        $('#edit_pharmacy_staff_schedule_modal #tuesday_time_to_note').html(`<small><i>* Cross to next day</i></small>`);
                    }
                    $('#edit_pharmacy_staff_schedule_modal #tuesday_time_from').val(tuesday_time_from);
                    $('#edit_pharmacy_staff_schedule_modal #tuesday_time_to').val(tuesday_time_to);

                    // WEDNESDAY -----------------------
                    let wednesday_time_from = currentWeek.scheduleWednesday['formatted_time_from'] ? currentWeek.scheduleWednesday['formatted_time_from'] : '';
                    let wednesday_time_to = currentWeek.scheduleWednesday['formatted_time_to'] ? currentWeek.scheduleWednesday['formatted_time_to'] : '';
                    $('#edit_pharmacy_staff_schedule_modal #wednesday_time_to_note').empty();
                    if(wednesday_time_from >= wednesday_time_to && (wednesday_time_from && wednesday_time_to)) {
                        $('#edit_pharmacy_staff_schedule_modal #wednesday_time_to_note').html(`<small><i>* Cross to next day</i></small>`);
                    }
                    $('#edit_pharmacy_staff_schedule_modal #wednesday_time_from').val(wednesday_time_from);
                    $('#edit_pharmacy_staff_schedule_modal #wednesday_time_to').val(wednesday_time_to);

                    // THURSDAY -----------------------
                    let thursday_time_from = currentWeek.scheduleThursday['formatted_time_from'] ? currentWeek.scheduleThursday['formatted_time_from'] : '';
                    let thursday_time_to = currentWeek.scheduleThursday['formatted_time_to'] ? currentWeek.scheduleThursday['formatted_time_to'] : '';
                    $('#edit_pharmacy_staff_schedule_modal #thursday_time_to_note').empty();
                    if(thursday_time_from >= thursday_time_to && (thursday_time_from && thursday_time_to)) {
                        $('#edit_pharmacy_staff_schedule_modal #thursday_time_to_note').html(`<small><i>* Cross to next day</i></small>`);
                    }
                    $('#edit_pharmacy_staff_schedule_modal #thursday_time_from').val(thursday_time_from);
                    $('#edit_pharmacy_staff_schedule_modal #thursday_time_to').val(thursday_time_to);

                    // FRIDAY -----------------------
                    let friday_time_from = currentWeek.scheduleFriday['formatted_time_from'] ? currentWeek.scheduleFriday['formatted_time_from'] : '';
                    let friday_time_to = currentWeek.scheduleFriday['formatted_time_to'] ? currentWeek.scheduleFriday['formatted_time_to'] : '';
                    $('#edit_pharmacy_staff_schedule_modal #friday_time_to_note').empty();
                    if(friday_time_from >= friday_time_to && (friday_time_from && friday_time_to)) {
                        $('#edit_pharmacy_staff_schedule_modal #friday_time_to_note').html(`<small><i>* Cross to next day</i></small>`);
                    }
                    $('#edit_pharmacy_staff_schedule_modal #friday_time_from').val(friday_time_from);
                    $('#edit_pharmacy_staff_schedule_modal #friday_time_to').val(friday_time_to);

                    // SATURDAY -----------------------
                    let saturday_time_from = currentWeek.scheduleSaturday['formatted_time_from'] ? currentWeek.scheduleSaturday['formatted_time_from'] : '';
                    let saturday_time_to = currentWeek.scheduleSaturday['formatted_time_to'] ? currentWeek.scheduleSaturday['formatted_time_to'] : '';
                    $('#edit_pharmacy_staff_schedule_modal #saturday_time_to_note').empty();
                    if(saturday_time_from >= saturday_time_to && (saturday_time_from && saturday_time_to)) {
                        $('#edit_pharmacy_staff_schedule_modal #saturday_time_to_note').html(`<small><i>* Cross to next day</i></small>`);
                    }
                    $('#edit_pharmacy_staff_schedule_modal #saturday_time_from').val(saturday_time_from);
                    $('#edit_pharmacy_staff_schedule_modal #saturday_time_to').val(saturday_time_to);

                    // SUNDAY -----------------------
                    let sunday_time_from = currentWeek.scheduleSunday['formatted_time_from'] ? currentWeek.scheduleSunday['formatted_time_from'] : '';
                    let sunday_time_to = currentWeek.scheduleSunday['formatted_time_to'] ? currentWeek.scheduleSunday['formatted_time_to'] : '';
                    $('#edit_pharmacy_staff_schedule_modal #sunday_time_to_note').empty();
                    if(sunday_time_from >= sunday_time_to && (sunday_time_from && sunday_time_to)) {
                        $('#edit_pharmacy_staff_schedule_modal #sunday_time_to_note').html(`<small><i>* Cross to next day</i></small>`);
                    }
                    $('#edit_pharmacy_staff_schedule_modal #sunday_time_from').val(sunday_time_from);
                    $('#edit_pharmacy_staff_schedule_modal #sunday_time_to').val(sunday_time_to);
                }

                $('#edit_pharmacy_staff_schedule_modal #schedule_id').val(schedule_id);
                $('#edit_pharmacy_staff_schedule_modal #employee_id').val(employee_id);

                $('#edit_pharmacy_staff_schedule_modal #modal-title-pharmacy-staff-schedule').html(employee_name)
                $('#edit_pharmacy_staff_schedule_modal #date_range').html(currentScheduleDateRange);

                // displaying calendar view
                createFullCalendar('schedule_calendar', currentDateFrom, events);

                $('#edit_pharmacy_staff_schedule_modal').modal('show');

            },error: function(msg) {

                $("#save_btn").val('Save');
                $("#save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                handleErrorResponse(msg);
                console.log(msg.responseText);
            }

        });
    }

    function createFullCalendar(calendarElementId, initialDate, eventsData) {
        var calendarEl = document.getElementById(calendarElementId);

        // Check if the calendar element exists
        if (!calendarEl) {
            console.error(`Element with ID '${calendarElementId}' not found.`);
            return;
        }

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth', // Set the initial view (e.g., month view)
            initialDate: initialDate,
            events: eventsData, // Pass the events data array
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
        });

        calendar.render(); // Render the calendar
    }

    document.addEventListener('DOMContentLoaded', function() {
        var events = [];
        createFullCalendar('schedule_calendar', '2024-02-03', events);
    });

</script>