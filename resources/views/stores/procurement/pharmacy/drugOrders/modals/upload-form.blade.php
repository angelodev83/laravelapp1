<div class="modal " style="display:none" id="upload_modal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Upload Form</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>


    <div class="modal-body">
        <form action="" method="POST" id="#uploadForm">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <input id="id" class="mb-0 form-control" name="id" type="hidden" />
                        <input id="file_id" class="mb-0 form-control" name="file_id" type="hidden" />
                                                                
                        <div class="mb-3">
                            <label label for="clinic_name" class="form-label">File</label>
                            <div class="chip chip-lg form-control" id="chip_controller">
                                <input type="hidden" id="file_id" name="file_id" value="">
                                <span><a class="file_name"></a></span><span class="closebtn" onclick="showDeleteFileOnly();">×</span>
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
        let data = {
            id: $("#upload_modal #file_id").val(),
        };
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/procurement/pharmacy/drug-orders/delete_file`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $("#upload_modal #chip_controller").hide();
                    $("#upload_modal #file").show();
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

    function showUploadForm(id){
        $('#upload_modal').modal('show');
        
        $('#upload_modal #file').off('change').on('change', function() {
            var formData = new FormData();
            formData.append('id', $('#upload_modal #id').val());
            formData.append('file', $('#upload_modal #file')[0].files[0]);
            
            sweetAlertLoading();
            $.ajax({
                //laravel requires this thing, it fetches it from the meta up in the head
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: `/store/procurement/pharmacy/drug-orders/file-upload`,
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
                        reloadDataTable();
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
        
        let btn = document.querySelector(`#upload-show-btn-${id}`);
        let arr = JSON.parse(btn.dataset.array);
        console.log('fire-------------',arr);

        $("#upload_modal #id").val(arr.id);
        if(arr.file_id){
            let filename = arr.file.filename;
            if (filename.length > 30) {
                filename = filename.substring(0, 30) + '...';
            }
            $('#upload_modal .file_name').text(filename);
            $("#upload_modal #file_id").val(arr.file_id);
            $("#upload_modal #chip_controller").show();
            $("#upload_modal #file").hide();
            $('#upload_modal .file_name').attr("href", "/admin/file/download/"+arr.file_id+"");
                        
        }
        else{
            $("#upload_modal #chip_controller").hide();
            $("#upload_modal #file").show();
        }
    }
</script>