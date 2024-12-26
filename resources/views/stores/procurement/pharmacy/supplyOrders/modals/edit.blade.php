<div class="modal" id="edit_supply_order_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Edit Supply Order</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- <form action="" method="POST" id="#supply_order_edit_form"> --}}
                    @include('stores/procurement/pharmacy/supplyOrders/partials/edit-form')
                {{-- </form> --}}
            </div>
        </div>
    </div>
</div>

<script> 
    let addMore = 0;
    let supply_order_id;
    function showEditDetailsModal(id) {
        addMore = 0;
        supply_order_id = id;
        $(`#edit_supply_order_modal #supply_order_item_tbody`).empty();
        $(`#edit_supply_order_modal #status_id`).empty();
        $(`#edit_supply_order_modal #wholesaler_id`).empty();

        $('#edit_supply_order_modal #order_date').datepicker({
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

        var btn = document.querySelector(`#supply-order-show-btn-${id}`);
        let arr = JSON.parse(btn.dataset.array);

        $('#edit_supply_order_modal #order_number').val(arr.order_number);
        $('#edit_supply_order_modal #order_date').val(arr.order_date);
        $('#edit_supply_order_modal #created_at').val(formatDateTime(arr.created_at));
        $('#edit_supply_order_modal #comments').val(arr.comments);
        if(arr.file){
            console.log(arr.file);
            let filename = arr.file.filename;
            if (filename.length > 30) {
                filename = filename.substring(0, 30) + '...';
            }
            $('#edit_supply_order_modal .file_name').text(filename);
            $("#edit_supply_order_modal #file_id").val(arr.file_id);
            $("#edit_supply_order_modal #chip_controller").show();
            $("#edit_supply_order_modal #file").hide();
            $('#edit_supply_order_modal .file_name').attr("href", "/admin/file/download/"+arr.file_id+"");
                        
        }
        else{
            $("#edit_supply_order_modal #chip_controller").hide();
            $("#edit_supply_order_modal #file").show();
        }
        populateNormalSelect(`#edit_supply_order_modal #status_id`, '#edit_supply_order_modal', '/admin/search/store-status', {category: 'procurement_order'}, arr.status_id)
        populateNormalSelect(`#edit_supply_order_modal #wholesaler_id`, '#edit_supply_order_modal', '/admin/search/wholesaler', {category: 'supply'}, arr.wholesaler_id)

        $.each(arr.items, function(i, item) {
            const k = i+1;
            addMore++;
            
            if(item.url != null){
                $('#edit_supply_order_modal #supply_order_item_tbody').append(
                    `<tr id="supply_order_item_tbody_tr_${item.id}">
                        <td><b>#${k}</b></td>
                        <td>    
                            <input type="text" class="form-control supplies-url-holder" name="url" id="url_${item.id}" placeholder="url" value="${item.url}">
                        </td>
                        <td>                                                                                       
                        </td>
                        <td>                                                                                       
                        </td>
                        <td>
                            <input type="number" min="0" step="1" class="form-control form-control-sm" id="quantity_${item.id}" name="quantity[${item.id}]" value="${item.quantity}">
                        </td>
                        <td> 
                            <input type="number" min="0" step="1" class="form-control form-control-sm" id="actual_quantity_${item.id}" name="actual_quantity[${item.id}]" value="${item.actual_quantity}">
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${k}" onclick="clickUpdateSupplyOrderItem(event,${item.id}, ${k})"><i class="fa fa-save"></i></button>
                            <button class="btn btn-sm btn-danger" title="DELETE ITEM #${k}" onclick="clickDeleteSupplyOrderItem(event,${item.id}, ${k})"><i class="fa fa-trash-can"></i></button>
                        </td>
                    </tr>`
                );
            }
            else{
                $('#edit_supply_order_modal #supply_order_item_tbody').append(
                    `<tr id="supply_order_item_tbody_tr_${item.id}">
                        <td><b>#${k}</b></td>
                        <td>    
                            <select class="form-select form-select-sm" data-placeholder="Select item.." name="number[${item.id}]" id="number_${item.id}" title="Select Item"></select>   
                            <input type="text" class="form-control form-control-sm add-hidden" readonly onclick="openEditSelect(${item.id})" id="item_${item.id}" name="item[${item.id}]" value="${item.number}" hidden>                                                                                
                        </td>
                        <td>                                                                                       
                            <input type="text" class="form-control form-control-sm" id="code_${item.id}" name="code[${item.id}]" value="${item.code}" disabled>
                        </td>
                        <td>                                                                                       
                            <input type="text" class="form-control form-control-sm" id="description_${item.id}" name="description[${item.id}]" value="${item.description}" disabled>
                        </td>
                        <td>
                            <input type="number" min="0" step="1" class="form-control form-control-sm" id="quantity_${item.id}" name="quantity[${item.id}]" value="${item.quantity}">
                        </td>
                        <td> 
                            <input type="number" min="0" step="1" class="form-control form-control-sm" id="actual_quantity_${item.id}" name="actual_quantity[${item.id}]" value="${item.actual_quantity}">
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${k}" onclick="clickUpdateSupplyOrderItem(event,${item.id}, ${k})"><i class="fa fa-save"></i></button>
                            <button class="btn btn-sm btn-danger" title="DELETE ITEM #${k}" onclick="clickDeleteSupplyOrderItem(event,${item.id}, ${k})"><i class="fa fa-trash-can"></i></button>
                        </td>
                    </tr>`
                );
            }
            
            $(`#number_${item.id}`).append("<option selected value='"+item.id+"'>"+item.number+"</option>");
            searchSupplyItem(`#number_${item.id}`, 'edit_supply_order_modal', null, item.id);            
        });

        $('#edit_supply_order_modal').modal('show');
    }
    function showGridEditDetailsModal(data) {
        addMore = 0;
        supply_order_id = data.id;
        $(`#edit_supply_order_modal #supply_order_item_tbody`).empty();
        $(`#edit_supply_order_modal #status_id`).empty();
        $(`#edit_supply_order_modal #wholesaler_id`).empty();

        $('#edit_supply_order_modal #order_date').datepicker({
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

        var btn = document.querySelector(`#supply-order-show-btn-${data.id}`);
        let arr = data;

        $('#edit_supply_order_modal #order_number').val(arr.order_number);
        $('#edit_supply_order_modal #order_date').val(arr.order_date);
        $('#edit_supply_order_modal #created_at').val(formatDateTime(arr.created_at));
        $('#edit_supply_order_modal #comments').val(arr.comments);
        if(arr.file){
            let filename = arr.file.filename;
            if (filename.length > 30) {
                filename = filename.substring(0, 30) + '...';
            }
            $('#edit_supply_order_modal .file_name').text(filename);
            $("#edit_supply_order_modal #file_id").val(arr.file_id);
            $("#edit_supply_order_modal #chip_controller").show();
            $("#edit_supply_order_modal #file").hide();
            $('#edit_supply_order_modal .file_name').attr("href", "/admin/file/download/"+arr.file_id+"");
                        
        }
        else{
            $("#edit_supply_order_modal #chip_controller").hide();
            $("#edit_supply_order_modal #file").show();
        }
        populateNormalSelect(`#edit_supply_order_modal #status_id`, '#edit_supply_order_modal', '/admin/search/store-status', {category: 'procurement_order'}, arr.status_id)
        populateNormalSelect(`#edit_supply_order_modal #wholesaler_id`, '#edit_supply_order_modal', '/admin/search/wholesaler', {category: 'supply'}, arr.wholesaler_id)

        $.each(arr.items, function(i, item) {
            const k = i+1;
            addMore++;

            if(item.url != null){
                $('#edit_supply_order_modal #supply_order_item_tbody').append(
                    `<tr id="supply_order_item_tbody_tr_${item.id}">
                        <td><b>#${k}</b></td>
                        <td>    
                            <input type="text" class="form-control supplies-url-holder" name="url" id="url_${item.id}" placeholder="url" value="${item.url}">
                        </td>
                        <td>                                                                                       
                        </td>
                        <td>                                                                                       
                        </td>
                        <td>
                            <input type="number" min="0" step="1" class="form-control form-control-sm" id="quantity_${item.id}" name="quantity[${item.id}]" value="${item.quantity}">
                        </td>
                        <td> 
                            <input type="number" min="0" step="1" class="form-control form-control-sm" id="actual_quantity_${item.id}" name="actual_quantity[${item.id}]" value="${item.actual_quantity}">
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${k}" onclick="clickUpdateSupplyOrderItem(event,${item.id}, ${k})"><i class="fa fa-save"></i></button>
                            <button class="btn btn-sm btn-danger" title="DELETE ITEM #${k}" onclick="clickDeleteSupplyOrderItem(event,${item.id}, ${k})"><i class="fa fa-trash-can"></i></button>
                        </td>
                    </tr>`
                );
            }
            else{
                $('#edit_supply_order_modal #supply_order_item_tbody').append(
                    `<tr id="supply_order_item_tbody_tr_${item.id}">
                        <td><b>#${k}</b></td>
                        <td>    
                            <select class="form-select form-select-sm" data-placeholder="Select item.." name="number[${item.id}]" id="number_${item.id}" title="Select Item"></select>   
                            <input type="text" class="form-control form-control-sm add-hidden" readonly onclick="openEditSelect(${item.id})" id="item_${item.id}" name="item[${item.id}]" value="${item.number}" hidden>                                                                                
                        </td>
                        <td>                                                                                       
                            <input type="text" class="form-control form-control-sm" id="code_${item.id}" name="code[${item.id}]" value="${item.code}" disabled>
                        </td>
                        <td>                                                                                       
                            <input type="text" class="form-control form-control-sm" id="description_${item.id}" name="description[${item.id}]" value="${item.description}" disabled>
                        </td>
                        <td>
                            <input type="number" min="0" step="1" class="form-control form-control-sm" id="quantity_${item.id}" name="quantity[${item.id}]" value="${item.quantity}">
                        </td>
                        <td> 
                            <input type="number" min="0" step="1" class="form-control form-control-sm" id="actual_quantity_${item.id}" name="actual_quantity[${item.id}]" value="${item.actual_quantity}">
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${k}" onclick="clickUpdateSupplyOrderItem(event,${item.id}, ${k})"><i class="fa fa-save"></i></button>
                            <button class="btn btn-sm btn-danger" title="DELETE ITEM #${k}" onclick="clickDeleteSupplyOrderItem(event,${item.id}, ${k})"><i class="fa fa-trash-can"></i></button>
                        </td>
                    </tr>`
                );
            }
            $(`#number_${item.id}`).append("<option selected value='"+item.id+"'>"+item.number+"</option>");
            searchSupplyItem(`#number_${item.id}`, 'edit_supply_order_modal', null, item.id);            
        });

        $('#edit_supply_order_modal').modal('show');
    }
</script>