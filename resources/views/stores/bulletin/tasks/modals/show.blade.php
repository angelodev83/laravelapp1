<div class="modal" id="show_task_modal" tabindex="-1" style="display:none;">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title">
            <span id="show-task-subject" onclick="clickSubject()"></span>
            <div class="row" id="show-task-subject-div-input" style="display: none;">
                <div class="col-md-12">
                    <input type="text" name="show-subject" id="show-subject" class="form-control" placeholder="Subject" autocomplete="off" style="height: 65px; font-size: 30px; min-width: 800px !important;">
                </div>
            </div>
          </h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
            <div class="form-body">
                <div class="row">
                    {{-- <form action="" method="POST" id="#task_show_form"> --}}
                        <div class="col-lg-12">
                            <div class="row">
                                <!-- start of part 2 (md-6) -->
                                <div class="col-md-6" id="task-relation-coldiv">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body" style="min-height: 400px;">
                                                    <ul class="list-group">
                                                        <li class="list-group-item" id="task-relation-subject">DRUG ORDER DETAILS</li>
                                                    </ul>
                                                    <div class="mt-2 row g-3">
                                                        <div class="col-md-12" id="drug-order-partials" style="display: none">
                                                            @include('stores/bulletin/tasks/drugOrder/partials/edit-form')
                                                        </div>
                                                        <div class="col-md-12" id="supply-order-partials"  style="display: none">
                                                            @include('stores/bulletin/tasks/supplyOrder/partials/edit-form')
                                                        </div>
                                                        <div class="col-md-12" id="inmar-return-partials"  style="display: none">
                                                            @include('stores/bulletin/tasks/inmarReturn/partials/edit-form')
                                                        </div>
                                                        <div class="col-md-12" id="clinical-order-partials"  style="display: none">
                                                            @include('stores/bulletin/tasks/clinicalOrder/partials/edit-form')
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end of part 2 (md-6) -->

                                <!-- start of part 1 (md-6) -->
                                <div class="col-md-12" id="task-content-coldiv">
                                    <div class="scrollable-content">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <input type="text" name="show-id" id="show-id" class="form-control" placeholder="Task ID" autocomplete="off" hidden>
                                                <input type="text" name="show-drug-order-id" id="show-drug-order-id" value="" hidden>
                                                <input type="text" name="show-supply-order-id" id="show-supply-order-id" value="" hidden>
                                                <input type="text" name="show-inmar-return-id" id="show-inmar-return-id" value="" hidden>
                                                <input type="text" name="show-clinical-order-id" id="show-clinical-order-id" value="" hidden>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-7">
                                                <div class="row g-3">
                                                    <div class="col-md-12 d-flex" style="align-items: center">
                                                        <div class="col-md-4">
                                                            <label for="Status" class="form-label"><i class="fa fa-circle me-3"></i>Status:</label>
                                                        </div>
                                                        <div class="col">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="show-status-btn-dropdown"></button>
                                                                <ul class="dropdown-menu" id="show-status-btn-dropdown-ul"></ul>
                                                            </div>
                                                            <input type="text" name="show-status-id" id="show-status-id" hidden>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="row g-3">
                                                    <div class="col-md-12 d-flex" style="align-items: center">
                                                        <div class="col-md-4">
                                                            <label for="Status" class="form-label"><i class="fa fa-flag me-3"></i>Priority:</label>
                                                        </div>
                                                        <div class="col">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="show-priority-status-btn-dropdown"></button>
                                                                <ul class="dropdown-menu" id="show-priority-status-btn-dropdown-ul"></ul>
                                                            </div>
                                                            <input type="text" name="show-priority-status-id" id="show-priority-status-id" hidden>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="row g-3">
                                                    <div class="col-md-12 d-flex" style="align-items: left">
                                                        <div class="col-md-4">
                                                            <label for="Status" class="form-label"><i class="fa fa-user me-3"></i>Assignee:</label>
                                                        </div>
                                                        <div class="col">
                                                            <div class="d-flex" id="show-assigned-to" onclick="clickAssignee()"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="row g-3">
                                                    <div class="col-md-12 d-flex" style="align-items: center">
                                                        <div class="col-md-4">
                                                            <label for="Status" class="form-label"><i class="fa fa-calendar me-3"></i>Due Date:</label>
                                                        </div>
                                                        <div class="col">
                                                            <div class="input-group"> <span class="input-group-text" id="icon-due-date"><i class="fa fa-calendar"></i></span>
                                                                <input type="text" class="form-control form-control-sm" id="due_date" name="due_date" placeholder="yyyy-mm-dd" autocomplete="off" aria-describedby="icon-due-date">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="row g-3">
                                                    <div class="col-md-12 d-flex" style="align-items: left">
                                                        <div class="col-md-4">
                                                            <label for="tags" class="form-label"><i class="fa fa-file-pdf me-3"></i>File Tags:</label>
                                                        </div>
                                                        <div class="col">
                                                            <div id="show-document-tags"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="row g-3">
                                                    <div class="col-md-12 d-flex" style="align-items: center">
                                                        <div class="col-md-4">
                                                            <label for="tags" class="form-label"><i class="fa fa-eye me-3"></i>Watchers:</label>
                                                        </div>
                                                        <div class="col">
                                                            <div id="task_watchers"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2 row g-3">
                                            <div class="col-md-12">
                                                <textarea class="form-control tinymce-content" name="show-description" id="show-description" rows="15" placeholder="Description"></textarea>
                                            </div>
                                        </div>
                                        <div class="mt-2 row g-3">
                                            <div class="col-md-7">
                                                <label for="edocuments" class="form-label">Attachments</label>
                                                <small class="attachment-label-color ms-2">Only accepts maximum size of 100 MB per file</small>
                                                <!-- <input id="show-documents" class="imageuploadify-file-general-class" name="show-documents[]" type="file" accept="*" multiple> -->
                                                <div id="for-file"></div>
                                            </div>
                                            <div class="col-md-5">
                                                <!-- task reminders starts -->
                                                <div class="m-1 col-md-12 d-flex">
                                                    <div class="card radius-10 w-100 ">
                                                        <div id="bulletin-tasks-recent" class="p-3 pt-2 mb-3 customers-list" style="height: 300px !important;">                                                
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- task reminders ends -->
            
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label for="show-task-comment" class="form-label">Comment</label>
                                                <textarea name="show-task-comment" row="4" id="show-task-comment" class="form-control" placeholder="Subject"> </textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end of part 1 (md-6) -->
                            </div>
                            

                        </div>
                    {{-- </form> --}}
                </div><!--end row-->
            </div>
  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          @can('menu_store.bulletin.task_reminders.update')
            <button type="button" class="btn btn-primary" onclick="clickSubmitBtnShowModal()">SAVE</button>
          @endcan
        </div>
      </div>
    </div>
</div>

<style>
    .custom-modal-fullsize {
        max-width: 97%;
        width: 97%;
    }

    .scrollable-content {
        width: 100%; /* Adjust width as needed */
        height: 100%; /* Adjust height as needed */
        overflow-y: auto;
        /* border: 1px solid #ccc; */
        padding: 10px;
    }

    .task-attachment-list-name {
        width: 250px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    #show-task-subject,
    #show-assigned-to {
        cursor: pointer;
    }
    #show-task-subject:hover,
    #show-assigned-to:hover {
        /* background-color: rgb(217, 207, 207); */
        color: red;
    }

    /* #show-status-btn-dropdown,
    #show-priority-status-btn-dropdown {
        min-width: 140px !important;
    } */

    .customers-contacts a {
        font-size: 14px;
        width: 27px;
        height: 27px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        border: 1px solid #eeecec;
        text-align: center;
        border-radius: 50%;
        color: #2b2a2a;
        margin: 3px;
    }

    .drug_order_number_dt_row {
        cursor: pointer;
    }
    .drug_order_number_dt_row:hover {
        color:#8833ff !important;
    }

.table-container {
  height: 390px; /* Fixed height */
  overflow-y: auto; /* Enable vertical scrolling */
}

/* .table-container table tbody tr:hover {
    background-color: #c7c7c7 !important;
} */

.table-container .table thead th {
  position: sticky;
  top: 0;
  z-index: 1;
  background-color: #c7c7c7; /* Adjust as needed */
}

.table-container .table tfoot th {
  position: sticky;
  bottom: 0;
  z-index: 1;
  background-color: #c7c7c7; /* Adjust as needed */
}

.table-container .table-hover>tbody>tr:hover>* {
    --bs-table-color-state: var(--bs-table-hover-color);
    --bs-table-bg-state: #dad9d9;;
}
</style>

<script>
    let deletedDocumentsArr = [];
    let addMore = 0;
    let drug_order_id;
    let supply_order_id;
    let inmar_return_id;
    let clinical_order_id;

    let disableUpdateActBtn = '';
    let disableUpdateBtn = 'disabled';
    let disableDeleteBtn = 'disabled';

    function showViewModal(id){
        resetInputs();
        $('.imageuploadify-container').remove();
        var btn = document.querySelector(`#task-edit-btn-${id}`);
        let data = btn.dataset;
        let statusArr = JSON.parse(data.statusArray);
        let drugOrderArr = JSON.parse(data.drugOrderArray);
        let supplyOrderArr = JSON.parse(data.supplyOrderArray);
        let inmarArr = JSON.parse(data.inmarArray);
        let clinicalOrderArr = JSON.parse(data.clinicalOrderArray);
        let documentsArr = JSON.parse(data.documentsArray);
        let statusesArr = JSON.parse(data.statusesArray);
        let priorityStatusArr = JSON.parse(data.priorityStatusArray);
        let tagsArr = JSON.parse(data.tagsArray);
        let watcher_list = data.watcher_list;

        $('#show_task_modal #task_watchers').html(watcher_list);

        console.log("dataset",data)
        console.log("drugOrderArr",drugOrderArr)
        console.log("supplyOrderArr",supplyOrderArr)
        console.log("inmarArr",inmarArr)
        console.log("clinicalOrderArr",clinicalOrderArr)
        console.log("documentsArr",documentsArr)
        console.log("statusesArr",statusesArr)
        console.log("id",data['id'])

        let hasOneDrugOrder = Object.keys(drugOrderArr).length > 0 ? true : false;
        let hasOneSupplyOrder = Object.keys(supplyOrderArr).length > 0 ? true : false;
        let hasOneInmarReturn = Object.keys(inmarArr).length > 0 ? true : false;
        let hasOneClinicalOrder = Object.keys(clinicalOrderArr).length > 0 ? true : false;
        let hasNoRelation = true;
        // let hasOneSupplyOrder = Object.keys(supplyOrderArr).length > 0 ? true : false;
        if(hasOneDrugOrder === true || hasOneSupplyOrder === true || hasOneInmarReturn === true || hasOneClinicalOrder === true) {
            hasNoRelation = false;
        }

        $('#drug-order-partials').css('display', 'none');
        $('#supply-order-partials').css('display', 'none');
        $('#inmar-return-partials').css('display', 'none');
        $('#clinical-order-partials').css('display', 'none');

        let statuses = [];
        if(hasNoRelation === true)
        {
            $('#task-relation-coldiv').css('display', 'none');
            $('#task-content-coldiv').removeClass('col-md-6').addClass('col-md-12');
        } else {
            $('#task-relation-coldiv').css('display', 'block');
            $('#task-content-coldiv').removeClass('col-md-12').addClass('col-md-6');
        }
        

        let task_relation_subject = '';

        $('#show-task-subject').html(data['subject'] + '<i class="fa fa-edit ms-5"></i>')
        $('#show-task-subject-div-input').val(data['subject'])
        tinymce.get("show-description").setContent(decodeHtmlEntities(data['description']));
        $('#show-id').val(data['id'])
        $('#show-status-id').val(data['status_id'])
        $('#show-subject').val(data['subject'])
        $('#show-priority-status-id').val(data['priority_status_id'])

        $('#show-status-btn-dropdown').removeClass('btn-warning btn-danger btn-success btn-secondary btn-info btn-primary btn-default btn-dark btn-info2');
        $('#show-priority-status-btn-dropdown').removeClass('btn-warning btn-danger btn-success btn-secondary btn-info btn-primary btn-default btn-dark btn-info2');
        // alert("removed btn-info2")

        $('#show-status-btn-dropdown').addClass(`btn-${statusArr['class']}`).html(`<i class="fa fa-circle me-3"></i>${statusArr['name']}`);

        $('#show_task_modal #due_date').val(data['due_date']);

        let statusDropdown = '';
        Object.keys(statusesArr).forEach(key => {
            let value = statusesArr[key];

            let status_name = `<span>${value['name']}</span>`;
            if(statusArr['id'] == key)
            {
                status_name = `<span><b>${value['name']}</b></span>
                    <span class="ms-auto ps-5 text-success"><i class="fa fa-check-double"></i></span>
                `;
                $('#task-relation-subject').css('background-color', `${value['color']}`);
                $('#task-relation-subject').css('color', `${value['text_color']}`);
                task_relation_subject += value['name'];
            }

            statusDropdown += `
                <li>
                    <a class="dropdown-item" href="javascript:;" onclick="changeTaskSelectedStatus(${data['id']}
                        , ${value['id']}
                    )">
                        <div class="task-container d-flex">
                            <div class="task-circle-container me-3" style="border: 1px solid ${value['color']};">
                                <div class="task-circle" style="background-color: ${value['color']};">
                                </div>
                            </div>
                            ${status_name}
                        </div>
                    </a>
                </li>
            `;
        });
        $('#show-status-btn-dropdown-ul').html(statusDropdown);

        let tags = '';
        Object.keys(tagsArr).forEach(key => {
            let value = tagsArr[key];
            tags += `<button class="px-3 btn btn-sm btn-outline-secondary radius-15 me-3">${value.name}</button>`;
        });
        $('#show-document-tags').html(tags);

        let emp = '';
        let selectEmp = `
            <select class="form-select" data-placeholder="Select Employee.." name="show_assign_to_select" id="show_assign_to_select" title="Select Employee Name">
                <option value="${data['assigned_to_employee_id']}" selected>${data['assigned_to']}</option>
            </select>
        `;
        if(data['assigned_to_image'] != '' && data['assigned_to_image'] != null) {
            emp = `
                <div class="d-flex">
                    <img src="/upload/userprofile/${data['assigned_to_image']}" width="35" height="35" class="shadow rounded-circle" alt="">
                    <div class="mt-2 flex-grow-1 ms-3">
                        <p id="show-assign-to-fullname" class="mb-0 font-weight-bold">${data['assigned_to']}<i id="assignee-edit-me" class="fa fa-edit ms-3"></i></p>
                        <div id="show_assign_to_select_div" style="min-width: 200px; display: none;">
                            ${selectEmp}
                        </div>
                    </div>
                </div>
            `;
        } else {
            emp = `
                <div class="employee-avatar-${data['assigned_to_initials_random_color']}-initials hr-employee ms-3">
                    ${data['assigned_to_initials']}
                </div>
                <p id="show-assign-to-fullname" class="mt-2 mb-0 font-weight-bold ms-3">${data['assigned_to']}<i id="assignee-edit-me" class="fa fa-edit ms-3"></i></p>
                <div id="show_assign_to_select_div"  style="min-width: 200px; display:none;">
                    ${selectEmp}
                </div>
            `;
        }
        $('#show-assigned-to').html(emp);
        // $('#show_assign_to_select_div').css('display', 'none');
        searchSelect2Api('show_assign_to_select', '#show_task_modal','/admin/search/user-employee');

        let priorityStatusDropdown = '';
        Object.keys(priorityStatusArr).forEach(key => {
            let value = priorityStatusArr[key];

            let status_name = `<span>${value['name']}</span>`;
            if(data['priority_status_id'] == key)
            {
                const priority = `<i class="fa fa-flag me-3"></i>${value['name']}`;
                $('#show-priority-status-btn-dropdown').addClass(`btn-${value['class']}`).html(priority);
                status_name = `<span><b>${value['name']}</b></span>
                    <span class="ms-auto ps-5 text-success"><i class="fa fa-check-double"></i></span>
                `;
            }

            priorityStatusDropdown += `
                <li>
                    <a class="dropdown-item" href="javascript:;" onclick="changeTaskSelectedPriorityStatus(${data['id']},${value['id']})">
                        <spn>
                            <i class="fa fa-flag text-${value['class']} me-3"></i>${status_name}
                        </span>
                    </a>
                </li>
            `;
        });
        $('#show-priority-status-btn-dropdown-ul').html(priorityStatusDropdown);


        loadTaskDocuments(documentsArr);


        // TASK RELATION HAS ONE - DRUG ORDER
        if(hasOneDrugOrder === true) {
            @can('menu_store.procurement.pharmacy.drug_orders.update')
                disableUpdateBtn = '';
            @endcan
            @can('menu_store.procurement.pharmacy.drug_orders.delete')
                disableDeleteBtn = '';
            @endcan

            $('#drug-order-partials').css('display', 'block');
            $('#show-order-number').val(drugOrderArr['order_number'])
            $('#show-account-number').val(drugOrderArr['account_number'])
            $('#show-po-name').val(drugOrderArr['po_name'])
            $('#show-wholesaler-name').val(drugOrderArr['wholesaler_name'])
            $('#show-do-comment').val(drugOrderArr['comments'])
            
            $('#show-drug-order-id').val(drugOrderArr['id']);
            $('#task-relation-subject').html('DRUG ORDER - <b>'+task_relation_subject+'</b>');
            $('#show-drug-order-number').html(drugOrderArr['order_number'])
            $('#show-drug-order-date').html(drugOrderArr['order_date'])
            $('#show-drug-order-comment').html(drugOrderArr['comments'])

            addMore = 0;
            drug_order_id = drugOrderArr['id'];
            $(`#show_task_modal #drug_order_item_tbody`).empty();
            $(`#show_task_modal #status_id`).css('display', 'none');

            $(`#show_task_modal #drug-order-partials #wholesaler_id`).empty();

            $('#show_task_modal #order_date').datepicker({
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

            let arr = drugOrderArr;
            // console.log('fire-------------',arr);

            $('#show_task_modal #po_name').val(arr.order_number);
            $('#show_task_modal #po_number').val(arr.order_number);
            $('#show_task_modal #order_date').val(arr.order_date);
            $('#show_task_modal #account_number').val(arr.account_number);
            $('#show_task_modal #wholesaler_name').val(arr.wholesaler_name);
            $('#show_task_modal #created_at').val(formatDateTime(arr.created_at));
            $('#show_task_modal #comments').val(arr.comments);
            $('#show_task_modal #po_memo').val(arr.po_memo);

            if(arr.file){
                let filename = arr.file.filename;
                if (filename.length > 30) {
                    filename = filename.substring(0, 30) + '...';
                }
                $('#show_task_modal #drug-order-partials .file_name').text(filename);
                $("#show_task_modal #drug-order-partials #file_id").val(arr.file_id);
                $("#show_task_modal #drug-order-partials #chip_controller").show();
                $("#show_task_modal #drug-order-partials #file").hide();
                $('#show_task_modal #drug-order-partials .file_name').attr("href", "/store/procurement/pharmacy/"+menu_store_id+"/drug-orders/download/"+arr.file_id+"");
            }
            else{
                $("#show_task_modal #drug-order-partials #chip_controller").hide();
                $("#show_task_modal #drug-order-partials #file").show();
            }
            populateNormalSelect(`#show_task_modal #drug-order-partials #wholesaler_id`, '#show_task_modal', '/admin/search/wholesaler', {category: 'procurement'}, arr.wholesaler_id)

            $.each(arr.items_imported, function(i, item) {
                const k = i+1;
                addMore++;
                $('#show_task_modal #drug_order_item_tbody').append(
                    `<tr id="drug_order_item_tbody_tr_${item.id}">
                        <td><b>#${k}</b></td>
                        <td width="40%">                                                                                       
                            <input type="text" class="form-control form-control-sm" id="product_description_${item.id}" name="product_description[${item.id}]" value="${item.product_description}" ${disableUpdateBtn}>
                        </td>
                        <td>
                            <input type="number" min="0" step="1" class="form-control form-control-sm" id="quantity_ordered_${item.id}" name="quantity_ordered[${item.id}]" value="${item.quantity_ordered}" ${disableUpdateBtn}>
                        </td>
                        <td> 
                            <input type="number" min="0" step="1" class="form-control form-control-sm" id="quantity_confirmed_${item.id}" name="quantity_confirmed[${item.id}]" value="${item.quantity_confirmed}" ${disableUpdateBtn}>
                        </td>
                        <td> 
                            <input type="number" min="0" class="form-control form-control-sm text-end" id="acq_cost_${item.id}" name="acq_cost[${item.id}]" value="${item.acq_cost}" ${disableUpdateBtn}>
                        </td>
                        <td width="17%"> 
                            <input type="text" class="form-control form-control-sm" id="ndc_${item.id}" name="ndc[${item.id}]" value="${item.ndc}" ${disableUpdateBtn}>
                        </td>
                        <td width="8%">
                            <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${k}" onclick="clickUpdateDrugOrderItem(${item.id}, ${k})" ${disableUpdateBtn}><i class="fa fa-save"></i></button>
                            <button class="btn btn-sm btn-danger" title="DELETE ITEM #${k}" onclick="clickDeleteDrugOrderItem(${item.id}, ${k})" ${disableDeleteBtn}><i class="fa fa-trash-can"></i></button>
                        </td>
                    </tr>`
                );
            });

        }

        // TASK RELATION HAS ONE - SUPPLY ORDER
        if(hasOneSupplyOrder === true) {
            disableUpdateBtn = 'disabled';
            disableDeleteBtn = 'disabled';
            @can('menu_store.procurement.pharmacy.supplies_orders.update')
                disableUpdateBtn = '';
            @endcan
            @can('menu_store.procurement.pharmacy.supplies_orders.delete')
                disableDeleteBtn = '';
            @endcan
            $('#supply-order-partials').css('display', 'block');
            $('#show-supply-order-id').val(supplyOrderArr['id']);
            $('#task-relation-subject').html('SUPPY ORDER - <b>'+task_relation_subject+'</b>');

            addMore = 0;
            supply_order_id = supplyOrderArr['id'];
            $(`#show_task_modal #supply_order_item_tbody`).empty();
            $(`#show_task_modal #status_id`).css('display', 'none');

            $(`#show_task_modal #supply-order-partials #wholesaler_id`).empty();

            $('#show_task_modal #supply-order-partials #order_date').datepicker({
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

            let arr = supplyOrderArr;
            // console.log('fire-------------',arr);

            $('#show_task_modal #supply-order-partials #order_number').val(arr.order_number);
            $('#show_task_modal #supply-order-partials #created_at').val(formatDateTime(arr.created_at));
            $('#show_task_modal #supply-order-partials #comments').val(arr.comments);
            $('#show_task_modal #supply-order-partials #order_date').val(arr.order_date);

            if(arr.file){
                let filename = arr.file.filename;
                if (filename.length > 30) {
                    filename = filename.substring(0, 30) + '...';
                }
                $('#show_task_modal #supply-order-partials .file_name').text(filename);
                $("#show_task_modal #supply-order-partials #file_id").val(arr.file_id);
                $("#show_task_modal #supply-order-partials #chip_controller").show();
                $("#show_task_modal #supply-order-partials #file").hide();
                $('#show_task_modal #supply-order-partials .file_name').attr("href", "/store/procurement/pharmacy/"+menu_store_id+"/supply-orders/download/"+arr.file_id+"");
            }
            else{
                $("#show_task_modal #supply-order-partials #chip_controller").hide();
                $("#show_task_modal #supply-order-partials #file").show();
            }
            populateNormalSelect(`#show_task_modal #supply-order-partials #wholesaler_id`, '#show_task_modal', '/admin/search/wholesaler', {category: 'supply'}, arr.wholesaler_id)

            $.each(arr.items, function(i, item) {
                const k = i+1;
                addMore++;
                $('#show_task_modal #supply_order_item_tbody').append(
                    `<tr id="supply_order_item_tbody_tr_${item.id}">
                        <td><b>#${k}</b></td>
                        <td>    
                            <select class="form-select form-select-sm" data-placeholder="Select item.." name="number[${item.id}]" id="number_${item.id}" title="Select Item" ${disableUpdateBtn}></select>   
                            <input type="text" class="form-control form-control-sm" id="item_${item.id}" name="item[${item.id}]" value="${item.number}" hidden>                                                                                
                        </td>
                        <td>                                                                                       
                            <input type="text" class="form-control form-control-sm" id="code_${item.id}" name="code[${item.id}]" value="${item.code}" disabled>
                        </td>
                        <td>                                                                                       
                            <input type="text" class="form-control form-control-sm" id="description_${item.id}" name="description[${item.id}]" value="${item.description}" disabled>
                        </td>
                        <td>
                            <input type="number" min="0" step="1" class="form-control form-control-sm" id="quantity_${item.id}" name="quantity[${item.id}]" value="${item.quantity}" ${disableUpdateBtn}>
                        </td>
                        <td> 
                            <input type="number" min="0" step="1" class="form-control form-control-sm" id="actual_quantity_${item.id}" name="actual_quantity[${item.id}]" value="${item.actual_quantity}" ${disableUpdateBtn}>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${k}" onclick="clickUpdateSupplyOrderItem(event,${item.id}, ${k})" ${disableUpdateBtn}><i class="fa fa-save"></i></button>
                            <button class="btn btn-sm btn-danger" title="DELETE ITEM #${k}" onclick="clickDeleteSupplyOrderItem(event,${item.id}, ${k})" ${disableDeleteBtn}><i class="fa fa-trash-can"></i></button>
                        </td>
                    </tr>`
                );
                $(`#number_${item.id}`).append("<option selected value='"+item.id+"'>"+item.number+"</option>");
                searchSupplyItem(`#show_task_modal #number_${item.id}`, 'show_task_modal', null, item.id);
            });
        }

        // TASK RELATION HAS ONE - INMAR
        if(hasOneInmarReturn === true) {
            disableUpdateBtn = 'disabled';
            disableDeleteBtn = 'disabled';
            @can('menu_store.procurement.pharmacy.inmar_returns.update')
                disableUpdateBtn = '';
            @endcan
            @can('menu_store.procurement.pharmacy.inmar_returns.delete')
                disableDeleteBtn = '';
            @endcan
            $('#inmar-return-partials').css('display', 'block');
            $('#show-inmar-return-id').val(inmarArr['id']);
            $('#task-relation-subject').html('INMAR RETURN - <b>'+task_relation_subject+'</b>');

            addMore = 0;
            inmar_return_id = inmarArr['id'];
            $(`#show_task_modal #inmar_item_tbody`).empty();
            $(`#show_task_modal #inmar-return-partials #inmar_return_wholesaler_id`).empty();
            $(`#show_task_modal #status_id`).css('display', 'none');

            let arr = inmarArr;
            // console.log('fire-------------',arr);

            $('#show_task_modal #inmar_return_name').val(arr.name);
            // $('#show_task_modal #created_at').val(formatDateTime(arr.created_at));
            $('#show_task_modal #inmar_return_comments').val(arr.comments);

            $('#show_task_modal #inmar_return_account_number').val(arr.account_number);
            $('#show_task_modal #inmar_return_po_name').val(arr.po_name);
            $('#show_task_modal #inmar_return_wholesaler_name').val(arr.wholesaler_name);

            $('#show_task_modal #inmar_return_return_date').val(arr.return_date);

            $('#show_task_modal #inmar_return_return_date').datepicker({
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

            populateNormalSelect(`#show_task_modal #inmar-return-partials #inmar_return_wholesaler_id`, '#show_task_modal', '/admin/search/wholesaler', {category: 'procurement'}, arr.wholesaler_id)

            if(arr.file){
                // console.log(arr.file);
                let filename = arr.file.filename;
                if (filename.length > 30) {
                    filename = filename.substring(0, 30) + '...';
                }
                $('#show_task_modal #inmar-return-partials .file_name').text(filename);
                $("#show_task_modal #inmar-return-partials #file_id").val(arr.file_id);
                $("#show_task_modal #inmar-return-partials #chip_controller").show();
                $("#show_task_modal #inmar-return-partials #file").hide();
                $('#show_task_modal #inmar-return-partials .file_name').attr("href", "/store/procurement/pharmacy/"+menu_store_id+"/inmars/download/"+arr.file_id+"");
                            
            }
            else{
                $("#show_task_modal #inmar-return-partials #chip_controller").hide();
                $("#show_task_modal #inmar-return-partials #file").show();
            }

            $.each(arr.items, function(i, item) {
                const k = i+1;
                addMore++;
                $('#show_task_modal #inmar_item_tbody').append(
                    `<tr id="item_tbody_tr_${item.id}">
                        <td><b>#${k}</b></td>
                        <td>
                            <input type="text" id="id_${item.id}" value="new" hidden>

                            <select class="form-select form-select-sm" data-placeholder="Select item.." id="med_${item.id}" onchange="doSelectItem(this.id, ${item.id})"  ${disableUpdateBtn}></select>

                            <input type="text" class="form-control form-control-sm" id="item_${item.id}" name="item[${item.id}]" hidden> 
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm" id="quantity_${item.id}" name="quantity[${item.id}]" value="${item.quantity}"  ${disableUpdateBtn}>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" id="ndc_${item.id}" name="ndc[${item.id}]" value="${item.ndc}"  ${disableUpdateBtn}>
                        </td>

                        <td>
                            <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${k}" onclick="clickUpdateInmarItem(event,${item.id}, ${k})" ${disableUpdateBtn}><i class="fa fa-save"></i></button>
                            <button class="btn btn-sm btn-danger" title="DELETE ITEM #${k}" onclick="clickDeleteInmarItem(event,${item.id}, ${k})" ${disableDeleteBtn}><i class="fa fa-trash-can"></i></button>
                        </td>
                    </tr>`
                );
                $(`#show_task_modal #med_${item.id}`).append("<option selected value='"+item.drug_id+"'>"+item.drugname+"</option>");
                searchSelect2ApiDrug(`#show_task_modal #med_${item.id}`, 'show_task_modal', null, item.id);
            });
        }

        // TASK RELATION HAS ONE - CLINICAL ORDER
        if(hasOneClinicalOrder === true) {
            disableUpdateBtn = 'disabled';
            disableDeleteBtn = 'disabled';
            @can('menu_store.procurement.clinical_orders.update')
                disableUpdateBtn = '';
            @endcan
            @can('menu_store.procurement.clinical_orders.delete')
                disableDeleteBtn = '';
            @endcan

            $('#clinical-order-partials').css('display', 'block');
           
            $('#show-clinical-order-id').val(clinicalOrderArr['id']);
            $('#task-relation-subject').html('CLINICAL ORDER - <b>'+task_relation_subject+'</b>');

            addMore = 0;
            clinical_order_id = clinicalOrderArr['id'];
            $(`#show_task_modal #clinical-order-partials #clinics`).empty();
            $(`#show_task_modal #clinical-order-partials #clinical_order_item_tbody`).empty();
            $(`#show_task_modal #clinical-order-partials #status_id`).css('display', 'none');

            $('#show_task_modal #clinical-order-partials #order_date').datepicker({
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

            let arr = clinicalOrderArr;
            // console.log('fire-------------',arr);

            $('#show_task_modal #clinical-order-partials #clinic_order_id').val(arr.id);
            $('#show_task_modal #clinical-order-partials #order_number').val(arr.order_number);
            $('#show_task_modal #clinical-order-partials #tracking_number').val(arr.shipment_tracking_number);
            $('#show_task_modal #clinical-order-partials #prescriber_name').val(arr.prescriber_name);
            $('#show_task_modal #clinical-order-partials #order_date').val(arr.order_date);
            $('#show_task_modal #clinical-order-partials #comments').val(arr.comments);

            populateNormalSelect(`#show_task_modal #clinical-order-partials #clinics`, '#show_task_modal #clinical-order-partials', '/admin/search/clinic', {}, arr.clinic_id)

            if(arr.file){
                // console.log(arr.file);
                let filename = arr.file.filename;
                if (filename.length > 30) {
                    filename = filename.substring(0, 30) + '...';
                }
                $('#show_task_modal #clinical-order-partials .file_name').text(filename);
                $("#show_task_modal #clinical-order-partials #file_id").val(arr.file_id);
                $("#show_task_modal #clinical-order-partials #chip_controller").show();
                $("#show_task_modal #clinical-order-partials #file").hide();
                $('#show_task_modal #clinical-order-partials .file_name').attr("href", "/store/procurement/pharmacy/"+menu_store_id+"/clinical-orders/download/"+arr.file_id+"");
                            
            }
            else{
                $("#show_task_modal #clinical-order-partials #chip_controller").hide();
                $("#show_task_modal #clinical-order-partials #file").show();
            }


            $.each(arr.items, function(i, item) {
                const k = i+1;
                addMore++;
                $('#show_task_modal #clinical-order-partials #clinical_order_item_tbody').append(
                    `<tr id="clinical_order_item_tbody_tr_${item.id}">
                        <td><b>#${k}</b></td>
                        <td>    
                            <select class="form-select form-select-sm" data-placeholder="Select item.." name="med[${item.id}]" id="med_${item.id}" title="Select Item"  ${disableUpdateBtn}></select>   
                            <input type="text" class="form-control form-control-sm" id="item_${item.id}" name="item[${item.id}]" value="${item.number}" hidden>                                                                                
                        </td>
                        <td>
                            <input type="number" min="0" step="1" class="form-control form-control-sm number_only" id="quantity_${item.id}" name="quantity[${item.id}]" value="${item.quantity}"  ${disableUpdateBtn}>
                        </td>
                        <td>                                                                                       
                            <input type="text" class="form-control form-control-sm" id="ndc${item.id}" name="ndc[${item.id}]" value="${item.ndc}"  ${disableUpdateBtn}>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1" title="SAVE ITEM #${k}" onclick="clickUpdateClinicalOrderItem(event,${item.id}, ${k})"  ${disableUpdateBtn}><i class="fa fa-save"></i></button>
                            <button class="btn btn-sm btn-danger" title="DELETE ITEM #${k}" onclick="clickDeleteClinicalOrderItem(event,${item.id}, ${k})"  ${disableDeleteBtn}><i class="fa fa-trash-can"></i></button>
                        </td>
                    </tr>`
                );
                $(`#show_task_modal #clinical-order-partials #med_${item.id}`).append("<option selected value='"+item.drug_id+"'>"+item.drugname+"</option>");
                searchItem(`#med_${item.id}`, 'show_task_modal', null, item.id);   
            });

        }

        let fileInput = $('<input/>', {
            id: 'show-documents',
            class: 'imageuploadify-file-general-class',
            name: 'show-documents[]',
            type: 'file',
            accept: '*',
            multiple: ''
        });
        $('#show_task_modal #for-file').html(fileInput); 
        $('#show_task_modal #show-documents').imageuploadify();
        
        $("#show_task_modal .imageuploadify-container").remove();
        $('#show_task_modal .imageuploadify-message').html('Drag&Drop Your File(s) Here To Upload');     
        
        $('#show_task_modal').modal('show');
    }

    function changeTaskSelectedStatus(task_id, status_id)
    {
        var btn = document.querySelector(`#task-edit-btn-${task_id}`);
        let data = btn.dataset;
        let statusesArr = JSON.parse(data.statusesArray);
        let selected = statusesArr[parseInt(status_id)];
        console.log("----------------",selected)
        $('#show-status-id').val(status_id);

        $('#show-status-btn-dropdown')
            .removeClass('btn-warning btn-danger btn-success btn-secondary btn-info btn-primary btn-default btn-dark btn-info2')
            .addClass(`btn btn-sm dropdown-toggle btn-${selected['class']}`)
            .html(`<i class="fa fa-circle me-3"></i>${selected['name']}`);
    }

    function changeTaskSelectedPriorityStatus(task_id, status_id)
    {
        var btn = document.querySelector(`#task-edit-btn-${task_id}`);
        let data = btn.dataset;
        let priorityStatusArr = JSON.parse(data.priorityStatusArray);
        let selected = priorityStatusArr[parseInt(status_id)];
        console.log("----------------",selected)
        $('#show-priority-status-id').val(status_id);
        
        $('#show-priority-status-btn-dropdown')
            .removeClass('btn-warning btn-danger btn-success btn-secondary btn-info btn-primary btn-info2')
            .addClass(`btn btn-sm dropdown-toggle btn-${selected['class']}`)
            .html(`<i class="fa fa-flag me-3"></i>${selected['name']}`);  
    }


    function deleteTaskSelectedDocument(id)
    {
        const url = "/store/bulletin/task-documents/delete";
        const parent_id = $('#show-id').val();
        clickDeleteStoreDocumentBtn(id, url, parent_id) 
    }

    function emitDeleteStoreDocumentFunction(id, parent_id, msg)
    {
        deletedDocumentsArr.push(parseInt(id));
        var btn = document.querySelector(`#task-edit-btn-${parent_id}`);
        let data = btn.dataset;
        let documentsArr = JSON.parse(data.documentsArray);
        loadTaskDocuments(documentsArr);
    }

    function loadTaskDocuments(documentsArr) {
        let documents_li = ``;
        Object.keys(documentsArr).forEach(key => {
            
            let value = documentsArr[key];

            if(!deletedDocumentsArr.includes(value['id'])) {
    
                var parts = value['path'].split('/');
                var lastPart = parts[parts.length - 1]; // Index -2 gets the last part after the last slash

                var fileIcon = fileUtil(value['ext'], 'icon');
                var fileClass = fileUtil(value['ext'], 'class');

                let created_at = formatDateTime(value['created_at']);

                documents_li += `
                    <div class="p-1 cursor-pointer customers-list-item d-flex align-items-center border-top border-bottom">
                        <div class="ms-1" title="${lastPart}">
                            <h6 class="mb-1 font-14 task-attachment-list-name">
                                <div class="d-flex align-items-center">
                                    <div><i class="bx ${fileIcon} me-2 font-24 ${fileClass}"></i></div>
                                    <div class="font-weight-bold" >${lastPart}</div>
                                </div>
                            </h6>
                            <p class="mb-0 font-13 text-secondary bulletin-announcement-text-truncate">
                                <small class="${fileClass} me-3">${value['ext']}</small> <small>${created_at}</small>
                            </p>
                        </div>
                        <div class="list-inline d-flex customers-contacts ms-auto">
                            <small class="float-end text-end w-100 bulletin-announcement-time-ago">
                                <a target="_new" href="${value['path']}" title="Download" class="list-inline-item" style="background-color: gray; color: white;"><i class='fa-solid fa-download'></i></a>
                                <a href="javascript: deleteTaskSelectedDocument(${value['id']});" title="Delete" class="list-inline-item" style="background-color: red;"><i class='fa fa-trash-can text-light'></i></a>
                            </small>
                        </div>
                    </div>
              
                `;

            }

        });
        $('#bulletin-tasks-recent').html(documents_li);
    }

    function clickSubmitBtnShowModal()
    {
        const id = $('#show-id').val();
        var btn = document.querySelector(`#task-edit-btn-${id}`);
        let tagsArr = JSON.parse(btn.dataset.tagsArray);
        let documentsArr = JSON.parse(btn.dataset.documentsArray);

        let data = {
            id: $('#show-id').val(),
            subject:  $('#show-subject').val(),
            status_id: $('#show-status-id').val(),
            priority_status_id: $('#show-priority-status-id').val(),
            assigned_to_employee_id: $('#show_assign_to_select').find(":selected").val(),
            due_date: $('#show_task_modal #due_date').val(),
            description: tinymce.get("show-description").getContent(),
            drugOrder: {},
            supplyOrder: {},
            inmarReturn: {},
            clinicalOrder: {},
        };

        // has one drug order
        if( $('#show-drug-order-id').val() != "" &&  $('#show-drug-order-id').val() != undefined)
        {
            data.drugOrder = {
                id: $('#show-drug-order-id').val(),
                status_id: $('#show-status-id').val()
            }
        }
        
        // has one supply order
        if( $('#show-supply-order-id').val() != "" &&  $('#show-supply-order-id').val() != undefined)
        {
            data.supplyOrder = {
                id: $('#show-supply-order-id').val(),
                status_id: $('#show-status-id').val(),
            }
        }

        // has one inmar return
        if( $('#show-inmar-return-id').val() != "" &&  $('#show-inmar-return-id').val() != undefined)
        {
            data.inmarReturn = {
                id: $('#show-inmar-return-id').val(),
                status_id: $('#show-status-id').val(),
            }
        }

        // has one clinical order
        if( $('#show-clinical-order-id').val() != "" &&  $('#show-clinical-order-id').val() != undefined)
        {
            data.clinicalOrder = {
                id: $('#show-clinical-order-id').val(),
                status_id: $('#show-status-id').val(),
            }
        }

        var formData = new FormData();
        var uploadFiles = $('#show-documents').get(0).files;
        
        for (let i = 0; i < uploadFiles.length; i++) {
            formData.append("files[]", uploadFiles[i]);
            var kbSize = uploadFiles[i].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }
        formData.append("data", JSON.stringify(data));    
        console.log("-------saving",data,formData);
        
        if(uploadFiles.length == 0 && tagsArr.length > 0 && documentsArr.length == 0 && data.status_id == 206) {
            sweetAlert2('warning', "This Task has document tag(s), please upload the required file to complete status.");
            return;
        }
        sweetAlertLoading();

        $.ajax({
              //laravel requires this thing, it fetches it from the meta up in the head
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "POST",
              url: `/store/bulletin/${menu_store_id}/task/edit`,
              data: formData,
              contentType: false,
              processData: false,
              dataType: "json",
              success: function(data) {
                  if(data.errors){
                      $.each(data.errors,function (key , val){
                          sweetAlert2('warning', 'Check field inputs.');
                          $("#e"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                          console.log(key);
                      });
                  }
                  else{
                    // table_task.ajax.reload(null, false);
                    reloadDataTable();
                    sweetAlert2('success', 'Record has been updated.');
                    $('.imageuploadify-container').remove();
                    //   $('#show_task_modal').modal('hide');
                  }
              },error: function(msg) {
                handleErrorResponse(msg);
                    if(msg.status == 403) {
                        sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                    }
                  console.log("Error");
                  console.log(msg.responseText);
              }
  
  
        });
    }

    function clickAssignee()
    {
        $('#show-assign-to-fullname').css('display', 'none');
        $('#show_assign_to_select_div').css('display', 'block ');
        // document.getElementById('show_assign_to_select_div').style.display = 'block !important';
    }

    function clickSubject()
    {
        $('#show-task-subject').css('display', 'none');
        $('#show-task-subject-div-input').css('display', 'block');
    }

    function resetInputs()
    {
        // $('#show-assign-to-fullname').css('display', 'block');
        // $('#show_assign_to_select_div').css('display', 'none ');
        $('#show-task-subject').css('display', 'block');
        $('#show-task-subject-div-input').css('display', 'none');
        disableUpdateActBtn = '';
        disableUpdateBtn = 'disabled';
        disableDeleteBtn = 'disabled';
    }
</script>