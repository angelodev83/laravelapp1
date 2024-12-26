<div class="modal" id="edit_drug_recall_notifications_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Update Drug Recall Return</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!--start row-->
                <div class="container">   
                    <div class="row">
                        <div class="col" id="alert-message-for-drug-recall-notification">
                        </div>
                    </div>                      
                    <div class="row">
                        <div class="col">
                            <input type="hidden" class="form-control" id="eid" name="eid">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th class="appending-items-data-table" width="18%">Reference No. <span class="text-danger">*</span></th>
                                        <td class="appending-items-data-table" width="37%">
                                            <input type="text" class="form-control form-control-sm" id="reference_number" disabled>
                                        </td>
                                        <th class="appending-items-data-table">Notice Date</th>
                                        <td class="appending-items-data-table">
                                            <div class="input-group"> <span class="input-group-text" id="icon-order-date"><i class="fa fa-calendar"></i></span>
                                                <input type="text" class="form-control form-control-sm" id="notice_date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" readonly>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="appending-items-data-table" width="18%">Wholesaler <span class="text-danger">*</span></th>
                                        <td class="appending-items-data-table" width="37%">
                                            <select class="form-select form-select-sm" name="wholesaler_id" id="wholesaler_id"></select>
                                        </td>
                                        <th class="appending-items-data-table">Supplier Name</th>
                                        <td class="appending-items-data-table">
                                            <input type="text" class="form-control form-control-sm" id="supplier_name">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="appending-items-data-table">Comments</th>
                                        <td class="appending-items-data-table">
                                            <textarea rows="2" name="comments" class="form-control form-control-sm" id="comments" placeholder=""></textarea>
                                        </td>
                                        <th class="appending-items-data-table">Date Created</th>
                                        <td class="appending-items-data-table" style="vertical-align: top !important;">
                                            <input type="text" class="form-control form-control-sm" id="created_at" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-center appending-items-data-table">
                                            <button class="btn btn-sm btn-info2 w-25" id="show_documents_btn" onclick="showDrugRecallNotificationDocuments()"><i class="fa fa-paperclip me-3"></i>Show Files</button>
                                            <button class="btn btn-sm btn-primary w-25" id="update_btn" onclick="updateDrugRecallNotification()"><i class="fa fa-edit me-3"></i>UPDATE CHANGES</button>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col" id="alert-message-for-drug-recall-notification-items">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <div class="table-container dt_scrollable_item_table_container" id="drug_recall_notification_item_table_container">
                                    <table class="table table-bordered table-striped table-hover" id="drug_recall_notification_item_table">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th width="30%">Drug Name <span class="text-danger">*</span></th>
                                                <th width="13%">Lot #</th>
                                                <th width="12%">Qty</th>
                                                <th width="16%">NDC <span class="text-danger">*</span></th>
                                                <th width="17%">Exp. Date</th>
                                                <th width="13%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="drug_recall_notification_item_tbody"> </tbody>
                                        <tfoot>
                                            <th colspan="7">
                                                <button class="btn btn-sm btn-secondary w-25" onclick="addNotificationItemsEditModal()"><i class="fa fa-plus me-3"></i>Add More Item</button>
                                            </th>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->
            </div>
        </div>
    </div>
</div>

<script> 
    let addMore = 0;

    function showEditDrugRecallNotificationModal(id)
    {
        addMore = 0;
        $(`#edit_drug_recall_notifications_modal #drug_recall_notification_item_tbody`).empty();
        $(`#edit_drug_recall_notifications_modal #wholesaler_id`).empty();

        $('#edit_drug_recall_notifications_modal #notice_date').datepicker({
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

        var btn = document.querySelector(`#notification-btn-${id}`);
        let arr = JSON.parse(btn.dataset.array);

        show_drug_recall_notification_id = arr.id;
        show_drug_recall_notification_reference_number = arr.reference_number;
        table_drug_recall_notification_documents.draw();
        table_drug_recall_notification_documents.columns.adjust();
        // table_drug_recall_notification_documents.destroy();
        // loadDrugRecallNotificationDocuments();

        $('#edit_drug_recall_notifications_modal #eid').val(arr.id);
        $('#edit_drug_recall_notifications_modal #reference_number').val(arr.reference_number);
        $('#edit_drug_recall_notifications_modal #notice_date').val(arr.notice_date);
        $('#edit_drug_recall_notifications_modal #supplier_name').val(arr.supplier_name);
        $('#edit_drug_recall_notifications_modal #created_at').val(formatDateTime(arr.created_at));
        $('#edit_drug_recall_notifications_modal #comments').val(arr.comments);


        populateNormalSelect(`#edit_drug_recall_notifications_modal #wholesaler_id`, '#edit_drug_recall_notifications_modal', '/admin/search/wholesaler', {category: 'procurement'}, arr.wholesaler_id)

        $.each(arr.items, function(i, item) {
            const k = i+1;
            addMore++;
            $('#edit_drug_recall_notifications_modal #drug_recall_notification_item_tbody').append(
                `<tr id="drug_recall_notification_item_tbody_tr_${item.id}">
                    <td><b>#${k}</b></td>
                    <td>
                        <input type="hidden" id="id_${item.id}" name="id[${item.id}]" value="${item.id}">

                        <select class="form-select" data-placeholder="Select medication.." id="med_id_${item.id}" name="med_id[${item.id}]"  title="Drug Selection"></select>
                        <small id="med_id_err_${addMore}" class="text-danger d-none">Medication is required.</small>
                    </td>
                    <td> 
                        <input type="text" class="form-control form-control-sm" id="lot_number_${item.id}" name="lot_number[${item.id}]" value="${item.lot_number}" placeholder="Lot #">
                    </td>
                    <td>
                        <input type="number" min="0" step="1" class="form-control form-control-sm" id="qty_${item.id}" name="qty[${item.id}]" value="${item.qty}" placeholder="Qty">
                    </td>
                    <td> 
                        <input type="text" class="form-control form-control-sm" id="ndc_${item.id}" name="ndc[${item.id}]" value="${item.ndc}">
                        <small id="ndc_err_${addMore}" class="text-danger d-none">NDC is required.</small>
                    </td>
                    <td> 
                        <input type="text" class="form-control form-control-sm datepicker" id="expiration_date_${item.id}" name="expiration_date[${item.id}]" value="${(item.expiration_date == '0000-00-00'||item.expiration_date == null) ? '' : item.expiration_date}" placeholder="YYYY-MM-DD">
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary me-1" id="update_item_btn_${item.id}" title="SAVE ITEM #${k}" onclick="updateDrugRecallNotificationItem(${item.id}, ${k})"><i class="fa fa-save"></i></button>
                        <button class="btn btn-sm btn-danger" id="delete_item_btn_${item.id}" title="DELETE ITEM #${k}" onclick="deleteDrugRecallNotificationItem(${item.id}, ${k})"><i class="fa fa-trash-can"></i></button>
                    </td>
                </tr>`
            );

            $(`#edit_drug_recall_notifications_modal #expiration_date_${item.id}`).datepicker({
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

            searchSelect2ApiDrug(`#edit_drug_recall_notifications_modal #med_id_${item.id}`, 'edit_drug_recall_notifications_modal');

            var newOption = new Option(item.drug_name, item.med_id, true, true);
            $(`#edit_drug_recall_notifications_modal #med_id_${item.id}`).append(newOption).trigger('change');
        });

        $('#edit_drug_recall_notifications_modal').modal('show');
    }

    function updateDrugRecallNotification()
    {
        const reference_number = $('#edit_drug_recall_notifications_modal #reference_number').val();
        let data = {
            id: $('#edit_drug_recall_notifications_modal #eid').val(),
            wholesaler_id: $('#edit_drug_recall_notifications_modal #wholesaler_id').val(),
            notice_date: $('#edit_drug_recall_notifications_modal #notice_date').val(),
            supplier_name: $('#edit_drug_recall_notifications_modal #supplier_name').val(),
            comments: $('#edit_drug_recall_notifications_modal #comments').val(),
        };

        $(`#edit_drug_recall_notifications_modal #update_btn`).html('<i class="fa-solid fa-spinner me-3"></i>UPDATING...').attr('disabled', 'disabled');

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PUT",
            url: '/store/procurement/drug-recall-notifications/edit',
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {

                reloadDataTable();
                
                $('#alert-message-for-drug-recall-notification').html(`<div class="alert alert-success">Reference <b>#${reference_number}</b> successfully <b>updated</b>.</div>`);
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);

                $(`#edit_drug_recall_notifications_modal #update_btn`).html('<i class="fa fa-edit me-3"></i>UPDATE CHANGES');
                $(`#edit_drug_recall_notifications_modal #update_btn`).removeAttr('disabled');

            },error: function(msg) {
                handleErrorResponse(msg);
                $(`#edit_drug_recall_notifications_modal #update_btn`).html('<i class="fa fa-edit me-3"></i>UPDATE CHANGES');
                $(`#edit_drug_recall_notifications_modal #update_btn`).removeAttr('disabled');

                console.log("Error");
                console.log(msg.responseText);
            }

        });
    }

    function addNotificationItemsEditModal()
    {
        var container = $('#edit_drug_recall_notifications_modal #drug_recall_notification_item_table_container');
            container.animate({
            scrollTop: container.prop("scrollHeight")
        }, 500);

        addMore++;

        $('#edit_drug_recall_notifications_modal #drug_recall_notification_item_tbody').append(
            `<tr id="new_drug_recall_notification_item_tbody_tr_${addMore}">
                <td class="text-danger" id="new_item_td_${addMore}"><b>#${addMore}</b></td>
                <td>
                    <input type="hidden" id="new_id_${addMore}" name="new_id[${addMore}]" value="0">

                    <select class="form-select" data-placeholder="Select medication.." id="new_med_id_${addMore}" name="med_id[${addMore}]" title="Drug Selection"></select>
                    <small id="med_id_err_${addMore}" class="text-danger d-none">Medication is required.</small>
                </td>
                <td> 
                    <input type="text" class="form-control form-control-sm" id="new_lot_number_${addMore}" name="lot_number[${addMore}]" placeholder="Lot #">
                </td>
                <td>
                    <input type="number" min="0" step="1" class="form-control form-control-sm" id="new_qty_${addMore}" name="qty[${addMore}]" placeholder="Qty">
                </td>
                <td> 
                    <input type="text" class="form-control form-control-sm" id="new_ndc_${addMore}" name="ndc[${addMore}]">
                    <small id="ndc_err_${addMore}" class="text-danger d-none">NDC is required.</small>
                </td>
                <td> 
                    <input type="text" class="form-control form-control-sm datepicker" id="new_expiration_date_${addMore}" name="expiration_date[${addMore}]" placeholder="YYYY-MM-DD">
                </td>
                <td>
                    <button class="btn btn-sm btn-primary me-1" id="save_item_btn_${addMore}" title="SAVE ITEM #${addMore}" onclick="saveDrugRecallNotificationItem(${addMore})"><i class="fa fa-save"></i></button>
                    <button class="btn btn-sm btn-danger" id="remove_item_btn_${addMore}" title="DELETE ITEM #${addMore}" onclick="removeDrugRecallNotificationItem(${addMore})"><i class="fa fa-trash-can"></i></button>
                </td>
            </tr>`
        );

        $(`#edit_drug_recall_notifications_modal #new_expiration_date_${addMore}`).datepicker({
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

        searchSelect2ApiDrug(`#edit_drug_recall_notifications_modal #new_med_id_${addMore}`, 'edit_drug_recall_notifications_modal');
    }

    function saveDrugRecallNotificationItem(key)
    {
        const item_key = $(`#edit_drug_recall_notifications_modal #new_id_${key}`).val();

        const med_id = $(`#edit_drug_recall_notifications_modal #new_med_id_${key}`).val();
        const ndc = $(`#edit_drug_recall_notifications_modal #new_ndc_${key}`).val();

        let flag = true;
        $(`#edit_drug_recall_notifications_modal #med_id_err_${key}`).addClass('d-none');
        if(med_id == '' || med_id == null || med_id == undefined) {
            $(`#edit_drug_recall_notifications_modal #med_id_err_${key}`).removeClass('d-none');
            flag = false;
        }
        $(`#edit_drug_recall_notifications_modal #ndc_err_${key}`).addClass('d-none');
        if(ndc == '' || ndc == null || ndc == undefined) {
            $(`#edit_drug_recall_notifications_modal #ndc_err_${key}`).removeClass('d-none');
            flag = false;
        }

        if(flag === false) {
            return;
        }

        let data = {
            drug_recall_notification_id: $('#edit_drug_recall_notifications_modal #eid').val(),
            med_id: med_id,
            drug_name: $(`#edit_drug_recall_notifications_modal #new_med_id_${key} option:selected`).text(),
            lot_number: $(`#edit_drug_recall_notifications_modal #new_lot_number_${key}`).val(),
            qty: $(`#edit_drug_recall_notifications_modal #new_qty_${key}`).val(),
            ndc: ndc,
            expiration_date: $(`#edit_drug_recall_notifications_modal #new_expiration_date_${key}`).val()
        };

        let _uri = '/store/procurement/drug-recall-notification-item/add';
        let _text = 'created';
        if(item_key != 0) {
            data['id'] = item_key;
            _uri = '/store/procurement/drug-recall-notification-item/edit';
            _text = 'updated';
        }

        $(`#edit_drug_recall_notifications_modal #save_item_btn_${key}`).html('<i class="fa-solid fa-spinner"></i>').attr('disabled', 'disabled');

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: _uri,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {

                reloadDataTable();

                const item = res.data;

                $(`#edit_drug_recall_notifications_modal #new_id_${key}`).val(item.id);

                if(item_key == 0) {
                    $(`#edit_drug_recall_notifications_modal #new_item_td_${key}`).removeClass('text-danger');
                    $(`#edit_drug_recall_notifications_modal #new_item_td_${key}`).addClass('text-primary');
                }
                
                $('#alert-message-for-drug-recall-notification-items').html(`<div class="alert alert-success">Item <b>#${key}</b> successfully <b>${_text}</b>.</div>`);
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);

                $(`#edit_drug_recall_notifications_modal #save_item_btn_${key}`).html('<i class="fa fa-save"></i>');
                $(`#edit_drug_recall_notifications_modal #save_item_btn_${key}`).removeAttr('disabled');

            },error: function(msg) {
                handleErrorResponse(msg);
                $(`#edit_drug_recall_notifications_modal #save_item_btn_${key}`).html('<i class="fa fa-save"></i>');
                $(`#edit_drug_recall_notifications_modal #save_item_btn_${key}`).removeAttr('disabled');

                console.log("Error");
                console.log(msg.responseText);
            }

        });

    }

    function updateDrugRecallNotificationItem(id, key)
    {
        const med_id = $(`#edit_drug_recall_notifications_modal #med_id_${id}`).val();
        const ndc = $(`#edit_drug_recall_notifications_modal #ndc_${id}`).val();

        let flag = true;
        $(`#edit_drug_recall_notifications_modal #med_id_err_${key}`).addClass('d-none');
        if(med_id == '' || med_id == null || med_id == undefined) {
            $(`#edit_drug_recall_notifications_modal #med_id_err_${key}`).removeClass('d-none');
            flag = false;
        }
        $(`#edit_drug_recall_notifications_modal #ndc_err_${key}`).addClass('d-none');
        if(ndc == '' || ndc == null || ndc == undefined) {
            $(`#edit_drug_recall_notifications_modal #ndc_err_${key}`).removeClass('d-none');
            flag = false;
        }

        if(flag === false) {
            return;
        }

        let data = {
            drug_recall_notification_id: $('#edit_drug_recall_notifications_modal #eid').val(),
            id: $(`#edit_drug_recall_notifications_modal #id_${id}`).val(),
            med_id: med_id,
            drug_name: $(`#edit_drug_recall_notifications_modal #med_id_${id} option:selected`).text(),
            lot_number: $(`#edit_drug_recall_notifications_modal #lot_number_${id}`).val(),
            qty: $(`#edit_drug_recall_notifications_modal #qty_${id}`).val(),
            ndc: ndc,
            expiration_date: $(`#edit_drug_recall_notifications_modal #expiration_date_${id}`).val()
        };

        $(`#edit_drug_recall_notifications_modal #update_item_btn_${id}`).html('<i class="fa-solid fa-spinner"></i>').attr('disabled', 'disabled');

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: '/store/procurement/drug-recall-notification-item/edit',
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {

                reloadDataTable();
                
                $('#alert-message-for-drug-recall-notification-items').html(`<div class="alert alert-success">Item <b>#${key}</b> successfully <b>updated</b>.</div>`);
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);

                $(`#edit_drug_recall_notifications_modal #update_item_btn_${id}`).html('<i class="fa fa-save"></i>');
                $(`#edit_drug_recall_notifications_modal #update_item_btn_${id}`).removeAttr('disabled');

            },error: function(msg) {
                handleErrorResponse(msg);
                $(`#edit_drug_recall_notifications_modal #update_item_btn_${id}`).html('<i class="fa fa-save"></i>');
                $(`#edit_drug_recall_notifications_modal #update_item_btn_${id}`).removeAttr('disabled');

                console.log("Error");
                console.log(msg.responseText);
            }

        });
    }

    function removeDrugRecallNotificationItem(key)
    {
        const item_key = $(`#edit_drug_recall_notifications_modal #new_id_${key}`).val();

        if(item_key == 0) {
            $(`#edit_drug_recall_notifications_modal #new_drug_recall_notification_item_tbody_tr_${key}`).remove();
            return;
        }
        deleteDrugRecallNotificationItem(item_key, key, true);
    }

    function deleteDrugRecallNotificationItem(id, key, is_new = false)
    {
        if(is_new == true) {
            $(`#edit_drug_recall_notifications_modal #remove_item_btn_${key}`).html('<i class="fa-solid fa-spinner"></i>').attr('disabled', 'disabled');
        } else {
            $(`#edit_drug_recall_notifications_modal #delete_item_btn_${id}`).html('<i class="fa-solid fa-spinner"></i>').attr('disabled', 'disabled');
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `You will not be able to recover this item #${key}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it'
        }).then((result) => {
            if (result.isConfirmed) {

                let data = {
                    id: id
                };
                $.ajax({
                    //laravel requires this thing, it fetches it from the meta up in the head
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: "DELETE",
                    url: '/store/procurement/drug-recall-notification-item/delete',
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(res) {

                        reloadDataTable();

                        const item = res.data;
                        
                        $('#alert-message-for-drug-recall-notification-items').html(`<div class="alert alert-warning">Item <b>#${key}</b> successfully <b>deleted</b>.</div>`);
                        setTimeout(function() {
                            $('.alert').alert('close');
                        }, 5000);

                        if(is_new == false) {
                            $(`#edit_drug_recall_notifications_modal #drug_recall_notification_item_tbody_tr_${id}`).remove();
                            $(`#edit_drug_recall_notifications_modal #delete_item_btn_${id}`).html('<i class="fa fa-trash-can">');
                            $(`#edit_drug_recall_notifications_modal #delete_item_btn_${id}`).removeAttr('disabled');
                        } else {
                            $(`#edit_drug_recall_notifications_modal #new_drug_recall_notification_item_tbody_tr_${key}`).remove();
                            $(`#edit_drug_recall_notifications_modal #remove_item_btn_${key}`).html('<i class="fa fa-trash-can">');
                            $(`#edit_drug_recall_notifications_modal #remove_item_btn_${key}`).removeAttr('disabled');
                        }

                    },error: function(msg) {
                        handleErrorResponse(msg);
                        if(is_new == false) {
                            $(`#edit_drug_recall_notifications_modal #delete_item_btn_${id}`).html('<i class="fa fa-trash-can">');
                            $(`#edit_drug_recall_notifications_modal #delete_item_btn_${id}`).removeAttr('disabled');
                        } else {
                            $(`#edit_drug_recall_notifications_modal #remove_item_btn_${key}`).html('<i class="fa fa-trash-can">');
                            $(`#edit_drug_recall_notifications_modal #remove_item_btn_${key}`).removeAttr('disabled');
                        }

                        console.log("Error");
                        console.log(msg.responseText);
                    }

                });
            } else {
                if(is_new == false) {
                    $(`#edit_drug_recall_notifications_modal #delete_item_btn_${id}`).html('<i class="fa fa-trash-can">');
                    $(`#edit_drug_recall_notifications_modal #delete_item_btn_${id}`).removeAttr('disabled');
                } else {
                    $(`#edit_drug_recall_notifications_modal #remove_item_btn_${key}`).html('<i class="fa fa-trash-can">');
                    $(`#edit_drug_recall_notifications_modal #remove_item_btn_${key}`).removeAttr('disabled');
                }
            }
        });
        
    }
</script>