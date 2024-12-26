<div class="modal modal-md" style="display:none;" id="delete_form_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-red">Warning! Delete confirmation.</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form name="delete_form"  id="delete_form"  >
                  <p>You are about to delete a prescription document. This procedure is irreversible. </p>
                  <p id="file_name" class="fw-bold"><p>
                  <input id="file_id" type="hidden" />
                

           </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="delete_btn" onclick="DeleteFile()">DELETE</button>
      </div>
    </div>
  </div>
</div>



<div class="modal modal-md" style="display:none;" id="delete_success_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-red">Delete succesful</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
             <p class="fw-bold">Patient record successfully deleted.<p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-md" style="display:none;" id="multiple_delete_success_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-red">Delete succesful</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
             <p class="fw-bold">Patient records successfully deleted.<p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div class="modal modal-md" style="display:none;" id="multiple_delete_form_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-red">Warning! Multiple Records Delete</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form name="delete_form"  id="delete_form"  >
                  <p>You are about to delete multiple patient records. This procedure is irreversible. </p>
                  <p id="patient_name" class="fw-bold"><p>
                  <input id="patient_id" type="hidden" />
                
                  <p>This will delete the following patient with IDs</p>
                  <p id="selectedPatientsList"></p>
         
           </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="delete_btn" onclick="DeleteMultiplePatients()">DELETE</button>
      </div>
    </div>
  </div>
</div>

<script>
  function ShowConfirmDeleteForm(filename,id) {
        $('#delete_form_modal').modal('show');
        $('#delete_form #file_id').val(id);
        $('#file_name').html(filename);
  }


  function DeleteFile(id) {
                var data = {};
                 $("#delete_btn").val('Deleting... please wait!').attr('disabled', 'disabled');
                data.file_id = $('#file_id').val();
             
                $.ajax({
                      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                      type: "POST",
                      url: "/admin/file/delete_file_via_ajax",
                      data: JSON.stringify(data),
                      contentType: "application/json; charset=utf-8",
                      dataType: "json",
                      success: function(msg) {
                        //success
                        window.location.reload(true);
                      },error: function(msg) {
                         $("#delete_btn").val('DELETE').removeAttr('disabled');
                         handleErrorResponse(msg);
                          console.log(msg.responseText);
                      }

            });
               
 }





</script>
