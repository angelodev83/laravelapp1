<div class="modal" id="edit_modal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title" id="modal_title"></h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">      
            <div class="row">
                <form action="" method="POST" id="#editForm">
                    <div class="col-lg-12">
                        <input type="hidden" id="id" name="id" value="">
                    
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label label for="task_name" class="form-label">TASK NAME</label>
                                <input type="text" readonly name="task_name" class="form-control" id="task_name" placeholder="">
                            </div>
                            <div class="col-md-4">
                                <label for="patient_name" class="form-label">PATIENT NAME</label>
                                <input type="text" name="patient_name" class="form-control" id="patient_name" placeholder="">
                            </div>
                            <div class="col-md-4">
                                <label for="patient_birthdate" class="form-label">PATIENT BIRTH DATE</label>
                                <input type="text" readonly name="patient_birthdate" class="form-control datepicker" id="patient_birthdate">
                            </div>
                            <div class="col-md-12 div_medications">
                                <label for="medications" class="form-label">MEDICATIONS <i> *Press ENTER for each Medications</i></label>
                                <textarea rows="3" name="medications" class="form-control" id="medications"></textarea>
                            </div>
                            <div class="col-md-4 div_outlier_type">
                                <label label for="outlier_type" class="form-label">OUTLIER TYPE</label>
                                <select class="form-select" data-placeholder="Select outlier type.." name="outlier_type" id="outlier_type"></select>
                            </div>
                            <div class="col-md-4 div_completed_date">
                                <label for="completed_date" class="form-label">COMPLETED DATE</label>
                                <input type="text" readonly name="completed_date" class="form-control datepicker" id="completed_date">
                            </div>
                            <div class="col-md-4 div_date_of_interaction">
                                <label for="date_of_interaction" class="form-label">DATE OF INTERACTION</label>
                                <input type="text" readonly name="date_of_interaction" class="form-control datepicker" id="date_of_interaction">
                            </div>
                            <div class="col-md-4 div_date_of_initiation">
                                <label for="date_of_initiation" class="form-label">DATE OF INITIATION</label>
                                <input type="text" readonly name="date_of_initiation" class="form-control datepicker" id="date_of_initiation">
                            </div>
                            <div class="col-md-4 div_side_effects">
                                <label for="side_effects" class="form-label">SIDE EFFECTS </label>
                                <input type="text" name="side_effects" class="form-control" id="side_effects" placeholder="">
                            </div>
                            <div class="col-md-4 div_date_side_effects">
                                <label for="date_side_effects" class="form-label">DATE SIDE EFFECTS</label>
                                <input type="text" readonly name="date_side_effects" class="form-control datepicker" id="date_side_effects">
                            </div>
                            <div class="col-md-4 div_date_follow_up">
                                <label for="date_follow_up" class="form-label">DATE FOLLOW UP</label>
                                <input type="text" readonly name="date_follow_up" class="form-control datepicker" id="date_follow_up">
                            </div>
                            <div class="col-md-6 div_recommended_vitamins">
                                <label for="recommended_vitamins" class="form-label">RECOMMENDED VITAMINS</label>
                                <input type="text" name="recommended_vitamins" class="form-control" id="recommended_vitamins" placeholder="">
                            </div>
                            <div class="col-md-4 div_pdc_rate">
                                <label for="pdc_rate" class="form-label">PDC RATE</label>
                                <input type="text" name="pdc_rate" class="form-control" id="pdc_rate" placeholder="">
                            </div>
                            <div class="col-md-12">
                                <label for="comments" class="form-label">COMMENTS</label>
                                <textarea rows="3" name="comments" class="form-control" id="comments"></textarea>
                            </div>
                        </div> 

                    </div>
                </form>
            </div><!--end row-->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="save_btn" onclick="updateForm()">Submit</button>
        </div>
        </div>
  </div>
</div>

<script>

    function updateForm(){
        $("#edit_modal #save_btn").val('Saving... please wait!');
        $("#edit_modal #save_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        let data = {};

        $('#edit_modal input, #edit_modal textarea, #edit_modal select').each(function() {
            data[this.id] = this.value;
        });
        //console.log(data);
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/divisionthree/task/update",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                $("#edit_modal #save_btn").val('Save');
                $("#edit_modal #save_btn").removeAttr('disabled');
                
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#edit_modal #"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    table_task.ajax.reload(null, false);
                    sweetAlert2('success', 'Record has been saved.');
                    $('#edit_modal').modal('hide');
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                $("#edit_modal #save_btn").val('Save');
                $("#edit_modal #save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }
</script>