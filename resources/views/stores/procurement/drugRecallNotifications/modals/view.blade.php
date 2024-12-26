<div class="modal" id="view_drug_recall_notification_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Drug Order Details</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            
                <!--start row-->
                <div class="container">                         
                    <div class="row">
                        <div class="col">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th width="15%">Reference No.</th>
                                        <td width="40%" id="reference_number"></td>
                                        <th width="15%">Notice Date</th>
                                        <td width="30%" id="notice_date"></td>
                                    </tr>
                                    <tr>
                                        <th>Wholesaler</th>
                                        <td id="wholesaler_name"></td>
                                        <th>Supplier Name</th>
                                        <td id="supplier_name"></td>                                    
                                    </tr>
                                    <tr>
                                        <th>Comments</th>
                                        <td id="comments"></td>
                                        <th>Date Created</th>
                                        <td id="created_at"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <div class="table-container dt_scrollable_item_table_container" id="drug_order_item_table_container">
                                    <table class="table table-bordered table-striped table-hover" id="drug_order_item_table">
                                        <thead>
                                            <tr>
                                                <th width="40%">Drug Name</th>
                                                <th width="13%">Lot #</th>
                                                <th width="12%">Qty</th>
                                                <th width="17%">NDC</th>
                                                <th width="18%">Exp. Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="drug_recall_notification_item_tbody" style="overflow-y: auto;"> </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->

            </div>

            <div class="modal-footer">
                <button class="btn btn-info2 w-25" id="show_documents_btn" onclick="showDrugRecallNotificationDocuments()"><i class="fa fa-paperclip me-3"></i>Show Files</button>
                @can('menu_store.procurement.drug_recall_notifications.update')
                    <button type="button" class="btn btn-primary w-25" onclick="clickEditShowModal()"><i class="fa fa-pencil me-2"> </i>EDIT</button>
                @endcan
            </div>
        </div>
    </div>
</div>



<script>
    function showViewDrugRecallNotificationModal(id)
    {
        $(`#view_drug_recall_notification_modal #drug_recall_notification_item_tbody`).empty();
        show_drug_recall_notification_id = id;
        var btn = document.querySelector(`#notification-btn-${id}`);
        let arr = JSON.parse(btn.dataset.array);
        // console.log('fire-------------',arr);
        let wholesaler = arr.wholesaler ? arr.wholesaler.name : '';

        show_drug_recall_notification_reference_number = arr.reference_number;

        table_drug_recall_notification_documents.draw();
        table_drug_recall_notification_documents.columns.adjust();
        // table_drug_recall_notification_documents.destroy();
        // loadDrugRecallNotificationDocuments();

        $('#view_drug_recall_notification_modal #reference_number').html(arr.reference_number);
        $('#view_drug_recall_notification_modal #notice_date').html(formatDate(arr.created_at));
        $('#view_drug_recall_notification_modal #wholesaler_name').html(wholesaler);
        $('#view_drug_recall_notification_modal #supplier_name').html(arr.supplier_name);
        $('#view_drug_recall_notification_modal #created_at').html(formatDateTime(arr.created_at));
        $('#view_drug_recall_notification_modal #comments').html(arr.comments);

        $.each(arr.items, function(i, item) {
            
            $('#view_drug_recall_notification_modal #drug_recall_notification_item_tbody').append(
                `<tr>
                    <td> ${(item.drug_name ? item.drug_name : '')} </td>
                    <td> ${(item.lot_number ? item.lot_number : '')} </td>
                    <td> ${(item.qty ? item.qty : '')} </td>
                    <td> ${(item.ndc ? item.ndc : '')} </td>
                    <td> ${(item.expiration_date ? item.expiration_date : '')} </td>
                </tr>`
            );

        });
        

        $('#view_drug_recall_notification_modal').modal('show');
    }

    function clickEditShowModal()
    {
        sweetAlertLoading();
        $('#view_drug_recall_notification_modal').modal('hide');
        showEditDrugRecallNotificationModal(show_drug_recall_notification_id);
        Swal.close();
    }
</script>