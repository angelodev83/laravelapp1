<div class="modal" id="editPharmacyStaff_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-body">
          
          <div class="card">
              <div class="p-4 card-body">
                  <h6 class="card-title">Re-assign Employee: <span class="text-primary" id="eemployee_name">Employee Name</span></h6>
                  <hr/>
                  <div class="mt-4 form-body">
                  <div class="row">
                      <form action="" method="POST" id="#pharmacyStaffUpdateForm">
                      <input type="hidden" id="eid" name="id" value="">
                      <input type="hidden" id="original_store_id" name="id" value="">
                      <input type="hidden" id="eemployee_id" name="id" value="">
                      <div class="col-lg-12">
                          <div class="p-4 border rounded border-3">
                              <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="store_name" class="form-label">From Store</label>
                                    <h6 class="text-primary" id="eestore_name">Store Name</h6>
                                </div>
                                <div class="col-md-12">
                                    <label for="store_id" class="form-label">To Store <span class="text-red">*</span></label>
                                    <select class="form-control" data-live-search="true" name="pharmacy_store_id" id="epharmacy_store_id" title="Store Selection..." required></select>
                                    <div class="invalid-feedback">
                                        Store field is required
                                    </div>
                                </div>
                                <div class="col-md-6" hidden>
                                    <label for="schedule" class="form-label">Schedule</label>
                                    <input type="text" name="schedule" id="eschedule" class="form-control">
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
            schedule: '',
            pharmacy_store_id: ''
        };

        let flag = true;

        $(".form-control").removeClass("is-invalid");

        for (const property in data) {
            let id = `#e${property}`;
            let val = id == "pharmacy_store_id" ? $(id).find(":selected").val() : $(id).val();
            if(!$(id)[0].checkValidity()) {
                $(id).addClass("is-invalid");
                flag = false;
            }
            data[property] = val;
        }
          
          console.log(data);
          
          if(flag === true) {
            sweetAlertLoading();
            $.ajax({
                //laravel requires this thing, it fetches it from the meta up in the head
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: "/admin/divisiontwob/pharmacy_staff/update_staff",
                data: JSON.stringify(data),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(data) {
                    table_staff.ajax.reload(null, false);
                    sweetAlert2(data.status, data.message);
                    $('#editPharmacyStaff_modal').modal('hide');
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