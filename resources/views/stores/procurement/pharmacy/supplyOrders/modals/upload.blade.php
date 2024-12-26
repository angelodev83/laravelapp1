<div class="modal " style="display:none" id="upload_modal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Edit Order</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>


    <div class="modal-body">
        <form action="" method="POST" id="#uploadForm">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <input id="order_id" class="mb-0 form-control" name="order_id" type="hidden" />
                        <input id="file_id" class="mb-0 form-control" name="file_id" type="hidden" />
                                                                
                        <div class="mb-3">
                            <label label for="clinic_name" class="form-label">File</label>
                            <div class="chip chip-lg form-control" id="chip_controller">
                                <span class="file_name"></span><span class="closebtn" onclick="showDeleteFileOnly();">×</span>
                            </div>
                            <input type="file" name="file" class="form-control" id="file">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer justify-content-between">
        <div class="text-left row"><span id="status_message" ></span></div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> 
    </div>
    </div>
  </div>
</div>

<script>
    function showDeleteFileOnly(){
        ShowConfirmDeleteForm($("#upload_modal #order_id").val(), $("#upload_modal #file_id").val(),1);
    }

    function showUploadForm(order_id){
        $('#upload_modal').modal('show');
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: `/store/procurement/pharmacy/${menu_store_id}/supply-orders/upload`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                $('#upload_modal #order_id').val(data.order.id);
                $('#upload_modal #file_id').val(data.order.file_id);
				if(data.file){
					$('#upload_modal #chip_controller').show();
					$('#upload_modal #file').hide();
					$('#upload_modal .file_name').text(data.file.filename);
					$('#upload_modal #file_id').val(data.file.id);
				}
				else{
					$('#upload_modal #chip_controller').hide();
					$('#upload_modal #file').show();
					$('#upload_modal .file_name').text('');
				}
            },
            error: function(msg) {
                handleErrorResponse(msg);
                console.log(msg.responseText);
            }
        });

        $('#upload_modal #file').off('change').on('change', function() {
            var formData = new FormData();
            formData.append('order_id', $('#upload_modal #order_id').val());
            formData.append('file', $('#upload_modal input[type=file]')[0].files[0]);
            
            sweetAlertLoading();
            $.ajax({
                //laravel requires this thing, it fetches it from the meta up in the head
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: `/store/operations/${menu_store_id}/mail-orders/file-upload`,
                data: formData,
                contentType: false, 
                processData: false,
                dataType: "json",
                success: function(msg) {
                    $("#save_btn").val('Save');
                    $("#save_btn").removeAttr('disabled');
               
                    if(msg.errors){
                     
                    $.each(msg.errors, function (key, val) {  
                        $("#" + key).after('<span class="error_txt">' + val[0] + '</span>');  
                    });
                     
                    }else{
						$('#upload_modal .file_name').text(msg.fileName);
						$('#upload_modal #chip_controller').show();
						$('#upload_modal #file').hide();
                        $('#orders_table').DataTable().ajax.reload();
                        Swal.close();
                        $('#upload_modal').modal('hide');
                    }


                },error: function(msg) {
                    handleErrorResponse(msg);
                    $("#add_user_btn").val('Save');
                    $("#add_user_btn").removeAttr('disabled');
                    //general error
                    console.log("Error");
                    console.log(msg.responseText);
                }


            });
        });
    }
</script>