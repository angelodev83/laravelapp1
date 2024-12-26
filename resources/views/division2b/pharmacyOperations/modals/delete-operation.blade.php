<div class="modal modal-md" style="display:none;" id="delete_operation_form_modal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-red">Warning! Delete confirmation.</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
              <form name="delete_form"  id="delete_form"  >
                    <p>You are about to remove a Pharmacy Operation record from operation. This procedure is irreversible. </p>
                    <p id="title_operation_id_text" class="fw-bold"><p>
                    <input id="reload" value="false" type="hidden" />
                    <input id="id" type="hidden" />
                    <p>This will delete the following:</p>
                    <ul>
                        <li>Pharmacy Operation Record</li>
                    </ul>
                   
             </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="delete_btn" onclick="DeleteOperation()">DELETE</button>
        </div>
      </div>
    </div>
  </div>
  
  
  
  
  <script>
      function ShowConfirmDeleteOperationForm(id) {
          $('#delete_operation_form_modal').modal('show');
          $('#delete_operation_form_modal #id').val(id);
          $('#title_operation_id_text').html('Pharmacy Operation: '+id);
      }
  
  
      function DeleteOperation() {
          var data = {};
              $("#delete_btn").val('Deleting... please wait!').attr('disabled', 'disabled');
            data.id = $('#delete_operation_form_modal #id').val();
          
          //console.log(data);
          sweetAlertLoading();
          $.ajax({
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "POST",
              url: "/admin/divisiontwob/pharmacy_operation/delete_operation",
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
                  $('#delete_operation_form_modal').modal('toggle');
                  
                  var reload = $('#delete_operation_form_modal #reload').val();
                  
                  if(reload === true || reload === "true") {
                    window.location.reload(true);
                  } else {
                    table_operation.ajax.reload(null, false);
                  }
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
  