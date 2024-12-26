<div class="modal" id="edit_wholesale_drug_return_modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Return Form</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            
                    <!--start row-->
                    <div class="row">
                        <form action="" method="POST" id="#wholesale_drug_return_edit_form">
                            <div class="col-lg-12">
                                <input type="text" name="eid" class="form-control" id="id" hidden>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="reference_number" class="form-label">Reference Number</label>
                                        <input type="text" name="ereference_number" class="form-control" id="reference_number" placeholder="Enter Order Number" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="prescriber_name" class="form-label">Prescriber Name <span class="text-danger">*</span></label>
                                        <input type="text" name="eprescriber_name" class="form-control" id="prescriber_name" placeholder="Prescriber Name">
                                    </div>
                                    <div class="col-md-6">
                                        <label label for="patient_name" class="form-label">For Patient <span class="text-danger">*</span></label>
                                        <input class="form-control" name="epatient_name" id="patient_name" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="shipment_status" class="form-label">Shipment Status</label>
                                        <select class="form-select" name="eshipment_status_id" id="shipment_status_id"></select>
                                    </div>
                                </div> 
                                <div class="mt-4 card" style="background-color: #dbc1ff6e;">
                                    <div class="card-header">
                                        <b class="ms-3">Item Medications</b>
                                    </div>
                                    <div class="p-3 card-body">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label for="emed_id" class="form-label">Drug Name</label>
                                                <select class="form-select" data-placeholder="Select medication.." name="emed_id" id="med_id" title="Drug Selection"></select>
                                            </div>
                                            <div class="col">
                                                <label for="einventory_type" class="form-label">Inventory Type</label>
                                                <select class="form-select" name="einventory_type" id="inventory_type">
                                                    <option value="">Select</option>
                                                    <option value="RX">RX</option>
                                                    <option value="340B">340B</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label for="edispense_quantity" class="form-label">Quantity</label>
                                                <input type="number" class="form-control text-end" min="1" name="edispense_quantity" id="dispense_quantity" placeholder="Qty">
                                            </div>
                                            <div class="col">
                                                <label for="endc" class="form-label">NDC</label>
                                                <input type="text" class="form-control" name="endc" id="ndc" placeholder="NDC">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="reject_comments" class="form-label">Reject Comments</label>
                                        <textarea rows="3" name="ereject_comments" class="form-control" id="reject_comments" placeholder=""></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--end row-->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save_btn" onclick="updateForm()">Submit</button>
            </div>
        </div>
    </div>
</div>



<script>    
    function updateForm(){
        menu_store_id = {{request()->id}}
        let fill_order = [
            'reference_number', 'date_filed', 'reject_comments', 'patient_id', 'prescriber_name', 'service', 'package', 'size', 'shipment_tracking_number', 'size', 'from_pharmacy_store_id', 'to_pharmacy_store_id', 'shipment_status_id'
        ];
        let fill_items = [
            'id', 'dispense_quantity', 'inventory_type', 'ndc', 'med_id'
        ];
        let data = {
            order: {
                pharmacy_store_id: menu_store_id
            },
            items: {}
        };

        $('#edit_wholesale_drug_return_modal input, #edit_wholesale_drug_return_modal textarea, #edit_wholesale_drug_return_modal select').each(function() {
            if(fill_order.includes(this.id)) {
                data.order[this.id] = this.value;
            }
            if(fill_items.includes(this.id)) {
                data.items[this.id] = this.value;
            }
        });

        data.items['med_id'] = $(`[name='emed_id']`).find(":selected").val();
        console.log(data);
        
        sweetAlertLoading();

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/procurement/pharmacy/wholesale-drug-returns/edit`,
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
                    table_drug_order.ajax.reload(null, false);
                    sweetAlert2('success', 'Record has been saved.');
                    $('#edit_wholesale_drug_return_modal').modal('hide');
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