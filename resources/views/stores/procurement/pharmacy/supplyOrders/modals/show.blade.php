<div class="modal" id="show_supply_order_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Supply Order Details</h6>
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
                                        <th width="18%">Order Number</th>
                                        <td width="37%" id="order_number"></td>
                                        <th>Date Created</th>
                                        <td id="created_at"></td>
                                    </tr>
                                    <tr>
                                        <th>Wholesaler</th>
                                        <td id="wholesaler"></td>
                                        <th>Status</th>
                                        <td id="status"></td>
                                    </tr>
                                    <tr>
                                        <th>Comments</th>
                                        <td colspan="3" id="comments"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <div class="table-container" id="supply_order_item_table_container">
                                    <table class="table table-bordered table-striped table-hover" id="supply_order_item_table">
                                        <thead>
                                            <tr>
                                                <th width="20%">Item #/URL</th>
                                                <th width="10%">Code</th>
                                                <th width="32%">Description</th>
                                                <th width="10%">Quantity</th>
                                                <th width="10%">Act Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="supply_order_item_tbody" style="overflow-y: auto;"> </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->

            </div>

            @can('menu_store.procurement.pharmacy.supplies_orders.update')
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="clickEditShowModal()"><i class="fa fa-pencil me-2"> </i>EDIT</button>
                </div>
            @endcan
        </div>
    </div>
</div>



<script>   
    let show_list_supply_order_id;
    let show_grid_supply_order_id;

    function showViewDetailsModal(id) {
        $(`#show_supply_order_modal #supply_order_item_tbody`).empty();
        show_list_supply_order_id = id;
        var btn = document.querySelector(`#supply-order-show-btn-${id}`);
        let arr = JSON.parse(btn.dataset.array);
        let wholesaler = arr.wholesaler ? arr.wholesaler.name : '';
        // console.log('fire-------------',arr);

        var status = `<button type="button" class="btn btn-${arr.status.class} btn-sm w-100" ><small>${arr.status.name}</small></button>`;

        $('#show_supply_order_modal #order_number').html(arr.order_number);
        $('#show_supply_order_modal #created_at').html(formatDateTime(arr.created_at));
        $('#show_supply_order_modal #wholesaler').html(wholesaler);
        $('#show_supply_order_modal #comments').html(arr.comments);
        $('#show_supply_order_modal #status').html(status);

        $.each(arr.items, function(i, item) {
            
            $('#show_supply_order_modal #supply_order_item_tbody').append(
                `<tr>
                    <td> ${(item.url ? item.url : (item.number ? item.number: ''))} </td>
                    <td> ${(item.code ? item.code : '')} </td>
                    <td> ${(item.description != "url request") ? item.description : ''} </td>
                    <td> ${(item.quantity ? item.quantity : '')} </td>
                    <td> ${(item.actual_quantity ? item.actual_quantity : '')} </td>
                </tr>`
            );

        });
        

        $('#show_supply_order_modal').modal('show');
    }

    function showGridViewModal(items, id) {
        data = items.getAttribute('data-supply-items');
        let arr = JSON.parse(decodeURIComponent(data));

        $(`#show_supply_order_modal #supply_order_item_tbody`).empty();
        show_grid_supply_order_id = arr;
        
        let wholesaler = arr.wholesaler ? arr.wholesaler.name : '';
        // console.log('fire-------------',arr);

        var status = `<button type="button" class="btn btn-${arr.status.class} btn-sm w-100" ><small>${arr.status.name}</small></button>`;

        $('#show_supply_order_modal #order_number').html(arr.order_number);
        $('#show_supply_order_modal #created_at').html(formatDateTime(arr.created_at));
        $('#show_supply_order_modal #wholesaler').html(wholesaler);
        $('#show_supply_order_modal #comments').html(arr.comments);
        $('#show_supply_order_modal #status').html(status);

        $.each(arr.items, function(i, item) {
            
            $('#show_supply_order_modal #supply_order_item_tbody').append(
                `<tr>
                    <td> ${(item.url ? item.url : (item.number ? item.number: ''))} </td>
                    <td> ${(item.code ? item.code : '')} </td>
                    <td> ${(item.description != "url request") ? item.description : ''} </td>
                    <td> ${(item.quantity ? item.quantity : '')} </td>
                    <td> ${(item.actual_quantity ? item.actual_quantity : '')} </td>
                </tr>`
            );

        });
        

        $('#show_supply_order_modal').modal('show');
    }

    
    function clickEditShowModal() {
        var activeTab = localStorage.getItem('activeTab');
        sweetAlertLoading();
        $('#show_supply_order_modal').modal('hide');
        if (activeTab == '#grid-view') {
            showGridEditDetailsModal(show_grid_supply_order_id);
        } else {
            showEditDetailsModal(show_list_supply_order_id);
        }
        Swal.close();
    }
</script>