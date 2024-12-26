<div class="modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title">Add Documents</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
            <div class="form-body">
                <div class="row">
                    <form action="" method="POST" id="#add_form">
                        <div class="col-lg-12">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="month" class="form-label">Date Covered <span class="text-danger">*</span></label>
                                    <div class="input-group"> <span class="input-group-text" id="icon-order-date"><i class="fa fa-calendar"></i></span>
                                        <input type="text" name="month" class="form-control" id="month" placeholder="YYYY-MM-DD" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label for="documents" class="form-label">Attachments <span class="text-danger">*</span></label>
                                    <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                    <input id="documents" class="imageuploadify-file-general-class" name="documents[]" type="file" accept="*" multiple>
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!--end row-->
            </div>
  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="validateForm()">Submit</button>
        </div>
      </div>
    </div>
  </div>

  <script>

    function showAddModal(){
        $(".form-control").removeClass("is-invalid");
        
        $('#add_modal #month').datepicker({
            format: "yyyy-mm-dd",
            modal: true,
            todayHighlight: true,
            uiLibrary: 'bootstrap5',
            autoclose:true,
            endDate: new Date()
        }).on("keydown cut copy paste",function(e) {
            e.preventDefault();
        });

        $('#add_modal').modal('show');
    }

    function validateForm()
    {
        let menu_store_id = {{request()->id}};

        let data = {};
        let flag = true;

        $(".form-control").removeClass("is-invalid");

        $('#add_modal input, #add_modal textarea, #add_modal select').each(function() {
            data[this.id] = this.value;
        });

        data['pharmacy_store_id'] = menu_store_id;
        data["tag_code"] = nav_code;
        data["tag_type"] = 'inventory_reconciliation';
        data["tag_name"] = 'daily';

        if(flag) {
            saveForm(data);
        }
    }

    function saveForm(data)
    {
        var formData = new FormData();
        var uploadFiles = $('#documents').get(0).files;
        
        for (let i = 0; i < uploadFiles.length; i++) {
            formData.append("files[]", uploadFiles[i]);
            var kbSize = uploadFiles[i].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }
        formData.append("data", JSON.stringify(data));    
        // console.log("-------saving",data);

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/inventory-reconciliation/daily-inventory-evaluation/add`,
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                // console.log("result dataaaaaa",data)
            
                $(".imageuploadify-container").remove();    
                table_document.ajax.reload(null, false);
                if(data.status == 422) {
                    sweetAlert2('warning', data.message);
                } else {
                    sweetAlert2(data.status, data.message);
                }
                $('#add_modal').modal('hide');
                    
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