<div class="modal" id="upload_bulk_fst_shipping_label_modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title">Upload One File per Date picked</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
            <div class="form-body">
                <div class="row">
                    <form action="" method="POST" id="#upload_bulk_fst_shipping_label_form">
                        <div class="col-lg-12">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="input-group me-2" style="min-width: 300px"> 
                                        <span class="input-group-text clear-shipped-date" id="icon-shipped-date">
                                            To Ship By <i class="fa fa-calendar ms-1"></i>
                                        </span>
                                        <input type="text" class="form-control datepicker" id="ship_by_date" placeholder="YYYY-MM-DD" style="border-color: #15a0a3;">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="documents" class="form-label">Attachments</label>
                                    <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                    <br><p>File names should be the same with the tracking numbers to track patient name.</p>
                                    <!-- <input id="upload_bulk_fst_shipping_label_file" class="imageuploadify-file-general-class" name="upload_bulk_fst_shipping_label_file" type="file" accept="*" multiple> -->
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
          <button type="button" class="btn btn-primary" onclick="saveUploadBulkShippingLabel()">Submit</button>
        </div>
      </div>
    </div>
</div>

<script>
    function proceedUploadBulkShippingLabel(_url)
    {
        if(_url == '') {
            sweetAlert2('error', 'No URL');
            return;
        }
        
        let pharmacy_store_id = {{request()->id}};

        var uploadFiles = $('#upload_bulk_fst_shipping_label_file').get(0).files;
        var formData = new FormData();
        for (let i = 0; i < uploadFiles.length; i++) {
            formData.append("upload_bulk_fst_shipping_label_files[]", uploadFiles[i]);
            var kbSize = uploadFiles[i].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }

        if(uploadFiles.length == 0) {
            sweetAlert2('warning', 'Please upload at least one file.');
            return;
        }
        
        if(pharmacy_store_id) {
            formData.append('pharmacy_store_id',pharmacy_store_id);
        }
        const ship_by_date = $('#upload_bulk_fst_shipping_label_modal #ship_by_date').val();
        formData.append('ship_by_date',ship_by_date);

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
                handleErrorResponse(msg);
                if(msg.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                //general error
                console.log("Error");
                console.log(msg);
                $.each(msg.responseJSON.errors,function (key , val){
                    sweetAlert2('warning', 'Check field inputs.');
                    $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                    console.log(key);
                });
            }
        });
    }
</script>