<div class="modal modal-md" style="display:none;" id="show_staff_leave_modal" tabindex="-1">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #464C1F !important;">
                <h6 class="modal-title text-white">Rejected Leave</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body mx-2">
                <div class="row gx-2">
                    <div class="col-12 mb-2 pb-1 mt-1">
                        <div id="fullname_avatar"></div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="rectangle">
                            <span class="ms-3" id="date_range">Your request</span> <span class="ms-auto me-3" id="computed_days">0 day</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <label for="type" class="form-label">Leave Type</label>
                        <p id="type"></p>
                    </div>
                    <div class="col-6">
                        <label for="formatted_created_at" class="form-label">Applied</label>
                        <p id="formatted_created_at"></p>
                    </div>
                    <div class="col-12">
                        <label for="half_days_breakdown" class="form-label">Halfdays Breakdown</label>
                        <div id="half_days_breakdown_div">

                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <label for="reason" class="form-label">Reason</label>
                    </div>
                    <div class="col-12">
                        <p id="reason"></p>
                    </div>
                    <div class="col-12" id="reason_for_rejection_label_row">
                        <label for="reason_for_rejection_value" class="form-label">Reason for rejection</label>
                    </div>
                    <div class="col-12" id="reason_for_rejection_value_row">
                        <p id="reason_for_rejection_value"></p>
                    </div>
                    <div class="col-12">
                        <label for="reason" class="form-label">Attachments</label>
                        <div id="attachments_div" class="gap-2 row mx-1"></div>
                    </div>
                    <div class="col-12 mb-3" id="reason_for_rejection_form_row">
                        <label for="type" class="form-label">Reason for rejection</label>
                        <textarea class="form-control" id="reason_for_rejection" name="reason_for_rejection" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                        <div class="row" id="button_row"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
  
  
  
  
  <script>
    function updateLeave(id, status_id) {
        const reason_for_rejection = $('#show_staff_leave_modal #reason_for_rejection').val();
        let data = {
            id: id,
            status_id: status_id
        };

        if(status_id == 903) {
            data['reason_for_rejection'] = reason_for_rejection;
        }

        //console.log(data);
        $("#reject_btn").attr('disabled');
        $("#reject_btn").val('Rejecting...');
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/pharmacy-staff/leaves/update",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(msg) {
            //success
                Swal.fire({
                    position: 'center',
                    icon: msg.status,
                    title: msg.message,
                    showConfirmButton: false,
                    timer: 4000
                });
                
                $('#show_staff_leave_modal').modal('hide');
                loadEvents();
            },error: function(msg) {
                if(msg.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                $("#reject_btn").removeAttr('disabled');
                $("#reject_btn").val('Reject');
                handleErrorResponse(msg);
                console.log(msg.responseText);
            }
        });
          
    }
  

    function showStaffLeaveModal(leave_id)
    {
        let data = {};
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/admin/pharmacy-staff/leaves/"+leave_id,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {
                console.log("get res",res)

                const detail = res.data.detail;
                const formatted = res.data.formatted;
                const status_id = detail.status_id;

                const half_days_breakdown = JSON.parse(detail.half_days_breakdown);
                // console.log("half_days_breakdown", half_days_breakdown);

                $('#show_staff_leave_modal #half_days_breakdown_div').empty();

                let breakdownHtml = '';
                var options = { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric' };
                Object.entries(half_days_breakdown).forEach(([key, value]) => {
                    let date = new Date(key);
                    var formattedDate = date.toLocaleDateString('en-US', options);
                    breakdownHtml += `
                        <span class="d-flex my-0 py-0">
                            <b class="my-0 py-0">
                                <i class="fa fa-${value=="Half day" ? 'circle-half-stroke' : 'circle'} me-2" style="color: #5c9ce3;"></i>${formattedDate}:
                            </b> 
                            <p class="my-0 py-0 ms-3">${value}</p>
                        </span>
                    `;
                });

                $('#show_staff_leave_modal #half_days_breakdown_div').html(breakdownHtml);

                $('#show_staff_leave_modal #attachments_div').html(formatted.attachments);

                $('#show_staff_leave_modal #reason_for_rejection_label_row').addClass('d-none');
                $('#show_staff_leave_modal #reason_for_rejection_value_row').addClass('d-none');
                $('#show_staff_leave_modal #reason_for_rejection_form_row').addClass('d-none');
                if(status_id == 903) {
                    $('#show_staff_leave_modal #reason_for_rejection_label_row').removeClass('d-none');
                    $('#show_staff_leave_modal #reason_for_rejection_value_row').removeClass('d-none');
                }
                if(status_id == 901) {
                    $('#show_staff_leave_modal #reason_for_rejection_form_row').removeClass('d-none');
                }

                @cannot('menu_store.hr.leaves.update')
                    $('#show_staff_leave_modal #reason_for_rejection_form_row').addClass('d-none');
                @endcannot

                $('#show_staff_leave_modal .modal-header h6').html(formatted.status_label);
                $('#show_staff_leave_modal #type').html(detail.type);
                $('#show_staff_leave_modal #formatted_created_at').html(formatted.formatted_created_at);
                $('#show_staff_leave_modal #fullname_avatar').html(formatted.fullname_avatar);
                $('#show_staff_leave_modal #reason').html(detail.reason);
                $('#show_staff_leave_modal #reason_for_rejection_value').html(detail.reason_for_rejection);
                $('#show_staff_leave_modal #button_row').html(formatted.button_row);
                $('#show_staff_leave_modal #date_range').html(formatted.date_range);
                $('#show_staff_leave_modal #computed_days').html(formatted.computed_days);                

                $('#show_staff_leave_modal').modal('show');

                Swal.close();
            },error: function(msg) {
                if(msg.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                $("#delete_btn").val('DELETE').removeAttr('disabled');
                handleErrorResponse(msg);
                console.log(msg.responseText);
            }
        });
    }
</script>