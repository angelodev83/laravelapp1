<!--start row-->
<div class="container">   
    <div class="row">
        <div class="col" id="alert-message-for-supply-order">
        </div>
    </div>                      
    <div class="row">
        <form action="" method="POST" id="#supply_order_edit_form">
            <div class="col">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="appending-items-data-table" width="18%">Order Number</th>
                            <td class="appending-items-data-table" width="37%">
                                <input type="text" class="form-control form-control-sm" id="order_number"
                                    @cannot('menu_store.procurement.pharmacy.supplies_orders.update')
                                        disabled
                                    @endcannot
                                >
                            </td>
                            <th class="appending-items-data-table" width="15%">Date Created</th>
                            <td class="appending-items-data-table" width="30%">
                                <input type="text" class="form-control form-control-sm" id="created_at" disabled>
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table">Wholesaler</th>
                            <td class="appending-items-data-table">
                                <select class="form-select form-select-sm" name="wholesaler_id" id="wholesaler_id"></select>
                            </td>
                            <th class="appending-items-data-table">Order Date</th>
                            <td class="appending-items-data-table">
                                <div class="input-group"> <span class="input-group-text" id="icon-order-date"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control form-control-sm" id="order_date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" readonly>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table">Comments</th>
                            <td class="appending-items-data-table" colspan="3">
                                <textarea rows="2" name="comments" class="form-control form-control-sm" id="comments" placeholder=""
                                @cannot('menu_store.procurement.pharmacy.supplies_orders.update')
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
                                    <span><a class="file_name"></a></span><span class="closebtn" onclick="clickDeleteSupplyOrderFile();">×</span>
                                </div>
                                <input type="file" name="file" class="form-control form-control-sm" id="file">
                            </td>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-center appending-items-data-table">
                                <button class="btn btn-sm btn-primary w-25" onclick="updateSupplyOrder(event)"
                                @cannot('menu_store.procurement.pharmacy.supplies_orders.update')
                                    disabled
                                @endcannot
                            ><i class="fa fa-edit me-3"></i>UPDATE CHANGES</button>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col" id="alert-message-for-supply-order-items">
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="mb-3">
                <form action="" method="POST" id="#supply_order_item_edit_form">
                <div class="table-container" id="supply_order_item_table_container">
                    <table class="table table-bordered table-striped table-hover" id="supply_order_item_table">
                        <thead>
                            <tr>
                                <th></th>
                                <th width="20%">Item #</th>
                                <th width="10%">Code</th>
                                <th width="32%">Description</th>
                                <th width="10%">Quantity</th>
                                <th width="10%">Act Qty</th>
                                <th width="13%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="supply_order_item_tbody"> </tbody>
                        @can('menu_store.procurement.pharmacy.supplies_orders.update')
                        <tfoot>
                            <th colspan="7">
                                <button class="btn btn-sm btn-secondary w-25" onclick="clickAddMoreSupplyOrderItem(event)"><i class="fa fa-plus me-3"></i>Add More Item</button>
                            </th>
                        </tfoot>
                        @endcan
                    </table>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end row-->

<script>
    function updateSupplyOrder(event)
    {
        event.preventDefault();
        let formData = new FormData();
        let data = {
            order: {
                id: supply_order_id,
                order_number: $(`#edit_task_modal #supply-order-partials #order_number`).val(),
                comments: $(`#edit_task_modal #supply-order-partials #comments`).val(),
                // status_id: $(`#edit_task_modal #show-status-id`).val(),
                wholesaler_id: $(`#edit_task_modal #supply-order-partials #wholesaler_id`).val(),
                order_date: $(`#edit_task_modal #supply-order-partials #order_date`).val(),
            },
            item: {}
        };

        let fileName;
        if($("#edit_task_modal #supply-order-partials #file").val().length == 0){
            fileName = $(`#edit_task_modal #supply-order-partials .file_name`).text()
        }
        else{
            fileName = $("#edit_task_modal #supply-order-partials #file").val().split('\\').pop(); // Get the file name
        }
        
        if ($("#edit_task_modal #supply-order-partials #file")[0].files.length !== 0) {
            formData.append('file', $('#edit_task_modal #supply-order-partials #file')[0].files[0]);
        } else {
            const status_id = $('#edit_task_modal #show-status-id').val();
            if(status_id == 706 && (fileName == '' || fileName == undefined)) {
                $('#alert-message-for-supply-order').html(`<div class="alert alert-danger">Upload Invoice to change status into <b>Completed</b>.</div>`);
                return;
            }
        }
        formData.append("data", JSON.stringify(data));
        sweetAlertLoading();

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/procurement/pharmacy/supply-orders/edit`,
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    if(data.status == 'warning'){
                        Swal.close();
                        $('#alert-message-for-supply-order').html(`<div class="alert alert-danger">Upload Invoice to change status into <b>Completed</b>.</div>`);
                    }
                    else{
                        if(data.data.file_id != "" && data.data.file_id != null){
                            if (fileName.length > 30) {
                                fileName = fileName.substring(0, 30) + '...';
                            }
                            $('#edit_supply_order_modal #supply-order-partials .file_name').text(fileName);
                            $("#edit_supply_order_modal #supply-order-partials #file_id").val(data.data.file_id);
                            $("#edit_supply_order_modal #supply-order-partials #chip_controller").show();
                            $("#edit_supply_order_modal #supply-order-partials #file").val('');
                            $("#edit_supply_order_modal #supply-order-partials #file").hide();
                            $('#edit_supply_order_modal #supply-order-partials .file_name').attr("href", "/store/procurement/pharmacy/"+menu_store_id+"/supply-orders/download/"+data.data.file_id+"");
                        }
                        Swal.close();
                        $('#alert-message-for-supply-order').html(`<div class="alert alert-success">Supply order changes successfully <b>updated</b>.</div>`);
                    }
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

    function clickDeleteSupplyOrderFile(){
        console.log('fire');
        let data = {
            id: $("#edit_task_modal #supply-order-partials #file_id").val(),
        };
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/procurement/pharmacy/supply-orders/delete_file`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $("#edit_task_modal #supply-order-partials #chip_controller").hide();
                    $("#edit_task_modal #supply-order-partials #file").show();
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

    function clickAddMoreSupplyOrderItem(event)
    {
        event.preventDefault();
        var container = $('#supply_order_item_table_container');
        container.animate({
        scrollTop: container.prop("scrollHeight")
        }, 500);
        addMore++;
        $('#edit_task_modal #supply_order_item_tbody').append(
            `<tr id="new_supply_order_item_tbody_tr_${addMore}">
                <td><b>#${addMore}</b></td>
                <td>
                    <input type="text" id="new_id_${addMore}" value="new" hidden>

                    <select class="form-select form-select-sm" data-placeholder="Select item.." id="new_number_${addMore}" onchange="doSelectItem(this.id, ${addMore})"></select>

                    <input type="text" class="form-control form-control-sm" id="new_item_${addMore}" name="new_item[${addMore}]" hidden> 
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" id="new_code_${addMore}" name="code[${addMore}]" disabled>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" id="new_description_${addMore}" name="description[${addMore}]" disabled>
                </td>
                <td>
                    <input type="number" min="0" step="1" class="form-control form-control-sm" id="new_quantity_${addMore}" name="quantity[${addMore}]">
                </td>
                <td> 
                    <input type="number" min="0" step="1" class="form-control form-control-sm" id="new_actual_quantity_${addMore}" name="actual_quantity[${addMore}]">
                </td>
                <td>
                    <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${addMore}" onclick="clickUpdateSupplyOrderItem(event, '', ${addMore})"><i class="fa fa-save"></i></button>
                    <button class="btn btn-sm btn-danger" title="DELETE ITEM #${addMore}" onclick="clickDeleteSupplyOrderItem(event,'', ${addMore})"><i class="fa fa-trash-can"></i></button>
                </td>
            </tr>`
        );
        $(`#new_description_${addMore}`).focus();

        searchSupplyItem(`#new_number_${addMore}`, 'edit_task_modal', addMore, null, addMore);   
    }

    function clickUpdateSupplyOrderItem(event, id = '', k = '')
    {
        event.preventDefault();
        if(id!='') {
            // update
            saveUpdateSupplyOrderItem(id, k);
        } else {
            // create
            saveCreateSupplyOrderItem(k);
        }
    } 

    function clickDeleteSupplyOrderItem(event, id = '', k = '')
    {
        event.preventDefault();
        if(id!= '') {
            confirmDeleteSupplyOrderItem(id, k)
        } else {
            var id = $(`#new_id_${k}`).val();
            if(id === 'new') {
                $(`#new_supply_order_item_tbody_tr_${k}`).remove();
            } else {
                confirmDeleteSupplyOrderItem(id, k);
            }
        }
    }

    function confirmDeleteSupplyOrderItem(id, k)
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
                saveDeleteSupplyOrderItem(id, k);
                $(`#new_supply_order_item_tbody_tr_${k}`).remove();
                // User confirmed, you can proceed with the delete action here
                Swal.fire(
                    'Deleted',
                    `Your item #${k} has been deleted.`,
                    'success'
                );
            }
        });
    }

    function saveCreateSupplyOrderItem(k)
    {
        let data = {
            description: $(`#new_description_${k}`).val(),
            quantity: $(`#new_quantity_${k}`).val(),
            actual_quantity: $(`#new_actual_quantity_${k}`).val(),
            code: $(`#new_code_${k}`).val(),
            number: $(`#new_item_${k}`).val(),
            item_id: $(`#new_number_${k}`).val(),
            order_id: supply_order_id
        };
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/procurement/pharmacy/supply-order-item/add`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $(`#new_id_${k}`).val(data.data.id);
                    $('#alert-message-for-supply-order-items').html(`<div class="alert alert-success">Item <b>#${k}</b> successfully <b>created</b>.</div>`);
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

    function saveUpdateSupplyOrderItem(id, k)
    {
        let data = {
            id: id,
            description: $(`#description_${id}`).val(),
            quantity: $(`#quantity_${id}`).val(),
            actual_quantity: $(`#actual_quantity_${id}`).val(),
            code: $(`#code_${id}`).val(),
            item_id: $(`#number_${id}`).val(),
            number: $(`#item_${id}`).val(),
        };
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/procurement/pharmacy/supply-order-item/edit`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $('#alert-message-for-supply-order-items').html(`<div class="alert alert-success">Item <b>#${k}</b> successfully <b>updated</b>.</div>`);
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

    function saveDeleteSupplyOrderItem(id, k)
    {
        let data = {
            id: id
        };
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/procurement/pharmacy/supply-order-item/delete`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $(`#edit_task_modal #supply_order_item_tbody_tr_${id}`).remove();
                    $('#edit_task_modal #alert-message-for-supply-order-items').html(`<div class="alert alert-warning">Item <b>#${k}</b> successfully <b>deleted</b>.</div>`);
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

    function doSelectItem(id, k)
    {
        $(`#new_description_${k}`).val($(`#${id}`).text())
    }
</script>