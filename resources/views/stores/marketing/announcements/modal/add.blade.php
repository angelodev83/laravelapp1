<div class="modal" id="addAnnouncement_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title">Add Announcement</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
            <div class="mt-4 form-body">
                <div class="row">
                    <form action="" method="POST" id="#announcementAddForm">
                        <div class="col-lg-12">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" name="subject" id="subject" class="form-control" placeholder="Title">
                                </div>

                                <div class="col-md-12">
                                    <label for="content" class="form-label">Content</label>
                                    <textarea class="form-control tinymce-content" name="content" id="content" rows="8" placeholder="Content"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!--end row-->
            </div>
  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="saveForm()">Submit</button>
        </div>
      </div>
    </div>
  </div>

  <script>
      function saveForm(){
  
          let data = {};
          let menu_store_id = {{request()->id}};
  
          $('#addAnnouncement_modal input, #addAnnouncement_modal textarea, #addAnnouncement_modal select').each(function() {
              data[this.id] = this.value;
          });

          data['content'] = tinymce.get("content").getContent();
          
          sweetAlertLoading();
          $.ajax({
              //laravel requires this thing, it fetches it from the meta up in the head
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "POST",
              url: `/store/marketing/${menu_store_id}/announcement/add`,
              data: JSON.stringify(data),
              contentType: "application/json; charset=utf-8",
              dataType: "json",
              success: function(data) {
                  
                  table_announcement.ajax.reload(null, false);
                  sweetAlert2(data.status, data.message);
                  $('#addAnnouncement_modal').modal('hide');
                  
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