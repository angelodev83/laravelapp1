<div class="modal " style="display:none" id="edit_order_modal" tabindex="-1">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Edit Order</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>


      <div class="modal-body">
                 <form action="" method="POST" id="#EditOrderTable">
                                   <div class="container">
                                   
                                                 <div class="row">
                                                         <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="patient_name" class="form-label">Customer</label>
                                                                <input id="patient_name" class="form-control mb-2" name="patient_name" type="text"  />
                                                                <input id="patient_id" class="editable" name="patient_id" type="hidden" data-item_field="patient_id" data-item_id="" />
                                                                <input id="order_id" class="form-control mb-0" name="order_id" type="hidden" />
                                                                <div id="search_results" class="search_results">
                                                                <ul class="list-group text-start"></ul>
                                                                </div>
                                                            </div>
                                                            <span>Enter the patient's name or click <a href="/admin/patients"><u>here</u></a> to create a new patient record.</span>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="medications" class="form-label">Order Number</label>
                                                                <input type="text" class="form-control editable" data-item_field="order_number" data-item_id="" id="order_number" maxlength="11" >
                                                                <span>Integers only</span>
                                                              </div>
                                                        </div>
                                                  </div>
                                                  <div class="row">
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="medications" class="form-label">Tracking Number</label>
                                                                <input type="text" class="form-control editable" data-item_field="shipment_tracking_number" data-item_id="" name="shipment_tracking_number" id="shipment_tracking_number" maxlength="11" >
                                                                
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="medications" class="form-label " >Shipping Status</label>
                                                                    <select id="shipmentStatusFilter" class="form-select editable" data-item_field="shipment_status_id">
                                                                        @foreach($shipment_statuses as $shipment_status)
                                                                            <option value="{{ $shipment_status->id }}">{{ $shipment_status->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                            </div>
                                                        </div>
                                                        
                                                 </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="shipment_from_store" class="form-label">Shipment From Store</label>
                                                            <select id="shipment_from_store" class="form-select editable" data-item_field="shipment_from_store">
                                                                @foreach($stores as $store)
                                                                    <option value="{{ $store }}">{{ $store }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="requested_by_store" class="form-label">Requested By Store</label>
                                                            <select id="requested_by_store" class="form-select editable" data-item_field="requested_by_store">
                                                                @foreach($stores as $store)
                                                                    <option value="{{ $store }}">{{ $store }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
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
                                                                                <th>Inventory Type</th>
                                                                                <th>Actions</th>
                            
                                                                        </thead>
                                                                        <tbody id="medications_table">
                                                                           
                                                                            
                                                                            
                                                                        </tbody>
                                                                    </table>
                                                                    <span id="add_more_link" class="add_more_link">+ Add more</span>
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