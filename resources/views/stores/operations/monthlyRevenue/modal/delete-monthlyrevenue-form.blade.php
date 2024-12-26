<div class="modal modal-md" style="display:none;" id="delete_form_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-red">Warning! Delete confirmation.</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form name="delete_form"  id="delete_form"  >
                  <p>You are about to delete an Monthly Revenue record. This procedure is irreversible. </p>
                  <p id="order_id_text" class="fw-bold"><p>
                  <input id="id" type="hidden" />
                  <input id="only_delete_file" type="hidden">
                  <p>This will delete the following:</p>
                  <ul>
                      <li>Monthly Reveneu Record</li>
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

<div class="modal modal-md" style="display:none;"  id="delete_success_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-red">Delete succesful</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
             <p class="fw-bold">Order record successfully deleted.<p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



<script>
  function ShowConfirmDeleteForm(id, file_id, only_delete_file ) {
        $('#delete_form_modal').modal('show');
        $('#delete_form_modal #id').val(id);
        $('#delete_form_modal #only_delete_file').val(only_delete_file)
        $('#delete_form_modal #order_id_text').html('Monthly Revenue ID: '+id+' File ID: '+file_id);
  }


    function DeleteOrder() {
        var data = {};
            $("#delete_form_modal #delete_btn").val('Deleting... please wait!').attr('disabled', 'disabled');
        data.id = $('#delete_form_modal #id').val();
        data.delete_file_only = $('#only_delete_file').val();
        
        
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/operations/${menu_store_id}/pgr/delete`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(msg) {
                sweetAlert2(msg.status,msg.message);
                $("#delete_form_modal #delete_btn").val('DELETE').removeAttr('disabled');
                $('#delete_form_modal').modal('toggle');
                $("#chip_controller").hide();
                $("#efile").show();
                table_monthlyRevenue.ajax.reload(null, false);
            },error: function(msg) {
              handleErrorResponse(msg);
                $("#delete_form_modal #delete_btn").val('DELETE').removeAttr('disabled');
                console.log(msg.responseText);
            }

        });
        
    }





</script>
