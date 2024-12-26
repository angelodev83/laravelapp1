<div class="modal" id="add_modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title">Register Form</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">      
            <div class="row">
                <form action="" method="POST" id="#addForm">
                    <div class="col-lg-12">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="date" class="form-label">Date</label>
                                <input type="text" class="form-control datepicker form-control-sm" id="date" name="date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" readonly>
                            </div>
                            <div class="col-md-12">
                                <label for="register_number" class="form-label">Register #</label>
                                {{-- <input type="text" name="register_number" class="form-control" id="register_number"> --}}
                                <select class="form-select" id="register_page_id" name="register_page_id">
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="total_cash_received" class="form-label">Total Cash Received</label>
                                <input type="number" name="total_cash_received" class="form-control" id="total_cash_received">
                            </div>
                            <div class="col-md-12">
                                <label for="total_cash_deposited_to_bank" class="form-label">Total Cash Deposited to Bank</label>
                                <input type="number" name="total_cash_deposited_to_bank" class="form-control" id="total_cash_deposited_to_bank">
                            </div>
                            <div class="col-md-12">
                                <label for="total_check_received" class="form-label">Total Check Received</label>
                                <input type="number" name="total_check_received" class="form-control" id="total_check_received">
                            </div>
                            <!-- <div class="col-md-12">
                                <label for="file" class="form-label">File</label>
                                <input type="file" name="file" class="form-control" id="file">
                            </div> -->
                            <div class="col-md-12">
                                <label for="documents" class="form-label">Attachments</label>
                                <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                <!-- <input id="file" class="imageuploadify-file-general-class" name="file" type="file" accept="*" multiple> -->
                                <!-- <input id="file" name="file" type="file" accept="*" multiple> -->
                                <div id="for-file"></div>
                            </div>
                        </div> 
                    </div>
                </form>
            </div><!--end row-->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="save_btn" onclick="saveForm()">Submit</button>
        </div>
    </div>
  </div>
</div>

<script>
    function saveForm(){
        $("#add_modal #save_btn").val('Saving... please wait!');
        $("#add_modal #save_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        let formData = new FormData();
        let data = {};

        $('#add_modal input, #add_modal textarea, #add_modal select').each(function() {
            data[this.id] = this.value;
        });
        data['menu_store_id'] = menu_store_id;
        data['register_number'] = $(`#add_modal #register_page_id  option:selected`).text();

        // if ($("#add_modal #file")[0].files.length !== 0) {
        //     formData.append('file', $('#add_modal #file')[0].files[0]);
        // }
        let uploadFiles = $('#file').get(0).files;
        for (let i = 0; i < uploadFiles.length; i++) {
            formData.append("file[]", uploadFiles[i]);
            let kbSize = uploadFiles[i].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }

        formData.append("data", JSON.stringify(data));
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/eod-register-report/${menu_store_id}/register/store`,
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#add_modal #save_btn").val('Save');
                $("#add_modal #save_btn").removeAttr('disabled');
                data = JSON.parse(data);
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    reloadDataTable();
                    sweetAlert2('success', 'Record has been saved.');
                    $('#add_modal').modal('hide');
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                $("#add_modal #save_btn").val('Save');
                $("#add_modal #save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }
</script>