<div class="modal" id="edit_ticket_modal" tabindex="-1">
    <div id="edit_ticket_modal_fullscreen" class="modal-dialog modal-fullscreen">
      <div class="modal-content">

        <div class="py-2 bg-white modal-header ps-3">
            <div id="codeDiv"></div>
            <div id="copiedCodeAlert"></div>
            <button type="button" class="btn-close menu_permission_update menu_permission_update_all" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          
            <div class="form-body">
                <div class="row">
                    
                <form action="" method="POST" id="#edit_ticket_modal">
                    <div class="col-md-12">

                        
                        <div class="row g-4">

                            <!-- details info start -->
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                @include('stores/escalation/tickets/partials/details')
                                @include('stores/escalation/tickets/partials/tracking')
                            </div>
                            <!-- details info end -->

                            <!-- attachments info start -->
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                @include('stores/escalation/tickets/partials/attachments')
                            </div>
                            <!-- attachments info end -->

                            <!-- comments info start -->
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                @include('stores/escalation/tickets/partials/comments')
                            </div>
                            <!-- comments info end -->

                           
                            
                        </div>
                    </div>
                </form>
            
                </div><!--end row-->
            </div>
            
        </div>

        <!-- footer starts -!->
        <div class="py-1 modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="updateForm()"><i class="fa fa-save me-2"></i> Save Changes</button>
        </div>
        <!-!- footer end -->
        
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