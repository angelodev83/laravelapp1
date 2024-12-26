<div class="modal " style="display:s;" id="add_order_modal" tabindex="-1">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Add new Order</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>


      <div class="modal-body">
            <div class="container">
                <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label for="medications" class="form-label">Select a Customer</label>
                                <input id="patient_name" class="form-control mb-2" name="patient_name" type="text" value="{{ $visitor->cart->customer->firstname ?? '' }} {{ $visitor->cart->customer->lastname ?? '' }}" />
                                <div id="search_results">
                                 <ul class="list-group text-start"></ul>
                                </div>
                            </div>
                                        
                                        <input id="patient_id" class="form-control mb-0" name="patient_id" type="hidden" />
                                       
                                    <span>Enter the patient's name in the input above to choose. Click <a href="/admin/patients"><u>here</u></a> to create a new patient record.</span>
                            
                        </div>
                </div>
            </div>
       
                                 <form action="" method="POST" id="#AddPrescriptionTable">
                                   <div class="container">
                                                 <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="medications" class="form-label">Order Number</label>
                                                                <input type="text" class="form-control" id="order_number" maxlength="8" pattern="\d{1,8}">
                                                                
                                                            </div>
                                                        </div>
                                                        
                                                 </div>
                                                 <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="medications" class="form-label">Medications</label>
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered">
                                                                        <thead>
                                                       
                                                                                <th>Name</th>
                                                                                <th>SIG</th>
                                                                                <th>Days Supply</th>
                                                                                <th>Refills Left</th>
                                                                                <th>NDC</th>
                                                                                <th>Inventory Type</th>
                            
                                                                        </thead>
                                                                        <tbody>
                                                                            @for ($i = 0; $i < 5; $i++)
                                                                                <tr>
                                                                                    <td><input type="text" class="form-control" name="items[{{$i}}][name]" id="name{{$i}}"></td>
                                                                                    <td><input type="text" class="form-control" name="items[{{$i}}][sig]" id="sig{{$i}}"></td>
                                                                                    <td><input type="text" class="form-control" name="items[{{$i}}][days_supply]" id="days_supply{{$i}}"></td>
                                                                                    <td><input type="text" class="form-control" name="items[{{$i}}][refills_left]" id="refills_left{{$i}}"></td>
                                                                                    <td><input type="text" class="form-control" name="items[{{$i}}][ndc]" id="ndc{{$i}}"></td>
                                                                                    <td>
                                                                                        <select class="form-control" name="items[{{$i}}][inventory_type]" id="inventory_type{{$i}}">
                                                                                            <option value="">-- Select --</option>
                                                                                            <option value="RX">RX</option>
                                                                                            <option value="340B">340B</option>
                                                                                        </select>
                                                                                    </td>
                                                                                </tr>
                                                                            @endfor
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                
                                                            </div>
                                                        </div>
                                                        
                                                 </div>
                                                    



                                                    


                                                   

                                                    
                                      </div>

                                </form>
                        

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="save_btn" onclick="SaveNewOrder()">Create Order</button>
      </div>
    </div>
  </div>
</div>
    <script>

          function ShowAddOrderForm() {
                $('#add_order_modal').modal('show');
                console.log('fire');
          }

           function SaveNewOrder(){
            $("#save_btn").val('Saving... please wait!');
            $("#save_btn").attr('disabled','disabled');
             $('.alert').remove();
             $('.error_txt').remove();

            //Magic: maps all the inputs data
            var data = {};

            $('#add_order_modal input, #add_order_modal textarea, #add_order_modal select').each(function() {
                data[this.id] = this.value;
            });
           

            console.log(data);
          
            $.ajax({
                //laravel requires this thing, it fetches it from the meta up in the head
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: "/admin/patient/add_order_via_ajax",
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
