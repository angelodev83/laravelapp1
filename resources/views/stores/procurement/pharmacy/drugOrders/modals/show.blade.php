<div class="modal" id="show_drug_order_modal" tabindex="-1">
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
                                        <th width="15%">PO Name</th>
                                        <td width="40%" id="po_number"></td>
                                        <th width="15%">Order Date</th>
                                        <td width="30%" id="order_date"></td>
                                    </tr>
                                    <tr>
                                        <th>Wholesaler</th>
                                        <td id="wholesaler_name"></td>
                                        <th>Date Created</th>
                                        <td id="created_at"></td>                                    
                                    </tr>
                                    <tr>
                                        <th>Account #</th>
                                        <td id="account_number"></td>    
                                        <th>Status</th>
                                        <td id="status"></td>
                                    </tr>
                                    <tr>
                                        <th>Comments</th>
                                        <td id="comments"></td>
                                        <th>PO Memo</th>
                                        <td id="po_memo"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <div class="table-container" id="drug_order_item_table_container">
                                    <table class="table table-bordered table-striped table-hover" id="drug_order_item_table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Quantity Ordered</th>
                                                <th>Confirmed Qty</th>
                                                <th>Invoiced Qty</th>
                                                <th>Received Quantity</th>
                                                <th>Acq Cost</th>
                                                <th>NDC</th>
                                            </tr>
                                        </thead>
                                        <tbody id="drug_order_item_tbody" style="overflow-y: auto;"> </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->

            </div>

            @can('menu_store.procurement.pharmacy.drug_orders.update')
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="clickEditShowModal()"><i class="fa fa-pencil me-2"> </i>EDIT</button>
                </div>
            @endcan
        </div>
    </div>
</div>



<script>   
    let show_list_drug_order_id;
    let show_grid_drug_order_id;

    function showViewDetailsModal(id)
    {
        console.log('list-view');
        $(`#show_drug_order_modal #drug_order_item_tbody`).empty();
        show_list_drug_order_id = id;
        var btn = document.querySelector(`#drug-order-show-btn-${id}`);
        let arr = JSON.parse(btn.dataset.array);
        // console.log('fire-------------',arr);
        let wholesaler = arr.wholesaler ? arr.wholesaler.name : '';

        var status = `<button type="button" class="btn btn-${arr.status.class} btn-sm w-100" ><small>${arr.status.name}</small></button>`;

        $('#show_drug_order_modal #po_name').html(arr.po_name);
        $('#show_drug_order_modal #po_memo').html(arr.po_memo);
        $('#show_drug_order_modal #po_number').html(arr.order_number);
        $('#show_drug_order_modal #order_date').html(formatDate(arr.created_at));
        $('#show_drug_order_modal #account_number').html(arr.account_number);
        $('#show_drug_order_modal #wholesaler_name').html(wholesaler);
        $('#show_drug_order_modal #created_at').html(formatDateTime(arr.created_at));
        $('#show_drug_order_modal #comments').html(arr.comments);
        $('#show_drug_order_modal #status').html(status);

        $.each(arr.items_imported, function(i, item) {
            
            $('#show_drug_order_modal #drug_order_item_tbody').append(
                `<tr>
                    <td> ${(item.product_description ? item.product_description : '')} </td>
                    <td> ${(item.quantity_ordered ? item.quantity_ordered : '')} </td>
                    <td> ${(item.expected_quantity_shipped ? item.expected_quantity_shipped : '')} </td>
                    <td> ${(item.quantity_shipped ? item.quantity_shipped : '')} </td>
                    <td> ${(item.quantity_confirmed ? item.quantity_confirmed : '')} </td>
                    <td> ${(item.acq_cost ? item.acq_cost : '')} </td>
                    <td> ${(item.ndc ? item.ndc : '')} </td>
                </tr>`
            );

        });
        

        $('#show_drug_order_modal').modal('show');
    }

    function showGridViewDetailsModal(items, id) {
        console.log('grid-view');
        data = items.getAttribute('data-drug-items');
        let arr = JSON.parse(decodeURIComponent(data));
        show_grid_drug_order_id = arr;
        $('#show_drug_order_modal').modal('show');
        $(`#show_drug_order_modal #drug_order_item_tbody`).empty();
        
        var status = `<button type="button" class="btn btn-${arr.status.class} btn-sm w-100" ><small>${arr.status.name}</small></button>`;

        $('#show_drug_order_modal #po_name').html(arr.order_number);
        $('#show_drug_order_modal #po_memo').html(arr.po_memo);
        $('#show_drug_order_modal #po_number').html(arr.order_number);
        $('#show_drug_order_modal #order_date').html(formatDate(arr.created_at));
        $('#show_drug_order_modal #account_number').html(arr.account_number);
        $('#show_drug_order_modal #wholesaler_name').html(arr.wholesaler.name);
        $('#show_drug_order_modal #created_at').html(formatDateTime(arr.created_at));
        $('#show_drug_order_modal #comments').html(arr.comments);
        $('#show_drug_order_modal #status').html(status);

        $.each(arr.items_imported, function(i, item) {
            $('#show_drug_order_modal #drug_order_item_tbody').append(
                `<tr>
                    <td> ${(item.product_description ? item.product_description : '')} </td>
                    <td> ${(item.quantity_ordered ? item.quantity_ordered : '')} </td>
                    <td> ${(item.expected_quantity_shipped ? item.expected_quantity_shipped : '')} </td>
                    <td> ${(item.quantity_shipped ? item.quantity_shipped : '')} </td>
                    <td> ${(item.quantity_confirmed ? item.quantity_confirmed : '')} </td>
                    <td> ${(item.acq_cost ? item.acq_cost : '')} </td>
                    <td> ${(item.ndc ? item.ndc : '')} </td>
                </tr>`
            );

        });
    }

    function clickEditShowModal()
    {
        var activeTab = localStorage.getItem('activeTab');
        
        sweetAlertLoading();
        $('#show_drug_order_modal').modal('hide');
        if (activeTab == '#grid-view') {
            showGridEditDetailsModal(show_grid_drug_order_id);
        } else {
            showEditDetailsModal(show_list_drug_order_id);
        }
        Swal.close();
    }
</script>