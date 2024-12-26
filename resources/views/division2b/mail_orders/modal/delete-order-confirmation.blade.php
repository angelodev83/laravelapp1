<div class="modal modal-md" style="display:none;" id="delete_form_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-red">Warning! Delete confirmation.</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form name="delete_form"  id="delete_form"  >
                  <p>You are about to delete an Order record. This procedure is irreversible. </p>
                  <p id="order_id_text" class="fw-bold"><p>
                  <input id="order_id" type="hidden" />
                  <p>This will delete the following:</p>
                  <ul>
                      <li>Order Record</li>
                      <li>Any associated medications inside the order</li>
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
  function ShowConfirmDeleteForm(id,order_number) {
        $('#delete_form_modal').modal('show');
        $('#delete_form #order_id').val(id);
        $('#order_id_text').html('Order ID: '+order_number);
  }


  function DeleteOrder() {
                var data = {};
                 $("#delete_btn").val('Deleting... please wait!').attr('disabled', 'disabled');
                data.order_id = $('#delete_form #order_id').val();
            
                $.ajax({
                      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                      type: "POST",
                      url: "/admin/order/delete_order_via_ajax",
                      data: JSON.stringify(data),
                      contentType: "application/json; charset=utf-8",
                      dataType: "json",
                      success: function(msg) {
                        //success
                         $("#delete_btn").val('DELETE').removeAttr('disabled');
                         $('#delete_form_modal').modal('hide');
                        $('#orders_table').DataTable().ajax.reload();
                      },error: function(msg) {
                        handleErrorResponse(msg);
                         $("#delete_btn").val('DELETE').removeAttr('disabled');
                          console.log(msg.responseText);
                      }

            });
               
 }





</script>
