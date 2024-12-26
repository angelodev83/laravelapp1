<div class="modal" id="add_staff_schedule_modal" tabindex="-1">
    <div class="modal-dialog modal-md">
      <div class="modal-content">

        <div class="modal-header">
            <h6 class="modal-title">Add Employee Schedule</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          
            <div class="row"><!--start row-->
                
                <div class="col-lg-12">
                    <div class="g-3 row">
                        <div class="col-12">
                            <label for="date_from" class="form-label">Employee</label>
                            <select class="form-control" name="employee_id" id="employee_id" title="Employee Selection..."></select>
                        </div>
                        <div class="col-12">
                            <label for="date_from" class="form-label">Date From</label>
                            <div class="input-group"> 
                                <span class="input-group-text">
                                    <i class="fa fa-calendar-day"></i>
                                </span>
                                <input type="text" class="form-control datepicker" id="date_from" placeholder="YYYY-MM-DD">
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="date_to" class="form-label">Date To</label>
                            <div class="input-group"> 
                                <span class="input-group-text">
                                    <i class="fa fa-calendar-day"></i>
                                </span>
                                <input type="text" class="form-control datepicker" id="date_to" placeholder="YYYY-MM-DD">
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="time_from" class="form-label">Time From</label>
                            {{-- <div class="input-group"> 
                                <span class="input-group-text">
                                    <i class="fa fa-clock"></i>
                                </span>
                                <input type="text" class="form-control" id="time_from" placeholder="HH:MM">
                            </div> --}}
                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <i class="fa fa-clock"></i>
                                </span>
                                <input type="text" class="form-control" id="time_from" placeholder="HH:MM" aria-label="HH:MM">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="time_from_am_pm" >AM</button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item am" href="javascript: selectTimeDropDown('time_from', 'am');">AM</a>
                                    </li>
                                    <li><a class="dropdown-item pm" href="javascript: selectTimeDropDown('time_from', 'pm');">PM</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="time_to" class="form-label">Time To</label>
                            {{-- <div class="input-group"> 
                                <span class="input-group-text">
                                    <i class="fa fa-clock"></i>
                                </span>
                                <input type="text" class="form-control" id="time_to" placeholder="HH:MM">
                            </div> --}}
                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <i class="fa fa-clock"></i>
                                </span>
                                <input type="text" class="form-control" id="time_to" placeholder="HH:MM" aria-label="HH:MM">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="time_to_am_pm">AM</button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item am" href="javascript: selectTimeDropDown('time_to', 'am');">AM</a>
                                    </li>
                                    <li><a class="dropdown-item pm" href="javascript: selectTimeDropDown('time_to', 'pm');">PM</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 gap-3">
                            <label for="week_day" class="form-label">Recurring</label>
                            <div>
                                <input id="week_day_1" type="checkbox" class="" checked/> Mon
                                <input id="week_day_2" type="checkbox" class="ms-3" checked/> Tue
                                <input id="week_day_3" type="checkbox" class="ms-3" checked/> Wed
                                <input id="week_day_4" type="checkbox" class="ms-3" checked/> Thu
                                <input id="week_day_5" type="checkbox" class="ms-3" checked/> Fri
                                <input id="week_day_6" type="checkbox" class="ms-3" /> Sat
                                <input id="week_day_7" type="checkbox" class="ms-3" /> Sun
                            </div>
                        </div>
                    </div>
                </div>
        
            </div><!--end row-->

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="submit_btn" onclick="savePharmacyStaffSchedule()">Submit</button>
        </div>
      </div>
    </div>
</div>

<script>
    function addStaffScheduleModal()
    {
        selectTimeDropDown('time_from', 'AM');
        selectTimeDropDown('time_to', 'AM');

        $('#add_staff_schedule_modal #employee_id').select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#add_staff_schedule_modal'),
		});

        $('#add_staff_schedule_modal #date_from').datepicker({
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

        $('#add_staff_schedule_modal #date_to').datepicker({
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

        loadEmployeeDropDown();

        $('#add_staff_schedule_modal').modal('show');
    }

    function loadEmployeeDropDown()
    {
        let data = {
            pharmacy_store_id: menu_store_id,
            is_offshore: is_offshore,
        };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/search/pharmacy-staff",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(data),
            success: function(data) {
                
                var len = data.data.length;
                
                $("#add_staff_schedule_modal #employee_id").empty();
                for( var i = 0; i<len; i++){
                    var id = data.data[i]['id'];
                    var name = data.data[i]['lastname']+', '+data.data[i]['firstname'];
                    $("#add_staff_schedule_modal #employee_id").append("<option value='"+id+"'>"+name+"</option>");
                }
            },
            error: function(res) {
                handleErrorResponse(res)
            }
        });
    }

    function savePharmacyStaffSchedule()
    {
        let data = {
            pharmacy_store_id: menu_store_id,
            employee_id: $(`#add_staff_schedule_modal #employee_id option:selected`).val(),
            date_from: $('#add_staff_schedule_modal #date_from').val(),
            date_to: $('#add_staff_schedule_modal #date_to').val(),
            time_from: $('#add_staff_schedule_modal #time_from').val(),
            time_to: $('#add_staff_schedule_modal #time_to').val(),
            time_from_am_pm: $('#time_from_am_pm').text(),
            time_to_am_pm: $('#time_to_am_pm').text(),
            recurring: []
        };

        for(let i = 1; i<=7; i++) {
            let is_checked = $(`#add_staff_schedule_modal #week_day_${i}`).is(':checked');
            if(is_checked) {
                data.recurring.push(i);
            }
        }

        console.log("data",data)

        $("#submit_btn").attr('disabled');
        $("#submit_btn").val('Saving...');

        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/human-resource/schedules/add`,
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
                
                $("#submit_btn").removeAttr('disabled');
                $("#submit_btn").val('Submit');

                $('#add_staff_schedule_modal').modal('hide');
                loadEvents();
            },error: function(res) {
                $("#submit_btn").removeAttr('disabled');
                $("#submit_btn").val('Submit');
                console.log(res);
                handleErrorResponse(res);
            }
        });
    }

    function selectTimeDropDown(id, value) {
        $(`#add_staff_schedule_modal #${id}_am_pm`).html(value.toUpperCase());
    }
</script>