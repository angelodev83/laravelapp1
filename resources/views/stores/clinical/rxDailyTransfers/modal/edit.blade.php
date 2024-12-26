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
                                <div class="col-md-12 d-none">
                                    <label label for="patient_name" class="form-label">Patient</label>
                                    <select class="form-select" data-placeholder="Select Patient.." name="patient_id" id="patient_id" title="Select Patient Name"></select>
                                </div>
                                <div class="col-md-6">
                                    <label for="patient_name" class="form-label">Patient</label>
                                    <input type="text" name="patient_name" class="form-control" id="patient_name" placeholder="Provider" disabled>
                                </div>

                                <div class="col-md-12">
                                    <label for="medication_description" class="form-label">Medication</label>
                                    <input type="text" class="form-control" id="medication_description" name="medication_description"/>
                                </div>
                                <div class="col-md-12">
                                    <label for="call_status" class="form-label">Call Status</label>
                                    <div class="row ms-2">
                                        <div class="form-check col-2">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault3" id="call_status_called">
                                            <label class="form-check-label" for="call_status_called">Called</label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault3" id="call_status_messaged">
                                            <label class="form-check-label" for="call_status_messaged">VM/SMS</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="is_transfer" class="form-label">Transfer</label>
                                    <div class="row ms-2">
                                        <div class="form-check col-2">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault" id="is_transfer_yes">
                                            <label class="form-check-label" for="is_transfer_yes">Yes</label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault" id="is_transfer_no">
                                            <label class="form-check-label" for="is_transfer_no">No</label>
                                        </div>
                                    </div>
                                </div>   
                                <div class="col-md-12">
                                    <label for="is_ma" class="form-label">MA</label>
                                    <div class="row ms-2">
                                        <div class="form-check col-2">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault4" id="is_ma_na">
                                            <label class="form-check-label" for="is_ma_na">N/A</label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault4" id="is_ma_messaged">
                                            <label class="form-check-label" for="is_ma_messaged">Messaged</label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault4" id="is_ma_sent">
                                            <label class="form-check-label" for="is_ma_sent">Sent</label>
                                        </div>
                                    </div>
                                </div>   
                                <div class="col-md-12">
                                    <label label for="provider" class="form-label">Provider</label>
                                    <select class="form-select" data-placeholder="Select Provider.." name="provider" id="provider" title="Select Provider"></select>
                                </div>                             
                                <div class="col-md-6">
                                    <label for="fax_pharmacy" class="form-label">Pharmacy</label>
                                    <input type="text" class="form-control" id="fax_pharmacy" name="fax_pharmacy" placeholder="eg. Faxed"/>
                                </div>
                                <div class="col-md-6">
                                    <label for="expected_rx" class="form-label">Scripts Expected</label>
                                    <input type="number" class="form-control text-end" id="expected_rx" name="expected_rx" placeholder="eg. 0"/>
                                </div>
                                <div class="col-md-12">
                                    <label for="is_received" class="form-label">Received</label>
                                    <div class="row ms-2">
                                        <div class="form-check col-2">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault2" id="is_received_yes">
                                            <label class="form-check-label" for="is_received_yes">Yes</label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault2" id="is_received_no">
                                            <label class="form-check-label" for="is_received_no">No</label>
                                        </div>
                                    </div>
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

        $('#is_transfer_yes').prop('checked', false);
        $('#is_transfer_no').prop('checked', false);

        $('#is_received_yes').prop('checked', false);
        $('#is_received_no').prop('checked', false);

        $('#call_status_called').prop('checked', false);
        $('#call_status_messaged').prop('checked', false);

        $('#is_ma_na').prop('checked', false);
        $('#is_ma_messaged').prop('checked', false);
        $('#is_ma_sent').prop('checked', false);
  

        $('#edit_modal #id').val(arr.id);
        $('#edit_modal #date').val(arr.date);
        $('#edit_modal #remarks').val(arr.remarks);
        $('#edit_modal #patient_name').val(arr.patient_name);
        $('#edit_modal #medication_description').val(arr.medication_description);
        $('#edit_modal #provider').val(arr.provider);
        $('#edit_modal #fax_pharmacy').val(arr.fax_pharmacy);
        $('#edit_modal #expected_rx').val(arr.expected_rx);

        if(arr.call_status == 'Called') {
            $('#call_status_called').prop('checked', true);
        }
        if(arr.call_status == 'VM/SMS') {
            $('#call_status_messaged').prop('checked', true);
        }

        if(arr.is_transfer == 'Yes') {
            $('#is_transfer_yes').prop('checked', true);
        }
        if(arr.is_transfer == 'No') {
            $('#is_transfer_no').prop('checked', true);
        }

        if(arr.is_received == 'Yes') {
            $('#is_received_yes').prop('checked', true);
        }
        if(arr.is_received == 'No') {
            $('#is_received_no').prop('checked', true);
        }

        if(arr.is_ma == 'N/A') {
            $('#is_ma_na').prop('checked', true);
        }
        if(arr.is_ma == 'Messaged') {
            $('#is_ma_messaged').prop('checked', true);
        }
        if(arr.is_ma == 'Sent') {
            $('#is_ma_sent').prop('checked', true);
        }

        $(`#edit_modal #patient_id`).append("<option selected value='"+arr.patient_id+"'>"+arr.dFirstname+" "+arr.dLastname+"</option>");  
        searchSelect2Api('patient_id', 'edit_modal', "/admin/patient/getNames", {source: 'pioneer'});
        populateNormalSelect('#provider', '#edit_modal', '/admin/search/clinical-provider', {}, arr.provider);
    
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
        if ($('#edit_modal #call_status_called').is(':checked')) {
            data['call_status'] = 'Called';
        } else if ($('#edit_modal #call_status_messaged').is(':checked')) {
            data['call_status'] = 'VM/SMS';
        }
        if ($('#edit_modal #is_transfer_yes').is(':checked')) {
            data['is_transfer'] = 'Yes';
        } else if ($('#edit_modal #is_transfer_no').is(':checked')) {
            data['is_transfer'] = 'No';
        }
        if ($('#edit_modal #is_received_yes').is(':checked')) {
            data['is_received'] = 'Yes';
        } else if ($('#edit_modal #is_received_no').is(':checked')) {
            data['is_received'] = 'No';
        }
        if ($('#edit_modal #is_ma_na').is(':checked')) {
            data['is_ma'] = 'N/A';
        } else if ($('#edit_modal #is_ma_messaged').is(':checked')) {
            data['is_ma'] = 'Messaged';
        } else if ($('#edit_modal #is_ma_sent').is(':checked')) {
            data['is_ma'] = 'Sent';
        }
        data['pharmacy_store_id'] = menu_store_id;

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/clinical/rx-daily-transfers/${$status}/edit`,
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