<div class="modal" id="add_supply_order_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Order Form</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            
                    <!--start row-->
                    <div class="row">
                        <form action="" method="POST" id="#supply_order_add_form">
                            <div class="col-lg-12">
                            
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="order_number" class="form-label">Order Number</label>
                                        <input type="text" name="order_number" class="form-control" id="order_number" placeholder="Enter Order Number">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="order_date" class="form-label">Order Date</label>
                                        <div class="input-group"> <span class="input-group-text" id="icon-order-date"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control" id="order_date" aria-describedby="icon-order-date" placeholder="YYYY-MM-DD" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="wholesaler_id" class="form-label">Wholesaler</label>
                                        <select class="form-select" name="wholesaler_id" id="wholesaler_id"></select>
                                    </div>
                                </div> 
                                <div class="mt-4 col-md-12">
                                {{-- <div class="mt-4 card">
                                    <div class="p-0 card-body">
                                        <h6 class="card-title" id="medication_holder">Item Supplies*</h6> --}}
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <th width="21%">Item #/URL</th>
                                                    <th width="18%">Code</th>
                                                    {{-- <th>Name</th> --}}
                                                    <th width="46%">Description</th>
                                                    <th width="15%">Quantity</th>
                                                </thead>
                                                <tbody>
                                                    @for ($i = 0; $i < 3; $i++)
                                                        <tr>
                                                            <td class="appending-items-data-table">
                                                                <div class="hide-for-url"><select class="form-select" data-placeholder="Select item.." name="number" id="number-{{$i}}" title="Item Selection"></select></div>
                                                                <input type="text" class="form-control add-hidden" readonly onclick="openSelect({{$i}})" name="item" id="item-{{$i}}" placeholder="item" hidden>
                                                                <input type="text" class="form-control supplies-url-holder" name="url" id="url-{{$i}}" placeholder="url">
                                                            
                                                            </td>
                                                            <td class="appending-items-data-table">
                                                                <!-- <select class="form-select" data-placeholder="Select code.." name="number_code" id="number_code-{{$i}}" title="Code Selection"></select> -->
                                                                
                                                                <input type="text" class="form-control hide-for-url" name="code" id="code-{{$i}}" placeholder="Code" disabled>
                                                            </td>
                                                            {{-- <td class="appending-items-data-table">
                                                                <input type="text" class="form-control hide-for-url" name="name" id="name{{$i}}" placeholder="Name">
                                                            </td> --}}
                                                            <td class="appending-items-data-table">
                                                                <input type="text" class="form-control hide-for-url" name="description" id="description-{{$i}}" placeholder="Description" disabled>
                                                            </td>
                                                            <td class="appending-items-data-table">
                                                                <input type="number" class="form-control text-end" min="1" name="quantity" id="quantity-{{$i}}" placeholder="Qty">
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                    <tr>
                                                        <td colspan="4">
                                                            <a href="javascript:;" onclick="addItems()" id="append_item">+ Add more items</a>
                                                            <a class="ms-5 hide-for-url" href="javascript:;" onclick="addUrlItems()" id="append_item">+ Add Url items</a>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div> 
                                    {{-- </div>
                                </div> --}}
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="comments" class="form-label">Comments</label>
                                        <textarea rows="3" name="comments" class="form-control" id="comments" placeholder=""></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--end row-->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save_btn" onclick="saveForm()">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
    var i = 2; //0 included

    function addItems()
    {
        var tableRow = $(`#number-${i}`);
        i++;
        tableRow.closest('tr').after(`<tr class="remove-after">
            <td class="appending-items-data-table">
                <select class="form-select" data-placeholder="Select item.." name="number" id="number-${i}" title="Item Selection"></select>
                <input type="text" class="form-control add-hidden" onclick="openSelect(${i})" readonly name="item" id="item-${i}" placeholder="item" hidden>
            </td>
            <td class="appending-items-data-table">
                <input type="text" class="form-control" name="code" id="code-${i}" placeholder="Code" disabled>
            </td>
            <td class="appending-items-data-table">
                <input type="text" class="form-control" name="description" id="description-${i}" placeholder="Description" disabled>
            </td>
            <td class="appending-items-data-table">
                <input type="number" class="form-control text-end" min="1" name="quantity" id="quantity-${i}" placeholder="Qty">
            </td>
        </tr>`);
        searchSupplyItem(`#number-${i}`, 'add_supply_order_modal', i);
    }

    function addUrlItems()
    {
        var tableRow = $(`#number-${i}`);
        i++;
        tableRow.closest('tr').after(`<tr class="remove-after">
            <td class="appending-items-data-table">
                <div class="d-none"><select class="form-select" data-placeholder="Select item.." name="number" id="number-${i}" title="Item Selection"></select></div>
                <input type="text" class="form-control add-hidden" onclick="openSelect(${i})" readonly name="item" id="item-${i}" placeholder="item" hidden>
                <input type="text" class="form-control supplies-url-holder" name="url" id="url-${i}" placeholder="url">
            </td>
            <td class="appending-items-data-table">
                <input type="text" class="form-control d-none" name="code" id="code-${i}" placeholder="Code" disabled>
            </td>
            <td class="appending-items-data-table">
                <input type="text" class="form-control d-none" name="description" id="description-${i}" placeholder="Description" disabled>
            </td>
            <td class="appending-items-data-table">
                <input type="number" class="form-control text-end" min="1" name="quantity" id="quantity-${i}" placeholder="Qty">
            </td>
        </tr>`);
        searchSupplyItem(`#number-${i}`, 'add_supply_order_modal', i);
    }

    
    function saveForm(){
        menu_store_id = {{request()->id}}
        let fill = [
            'order_number', 'order_date', 'comments', 'wholesaler_id'
        ];
        let data = {
            order: {
                pharmacy_store_id: menu_store_id
            },
            items: {
                number: [], code: [], description: [], quantity: [], item: [], url: []
            }
        };

        $('#add_supply_order_modal input, #add_supply_order_modal textarea, #add_supply_order_modal select').each(function() {
            if(fill.includes(this.id)) {
                data.order[this.id] = this.value;
            } else {
                data.items[this.name].push(this.value);
            }
        });

        console.log(data);
        // return;
        
        sweetAlertLoading();

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/procurement/pharmacy/supply-orders/add`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                $("#save_btn").val('Save');
                $("#save_btn").removeAttr('disabled');
                reloadDataTable();
                if(data.errors){
                    $.each(data.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                        $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                        console.log(key);
                    });
                }
                else{
                    table_supply_order.ajax.reload(null, false);
                    sweetAlert2('success', 'Record has been saved.');
                    $('#add_supply_order_modal').modal('hide');
                }
            },error: function(msg) {
                handleErrorResponse(msg);
                $("#save_btn").val('Save');
                $("#save_btn").removeAttr('disabled');
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }

        });
    }

</script>