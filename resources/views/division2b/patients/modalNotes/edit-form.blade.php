<div class="modal" id="edit_modal" tabindex="-1">
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
                            <input type="hidden" name="note_id"  id="note_id"> 
                            <input type="hidden" name="file_id"  id="file_id"> 
                            
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
                                <div class="chip chip-lg form-control" id="chip_controller">
                                    <span class="file_name"></span><span class="closebtn" onclick="showDeleteFileOnly();">×</span>
                                </div>
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
            <button type="button" class="btn btn-primary" id="save_btn" onclick="updateForm()">Submit</button>
        </div>
        </div>
  </div>
</div>

<script>
    function showDeleteFileOnly(){
        ShowConfirmDeleteForm($("#edit_modal #note_id").val(), $("#edit_modal #file_id").val(),1);
    }
    
    function updateForm(){
        $("#edit_modal #save_btn").val('Saving... please wait!');
        $("#edit_modal #save_btn").attr('disabled','disabled');
        $('.error_txt').remove();

        var formData = new FormData();
        formData.append('name', $("#edit_modal #name").val());
        formData.append('body', $("#edit_modal #body").val());
        formData.append('note_id', $("#edit_modal #note_id").val());
        formData.append('file_id', $("#edit_modal #file_id").val());
        formData.append('file', $('#edit_modal input[type=file]')[0].files[0]);
        //new Response(formData).text().then(console.log);
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/divisiontwob/patients/note_update",
            data: formData,
            contentType: false, 
            processData: false,
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
                    table_notes.ajax.reload(null, false);
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