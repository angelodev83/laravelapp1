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
                                <input type="hidden" id="id" name="id">
                                <div class="col-md-6">
                                    <label for="as_of_date" class="form-label">As Of Date</label>
                                    <input type="text" disabled class="form-control" id="as_of_date" name="as_of_date">
                                </div>
                                <div class="col-md-6">
                                    <label for="account_number" class="form-label">Account #</label>
                                    <input type="text" disabled class="form-control" id="account_number" name="account_number">
                                </div>
                                <div class="col-md-12">
                                    <label for="account_name" class="form-label">Account Name</label>
                                    <input type="text" name="account_name" class="form-control" id="account_name">
                                </div>

                                <div class="col-md-4">
                                    <label for="amount_new_charges" class="form-label">New Charges</label>
                                    <input type="number" name="amount_new_charges" class="form-control" id="amount_new_charges" onkeyup="computeTotalBalance()" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="amount_invoiced_less_than_30" class="form-label">Invoiced (< 30)</label>
                                    <input type="number" name="amount_invoiced_less_than_30" class="form-control" id="amount_invoiced_less_than_30" onkeyup="computeTotalBalance()" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="amount_30_days" class="form-label">30 Days</label>
                                    <input type="number" name="amount_30_days" class="form-control" id="amount_30_days" onkeyup="computeTotalBalance()" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="amount_60_days" class="form-label">60 Days</label>
                                    <input type="number" name="amount_60_days" class="form-control" id="amount_60_days" onkeyup="computeTotalBalance()" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="amount_90_days" class="form-label">90 Days</label>
                                    <input type="number" name="amount_90_days" class="form-control" id="amount_90_days" onkeyup="computeTotalBalance()" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="amount_120_days" class="form-label">120 Days</label>
                                    <input type="number" name="amount_120_days" class="form-control" id="amount_120_days" onkeyup="computeTotalBalance()" min="0">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="amount_last_payment" class="form-label">Amount Last Payment</label>
                                    <input type="number" name="amount_last_payment" class="form-control" id="amount_last_payment" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="amount_unreconciled" class="form-label">Amount Unreconciled</label>
                                    <input type="number" name="amount_unreconciled" class="form-control" id="amount_unreconciled" onkeyup="computeTotalBalance()" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="amount_total_balance" class="form-label">Amount Total Balance</label>
                                    <input type="number" disabled name="amount_total_balance" class="form-control" id="amount_total_balance">
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
    function showEditForm(id)
    {
        let btn = $(`#data-show-btn-${id}`);
        let arr = btn.data('array');
        $('#edit_modal #id').val(arr.id);

        $('#edit_modal input, #edit_modal textarea, #edit_modal select').each(function() {
            let val = arr[this.id] ?? '';
            if(val != '') {
                $(`#${this.id}`).val(val);
            }
        });
        
        $('#edit_modal').modal('show');
    }

    function computeTotalBalance()
    {
        var amount_new_charges = $('#edit_modal #amount_new_charges').val();
        var amount_invoiced_less_than_30 = $('#edit_modal #amount_invoiced_less_than_30').val();
        var amount_30_days = $('#edit_modal #amount_30_days').val();
        var amount_60_days = $('#edit_modal #amount_60_days').val();
        var amount_90_days = $('#edit_modal #amount_90_days').val();
        var amount_120_days = $('#edit_modal #amount_120_days').val();
        var amount_unreconciled = $('#edit_modal #amount_unreconciled').val();

        var amount_last_payment = $('#edit_modal #amount_last_payment').val();

        let sum_amounts = (amount_new_charges*1)
            + (amount_invoiced_less_than_30*1)
            + (amount_30_days*1)
            + (amount_60_days*1)
            + (amount_90_days*1)
            + (amount_120_days*1);
            // + (amount_unreconciled*1);

        var amount_total_balance = sum_amounts - (amount_unreconciled*1);
        amount_total_balance = amount_total_balance.toFixed(2);
        console.log("-----", amount_total_balance, sum_amounts, amount_unreconciled)
        $('#edit_modal #amount_total_balance').val(amount_total_balance);
    }
    
    function updateForm(event)
    {
        event.preventDefault();
        $("#edit_modal #update_btn").val('Saving... please wait!');
        $("#edit_modal #update_btn").attr('disabled','disabled');
        $('.error_txt').remove();
        
        let formData = new FormData();
        
        let data = {
            id: $('#edit_modal #id').val()
        };

        $('#edit_modal input, #edit_modal textarea, #edit_modal select').each(function() {
            data[this.id] = this.value;
        });
        

        formData.append("data", JSON.stringify(data));

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/data-insights/account-receivables/update`,
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