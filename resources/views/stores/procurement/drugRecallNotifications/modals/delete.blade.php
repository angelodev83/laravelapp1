<div class="modal modal-md" style="display:none;" id="delete_form_modal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-red">Warning! Delete confirmation.</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
              <form name="delete_form"  id="delete_form"  >
                    <p>You are about to delete an Drug Recall Return record. This procedure is irreversible. </p>
                    <p id="order_id_text" class="fw-bold"><p>
                    <input id="item_id" type="hidden" />
                    <p>This will delete the following:</p>
                    <ul>
                        <li>Drug Recall Return Record</li>
                        <li>Any associated drug items and files inside</li>
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
    function ShowConfirmDeleteForm(id) {
        var btn = document.querySelector(`#notification-btn-${id}`);
        let array = JSON.parse(btn.dataset.array)

        $('#delete_form_modal').modal('show');
        $('#delete_form_modal #item_id').val(array.id);
        $('#delete_form_modal #order_id_text').html('Return ID: '+array.id+', Reference #: '+array.reference_number);
    }
  
  
    function DeleteOrder() {
          var data = {};
              $("#delete_form_modal #delete_btn").val('Deleting... please wait!').attr('disabled', 'disabled');
          data.id = $('#item_id').val();
          
          console.log(data);
          sweetAlertLoading();
          $.ajax({
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "DELETE",
              url: `/store/procurement/drug-recall-notifications/delete`,
              data: JSON.stringify(data),
              contentType: "application/json; charset=utf-8",
              dataType: "json",
              success: function(msg) {
                  Swal.fire({
                      position: 'center',
                      icon: msg.status,
                      title: msg.message,
                      showConfirmButton: false,
                      timer: 4000
                  });
                  $("#delete_form_modal #delete_btn").val('DELETE').removeAttr('disabled');
                  $('#delete_form_modal').modal('toggle');
                  table_drug_recall_notifications.ajax.reload(null, false);
              },error: function(msg) {
                handleErrorResponse(msg);
                  $("#delete_form_modal #delete_btn").val('DELETE').removeAttr('disabled');
                  console.log(msg.responseText);
              }
  
           });
   }
  
  </script>
  