<div class="modal modal-md" style="display:none;" id="delete_form_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-red">Warning! Delete confirmation.</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form name="delete_form"  id="delete_form"  >
                  <p>You are about to delete a Patient Medication record. This procedure is irreversible. </p>
                  <p id="title_id_text" class="fw-bold"><p>
                  <input id="id" type="hidden" />
                  <p>This will delete the following:</p>
                  <ul>
                      <li>Patient Medication Record</li>
                  </ul>
                 
           </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="delete_btn" onclick="deleteForm()">DELETE</button>
      </div>
    </div>
  </div>
</div>




<script>
    function ShowConfirmDeleteForm(id) {
        $('#delete_form_modal').modal('show');
        $('#delete_form_modal #id').val(id);
        $('#title_id_text').html('Patient Medication: '+id);
    }


    function deleteForm() {
        var data = {};
            $("#delete_btn").val('Deleting... please wait!').attr('disabled', 'disabled');
        data.id = $('#delete_form_modal #id').val();
        
        //console.log(data);
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/store/clinical/tebra-patients/medication_delete",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(msg) {
                swal2(msg.status, msg.message);
                $("#delete_btn").val('DELETE').removeAttr('disabled');
                $('#delete_form_modal').modal('toggle');
                table_medications.ajax.reload(null, false);
            },error: function(msg) {
              handleErrorResponse(msg);
                $("#delete_btn").val('DELETE').removeAttr('disabled');
                console.log(msg.responseText);
            }

        });
        
    }

</script>
