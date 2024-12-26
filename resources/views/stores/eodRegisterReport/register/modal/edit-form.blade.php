<div class="modal" id="edit_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Edit Register</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form action="" method="POST" id="#editForm">
                        <div class="col-lg-12">
                            <div class="row g-3">
                                <input type="hidden" id="id">
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
                                    <div class="chip chip-lg form-control" id="chip_controller">
                                        <input type="hidden" id="file_id" name="file_id" value="">
                                        <span><a class="file_name"></a></span><span class="closebtn" onclick="showDeleteFileOnly();">×</span>
                                    </div>
                                    <input type="file" name="file" class="form-control" id="file">
                                </div> -->
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

        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5', modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true,
   			orientation: "right",
        });

        let btn = $(`#data-show-btn-${id}`);
        let arr = btn.data('array');
        // console.log('fire-------------', arr);

        // var dateTimeString = arr.return_date;
        // var datePart = dateTimeString.split(' ')[0];
        $('#edit_modal #id').val(arr.id);
        $('#edit_modal #date').val(arr.date);
        $('#edit_modal #register_number').val(arr.register_number);
        $('#edit_modal #total_cash_received').val(arr.total_cash_received);
        $('#edit_modal #total_cash_deposited_to_bank').val(arr.total_cash_deposited_to_bank);
        $('#edit_modal #total_check_received').val(arr.total_check_received);

        $(`#edit_modal #register_page_id`).empty();
        populateNormalSelect(`#edit_modal #register_page_id`, '#edit_modal', '/admin/search/page-by-parent-id', {parent_id: 60}, arr.register_page_id)
        
        if(arr.file_id){
            let filename = arr.file.filename;
            if (filename.length > 30) {
                filename = filename.substring(0, 30) + '...';
            }
            $('#edit_modal .file_name').text(filename);
            $("#edit_modal #file_id").val(arr.file_id);
            $("#edit_modal #chip_controller").show();
            $("#edit_modal #file").hide();
            $('#edit_modal .file_name').attr("href", "/admin/file/download/"+arr.file_id+"");          
        }
        else{
            $("#edit_modal #chip_controller").hide();
            $("#edit_modal #file").show();
        }
        // populateNormalSelect(`#edit_modal #status_id`, '#edit_modal', '/admin/search/store-status', {category: 'procurement_order'}, arr.status_id)

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
            total_cash_received: $(`#edit_modal #total_cash_received`).val(),
            total_cash_deposited_to_bank: $(`#edit_modal #total_cash_deposited_to_bank`).val(),
            total_check_received: $(`#edit_modal #total_check_received`).val(),
            date: $(`#edit_modal #date`).val(),
            register_number: $(`#edit_modal #register_page_id option:selected`).text(),
            register_page_id: $(`#edit_modal #register_page_id`).val(),
            pharmacy_store_id: menu_store_id,
        };

        formData.append("data", JSON.stringify(data));
        //console.log(data);

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/eod-register-report/${menu_store_id}/register/update`,
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
                    reloadDataTable();
                    
                    sweetAlert2(data.status, data.message);
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