<div class="modal" id="edit_ticket_modal" tabindex="-1">
    <div id="edit_ticket_modal_fullscreen" class="modal-dialog modal-fullscreen">
      <div class="modal-content">

        <div class="modal-header">

            <div class="col-md-4 menu_permission_update menu_permission_update_semi" style="display: none;">
                <div class="ms-3" id="assigned_to"></div>
            </div><div class="col-md-2 menu_permission_update menu_permission_update_semi" style="display: none;">
                <div id="show_due_date"></div>
            </div><div class="col-md-2 menu_permission_update menu_permission_update_semi" style="display: none;">
                <div class="me-3" id="priority"></div>
            </div><div class="col-md-3 menu_permission_update menu_permission_update_semi" style="display: none;">
                <div id="status"></div>
            </div><div class="col-md-1 menu_permission_update menu_permission_update_semi" style="display: none;">
                <button type="button" class="btn-close me-3 float-end" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        
            <h6 class="modal-title menu_permission_update menu_permission_update_all">Edit Ticket</h6>
            <button type="button" class="btn-close menu_permission_update menu_permission_update_all" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          
            <div class="form-body">
                <div class="row">
                    
                <form action="" method="POST" id="#edit_ticket_modal">
                    <div class="col-md-12">

                        
                        <div class="row g-0">

                            <!-- body info start -->
                            <div class="col-lg-8 col-md-12 col-sm-12">
                                <div id="ticket_edit_modal_body">
                                    <div class="row g-3">
                                        <input type="hidden" id="eid" name="id" value="">
                                        <div class="col-md-8">
                                            <label for="esubject" class="form-label">Subject <span class="text-danger">*</span></label>
                                            <input type="text" name="subject" id="esubject" class="form-control" placeholder="Subject" autocomplete="off">
                                            <div class="invalid-feedback">
                                                Subject field is required
                                            </div>
                                        </div>
                                        
                                        <!-- default editable starts -->
                                        <div class="col-md-4 menu_permission_update menu_permission_update_all">
                                            <label label for="priority_status_id" class="form-label">Due Date</label>
                                            <div class="input-group"> <span class="input-group-text" id="icon-due-date"><i class="fa fa-calendar"></i></span>
                                                <input type="text" class="form-control" id="edue_date" name="edue_date" placeholder="yyyy-mm-dd" autocomplete="off" aria-describedby="icon-due-date">
                                            </div>
                                        </div>

                                        <div class="col-md-6 menu_permission_update menu_permission_update_all">
                                            <label label for="eemployee_name" class="form-label">Reassign To</label>
                                            <select class="form-select" data-placeholder="Select Employee.." name="eassigned_to_employee_id" id="eassigned_to_employee_id" title="Select Employee Name"></select>
                                        </div>

                                        <div class="col-md-3 menu_permission_update menu_permission_update_all">
                                            <label label for="estatus_id" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select" name="status_id" id="estatus_id" title="Select Status"></select>
                                        </div>

                                        <div class="col-md-3 menu_permission_update menu_permission_update_all">
                                            <label label for="epriority_status_id" class="form-label">Priority <span class="text-danger">*</span></label>
                                            <select class="form-select" name="epriority_status_id" id="epriority_status_id" title="Select Priority"></select>
                                        </div>
                                        <!-- default editable ends -->

                                        <div class="col-md-12">
                                            <label for="ewatchers" class="form-label">Watchers</label>
                                            <div id="ewatchers"></div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <label for="edescription" class="form-label">Description</label>
                                            <textarea class="form-control tinymce-content" name="edescription" id="edescription" rows="8" placeholder="Description"></textarea>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <label for="edocuments" class="form-label">Attachments</label>
                                            <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                            <!-- <input id="edocuments" class="imageuploadify-file-general-class" name="edocuments[]" type="file" accept="*" multiple> -->
                                        </div>

                                        
                                        <div class="drop-area" id="dropArea">
                                            <p>Drag & Drop files here</p>
                                            <p>or</p>
                                            <input type="file" id="edocuments" multiple>
                                            <label for="fileInput" class="efile-label">Select files</label>
                                        </div>
                                        <div id="efileList">
                                            <!-- Display dragged files here -->
                                        </div>

                                        <!-- partial edit-detail starts -->
                                        @include('stores/escalation/tickets/partials/edit-details')
                                        <!-- partial edit-detail starts -->
                                    </div>
                                </div>
                            </div>
                            <!-- body info ends -->

                            <!-- partials comment sectiono starts -->
                            @include('stores/escalation/tickets/partials/edit-comments')
                            <!-- partials comment sectiono starts -->
                            
                        </div>
                    </div>
                </form>
            
                </div><!--end row-->
            </div>
            
        </div>

        <!-- partial edit-form-footer starts -->
        {{-- @include('stores/escalation/tickets/partials/edit-form-footer') --}}
        <!-- partial edit-form-footer starts -->
        
      </div>
    </div>
  </div>


<style>
    
</style>
  
<script>        
    function updateForm(){
        let data = {};
        let menu_store_id = {{request()->id}};
          $('.error_txt').remove();
  
          data['id'] = $("#eid").val();
          data['subject'] = document.getElementById("esubject").value;
          data['status_id'] = $('#estatus_id').find(":selected").val();
          data['priority_status_id'] = $('#epriority_status_id').find(":selected").val();
          data['description'] = tinymce.get("edescription").getContent();
          data['assigned_to_employee_id'] = $('#eassigned_to_employee_id').find(":selected").val();
          data['due_date'] = document.getElementById("edue_date").value;

        var formData = new FormData();
        var uploadFiles = $('#edocuments').get(0).files;
        
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

          console.log("updateing", data);
        //   new Response(formData).text().then(console.log);
          sweetAlertLoading();
          $.ajax({
              //laravel requires this thing, it fetches it from the meta up in the head
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "POST",
              url: `/store/escalation/${menu_store_id}/tickets/edit`,
              data: formData,
              contentType: false,
              processData: false,
              dataType: "json",
              success: function(data) {
                  if(data.errors){
                      $.each(data.errors,function (key , val){
                          sweetAlert2('warning', 'Check field inputs.');
                          $("#e"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                          console.log(key);
                      });
                  }
                  else{
                      reloadDataTable();
                      sweetAlert2('success', 'Record has been updated.');
                      $('#edit_ticket_modal').modal('hide');
                  }
              },error: function(msg) {
                handleErrorResponse(msg);
                    if(msg.status == 403) {
                        sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                    }
                  console.log("Error");
                  console.log(msg.responseText);
              }
  
  
          });
    }
</script>