<div class="modal" id="edit_drug_order_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Edit Drug Order</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- <form action="" method="POST" id="#drug_order_edit_form"> --}}
                    @include('stores/procurement/pharmacy/drugOrders/partials/edit-form')
                {{-- </form> --}}
            </div>
        </div>
    </div>
</div>

<script> 
    let addMore = 0;
    let drug_order_id;
    function showEditDetailsModal(id) {
        addMore = 0;
        drug_order_id = id;
        $(`#edit_drug_order_modal #drug_order_item_tbody`).empty();
        $(`#edit_drug_order_modal #status_id`).empty();
        $(`#edit_drug_order_modal #wholesaler_id`).empty();

        $('#edit_drug_order_modal #order_date').datepicker({
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

        var btn = document.querySelector(`#drug-order-show-btn-${id}`);
        let arr = JSON.parse(btn.dataset.array);

        $('#edit_drug_order_modal #po_name').val(arr.po_name);
        $('#edit_drug_order_modal #po_number').val(arr.order_number);
        $('#edit_drug_order_modal #order_date').val(arr.order_date);
        $('#edit_drug_order_modal #account_number').val(arr.account_number);
        $('#edit_drug_order_modal #wholesaler_name').val(arr.wholesaler_name);
        $('#edit_drug_order_modal #created_at').val(formatDateTime(arr.created_at));
        $('#edit_drug_order_modal #comments').val(arr.comments);
        $('#edit_drug_order_modal #po_memo').val(arr.po_memo);
        if(arr.file){
            console.log(arr.file);
            let filename = arr.file.filename;
            if (filename.length > 30) {
                filename = filename.substring(0, 30) + '...';
            }
            $('#edit_drug_order_modal .file_name').text(filename);
            $("#edit_drug_order_modal #file_id").val(arr.file_id);
            $("#edit_drug_order_modal #chip_controller").show();
            $("#edit_drug_order_modal #file").hide();
            $('#edit_drug_order_modal .file_name').attr("href", "/admin/file/download/"+arr.file_id+"");
                        
        }
        else{
            $("#edit_drug_order_modal #chip_controller").hide();
            $("#edit_drug_order_modal #file").show();
        }
        populateNormalSelect(`#edit_drug_order_modal #status_id`, '#edit_drug_order_modal', '/admin/search/store-status', {category: 'procurement_order'}, arr.status_id)
        populateNormalSelect(`#edit_drug_order_modal #wholesaler_id`, '#edit_drug_order_modal', '/admin/search/wholesaler', {category: 'procurement'}, arr.wholesaler_id)

        $.each(arr.items_imported, function(i, item) {
            const k = i+1;
            addMore++;
            $('#edit_drug_order_modal #drug_order_item_tbody').append(
                `<tr id="drug_order_item_tbody_tr_${item.id}">
                    <td><b>#${k}</b></td>
                    <td>                                                                                       
                        <input type="text" class="form-control form-control-sm" id="product_description_${item.id}" name="product_description[${item.id}]" value="${item.product_description}">
                    </td>
                    <td>
                        <input type="number" min="0" step="1" class="form-control form-control-sm" id="quantity_ordered_${item.id}" name="quantity_ordered[${item.id}]" value="${item.quantity_ordered}">
                    </td>
                    <td>
                        <input type="number" min="0" step="1" class="form-control form-control-sm" id="confirmed_qty_${item.id}" name="confirmed_qty[${item.id}]" value="${item.expected_quantity_shipped}">
                    </td>
                    <td>
                        <input type="number" min="0" step="1" class="form-control form-control-sm" id="invoiced_qty_${item.id}" name="invoiced_qty[${item.id}]" value="${item.quantity_shipped}">
                    </td>
                    <td> 
                        <input type="number" min="0" step="1" class="form-control form-control-sm" id="quantity_confirmed_${item.id}" name="quantity_confirmed[${item.id}]" value="${item.quantity_confirmed}">
                    </td>
                    <td> 
                        <input type="number" min="0" class="form-control form-control-sm text-end" id="acq_cost_${item.id}" name="acq_cost[${item.id}]" value="${item.acq_cost}">
                    </td>
                    <td> 
                        <input type="text" class="form-control form-control-sm" id="ndc_${item.id}" name="ndc[${item.id}]" value="${item.ndc}">
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${k}" onclick="clickUpdateDrugOrderItem(event,${item.id}, ${k})"><i class="fa fa-save"></i></button>
                        <button class="btn btn-sm btn-danger" title="DELETE ITEM #${k}" onclick="clickDeleteDrugOrderItem(event,${item.id}, ${k})"><i class="fa fa-trash-can"></i></button>
                    </td>
                </tr>`
            );
        });
        

        $('#edit_drug_order_modal').modal('show');
    }

    function showGridEditDetailsModal(data) {
        addMore = 0;
        drug_order_id = data.id;
        $(`#edit_drug_order_modal #drug_order_item_tbody`).empty();
        $(`#edit_drug_order_modal #status_id`).empty();
        $(`#edit_drug_order_modal #wholesaler_id`).empty();

        $('#edit_drug_order_modal #order_date').datepicker({
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

        var btn = document.querySelector(`#drug-order-show-btn-${data.id}`);

        $('#edit_drug_order_modal #po_name').val(data.po_name);
        $('#edit_drug_order_modal #po_number').val(data.order_number);
        $('#edit_drug_order_modal #order_date').val(data.order_date);
        $('#edit_drug_order_modal #account_number').val(data.account_number);
        $('#edit_drug_order_modal #wholesaler_name').val(data.wholesaler_name);
        $('#edit_drug_order_modal #created_at').val(formatDateTime(data.created_at));
        $('#edit_drug_order_modal #comments').val(data.comments);
        $('#edit_drug_order_modal #po_memo').val(data.po_memo);
        if(data.file){
            console.log(data.file);
            let filename = data.file.filename;
            if (filename.length > 30) {
                filename = filename.substring(0, 30) + '...';
            }
            $('#edit_drug_order_modal .file_name').text(filename);
            $("#edit_drug_order_modal #file_id").val(data.file_id);
            $("#edit_drug_order_modal #chip_controller").show();
            $("#edit_drug_order_modal #file").hide();
            $('#edit_drug_order_modal .file_name').attr("href", "/admin/file/download/"+data.file_id+"");
                        
        }
        else{
            $("#edit_drug_order_modal #chip_controller").hide();
            $("#edit_drug_order_modal #file").show();
        }
        populateNormalSelect(`#edit_drug_order_modal #status_id`, '#edit_drug_order_modal', '/admin/search/store-status', {category: 'procurement_order'}, data.status_id)
        populateNormalSelect(`#edit_drug_order_modal #wholesaler_id`, '#edit_drug_order_modal', '/admin/search/wholesaler', {category: 'procurement'}, data.wholesaler_id)

        $.each(data.items_imported, function(i, item) {
            const k = i+1;
            addMore++;
            $('#edit_drug_order_modal #drug_order_item_tbody').append(
                `<tr id="drug_order_item_tbody_tr_${item.id}">
                    <td><b>#${k}</b></td>
                    <td>                                                                                       
                        <input type="text" class="form-control form-control-sm" id="product_description_${item.id}" name="product_description[${item.id}]" value="${item.product_description}">
                    </td>
                    <td>
                        <input type="number" min="0" step="1" class="form-control form-control-sm" id="quantity_ordered_${item.id}" name="quantity_ordered[${item.id}]" value="${item.quantity_ordered}">
                    </td>
                    <td>
                        <input type="number" min="0" step="1" class="form-control form-control-sm" id="confirmed_qty_${item.id}" name="confirmed_qty[${item.id}]" value="${item.expected_quantity_shipped}">
                    </td>
                    <td>
                        <input type="number" min="0" step="1" class="form-control form-control-sm" id="invoiced_qty_${item.id}" name="invoiced_qty[${item.id}]" value="${item.quantity_shipped}">
                    </td>
                    <td> 
                        <input type="number" min="0" step="1" class="form-control form-control-sm" id="quantity_confirmed_${item.id}" name="quantity_confirmed[${item.id}]" value="${item.quantity_confirmed}">
                    </td>
                    <td> 
                        <input type="number" min="0" class="form-control form-control-sm text-end" id="acq_cost_${item.id}" name="acq_cost[${item.id}]" value="${item.acq_cost}">
                    </td>
                    <td> 
                        <input type="text" class="form-control form-control-sm" id="ndc_${item.id}" name="ndc[${item.id}]" value="${item.ndc}">
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${k}" onclick="clickUpdateDrugOrderItem(event,${item.id}, ${k})"><i class="fa fa-save"></i></button>
                        <button class="btn btn-sm btn-danger" title="DELETE ITEM #${k}" onclick="clickDeleteDrugOrderItem(event,${item.id}, ${k})"><i class="fa fa-trash-can"></i></button>
                    </td>
                </tr>`
            );
        });
        
        $('#edit_drug_order_modal').modal('show');
    }
</script>