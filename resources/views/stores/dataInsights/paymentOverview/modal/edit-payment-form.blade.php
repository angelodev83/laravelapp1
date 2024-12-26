<div class="modal" id="editPayment_modal" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title" id="emr_title">Edit Payment</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

            <div class="row">
                <form action="" method="POST" id="#paymentEditForm">
                <div class="col-lg-12">
                        <input type="hidden" id="eid" name="id" value="">
                        <input type="hidden" id="efile_id" name="file_id" value="">
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Invoice No.</label>
                                <input type="input" name="name" class="form-control" id="ename">
                            </div>
                            
                            <div class="d-none">
                                <label label for="store" class="form-label">Select Store</label>
                                <select class="form-select" disabled name="store_select" id="estore_select" data-placeholder="Select Store">
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label label for="status" class="form-label">Select Status</label>
                                <select class="form-select" disabled name="status" id="estatus" data-placeholder="Select Status">
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="month" class="form-label">Month</label>
                                <input type="text" name="month" class="form-control" id="emonth">
                            </div>
                            
                            <div class="col-md-12">
                                <label label for="clinic_name" class="form-label">File</label>
                                <div class="chip chip-lg form-control" id="chip_controller">
                                    <span class="file_name"></span><span class="closebtn" onclick="showDeleteFileOnly();">×</span>
                                </div>
                                <input type="file" name="file" class="form-control" id="efile">
                            </div>

                                <div class="col-md-12">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="text" name="amount" class="form-control" id="eamount">
                            </div>
                            
                        </div> 
                    </div>
                </form>
            </div><!--end row-->

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="save_btn" onclick="updateForm()">Submit</button>
        </div>
        </div>
  </div>
</div>
<script>
    function showDeleteFileOnly(){
        ShowConfirmDeleteForm($("#eid").val(), $("#efile_id").val(),1);
    }
    
    function updateForm(){
        $("#editPayment_modal #save_btn").val('Saving... please wait!');
        $("#editPayment_modal #save_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        
        
        var formData = new FormData();
        if( document.getElementById("efile").files.length != 0 ){
            formData.append('file', $('#efile')[0].files[0]);
        }
        
        formData.append('name', $("#ename").val());
        formData.append('id', $("#eid").val());
        formData.append('status', $("#estatus").val());
        formData.append('amount', $("#eamount").val());
        formData.append('month', $("#emonth").val());
        
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/data-insights/${menu_store_id}/payments-overview/update`,
            data: formData,
            contentType: false, 
            processData: false,
            dataType: "json",
            success: function(data) {
                $("#editPayment_modal #save_btn").val('Save');
                $("#editPayment_modal #save_btn").removeAttr('disabled');
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#e"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    table_payment.ajax.reload(null, false);
                    sweetAlert2('success', 'Record has been updated.');
                    $('#editPayment_modal').modal('hide');
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                $("#editPayment_modal #save_btn").val('Save');
                $("#editPayment_modal #save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }
</script>