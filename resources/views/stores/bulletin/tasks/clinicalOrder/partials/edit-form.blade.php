<!--start row-->
<div class="container">   
    <div class="row">
        <div class="col" id="alert-message-for-clinical-order">
        </div>
    </div>                      
    <div class="row">
        <form action="" method="POST" id="#clinical_order_edit_form">
            <div class="col">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <input type="hidden" id="clinic_order_id">
                            <th class="appending-items-data-table" width="10%">Order No.</th>
                            <td class="appending-items-data-table" width="30%">
                                <input type="text" disabled class="form-control form-control-sm" id="order_number"
                                @cannot('menu_store.procurement.clinical_orders.update')
                                    disabled
                                @endcannot>
                            </td>
                            <th class="appending-items-data-table" width="10%">Tracking No.</th>
                            <td class="appending-items-data-table" width="30%">
                                <input type="text" class="form-control form-control-sm" id="tracking_number"
                                @cannot('menu_store.procurement.clinical_orders.update')
                                    disabled
                                @endcannot>
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table" width="10%">Prescriber Full Name</th>
                            <td class="appending-items-data-table" width="30%">
                                <input type="text" class="form-control form-control-sm" id="prescriber_name"
                                @cannot('menu_store.procurement.clinical_orders.update')
                                    disabled
                                @endcannot>
                            </td>
                            <th class="appending-items-data-table" width="15%">Order Date</th>
                            <td class="appending-items-data-table" width="30%">
                                <div class="input-group"> <span class="input-group-text" id="icon-order-date"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control form-control-sm datepicker" id="order_date"  name="order_date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" readonly
                                    @cannot('menu_store.procurement.clinical_orders.update')
                                        disabled
                                    @endcannot>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table">Comments</th>
                            <td class="appending-items-data-table">
                                <textarea rows="2" name="comments" class="form-control form-control-sm" id="comments" placeholder=""
                                @cannot('menu_store.procurement.clinical_orders.update')
                                    disabled
                                @endcannot></textarea>
                            </td>
                            <th class="appending-items-data-table" width="10%">Clinic/External Location </th>
                            <td class="appending-items-data-table" width="30%">
                                <select class="form-select form-select-sm" id="clinics" 
                                @cannot('menu_store.procurement.clinical_orders.update')
                                    disabled
                                @endcannot>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table">Invoice</th>
                            <td colspan="4" class="appending-items-data-table">
                                <div class="chip chip-lg form-control" id="chip_controller">
                                    <input type="hidden" id="file_id" name="file_id" value="">
                                    <span class="file_name"></span><span class="closebtn" onclick="clickDeleteClinicalOrderFile();">×</span>
                                </div>
                                <input type="file" name="file" class="form-control form-control-sm" id="file">
                            </td>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-center appending-items-data-table">
                                <button class="btn btn-sm btn-primary w-25" onclick="updateClinicOrder(event)"><i class="fa fa-edit me-3"
                                @cannot('menu_store.procurement.clinical_orders.update')
                                    disabled
                                @endcannot></i>UPDATE CHANGES</button>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col" id="alert-message-for-clinical-order-items">
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="mb-3">
                <form action="" method="POST" id="#clinical_order_item_edit_form">
                <div class="table-container" id="clinical_order_item_table_container">
                    <table class="table table-bordered table-striped table-hover" id="clinical_order_item_table">
                        <thead>
                            <tr>
                                <th width="1%"></th>
                                <th width="35%">Drug Name</th>
                                <th width="10%">Quantity</th>
                                <th width="20%">NDC</th>
                                <th width="13%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="clinical_order_item_tbody"> </tbody>
                        <tfoot>
                            <th colspan="7">
                                <button class="btn btn-sm btn-secondary w-25" onclick="clickAddMoreClinicalOrderItem(event)"><i class="fa fa-plus me-3"></i>Add More Item</button>
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

    function updateClinicOrder(event)
    {
        event.preventDefault();   
        let formData = new FormData();
        const status_id = $('#edit_task_modal #show-status-id').val();
        let data = {
                id: $(`#edit_task_modal #clinical-order-partials #clinic_order_id`).val(),
                order_number: $(`#edit_task_modal #clinical-order-partials #order_number`).val(),
                tracking_number: $(`#edit_task_modal #clinical-order-partials #tracking_number`).val(),
                prescriber_name: $(`#edit_task_modal #clinical-order-partials #prescriber_name`).val(),
                order_date: $(`#edit_task_modal #clinical-order-partials #order_date`).val(),
                clinic_id: $(`#edit_task_modal #clinical-order-partials #clinics`).val(),
                comments: $(`#edit_task_modal #clinical-order-partials #comments`).val(), 
                status_id: status_id,
                pharmacy_store_id: menu_store_id,
        };
        
        let fileName;
        if($("#edit_task_modal #clinical-order-partials #file").val().length == 0){
            fileName = $(`#edit_task_modal #clinical-order-partials .file_name`).text()
        }
        else{
            fileName = $("#edit_task_modal #clinical-order-partials #file").val().split('\\').pop(); // Get the file name
        }
        
        if ($("#edit_task_modal #clinical-order-partials #file")[0].files.length !== 0) {
            formData.append('file', $('#edit_task_modal #clinical-order-partials #file')[0].files[0]);
        } else {
            if(status_id == 706 && (fileName == '' || fileName == undefined)) {
                $('#alert-message-for-drug-order').html(`<div class="alert alert-danger">Upload Invoice to change status into <b>Completed</b>.</div>`);
                return;
            }
        }

        formData.append("data", JSON.stringify(data));
        //console.log(data);

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/procurement/${menu_store_id}/clinical-orders/edit`,
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();

                    if(data.status == 'warning'){
                        $(`#edit_task_modal #clinical-order-partials #status_id`).val(data.status_id);
                        Swal.close();
                        $('#alert-message-for-clinical-order').html(`<div class="alert alert-danger">Upload Invoice to change to status <b>Completed</b>.</div>`);
                    }
                    else{
                        data = JSON.parse(data);
                        
                        if(data.file_id != "" && data.file_id != null){
                            if (fileName.length > 30) {
                                fileName = fileName.substring(0, 30) + '...';
                            }
                            $('#edit_task_modal #clinical-order-partials .file_name').text(fileName);
                            $("#edit_task_modal #clinical-order-partials #file_id").val(data.file_id);
                            $("#edit_task_modal #clinical-order-partials #chip_controller").show();
                            $("#edit_task_modal #clinical-order-partials #file").val('');
                            $("#edit_task_modal #clinical-order-partials #file").hide();
                            $('#edit_task_modal #clinical-order-partials .file_name').attr("href", "/store/procurement/"+menu_store_id+"/clinical-orders/download/"+data.file_id+"");
                        }
                        
                        Swal.close();
                        $('#alert-message-for-clinical-order').html(`<div class="alert alert-success">Clinical order changes successfully <b>updated</b>.</div>`);
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

    function clickDeleteClinicalOrderFile(){
        let data = {
            id: $("#edit_task_modal #clinical-order-partials #file_id").val(),
        };
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/procurement/clinical-orders/delete_file`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $("#edit_task_modal #clinical-order-partials #chip_controller").hide();
                    $("#edit_task_modal #clinical-order-partials #file").show();
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

    function clickAddMoreClinicalOrderItem(event)
    {
        event.preventDefault();
        var container = $('#edit_task_modal #clinical-order-partials #clinical_order_item_table_container');
        container.animate({
        scrollTop: container.prop("scrollHeight")
        }, 500);
        addMore++;
        $('#edit_task_modal #clinical-order-partials #clinical_order_item_tbody').append(
            `<tr id="new_clinical_order_item_tbody_tr_${addMore}">
                <td><b>#${addMore}</b></td>
                <td>
                    <input type="text" id="new_id_${addMore}" value="new" hidden>

                    <select class="form-select form-select-sm" data-placeholder="Select medication.." id="new_med_${addMore}" onchange="doSelectItem(this.id, ${addMore})"></select>

                    <input type="text" class="form-control form-control-sm" id="new_item_${addMore}" name="new_item[${addMore}]" hidden> 
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm number_only" id="new_quantity_${addMore}" name="quantity[${addMore}]">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" id="new_ndc_${addMore}" name="ndc[${addMore}]">
                </td>

                <td>
                    <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${addMore}" onclick="clickUpdateClinicalOrderItem(event, '', ${addMore})"><i class="fa fa-save"></i></button>
                    <button class="btn btn-sm btn-danger" title="DELETE ITEM #${addMore}" onclick="clickDeleteClinicalOrderItem(event,'', ${addMore})"><i class="fa fa-trash-can"></i></button>
                </td>
            </tr>`
        );
        $(`#new_description_${addMore}`).focus();

        searchItem(`#new_med_${addMore}`, 'edit_task_modal', addMore, null, addMore);   
    }

    function clickUpdateClinicalOrderItem(event, id = '', k = '')
    {
        event.preventDefault();
        if(id!='') {
            // update
            saveUpdateItem(id, k);
        } else {
            // create
            saveCreateItem(k);
        }
    } 

    function clickDeleteClinicalOrderItem(event, id = '', k = '')
    {
        event.preventDefault();
        if(id!= '') {
            confirmDeleteItem(id, k)
        } else {
            var id = $(`#new_id_${k}`).val();
            if(id === 'new') {
                $(`#new_clinical_order_item_tbody_tr_${k}`).remove();
            } else {
                confirmDeleteItem(id, k);
            }
        }
    }

    function confirmDeleteItem(id, k)
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
                saveDeleteItem(id, k);
                $(`#new_clinical_order_item_tbody_tr_${k}`).remove();
                // User confirmed, you can proceed with the delete action here
                Swal.fire(
                    'Deleted',
                    `Your item #${k} has been deleted.`,
                    'success'
                );
            }
        });
    }

    function saveCreateItem(k)
    {
        let data = {
            id: 0,
            clinic_order_id: $('#edit_task_modal #clinical-order-partials #clinic_order_id').val(),
            quantity: $(`#edit_task_modal #clinical-order-partials #new_quantity_${k}`).val(),
            ndc: $(`#edit_task_modal #clinical-order-partials #new_ndc_${k}`).val(),
            med_id: $(`#edit_task_modal #clinical-order-partials #new_med_${k}`).val(),
        };
        //console.log(data);
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/procurement/clinical-orders/edit-item`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2(data.status, JSON.stringify(val[0]));
                    });
                }
                else{
                    reloadDataTable();
                    $(`#new_id_${k}`).val(data.data);
                    $('#alert-message-for-clinical-order-items').html(`<div class="alert alert-success">Item <b>#${k}</b> successfully <b>created</b>.</div>`);
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

    function saveUpdateItem(id, k)
    {
        let data = {
            id: id,
            
            quantity: $(`#edit_task_modal #clinical-order-partials #quantity_${id}`).val(),
            ndc: $(`#edit_task_modal #clinical-order-partials #ndc${id}`).val(),
            med_id: $(`#edit_task_modal #clinical-order-partials #med_${id}`).val(),
        };
        //console.log(data);
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/procurement/clinical-orders/edit-item`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2(data.status, JSON.stringify(val[0]));
                    });
                }
                else{
                    reloadDataTable();
                    $('#alert-message-for-clinical-order-items').html(`<div class="alert alert-success">Item <b>#${k}</b> successfully <b>updated</b>.</div>`);
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

    function saveDeleteItem(id, k)
    {
        let data = {
            id: id
        };

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/procurement/clinical-orders/delete`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $(`#edit_task_modal #clinical-order-partials #clinical_order_item_tbody_tr_${id}`).remove();
                    $('#edit_task_modal #clinical-order-partials #alert-message-for-clinical-order-items').html(`<div class="alert alert-warning">Item <b>#${k}</b> successfully <b>deleted</b>.</div>`);
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