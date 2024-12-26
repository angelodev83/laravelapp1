<div class="modal modal-md" style="display:none;" id="delete_store_document_form_modal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-red">Warning! Delete confirmation.</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
              <form name="delete_store_document_form"  id="delete_store_document_form"  >
                    <p>You are about to remove a Document record from document. This procedure is irreversible. </p>
                    <p id="title_document_id_text" class="fw-bold"><p>
                    <input id="reload" value="false" type="hidden" />
                    <input id="delete_store_document_id" type="hidden" />
                    <input id="delete_store_document_parent_id" type="hidden" />
                    <input id="delete_store_document_url" type="hidden" />
                    <p>This will delete the following:</p>
                    <ul>
                        <li>Document Record</li>
                    </ul>
                   
             </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="delete_store_document_btn" onclick="saveDeleteStoreDocument()">DELETE</button>
        </div>
      </div>
    </div>
  </div>
  
  
  
  
  <script>
      function clickDeleteStoreDocumentBtn(id, url, parent_id = null) {
          $('#delete_store_document_form_modal').modal('show');
          $('#delete_store_document_form_modal #delete_store_document_id').val(id);
          $('#delete_store_document_form_modal #delete_store_document_url').val(url);
          $('#delete_store_document_form_modal #delete_store_document_parent_id').val(parent_id);
          $('#title_document_id_text').html('Document: '+id);
      }
  
  
      function saveDeleteStoreDocument() {
          var data = {};
              $("#delete_store_document_btn").val('Deleting... please wait!').attr('disabled', 'disabled');
            data.id = $('#delete_store_document_form_modal #delete_store_document_id').val();

        
        let _url = $('#delete_store_document_form_modal #delete_store_document_url').val();
        let _id = $('#delete_store_document_form_modal #delete_store_document_id').val();
        let _parent_id = $('#delete_store_document_form_modal #delete_store_document_parent_id').val();
          
          //console.log(data);
          sweetAlertLoading();
          $.ajax({
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "POST",
              url: _url,
              data: JSON.stringify(data),
              contentType: "application/json; charset=utf-8",
              dataType: "json",
              success: function(msg) {
              //success
                  Swal.fire({
                      position: 'center',
                      icon: msg.status,
                      title: msg.message,
                      showConfirmButton: false,
                      timer: 4000
                  });
                  $("#delete_store_document_btn").val('DELETE').removeAttr('disabled');
                  $('#delete_store_document_form_modal').modal('toggle');
                  
                  emitDeleteStoreDocumentFunction(_id, _parent_id, msg)
              },error: function(msg) {
                    if(msg.status == 403) {
                        sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                    }
                    handleErrorResponse(msg);
                  $("#delete_store_document_btn").val('DELETE').removeAttr('disabled');
                  console.log(msg.responseText);
              }
  
          });
          
      }
  
  </script>
  