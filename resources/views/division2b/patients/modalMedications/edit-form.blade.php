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
                        <div class="row g-3">
                            <input type="hidden" name="patient_id"  id="patient_id"> 
                            <input type="hidden" name="med_id"  id="med_id"> 
                            <div class="col-md-12">
                                <label for="medications" class="form-label">MEDICATIONS <i></i></label>
                                <textarea rows="3" name="medications" class="form-control" id="medications"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">QUANTITY </label>
                                <input type="text" name="quantity" class="form-control number_only" id="quantity" placeholder="">
                            </div>
                            <div class="col-md-6">
                                <label for="refills" class="form-label">REFILL </label>
                                <input type="text" name="refills" class="form-control number_only" id="refills" placeholder="">
                            </div>
                            <div class="col-md-12">
                                <label for="store_location" class="form-label">STORE</label>
                                <input type="text" name="store_location" class="form-control" id="store_location" placeholder="">
                            </div>
                            <div class="col-md-6">
                                <label for="prescribed_on" class="form-label">PRESCRIBED ON</label>
                                <input type="text" readonly name="prescribed_on" class="form-control datetimepicker" id="prescribed_on">
                            </div>
                            <div class="col-md-6">
                                <label for="prescribed_by" class="form-label">PRESCRIBED BY </label>
                                <input type="text" name="prescribed_by" class="form-control" id="prescribed_by" placeholder="">
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
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/divisiontwob/patients/medication_update",
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
                    table_medications.ajax.reload(null, false);
                    sweetAlert2(data.status, data.message);
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