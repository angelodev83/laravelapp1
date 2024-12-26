<div class="modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h6 class="modal-title">Upload Document(s) Form</h6>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">      
              <div class="row">
                  <form action="" method="POST" id="#addForm">
                    <input type="text" name="page_id" class="form-control" id="page_id" hidden>
                      <div class="col-lg-12">
                          <div class="row g-3">
                              <div class="col-md-12">
                                  <label for="folder" class="form-label">Folders</label>
                                  <select class="form-select" name="folder_id" id="folder_id" required>
                                      <option value="">--Create New Folder--</option>
                                      @foreach ($folders as $folder)
                                        <option value="{{$folder->id}}">{{$folder->name}}</option>
                                      @endforeach
                                  </select>
                              </div>
                              <div class="col-md-12" id="for-new-folder">
                                  <label for="new_folder" class="form-label">New Folder Name</label>
                                  <input type="text" name="new_folder" class="form-control" id="new_folder" required>
                                  <div class="invalid-feedback">
                                    Folder name is required
                                </div>
                              </div>
                              <div class="col-md-12">
                                  <label for="documents" class="form-label">Attachments</label>
                                  <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                  <!-- <input id="file" class="imageuploadify-file-general-class" name="file" type="file" accept="*" multiple> -->
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
    function clickUploadBtn() {
        $('#add_modal #page_id').val(page_id);

        $(`#add_modal #folder_id`).val(folder_id);

        if(!folder_id) {
            $('#add_modal #for-new-folder').show();
        } else {
            $('#add_modal #for-new-folder').hide();
        }

        let fileInput = $('<input/>', {
            id: 'file',
            class: 'imageuploadify-file-general-class',
            name: 'file',
            type: 'file',
            accept: '*',
            multiple: ''
        });
        $('#add_modal #for-file').html(fileInput); 
        $('#add_modal #file').imageuploadify();
        $("#add_modal .imageuploadify-container").remove();
        $('#add_modal .imageuploadify-message').html('Drag&Drop Your File(s) Here To Upload');     
        $('#add_modal').modal('show');
    }
    
    function saveForm(){
        $(".form-control").removeClass("is-invalid");
        if ($('#add_modal #folder_id').val() == '' && $('#add_modal #new_folder').val() == '') {
            if(!$(`#add_modal #new_folder`)[0].checkValidity()) {
                $(`#add_modal #new_folder`).addClass("is-invalid");
                return;
            }
        }

        $("#add_modal #save_btn").val('Saving... please wait!');
        $("#add_modal #save_btn").attr('disabled','disabled');
        
        let formData = new FormData();
        let data = {};

        $('#add_modal input, #add_modal textarea, #add_modal select').each(function() {
            data[this.id] = this.value;
        });
        data['pharmacy_store_id'] = null;
        data['page_code'] = page_code;
      
        let uploadFiles = $('#file').get(0).files;
        for (let i = 0; i < uploadFiles.length; i++) {
            formData.append("files[]", uploadFiles[i]);
            let kbSize = uploadFiles[i].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }

        formData.append("data", JSON.stringify(data));
        // console.log("data",data)
        // new Response(formData).text().then(console.log);
        sweetAlertLoading();

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `${page_id}/add`,
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

                    // let refresh = false;
                    // if($('#add_modal #folder_id').val() === '') {
                    //     refresh = true;
                    // }

                    // reloadDataTable(true);

                    // Example usage: Reload page with additional parameters
                    let additionalParams = {
                        folder_id: data.data.folder_id
                    };

                    reloadPageWithParams(additionalParams);
                    
                    sweetAlert2(data.status, data.message);
                    $('#add_modal').modal('hide');
                }
            },error: function(msg) {

                $("#add_modal #save_btn").val('Save');
                $("#add_modal #save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                handleErrorResponse(msg);
                console.log(msg.responseText);
            }
        });
    }
</script>