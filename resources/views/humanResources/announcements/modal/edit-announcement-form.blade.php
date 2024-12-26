<div class="modal" id="updateAnnouncement_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">

        <div class="modal-header">
            <h6 class="modal-title">Edit Announcement</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          
            <div class="mt-4 form-body">
                <div class="row">
                    
                <form action="" method="POST" id="#announcementUpdateForm">
                    <div class="col-lg-12">
                        <div class="row g-3">
                            <input type="hidden" id="eid" name="id" value="">
                            <div class="col-md-12">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" name="subject" id="esubject" class="form-control" placeholder="Title">
                            </div>

                            <div class="col-md-12">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control tinymce-content" name="econtent" id="econtent" rows="8" placeholder="Content"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            
                </div><!--end row-->
            </div>
            
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="updateForm()">Submit</button>
        </div>
      </div>
    </div>
  </div>
  
  <script>
        
      function updateForm(){
          $('.error_txt').remove();
          let data = {};
  
          data['id'] = $("#eid").val();
          data['subject'] = document.getElementById("esubject").value;
          data['content'] = tinymce.get("econtent").getContent();
          
          sweetAlertLoading();
          $.ajax({
              //laravel requires this thing, it fetches it from the meta up in the head
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "POST",
              url: "/admin/human_resources/update_announcement",
              data: JSON.stringify(data),
              contentType: "application/json; charset=utf-8",
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
                      table_announcement.ajax.reload(null, false);
                      sweetAlert2('success', 'Record has been updated.');
                      $('#updateAnnouncement_modal').modal('hide');
                      //window.location.reload(true);
                  }
              },error: function(msg) {
                handleErrorResponse(msg);
                if(msg.status == 403) {
                        sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                    }
                  $("#add_user_btn").val('Save');
                  $("#add_user_btn").removeAttr('disabled');
                  //general error
                  console.log("Error");
                  console.log(msg.responseText);
              }
  
  
          });
      }
  </script>