<div class="modal modal-md" style="display:none;" id="delete_document_form_modal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-red">Warning! Delete confirmation.</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
              <form name="delete_form"  id="delete_form"  >
                    <p>You are about to remove a Operations > Meetings document record. This procedure is irreversible. </p>
                    <p id="title_document_id_text" class="fw-bold"><p>
                    <input id="reload" value="false" type="hidden" />
                    <input id="id" type="hidden" />
                    <p>This will delete the following:</p>
                    <ul id="description-ul">
                        <li>P&P Document Record</li>
                    </ul>
                   
             </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="delete_btn" onclick="saveDelete()">DELETE</button>
        </div>
      </div>
    </div>
  </div>
  
  
  
  
  <script>
      function ShowConfirmDeleteForm(id) {
        var btn = document.querySelector(`#btn-knowledge-base-delete-${id}`);
        let data = btn.dataset;
        let subject = data.subject;
        let description = data.description;
          $('#delete_document_form_modal').modal('show');
          $('#delete_document_form_modal #id').val(id);
          $('#title_document_id_text').html(`${subject}: ${id}`);
          $('#description-ul').html(`<li>${description}</li>`);
      }
  
  
      function saveDelete() {
          var data = {};
              $("#delete_btn").val('Deleting... please wait!').attr('disabled', 'disabled');
            data.id = $('#delete_document_form_modal #id').val();
          
          //console.log(data);
          sweetAlertLoading();
          $.ajax({
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "DELETE",
              url: `/store/procurement/pharmacy/drug-orders/delete_file`,
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
                  $("#delete_btn").val('DELETE').removeAttr('disabled');
                  $('#delete_document_form_modal').modal('toggle');
                  
                    table_document.ajax.reload(null, false);
                    loadRecentFiles(34);
              },error: function(msg) {
                handleErrorResponse(msg);
                    if(msg.status == 403) {
                        sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                    }
                  $("#delete_btn").val('DELETE').removeAttr('disabled');
                  console.log(msg.responseText);
              }
  
          });
          
      }
  
  </script>
  