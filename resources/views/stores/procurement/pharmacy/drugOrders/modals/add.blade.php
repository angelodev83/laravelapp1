<div class="modal" id="add_drug_order_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Drug Order Form</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            
                    <!--start row-->
                    <div class="row">
                        <form action="" method="POST" id="#drug_order_add_form">
                            <div class="col-lg-12">
                            
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="order_number" class="form-label">PO Name <span class="text-danger">*</span></label>
                                        <input type="text" name="order_number" class="form-control" id="order_number" placeholder="PO Name">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="order_date" class="form-label">Order Date</label>
                                        <div class="input-group"> <span class="input-group-text" id="icon-order-date"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control" id="order_date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="wholesaler_name" class="form-label">Wholesaler <span class="text-danger">*</span></label>
                                        <input type="text" name="wholesaler_name" class="form-control" id="wholesaler_name" placeholder="Wholesaler Name" hidden>
                                        <select class="form-select" name="wholesaler_id" id="wholesaler_id" required></select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="account_number" class="form-label">Account Number <span class="text-danger">*</span></label>
                                        <input type="text" name="account_number" class="form-control" id="account_number" placeholder="Account Number">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" name="status_id" id="status_id"></select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="po_memo" class="form-label">PO Memo <span class="text-danger">*</span></label>
                                        <input type="text" name="po_memo" class="form-control" id="po_memo" placeholder="PO Memo">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="comments" class="form-label">Comments</label>
                                        <textarea rows="3" name="comments" class="form-control" id="comments" placeholder=""></textarea>
                                    </div>
                                </div> 
                                <div class="mt-4 col-md-12" hidden>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th width="50%">Drug Name</th>
                                                <th width="15%">Inventory Type</th>
                                                <th width="14%">Quantity</th>
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
                                                            <input type="number" class="form-control text-end" min="1" name="quantity" id="quantity-{{$i}}" placeholder="Qty">
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
                                {{-- <div class="mt-2 row g-3">
                                    <div class="col-md-12">
                                        <label for="documents" class="form-label">Attachments</label>
                                        <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                        <input id="documents" class="imageuploadify-file-general-class" name="documents[]" type="file" accept="*" multiple>
                                    </div>
                                </div> --}}
                                <div class="mt-2 row g-3">
                                    <div class="col-md-12">
                                        <label for="documents" class="form-label">Import File to Upload Items (Accepts <span class="text-success">CSV</span> or <span class="text-success">XLSX</span> Files Only)</label>
                                        <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                        <input id="documents" class="imageuploadify-file-general-class" name="documents" type="file" accept=".xlsx,.xls,.csv">
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
                <input type="number" class="form-control text-end" min="1" name="quantity" id="quantity-${i}" placeholder="Qty">
            </td>
            <td class="appending-items-data-table">
                <input type="text" class="form-control" name="ndc" id="ndc-${i}" placeholder="NDC">
            </td>
        </tr>`);
        searchSelect2ApiDrug(`#med_id-${i}`, 'add_drug_order_modal');
    }

    
    function saveForm(){
        menu_store_id = {{request()->id}}
        let fill_order = [
            'order_number', 'order_date', 'comments', 'service', 'package', 'size', 'shipment_tracking_number', 'size', 'from_pharmacy_store_id', 'to_pharmacy_store_id', 'shipment_status_id', 'po_name', 'account_number', 'wholesaler_name', 'status_id', 'wholesaler_id', 'po_memo'
        ];
        let fill_prescription = [
            'consultation_disclosure', 'medication_guide', 'campaign_inserts', 'patient_id', 'prescriber_name'
        ];
        let fill_item = [
            'med_id', 'inventory_type', 'quantity', 'ndc'
        ];
        let data = {
            order: {
                pharmacy_store_id: menu_store_id
            },
            prescription: {
                pharmacy_store_id: menu_store_id
            },
            items: {
                med_id: [], ndc: [], inventory_type: [], quantity: []
            }
        };

        $('#add_drug_order_modal input, #add_drug_order_modal textarea, #add_drug_order_modal select').each(function() {
            if(fill_order.includes(this.id)) {
                data.order[this.id] = this.value;
            }
            if(fill_prescription.includes(this.id)) {
                data.prescription[this.id] = this.value;
            }
            if(fill_item.includes(this.name)) {
                data.items[this.name].push(this.value);
            }
        });


        var formData = new FormData();
        var uploadFiles = $('#documents').get(0).files;
        
        for (let i = 0; i < uploadFiles.length; i++) {
            formData.append("files[]", uploadFiles[i]);
            var kbSize = uploadFiles[i].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }
        formData.append("data", JSON.stringify(data));
        
        sweetAlertLoading();

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/procurement/pharmacy/drug-orders/add`,
            data: formData,
            contentType: false,
            processData: false,
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
                    $('#add_drug_order_modal').modal('hide');
                }
            },
            error: function(msg) {
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