<div class="modal" id="addClinicOrder_modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h6 class="modal-title">Order Form</h6>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
        
                <!--start row-->
                <div class="row">
                    <form action="" method="POST" id="#clinicOrderAddForm">
                    <div class="col-lg-12">
                        
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="order_number" class="form-label">Order Number</label>
                                    <input type="text" readonly name="order_number" class="form-control" id="order_number" placeholder="Enter Order Number">
                                </div>
                                <div class="col-md-6">
                                    <label label for="clinic_name" class="form-label">Clinic/External Location <span class="text-danger">*</span></label>
                                    <select class="form-select" data-placeholder="Select Clinic.." name="clinic_id" id="clinic_id" title="Select Clinic Location"></select>
                                </div>
                                <div class="col-md-6">
                                    <label for="order_date" class="form-label">Order Date</label>
                                    <div class="input-group"> <span class="input-group-text" id="icon-order-date"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control datepicker" id="order_date" name="order_date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="prescriber_name" class="form-label">Prescriber Full Name</label>
                                    <input type="text" name="prescriber_name" class="form-control" id="prescriber_name" placeholder="Enter Prescriber Full Name">
                                </div>
                                <!-- <div class="col-md-6">
                                    <label label for="patient_name" class="form-label">For Patient <span class="text-danger">*</span></label>
                                    <select class="form-select" data-placeholder="Select Patient.." name="patient_id" id="patient_id" title="Select Patient Name"></select>
                                </div> -->
                            </div> 
                            <div class="card" style="margin-top: 20px;">
                                <div class="p-4 card-body">
                                    <h6 class="card-title" id="medication_holder">Medications*</h6>
                                    
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th WIDTH="40%">Drug Name</th>
                                                
                                                <th WIDTH="10%">Quantity</th>
                                                <th WIDTH="20%">NDC</th>
                                            </thead>
                                            <tbody>
                                                @for ($i = 0; $i < 3; $i++)
                                                    <tr>
                                                        <td><select class="form-select" data-placeholder="Select medication.." name="items[{{$i}}][drugname]" id="drugname{{$i}}" title="Drug Selection"></select></td>
                                                        <td><input type="text" class="form-control number_only" name="items[{{$i}}][quantity]" id="quantity{{$i}}" placeholder="Type Qty"></td>
                                                        <td><input type="text" class="form-control auto_width" name="items[{{$i}}][ndc]" id="ndc{{$i}}" placeholder="Type Here"></td>
                                                    </tr>
                                                @endfor
                                                <tr><td colspan="3"><a href="javascript:;" onclick="moreMedication()" id="more_med">+ Add more medications</a></td></tr>
                                            </tbody>
                                        </table>
                                    </div> 
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="comments" class="form-label">Comments</label>
                                    <textarea rows="3" name="comments" class="form-control" id="comments" placeholder=""></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!--end row-->

          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" id="save_btn" onclick="saveForm()">Submit</button>
          </div>
          </div>
    </div>
  </div>
  
  <script>
      let menu_store_id = {{request()->id}};
      var medCount = 2; //0 included
  
      function moreMedication()
      {
          var tableRow = $(`#drugname${medCount}`);
          medCount++;
          tableRow.closest('tr').after('<tr class="rowToRemove"><td><select data-placeholder="Select medication.." class="form-select" name="items['+medCount+'][drugname]" id="drugname'+medCount+'" title="Drug Selection"></select></td><td><input type="text" class="form-control number_only" name="items['+medCount+'][quantity]" id="quantity'+medCount+'" placeholder="Type Qty"></td><td><input type="text" class="form-control auto_width" name="items['+medCount+'][ndc]" id="ndc'+medCount+'" placeholder="Type Here"></td></tr>');

          searchSelect2ApiDrug(`drugname${medCount}`, 'addClinicOrder_modal');
  
          $('.number_only').keyup(function(e){
              if (/\D/g.test(this.value))
              {
                  // Filter non-digits from input value.
                  this.value = this.value.replace(/\D/g, '');
              }
          });
  
        //   $(".auto_width").on('keyup', function(){
          
        //       elementId = $(this).prop('id');
  
        //       let width = $(this).val().length * 10 + 25;
  
        //       $(this).css('width', width +"px");
        //   });
      }

      
      function saveForm(){
        menu_store_id = {{request()->id}}
          $("#save_btn").val('Saving... please wait!');
          $("#save_btn").attr('disabled','disabled');
          $('.error_txt').remove();
          let data = {};
  
          $('#addClinicOrder_modal input, #addClinicOrder_modal textarea, #addClinicOrder_modal select').each(function() {
              data[this.id] = this.value;
          });
          data['med_count'] = medCount;
          data['pharmacy_store_id'] = menu_store_id;
          sweetAlertLoading();


          $.ajax({
              //laravel requires this thing, it fetches it from the meta up in the head
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "POST",
              url: `/store/procurement/${menu_store_id}/clinical-orders/add`,
              data: JSON.stringify(data),
              contentType: "application/json; charset=utf-8",
              dataType: "json",
              success: function(data) {
                  $("#save_btn").val('Save');
                  $("#save_btn").removeAttr('disabled');
                  reloadDataTable();
                  if(data.errors){
                      $.each(data.errors,function (key , val){
                          sweetAlert2('warning', 'Check field inputs.');
                          $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                          console.log(key);
                      });
                  }
                  else{
                      table_clinicOrders.ajax.reload(null, false);
                      sweetAlert2('success', 'Record has been saved.');
                      $('#addClinicOrder_modal').modal('hide');
                  }
              },error: function(msg) {
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