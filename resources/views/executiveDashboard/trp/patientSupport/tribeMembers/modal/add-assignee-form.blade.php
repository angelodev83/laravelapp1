<style>
  #change_task_modal {
      z-index: 2000; 
  }
</style>
<div class="modal " id="add_assignee_modal" style="display:none;" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Assignee</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

          <div class="container">
              <div class="row">
                  <input type="hidden" id="id" name="id">
                  <input type="hidden" id="task_id" name="task_id">
                  <input type="hidden" id="task_status_id" name="task_status_id">
                 
                    <div class="mb-3 col-12">
                        <select class="form-select form-control select2modal" data-placeholder="Select assignee.." name="assignee" id="assignee">
                        </select>
                    </div>
                    <div class="col-12">
                        <label label for="assignee" class="form-label">Assignee</label>
                        <div id="selected_assignee">
                        </div>
                    </div>
              </div>
          </div>
      


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
    
    function showAssigneeModal(id, task_id, task_status_id) {
        @php
            if(Auth::user()->can('menu_store.patient_support.transfer_rx.update')) {
        @endphp
        let menu_store_id = {{request()->id}};
        $('#add_assignee_modal #id').val(id);
        $('#add_assignee_modal #task_id').val(task_id);
        $('#add_assignee_modal #task_status_id').val(task_status_id);
        $('#add_assignee_modal').modal('show');
        
        $( '#add_assignee_modal .select2modal' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#add_assignee_modal .modal-content'),
		});

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/get_assignee_data`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: {
                id: id,
                task_id: task_id,
                task_status_id: task_status_id,
            },
            success: function(data) {
                let usersLen = data.users.length;
                $("#add_assignee_modal #assignee").empty();
                $("#add_assignee_modal #assignee").append("<option value='' disabled selected>Select Assignee..</option>");
                for( let usersVar = 0; usersVar<usersLen; usersVar++){
                let usersName = data.users[usersVar].firstname+' '+data.users[usersVar].lastname;
                let usersId = data.users[usersVar].id;
                $("#add_assignee_modal #assignee").append("<option value='"+usersId+"'>"+usersName+"</option>");
                }

                let assigneeLen = Object.keys(data.assignee).length;

                $.each(Object.values(data.assignee), function(index, person) {
                    let assigneeName = person.firstname + ' ' + person.lastname;
                    let assigneeId = person.id;
                    $("#add_assignee_modal #selected_assignee").append('<div class="chip chip-lg form-control chip_assignee"><span class="selected_assignees">'+assigneeName+'</span><span class="closebtn" onclick="removeAssignee('+assigneeId+', '+task_status_id+', '+task_id+');">×</span></div>')
                });

            },
            error: function (msg) {
                handleErrorResponse(msg);
            }
            
        });
        @php
            }
        @endphp

    }
    


  
    function removeAssignee(userId, taskStatusId, taskId){
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/delete_assignee`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify({
                user_id: userId,
                task_status_id: taskStatusId,
            }),
            success: function(result){
                console.log(result);
                $('#edit_modal #assignees').text(result.assignees);
                $('#add_assignee_modal').modal('hide');
                tableTasks['table_task_' + taskId].ajax.reload(null, false);
                sweetAlert2(result.status, result.message);
            },
            error: function (msg) {
                handleErrorResponse(msg);
            }
        });
    }



</script>