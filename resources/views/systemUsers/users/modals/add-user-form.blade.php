<div class="modal" id="addUser_modal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <!-- <div class="modal-header">
        <h6 class="modal-title">Add Employee</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div> -->
      <div class="modal-body">
        
        <div class="card">
            <div class="p-4 card-body">
                <h6 class="card-title">Add New User</h6>
                <hr/>
                <div class="mt-4 form-body">
                <div class="row">
                    <form action="" method="POST" id="#userAddForm">
                    <div class="col-lg-12">
                        <div class="p-4 border rounded border-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">User Name</label>
                                    <input type="text" name="name" id="name" class="form-control" autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password Confirmation</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label for="role_id" class="form-label">Role</label>
                                    <select class="form-control" name="role_id" id="role_id" title="Role Selection..."></select>
                                </div>
                                <div class="col-md-12">
                                    <label for="employee_id" class="form-label">Employee</label>
                                    <select class="form-control" name="employee_id" id="employee_id" title="Employee Selection..."></select>
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

        $('#addUser_modal input, #addUser_modal textarea, #addUser_modal select').each(function() {
            data[this.id] = this.value;
        });
        
        console.log(data);
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/user/add_user",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
                table_user.ajax.reload(null, false);
                sweetAlert2(data.status, data.message);
                $('#addUser_modal').modal('hide');
                
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
</script>