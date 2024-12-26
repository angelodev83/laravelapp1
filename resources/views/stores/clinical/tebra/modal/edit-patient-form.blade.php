<div class="modal fade" style="display:none;" id="edit_patient_modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Update Patient Information</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form action="" method="POST" id="EditForm">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" class="form-control" id="patient_id">
                            <!-- <div class="mb-3">
                                <label for="addonApplied" class="form-label">First name</label>
                                <input type="text" class="form-control" id="edit_firstname">
                            </div>
                            <div class="mb-3">
                                <label for="addonApplied" class="form-label">Last name</label>
                                <input type="text" class="form-control" id="edit_lastname">
                            </div>
                            <div class="mb-3">
                                <label for="birthdate" class="form-label">Patient Date of Birth</label>
                                <input class="form-control datepicker" id="edit_birthdate">
                            </div> -->
                        </div>
                        <div class="col-md-12">
                            <!-- <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="edit_address">
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="edit_city">
                            </div>
                            <div class="mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="edit_state">
                            </div>
                            <div class="mb-3">
                                <label for="zip_code" class="form-label">Zip code</label>
                                <input type="text" class="form-control" id="edit_zip_code">
                            </div>
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone number</label>
                                <input type="text" class="form-control" id="edit_phone_number">
                            </div> -->
                            <div class="col-12">
                                <input type="checkbox" id="known_allergies" name="known_allergies">
                                <label for="known_allergies" class="form-label">Known Allergies</label>
                            </div>
                            <div class="col-12">
                                <input type="checkbox" id="medication_allergies" name="medication_allergies">
                                <label for="medication_allergies" class="form-label">Medication Allergies</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="save_btn" onclick="UpdatePatient()">Save</button>
      </div>
    </div>
  </div>
</div>
<script>

  function UpdatePatient() {
    
    $('.error_txt').remove();
    $('.alert').remove();
    $("#save_btn").val('Saving... please wait!');
    $("#save_btn").attr('disabled', 'disabled');

    //Magic: maps all the inputs data
    var data = {};
    $('#EditForm input, textarea, select').each(function() {
      data[this.id] = this.value;
    });

    data['known_allergies'] = $('#known_allergies').prop('checked');
    data['medication_allergies'] = $('#medication_allergies').prop('checked');

    console.log(data);
    sweetAlertLoading();
    $.ajax({
      //laravel requires this thing, it fetches it from the meta up in the head
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: "POST",
      url: "/admin/divisiontwob/patients/patient_update",
      data: JSON.stringify(data),
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      success: function(msg) {
       
        if (msg.errors) {
          $.each(msg.errors, function(key, val) {
            sweetAlert2('warning', 'Check field inputs.');
            $("#EditForm #" + key).after('<small class="error_txt">' + val[0] + '</small>');
          });
          $("#save_btn").val('Save');
          $("#save_btn").removeAttr('disabled');

        } else {
          //success
          window.location.reload(true);
           
        }
      },
      error: function(msg) {
        handleErrorResponse(msg);
         $("#save_btn").val('Save');
          $("#save_btn").removeAttr('disabled');
        //general error
        console.log("Error");
        console.log(msg.responseText);
      }
    });
  }



</script>