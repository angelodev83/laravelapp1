<div class="modal modal-md" style="display:none;" id="archive_renewal_modal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-warning">Warning! Archive confirmation.</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
              <form name="archive_form"  id="archive_form"  >
                    <p>You are about to archive a Renewal record. This procedure is reversible. </p>
                    <p id="title_archive_renewal_id_text" class="fw-bold"><p>
                    <input id="reload" value="false" type="hidden" />
                    <input id="id" type="hidden" />
                    <p>This will archive the following:</p>
                    <ul>
                        <li>Renewal Record</li>
                    </ul>
                   
             </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-warning" id="archive_btn" onclick="saveArchive()">Archive</button>
        </div>
      </div>
    </div>
  </div>
  
  
  
  
  <script>
      function clickArchiveBtn(id, rx_number) {
          $('#archive_renewal_modal').modal('show');
          $('#archive_renewal_modal #id').val(id);
          $('#title_archive_renewal_id_text').html('Renewal: '+id+'; RX #'+rx_number);
      }
  
      function saveArchive() {
        let id = $('#archive_renewal_modal #id').val();

        $('#archive_renewal_modal').modal('hide');
        updateDetails('is_archived', id, 1);
      }
  
  </script>
  