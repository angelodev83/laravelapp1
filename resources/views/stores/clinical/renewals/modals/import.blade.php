<div class="modal" id="import_single_excel_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title">Import Excel File</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
            <div class="form-body">
                <div class="row">
                    <form action="" method="POST" id="#import_single_excel_form">
                        <div class="col-lg-12">
                            <label class="form-label">** Please follow this Excel File Format (Sample ONLY Template)</label>
                            <div class="table-responsive">
                                <table class="table table-border" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Serial Number</th>
                                            <th>Rx Number</th>
                                            <th>Renew Date</th>
                                            <th>Telebridge</th>
                                            <th>Reason for Denial</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>13701</td>
                                            <td>123456</td>
                                            <td>7/22/2024</td>
                                            <td>Yes</td>
                                            <td>Lorem ipsum dolor sit amet</td>
                                        </tr>    
                                        <tr>
                                            <td>13701</td>
                                            <td>789012</td>
                                            <td>7/5/2024</td>
                                            <td>No</td>
                                            <td>Ut enim ad minim veniam</td>
                                        </tr>                               
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="documents" class="form-label">Attachment</label>
                                    <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                    <div id="for-file"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!--end row-->
            </div>
  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="saveImportSingleExcel()">Submit</button>
        </div>
      </div>
    </div>
</div>

<script>
    function proceedImportSingleExcel(_url)
    {
        if(_url == '') {
            sweetAlert2('error', 'No URL');
            return;
        }
        
        let pharmacy_store_id = {{request()->id}};

        if($('input[name="upload_file"]').get(0).files.length == 0) {
            sweetAlert2('warning', 'Please upload at least one file.');
            return;
        }

        var upload_file = $('input[name="upload_file"]').get(0).files[0];
        var kbSize = upload_file.size/1024;
        if(kbSize > 100000) {
            sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
            return;
        }

        var formData = new FormData();
        formData.append('upload_file', upload_file);
        if(pharmacy_store_id) {
            formData.append('pharmacy_store_id',pharmacy_store_id);
        }
        
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: _url,
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                
                $('#import_single_excel_modal').modal('hide');
                sweetAlert2('success', 'Record has been saved.');
                loadBoardData();

            },error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }
</script>