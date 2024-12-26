<div class="modal modal-md" style="display:none;" id="archive_ticket_form_modal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-warning">Warning! Archive confirmation.</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
              <form name="archive_form"  id="archive_form"  >
                    <p>You are about to archive a Ticket record. This procedure is reversible. </p>
                    <p id="title_archive_ticket_id_text" class="fw-bold"><p>
                    <input id="reload" value="false" type="hidden" />
                    <input id="id" type="hidden" />
                    <p>This will archive the following:</p>
                    <ul>
                        <li>Ticket Record</li>
                    </ul>
                   
             </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-warning" id="archive_btn" onclick="saveArchive()">ARCHIVE</button>
        </div>
      </div>
    </div>
  </div>
  
  
  
  
  <script>
      function clickArchiveBtn(id) {
          $('#archive_ticket_form_modal').modal('show');
          $('#archive_ticket_form_modal #id').val(id);
          $('#title_archive_ticket_id_text').html('Ticket: '+id);
      }
  
      function saveArchive() {
        let id = $('#archive_ticket_form_modal #id').val();

        let _selectedIds = [id];
        let _unselectedIds = [];
        if(!id) {
            var checkedIds = Object.keys(selectedIds).filter(function(id) {
                return selectedIds[id];
            });
            _selectedIds = checkedIds;
        }
        let data = {
            selectedIds: _selectedIds,
            unselectedIds: _unselectedIds
        };
        doArchiveTicket(data);
      }
  
  </script>
  