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
                                    <input type="text" class="form-control datepicker form-control-sm" id="date" name="date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" readonly>
                                </div>
                                <div class="col-md-12 d-none">
                                    <label label for="patient_name" class="form-label">Patient</label>
                                    <select class="form-select" data-placeholder="Select Patient.." name="patient_id" id="patient_id" title="Select Patient Name"></select>
                                </div>
                                <div class="col-md-6">
                                    <label for="patient_name" class="form-label">Patient</label>
                                    <input type="text" name="patient_name" class="form-control" id="patient_name" placeholder="Provider" disabled>
                                </div>

                                <div class="col-md-12">
                                    <label for="medication_description" class="form-label">Brand Recommended</label>
                                    <input type="text" name="medication_description" class="form-control" id="medication_description" placeholder="">
                                </div>
                                <div class="col-md-12">
                                    <label for="is_switched" class="form-label">Status</label>
                                    <div class="row ms-2">
                                        <div class="form-check col-3">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault2" id="status_name_trhc">
                                            <label class="form-check-label" for="status_name_trhc">TRHC patient</label>
                                        </div>
                                        <div class="form-check col-3">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault2" id="status_name_outside">
                                            <label class="form-check-label" for="status_name_outside">Outside</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="last_provider_that_sent_rx" class="form-label">Provider</label>
                                    <input type="text" name="last_provider_that_sent_rx" class="form-control" id="last_provider_that_sent_rx" placeholder="">
                                </div>
                                <div class="col-md-12">
                                    <label for="is_switched" class="form-label">Is switched?</label>
                                    <div class="row ms-2">
                                        <div class="form-check col-2">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault" id="is_switched_yes">
                                            <label class="form-check-label" for="is_switched_yes">Yes</label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault" id="is_switched_no">
                                            <label class="form-check-label" for="is_switched_no">No</label>
                                        </div>
                                    </div>
                                </div>                                
                                <div class="col-md-12">
                                    <label for="pertinent_financial_info" class="form-label">Pertinent Financial Info</label>
                                    <textarea rows="2" name="pertinent_financial_info" class="form-control" id="pertinent_financial_info" placeholder=""></textarea>
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

        $('#is_switched_yes').prop('checked', false);
        $('#is_switched_no').prop('checked', false);
        $('#status_name_trhc').prop('checked', false);
        $('#status_name_outside').prop('checked', false);
  

        $('#edit_modal #id').val(arr.id);
        $('#edit_modal #date').val(arr.date);
        $('#edit_modal #remarks').val(arr.remarks);
        $('#edit_modal #patient_name').val(arr.patient_name);
        $('#edit_modal #last_provider_that_sent_rx').val(arr.last_provider_that_sent_rx);
        $('#edit_modal #medication_description').val(arr.medication_description);
        $('#edit_modal #pertinent_financial_info').val(arr.pertinent_financial_info);

        if(arr.is_switched == 'Yes') {
            $('#is_switched_yes').prop('checked', true);
        }
        if(arr.is_switched == 'No') {
            $('#is_switched_no').prop('checked', true);
        }

        if(arr.status_name == 'TRHC patient') {
            $('#status_name_trhc').prop('checked', true);
        }
        if(arr.status_name == 'Outside') {
            $('#status_name_outside').prop('checked', true);
        }

        $(`#edit_modal #patient_id`).append("<option selected value='"+arr.patient_id+"'>"+arr.dFirstname+" "+arr.dLastname+"</option>");  
        searchSelect2Api('patient_id', 'edit_modal', "/admin/patient/getNames", {source: 'pioneer'});
    
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

        // Process radio button value
        if ($('#edit_modal #is_switched_yes').is(':checked')) {
            data['is_switched'] = 'Yes';
        } else if ($('#edit_modal #is_switched_no').is(':checked')) {
            data['is_switched'] = 'No';
        }
        if ($('#edit_modal #status_name_trhc').is(':checked')) {
            data['status_name'] = 'TRHC patient';
        } else if ($('#edit_modal #status_name_outside').is(':checked')) {
            data['status_name'] = 'Outside';
        }
        data['pharmacy_store_id'] = menu_store_id;

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/clinical/therapy-change-and-reco/edit`,
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
</script>