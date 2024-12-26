<div class="modal " style="display:none" id="view_order_modal" tabindex="-1">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Order Details</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>


      <div class="modal-body">
                 <form action="" method="POST" id="#EditOrderTable">
                                   <div class="container">
                                   
                                                  
                                                <div class="row">
                                                    
                                                    <div class="col">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">Patient</th>
                                                                    <th scope="col">Tracking Number</th>
                                                                    <th scope="col">Order Number</th>
                                                                    <th scope="col">Shipping Status</th>
                                                                    <th scope="col">Shipment from</th>
                                                                    <th scope="col">Requested by</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td id="patient"></td>
                                                                    <td id="tracking_no"></td>
                                                                    <td id="order_no"></td>
                                                                    <td id="shipping_status"></td>
                                                                    <td id="shipment_from_store"></td>
                                                                    <td id="requested_by_store"></td>
                                                                
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                 <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="medications" class="form-label" id="medication_label">Medications</label>
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered" id="medication_table">
                                                                        <thead>
                                                                                <th>Name</th>
                                                                                <th>SIG</th>
                                                                                <th>Days Supply</th>
                                                                                <th>Refills Left</th>
                                                                                <th>NDC</th>
                                                                            
                                                                                
                            
                                                                        </thead>
                                                                        <tbody id="medications_table">
                                                                           
                                                                            
                                                                            
                                                                        </tbody>
                                                                    </table>
                                                                    
                                                                </div>
                                                                
                                                            </div>
                                                        </div>
                                                        
                                                 </div>
                                     </div>

                                </form>
                        

      </div>
      <div class="modal-footer justify-content-between">
        <div class="row text-left"><span id="status_message" ></span></div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        
      </div>
    </div>
  </div>
</div>

<script>


</script>