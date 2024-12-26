<div class="modal" id="addPharmacySupport_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-body">
          
          <div class="card">
              <div class="p-4 card-body">
                  <h6 class="card-title">Add New Support</h6>
                  <hr/>
                  <div class="mt-4 form-body">
                  <div class="row">
                      <form action="" method="POST" id="#pharmacySupportAddForm">
                      <div class="col-lg-12">
                          <div class="p-4 border rounded border-3">
                              <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="hidden" id="pharmacy_operation_id" name="id" value="">
                                    <label for="employee_id" class="form-label">Employee <span class="text-red">*</span></label>
                                    <select class="form-control" name="employee_id" id="employee_id" title="Employee Selection..." required></select>
                                    <div class="invalid-feedback">
                                        Employee field is required
                                    </div>
                                </div>  
                                <div class="col-md-6">
                                    <label for="schedule" class="form-label">Schedule</label>
                                    <input type="text" name="schedule" id="schedule" class="form-control">
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
  
          $('#addPharmacySupport_modal input, #addPharmacySupport_modal textarea, #addPharmacySupport_modal select').each(function() {
            if(!$(`#${this.id}`)[0].checkValidity()) {
                $(`#${this.id}`).addClass("is-invalid");
                flag = false;
            }
            data[this.id] = this.value;
          });
          
          console.log(data);
          
          if(flag === true) {
            sweetAlertLoading();
            $.ajax({
                //laravel requires this thing, it fetches it from the meta up in the head
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: "/admin/divisiontwob/pharmacy_support/add_support",
                data: JSON.stringify(data),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(data) {

                    $("input#pharmacy_operation_id").val(data.data.pharmacy_operation_id);
                    
                    table_support.ajax.reload(null, false);
                    sweetAlert2(data.status, data.message);
                    $('#addPharmacySupport_modal').modal('hide');
                    
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
                        $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
            });
          }
      }
  </script>