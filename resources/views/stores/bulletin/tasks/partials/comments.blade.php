<div class="card">
    <div class="my-2 card-body">

        <!-- Popover content (hidden by default) -->
        <div id="task_comment_attachment_chip_card" style="display: none;"></div>

        <!-- row start -->
        <div class="row gy-1">
            <h6 class="mx-2 mb-1">Comments <i class="bx bx-message-detail me-2"></i></h6>

            <div class="mt-2 col-12 ms-2 me-1" >

                <div class="pb-2 mb-2 store-metrics pe-3" id="taskCommentsList"></div>

            </div>

            <div class="col-12">
                <div class="px-2 mt-1 row g-1">
                    <div class="col-md-12">
                        <textarea type="text" row="1" name="task_comment_text" id="task_comment_text" class="form-control" placeholder="Write a comment" autocomplete="off"></textarea>
                    </div>
                    <div class="col-md-12 pe-3">
                        <button id="task_comment_send_btn" class="px-3 btn btn-sm btn-primary" onclick="taskCommentSend(event)"><i class="fa fa-paper-plane me-2"></i>Send</button>
                        <button class="btn btn-sm btn-default ms-auto document_filename_link" onclick="taskCommentAttach(event)" title="Attach file(s) to send on comment"><i class="fa fa-paperclip"></i></button>
                        <input type="file" id="edit_task_modal_upload_documents" name="files[]" multiple hidden>
                        <span id="task_comment_attachment_chip_span"  title="See Comment Attachment(s)">
                            
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- row end -->

    </div>
</div>

<script>

    function taskCommentSend(event)
    {
        event.preventDefault();
        var formData = new FormData();
        let data = {
            comment: $('#task_comment_text').val(),
            task_id: $("#edit_task_modal #eid").val(),
            pharmacy_store_id: menu_store_id,
        };
        formData.append("data", JSON.stringify(data));

        var uploadFiles = $('#edit_task_modal_upload_documents').get(0).files;
        
        for (let i = 0; i < uploadFiles.length; i++) {
            formData.append("files[]", uploadFiles[i]);
            var kbSize = uploadFiles[i].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }

        $('#task_comment_send_btn').prop('disabled', true);
        $('#task_comment_send_btn').html(`<i class="fa-solid fa-spinner me-2"></i>Sending...`)

        // sweetAlertLoading();
          $.ajax({
              //laravel requires this thing, it fetches it from the meta up in the head
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "POST",
              url: `/store/bulletin/task/store-comment`,
              data: formData,
              contentType: false,
              processData: false,
              dataType: "json",
              success: function(res) {
                    $('#task_comment_text').val('');
                    $('#task_comment_send_btn').prop('disabled', false);
                    $('#task_comment_send_btn').html(`<i class="fa fa-paper-plane me-2"></i>Send`);

                    resetTaskCommentChipAttachment();
                    loadTaskComments();
                    loadTaskAttachments();

                    reloadDataTable();
                    // reloadAttachmentsDatatable();
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

    function resetTaskCommentChipAttachment()
    {
        $('#edit_task_modal_upload_documents').val(null);
        $('#edit_task_modal #task_comment_attachment_chip_span').empty();
    }

    function taskCommentAttach(event)
    {
        event.preventDefault();
        $('#edit_task_modal_upload_documents').click();
    }

</script>