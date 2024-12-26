<div class="modal" id="medication_modal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title" id="modal_title"></h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">      
            <div class="row">
                <form action="" method="POST" id="#medicationForm">
                    <div class="col-lg-12">
                    
                        <div class="row g-3">
                            <input type="hidden" name="target_list"  id="target_list" value=""> 
                            
                            
                            <!-- <label for="prescribed_on" class="form-label" id="medication_holder" style="margin-bottom:-10px;">MEDICATION(S)</label> -->
                            <h3 id="medication_holder">Medication Information</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <th WIDTH="40%">Drug Name*</th>
                                        <th WIDTH="20%">Strength*</th>
                                    </thead>
                                    <tbody>
                                        
                                        <tr>
                                            <td><input type="text" class="form-control auto_width add_medication" name="drugname" id="drugname"></td>
                                            <td><input type="text" class="form-control auto_width add_medication" name="strength" id="strength"></td>
                                        </tr>
                                      
                                    </tbody>
                                </table>
                            </div> 

                            <h3>Medication List</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <th WIDTH="40%">Drug Name*</th>
                                        <th WIDTH="20%">Strength*</th>
                                        <th WIDTH="5%">Delete</th>
                                    </thead>
                                    <tbody id="medication_list">
                                        
                                        <!-- <tr>
                                            <td><input type="text" class="form-control auto_width edit_medication" name="drugname" id="drugname"></td>
                                            <td><input type="text" class="form-control auto_width edit_medication" name="strength" id="strength"></td>
                                            <td><a href="javascript:;" class="btn-light btn-block" style="background-color:#dee2e6"><i class="bx bxs-trash"></i></a></td>
                                        </tr> -->
                                      
                                    </tbody>
                                </table>
                            </div> 
                        </div> 

                    </div>
                </form>
            </div><!--end row-->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
  </div>
</div>

<script>
    function showMedicationForm(id, taskId)
    {

        $('#medication_modal').modal('show');
        $('#medication_modal #modal_title').text('MEDICATION FORM');

        $(document).off('click', '#medication_modal .edit_medication').on('click', '#medication_modal .edit_medication', function() {
            let inputName = $(this).attr('name'); // Get the name attribute of the input field
            let dataId = $(this).closest('td').data('id'); // Get the data-id attribute of the closest <td>
            let input = $(this);
            
            
            input.off('keypress').on('keypress', function(event) {
                let inputValue = $(this).val(); // Get the value of the input field
                let eventHandled = false;
                if (eventHandled) {
                    return; // Exit if the event has already been handled
                }
                if ((event.keyCode === 13)) {
                    eventHandled = true; // Set the flag to true to indicate that the event has been handled

                    newValue = $(this).val();
                    
                    sweetAlertLoading();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "PUT",
                        url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/update_patient_medication`,
                        data: JSON.stringify({
                            id: id,
                            med_id: dataId,
                            value: newValue,
                            field: inputName,
                        }),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success: function(data){
                            $('#edit_modal #medication').css('white-space', 'pre-wrap').text(data.medications);
                            sweetAlert2(data.status, data.message);
                            tableTasks['table_task_' + taskId].ajax.reload(null, false);
                            Swal.close();
                        },
                        error: function(data) {
                            handleErrorResponse(data);
                            console.log("Error");
                            console.log(data.responseText);
                        },
                        complete: function() {
                            eventHandled = false; // Reset the flag after the AJAX request is complete
                        }
                    });
                    
                }
            });
        });

        $('#medication_modal .add_medication').off('keydown').on('keydown', function(event) {
            if (event.which === 13 && !event.shiftKey) {
                let drugname = $(this).closest('tr').find('input[name^="drugname"]').val();
                let strength = $(this).closest('tr').find('input[name^="strength"]').val();
                
                if (drugname != '' && strength != '') {
                    // Both drugname and strength have values, so make an AJAX request
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: "POST",
                        url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/medication_store`,
                        data: JSON.stringify({
                            id: id,
                            drug_name: drugname,
                            strength: strength
                        }),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success: function(data) {
                            $('#medication_modal #drugname').val('');
                            $('#medication_modal #strength').val('');
                            
                            // Create a new row
                            let newRow = $('<tr>').attr('id', 'med_' + data.medication.id).addClass('med_row');
                            
                            // Create and append new cells with input values
                            newRow.append($('<td>').append($('<input>').attr({type: 'text', class: 'form-control auto_width edit_medication', name: 'name'}).val(drugname)).attr('data-id', data.medication.id));
                            newRow.append($('<td>').append($('<input>').attr({type: 'text', class: 'form-control auto_width edit_medication', name: 'strength'}).val(strength)).attr('data-id', data.medication.id));
                            newRow.append($('<td>').append($('<a>').attr({href: '#', class: 'btn btn-primary'}).text('DELETE')).attr('data-id', data.medication.id).attr('onclick', 'deleteMedication('+data.medication.id+', '+id+', '+taskId+')'));

                            // Append the new row to the second table
                            $('#medication_modal #medication_list').append(newRow);

                            $('#edit_modal #medication').css('white-space', 'pre-wrap').text(data.medications);
                            tableTasks['table_task_' + taskId].ajax.reload(null, false);
                        },error: function(msg) {
                            handleErrorResponse(msg);
                            //general error
                            console.log("Error");
                            console.log(msg.responseText);
                        }


                    });
                }
            }
        });

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/get_patient_medications`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: {
                id: id,
            },
            success: function(result){
            
                result.medications.forEach(function(med) {
                    let newRow = $('<tr>').attr('id', 'med_' + med.id).addClass('med_row');
                    
                    // Create and append new cells with input values
                    newRow.append($('<td>').append($('<input>').attr({type: 'text', class: 'form-control auto_width edit_medication', name: 'name'}).val(med.name)).attr('data-id', med.id));
                    newRow.append($('<td>').append($('<input>').attr({type: 'text', class: 'form-control auto_width edit_medication', name: 'strength'}).val(med.strength)).attr('data-id', med.id));
                    newRow.append($('<td>').append($('<a>').attr({href: '#', class: 'btn btn-primary'}).text('DELETE')).attr('data-id', med.id).attr('onclick', 'deleteMedication('+med.id+', '+id+', '+taskId+')'));

                    // Append the new row to the second table
                    $('#medication_modal #medication_list').append(newRow);
                });
            
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }
    
    function deleteMedication(id, patientId, taskId){
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/delete_patient_medication`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify({
                id: id,
                patient_id: patientId,
            }),
            success: function(result){
                $('#edit_modal #medication').css('white-space', 'pre-wrap').text(result.medications);
                $('#medication_modal #med_'+id+'').remove();
                tableTasks['table_task_' + taskId].ajax.reload(null, false);
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }
</script>