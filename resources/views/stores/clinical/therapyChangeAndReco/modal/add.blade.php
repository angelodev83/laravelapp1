<div class="modal" id="add_modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title">Add Form</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">      
            <div class="row">
                <form action="" method="POST" id="#addForm">
                    <div class="col-lg-12">
                        <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="text" class="form-control datepicker form-control-sm" id="date" name="date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" readonly>
                                </div>
                                <div class="col-md-12">
                                    <label label for="patient_name" class="form-label">Patient</label>
                                    <select class="form-select" data-placeholder="Select Patient.." name="patient_id" id="patient_id" title="Select Patient Name"></select>
                                </div>
                                <div class="col-md-12">
                                    <label for="diagnosis" class="form-label">Diagnosis</label>
                                    <!-- <textarea rows="3" name="diagnosis" class="form-control" id="diagnosis" placeholder=""></textarea> -->
                                    <div id="diagnosis" class="row ms-2"></div>
                                </div>
                                <div class="col-md-12">
                                    <label for="reason" class="form-label">Reason</label>
                                    <textarea rows="3" name="reason" class="form-control" id="reason" placeholder=""></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label for="provider_id" class="form-label">Provider</label>
                                    <select class="form-select" name="provider_id" id="provider_id"></select>
                                </div>
                                <div class="col-md-12">
                                    <label for="employee_id" class="form-label">In Charge</label>
                                    <select class="form-select" name="employee_id" id="employee_id"></select>
                                </div> 
                                <div class="col-md-12">
                                    <label for="status_id" class="form-label">Call Status</label>
                                    <select class="form-select" name="status_id" id="status_id"></select>
                                </div>
                                <div class="col-md-12">
                                    <label for="time_start" class="form-label">Time Start</label>
                                    <input type="time" readonly class="form-control form-control-sm timeChange" id="time_start" name="time_start">
                                </div>
                                <div class="col-md-12">
                                    <label for="time_end" class="form-label">Time End</label>
                                    <input type="time" readonly class="form-control form-control-sm timeChange" id="time_end" name="time_end">
                                </div>
                                <div class="col-md-12">
                                    <label for="total_time" class="form-label">Total Time</label>
                                    <input type="text" class="form-control form-control-sm" id="total_time" name="total_time"  readonly>
                                </div>
                                <div class="col-md-12">
                                    <label for="soap" class="form-label">SOAP</label>
                                    <textarea rows="3" name="soap" class="form-control" id="soap" placeholder=""></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label for="ses_adrs" class="form-label">SES/ADRS</label>
                                    <div class="row ms-2">
                                        <div class="form-check col-6">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault" id="ses_adrs_yes">
                                            <label class="form-check-label" for="ses_adrs_yes">Yes</label>
                                        </div>
                                        <div class="form-check col-6">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault" id="ses_adrs_no" checked>
                                            <label class="form-check-label" for="ses_adrs_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="col-md-12">
                                    <label for="ses_adrs" class="form-label">Caregoals</label>
                                    <div class="row ms-2" id="caregoals"></div>
                                </div> -->
                            </div> 
                    </div>
                </form>
            </div><!--end row-->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="save_btn" onclick="saveForm()">Submit</button>
        </div>
    </div>
  </div>
</div>

<script>
    function saveForm(){
        // $("#add_modal #save_btn").val('Saving... please wait!');
        // $("#add_modal #save_btn").attr('disabled','disabled');
        // $('.error_txt').remove();
        
        let data = {};

        $('#add_modal input, #add_modal textarea, #add_modal select').each(function() {
            if (this.type === 'checkbox') {
                // Skip checkbox processing here
                return true;
            }
            if (this.type === 'radio') {
                // Skip radio processing here
                return true;
            }
            data[this.id] = this.value;
        });

        let diagnoses = [];

        // Process radio button value
        if ($('#add_modal #ses_adrs_yes').is(':checked')) {
            data['ses_adrs'] = 1;
        } else if ($('#add_modal #ses_adrs_no').is(':checked')) {
            data['ses_adrs'] = 0;
        }

        $('#add_modal .diagnosis-checkbox:checked').each(function() {
            diagnoses.push(this.value);
        });
        data['diagnosis'] = diagnoses;
        data['pharmacy_store_id'] = menu_store_id;

        // console.log(data);
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/clinical/outreach/add`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            success: function(data) {
                $("#add_modal #save_btn").val('Save');
                $("#add_modal #save_btn").removeAttr('disabled');
                
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    reloadDataTable();
                    sweetAlert2('success', 'Record has been saved.');
                    $('#add_modal').modal('hide');
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                $("#add_modal #save_btn").val('Save');
                $("#add_modal #save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }
</script>