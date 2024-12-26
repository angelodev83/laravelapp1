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
                                    <label for="rx_number" class="form-label">Rx #</label>
                                    <input type="text" disabled class="form-control" id="rx_number" name="rx_number">
                                </div>
                                <div class="col-md-12">
                                    <label for="gross_profit" class="form-label">Gross Profit</label>
                                    <input type="number" name="gross_profit" class="form-control" id="gross_profit">
                                </div>
                                <div class="col-md-12">
                                    <label for="acquisition_cost" class="form-label">Acquisition Cost</label>
                                    <input type="number" name="acquisition_cost" class="form-control" id="acquisition_cost">
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


    function showEditForm(id)
    {
        data_id = id;

        let btn = $(`#data-show-btn-${id}`);
        let arr = btn.data('array');
        // console.log('fire-------------', arr);

        // var dateTimeString = arr.return_date;
        // var datePart = dateTimeString.split(' ')[0];
        $('#edit_modal #id').val(arr.id);
        $('#edit_modal #rx_number').val(arr.rx_number);
        $('#edit_modal #gross_profit').val(arr.gross_profit);
        $('#edit_modal #acquisition_cost').val(arr.acquisition_cost);
        
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
            rx_number: $(`#edit_modal #rx_number`).val(),
            gross_profit: $(`#edit_modal #gross_profit`).val(),
            acquisition_cost: $(`#edit_modal #acquisition_cost`).val(),
            pharmacy_store_id: menu_store_id,
        };
        

        formData.append("data", JSON.stringify(data));
        //console.log(data);

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/data-insights/gross-revenue-and-cogs/update`,
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