<div class="modal" id="edit_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Edit Form</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form action="" method="POST" id="#editForm">
                        <div class="col-lg-12">
                            <div class="row g-3">
                                <input type="hidden" id="id">
                                <div class="col-md-12">
                                    <label for="account_number" class="form-label">Account #</label>
                                    <input type="text" disabled class="form-control" id="account_number" name="account_number">
                                </div>
                                <div class="col-md-12">
                                    <label for="account_name" class="form-label">Account Name</label>
                                    <input type="text" name="account_name" class="form-control" id="account_name">
                                </div>
                                <div class="col-md-12" hidden>
                                    <label for="running_balance_as_of_date" class="form-label">Running Balance as of Date</label>
                                    <input type="number" name="running_balance_as_of_date" class="form-control" id="running_balance_as_of_date">
                                </div>
                                <div class="col-md-12">
                                    <label for="paid_amount" class="form-label">Paid Amount</label>
                                    <input type="number" name="paid_amount" class="form-control" id="paid_amount">
                                </div>
                            </div> 
                        </div>
                    </form>
                </div><!--end row-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="update_btn" onclick="updateForm(event)">Submit</button>
            </div>
            
        </div>
    </div>
</div>

<script> 
    let addMore = 0;
    let inmar_return_id;

    function showDeleteFileOnly(){
        let data = {
            id: $("#edit_modal #file_id").val(),
        };
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/eod-register-report/register/delete_file`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $("#edit_modal #chip_controller").hide();
                    $("#edit_modal #file").show();
                    Swal.close();
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }

        });

        
    }

    function showEditForm(id)
    {
        data_id = id;

        let btn = $(`#data-show-btn-${id}`);
        let arr = btn.data('array');
        // console.log('fire-------------', arr);

        // var dateTimeString = arr.return_date;
        // var datePart = dateTimeString.split(' ')[0];
        $('#edit_modal #id').val(arr.id);
        $('#edit_modal #account_number').val(arr.account_number);
        $('#edit_modal #account_name').val(arr.account_name);
        $('#edit_modal #running_balance_as_of_date').val(arr.running_balance_as_of_date);
        $('#edit_modal #paid_amount').val(arr.paid_amount);
        
        $('#edit_modal').modal('show');
    }
    
    function updateForm(event)
    {
        event.preventDefault();
        $("#edit_modal #update_btn").val('Saving... please wait!');
        $("#edit_modal #update_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        
        let formData = new FormData();
        
        let data = {
            id: $(`#edit_modal #id`).val(),
            account_number: $(`#edit_modal #account_number`).val(),
            account_name: $(`#edit_modal #account_name`).val(),
            // running_balance_as_of_date: $(`#edit_modal #running_balance_as_of_date`).val(),
            paid_amount: $(`#edit_modal #paid_amount`).val(),
            pharmacy_store_id: menu_store_id,
        };
        

        formData.append("data", JSON.stringify(data));
        //console.log(data);

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/data-insights/collected-payments/update`,
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#edit_modal #update_btn").val('Save');
                $("#edit_modal #update_btn").removeAttr('disabled');
                data = JSON.parse(data);
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#edit_modal #"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    reloadDataTable(data);
                    
                    $('#edit_modal').modal('hide');    
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                $("#edit_modal #update_btn").val('Save');
                $("#edit_modal #update_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }

        });
    }
</script>