<div class="modal modal-md" style="display:none;" id="delete_all_form_modal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-danger">Warning! Multiple Deletion confirmation.</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
              <form name="delete_all_form"  id="delete_all_form"  >
                    <p>You are about to delete all selected checkbox items. This procedure is irreversible. </p>
                    <p id="title_delete_all_id_text" class="fw-bold"><p>
                    <input id="reload" value="false" type="hidden" />
                    <input id="id" type="hidden" />
                    <p>This will delete all the following:</p>
                    <ul>
                        <li>Clinical Therapy change+reco Record</li>
                    </ul>
                   
             </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="delete_all_btn" onclick="saveDeleteAll()">Delete All Selected</button>
        </div>
      </div>
    </div>
  </div>
  
  
  
  
  <script>  
      function saveDeleteAll() {
        let id = $('#delete_all_form_modal #id').val();

        let _selectedIds = [id];
        if(!id) {
            var checkedIds = Object.keys(selectedIds).filter(function(id) {
                return selectedIds[id];
            });
            _selectedIds = checkedIds;
        }
        let data = {
            selectedIds: _selectedIds
        };

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `therapy-change-and-reco/delete-all`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    $('#delete_all_form_modal').modal('hide');
                    reloadDataTable(data);
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }
        });
    }
  
  </script>
  