<div class="modal" id="add_modal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title" id="modal_title"></h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">      
            <div class="row">
                <form action="" method="POST" id="#addForm">
                    <div class="col-lg-12">
                    
                        <div class="row g-3">
                            <input type="hidden" name="patient_id"  id="patient_id"> 
                            
                            <div class="col-md-12">
                                <label for="name" class="form-label">TITLE</label>
                                <input type="text" name="name" class="form-control datetimepicker" id="name">
                            </div>
                            <div class="col-md-12">
                                <label for="body" class="form-label">BODY</label>
                                <textarea rows="3" name="body" class="form-control" id="body" placeholder=""></textarea>                                    
                            </div>
                            <div class="col-md-12">
                                <label for="file" class="form-label">File</label>
                                <div id="fileDropArea" class="p-5 text-center border d-flex border-3 align-items-center justify-content-center">
                                    <span class="fw-bold lead" id="droparea_text"></span>
                                </div>
                                <input type="file" name="file" class="form-control" id="file">
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

        var formData = new FormData();
        formData.append('name', $("#add_modal #name").val());
        formData.append('body', $("#add_modal #body").val());
        formData.append('patient_id', $("#add_modal #patient_id").val());
        formData.append('file', $('#add_modal input[type=file]')[0].files[0]);
        //new Response(formData).text().then(console.log);
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/store/clinical/tebra-patients/note_store",
            data: formData,
            contentType: false, 
            processData: false,
            dataType: "json",
            success: function(data) {
                $("#add_modal #save_btn").val('Save');
                $("#add_modal #save_btn").removeAttr('disabled');
                
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#add_modal #"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    table_notes.ajax.reload(null, false);
                    sweetAlert2(data.status, data.message);
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