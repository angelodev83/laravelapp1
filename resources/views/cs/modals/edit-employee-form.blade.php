<div class="modal" id="updateEmployee_modal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-body">
        
        <div class="card">
            <div class="card-body p-4">
                <h6 class="card-title">Edit Employee</h6>
                <hr/>
                <div class="form-body mt-4">
                <div class="row">
                    <form action="" method="POST" id="#employeeUpdateForm">
                    <div class="col-lg-12">
                        <div class="border border-3 p-4 rounded">
                            <div class="row g-3">
                                <input type="hidden" id="eid" name="id" value="">
                                <input type="hidden" id="eold_oig_status" name="old_oig_status" value="">
                                <div class="col-md-6">
                                    <label   label for="lastname" class="form-label">Last Name</label>
                                    <input type="text" name="lastname" class="form-control" id="elastname" placeholder="Last Name">
                                </div>
                                <div class="col-md-6">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <input type="text" name="firstname" class="form-control" id="efirstname" placeholder="First Name">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" id="eemail" placeholder="Email">
                                </div>
                                <div class="col-md-6">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="position" name="position" class="form-control" id="eposition" placeholder="Position">
                                </div>
                                <div class="col-md-12">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="location" name="location" class="form-control" id="elocation" placeholder="Location">
                                </div>
                                <div class="col-4">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date"  name="start_date" class="form-control" id="estartdate" placeholder="">
                                </div>
                                <div class="col-4">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date"  name="end_date" class="form-control" id="eenddate" placeholder="">
                                </div>
                                <div class="col-md-4">
                                    <label for="birthdate" class="form-label">Date of Birth</label>
                                    <input type="date"  name="birthdate" class="form-control" id="ebirthdate">
                                </div>
                                <div class="col-md-12">
                                    <label label for="oig_status" class="form-label">OIG Status</label>
                                    <select class="form-select" name="oig_status" id="eoig_status" title="Select Clinic Location"></select>
                                </div>
                                <div class="col-6">
                                    <input type="checkbox" id="estatus" name="status">
                                    <label for="estatus" class="form-label">Active</label>
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
        $('.error_txt').remove();
        let data = {};

        data['status'] = $('#estatus').prop('checked');
        data['lastname'] = document.getElementById("elastname").value;
        data['firstname'] = $("#efirstname").val();
        data['email'] = $("#eemail").val();
        data['position'] = $("#eposition").val();
        data['id'] = $("#eid").val();
        data['location'] = $("#elocation").val();
        data['oig_status'] = $("#eoig_status").val();
        data['old_oig_status'] = $("#eold_oig_status").val();
        data['startdate'] = document.getElementById("estartdate").value;
        data['enddate'] = document.getElementById("eenddate").value
        data['date_of_birth'] = document.getElementById("ebirthdate").value
        
        console.log(data);
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/oig_check/update_employee",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#e"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    table_employee.ajax.reload(null, false);
                    sweetAlert2('success', 'Record has been updated.');
                    $('#updateEmployee_modal').modal('hide');
                    //window.location.reload(true);
                }
            },error: function(msg) {

                $("#add_user_btn").val('Save');
                $("#add_user_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                handleErrorResponse(msg);
                console.log(msg.responseText);
            }


        });
    }
</script>