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
                                            <input class="form-check-input" type="radio" name="flexRadioDefault" id="ses_adrs_no">
                                            <label class="form-check-label" for="ses_adrs_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="ses_adrs" class="form-label">Caregoals</label>
                                    <div class="row ms-2" id="caregoals"></div>
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
    let addMore = 0;
    let inmar_return_id;

    function showDeleteFileOnly(){
        let data = {
            id: $("#edit_modal #file_id").val(),
        };
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/eod-register-report/register/delete_file`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $("#edit_modal #chip_controller").hide();
                    $("#edit_modal #file").show();
                    Swal.close();
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }

        });

        
    }

    function showEditForm(id)
    {
        data_id = id;

        $('#edit_modal #time_start').off('click').on('click', function() {
            $(this).attr('readonly', false); // Temporarily remove readonly
            // $(this).focus(); // Open the time picker
            var input = $(this); 
                setTimeout(function() {
                    input[0].showPicker(); // Show the time picker
                }, 10);
        });

        $('#edit_modal #time_start').off('blur').on('blur', function() {
            $(this).attr('readonly', true); // Re-add readonly when the input loses focus
        });

        $('#edit_modal #time_end').off('click').on('click', function() {
            $(this).attr('readonly', false); // Temporarily remove readonly
            // $(this).focus(); // Open the time picker
            var input = $(this); 
                setTimeout(function() {
                    input[0].showPicker(); // Show the time picker
                }, 10);
        });

        $('#edit_modal #time_end').off('blur').on('blur', function() {
            $(this).attr('readonly', true); // Re-add readonly when the input loses focus
        });
        

        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5', modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true,
   			orientation: "right",
        });

        let btn = $(`#data-edit-btn-${id}`);
        let arr = btn.data('array');
        console.log('fire-------------', arr);

        let diagnosesIds = arr.diagnoses.map(diagnosis => diagnosis.store_status_id);

        (arr.ses_adrs == 1)?$("#edit_modal #ses_adrs_yes").prop("checked", true):$("#edit_modal #ses_adrs_no").prop("checked", true);
        

        $('#edit_modal #id').val(arr.id);
        $('#edit_modal #date').val(arr.date);
        $('#edit_modal #diagnosis').val(arr.diagnosis);
        $('#edit_modal #reason').val(arr.reason);
        $('#edit_modal #care_goals').val(arr.care_goals);
        $('#edit_modal #biller').val(arr.biller);
        $('#edit_modal #profits').val(arr.profits);
        (arr.time_start != null)?$('#edit_modal #time_start').val(removeSeconds(arr.time_start)):'';
        (arr.time_end != null)?$('#edit_modal #time_end').val(removeSeconds(arr.time_end)):'';
        $('#edit_modal #soap').val(arr.soap);


        $(`#edit_modal #patient_id`).append("<option selected value='"+arr.patient_id+"'>"+arr.dFirstname+" "+arr.dLastname+"</option>");  
        (arr.in_charge.id != null)?$(`#edit_modal #employee_id`).append("<option selected value='"+arr.in_charge.id+"'>"+arr.in_charge.firstname+" "+arr.in_charge.lastname+"</option>"):'';  
        populateNormalSelect(`#status_id`, '#edit_modal', '/admin/search/store-status', {category: 'kpi_call_status'}, arr.store_call_status_id)
        searchSelect2Api('patient_id', 'edit_modal', "/admin/patient/getNames", {source: 'pioneer'});     
        searchSelect2Api('employee_id', 'edit_modal','/admin/search/user-employee');
        populateCheckBox('#edit_modal', '#diagnosis', "/admin/search/store-status", {category: 'diagnosis'}, diagnosesIds);
        populateNormalSelect(`#provider_id`, '#edit_modal', '/admin/search/store-status', {category: 'clinical_provider'}, arr.store_provider_status_id)
        
        $('#edit_modal #caregoals').empty();

        arr.diagnoses.forEach(function(diagnosis) {
            if (diagnosis.status) {
                let name = diagnosis.status.name;
                let description = diagnosis.status.description;

                let diagnosisHtml = `
                    <div class="col-12 diagnosis-item">
                        <h6>${name}</h6>
                        <p>${description.replace(/\n/g, "<br>")}</p>
                    </div>
                `;
                $('#edit_modal #caregoals').append(diagnosisHtml);
            }
        });

        $('#edit_modal .timeChange').off('change').on('change', function() {
            computeTotalTime('#edit_modal');
        });
        computeTotalTime('#edit_modal');
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
        if ($('#edit_modal #ses_adrs_yes').is(':checked')) {
            data['ses_adrs'] = 1;
        } else if ($('#edit_modal #ses_adrs_no').is(':checked')) {
            data['ses_adrs'] = 0;
        }

        $('#edit_modal .diagnosis-checkbox:checked').each(function() {
            diagnoses.push(this.value);
        });
        data['diagnosis'] = diagnoses;
        data['pharmacy_store_id'] = menu_store_id;

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/clinical/outreach/edit`,
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
                    reloadDataTable();
                    
                    sweetAlert2(data.status, data.message);
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
</script>