<div class="modal" id="edit_clinical_order_modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Edit Clinical Order</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- <form action="" method="POST" id="#clinical_order_edit_form"> --}}
                    @include('stores/procurement/clinicalOrders/partials/edit-form')
                {{-- </form> --}}
            </div>
        </div>
    </div>
</div>

<script> 
    let addMore = 0;
    let clinical_order_id;
    function showEditForm(id,clinics,statuses,items)
    {
        addMore = 0;
        inmar_id = id;
        $(`#edit_clinical_order_modal #clinical_order_item_tbody`).empty();
        $(`#edit_clinical_order_modal #status_id`).empty();
        $(`#edit_clinical_order_modal #clinics`).empty();

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
        console.log('fire-------------',clinics);

        $('#edit_clinical_order_modal #clinic_order_id').val(arr.id);
        $('#edit_clinical_order_modal #order_number').val(arr.order_number);
        $('#edit_clinical_order_modal #tracking_number').val(arr.shipment_tracking_number);
        $('#edit_clinical_order_modal #prescriber_name').val(arr.prescriber_name);
        $('#edit_clinical_order_modal #order_date').val(arr.order_date);
        $('#edit_clinical_order_modal #comments').val(arr.comments);

        if(arr.file_name){
            let filename = arr.file_name;
            if (filename.length > 30) {
                filename = filename.substring(0, 30) + '...';
            }
            $('#edit_clinical_order_modal .file_name').text(filename);
            $("#edit_clinical_order_modal #file_id").val(arr.file_id);
            $("#edit_clinical_order_modal #chip_controller").show();
            $("#edit_clinical_order_modal #file").hide();
            $('#edit_clinical_order_modal .file_name').attr("href", "/admin/file/download/"+arr.file_id+"");
                        
        }
        else{
            $("#edit_clinical_order_modal #chip_controller").hide();
            $("#edit_clinical_order_modal #file").show();
        }

        
        // populateNormalSelect(`#edit_clinical_order_modal #status_id`, '#edit_clinical_order_modal', '/admin/search/store-status', {category: 'procurement_order'}, arr.status_id)
        statuses.forEach(function(status) {
            // Create a new option element and append it to the select element
            if(status.id == arr.shipment_status_id){
                $('#edit_clinical_order_modal #status_id').append($('<option>', {
                    value: status.id,
                    text: status.name,
                    selected: true
                }));
            }
            else{
                $('#edit_clinical_order_modal #status_id').append($('<option>', {
                    value: status.id,
                    text: status.name
                }));
            }
        });

        clinics.forEach(function(clinic) {
            // Create a new option element and append it to the select element
            if(clinic.id == arr.clinic_id){
                $('#edit_clinical_order_modal #clinics').append($('<option>', {
                    value: clinic.id,
                    text: clinic.name,
                    selected: true
                }));
            }
            else{
                $('#edit_clinical_order_modal #clinics').append($('<option>', {
                    value: clinic.id,
                    text: clinic.name
                }));
            }
        });
        
        $.each(items, function(i, item) {
            const k = i+1;
            addMore++;
            //console.log(item);
            $('#edit_clinical_order_modal #clinical_order_item_tbody').append(
                `<tr id="clinical_order_item_tbody_tr_${item.id}">
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
                        <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${k}" onclick="clickUpdateItem(event,${item.id}, ${k})"><i class="fa fa-save"></i></button>
                        <button class="btn btn-sm btn-danger" title="DELETE ITEM #${k}" onclick="clickDeleteItem(event,${item.id}, ${k})"><i class="fa fa-trash-can"></i></button>
                    </td>
                </tr>`
            );
            $(`#edit_clinical_order_modal #med_${item.id}`).append("<option selected value='"+item.drug_id+"'>"+item.drugname+"</option>");
            searchItem(`#med_${item.id}`, 'edit_clinical_order_modal', null, item.id);            
        });

        $('#edit_clinical_order_modal').modal('show');
    }
</script>