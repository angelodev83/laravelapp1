<div class="modal" id="updateEmployee_modal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-body">
        
        <div class="card">
            <div class="p-4 card-body">
                <h6 class="card-title">Edit Employee</h6>
                <hr/>
                <div class="mt-4 form-body">
                <div class="row">
                    <form action="" method="POST" id="#employeeUpdateForm">
                    <div class="col-lg-12">
                        <div class="p-4 border rounded border-3">
                            <div class="row g-3">
                                <input type="hidden" id="eid" name="id" value="">
                                <div class="col-md-6">
                                    <label   label for="lastname" class="form-label">Last Name</label>
                                    <input type="text" name="lastname" class="form-control" id="elastname" placeholder="Last Name">
                                </div>
                                <div class="col-md-6">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <input type="text" name="firstname" class="form-control" id="efirstname" placeholder="First Name">
                                </div>
                                <div class="col-md-6 d-none">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" id="eemail" placeholder="Email">
                                </div>
                                <div class="col-md-6">
                                    <label for="birthdate" class="form-label">Date of Birth</label>
                                    <input type="date"  name="birthdate" class="form-control" id="ebirthdate">
                                </div>
                                <div class="col-md-6">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="position" name="position" class="form-control" id="eposition" placeholder="Position">
                                </div>
                                <div class="col-md-12">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="location" name="location" class="form-control" id="elocation" placeholder="Location">
                                </div>
                                <div class="col-6">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" id="estartdate" placeholder="">
                                </div>
                                <div class="col-6">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control" id="eenddate" placeholder="">
                                </div>
                                
                                <div class="col-6">
                                    {{-- <input type="checkbox" id="estatus" name="status">
                                    <label for="estatus" class="form-label">Active</label> --}}
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="radioStatus" id="eradioActive">
                                        <label class="form-check-label" for="eradioActive">Active</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="radioStatus" id="eradioTerminated">
                                        <label class="form-check-label" for="eradioTerminated">Terminated</label>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <label for="department_id" class="form-label">Department</label>
                                    <select class="form-control" id="edepartment_id">
                                    </select>
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

        let stat = 'Active';
        if($('#eradioTerminated').prop('checked') == true) {
            stat = 'Terminated';
        }

        data['status'] = stat;
        data['lastname'] = document.getElementById("elastname").value;
        data['firstname'] = $("#efirstname").val();
        data['email'] = $("#eemail").val();
        data['position'] = $("#eposition").val();
        data['department_id'] = $("#edepartment_id").val();
        data['id'] = $("#eid").val();
        data['location'] = $("#elocation").val();
        data['startdate'] = document.getElementById("estartdate").value;
        data['enddate'] = document.getElementById("eenddate").value;
        data['date_of_birth'] = document.getElementById("ebirthdate").value
        
        console.log(data);
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/human_resources/update_employee",
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
                handleErrorResponse(msg);
                $("#add_user_btn").val('Save');
                $("#add_user_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }
</script>