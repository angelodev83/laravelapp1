<div class="modal" id="viewInmar_modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title" id="vinmar_id_text"></h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <form action="" method="POST" id="#inmarEditForm">
                <div class="col-lg-12">
                        <input type="hidden" id="vid" name="id" value="">
                            
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label label for="clinic_name" class="form-label">PO Name</label>
                                <p id="vpo_name"></p>
                            </div>
                            <div class="col-md-6">
                                <label label for="clinic_name" class="form-label">Account Number</label>
                                <p id="vaccount_number"></p>
                            </div>
                            <div class="col-md-6">
                                <label label for="clinic_name" class="form-label">Wholesaler Name</label>
                                <p id="vwholesaler_name"></p>
                            </div>
                            <div class="col-md-6">
                                <label for="return_date" class="form-label">Return Date</label>
                                <p id="vreturn_date"></p>
                            </div>
                            <div class="col-md-6">
                                <label label for="status" class="form-label">Status</label>
                                <p id="vstatus" title=""></p>
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
                                <p id="vcomments"></p>
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