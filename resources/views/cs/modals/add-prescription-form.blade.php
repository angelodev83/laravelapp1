<div class="modal modal-xl" style="display:none;" id="add_prescription_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Add new prescription</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
                   <form action="" method="POST" id="#AddPrescriptionTable">
                                   <div class="container">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                          
                                                              <div class="mb-3">
                                                                <label for="medications" class="form-label">Order Number</label>
                                                                <input type="text" class="form-control" id="order_number" maxlength="8" pattern="\d{1,8}">
                                                                
                                                            </div>
                                                              <div class="mb-3">
                                                                <label for="medications" class="form-label">Medications</label>
                                                                <input type="text" class="form-control" id="medications">
                                                                
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="dosage" class="form-label">Dosage</label>
                                                                <input type="text" class="form-control" id="dosage">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="qty" class="form-label">Quantity</label>
                                                                <input type="text" class="form-control" id="qty">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="days_supply" class="form-label">Days Supply</label>
                                                                <input type="text" class="form-control" id="days_supply">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="refills_remaining" class="form-label">Refills Remaining</label>
                                                                <input type="text" class="form-control" id="refills_remaining">
                                                            </div>

                                                             <div class="mb-3">
                                                                <label for="addonApplied" class="form-label">Telemed Bridge</label>
                                                                <select class="form-select" id="telemed_bridge">
                                                                    <option value="1">Yes</option>
                                                                    <option value="0">No</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="addonApplied" class="form-label">Is Addon Applied</label>
                                                                <select class="form-select" id="is_addon_applied">
                                                                    <option value="1">Yes</option>
                                                                    <option value="0">No</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="npi" class="form-label">Requested for</label>
                                                                <input type="text" class="form-control" id="requested_for">
                                                            </div>
                                                           <div class="mb-3">
                                                                <label for="addonApplied" class="form-label">Request type</label>
                                                                <select class="form-select" id="request_type">
                                                                    <option value="New Scripts">New Scripts</option>
                                                                    <option value="For Renewal">For Renewal</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="npi" class="form-label">NPI</label>
                                                                <input type="text" class="form-control" id="npi">
                                                            </div>
                                                            
                                                            <!-- Add more fields for the first column -->
                                                        </div>

                                                        <div class="col-md-6">
                                                         
                                                           
                                                            <div class="mb-3">
                                                                <label for="prescriberName" class="form-label">Prescriber Name</label>
                                                                <input type="text" class="form-control" id="prescriber_name">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="prescriberFax" class="form-label">Prescriber Phone</label>
                                                                <input type="text" class="form-control" id="prescriber_phone">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="prescriberFax" class="form-label">Prescriber Fax</label>
                                                                <input type="text" class="form-control" id="prescriber_fax">
                                                            </div>
                                                             <div class="mb-3">
                                                                <label for="prescriberFax" class="form-label">Submitted by</label>
                                                                <input type="text" class="form-control" id="submitted_by">
                                                            </div>
                                                            <div class="mb-3">
                                                                  <label for="status" class="form-label">Status</label>
                                                                  <select class="form-select" id="status">
                                                                    
                                                                       @foreach ($statuses as $status)
                                                                          <option value="{{ $status->id }}" >{{ $status->name }}</option>
                                                                       @endforeach
                                                                  </select>
                                                              </div>
                                                               <div class="mb-3">
                                                                  <label for="stages" class="form-label">Stages</label>
                                                                  <select class="form-select" id="stage">
                                                                        @foreach ($stages as $stage)
                                                                          <option value="{{ $stage->id }}" >{{ $stage->name }}</option>
                                                                       @endforeach
                                                                  </select>
                                                              </div>
                                                               <div class="mb-3">
                                                                 <label for="special_instructions" class="form-label">Special instructions</label>
                                                                  <input type="text" class="form-control" id="special_instructions">
                                                              </div>
                                                             
                                                            
                                                        </div>
                                                        
                                                    </div>



                                                     <div class="row">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                        <th scope="col" class="header-bg">Received At</th>
                                                                        <th scope="col" class="header-bg">Sent At</th>
                                                                        <th scope="col" class="header-bg">Submitted At</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                        
                                                                       <td><input type="text" class="form-control datepicker" id="received_at" placeholder="MM/DD/YYYY" value="{{ date('m/d/Y') }}"></td>
                                                                        <td><input type="text" class="form-control datepicker" id="sent_at" placeholder="MM/DD/YYYY" value="{{ date('m/d/Y') }}"></td>
                                                                        <td><input type="text" class="form-control datepicker" id="submitted_at" placeholder="MM/DD/YYYY" value="{{ date('m/d/Y') }}"></td>

                                                                        </tr>
                                                                        <!-- Add more rows as needed -->
                                                                    </tbody>
                                                                </table>
                                                        </div>


                                                   

                                                    
                                                </div>

                                </form>
                        

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="save_btn" onclick="SaveNewPrescription()">Save</button>
      </div>
    </div>
  </div>
</div>
    <script>
    
          function ShowAddPrescriptionForm() {
                $('#add_prescription_modal').modal('show');
                console.log('fire');
          }

           function SaveNewPrescription(){
            $("#save_btn").val('Saving... please wait!');
            $("#save_btn").attr('disabled','disabled');
             $('.alert').remove();
             $('.error_txt').remove();

            //Magic: maps all the inputs data
            var data = {};
            
            $('input, textarea, select').each(function() {
                data[this.id] = this.value;
            });
            data['patient_id'] = {{ $patient->id }};

            console.log(data);
          
            $.ajax({
                //laravel requires this thing, it fetches it from the meta up in the head
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: "/admin/patient/add_prescription_via_ajax",
                data: JSON.stringify(data),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(msg) {
                    $("#save_btn").val('Save');
                      $("#save_btn").removeAttr('disabled');
               
                    if(msg.errors){
                     
                      $.each(msg.errors,function (key , val){
                          $("#"+key ).after( '<span class="error_txt">'+val[0]+'</span>' );
                          console.log(key);
                      });
                     

                    }else{
                        //success
                        window.location.reload(true);
                    }


                },error: function(msg) {

                    $("#add_user_btn").val('Save');
                    $("#add_user_btn").removeAttr('disabled');
                    //general error
                    console.log("Error");
                    handleErrorResponse(msg);
                    console.log(msg.responseText);
                }


            });

          }


    </script>
