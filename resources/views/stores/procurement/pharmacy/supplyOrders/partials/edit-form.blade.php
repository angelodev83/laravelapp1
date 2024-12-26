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
                                <input type="text" class="form-control form-control-sm" id="order_number">
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
                            <td class="appending-items-data-table">
                                <textarea rows="2" name="comments" class="form-control form-control-sm" id="comments" placeholder=""></textarea>
                            </td>
                            <th class="appending-items-data-table">Status</th>
                            <td class="appending-items-data-table">
                                <select class="form-select form-select-sm" name="status_id" id="status_id"></select>
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table">Invoice</th>
                            <td colspan="4" class="appending-items-data-table">
                                <div class="chip chip-lg form-control" id="chip_controller">
                                    <input type="hidden" id="file_id" name="file_id" value="">
                                    <span><a class="file_name"></a></span><span class="closebtn" onclick="editModalDeleteInvoice();">×</span>
                                </div>
                                <input type="file" name="file" class="form-control form-control-sm" id="file">
                            </td>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-center appending-items-data-table">
                                <button class="btn btn-sm btn-primary w-25" onclick="updateSupplyOrder(event)"><i class="fa fa-edit me-3"></i>UPDATE CHANGES</button>
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
                                <th width="20%">Item #/URL</th>
                                <th width="10%">Code</th>
                                <th width="32%">Description</th>
                                <th width="10%">Quantity</th>
                                <th width="10%">Act Qty</th>
                                <th width="13%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="supply_order_item_tbody"> </tbody>
                        <tfoot>
                            <th colspan="7">
                                <button class="btn btn-sm btn-secondary w-25 hide-for-edit-non-url" onclick="clickAddMoreItem(event)"><i class="fa fa-plus me-3"></i>Add More Item</button>
                                <button class="btn btn-sm btn-secondary w-25 hide-for-edit-url" onclick="clickAddMoreUrlItem(event)"><i class="fa fa-plus me-3"></i>Add More Url Item</button>
                            </th>
                        </tfoot>
                    </table>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end row-->

<script>
    function editModalDeleteInvoice(){
        console.log('fire');
        let data = {
            id: $("#edit_supply_order_modal #file_id").val(),
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
                    $("#edit_supply_order_modal #chip_controller").hide();
                    $("#edit_supply_order_modal #file").show();
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

    function updateSupplyOrder(event)
    {
        event.preventDefault();
        let formData = new FormData();
        let data = {
            order: {
                id: supply_order_id,
                order_number: $(`#edit_supply_order_modal #order_number`).val(),
                comments: $(`#edit_supply_order_modal #comments`).val(),
                status_id: $(`#edit_supply_order_modal #status_id`).val(),
                wholesaler_id: $(`#edit_supply_order_modal #wholesaler_id`).val(),
                order_date: $(`#edit_supply_order_modal #order_date`).val(),
            },
            item: {}
        };

        let fileName;
        if($("#edit_supply_order_modal #file").val().length == 0){
            fileName = $(`#edit_supply_order_modal .file_name`).text()
        }
        else{
            fileName = $("#edit_supply_order_modal #file").val().split('\\').pop(); // Get the file name
        }
        
        if ($("#edit_supply_order_modal #file")[0].files.length !== 0) {
            formData.append('file', $('#edit_supply_order_modal #file')[0].files[0]);
        } else {
            const status_id = $('#edit_supply_order_modal #status_id').val();
            var file_id = $('#edit_supply_order_modal #file_id').val();
            var hasFileId = file_id != '' && file_id != null && file_id != undefined;
            if(status_id == 706 &&!hasFileId) {
                $('#alert-message-for-supply-order').html(`<div class="alert alert-danger"><b>Error!</b> Please upload Invoice to change status into <b>Completed</b>.</div>`);
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
                        $(`#edit_supply_order_modal #status_id`).val(data.status_id);
                        Swal.close();
                        $('#alert-message-for-inmar-return').html(`<div class="alert alert-danger">Upload Invoice to change to status <b>Completed</b>.</div>`);
                    }
                    else{
                        if(data.data.file_id != "" && data.data.file_id != null){
                            if (fileName.length > 30) {
                                fileName = fileName.substring(0, 30) + '...';
                            }
                            $('#edit_supply_order_modal .file_name').text(fileName);
                            $("#edit_supply_order_modal #file_id").val(data.data.file_id);
                            $("#edit_supply_order_modal #chip_controller").show();
                            $("#edit_supply_order_modal #file").val('');
                            $("#edit_supply_order_modal #file").hide();
                            $('#edit_supply_order_modal .file_name').attr("href", "/admin/file/download/"+data.data.file_id+"");
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

    function clickAddMoreItem(event)
    {
        event.preventDefault();
        var container = $('#edit_supply_order_modal #supply_order_item_table_container');
        container.animate({
        scrollTop: container.prop("scrollHeight")
        }, 500);
        console.log($('#edit_supply_order_modal #wholesaler_id').val());
        addMore++;
        $('#edit_supply_order_modal #supply_order_item_tbody').append(
            `<tr id="new_supply_order_item_tbody_tr_${addMore}">
                <td><b>#${addMore}</b></td>
                <td>
                    <input type="text" id="new_id_${addMore}" value="new" hidden>

                    <select class="form-select form-select-sm" data-placeholder="Select item.." id="new_number_${addMore}" onchange="doSelectItem(this.id, ${addMore})"></select>

                    <input type="text" class="form-control form-control-sm add-hidden" readonly onclick="openNewEditSelect(${addMore})" id="new_item_${addMore}" name="new_item[${addMore}]" hidden> 
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

        searchSupplyItem(`#new_number_${addMore}`, 'edit_supply_order_modal', addMore, null, addMore);   
    }

    function clickAddMoreUrlItem(event)
    {
        event.preventDefault();
        var container = $('#edit_supply_order_modal #supply_order_item_table_container');
        container.animate({
        scrollTop: container.prop("scrollHeight")
        }, 500);
        console.log($('#edit_supply_order_modal #wholesaler_id').val());
        addMore++;
        $('#edit_supply_order_modal #supply_order_item_tbody').append(
            `<tr id="new_supply_order_item_tbody_tr_${addMore}">
                <td><b>#${addMore}</b></td>
                <td>
                    <input type="text" id="new_id_${addMore}" value="new" hidden>
                    <div class="d-none"><select class="form-select form-select-sm" data-placeholder="Select item.." id="new_number_${addMore}" onchange="doSelectItem(this.id, ${addMore})"></select></div>

                    <input type="text" class="form-control supplies-url-holder" name="url" id="new_url_${addMore}" placeholder="url">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm d-none" id="new_code_${addMore}" name="code[${addMore}]" disabled>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm d-none" id="new_description_${addMore}" name="description[${addMore}]" disabled>
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

        searchSupplyItem(`#new_number_${addMore}`, 'edit_supply_order_modal', addMore, null, addMore);   
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
            url: $(`#new_url_${k}`).val(),
            order_id: supply_order_id
        };

        // console.log(data);
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
            url: $(`#url_${id}`).val(),
        };

        // console.log(data);
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
                    $(`#edit_supply_order_modal #supply_order_item_tbody_tr_${id}`).remove();
                    $('#edit_supply_order_modal #alert-message-for-supply-order-items').html(`<div class="alert alert-warning">Item <b>#${k}</b> successfully <b>deleted</b>.</div>`);
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