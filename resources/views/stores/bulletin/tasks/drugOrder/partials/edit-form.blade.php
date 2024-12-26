<!--start row-->
<div class="container">   
    <div class="row">
        <div class="col" id="alert-message-for-drug-order">
        </div>
    </div>                      
    <div class="row">
        {{-- <form action="" method="POST" id="#drug_order_edit_form"> --}}
            <div class="col">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="appending-items-data-table" width="15%">PO Name</th>
                            <td class="appending-items-data-table" width="40%">
                                <input type="text" class="form-control form-control-sm" id="po_name"
                                @cannot('menu_store.procurement.pharmacy.drug_orders.update')
                                    disabled
                                @endcannot
                            >
                            </td>
                            <th class="appending-items-data-table">Order Date</th>
                            <td class="appending-items-data-table">
                                <div class="input-group"> <span class="input-group-text" id="icon-order-date"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control form-control-sm" id="order_date" aria-describedby="icon-order-date" readonly
                                    @cannot('menu_store.procurement.pharmacy.drug_orders.update')
                                        disabled
                                    @endcannot
                                    >
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table">Wholesaler</th>
                            <td class="appending-items-data-table">
                                <input type="text" class="form-control form-control-sm" id="wholesaler_name" hidden>
                                <select class="form-select form-select-sm" name="wholesaler_id" id="wholesaler_id"
                                @cannot('menu_store.procurement.pharmacy.drug_orders.update')
                                    disabled
                                @endcannot></select>
                            </td>
                            <th class="appending-items-data-table">Account #</th>
                            <td class="appending-items-data-table">
                                <input type="text" class="form-control form-control-sm" id="account_number"
                                @cannot('menu_store.procurement.pharmacy.drug_orders.update')
                                    disabled
                                @endcannot
                            >
                            </td>                                        
                        </tr>
                        <tr>
                            <th class="appending-items-data-table" width="15%">PO Memo</th>
                            <td class="appending-items-data-table" width="40%">
                                <input type="text" class="form-control form-control-sm" id="po_memo"
                                @cannot('menu_store.procurement.pharmacy.drug_orders.update')
                                    disabled
                                @endcannot
                            >
                            </td>
                            <th class="appending-items-data-table">Date Created</th>
                            <td class="appending-items-data-table">
                                <input type="text" class="form-control form-control-sm" id="created_at" disabled>
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table" width="15%">Comments</th>
                            <td class="appending-items-data-table" colspan="3">
                                <textarea rows="2" name="comments" class="form-control form-control-sm" id="comments" placeholder=""
                                @cannot('menu_store.procurement.pharmacy.drug_orders.update')
                                    disabled
                                @endcannot
                            ></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table">Invoice</th>
                            <td colspan="4" class="appending-items-data-table">
                                <div class="chip chip-lg form-control" id="chip_controller">
                                    <input type="hidden" id="file_id" name="file_id" value="">
                                    <span><a class="file_name"></a></span><span class="closebtn" onclick="clickDeleteDrugOrderFile();">×</span>
                                </div>
                                <input type="file" name="file" class="form-control form-control-sm" id="file">
                            </td>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-center appending-items-data-table">
                                <button class="btn btn-sm btn-primary w-25" onclick="updateDrugOrder()"
                                @cannot('menu_store.procurement.pharmacy.drug_orders.update')
                                    disabled
                                @endcannot
                            ><i class="fa fa-edit me-3"></i>UPDATE CHANGES</button>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        {{-- </form> --}}
    </div>
    <div class="row">
        <div class="col" id="alert-message-for-drug-order-items">
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="mb-3">
                {{-- <form action="" method="POST" id="#drug_order_item_edit_form"> --}}
                <div class="table-container" id="drug_order_item_table_container">
                    <table class="table table-bordered table-striped table-hover" id="drug_order_item_table">
                        <thead>
                            <tr>
                                <th></th>
                                <th width="30%">Name</th>
                                <th width="10%">Quantity Ordered</th>
                                <th width="10%">Quantity Confirmed</th>
                                <th width="10%">Acq Cost</th>
                                <th width="17%">NDC</th>
                                <th width="13%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="drug_order_item_tbody"> </tbody>
                        @can('menu_store.procurement.pharmacy.drug_orders.update')
                            <tfoot>
                                <th colspan="7">
                                    <button class="btn btn-sm btn-secondary w-25" onclick="clickAddMoreDrugOrderItem(event)"><i class="fa fa-plus me-3"></i>Add More Item</button>
                                </th>
                            </tfoot>
                        @endcan
                    </table>
                </div>
                {{-- </form> --}}
            </div>
        </div>
    </div>
</div>
<!--end row-->

<script>
    function updateDrugOrder()
    {
        let formData = new FormData();
        const status_id = $('#edit_task_modal #show-status-id').val();
        let data = {
            order: {
                id: drug_order_id,
                order_number: $(`#edit_task_modal #drug-order-partials #po_name`).val(),
                // po_name: $(`#edit_task_modal #drug-order-partials #po_name`).val(),
                account_number: $(`#edit_task_modal #drug-order-partials #account_number`).val(),
                wholesaler_name: $(`#edit_task_modal #drug-order-partials #wholesaler_name`).val(),
                order_date: $(`#edit_task_modal #drug-order-partials #order_date`).val(),
                comments: $(`#edit_task_modal #drug-order-partials #comments`).val(),
                po_memo: $(`#edit_task_modal #drug-order-partials #po_memo`).val(),
                status_id: status_id,
                wholesaler_id: $(`#edit_task_modal #drug-order-partials #wholesaler_id`).val(),
            },
            item: {}
        };

        let fileName;
        if($("#edit_task_modal #drug-order-partials #file").val().length == 0){
            fileName = $(`#edit_task_modal #drug-order-partials .file_name`).text()
        }
        else{
            fileName = $("#edit_task_modal #drug-order-partials #file").val().split('\\').pop(); // Get the file name
        }
        
        if ($("#edit_task_modal #drug-order-partials #file")[0].files.length !== 0) {
            formData.append('file', $('#edit_task_modal #drug-order-partials #file')[0].files[0]);
        } else {
            if(status_id == 706 && (fileName == '' || fileName == undefined)) {
                $('#alert-message-for-drug-order').html(`<div class="alert alert-danger">Upload Invoice to change status into <b>Completed</b>.</div>`);
                return;
            }
        }
        formData.append("data", JSON.stringify(data));
        sweetAlertLoading();

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/procurement/pharmacy/drug-orders/edit`,
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    if(data.data.file_id != "" && data.data.file_id != null){
                        if (fileName.length > 30) {
                            fileName = fileName.substring(0, 30) + '...';
                        }
                        $('#edit_drug_order_modal #drug-order-partials .file_name').text(fileName);
                        $("#edit_drug_order_modal #drug-order-partials #file_id").val(data.data.file_id);
                        $("#edit_drug_order_modal #drug-order-partials #chip_controller").show();
                        $("#edit_drug_order_modal #drug-order-partials #file").val('');
                        $("#edit_drug_order_modal #drug-order-partials #file").hide();
                        $('#edit_drug_order_modal #drug-order-partials .file_name').attr("href", "/store/procurement/pharmacy/"+menu_store_id+"/drug-orders/download/"+data.data.file_id+"");
                    }
                    Swal.close();
                    $('#alert-message-for-drug-order').html(`<div class="alert alert-success">Drug order changes successfully <b>updated</b>.</div>`);
                    setTimeout(function() {
                        $('.alert').alert('close');
                    }, 5000);
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }

        });
    }

    function clickDeleteDrugOrderFile(){
        let data = {
            id: $("#edit_task_modal #drug-order-partials #file_id").val(),
        };
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/procurement/pharmacy/drug-orders/delete_file`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $("#edit_task_modal #drug-order-partials #chip_controller").hide();
                    $("#edit_task_modal #drug-order-partials #file").show();
                    Swal.close();
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }
        });
    }

    function clickAddMoreDrugOrderItem(event)
    {
        console.log("FIRE ADD MORE")
        event.preventDefault();
        var container = $('#edit_task_modal #drug_order_item_table_container');
        container.animate({
        scrollTop: container.prop("scrollHeight")
        }, 500);
        addMore++;
        $('#edit_task_modal #drug_order_item_tbody').append(
            `<tr id="new_drug_order_item_tbody_tr_${addMore}">
                <td><b>#${addMore}</b></td>
                <td>
                    <input type="text" id="new_id_${addMore}" value="new" hidden>

                    <select class="form-select form-select-sm" data-placeholder="Select medication.." id="new_medication_${addMore}" onchange="doSelectMedication(this.id, ${addMore})"></select>

                    <input type="text" class="form-control form-control-sm" id="new_product_description_${addMore}" name="product_description[${addMore}]" hidden>
                </td>
                <td>
                    <input type="number" min="0" step="1" class="form-control form-control-sm" id="new_quantity_ordered_${addMore}" name="quantity_ordered[${addMore}]">
                </td>
                <td> 
                    <input type="number" min="0" step="1" class="form-control form-control-sm" id="new_quantity_confirmed_${addMore}" name="quantity_confirmed[${addMore}]">
                </td>
                <td> 
                    <input type="number" min="0" class="form-control form-control-sm text-end" id="new_acq_cost_${addMore}" name="acq_cost[${addMore}]">
                </td>
                <td> 
                    <input type="text" class="form-control form-control-sm" id="new_ndc_${addMore}" name="ndc[${addMore}]">
                </td>
                <td>
                    <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${addMore}" onclick="clickUpdateDrugOrderItem('', ${addMore})"><i class="fa fa-save"></i></button>
                    <button class="btn btn-sm btn-danger" title="DELETE ITEM #${addMore}" onclick="clickDeleteDrugOrderItem('', ${addMore})"><i class="fa fa-trash-can"></i></button>
                </td>
            </tr>`
        );
        $(`#new_product_description_${addMore}`).focus();

        searchSelect2ApiDrug(`#edit_task_modal #new_medication_${addMore}`, 'edit_task_modal');
    }

    function clickUpdateDrugOrderItem(id = '', k = '')
    {
        if(id!='') {
            // update
            saveUpdateDrugOrderItem(id, k);
        } else {
            // create
            saveCreateDrugOrderItem(k);
        }
    } 

    function clickDeleteDrugOrderItem(id = '', k = '')
    {
        if(id!= '') {
            confirmDeleteDrugOrderItem(id, k)
        } else {
            var id = $(`#new_id_${k}`).val();
            if(id === 'new') {
                $(`#new_drug_order_item_tbody_tr_${k}`).remove();
            } else {
                confirmDeleteDrugOrderItem(id, k);
            }
        }
    }

    function confirmDeleteDrugOrderItem(id, k)
    {
        Swal.fire({
            title: 'Are you sure?',
            text: `You will not be able to recover this item #${k}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it'
        }).then((result) => {
            if (result.isConfirmed) {
                saveDeleteDrugOrderItem(id, k);
                $(`#new_drug_order_item_tbody_tr_${k}`).remove();
                // User confirmed, you can proceed with the delete action here
                Swal.fire(
                    'Deleted',
                    `Your item #${k} has been deleted.`,
                    'success'
                );
            }
        });
    }

    function saveCreateDrugOrderItem(k)
    {
        let data = {
            product_description: $(`#new_product_description_${k}`).val(),
            quantity_ordered: $(`#new_quantity_ordered_${k}`).val(),
            quantity_confirmed: $(`#new_quantity_confirmed_${k}`).val(),
            acq_cost: $(`#new_acq_cost_${k}`).val(),
            ndc: $(`#new_ndc_${k}`).val(),
            drug_order_id: drug_order_id
        };
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/procurement/pharmacy/drug-order-item/add`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $(`#new_id_${k}`).val(data.data.id);
                    $('#alert-message-for-drug-order-items').html(`<div class="alert alert-success">Item <b>#${k}</b> successfully <b>created</b>.</div>`);
                    setTimeout(function() {
                        $('.alert').alert('close');
                    }, 5000);
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }

        });
    }

    function saveUpdateDrugOrderItem(id, k)
    {
        let data = {
            id: id,
            product_description: $(`#product_description_${id}`).val(),
            quantity_ordered: $(`#quantity_ordered_${id}`).val(),
            quantity_confirmed: $(`#quantity_confirmed_${id}`).val(),
            acq_cost: $(`#acq_cost_${id}`).val(),
            ndc: $(`#ndc_${id}`).val(),
        };
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/procurement/pharmacy/drug-order-item/edit`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $('#alert-message-for-drug-order-items').html(`<div class="alert alert-success">Item <b>#${k}</b> successfully <b>updated</b>.</div>`);
                    setTimeout(function() {
                        $('.alert').alert('close');
                    }, 5000);
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }

        });
    }

    function saveDeleteDrugOrderItem(id, k)
    {
        let data = {
            id: id
        };
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/procurement/pharmacy/drug-order-item/delete`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $(`#edit_task_modal #drug_order_item_tbody_tr_${id}`).remove();
                    $('#edit_task_modal #alert-message-for-drug-order-items').html(`<div class="alert alert-warning">Item <b>#${k}</b> successfully <b>deleted</b>.</div>`);
                    setTimeout(function() {
                        $('.alert').alert('close');
                    }, 5000);
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }

        });
    }

    function doSelectMedication(id, k)
    {
        $(`#new_product_description_${k}`).val($(`#${id}`).text())
    }
</script>