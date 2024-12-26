<div class="modal modal-md" style="display:none;" id="add_staff_leave_modal" tabindex="-1">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header pb-1">
                <div class="modal-title">
                    <h6 class="mb-0 pb-0">Request Time Off</h6>
                    <small>Please fill out the details for your time off</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">

                    <div class="col-12 mt-2">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="type" name="type">
                            <option>Paid Leave</option>
                            <option>Unpaid Leave</option>
                            <option>Sick Leave</option>
                        </select>
                    </div>

                    <div class="col-6 mt-2">
                        <label for="date_from" class="form-label">From <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" id="icon-date"><i class="fa fa-calendar"></i></span>
                            <input type="text" class="form-control datepicker" id="date_from" name="date_from" aria-describedby="icon-date" placeholder="MM/DD/YYYY" required>
                        </div>
                        <div class="invalid-feedback">
                            Date From is required
                        </div>
                    </div>
                    <div class="col-6 mt-2">
                        <label for="date_to" class="form-label">To <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" id="icon-date"><i class="fa fa-calendar"></i></span>
                            <input type="text" class="form-control datepicker" id="date_to" name="date_to" aria-describedby="icon-date" placeholder="MM/DD/YYYY" required>
                        </div>
                        <div class="invalid-feedback">
                            Date To is required
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="rectangle">
                            <span class="ms-3">Your request</span> <span class="ms-auto me-3" id="compute_day">0 day</span>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="rectangle" style="height: 51px;">
                            <span class="ms-3">
                                <p class="my-0 py-0">Select Half Days</p>
                                <p class="my-0 py-0 text-secondary" style="font-size: 13px !important;">View Full Breakdown</p>
                            </span> 
                            <span class="ms-auto" style="vertical-align: middle;">
                                <div class="form-check form-switch me-3">
                                    <input class="form-check-input" type="checkbox" id="select_half_days_id" style="width: 3em !important;" onchange="toggleSelectHalfDays()">
                                </div>
                            </span>
                        </div>
                    </div>

                    <div class="col-12 d-none" id="select_half_days_breakdown"></div>

                    <div class="col-12 mt-1">
                        <label for="type" class="form-label">Reason</label>
                        <textarea class="form-control" id="reason" name="reason"></textarea>
                    </div>

                    <div class="col-12 mt-1">
                        <label for="documents" class="form-label">Attachments</label>
                        <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                        <div id="for-file"></div>
                    </div>
                    
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary w-100" id="request_btn" onclick="storeLeave()">Request</button>
            </div>

        </div>
    </div>
</div>
  
  
  
  
  <script>

    function toggleSelectHalfDays()
    {
        var has_half_days = $('#select_half_days_id').prop('checked');

        if(has_half_days == 'false' || has_half_days == false || has_half_days == 0) {
            $('#add_staff_leave_modal .day_breakdown').each(function() {
                this.value = "Full day";
            });
            $('#add_staff_leave_modal #select_half_days_breakdown').addClass('d-none');
        } else {
            $('#add_staff_leave_modal #select_half_days_breakdown').removeClass('d-none');
        }
    }

    function computeAddDays()
    {
        const date_from = $('#add_staff_leave_modal #date_from').val();
        const date_to = $('#add_staff_leave_modal #date_to').val();

        // Parse the input dates
        var start = new Date(date_from);
        var end = new Date(date_to);

        // Validate the dates
        if (isNaN(start.getTime()) || isNaN(end.getTime())) {
            $('#result').text('Invalid date format. Please enter dates in MM/DD/YYYY format.');
            return;
        }

        // Calculate the difference in time
        var timeDiff = end - start;
        
        // Convert time difference from milliseconds to days
        var daysDiff = timeDiff / (1000 * 3600 * 24);
        daysDiff += 1;
        var daysText = daysDiff+' day';

        if(daysDiff > 1) {
            daysText = daysDiff+' days';
        }

        let breakdownHtml = '';
        var options = { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric' };
        for(let i = 0; i < daysDiff; i++) {
            let date = new Date(date_from);
            date.setDate(date.getDate() + i);
            var formattedDate = date.toLocaleDateString('en-US', options);

            var year = date.getFullYear();
            var month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
            var day = String(date.getDate()).padStart(2, '0');

            breakdownHtml += `
                <label for="type" class="form-label mb-0">${formattedDate}</label>
                <input type="hidden" value="${year}-${month}-${day}" id="date_breakdown_${i}">
                <select class="form-select day_breakdown mb-2" id="day_breakdown_${i}" name="${i}" value="Full day">
                    <option value="Full day">Full day</option>
                    <option value="Half day">Half day</option>
                </select>
            `;
        }

        $('#add_staff_leave_modal #compute_day').html(daysText);
        $('#add_staff_leave_modal #select_half_days_breakdown').html(breakdownHtml);
    }

    function storeLeave() {

        let data = {
            pharmacy_store_id: menu_store_id,
            type: $('#add_staff_leave_modal #type').val(),
            date_from: $('#add_staff_leave_modal #date_from').val(),
            date_to: $('#add_staff_leave_modal #date_to').val(),
            reason: $('#add_staff_leave_modal #reason').val(),
            half_days_breakdown: null,
            is_select_half_days: 0,
        };

        var has_half_days = $('#select_half_days_id').prop('checked');
        if(has_half_days == 'true' || has_half_days == true || has_half_days == 1) {
            data.is_select_half_days = 1;
        }

        let breakdown = {};
        $('#add_staff_leave_modal .day_breakdown').each(function() {
            var date = $(`#add_staff_leave_modal #date_breakdown_${this.name}`).val();
            breakdown[date] = this.value;
        });
        data['half_days_breakdown'] = breakdown;
        console.log("data-------",data)
        // return;

        let formData = new FormData();
        let uploadFiles = $('#add_staff_leave_modal #file').get(0).files;
        for (let i = 0; i < uploadFiles.length; i++) {
            formData.append("files[]", uploadFiles[i]);
            let kbSize = uploadFiles[i].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }

        formData.append("data", JSON.stringify(data));

        console.log("save data--",data);
        $("#request_btn").attr('disabled');
        $("#request_btn").val('Requesting...');
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/pharmacy-staff/leaves/add",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                res = JSON.parse(res);
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
                
                $("#request_btn").removeAttr('disabled');
                $("#request_btn").val('Request');

                $('#add_staff_leave_modal').modal('hide');
                loadEvents();
            },error: function(res) {
                if(res.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                $("#request_btn").removeAttr('disabled');
                $("#request_btn").val('Request');
                console.log(res.message);
                handleErrorResponse(res);
            }
        });
          
    }
  

    function addStaffLeaveModal()
    {
        let fileInput = $('<input/>', {
            id: 'file',
            class: 'imageuploadify-file-general-class',
            name: 'file',
            type: 'file',
            accept: '*',
            multiple: '',
        });
        $('#add_staff_leave_modal #for-file').html(fileInput); 
        $('#add_staff_leave_modal #file').imageuploadify();
        $("#add_staff_leave_modal .imageuploadify-container").remove();
        $('#add_staff_leave_modal .imageuploadify-images-list i.bxs-cloud-upload').remove();
        $('#add_staff_leave_modal .imageuploadify-images-list button.btn-default').css("margin-top", "1px");
        $('#add_staff_leave_modal .imageuploadify-images-list button.btn-default').css("margin-bottom", "10px");
        $('#add_staff_leave_modal .imageuploadify-images-list .imageuploadify-message').css('border', 'none');
        $('#add_staff_leave_modal .imageuploadify-images-list .imageuploadify-message').css('font-size', '15px');
        $('#add_staff_leave_modal .imageuploadify-images-list .imageuploadify-message').css('margin-top', '3px');
        
        
        $('#add_staff_leave_modal #select_half_days_breakdown').html('');
        $('#add_staff_leave_modal #type').val("Paid Leave");

        $('#add_staff_leave_modal').modal('show');
    }
</script>