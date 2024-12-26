<div class="modal" id="addPharmacyOperation_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-body">
          
          <div class="card">
              <div class="p-4 card-body">
                  <h6 class="card-title">Add New Operation</h6>
                  <hr/>
                  <div class="mt-4 form-body">
                  <div class="row">
                      <form action="" method="POST" id="#pharmacySupportAddForm">
                      <input id="reload" value="false" type="hidden" />
                      <div class="col-lg-12">
                          <div class="p-4 border rounded border-3">
                              <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="code" class="form-label">Code <span class="text-red">*</span></label>
                                    <input type="text" name="code" id="code" class="form-control" autocomplete="off" required>
                                    <div class="invalid-feedback">
                                        Code field is required
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label for="name" class="form-label">Name <span class="text-red">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" autocomplete="off" required>
                                    <div class="invalid-feedback">
                                        Name field is required
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label for="cover_image" class="form-label">Cover Image</label>
                                    <input id="cover_image" class="imageuploadify-file-general-class" name="cover_image" type="file" accept=".xlsx,.xls,image/*,.doc,audio/*,.docx,video/*,.ppt,.pptx,.txt,.pdf">
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
          <button type="button" class="btn btn-primary" onclick="saveForm()">Submit</button>
        </div>
      </div>
    </div>
  </div>

  <script>
      
      function saveForm(){
  
          let data = {};
          let flag = true;
          $(".form-control").removeClass("is-invalid");

          $('#addPharmacyOperation_modal input, #addPharmacyOperation_modal textarea, #addPharmacyOperation_modal select').each(function() {
            if(!$(`#${this.id}`)[0].checkValidity()) {
                $(`#${this.id}`).addClass("is-invalid");
                flag = false;
            }  
            data[this.id] = this.value;
          });
          
          console.log(data);

          if(flag === true) {
            var cover_image = $('input[name="cover_image"]').get(0).files[0];
            var formData = new FormData();
            formData.append('cover_image', cover_image);
            delete data["cover_image"];
            delete data["reload"];
            formData.append('data', JSON.stringify( data ));

            sweetAlertLoading();
            $.ajax({
                //laravel requires this thing, it fetches it from the meta up in the head
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: "/admin/divisiontwob/pharmacy_operation/add_operation",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(data) {
                $(".imageuploadify-container").remove();
                    
                
                sweetAlert2(data.status, data.message);
                $('#addPharmacyOperation_modal').modal('hide');

                var reload = $('#addPharmacyOperation_modal #reload').val();
                    
                if(reload === true || reload === "true") {
                    window.location.reload(true);
                } else {
                    table_operation.ajax.reload(null, false);
                }
                    
                },
                error: function(msg) {
                    handleErrorResponse(msg);
                    if(msg.status == 403) {
                        sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                    }
                    //general error
                    console.log("Error");
                    console.log(msg);
                    $.each(msg.responseJSON.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
            });
          }
      }
  </script>