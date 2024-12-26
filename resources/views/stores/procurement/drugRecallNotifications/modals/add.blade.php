<div class="modal" id="add_drug_recall_notifications_modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Create Drug Recall Return</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            
                    <!--start row-->
                    <div class="row">
                        <form action="" method="POST" id="#drug_recall_notifications_add_form">
                            <div class="col-lg-12">
                            
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="reference_number" class="form-label">Reference Number</label>
                                        <input type="text" name="reference_number" class="form-control" id="reference_number" placeholder="Enter Reference Number" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="notice_date" class="form-label">Notice Date</label>
                                        <div class="input-group"> <span class="input-group-text" id="icon-order-date"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control" id="notice_date" name="notice_date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="wholesaler_id" class="form-label">Wholesaler</label>
                                        <select class="form-select" name="wholesaler_id" id="wholesaler_id"></select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="supplier_name" class="form-label">Supplier Name</label>
                                        <input type="text" name="supplier_name" class="form-control" id="supplier_name" placeholder="Supplier Name">
                                    </div>
                                </div> 
                                <div class="mt-4 col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th width="40%">Drug Name</th>
                                                <th width="13%">Lot #</th>
                                                <th width="12%">Qty</th>
                                                <th width="17%">NDC</th>
                                                <th width="18%">Exp. Date</th>
                                            </thead>
                                            <tbody>
                                                @for ($i = 0; $i < 3; $i++)
                                                    <tr>
                                                        <td class="appending-items-data-table">
                                                            <select class="form-select" data-placeholder="Select medication.." name="med_id" id="med_id-{{$i}}" title="Drug Selection"></select>
                                                        </td>
                                                        <td class="appending-items-data-table">
                                                            <input type="text" class="form-control form-control-sm" name="lot_number" id="lot_number-{{$i}}" placeholder="Lot #">
                                                        </td>
                                                        <td class="appending-items-data-table">
                                                            <input type="number" class="form-control form-control-sm text-end" min="1" name="qty" id="qty-{{$i}}" placeholder="Qty">
                                                        </td>
                                                        <td class="appending-items-data-table">
                                                            <input type="text" class="form-control form-control-sm" name="ndc" id="ndc-{{$i}}" placeholder="NDC">
                                                        </td>
                                                        <td class="appending-items-data-table">
                                                            <input type="text" class="form-control form-control-sm datepicker" id="expiration_date-{{$i}}" name="expiration_date" placeholder="YYYY-MM-DD">
                                                        </td>
                                                    </tr>
                                                @endfor
                                                <tr>
                                                    <td colspan="5">
                                                        <button id="append_item" class="btn btn-sm btn-secondary w-25" onclick="addNotificationItems()"><i class="fa fa-plus me-3"></i>Add More Item</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div> 
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="comments" class="form-label">Comments</label>
                                        <textarea rows="3" name="comments" class="form-control" id="comments" placeholder=""></textarea>
                                    </div>
                                </div>
                                <div class="mt-2 row g-3">
                                    <div class="col-md-12">
                                        <label for="documents" class="form-label">Attachments</label>
                                        <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                        <input id="documents" class="imageuploadify-file-general-class" name="documents" type="file" accept="*" multiple>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--end row-->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save_btn" onclick="saveDrugRecallNotificationForm()">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
    var i = 2; //0 included

    function addNotificationItems()
    {
        var tableRow = $(`#add_drug_recall_notifications_modal #med_id-${i}`);
        i++;
        tableRow.closest('tr').after(`<tr class="add_drug_recall_notification_item_tr">
            <td class="appending-items-data-table">
                <select class="form-select" data-placeholder="Select medication.." name="med_id" id="med_id-${i}" title="Drug Selection"></select>
            </td>
            <td class="appending-items-data-table">
                <input type="text" class="form-control form-control-sm" name="lot_number" id="lot_number-${i}" placeholder="Lot #">
            </td>
            <td class="appending-items-data-table">
                <input type="number" class="form-control form-control-sm text-end" min="1" name="qty" id="qty-${i}" placeholder="Qty">
            </td>
            <td class="appending-items-data-table">
                <input type="text" class="form-control form-control-sm" name="ndc" id="ndc-${i}" placeholder="NDC">
            </td>
            <td class="appending-items-data-table">
                <input type="text" class="form-control form-control-sm datepicker" id="expiration_date-${i}" name="expiration_date" placeholder="YYYY-MM-DD">
            </td>
        </tr>`);
        $(`#add_drug_recall_notifications_modal #expiration_date-${i}`).datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5',
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true,
        });
        searchSelect2ApiDrug(`#add_drug_recall_notifications_modal #med_id-${i}`, 'add_drug_recall_notifications_modal');
    }

    
    function saveDrugRecallNotificationForm(){
        menu_store_id = {{request()->id}}
        let fill = [
            'reference_number', 'notice_date', 'comments', 'wholesaler_id', 'supplier_name'
        ];
        let data = {
            detail: {
                pharmacy_store_id: menu_store_id
            },
            items: {
                med_id: [], drug_name: [], qty: [], lot_number: [], ndc: [], expiration_date: [], 
            }
        };

        $('#add_drug_recall_notifications_modal input, #add_drug_recall_notifications_modal textarea, #add_drug_recall_notifications_modal select').each(function() {
            if(fill.includes(this.id)) {
                data.detail[this.id] = this.value;
            } else {
                if(this.name != 'documents') {
                    if(this.name == 'med_id') {
                        const selectedText = $(`#add_drug_recall_notifications_modal #${this.id} option:selected`).text();
                        data.items['drug_name'].push(selectedText);
                    }
                    data.items[this.name].push(this.value);
                }
            }
        });

        console.log(data);
    
        var formData = new FormData();
        var uploadFiles = $('#add_drug_recall_notifications_modal #documents').get(0).files;
        
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
            url: `/store/procurement/drug-recall-notifications/add`,
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(res) {
                $("#save_btn").val('Save');
                $("#save_btn").removeAttr('disabled');

                $('#add_drug_recall_notifications_modal .add_drug_recall_notification_item_tr').remove();
                
                const data = res.data;

                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    table_drug_recall_notifications.ajax.reload(null, false);
                    sweetAlert2('success', 'Record has been saved.');
                    $('#add_drug_recall_notifications_modal').modal('hide');
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

    function showAddDrugRecallNotificationModal()
    {
        let currentYear = new Date().getFullYear();
        let lastTwoDigits = currentYear.toString().slice(-2);
        let randomNumber = Math.floor(100000 + Math.random() * 900000);

        $('#add_drug_recall_notifications_modal #reference_number').val(randomNumber+'-'+lastTwoDigits);

        searchSelect2ApiDrug(`#add_drug_recall_notifications_modal #med_id-0`, 'add_drug_recall_notifications_modal');
        searchSelect2ApiDrug(`#add_drug_recall_notifications_modal #med_id-1`, 'add_drug_recall_notifications_modal');
        searchSelect2ApiDrug(`#add_drug_recall_notifications_modal #med_id-2`, 'add_drug_recall_notifications_modal');

        $(`#add_drug_recall_notifications_modal #wholesaler_id`).empty();
        // searchSelect2Api('patient_id', 'add_drug_order_modal', "/admin/patient/getNames", {source: 'pioneer'});
        populateNormalSelect(`#add_drug_recall_notifications_modal #wholesaler_id`, '#add_drug_recall_notifications_modal', '/admin/search/wholesaler', {category: 'procurement'}, 6)
        $('#add_drug_recall_notifications_modal').modal('show');
    }
</script>