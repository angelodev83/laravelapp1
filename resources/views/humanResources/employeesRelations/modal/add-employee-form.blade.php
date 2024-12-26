<div class="modal" id="addEmployee_modal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <!-- <div class="modal-header">
        <h6 class="modal-title">Add Employee</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div> -->
      <div class="modal-body">
        
        <div class="card">
            <div class="p-4 card-body">
                <h6 class="card-title">Add New Employee</h6>
                <hr/>
                <div class="mt-4 form-body">
                <div class="row">
                    <form action="" method="POST" id="#employeeAddForm">
                    <div class="col-lg-12">
                        <div class="p-4 border rounded border-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label   label for="lastname" class="form-label">Last Name</label>
                                    <input type="text" name="lastname" class="form-control" id="lastname" placeholder="Last Name">
                                </div>
                                <div class="col-md-6">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <input type="text" name="firstname" class="form-control" id="firstname" placeholder="First Name">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" id="email" placeholder="Email">
                                </div>
                                <div class="col-md-6">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="position" name="position" class="form-control" id="position" placeholder="Position">
                                </div>
                                <div class="col-md-12">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="location" name="location" class="form-control" id="location" placeholder="Location">
                                </div>
                                <div class="col-6">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" id="startdate" placeholder="">
                                </div>
                                <div class="col-6">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control" id="enddate" placeholder="">
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

        $('#addEmployee_modal input, #addEmployee_modal textarea, #addEmployee_modal select').each(function() {
            data[this.id] = this.value;
        });
        
        console.log(data);
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/human_resources/add_employee",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                // $("#save_btn").val('Save');
                // $("#save_btn").removeAttr('disabled');
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    table_employee.ajax.reload(null, false);
                    sweetAlert2('success', 'Record has been saved.');
                    $('#addEmployee_modal').modal('hide');
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