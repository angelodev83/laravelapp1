<div class="modal" id="editUser_modal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <!-- <div class="modal-header">
        <h6 class="modal-title">Add Employee</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div> -->
      <div class="modal-body">
        
        <div class="card">
            <div class="p-4 card-body">
                <h6 class="card-title">Edit User</h6>
                <hr/>
                <div class="mt-4 form-body">
                <div class="row">
                    <form action="" method="POST" id="#userUpdateForm">
                    <input type="hidden" id="eid" name="id" value="">
                    <div class="col-lg-12">
                        <div class="p-4 border rounded border-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">User Name</label>
                                    <input type="text" name="name" id="ename" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="eemail" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="epassword" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password Confirmation</label>
                                    <input type="password" name="password_confirmation" id="epassword_confirmation" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label for="role_id" class="form-label">Role</label>
                                    <select class="form-control" name="role_id" id="erole_id" title="Role Selection..."></select>
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

        let data = {};

        // $('#editUser_model input, #editUser_model textarea, #editUser_model select').each(function() {
        //     console.log()
        //     data[this.id.substring(1)] = this.value;
        // });
        data['name'] = $("input#ename").val();
        data['email'] = $("input#eemail").val();
        data['password'] = $("input#epassword").val();
        data['password_confirmation'] = $("input#epassword_confirmation").val();
        data['role_id'] = $('#erole_id').find(":selected").val();
        data['id'] = $("input#eid").val();
        
        console.log(data);
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/user/update_user",
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
                    table_user.ajax.reload(null, false);
                    sweetAlert2(data.status, data.message);
                    window.location.reload(true);
                }
                
            },error: function(msg) {
                handleErrorResponse(msg);
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
</script>