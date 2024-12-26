<!--start row-->
<div class="container">   
    <div class="row">
        <div class="col" id="alert-message-for-inmar-return">
        </div>
    </div>                      
    <div class="row">
        <form action="" method="POST" id="#inmar_return_edit_form">
            <div class="col">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <input type="hidden" id="inmar_id">
                            <th class="appending-items-data-table" width="10%">Reference No.</th>
                            <td class="appending-items-data-table" width="30%">
                                <input type="text" class="form-control form-control-sm" id="inmar_return_name"
                                @cannot('menu_store.procurement.pharmacy.inmar_returns.update')
                                    disabled
                                @endcannot
                                >
                            </td>
                            <th class="appending-items-data-table" width="10%">PO Name</th>
                            <td class="appending-items-data-table" width="30%">
                                <input type="text" class="form-control form-control-sm" id="inmar_return_po_name"
                                @cannot('menu_store.procurement.pharmacy.inmar_returns.update')
                                    disabled
                                @endcannot
                                >
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table" width="10%">Account Number</th>
                            <td class="appending-items-data-table" width="30%">
                                <input type="text" class="form-control form-control-sm" id="inmar_return_account_number"
                                @cannot('menu_store.procurement.pharmacy.inmar_returns.update')
                                    disabled
                                @endcannot
                                >
                            </td>
                            <th class="appending-items-data-table" width="15%">Return Date</th>
                            <td class="appending-items-data-table" width="30%">
                                <div class="input-group"> <span class="input-group-text" id="icon-return-date"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control form-control-sm" id="inmar_return_return_date" aria-describedby="icon-return-date" readonly
                                    @cannot('menu_store.procurement.pharmacy.inmar_returns.update')
                                        disabled
                                    @endcannot
                                    >
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table" width="10%">Comments</th>
                            <td class="appending-items-data-table" width="30%">
                                <textarea rows="2" name="comments" class="form-control form-control-sm" id="inmar_return_comments" placeholder=""
                                @cannot('menu_store.procurement.pharmacy.inmar_returns.update')
                                    disabled
                                @endcannot
                                ></textarea>
                            </td>
                            <th class="appending-items-data-table">Wholesaler Name</th>
                            <td class="appending-items-data-table">
                                <select class="form-select form-select-sm" name="inmar_return_wholesaler_id" id="inmar_return_wholesaler_id" title="Select.." 
                                @cannot('menu_store.procurement.pharmacy.inmar_returns.update')
                                    disabled
                                @endcannot
                                >
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table">Invoice</th>
                            <td colspan="4" class="appending-items-data-table">
                                <div class="chip chip-lg form-control" id="chip_controller">
                                    <input type="hidden" id="file_id" name="file_id" value="">
                                    <span class="file_name"></span><span class="closebtn" onclick="clickDeleteInmarReturnFile();">×</span>
                                </div>
                                <input type="file" name="file" class="form-control form-control-sm" id="file">
                            </td>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-center appending-items-data-table">
                                <button class="btn btn-sm btn-primary w-25" onclick="updateInmar(event)"><i class="fa fa-edit me-3"></i>UPDATE CHANGES</button>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col" id="alert-message-for-inmar-return-items">
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="mb-3">
                <form action="" method="POST" id="#inmar_return_item_edit_form">
                <div class="table-container" id="inmar_return_item_table_container">
                    <table class="table table-bordered table-striped table-hover" id="inmar_return_item_table">
                        <thead>
                            <tr>
                                <th width="1%"></th>
                                <th width="40%">Drug Name</th>
                                <th width="10%">Quantity</th>
                                <th width="20%">NDC</th>
                                <th width="8%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="inmar_item_tbody"> </tbody>
                        <tfoot>
                            <th colspan="7">
                                <button class="btn btn-sm btn-secondary w-25" onclick="clickAddMoreItem(event)"><i class="fa fa-plus me-3"></i>Add More Item</button>
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

    function clickDeleteInmarReturnFile(){
        let data = {
            id: $("#edit_task_modal #inmar-return-partials #file_id").val(),
        };
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/procurement/pharmacy/inmar-returns/delete_file`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $("#edit_task_modal #inmar-return-partials #chip_controller").hide();
                    $("#edit_task_modal #inmar-return-partials #file").show();
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

    function updateInmar(event)
    {
        event.preventDefault();
        let formData = new FormData();
        const status_id = $('#edit_task_modal #show-status-id').val();
        let data = {
                id: $(`#edit_task_modal #show-inmar-return-id`).val(),
                name: $(`#edit_task_modal #inmar_return_name`).val(),
                po_name: $(`#edit_task_modal #inmar_return_po_name`).val(),
                account_number: $(`#edit_task_modal #inmar_return_account_number`).val(),
                return_date: $(`#edit_task_modal #inmar_return_return_date`).val(),
                // wholesaler_name: $(`#edit_task_modal #inmar_return_wholesaler_name`).val(),
                wholesaler_id: $(`#edit_task_modal #inmar_return_wholesaler_id`).val(),
                comments: $(`#edit_task_modal #inmar_return_comments`).val(),
                status_id: status_id,
                pharmacy_store_id: menu_store_id,
        };
        let fileName;
        if($("#edit_task_modal #inmar-return-partials #file").val().length == 0){
            fileName = $(`#edit_task_modal #inmar-return-partials .file_name`).text()
        }
        else{
            fileName = $("#edit_task_modal #inmar-return-partials #file").val().split('\\').pop(); // Get the file name
        }
        
        if ($("#edit_task_modal #inmar-return-partials #file")[0].files.length !== 0) {
            formData.append('file', $('#edit_task_modal #inmar-return-partials #file')[0].files[0]);
        } else {
            if(status_id == 706 && (fileName == '' || fileName == undefined)) {
                $('#alert-message-for-inmar-return').html(`<div class="alert alert-danger">Upload Invoice to change status into <b>Completed</b>.</div>`);
                return;
            }
        }
        formData.append("data", JSON.stringify(data));
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/procurement/pharmacy/inmar-returns/edit`,
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
                        // $(`#edit_task_modal #status_id`).val(data.status_id);
                        Swal.close();
                        $('#alert-message-for-inmar-return').html(`<div class="alert alert-danger">Upload Invoice to change to status <b>Completed</b>.</div>`);
                    }
                    else{
                        if(data.file_id != ""){
                            if (fileName.length > 50) {
                                fileName = fileName.substring(0, 50) + '...';
                            }
                            $('#edit_task_modal #inmar-return-partials .file_name').text(fileName);
                            $("#edit_task_modal #inmar-return-partials #file_id").val(data.file_id);
                            $("#edit_task_modal #inmar-return-partials #chip_controller").show();
                            $("#edit_task_modal #inmar-return-partials #file").val('');
                            $("#edit_task_modal #inmar-return-partials #file").hide();
                            $('#edit_task_modal #inmar-return-partials .file_name').attr("href", "/store/procurement/pharmacy/"+menu_store_id+"/inmars/download/"+data.file_id+"");
                        }
                        
                        Swal.close();
                        $('#alert-message-for-inmar-return').html(`<div class="alert alert-success">Supply order changes successfully <b>updated</b>.</div>`);
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
        var container = $('#item_table_container');
        container.animate({
        scrollTop: container.prop("scrollHeight")
        }, 500);
        addMore++;
        $('#edit_task_modal #inmar_item_tbody').append(
            `<tr id="new_item_tbody_tr_${addMore}">
                <td><b>#${addMore}</b></td>
                <td>
                    <input type="text" id="new_id_${addMore}" value="new" hidden>

                    <select class="form-select form-select-sm" data-placeholder="Select item.." id="new_med_${addMore}" onchange="doSelectItem(this.id, ${addMore})"></select>

                    <input type="text" class="form-control form-control-sm" id="new_item_${addMore}" name="new_item[${addMore}]" hidden> 
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm number_only" id="new_quantity_${addMore}" name="quantity[${addMore}]">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" id="new_ndc_${addMore}" name="ndc[${addMore}]">
                </td>

                <td>
                    <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${addMore}" onclick="clickUpdateInmarItem(event, '', ${addMore})"><i class="fa fa-save"></i></button>
                    <button class="btn btn-sm btn-danger" title="DELETE ITEM #${addMore}" onclick="clickDeleteInmarItem(event,'', ${addMore})"><i class="fa fa-trash-can"></i></button>
                </td>
            </tr>`
        );
        $(`#new_description_${addMore}`).focus();

        // searchInmarItem(`#new_med_${addMore}`, 'edit_modal', addMore, null, addMore);   
        searchSelect2ApiDrug(`#edit_task_modal #new_med_${addMore}`, 'edit_task_modal');
    }

    function clickUpdateInmarItem(event, id = '', k = '')
    {
        event.preventDefault();
        if(id!='') {
            // update
            saveUpdateInmarItem(id, k);
        } else {
            // create
            saveCreateInmarItem(k);
        }
    } 

    function clickDeleteInmarItem(event, id = '', k = '')
    {
        event.preventDefault();
        if(id!= '') {
            confirmDeleteInmarItem(id, k)
        } else {
            var id = $(`#new_id_${k}`).val();
            if(id === 'new') {
                $(`#new_item_tbody_tr_${k}`).remove();
            } else {
                confirmDeleteInmarItem(id, k);
            }
        }
    }

    function confirmDeleteInmarItem(id, k)
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
                saveDeleteInmarItem(id, k);
                $(`#new_item_tbody_tr_${k}`).remove();
                // User confirmed, you can proceed with the delete action here
                Swal.fire(
                    'Deleted',
                    `Your item #${k} has been deleted.`,
                    'success'
                );
            }
        });
    }

    function saveCreateInmarItem(k)
    {
        let data = {
            id: 0,
            inmar_id: $('#edit_task_modal #show-inmar-return-id').val(),
            quantity: $(`#edit_task_modal #new_quantity_${k}`).val(),
            ndc: $(`#edit_task_modal #new_ndc_${k}`).val(),
            med_id: $(`#edit_task_modal #new_med_${k}`).val(),
        };
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/procurement/pharmacy/inmar-returns/edit-item`,
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
                    $('#alert-message-for-inmar-return-items').html(`<div class="alert alert-success">Item <b>#${k}</b> successfully <b>created</b>.</div>`);
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

    function saveUpdateInmarItem(id, k)
    {
        
        let data = {
            id: id,
            
            quantity: $(`#edit_task_modal #quantity_${id}`).val(),
            ndc: $(`#edit_task_modal #ndc_${id}`).val(),
            med_id: $(`#edit_task_modal #med_${id}`).val(),
        };
        //console.log(data);
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: `/store/procurement/pharmacy/inmar-returns/edit-item`,
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
                    $(`#edit_task_modal #inmar_return_item_tbody_tr_${id}`).remove();
                    $('#alert-message-for-inmar-return-items').html(`<div class="alert alert-success">Item <b>#${k}</b> successfully <b>updated</b>.</div>`);
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

    function saveDeleteInmarItem(id, k)
    {
        let data = {
            id: id
        };

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: `/store/procurement/pharmacy/inmar-returns/delete`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                if(data.errors){
                    
                }
                else{
                    reloadDataTable();
                    $(`#edit_task_modal #inmar_item_tbody_tr_${id}`).remove();
                    $('#edit_task_modal #alert-message-for-inmar-return-items').html(`<div class="alert alert-warning">Item <b>#${k}</b> successfully <b>deleted</b>.</div>`);
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