<div class="modal" id="add_ticket_modal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title">Add Ticket</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
            <div class="form-body">
                <div class="row">
                    <form action="" method="POST" id="#ticket_add_form">
                        <div class="col-lg-12">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <input type="text" name="subject" id="subject" class="form-control" placeholder="Subject" autocomplete="off" required>
                                    <div class="invalid-feedback">
                                        Subject field is required
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label label for="priority_status_id" class="form-label">Due Date</label>
                                    <div class="input-group"> <span class="input-group-text" id="icon-due-date"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control" id="due_date" name="due_date" placeholder="yyyy-mm-dd" autocomplete="off" aria-describedby="icon-due-date">
                                    </div>
                                </div>

                                {{-- <div class="col-md-6">
                                    <label label for="employee_name" class="form-label">Assign To</label>
                                    <select class="form-select" data-placeholder="Select Employee.." name="assigned_to_employee_id" id="assigned_to_employee_id" title="Select Employee Name"></select>
                                </div> --}}

                                <div class="col-md-6">
                                    <label label for="category_id" class="form-label">Support Category <span class="text-danger">*</span></label>
                                    <select class="form-select" name="category_id" id="category_id" title="Select Category"></select>
                                </div>

                                <div class="col-md-3">
                                    <label label for="estatus_id" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" name="status_id" id="status_id" title="Select Status"></select>
                                </div>

                                <div class="col-md-3">
                                    <label label for="priority_status_id" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-select" name="priority_status_id" id="priority_status_id" title="Select Priority"></select>
                                </div>

                                <div class="col-md-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control tinymce-content" name="description" id="description" rows="4" placeholder="Description"></textarea>
                                </div>

                                <div class="col-md-12">
                                    <label for="documents" class="form-label">Attachments</label>
                                    <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                    <!-- <input id="documents" class="imageuploadify-file-general-class" name="documents[]" type="file" accept="*" multiple> -->
                                </div>

                                <div class="drop-area" id="dropArea">
                                    <p>Drag & Drop files here</p>
                                    <p>or</p>
                                    <input type="file" id="documents" multiple>
                                    <label for="fileInput" class="file-label">Select files</label>
                                </div>
                                <div id="fileList">
                                    <!-- Display dragged files here -->
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!--end row-->
            </div>
  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="validateForm()">Submit</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function validateForm()
    {
        let menu_store_id = {{request()->id}};

        let data = {};
        let flag = true;

        $(".form-control").removeClass("is-invalid");

        $('#add_ticket_modal input, #add_ticket_modal textarea, #add_ticket_modal select').each(function() {
            
            if(!$(`#${this.id}`)[0].checkValidity()) {
                $(`#${this.id}`).addClass("is-invalid");
                flag = false;
            }
            data[this.id] = this.value;

        });

        data['description'] = tinymce.get("description").getContent();
        data['pharmacy_store_id'] = menu_store_id;

        if(flag) {
            saveForm(data);
        }
    }

    function saveForm(data)
    {
        var formData = new FormData();
        var uploadFiles = $('#documents').get(0).files;
        
        for (let i = 0; i < uploadFiles.length; i++) {
            formData.append("files[]", uploadFiles[i]);
            var kbSize = uploadFiles[i].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }
        console.log("saving",data);
        formData.append("data", JSON.stringify(data));        
        // new Response(formData).text().then(console.log);
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/escalation/${data.pharmacy_store_id}/tickets/add`,
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                reloadDataTable();
                $(".imageuploadify-container").remove();    
                table_ticket.ajax.reload(null, false);
                sweetAlert2(data.status, data.message);
                $('#add_ticket_modal').modal('hide');
                    
            },error: function(msg) {
                handleErrorResponse(msg);
                if(msg.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                //general error
                console.log("Error");
                console.log(msg);
                $.each(msg.responseJSON.errors,function (key , val){
                    sweetAlert2('warning', 'Check field inputs.');
                    $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                    console.log(key);
                });
            }
        });
    }
 
</script>