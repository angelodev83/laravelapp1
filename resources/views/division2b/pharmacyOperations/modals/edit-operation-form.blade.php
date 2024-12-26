<div class="modal" id="editPharmacyOperation_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-body">
          
          <div class="card">
              <div class="p-4 card-body">
                  <div class="mt-4 form-body">
                  <div class="row">
                      <form action="" method="POST" id="#pharmacyOperationUpdateForm">
                      <input type="hidden" id="eid" name="id" value="">
                      <div class="col-lg-12">
                          <div class="p-4 border rounded border-3">
                              <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="code" class="form-label">Code <span class="text-red">*</span></label>
                                    <input type="text" name="code" id="ecode" class="form-control" autocomplete="off" required>
                                    <div class="invalid-feedback">
                                        Code field is required
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label for="name" class="form-label">Name <span class="text-red">*</span></label>
                                    <input type="text" name="name" id="ename" class="form-control" autocomplete="off" required>
                                    <div class="invalid-feedback">
                                        Name field is required
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="edescription" rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <img id="ecover_image" src="/assets/images/errors-images/404-error.png" class="card-img-top" alt=""/>
                                </div>
                                <div class="col-md-6">
                                    <label for="cover_image" class="form-label">Cover Image</label>
                                    <input id="cover_image" class="imageuploadify-file-general-class" name="ecover_image" type="file" accept=".xlsx,.xls,image/*,.doc,audio/*,.docx,video/*,.ppt,.pptx,.txt,.pdf">
                                </div>
                              </div>
                          </div>
                      </div>
                      </form>
                  </div><!--end row-->
              </div>
              </div>
          </div>
  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="updateForm()">Submit</button>
        </div>
      </div>
    </div>
  </div>
  
  <script>
  
      function updateForm(){
  
        let data = {
            id: '',
            code: '',
            name: '',
            description: ''
        };

        let flag = true;

        $(".form-control").removeClass("is-invalid");

        for (const property in data) {
            let id = `#e${property}`;
            let val = $(id).val();
            if(!$(id)[0].checkValidity()) {
                $(id).addClass("is-invalid");
                flag = false;
            }
            data[property] = val;
        }
        
        console.log(data);

        if(flag === true) {
            var cover_image = $('input[name="ecover_image"]').get(0).files[0];
            var formData = new FormData();
            formData.append('cover_image', cover_image);
            delete data["cover_image"];
            formData.append('data', JSON.stringify( data ));
    
            sweetAlertLoading();
            $.ajax({
                //laravel requires this thing, it fetches it from the meta up in the head
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: "/admin/divisiontwob/pharmacy_operation/update_operation",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(data) {
                $(".imageuploadify-container").remove();
                table_operation.ajax.reload(null, false);
                sweetAlert2(data.status, data.message);
                $('#editPharmacyOperation_modal').modal('hide');
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
                        $("#e"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
            });
        }

      }
  </script>