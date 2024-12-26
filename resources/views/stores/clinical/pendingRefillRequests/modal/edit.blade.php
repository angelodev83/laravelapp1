<div class="modal" id="edit_modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Edit Form</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form action="" method="POST" id="#editForm">
                        <div class="col-lg-12">
                            <div class="row g-3">
                                <input type="hidden" id="id">
                                <div class="col-md-6">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="text" class="form-control datepicker" id="date" name="date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="rx_number" class="form-label">Script No.</label>
                                    <input type="text" name="rx_number" class="form-control" id="rx_number" placeholder="">
                                </div>
                                <div class="col-md-6 d-none">
                                    <label label for="patient_name" class="form-label">Patient</label>
                                    <select class="form-select" data-placeholder="Select Patient.." name="patient_id" id="patient_id" title="Select Patient Name"></select>
                                </div>
                                <div class="col-md-12">
                                    <label for="patient_name" class="form-label">Patient</label>
                                    <input type="text" name="patient_name" class="form-control" id="patient_name" placeholder="Provider" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="send_date" class="form-label">Send Date</label>
                                    <input type="text" class="form-control datepicker" id="send_date" name="send_date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD">
                                </div>
                                <div class="col-md-6">
                                    <label for="send_time" class="form-label">Send Time</label>
                                    <input type="time" class="form-control timeChange" id="send_time" name="send_time" placeholder="--:-- --">
                                </div>
                                <div class="col-md-6">
                                    <label for="status_name" class="form-label">Status</label>
                                    <input type="text" name="status_name" class="form-control" id="status_name" placeholder="eg. Confirmed">
                                </div>
                                <div class="col-md-6">
                                    <label for="provider" class="form-label">Provider</label>
                                    <input type="text" name="provider" class="form-control" id="provider" placeholder="Provider">
                                </div>
                                <div class="col-md-12">
                                    <label for="medication_description" class="form-label">Medication</label>
                                    <input type="text" name="medication_description" class="form-control" id="medication_description" placeholder="">
                                </div>
                                <div class="col-md-12">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <textarea rows="3" name="remarks" class="form-control" id="remarks" placeholder=""></textarea>
                                </div>
                            </div> 
                        </div>
                    </form>
                </div><!--end row-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="update_btn" onclick="updateForm(event)">Submit</button>
            </div>
            
        </div>
    </div>
</div>

<script> 
    function showEditForm(id)
    {
        data_id = id;

        let btn = $(`#data-edit-btn-${id}`);
        let arr = btn.data('array');
        console.log('fire-------------', arr);

        $('#edit_modal #id').val(arr.id);
        $('#edit_modal #date').val(arr.date);
        $('#edit_modal #remarks').val(arr.remarks);
        $('#edit_modal #rx_number').val(arr.rx_number);
        $('#edit_modal #send_date').val(arr.o_send_date);
        (arr.o_send_time != null)?$('#edit_modal #send_time').val(removeSeconds(arr.o_send_time)):'';
        $('#edit_modal #provider').val(arr.provider);
        $('#edit_modal #patient_name').val(arr.patient_name);
        $('#edit_modal #status_name').val(arr.status_name);
        $('#edit_modal #medication_description').val(arr.medication_description);

        $(`#edit_modal #patient_id`).append("<option selected value='"+arr.patient_id+"'>"+arr.dFirstname+" "+arr.dLastname+"</option>");  
        searchSelect2Api('patient_id', 'edit_modal', "/admin/patient/getNames", {source: 'pioneer'});

        $('#edit_modal #send_date').datepicker({
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

        $('#edit_modal #send_time').off('click').on('click', function() {
            $(this).attr('readonly', false); // Temporarily remove readonly
            var input = $(this); 
                setTimeout(function() {
                    input[0].showPicker(); // Show the time picker
                }, 10);
        });

        console.log("o--------------send time", $('#send_time').val())
    
        $('#edit_modal').modal('show');
    }
    
    function updateForm(event)
    {
        event.preventDefault();
        $("#edit_modal #update_btn").val('Saving... please wait!');
        $("#edit_modal #update_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        
        let data = {};

        $('#edit_modal input, #edit_modal textarea, #edit_modal select').each(function() {
            data[this.id] = this.value;
        });

        data['pharmacy_store_id'] = menu_store_id;

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/clinical/pending-refill-requests/edit`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            success: function(data) {
                $("#edit_modal #update_btn").val('Save');
                $("#edit_modal #update_btn").removeAttr('disabled');
            
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#edit_modal #"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    reloadDataTable(data);
                    $('#edit_modal').modal('hide');    
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                $("#edit_modal #update_btn").val('Save');
                $("#edit_modal #update_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }

        });
    }

    function removeSeconds(timeString) {
        return timeString.slice(0, 5);
    }
</script>