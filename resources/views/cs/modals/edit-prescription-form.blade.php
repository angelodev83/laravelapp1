<div class="modal fade" style="display:none;" id="edit_prescription_modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Edit prescription</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

                   <form action="" method="POST" id="EditPrescriptionForm">
                                   <div class="container">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                          <input type="hidden" class="form-control" id="edit_prescription_id" >
                                                              <div class="mb-3">
                                                                <label for="medications" class="form-label">Order Number</label>
                                                                <input type="text" class="form-control" id="edit_order_number" maxlength="8" pattern="\d{1,8}">
                                                            </div>
                                                              <div class="mb-3">
                                                                <label for="medications" class="form-label">Medications</label>
                                                                <input type="text" class="form-control" id="edit_medications">
                                                             </div>
                                                            <div class="mb-3">
                                                                <label for="dosage" class="form-label">SIG</label>
                                                                <input type="text" class="form-control" id="edit_sig">
                                                            </div>
                                                           
                                                            <div class="mb-3">
                                                                <label for="edit_days_supply" class="form-label">Days Supply</label>
                                                                <input type="text" class="form-control" id="edit_days_supply">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="refills_requested" class="form-label">Refills Requested</label>
                                                                <input type="text" class="form-control" id="edit_refills_requested">
                                                            </div>
                                                           
                                                           <div class="mb-3">
                                                                <label for="addonApplied" class="form-label">Request type</label>
                                                                <select class="form-select" id="edit_request_type">
                                                                    <option value="New Scripts">New Scripts</option>
                                                                    <option value="For Renewal">For Renewal</option>
                                                                    <option value="Telemed">Telemed</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="npi" class="form-label">NPI</label>
                                                                <input type="text" class="form-control" id="edit_npi">
                                                            </div>
                                                            
                                                            <!-- Add more fields for the first column -->
                                                        </div>

                                                        <div class="col-md-6">
                                                         
                                                           
                                                            <div class="mb-3">
                                                                <label for="prescriberName" class="form-label">Prescriber Name</label>
                                                                <input type="text" class="form-control" id="edit_prescriber_name">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="prescriberFax" class="form-label">Prescriber Phone</label>
                                                                <input type="text" class="form-control" id="edit_prescriber_phone">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="prescriberFax" class="form-label">Prescriber Fax</label>
                                                                <input type="text" class="form-control" id="edit_prescriber_fax">
                                                            </div>
                                                             <div class="mb-3">
                                                                <label for="prescriberFax" class="form-label">Submitted by</label>
                                                                <input type="text" class="form-control" id="edit_submitted_by">
                                                            </div>
                                                            <div class="mb-3">
                                                                  <label for="status" class="form-label">Status</label>
                                                                  <select class="form-select" id="edit_status">
                                                                    
                                                                       @foreach ($statuses as $status)
                                                                          <option value="{{ $status->id }}" >{{ $status->name }}</option>
                                                                       @endforeach
                                                                  </select>
                                                              </div>
                                                               <div class="mb-3">
                                                                  <label for="stages" class="form-label">Stages</label>
                                                                  <select class="form-select" id="edit_stage">
                                                                        @foreach ($stages as $stage)
                                                                          <option value="{{ $stage->id }}" >{{ $stage->name }}</option>
                                                                       @endforeach
                                                                  </select>
                                                              </div>
                                                               <div class="mb-3">
                                                                 <label for="special_instructions" class="form-label">Special instructions</label>
                                                                  <input type="text" class="form-control" id="edit_special_instructions">
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
                                                                       <td><input type="text" class="form-control " id="edit_received_at" placeholder="MM/DD/YYYY" value="{{ date('m/d/Y') }}"></td>
                                                                        <td><input type="text" class="form-control datepicker" id="edit_sent_at" placeholder="MM/DD/YYYY" value="{{ date('m/d/Y') }}"></td>
                                                                        <td><input type="text" class="form-control datepicker" id="edit_submitted_at" placeholder="MM/DD/YYYY" value="{{ date('m/d/Y') }}"></td>

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
        <button type="button" class="btn btn-primary" id="save_btn" onclick="EditPrescription()">Save changes</button>
      </div>
    </div>
  </div>
</div>
    <script>
    
          function ShowEditPrescriptionForm(prescription_id) {
                $('#edit_prescription_modal').modal('show');
               const button = $(`#edit_btn_${prescription_id}`);
            
                
                        $('#edit_prescription_id').val(prescription_id);
                        $('#edit_order_number').val(button.data('order_number'));
                        
                        $('#edit_medications').val(button.data('medications'));
                        $('#edit_sig').val(button.data('sig'));
                       
                        $('#edit_days_supply').val(button.data('days_supply'));
                        $('#edit_refills_requested').val(button.data('refills_requested'));
                        $('#edit_patient_id').val(button.data('patient_id'));
                        
                        $('#edit_stage').val(button.data('stage'));
                        $('#edit_status').val(button.data('status'));

                        $('#edit_npi').val(button.data('npi'));
                        $('#edit_request_type').val(button.data('request_type'));
                        
                        $('#edit_prescriber_name').val(button.data('prescriber_name'));
                        $('#edit_prescriber_phone').val(button.data('prescriber_phone'));
                        $('#edit_prescriber_fax').val(button.data('prescriber_fax'));
                       
                        $('#edit_submitted_at').val(button.data('submitted_at'));
                        $('#edit_sent_at').val(button.data('sent_at'));
                        $('#edit_received_at').val(button.data('received_at'));
                        $('#edit_submitted_by').val(button.data('submitted_by'));
                        $('#edit_special_instructions').val(button.data('special_instructions'));
               
                console.log('fire');
          }

           function EditPrescription(){
            $("#save_btn").val('Saving... please wait!');
            $("#save_btn").attr('disabled','disabled');
             $('.alert').remove();
             $('.error_txt').remove();

            //Magic: maps all the inputs data
            var data = {};
           
            $('input, textarea, select').filter(function() {
                return this.id.startsWith('edit_');
            }).each(function() {
                data[this.id] = this.value;
            });
             data['prescription_id'] = $('#edit_prescription_id').val();
            
            console.log(data);
          
            $.ajax({
                //laravel requires this thing, it fetches it from the meta up in the head
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: "/admin/patient/edit_prescription_via_ajax",
                data: JSON.stringify(data),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(msg) {
                    $("#save_btn").val('SAVE');
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

                    $("#add_user_btn").val('SAVE');
                    $("#add_user_btn").removeAttr('disabled');
                    //general error
                    console.log("Error");
                    handleErrorResponse(msg);
                    console.log(msg.responseText);
                }


            });

          }


    </script>
