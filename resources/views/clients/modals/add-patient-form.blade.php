<div class="modal " id="add_form_modal" style="display:none;" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Add new Patient</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <form action="" method="POST" id="AddForm">
          <div class="container">
              <div class="row">
                  <div class="col-md-6">
                      <div class="mb-3">
                          <label for="addonApplied" class="form-label">First name</label>
                          <input type="text" class="form-control" id="firstname">
                      </div>
                      <div class="mb-3">
                          <label for="addonApplied" class="form-label">Last name</label>
                          <input type="text" class="form-control" id="lastname">
                      </div>
                      <div class="mb-3">
                          <label for="birthdate" class="form-label">Patient Date of Birth</label>
                          <input class="form-control datepicker" id="birthdate">
                          <small>mm/dd/yyyy</small>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="mb-3">
                          <label for="address" class="form-label">Address</label>
                          <input type="text" class="form-control" id="address">
                      </div>
                      <div class="mb-3">
                          <label for="city" class="form-label">City</label>
                          <input type="text" class="form-control" id="city">
                      </div>
                      <div class="mb-3">
                          <label for="state" class="form-label">State</label>
                          <input type="text" class="form-control" id="state">
                      </div>
                      <div class="mb-3">
                          <label for="zip_code" class="form-label">Zip code</label>
                          <input type="text" class="form-control" id="zip_code">
                      </div>
                      <div class="mb-3">
                          <label for="phone_number" class="form-label">Phone number</label>
                          <input type="text" class="form-control" id="phone_number">
                      </div>
                  </div>
              </div>
          </div>
      </form>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="save_btn" onclick="SaveNewPatient()">Save</button>
      </div>
    </div>
  </div>
</div>
<script>
  function ShowAddPatientForm () {
    $('#add_form_modal').modal('show');
    console.log('fire');
  }

  function SaveNewPatient() {
    
    $('.alert').remove();
    $('.error_txt').remove();
    $("#save_btn").val('Saving... please wait!');
    $("#save_btn").attr('disabled', 'disabled');

    //Magic: maps all the inputs data
    var data = {};
    $('#AddForm input, textarea, select').each(function() {
      data[this.id] = this.value;
    });



  console.log(data);
    $.ajax({
      //laravel requires this thing, it fetches it from the meta up in the head
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: "POST",
      url: "/admin/patients/add_patient_via_ajax",
      data: JSON.stringify(data),
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      success: function(msg) {
         $("#save_btn").val('Save');
          $("#save_btn").removeAttr('disabled');

        if (msg.errors) {
          $.each(msg.errors, function(key, val) {
            $("#" + key).after('<small class="error_txt">' + val[0] + '</small>');
          });
         

        } else {
          var patient = msg.data;
          //success
               $("#AddForm").before( '<div class="alert alert-success" role="alert">New patient succesfully added!</div>');
               
              var newRow = '<tr id="row-' + patient.id + '">' +
                '<td><a href="/admin/patient/' + patient.id + '">' + patient.firstname +  '</a></td>' +
                '<td><a href="/admin/patient/' + patient.id + '">'  + patient.lastname + '</a></td>' +
                '<td>' + patient.birthday + '</td>' +
                '<td>' + patient.address + '</td>' +
                '<td>' + patient.city + '</td>' +
                '<td>' + patient.state + '</td>' +
                '<td>' + patient.zip_code + '</td>' +
                '<td>' + patient.phone_number + '</td>' +
                '<td>' + patient.created + '</td>' +
                '<td>' + patient.updated + '</td>' +
                '<td>' +
                '<button type="button" class="btn btn-primary btn-sm" onclick="ShowEditForm(' + patient.id + ',\'' + patient.firstname + '\',\'' + patient.lastname + '\',\'' + patient.birthday + '\',\'' + patient.address + '\',\'' + patient.city + '\',\'' + patient.state + '\',\'' + patient.zip_code + '\',\'' + patient.phone_number + '\')" style="margin-right:4px;"><i class="fa-solid fa-pencil"></i></button>' +
                '<button type="button" class="btn btn-secondary btn-sm" onclick="ShowConfirmDeleteForm(\'' + patient.firstname + '\', \'' + patient.lastname + '\', ' + patient.id + ')"><i class="fa-solid fa-trash-can"></i></button>' +
                '</td>' +
                '</tr>';

            // Prepend the new row to the table
            $('#patients_table tbody').prepend(newRow);

            $('#AddForm')[0].reset();

             console.log(patient);
        }
      },
      error: function(msg) {
         $("#save_btn").val('Save');
          $("#save_btn").removeAttr('disabled');
        //general error
        console.log("Error");
        handleErrorResponse(msg);
        console.log(msg.responseText);
      }
    });
  }



</script>