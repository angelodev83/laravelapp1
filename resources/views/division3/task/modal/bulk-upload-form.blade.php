<div class="modal" id="bulkUpload_modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title" id="modal_title"></h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
                    <div class="row">
                        <form action="" method="POST" id="bulkUploadForm" enctype="multipart/form-data">
                        <div class="col-lg-12">
                            
                                <div class="row g-3">
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
            <button type="button" class="btn btn-primary" id="save_btn" onclick="bulkForm()">Submit</button>
        </div>
        </div>
  </div>
</div>

<script>
    function bulkForm(){
        $("#bulkUpload_modal #save_btn").val('Saving... please wait!');
        $("#bulkUpload_modal #save_btn").attr('disabled','disabled');
        $('.error_txt').remove();

    
        sweetAlertLoading();
        var formData = new FormData();
        formData.append('name', $("#name").val());
        formData.append('file', $('input[type=file]')[0].files[0]);
        
        //new Response(formData).text().then(console.log);
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/divisionthree/task/upload_csv",
            data: formData,
            contentType: false, 
            processData: false,
            dataType: "json",
            success: function(data) {
                $("#bulkUpload_modal #save_btn").val('Save');
                $("#bulkUpload_modal #save_btn").removeAttr('disabled');
                
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    table_task.ajax.reload(null, false);
                    sweetAlert2('success', 'Record has been saved.');
                    $('#bulkUpload_modal').modal('hide');
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                $("#bulkUpload_modal #save_btn").val('Save');
                $("#bulkUpload_modal #save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }
</script>