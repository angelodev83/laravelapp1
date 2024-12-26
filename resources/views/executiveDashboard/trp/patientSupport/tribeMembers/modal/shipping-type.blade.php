<style>
  #shipping_type_modal {
      z-index: 2000; 
  }
</style>
<div class="modal " id="shipping_type_modal" style="display:none;" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Shipping Type</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <form action="" method="POST" id="changeTaskForm">
          <div class="container">
              <div class="row">
                  <input type="hidden" id="id" name="id">
                  <input type="hidden" id="task_id" name="task_id">
                  <!-- <div class="col-12">
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
                  </div> -->
                  <div class="col-12">
                      <div class="mb-3">
                          <!-- <label label for="shipping_type" class="form-label">Shipping Type</label> -->
                          <select class="form-select" data-placeholder="Select Shipping Type.." name="shipping_type" id="shipping_type"></select>
                      </div>
                  </div>
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

  function showShippingTypeModal(id, task_id, task_status_id, shipping_type) {
    @php
      if(Auth::user()->can('menu_store.patient_support.transfer_rx.update')) {
    @endphp
    let menu_store_id = {{request()->id}};
    // Get the current URL
    let currentUrl = window.location.href;
    // Split the URL by '/' to get individual segments
    let segments = currentUrl.split('/');
    // Find the segment containing the desired value (in this case, the segment at index 4)
    let transferListId = segments[7];
    $('#shipping_type_modal').modal('show');
    
    $('#shipping_type_modal #shipping_type').select2( {
      theme: "bootstrap-5",
      width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
      placeholder: $( this ).data( 'placeholder' ),
      closeOnSelect: true,
      dropdownParent: $('#shipping_type_modal .modal-content'),
		});

   
    $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: "GET",
        url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/get_shipping_type`,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        data: {
            id: id,
            task_id: task_id,
            list_id: transferListId,
            task_status_id: task_status_id,
        },
        success: function(data) {
          
          let len = data.data.length;
          $("#shipping_type_modal #shipping_type").empty();
          $("#shipping_type_modal #shipping_type").append("<option value=''></option>");
          for( let shipVar = 0; shipVar<len; shipVar++){
            let shipName = data.data[shipVar];
           
            if(shipName == shipping_type){$("#shipping_type_modal #shipping_type").append("<option selected value='"+shipName+"'>"+shipName+"</option>");}
            else{$("#shipping_type_modal #shipping_type").append("<option value='"+shipName+"'>"+shipName+"</option>");}
          }
          
        },
        error: function(msg) {
          handleErrorResponse(msg);
        }
    });

    $('#shipping_type_modal #shipping_type').off('change').change(function() {
      // Get the selected value
      let selectedShippingType = $(this).val();
      sweetAlertLoading(),
      $.ajax({
        //laravel requires this thing, it fetches it from the meta up in the head
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "PUT",
        url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/update_shipping_type`,
        data: JSON.stringify({
          shipping_type: selectedShippingType,
          task_status_id: task_status_id
        }),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function(data) {
          $('#shipping_type_modal').modal('hide');
          tableTasks['table_task_' + task_id].ajax.reload(null, false);
          sweetAlert2(data.status, data.message); 
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