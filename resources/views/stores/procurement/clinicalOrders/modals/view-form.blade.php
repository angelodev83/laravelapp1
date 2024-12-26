<div class="modal" id="view_modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title" id="vinmar_id_text"></h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <form action="" method="POST" id="#viewForm">
                <div class="col-lg-12">
                        <input type="hidden" id="vid" name="id" value="">
                            
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label label for="order_number" class="form-label">Order No.</label>
                                <p id="order_number"></p>
                            </div>
                            <div class="col-md-6">
                                <label label for="tracking_number" class="form-label">Tracking Number</label>
                                <p id="tracking_number"></p>
                            </div>
                            <div class="col-md-6">
                                <label label for="prescriber_name" class="form-label">Prescriber Name</label>
                                <p id="prescriber_name"></p>
                            </div>
                            <div class="col-md-6">
                                <label for="order_date" class="form-label">Order Date</label>
                                <p id="order_date"></p>
                            </div>
                            <div class="col-md-6">
                                <label label for="clinic" class="form-label">Clinic/External Location</label>
                                <p id="clinic" title=""></p>
                            </div>
                            <div class="col-md-6">
                                <label label for="status" class="form-label">Status</label>
                                <p id="status" title=""></p>
                            </div>
                        </div> 
                        <div class="card" style="margin-top: 20px;">
                            <div class="card-body p-4">
                                <h6 class="card-title" id="medication_holder">Medications*</h6>
                                
                                
                                <div class="table-responsive">
                                    <table id="inmarView_table" class="table" cellspacing="0" cellpadding="0" style="border: none;">
                                        <thead>
                                            <th WIDTH="40%">Drug Name</th>
                                            <th WIDTH="10%">Quantity</th>
                                            <th WIDTH="20%">NDC</th>
                                            
                                        </thead>
                                        <tbody>
                                            
                                            <tr>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div> 
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="comments" class="form-label">Comments</label>
                                <p id="comments"></p>
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
    
</script>