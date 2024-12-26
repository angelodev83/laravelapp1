<div class="modal" id="editRole_modal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <!-- <div class="modal-header">
        <h6 class="modal-title">Add Employee</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div> -->
      <div class="modal-body">
        
        <div class="card">
            <div class="p-4 card-body">
                <h6 class="card-title">Edit Role</h6>
                <hr/>
                <div class="mt-4 form-body">
                <div class="row">
                    <form action="" method="POST" id="#roleAddForm">
                    <input type="hidden" id="eid" name="id" value="">
                    <input type="hidden" id="ename" name="name" value="">
                    <div class="col-lg-12">
                        <div class="p-4 border rounded border-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label   label for="name" class="form-label">Role Name</label>
                                    <input type="text" name="display_name" id="edisplay_name" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="edescription" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="mb-4">
                                        <label for="multiple-select-clear-field" class="form-label">Permissions</label>
                                        <select class="form-select" id="epermissions" name="epermissions[]" data-placeholder="Choose anything" multiple></select>
                                    </div>
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
        <button type="button" class="btn btn-primary" onclick="updateRoleForm()">Submit</button>
      </div>
    </div>
  </div>
</div>

<script>

    function updateRoleForm(){
        let data = {};

        $('#editRole_modal input, #editRole_modal textarea').each(function() {
            console.log()
            data[this.id.substring(1)] = this.value;
        });

        data['permissions'] = $("#epermissions").select2('val');
        
        console.log(data);
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/role/update_role",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
                table_role.ajax.reload(null, false);
                sweetAlert2(data.status, data.message);
                $('#editRole_modal').modal('hide');
                
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
</script>