<div class="modal modal-md" style="display:none;" id="delete_modal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-red">Warning! Delete confirmation.</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
              <form name="delete_form"  id="delete_form"  >
                    <p>You are about to remove a RTS record. This procedure is irreversible. </p>
                    <p id="rts_id" class="fw-bold"><p>
                    <input id="reload" value="false" type="hidden" />
                    <input id="id" type="hidden" />
                    <p>This will delete the following:</p>
                    <ul>
                        <li>RTS Record</li>
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
      function showConfirmDeleteForm(id) {
          $('#delete_modal').modal('show');
          $('#delete_modal #id').val(id);
          $('#delete_modal #rts_id').html('ID: '+id);
      }
  
  
      function saveDelete() {
          var data = {};
            $("#delete_modal #delete_btn").val('Deleting... please wait!').attr('disabled', 'disabled');
            data.id = $('#delete_modal #id').val();
          
            //console.log(data);
            sweetAlertLoading();
            $.ajax({
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "DELETE",
              url: `/store/operations/${menu_store_id}/rts/delete`,
              data: JSON.stringify(data),
              contentType: "application/json; charset=utf-8",
              dataType: "json",
              success: function(msg) {
              //success
                console.log(msg.status);
                sweetAlert2(msg.status, msg.message);
                $("#delete_modal #delete_btn").val('DELETE').removeAttr('disabled');
                $('#delete_modal').modal('toggle');
                
                dataTable_global.ajax.reload(null, false);
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
  