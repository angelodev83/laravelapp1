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
                                <label for="name" class="form-label">Title</label>
                                <input type="text" name="name" class="form-control" id="name">
                            </div>
                            <div class="col-md-12">
                                <label for="caption" class="form-label">Caption</label>
                                <textarea id="caption" name="caption" class="form-control" rows="3" placeholder=""></textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control tinymce-content" name="content" id="content" rows="4" placeholder="Content"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label label for="status_id" class="form-label">Type</label>
                                <select class="form-select" data-placeholder="Select.." name="status_id" id="status_id"></select>
                            </div>
                            <div class="col-md-12" id="url_holder">
                                <label for="url" class="form-label">URL</label>
                                <input type="text" name="url" class="form-control" id="url">
                            </div>
                        
                            <div class="col-md-12">
                                <label for="documents" class="form-label">Thumbnail</label>
                                <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                <div id="for-file"></div>
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
        let formData = new FormData();
        let data = {};

        $('#add_modal input, #add_modal textarea, #add_modal select').each(function() {
            data[this.id] = this.value;
        });
        data['menu_store_id'] = menu_store_id;
        data['content'] = tinymce.get("content").getContent();

        // if ($("#add_modal #file")[0].files.length !== 0) {
        //     formData.append('file', $('#add_modal #file')[0].files[0]);
        // }
        let uploadFile = $('#add_modal #file').get(0).files[0];
        if (!uploadFile) {
            sweetAlert2('warning', 'Image is required!');
            return;
        }
        formData.append("file", uploadFile);
        let kbSize = uploadFile.size/1024;
        if(kbSize > 100000) {
            sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
            return;
        }
        
        console.log(data);
        formData.append("data", JSON.stringify(data));
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/marketing/${menu_store_id}/news-and-events/add`,
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#add_modal #save_btn").val('Save');
                $("#add_modal #save_btn").removeAttr('disabled');
                data = JSON.parse(data);
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#add_modal #"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    // reloadDataTable();
                    loadNews();
                    Swal.close();
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