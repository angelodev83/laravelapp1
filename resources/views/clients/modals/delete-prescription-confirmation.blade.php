<div class="modal modal-md"  style="display:none;" id="delete_form_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-red">Warning! Delete confirmation.</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form name="delete_form"  id="delete_form"  >
                  <p>You are about to delete a patient record. This procedure is irreversible. </p>
                  <p id="prescription_name" class="fw-bold"><p>
                  <input id="prescription_id" type="hidden" />
                  <p>This will delete the following:</p>
                  <ul>
                      <li>Prescription Record</li>
                      <li>Any associated pdf file</li>
                  </ul>
           </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="delete_btn" onclick="DeletePrescription()">DELETE</button>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-md" style="display:none;" id="delete_success_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-red">Delete succesful</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
             <p class="fw-bold">Patient record successfully deleted.<p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-md" style="display:none;" id="multiple_delete_success_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-red">Delete succesful</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
             <p class="fw-bold">Patient records successfully deleted.<p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div class="modal modal-md" style="display:none;" id="multiple_delete_form_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-red">Warning! Multiple Records Delete</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form name="delete_form"  id="delete_form"  >
                  <p>You are about to delete multiple patient records. This procedure is irreversible. </p>
                  <p id="patient_name" class="fw-bold"><p>
                  <input id="patient_id" type="hidden" />
                
                  <p>This will delete the following patient with IDs</p>
                  <p id="selectedPatientsList"></p>
         
           </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="delete_btn" onclick="DeleteMultiplePatients()">DELETE</button>
      </div>
    </div>
  </div>
</div>

<script>
  function ShowConfirmDeleteForm(id) {
        $('#delete_form_modal').modal('show');
        $('#delete_form #prescription_id').val(id);
        $('#prescription_name').html('Prescription ID: '+id);
  }

  function ConfirmMultipleDelete(firstname,lastname,id) {
       var selectedPatientIds = [];
        $(".row-checkbox:checked").each(function () {
            selectedPatientIds.push($(this).val());
        });
      var data = { patient_ids: selectedPatientIds };

      // Check if data has at least one value in the patient_ids array
      if (data.patient_ids && data.patient_ids.length > 0) {

                $('#multiple_delete_form_modal').modal('show');
       
              var selectedPatients = [];
              $(".row-checkbox:checked").each(function() {
                  var id = $(this).val();
                  var firstname = $(this).data('firstname');
                  var lastname = $(this).data('lastname');
                  selectedPatients.push({ id: id, firstname: firstname, lastname: lastname });
              });

              // Create an empty ul element
              var ul = $('<ul></ul>');

              // Loop through the selectedPatients array
              selectedPatients.forEach(function(patient) {
                  // Create a new li element for each selected patient
                  var li = $('<li></li>').text(`${patient.id.toString().padStart(2, '0')} - ${patient.firstname} ${patient.lastname}`);

                  // Append the li element to the ul
                  ul.append(li);
              });

              // Append the ul element to a container in your HTML (assuming the container has an ID of 'selectedPatientsList')
              $('#selectedPatientsList').empty().append(ul); 

      } else {
          // No value exists in the patient_ids array
          console.log('Data does not have any value or is empty.');
      }

      

     
  }

  function DeletePrescription(id) {
                var data = {};
                 $("#delete_btn").val('Deleting... please wait!').attr('disabled', 'disabled');
                data.prescription_id = $('#prescription_id').val();
             

             console.log(data);
                $.ajax({
                      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                      type: "POST",
                      url: "/admin/prescription/delete_prescription_via_ajax",
                      data: JSON.stringify(data),
                      contentType: "application/json; charset=utf-8",
                      dataType: "json",
                      success: function(msg) {
                        $("#delete_btn").val('DELETE').removeAttr('disabled');
                         $('#row-'+data.prescription_id +'').remove();
                         $('#delete_form_modal').modal('hide');
                        $('#delete_success_modal').modal('show');

                      },error: function(msg) {
                         $("#delete_btn").val('DELETE').removeAttr('disabled');
                         handleErrorResponse(msg);
                          console.log(msg.responseText);
                      }

            });
               
 }


function DeleteMultiplePatients() {
    var selectedPatientIds = [];
    $(".row-checkbox:checked").each(function() {
        selectedPatientIds.push($(this).val());
    });

    var data = { patient_ids: selectedPatientIds }; // Create a data object with patient_ids

    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: "POST",
        url: "/admin/patients/delete_patients_via_ajax",
        data: JSON.stringify(data),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function(msg) {
            $("#delete_btn").val('DELETE').removeAttr('disabled');
           

             $('#multiple_delete_form_modal').modal('hide');
            

            var count = selectedPatientIds.length;
            var completed = 0;

            selectedPatientIds.forEach(function(patientId, index) {
                $('#row-' + patientId).fadeOut(300 * index, function() {
                    $(this).remove();
                    completed++;
                    if (completed === count) {
                        $('#multiple_delete_success_modal').modal('show');
                    }
                });
});

        },
        error: function(msg) {
            $("#delete_btn").val('DELETE').removeAttr('disabled');
            handleErrorResponse(msg);
            console.log(msg.responseText);
            // Handle error
        }
    });
}



</script>
