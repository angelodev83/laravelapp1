<div class="modal " id="default_automation_modal" style="display:none;" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Default</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <form action="" method="POST" id="defaultAutomationForm">
          <div class="container">
              <div class="row">
                  <input type="hidden" id="id" name="id">
                  <input type="hidden" id="task_id" name="task_id">
                  <div class="col-12">
                      <div class="mb-3">
                          <label label for="assignee" class="form-label">Assignee</label>
                          <select class="form-select select2modal" data-placeholder="Select Assignee.." name="assignee" id="assignee"></select>
                      </div>
                      <div class="mb-3">
                          <label label for="task" class="form-label">Task</label>
                          <select class="form-select select2modal" data-placeholder="Select Task.." name="task" id="task"></select>
                      </div>
                  </div>
              </div>
          </div>
      </form>
      
    @include('sweetalert2/script')

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>

  function showDefaultAutomationModal(menu_store_id) {
    $('#default_automation_modal').modal('show');
    
    
    $( '#default_automation_modal .select2modal' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#default_automation_modal .modal-content'),
		});

    
    $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: "GET",
        url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/get_default_list`,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        data: {
            // id: $('#change_task_modal #id').val(),
            // task_id: $('#change_task_modal #task_id').val(),
        },
        success: function(data) {
          console.log(data);
          let userLen = data.users.length;
          $("#default_automation_modal #assignee").empty();
          $("#default_automation_modal #assignee").append("<option selected></option>");
          for( let userVar = 0; userVar<userLen; userVar++){
            let userFirstName = data.users[userVar].firstname;
            let userLastName = data.users[userVar].lastname;
            let userId = data.users[userVar].id;
            if(userId == data.default_assignee.default_id){$("#default_automation_modal #assignee").append("<option selected value='"+userId+"'>"+userFirstName+" "+userLastName+"</option>");}
            else{$("#default_automation_modal #assignee").append("<option value='"+userId+"'>"+userFirstName+" "+userLastName+"</option>");}
          }

          let taskLen = data.tasks.length;
          $("#default_automation_modal #task").empty();
          $("#default_automation_modal #task").append("<option selected></option>");
          for( let taskVar = 0; taskVar<taskLen; taskVar++){
            let taskName = data.tasks[taskVar].name;
            let taskId = data.tasks[taskVar].id;
            if(taskId == data.default_task.default_id){$("#default_automation_modal #task").append("<option selected value='"+taskId+"'>"+taskName+"</option>");}
            else{$("#default_automation_modal #task").append("<option value='"+taskId+"'>"+taskName+"</option>");}
          }
        },
        error: function(msg) {
          handleErrorResponse(msg);
        }
    });

    $('#default_automation_modal #assignee').off('change').change(function() {
      // Get the selected value
      let selectedId = $(this).val();
      let defaultNameId = $(this).attr('id');
      
      sweetAlertLoading(),
      $.ajax({
        //laravel requires this thing, it fetches it from the meta up in the head
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "POST",
        url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/update_default`,
        data: JSON.stringify({
          default_id: selectedId,
          default_name: defaultNameId
        }),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function(data) {
          if (data.errors) {
            $.each(data.errors, function(key, val) {
              $("#" + key).after('<small class="error_txt">' + val[0] + '</small>');
            });
          }
          else{
            sweetAlert2(data.status, data.message);
            $('#default_automation_modal').modal('hide');
            // window.location.href = '/admin/division3/monthly_report/' + $('#report_year').val()+'/'+$('#store').val();
          }
        },
        error: function(data) {
          handleErrorResponse(data);
          //general error
          console.log("Error");
          console.log(data.responseText);
        }
      });

    });

    $('#default_automation_modal #task').off('change').change(function() {
      // Get the selected value
      let selectedId = $(this).val();
      let defaultNameId = $(this).attr('id');
      sweetAlertLoading(),
      $.ajax({
        //laravel requires this thing, it fetches it from the meta up in the head
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "POST",
        url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/update_default`,
        data: JSON.stringify({
          default_id: selectedId,
          default_name: defaultNameId
        }),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function(data) {
          if (data.errors) {
            $.each(data.errors, function(key, val) {
              $("#" + key).after('<small class="error_txt">' + val[0] + '</small>');
            });
          }
          else{
            sweetAlert2(data.status, data.message);
            $('#default_automation_modal').modal('hide');
            // window.location.href = '/admin/division3/monthly_report/' + $('#report_year').val()+'/'+$('#store').val();
          }
        },
        error: function(data) {
          handleErrorResponse(data);
          //general error
          console.log("Error");
          console.log(data.responseText);
        }
      });

    });
  }
</script>