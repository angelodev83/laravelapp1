<div class="modal modal-md" style="display:none;" id="delete_item_form_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-red">Warning! Item Delete confirmation.</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form name="delete_form"  id="delete_form"  >
                  <p>You are about to delete an Item on this Order. This procedure is irreversible. </p>
                  
                  <input id="item_id" type="hidden" />
                  
                  <p id="order_id_text" class="fw-bold lead"><p>
                  
                 
           </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="delete_btn" onclick="DeleteItem()">DELETE</button>
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
  function ShowConfirmDeleteItemForm(id,item_name) {
        $('#delete_item_form_modal').modal('show');
        $('#delete_item_form_modal #item_id').val(id);
        $('#delete_item_form_modal #order_id_text').html(item_name);
  }


  function DeleteItem() {
            var data = {};
            $("#delete_item_form_modal #delete_btn").val('Deleting... please wait!').attr('disabled', 'disabled');
            data.item_id = $('#item_id').val();
             
                $.ajax({
                      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                      type: "POST",
                      url: "/admin/item/delete_item_via_ajax",
                      data: JSON.stringify(data),
                      contentType: "application/json; charset=utf-8",
                      dataType: "json",
                      success: function(msg) {
                        
                        // Close the modal
                        $('#delete_item_form_modal').modal('hide');

                        // Remove the item row
                        $('#item_row_' + data.item_id).remove();
                         $("#delete_item_form_modal #delete_btn").val('DELETE').removeAttr('disabled');
                      },error: function(msg) {
                        handleErrorResponse(msg);
                         $("#delete_item_form_modal #delete_btn").val('DELETE').removeAttr('disabled');
                          console.log(msg.responseText);
                      }

            });
               
 }





</script>
