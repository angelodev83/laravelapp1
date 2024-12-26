<div class="modal" id="add_wholesale_drug_return_modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Return Form</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            
                    <!--start row-->
                    <div class="row">
                        <form action="" method="POST" id="#wholesale_drug_return_add_form">
                            <div class="col-lg-12">
                            
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="reference_number" class="form-label">Reference Number</label>
                                        <input type="text" name="reference_number" class="form-control" id="reference_number" placeholder="Enter Reference Number">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="prescriber_name" class="form-label">Prescriber Name <span class="text-danger">*</span></label>
                                        <input type="text" name="prescriber_name" class="form-control" id="prescriber_name" placeholder="Prescriber Name">
                                    </div>
                                    <div class="col-md-6">
                                        <label label for="patient_name" class="form-label">For Patient <span class="text-danger">*</span></label>
                                        <select class="form-select" data-placeholder="Select Patient.." name="patient_id" id="patient_id" title="Select Patient Name"></select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="shipment_status" class="form-label">Shipment Status</label>
                                        <select class="form-select" name="shipment_status_id" id="shipment_status_id"></select>
                                    </div>
                                </div> 
                                <div class="mt-4 col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th width="50%">Drug Name</th>
                                                <th width="15%">Inventory Type</th>
                                                <th width="14%">Dispense Qty</th>
                                                <th width="20%">NDC</th>
                                            </thead>
                                            <tbody>
                                                @for ($i = 0; $i < 3; $i++)
                                                    <tr>
                                                        <td class="appending-items-data-table">
                                                            <select class="form-select" data-placeholder="Select medication.." name="med_id" id="med_id-{{$i}}" title="Drug Selection"></select>
                                                        </td>
                                                        <td class="appending-items-data-table">
                                                            <select class="form-select" name="inventory_type" id="inventory_type-{{$i}}">
                                                                <option value="">Select</option>
                                                                <option value="RX">RX</option>
                                                                <option value="340B">340B</option>
                                                            </select>
                                                        </td>
                                                        <td class="appending-items-data-table">
                                                            <input type="number" class="form-control text-end" min="1" name="dispense_quantity" id="dispense_quantity-{{$i}}" placeholder="Qty">
                                                        </td>
                                                        <td class="appending-items-data-table">
                                                            <input type="text" class="form-control" name="ndc" id="ndc-{{$i}}" placeholder="NDC">
                                                        </td>
                                                    </tr>
                                                @endfor
                                                <tr>
                                                    <td colspan="4">
                                                        <a href="javascript:;" onclick="addItems()" id="append_item"><b>+ Add more items</b></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div> 
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="reject_comments" class="form-label">Reject Comments</label>
                                        <textarea rows="3" name="reject_comments" class="form-control" id="reject_comments" placeholder=""></textarea>
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
    var i = 2; //0 included

    function addItems()
    {
        var tableRow = $(`#med_id-${i}`);
        i++;
        tableRow.closest('tr').after(`<tr>
            <td class="appending-items-data-table">
                <select class="form-select" data-placeholder="Select medication.." name="med_id" id="med_id-${i}" title="Drug Selection"></select>
            </td>
            <td class="appending-items-data-table">
                <select class="form-select" name="inventory_type" id="inventory_type-${i}">
                    <option value="">Select</option>
                    <option value="RX">RX</option>
                    <option value="340B">340B</option>
                </select>
            </td>
            <td class="appending-items-data-table">
                <input type="number" class="form-control text-end" min="1" name="dispense_quantity" id="dispense_quantity-${i}" placeholder="Qty">
            </td>
            <td class="appending-items-data-table">
                <input type="text" class="form-control" name="ndc" id="ndc-${i}" placeholder="NDC">
            </td>
        </tr>`);
        searchSelect2ApiDrug(`#med_id-${i}`, 'add_wholesale_drug_return_modal');
    }

    
    function saveForm(){
        menu_store_id = {{request()->id}}
        let fill_order = [
            'reference_number', 'date_filed', 'reject_comments', 'service', 'package', 'size', 'prescriber_name', 'patient_id', 'shipment_tracking_number', 'size', 'from_pharmacy_store_id', 'to_pharmacy_store_id', 'shipment_status_id'
        ];
        let data = {
            order: {
                pharmacy_store_id: menu_store_id
            },
            items: {
                med_id: [], ndc: [], inventory_type: [], dispense_quantity: []
            }
        };

        console.log('fire before',data);
        $('#add_wholesale_drug_return_modal input, #add_wholesale_drug_return_modal textarea, #add_wholesale_drug_return_modal select').each(function() {
            if(fill_order.includes(this.id)) {
                data.order[this.id] = this.value;
            } else {
                data.items[this.name].push(this.value);
            }
        });

        console.log('fire after',data);
        // return;
        
        sweetAlertLoading();

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/procurement/pharmacy/wholesale-drug-returns/add`,
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
                    $('#add_wholesale_drug_return_modal').modal('hide');
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