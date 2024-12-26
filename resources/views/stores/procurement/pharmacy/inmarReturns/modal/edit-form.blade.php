<div class="modal" id="edit_inmar_return_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Edit INMAR</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- <form action="" method="POST" id="#inmar_return_edit_form"> --}}
                    @include('stores/procurement/pharmacy/inmarReturns/partials/edit-form')
                {{-- </form> --}}
            </div>
        </div>
    </div>
</div>

<script> 
    let addMore = 0;
    let inmar_return_id;
    function showEditForm(id,statuses,medications,wholesalers)
    {
        addMore = 0;
        inmar_id = id;
        $(`#edit_inmar_return_modal #inmar_return_item_tbody`).empty();
        $(`#edit_inmar_return_modal #status_id`).empty();
        $("#edit_inmar_return_modal #wholesaler_id").empty();

        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5', modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true,
   			orientation: "right",
        });

        var btn = document.querySelector(`#inmar-show-btn-${id}`);
        let arr = JSON.parse(btn.dataset.array);
        //console.log('fire-------------',arr);

        var dateTimeString = arr.return_date;
        var datePart = dateTimeString.split(' ')[0];
        $('#edit_inmar_return_modal #inmar_id').val(arr.id);
        $('#edit_inmar_return_modal #name').val(arr.name);
        $('#edit_inmar_return_modal #po_name').val(arr.po_name);
        // $('#edit_inmar_return_modal #wholesaler_name').val(arr.wholesaler_name);
        $('#edit_inmar_return_modal #account_number').val(arr.account_number);
        $('#edit_inmar_return_modal #return_date').val(datePart);
        $('#edit_inmar_return_modal #comments').val(arr.comments);
        if(arr.file_name){
            let filename = arr.file_name;
            if (filename.length > 30) {
                filename = filename.substring(0, 30) + '...';
            }
            $('#edit_inmar_return_modal .file_name').text(filename);
            $("#edit_inmar_return_modal #file_id").val(arr.file_id);
            $("#edit_inmar_return_modal #chip_controller").show();
            $("#edit_inmar_return_modal #file").hide();
            $('#edit_inmar_return_modal .file_name').attr("href", "/admin/file/download/"+arr.file_id+"");
                        
        }
        else{
            $("#edit_inmar_return_modal #chip_controller").hide();
            $("#edit_inmar_return_modal #file").show();
        }
        // populateNormalSelect(`#edit_inmar_return_modal #status_id`, '#edit_inmar_return_modal', '/admin/search/store-status', {category: 'procurement_order'}, arr.status_id)
        statuses.forEach(function(status) {
            // Create a new option element and append it to the select element
            if(status.id == arr.status_id){
                $('#edit_inmar_return_modal #status_id').append($('<option>', {
                    value: status.id,
                    text: status.name,
                    selected: true
                }));
            }
            else{
                $('#edit_inmar_return_modal #status_id').append($('<option>', {
                    value: status.id,
                    text: status.name
                }));
            }
        });

        console.log("wholesalers",wholesalers,"id",arr.wholesaler_id)
        wholesalers.forEach(function(wholesaler) {
            // Create a new option element and append it to the select element
            if(wholesaler.id == arr.wholesaler_id){
                $('#edit_inmar_return_modal #wholesaler_id').append($('<option>', {
                    value: wholesaler.id,
                    text: wholesaler.name,
                    selected: true
                }));
            }
            else{
                $('#edit_inmar_return_modal #wholesaler_id').append($('<option>', {
                    value: wholesaler.id,
                    text: wholesaler.name
                }));
            }
        });
        
        $.each(medications, function(i, item) {
            const k = i+1;
            addMore++;
            //console.log(item);
            $('#edit_inmar_return_modal #inmar_return_item_tbody').append(
                `<tr id="inmar_return_item_tbody_tr_${item.id}">
                    <td><b>#${k}</b></td>
                    <td>    
                        <select class="form-select form-select-sm" data-placeholder="Select item.." name="med[${item.id}]" id="med_${item.id}" title="Select Item"></select>   
                        <input type="text" class="form-control form-control-sm" id="item_${item.id}" name="item[${item.id}]" value="${item.number}" hidden>                                                                                
                    </td>
                    <td>
                        <input type="number" min="0" step="1" class="form-control form-control-sm number_only" id="quantity_${item.id}" name="quantity[${item.id}]" value="${item.quantity}">
                    </td>
                    <td>                                                                                       
                        <input type="text" class="form-control form-control-sm" id="ndc${item.id}" name="ndc[${item.id}]" value="${item.ndc}">
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${k}" onclick="clickUpdateInmarItem(event,${item.id}, ${k})"><i class="fa fa-save"></i></button>
                        <button class="btn btn-sm btn-danger" title="DELETE ITEM #${k}" onclick="clickDeleteInmarItem(event,${item.id}, ${k})"><i class="fa fa-trash-can"></i></button>
                    </td>
                </tr>`
            );
            $(`#edit_inmar_return_modal #med_${item.id}`).append("<option selected value='"+item.drug_id+"'>"+item.drugname+"</option>");
            searchInmarItem(`#med_${item.id}`, 'edit_inmar_return_modal', null, item.id);            
        });

        $('#edit_inmar_return_modal').modal('show');
    }
</script>