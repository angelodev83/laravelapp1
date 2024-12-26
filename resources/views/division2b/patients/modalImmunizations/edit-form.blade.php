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
                            <input type="hidden" name="id"  id="id"> 
                            <input type="hidden" name="patient_id"  id="patient_id"> 
                            
                            <div class="col-md-12">
                                <label for="name" class="form-label">NAME</label>
                                <input type="text" name="name" class="form-control" id="name">
                            </div>
                            <div class="col-md-6">
                                <label for="schedule" class="form-label">SCHEDULE</label>
                                <input type="text" readonly name="schedule" class="form-control datetimepicker" id="schedule">
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
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/divisiontwob/patients/immunization_update",
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
                    table_immunizations.ajax.reload(null, false);
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