<div class="modal" id="bulk_modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title">Update Files Form</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">      
            <div class="row">
                <form action="" method="POST" id="#addForm">
                    <div class="col-lg-12">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <input type="hidden" id="id">
                                <label for="documents" class="form-label">Attachments</label>
                                <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                <!-- <input id="file" name="file" type="file" accept="*" multiple> -->
                                <div id="for-file"></div>
                            </div>
                        </div> 
                    </div>
                </form>
            </div><!--end row-->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="save_btn" onclick="fileSaveForm()">Submit</button>
        </div>
    </div>
  </div>
</div>

<script>
    function fileSaveForm(){
        
        $("#bulk_modal #save_btn").val('Saving... please wait!');
        $("#bulk_modal #save_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        let formData = new FormData();
        let data = {};

        $('#bulk_modal input, #bulk_modal textarea, #bulk_modal select').each(function() {
            data[this.id] = this.value;
        });
        data['menu_store_id'] = menu_store_id;

        // if ($("#bulk_modal #file")[0].files.length !== 0) {
        //     formData.append('file', $('#bulk_modal #file')[0].files[0]);
        // }
        let uploadFiles = $('#bulk_modal #file').get(0).files;
        for (let i = 0; i < uploadFiles.length; i++) {
            formData.append("file[]", uploadFiles[i]);
            let kbSize = uploadFiles[i].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }

        formData.append("data", JSON.stringify(data));
        // new Response(formData).text().then(console.log);
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/eod-register-report/${menu_store_id}/register/file-upload`,
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#bulk_modal #save_btn").val('Save');
                $("#bulk_modal #save_btn").removeAttr('disabled');
                data = JSON.parse(data);
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    reloadFileDataTable();
                    sweetAlert2('success', 'Record has been saved.');
                    $('#bulk_modal').modal('hide');
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                $("#bulk_modal #save_btn").val('Save');
                $("#bulk_modal #save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }
</script>