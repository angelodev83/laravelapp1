<style>
  #change_task_modal {
      z-index: 2000; 
  }
</style>
<div class="modal " id="change_task_modal" style="display:none;" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Task</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <form action="" method="POST" id="changeTaskForm">
          <div class="container">
              <div class="row">
                  <input type="hidden" id="id" name="id">
                  <input type="hidden" id="task_id" name="task_id">
                  <div class="col-12">
                      <div class="mb-3">
                          <label label for="task"  class="form-label">Selected</label>
                          <div class="gap-2 d-grid" id="selected_button"></div>
                      </div>
                  </div>
                  <hr>
                  <div class="col-12">
                      <div class="mb-3">
                          <label label for="task"  class="form-label">Selection</label>
                          <div class="gap-2 d-grid" id="selection_button"></div>
                      </div>
                  </div>
                  <!-- <div class="col-12">
                      <div class="mb-3">
                          <label label for="task" class="form-label">Task</label>
                          <select class="form-select" data-placeholder="Select Task.." name="task" id="task"></select>
                      </div>
                  </div> -->
              </div>
          </div>
      </form>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
  
  function ShowChangeTaskModal(id, task_id) {
    @php
      if(Auth::user()->can('menu_store.patient_support.transfer_rx.update')) {
    @endphp
    let menu_store_id = {{request()->id}};
    // Get the current URL
    var currentUrl = window.location.href;
    // Split the URL by '/' to get individual segments
    var segments = currentUrl.split('/');
    // Find the segment containing the desired value (in this case, the segment at index 4)
    var transferListId = segments[7];
    $('#change_task_modal').modal('show');
    // $('#change_task_modal #id').val(id);
    // $('#change_task_modal #task_id').val(task_id);
    $('#task').select2( {
      theme: "bootstrap-5",
      width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
      placeholder: $( this ).data( 'placeholder' ),
      closeOnSelect: true,
      dropdownParent: $('#change_task_modal'),
		});
    $('#change_task_modal #selected_button, #change_task_modal #selection_button').empty(); // Remove existing buttons
    $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: "GET",
        url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/get_patient_data`,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        data: {
            id: id,
            task_id: task_id,
            list_id: transferListId,
        },
        success: function(data) {
          console.log(data);
          let taskLen = data.task.length;
          $("#task").empty();
          for( let taskVar = 0; taskVar<taskLen; taskVar++){
            let taskName = data.task[taskVar].name;
            let taskId = data.task[taskVar].id;
            let taskColor = data.task[taskVar].color;
            let taskIcon =data.task[taskVar].widget_icon;
            if(task_id == taskId){$("#change_task_modal #task").append("<option selected value='"+taskId+"'><i style='"+taskColor+"' class='"+taskIcon+"'></i> "+taskName+"</option>");}
            else{$("#change_task_modal #task").append("<option value='"+taskId+"'><i style='"+taskColor+"' class='"+taskIcon+"'></i> "+taskName+"</option>");}
          }
          
          // Sort the tasks based on the 'sort' property
          data.task.sort(function(a, b) {
              return a.sort - b.sort;
          });
          data.task.forEach(function(task) {
            if(task.id === task_id){
              $('#change_task_modal #selected_button').append('<button class="'+task.class+'" style="background-color:'+task.color+'; color:'+task.text_color+';" data-task-id="' + task.id + '">' + task.name + '</button>');
            }
            else{
              $('#change_task_modal #selection_button').append('<button class="'+task.class+'" style="background-color:'+task.color+'; color:'+task.text_color+';" data-task-id="' + task.id + '">' + task.name + '</button>');
            }
          });
        },
        error: function(data) {
          handleErrorResponse(data);
        }
    });

    $('#change_task_modal #task').change(function() {
      // Get the selected value
      let selectedTaskId = $(this).val();
      sweetAlertLoading(),
      $.ajax({
        //laravel requires this thing, it fetches it from the meta up in the head
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "PUT",
        url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/update_task`,
        data: JSON.stringify({
          task_id: selectedTaskId,
          id: id
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
            tableTasks['table_task_' + task_id].ajax.reload(null, false);
            tableTasks['table_task_' + data.task_to].ajax.reload(null, false);
            sweetAlert2(data.status, data.message);
            $('#change_task_modal').modal('hide');
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

    $('#change_task_modal #selection_button, #change_task_modal #selected_button').off('click').on('click', 'button', function(e) {
      e.preventDefault();
      // Get the selected value
      let selectedTaskId = $(this).data('task-id');
      // console.log($(this).data('task-id'));
      sweetAlertLoading();
      $.ajax({
        //laravel requires this thing, it fetches it from the meta up in the head
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "PUT",
        url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/update_task`,
        data: JSON.stringify({
          task_id: selectedTaskId,
          id: id,
          task_from: task_id
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
            tableTasks['table_task_' + task_id].ajax.reload(null, false);
            tableTasks['table_task_' + data.task_to].ajax.reload(null, false);
            if ($('#edit_modal').is(':visible')) {
              $('#edit_modal #selected_status').empty();
              $('#edit_modal #selected_status').append('<button class="'+data.task.class+'" style="background-color:'+data.task.color+'; color:'+data.task.text_color+';" data-task-id="' + data.task.id + '">' + data.task.name + '</button>');
            }
            
            $('#change_task_modal').modal('hide');
            sweetAlert2(data.status, data.message);
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
    @php
      }
    @endphp
  }
  
  
  



</script>