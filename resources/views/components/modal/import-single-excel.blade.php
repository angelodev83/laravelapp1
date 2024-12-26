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
                        <div class="col-lg-12 d-none" id="guide_table"></div>
                        <div class="col-lg-12">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="documents" class="form-label">Attachment</label>
                                    <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                    <!-- <input id="upload_file" class="imageuploadify-file-general-class" name="upload_file" type="file" accept=".xlsx,.xls,.csv"> -->
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
    function proceedImportSingleExcel(_url, data = {})
    {
        console.log("proceed import data", data)
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
            formData.append('pharmacy_store_id', pharmacy_store_id);
        }
        formData.append('data', JSON.stringify(data));
        
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
                
                reloadDataTable(data);

            },error: function(msg) {
                if(msg.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                //general error
                console.log("Error");
                console.log(msg);
                handleErrorResponse(msg);
                $.each(msg.responseJSON.errors,function (key , val){
                    sweetAlert2('warning', 'Check field inputs.');
                    $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                    console.log(key);
                });
            }
        });
    }
</script>