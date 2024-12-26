<div class="modal" id="addMonthlyRevenue_modal" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title">Add Pharmacy Gross Revenue</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            
            
                
                    <div class="row">
                        <form action="" method="POST" id="#monthlyRevenueAddForm" enctype="multipart/form-data">
                        <div class="col-lg-12">
                                <input type="hidden" id="store">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="file" class="form-label">Select File</label>
                                        <input type="file" name="file" class="form-control" id="file">
                                    </div>
                                    <!-- <div class="col-md-12">
                                        <label label for="store" class="form-label">Select Store</label>
                                        <select class="form-select" name="store_select" id="store_select" data-placeholder="Select Store">
                                        </select>
                                    </div> -->
                                    <div class="col-md-12">
                                        <label for="month" class="form-label">Month Covered</label>
                                        <input type="text" name="month" class="form-control" id="month">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="amount" class="form-label">Amount</label>
                                        <input type="text" name="amount" class="form-control amount_only" id="amount">
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
        $("#save_btn").val('Saving... please wait!');
        $("#save_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        let data = {};

        $('#addMonthlyRevenue_modal input, #addMonthlyRevenue_modal textarea, #addMonthlyRevenue_modal select').each(function() {
            data[this.id] = this.value;
        });
        
        sweetAlertLoading();
        
        var formData = new FormData();
        formData.append('file', $('input[type=file]')[0].files[0]);
        formData.append('store_select', $("#store").val());
        formData.append('amount', $("#amount").val());
        formData.append('month', $("#month").val());
        

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/operations/${menu_store_id}/pgr/store`,
            data: formData,
            contentType: false, 
            processData: false,
            dataType: "json",
            success: function(data) {
                $("#save_btn").val('Save');
                $("#save_btn").removeAttr('disabled');
                
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    table_monthlyRevenue.ajax.reload(null, false);
                    sweetAlert2('success', 'Record has been saved.');
                    $('#addMonthlyRevenue_modal').modal('hide');
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                $("#save_btn").val('Save');
                $("#save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }
</script>