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
                                <textarea rows="3" name="diagnosis" class="form-control" id="diagnosis" placeholder=""></textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="reason" class="form-label">Reason</label>
                                <textarea rows="3" name="reason" class="form-control" id="reason" placeholder=""></textarea>
                            </div>
                             <div class="col-md-12">
                                <label for="status_id" class="form-label">Call Status</label>
                                <select class="form-select" name="status_id" id="status_id"></select>
                            </div>
                            <div class="col-md-12">
                                <label for="care_goals" class="form-label">Care Goals</label>
                                <input type="text" name="care_goals" class="form-control" id="care_goals">
                            </div>
                            <div class="col-md-12">
                                <label for="biller" class="form-label">Biller</label>
                                <input type="text" name="biller" class="form-control" id="biller">
                            </div>
                            <div class="col-md-12">
                                <label for="profits" class="form-label">Profits</label>
                                <input type="number" name="profits" class="form-control" id="profits">
                            </div>
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
        $("#add_modal #save_btn").val('Saving... please wait!');
        $("#add_modal #save_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        
        let data = {};

        $('#add_modal input, #add_modal textarea, #add_modal select').each(function() {
            data[this.id] = this.value;
        });
        data['pharmacy_store_id'] = menu_store_id;

        // console.log(data);
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/clinical/kpi/add`,
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