<div class="modal modal-md" style="display:none;" id="delete_form_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-red">Warning! Delete confirmation.</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form name="delete_form"  id="delete_form"  >
                  <p>You are about to delete a Patient Notes record.</p>
                  <p> This procedure is irreversible. </p>
                  <p id="id_text" class="fw-bold"><p>
                  <input id="id" type="hidden" />
                  <input id="only_delete_file" type="hidden">
                  <p>This will delete the following:</p>
                  <ul>
                      <li>Patient Notes Record</li>
                      <li>Files</li>
                  </ul>
                 
           </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="delete_btn" onclick="DeleteOrder()">DELETE</button>
      </div>
    </div>
  </div>
</div>



<script>
  function ShowConfirmDeleteForm(id, file_id, only_delete_file) {
        $('#delete_form_modal').modal('show');
        $('#delete_form_modal #id').val(id);
        $('#delete_form_modal #only_delete_file').val(only_delete_file);
        $('#delete_form_modal #id_text').html('Notes ID: '+id);
  }


    function DeleteOrder() {
        var data = {};
            $("#delete_form_modal #delete_btn").val('Deleting... please wait!').attr('disabled', 'disabled');
        data.id = $('#delete_form_modal #id').val();
        data.delete_file_only = $('#delete_form_modal #only_delete_file').val();
        
        //console.log(data);
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/store/clinical/tebra-patients/note_delete",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(msg) {
              //success
              swal2(msg.status, msg.message);
              $("#delete_form_modal #delete_btn").val('DELETE').removeAttr('disabled');
              $('#delete_form_modal').modal('toggle');
              $("#edit_modal #chip_controller").hide();
              $("#edit_modal #fileDropArea").removeClass('d-none');
              table_notes.ajax.reload(null, false);
            },error: function(msg) {
              handleErrorResponse(msg);
              $("#delete_form_modal #delete_btn").val('DELETE').removeAttr('disabled');
              console.log(msg.responseText);
            }

        });
        
    }





</script>