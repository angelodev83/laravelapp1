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
                                <input type="text" class="form-control form-control-sm" id="name">
                            </td>
                            <th class="appending-items-data-table" width="10%">PO Name</th>
                            <td class="appending-items-data-table" width="30%">
                                <input type="text" class="form-control form-control-sm" id="po_name">
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table" width="10%">Account Number</th>
                            <td class="appending-items-data-table" width="30%">
                                <input type="text" class="form-control form-control-sm" id="account_number">
                            </td>
                            <th class="appending-items-data-table" width="15%">Return Date</th>
                            <td class="appending-items-data-table" width="30%">
                                <div class="input-group"> <span class="input-group-text" id="icon-order-date"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control datepicker form-control-sm" id="return_date" name="return_date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" readonly>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table" width="10%">Wholesaler Name</th>
                            <td class="appending-items-data-table" width="30%">
                                {{-- <select class="form-select" name="wholesaler_name" id="wholesaler_name" title="Select..">
                                    <option value='AMENISOURCE'>AMENISOURCE</option>
                                    <option value='McKENSON'>McKENSON</option>
                                </select> --}}
                                <select class="form-select form-select-sm" name="wholesaler_id" id="wholesaler_id" title="Select..">
                                </select>
                            </td>
                            <th class="appending-items-data-table" width="10%">Status</th>
                            <td class="appending-items-data-table" width="30%">
                                <select class="form-select form-select-sm" name="status_id" id="status_id" title="Select..">
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th class="appending-items-data-table">Comments</th>
                            <td colspan="4" class="appending-items-data-table">
                                <textarea rows="2" name="comments" class="form-control form-control-sm" id="comments" placeholder=""></textarea>
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
                        <tbody id="inmar_return_item_tbody"> </tbody>
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

    function editModalDeleteInvoice(){
        let data = {
            id: $("#edit_inmar_return_modal #file_id").val(),
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
                    $("#edit_inmar_return_modal #chip_controller").hide();
                    $("#edit_inmar_return_modal #file").show();
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
        const status_id = $('#edit_inmar_return_modal #status_id').val();
        let data = {
                id: $(`#edit_inmar_return_modal #inmar_id`).val(),
                name: $(`#edit_inmar_return_modal #name`).val(),
                po_name: $(`#edit_inmar_return_modal #po_name`).val(),
                account_number: $(`#edit_inmar_return_modal #account_number`).val(),
                return_date: $(`#edit_inmar_return_modal #return_date`).val(),
                // wholesaler_name: $(`#edit_inmar_return_modal #wholesaler_name`).val(),
                comments: $(`#edit_inmar_return_modal #comments`).val(),
                status_id: status_id,  
                wholesaler_id: $(`#edit_inmar_return_modal #wholesaler_id`).val(),  
                pharmacy_store_id: menu_store_id,
        };

        let fileName;
        if($("#edit_inmar_return_modal #file").val().length == 0){
            fileName = $(`#edit_inmar_return_modal .file_name`).text()
        }
        else{
            console.log('else');
            fileName = $("#edit_inmar_return_modal #file").val().split('\\').pop(); // Get the file name
        }
        
        if ($("#edit_inmar_return_modal #file")[0].files.length !== 0) {
            formData.append('file', $('#edit_inmar_return_modal #file')[0].files[0]);
        } else {
            var file_id = $('#edit_inmar_return_modal #file_id').val();
            var hasFileId = file_id != '' && file_id != null && file_id != undefined;
            if(status_id == 706 && (fileName == '' || fileName == undefined || !hasFileId)) {
                $('#alert-message-for-inmar-return').html(`<div class="alert alert-danger"><b>Error!</b> Please upload Invoice to change status into <b>Completed</b>.</div>`);
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
                        $(`#edit_inmar_return_modal #status_id`).val(data.status_id);
                        Swal.close();
                        $('#alert-message-for-inmar-return').html(`<div class="alert alert-danger">Upload Invoice to change to status <b>Completed</b>.</div>`);
                    }
                    else{
                        if(data.file_id != "" && data.file_id != null){
                            if (fileName.length > 30) {
                                fileName = fileName.substring(0, 30) + '...';
                            }
                            $('#edit_inmar_return_modal .file_name').text(fileName);
                            $("#edit_inmar_return_modal #file_id").val(data.file_id);
                            $("#edit_inmar_return_modal #chip_controller").show();
                            $("#edit_inmar_return_modal #file").val('');
                            $("#edit_inmar_return_modal #file").hide();
                            $('#edit_inmar_return_modal .file_name').attr("href", "/admin/file/download/"+data.file_id+"");
                        }
                        
                        Swal.close();
                        $('#alert-message-for-inmar-return').html(`<div class="alert alert-success">Inmar return changes successfully <b>updated</b>.</div>`);
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
        var container = $('#edit_inmar_return_modal #inmar_return_item_table_container');
        container.animate({
        scrollTop: container.prop("scrollHeight")
        }, 500);
        addMore++;
        $('#edit_inmar_return_modal #inmar_return_item_tbody').append(
            `<tr id="new_inmar_return_item_tbody_tr_${addMore}">
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

        searchInmarItem(`#new_med_${addMore}`, 'edit_inmar_return_modal', addMore, null, addMore);   
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
                $(`#new_inmar_return_item_tbody_tr_${k}`).remove();
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
                $(`#new_inmar_return_item_tbody_tr_${k}`).remove();
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
            inmar_id: $('#edit_inmar_return_modal #inmar_id').val(),
            quantity: $(`#edit_inmar_return_modal #new_quantity_${k}`).val(),
            ndc: $(`#edit_inmar_return_modal #new_ndc_${k}`).val(),
            med_id: $(`#edit_inmar_return_modal #new_med_${k}`).val(),
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
            
            quantity: $(`#edit_inmar_return_modal #quantity_${id}`).val(),
            ndc: $(`#edit_inmar_return_modal #ndc${id}`).val(),
            med_id: $(`#edit_inmar_return_modal #med_${id}`).val(),
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
                    $(`#edit_inmar_return_modal #inmar_return_item_tbody_tr_${id}`).remove();
                    $('#edit_inmar_return_modal #alert-message-for-inmar-return-items').html(`<div class="alert alert-warning">Item <b>#${k}</b> successfully <b>deleted</b>.</div>`);
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