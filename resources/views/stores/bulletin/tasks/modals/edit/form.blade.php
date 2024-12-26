<div class="modal" id="edit_task_modal" tabindex="-1">
    <div id="edit_task_modal_fullscreen" class="modal-dialog modal-fullscreen">
      <div class="modal-content">

        <div class="py-2 bg-white modal-header ps-3">
            <div id="numberDiv"></div>
            <div id="copiedNumberAlert"></div>
            <button type="button" class="btn-close menu_permission_update menu_permission_update_all" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          
            <div class="form-body">
                <div class="row">
                    
                <form action="" method="POST" id="#edit_task_modal">
                    <div class="col-md-12">

                        
                        <div class="row g-4">

                            <!-- details info start -->
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                @include('stores/bulletin/tasks/partials/details')
                                {{-- @include('stores/bulletin/tasks/partials/tracking') --}}
                            </div>
                            <!-- details info end -->

                            <!-- attachments info start -->
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                @include('stores/bulletin/tasks/partials/attachments')
                            </div>
                            <!-- attachments info end -->

                            <!-- comments info start -->
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                @include('stores/bulletin/tasks/partials/comments')
                            </div>
                            <!-- comments info end -->

                            <div class="col-lg-12 col-md-12 col-sm-12" id="task-relation-coldiv" style="display: none">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body" style="min-height: 400px;">
                                                <ul class="list-group">
                                                    <li class="list-group-item" id="task-relation-subject">PROCUREMENT ORDER/RETURN DETAILS</li>
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
                            
                        </div>
                    </div>
                </form>
            
                </div><!--end row-->
            </div>
            
        </div>

        <!-- footer starts -!->
        <div class="py-1 modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="updateTaskForm()"><i class="fa fa-save me-2"></i> Save Changes</button>
        </div>
        <!-!- footer end -->
        
      </div>
    </div>
  </div>


<style>
    
</style>
  
<script>        
    function updateTaskForm(){
        let data = {};
        let menu_store_id = {{request()->id}};
          $('.error_txt').remove();
  
          data['id'] = $("#eid").val();
          data['subject'] = document.getElementById("esubject").value;
          data['status_id'] = $('#estatus_id').find(":selected").val();
          data['priority_status_id'] = $('#epriority_status_id').find(":selected").val();
          data['description'] = tinymce.get("eTaskDescription").getContent();
          data['assigned_to_employee_id'] = $('#eassigned_to_employee_id').find(":selected").val();
          data['due_date'] = document.getElementById("edue_date").value;

        var formData = new FormData();
        var uploadFiles = $('#edocuments').get(0).files;
        
        for (let i = 0; i < uploadFiles.length; i++) {
            formData.append("files[]", uploadFiles[i]);
            var kbSize = uploadFiles[i].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }
        console.log("saving",data);
        formData.append("data", JSON.stringify(data));    

          console.log("updateing", data);
        //   new Response(formData).text().then(console.log);
          sweetAlertLoading();
          $.ajax({
              //laravel requires this thing, it fetches it from the meta up in the head
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "POST",
              url: `/store/bulletin/${menu_store_id}/tasks/edit`,
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
                      reloadDataTable();
                      sweetAlert2('success', 'Record has been updated.');
                      $('#edit_task_modal').modal('hide');
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



    // --------------------------------------------------------
    let deletedDocumentsArr = [];
    let addMore = 0;
    let drug_order_id;
    let supply_order_id;
    let inmar_return_id;
    let clinical_order_id;

    let disableUpdateActBtn = '';
    let disableUpdateBtn = 'disabled';
    let disableDeleteBtn = 'disabled';

    function showTaskEditModal(id){
        let modal = $('#edit_task_modal');
        $('#edit_task_modal #edocuments').hide();
        modal.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modal.addClass('dragover');
        });

        modal.on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modal.removeClass('dragover');
        });

        modal.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modal.removeClass('dragover');

            var files = e.originalEvent.dataTransfer.files;
            ehandleTaskFiles(files);
        });

        $('#task-relation-coldiv').css('display', 'none');
        $('#drug-order-partials').css('display', 'none');
        $('#supply-order-partials').css('display', 'none');
        $('#inmar-return-partials').css('display', 'none');
        $('#clinical-order-partials').css('display', 'none');
        resetInputs();
        $('.imageuploadify-container').remove();
        getTaskById(id);
    }


    function getTaskById(id)
    {
        let params = {};
        $.ajax({    
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/store/bulletin/task/load/"+id,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(params),
            success: function(res) {
            
                console.log("task---------------------by id ", res);
                const task = res.item;
                const custom = res.custom;
                console.log("item---------------------by id ", task);
                console.log("custom---------------------by id ", custom);

                if(task) {
                    populateDetails(task, custom);
                }

            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function populateDetails(task, custom) {
        const id = task.id;
        const number = task.number;
        let subject = task.subject;
        let description = task.description;
        let due_date = task.due_date;
        let status_id = task.status_id;
        let priority_status_id = task.priority_status_id;

        const status_type = custom.status_type;

        const watcher_list = custom.watcherList;

        if(description == null || description == undefined)
        {
            description = '';
        }

        const loadDiv = `
            <div class="my-2 border shadow-none card">
                <div class="py-2 card-body">
                    <div class="p-4 row">
                        <div class="mb-3 spinner-grow text-primary" role="status"> 
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#edit_task_modal #taskAttachmentsList').html(loadDiv);
        $('#edit_task_modal #taskCommentsList').html(loadDiv);

        original_edited_task_subject = subject;
        original_edited_task_description = description;

        $('#edit_task_modal #ewatchers').html(watcher_list);

        $('#edit_task_modal #subject_text').html(subject);

        if(task.assigned_to_employee_id) {
            let assigned_to = task.assigned_to;

            resolveTaskEmployeeAvatar(assigned_to);

            const status = `<button class="btn btn-sm w-100 btn-${task.status.class}">${task.status.name}</button>`;
            const priority = `<button class="btn btn-sm w-100 btn-outline-${task.priority_status.class}"><i class="fa fa-flag me-2"></i>${task.priority_status.name}</button>`;

            let dueDateHtml = '';
            if(custom.formatted_due_date) {
                dueDateHtml += ' <span class="me-2">Due Date on '+custom.formatted_due_date+'</span>';
            }
            // $('#edit_task_modal #assigned_to').html(selectEmp);
            $('#edit_task_modal #status').html(status);
            $('#edit_task_modal #priority').html(priority);
            $('#edit_task_modal #show_due_date').html(dueDateHtml);
            $('#edit_task_modal #numberDiv').html(`
                <button type="button" class="mx-2 btn btn-dark position-relative me-lg-5" title="Copy Code" onclick="copyText('${number}', '#copiedCodeAlert')"> <i class='fa-regular fa-copy me-2'></i> <span class="font-15 fw-bold"><b>#${number}</span></button>
            `);
            $('#assignee_task_modal #numberAssigneeDiv').html(`
                <button type="button" class="mx-2 btn btn-dark position-relative me-lg-5" title="Copy Number" onclick="copyText('${number}', '#assignee_task_modal #copiedNumberAssigneeAlert')"> <i class='fa-regular fa-copy me-2'></i> <span class="font-15 fw-bold">#${number}</span></button>
            `);
        }

        let createdByEmp = ``;
        const userEmployee = task.user.employee;
        if(userEmployee.image != '' && userEmployee.image != null) {
            createdByEmp = `
                <div class="d-flex">
                    <img src="/upload/userprofile/${userEmployee.image}" width="35" height="35" class="rounded-circle image-has-border" alt="">
                    <div class="mt-2 flex-grow-1 ms-3">
                        <p id="show-assign-to-fullname" class="mb-0 font-weight-bold">${userEmployee.firstname} ${userEmployee.lastname}</p>
                    </div>
                </div>
            `;
        } else {
            createdByEmp = `
                <div class="employee-avatar-${userEmployee.initials_random_color}-initials hr-employee">
                    ${userEmployee.firstname.charAt(0)}${userEmployee.lastname.charAt(0)}
                </div>
                <p id="show-assign-to-fullname" class="mt-2 mb-0 font-weight-bold ms-3">${userEmployee.firstname} ${userEmployee.lastname}</p>
            `;
        }
        $('#edit_task_modal #created_by').html(createdByEmp);

        tinymce.get("eTaskDescription").setContent(description);

        $('#edit_task_modal #show-id').val(id);
        $("#edit_task_modal input#esubject").val(subject);
        $("#edit_task_modal textarea#eTaskDescription").val(description);
        $("#edit_task_modal input#eid").val(id);
        $("#edit_task_modal #edue_date").val(due_date);

        getTaskStatusDropDownUl('#taskStatusDropDown', '#taskStatusDropDownUl', status_id, status_type);
        getPriorityStatusDropDownUl('#taskPriorityStatusDropDown', '#taskPriorityStatusDropDownUl', priority_status_id, 'priority');

        loadTaskAttachments();
        loadTaskComments();

        // TASK RELATION HAS ONE - DRUG ORDER -------------------------------------
        if(custom.type === "drug_order") {
            $('#task-relation-coldiv').css('display', 'block');
            resolveDrugOrder(task.drug_order);
        }

        // TASK RELATION HAS ONE - SUPPLY ORDER -------------------------------------
        if(custom.type === "supply_order") {
            $('#task-relation-coldiv').css('display', 'block');
            resolveSupplyOrder(task.supply_order);
        }

        // TASK RELATION HAS ONE - INMAR -------------------------------------
        if(custom.type === "inmar_return") {
            $('#task-relation-coldiv').css('display', 'block');
            resolveInmarReturn(task.inmar);
        }

        // TASK RELATION HAS ONE - CLINICAL ORDER -------------------------------------
        if(custom.type === "clinical_order") {
            $('#task-relation-coldiv').css('display', 'block'); 
            resolveClinicalOrder(task.clinical_order)
        }

        $('#edit_task_modal').modal('show');

        is_task_modal_loading = false;

        show_edit_modal = 'task';
    }

    function resolveDrugOrder(drugOrderArr) 
    {
        let task_relation_subject = '';
        const status = drugOrderArr.status;

        let status_name = `<span>${status['name']}</span>`;
        status_name = `<span><b>${status['name']}</b></span>
            <span class="ms-auto ps-5 text-success"><i class="fa fa-check-double"></i></span>
        `;
        $('#task-relation-subject').css('background-color', `${status['color']}`);
        $('#task-relation-subject').css('color', `${status['text_color']}`);
        task_relation_subject += status['name'];


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
        $(`#edit_task_modal #drug_order_item_tbody`).empty();
        $(`#edit_task_modal #status_id`).css('display', 'none');

        $(`#edit_task_modal #drug-order-partials #wholesaler_id`).empty();

        $('#edit_task_modal #order_date').datepicker({
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

        $('#edit_task_modal #po_name').val(arr.order_number);
        $('#edit_task_modal #po_number').val(arr.order_number);
        $('#edit_task_modal #order_date').val(arr.order_date);
        $('#edit_task_modal #account_number').val(arr.account_number);
        $('#edit_task_modal #wholesaler_name').val(arr.wholesaler_name);
        $('#edit_task_modal #created_at').val(formatDateTime(arr.created_at));
        $('#edit_task_modal #comments').val(arr.comments);
        $('#edit_task_modal #po_memo').val(arr.po_memo);

        if(arr.file){
            let filename = arr.file.filename;
            if (filename.length > 30) {
                filename = filename.substring(0, 30) + '...';
            }
            $('#edit_task_modal #drug-order-partials .file_name').text(filename);
            $("#edit_task_modal #drug-order-partials #file_id").val(arr.file_id);
            $("#edit_task_modal #drug-order-partials #chip_controller").show();
            $("#edit_task_modal #drug-order-partials #file").hide();
            $('#edit_task_modal #drug-order-partials .file_name').attr("href", "/store/procurement/pharmacy/"+menu_store_id+"/drug-orders/download/"+arr.file_id+"");
        }
        else{
            $("#edit_task_modal #drug-order-partials #chip_controller").hide();
            $("#edit_task_modal #drug-order-partials #file").show();
        }
        populateNormalSelect(`#edit_task_modal #drug-order-partials #wholesaler_id`, '#edit_task_modal', '/admin/search/wholesaler', {category: 'procurement'}, arr.wholesaler_id)

        $.each(arr.items_imported, function(i, item) {
            const k = i+1;
            addMore++;
            $('#edit_task_modal #drug_order_item_tbody').append(
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

    function resolveSupplyOrder(supplyOrderArr) 
    {
        let task_relation_subject = '';
        const status = supplyOrderArr.status;
        console.log("supply order statuts", status)

        let status_name = `<span>${status['name']}</span>`;
        status_name = `<span><b>${status['name']}</b></span>
            <span class="ms-auto ps-5 text-success"><i class="fa fa-check-double"></i></span>
        `;
        $('#task-relation-subject').css('background-color', `${status['color']}`);
        $('#task-relation-subject').css('color', `${status['text_color']}`);
        task_relation_subject += status['name'];

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
        $(`#edit_task_modal #supply_order_item_tbody`).empty();
        $(`#edit_task_modal #status_id`).css('display', 'none');

        $(`#edit_task_modal #supply-order-partials #wholesaler_id`).empty();

        $('#edit_task_modal #supply-order-partials #order_date').datepicker({
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

        $('#edit_task_modal #supply-order-partials #order_number').val(arr.order_number);
        $('#edit_task_modal #supply-order-partials #created_at').val(formatDateTime(arr.created_at));
        $('#edit_task_modal #supply-order-partials #comments').val(arr.comments);
        $('#edit_task_modal #supply-order-partials #order_date').val(arr.order_date);

        if(arr.file){
            let filename = arr.file.filename;
            if (filename.length > 30) {
                filename = filename.substring(0, 30) + '...';
            }
            $('#edit_task_modal #supply-order-partials .file_name').text(filename);
            $("#edit_task_modal #supply-order-partials #file_id").val(arr.file_id);
            $("#edit_task_modal #supply-order-partials #chip_controller").show();
            $("#edit_task_modal #supply-order-partials #file").hide();
            $('#edit_task_modal #supply-order-partials .file_name').attr("href", "/store/procurement/pharmacy/"+menu_store_id+"/supply-orders/download/"+arr.file_id+"");
        }
        else{
            $("#edit_task_modal #supply-order-partials #chip_controller").hide();
            $("#edit_task_modal #supply-order-partials #file").show();
        }
        populateNormalSelect(`#edit_task_modal #supply-order-partials #wholesaler_id`, '#edit_task_modal', '/admin/search/wholesaler', {category: 'supply'}, arr.wholesaler_id)

        $.each(arr.items, function(i, item) {
            const k = i+1;
            addMore++;
            $('#edit_task_modal #supply_order_item_tbody').append(
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
            searchSupplyItem(`#edit_task_modal #number_${item.id}`, 'edit_task_modal', null, item.id);
        });
    }

    function resolveInmarReturn(inmarArr) 
    {
        let task_relation_subject = '';
        const status = inmarArr.status;

        let status_name = `<span>${status['name']}</span>`;
        status_name = `<span><b>${status['name']}</b></span>
            <span class="ms-auto ps-5 text-success"><i class="fa fa-check-double"></i></span>
        `;
        $('#task-relation-subject').css('background-color', `${status['color']}`);
        $('#task-relation-subject').css('color', `${status['text_color']}`);
        task_relation_subject += status['name'];

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
        $(`#edit_task_modal #inmar_item_tbody`).empty();
        $(`#edit_task_modal #inmar-return-partials #inmar_return_wholesaler_id`).empty();
        $(`#edit_task_modal #status_id`).css('display', 'none');

        let arr = inmarArr;
        // console.log('fire-------------',arr);

        $('#edit_task_modal #inmar_return_name').val(arr.name);
        // $('#edit_task_modal #created_at').val(formatDateTime(arr.created_at));
        $('#edit_task_modal #inmar_return_comments').val(arr.comments);

        $('#edit_task_modal #inmar_return_account_number').val(arr.account_number);
        $('#edit_task_modal #inmar_return_po_name').val(arr.po_name);
        $('#edit_task_modal #inmar_return_wholesaler_name').val(arr.wholesaler_name);

        $('#edit_task_modal #inmar_return_return_date').val(arr.return_date);

        $('#edit_task_modal #inmar_return_return_date').datepicker({
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

        populateNormalSelect(`#edit_task_modal #inmar-return-partials #inmar_return_wholesaler_id`, '#edit_task_modal', '/admin/search/wholesaler', {category: 'procurement'}, arr.wholesaler_id)

        if(arr.file){
            // console.log(arr.file);
            let filename = arr.file.filename;
            if (filename.length > 30) {
                filename = filename.substring(0, 30) + '...';
            }
            $('#edit_task_modal #inmar-return-partials .file_name').text(filename);
            $("#edit_task_modal #inmar-return-partials #file_id").val(arr.file_id);
            $("#edit_task_modal #inmar-return-partials #chip_controller").show();
            $("#edit_task_modal #inmar-return-partials #file").hide();
            $('#edit_task_modal #inmar-return-partials .file_name').attr("href", "/store/procurement/pharmacy/"+menu_store_id+"/inmars/download/"+arr.file_id+"");
                        
        }
        else{
            $("#edit_task_modal #inmar-return-partials #chip_controller").hide();
            $("#edit_task_modal #inmar-return-partials #file").show();
        }

        $.each(arr.items, function(i, item) {
            const k = i+1;
            addMore++;
            $('#edit_task_modal #inmar_item_tbody').append(
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
            $(`#edit_task_modal #med_${item.id}`).append("<option selected value='"+item.drug_id+"'>"+item.drugname+"</option>");
            searchSelect2ApiDrug(`#edit_task_modal #med_${item.id}`, 'edit_task_modal', null, item.id);
        });
    }

    function resolveClinicalOrder(clinicalOrderArr)
    {
        let task_relation_subject = '';
        const status = clinicalOrderArr.status;

        let status_name = `<span>${status['name']}</span>`;
        status_name = `<span><b>${status['name']}</b></span>
            <span class="ms-auto ps-5 text-success"><i class="fa fa-check-double"></i></span>
        `;
        $('#task-relation-subject').css('background-color', `${status['color']}`);
        $('#task-relation-subject').css('color', `${status['text_color']}`);
        task_relation_subject += status['name'];

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
        $(`#edit_task_modal #clinical-order-partials #clinics`).empty();
        $(`#edit_task_modal #clinical-order-partials #clinical_order_item_tbody`).empty();
        $(`#edit_task_modal #clinical-order-partials #status_id`).css('display', 'none');

        $('#edit_task_modal #clinical-order-partials #order_date').datepicker({
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

        $('#edit_task_modal #clinical-order-partials #clinic_order_id').val(arr.id);
        $('#edit_task_modal #clinical-order-partials #order_number').val(arr.order_number);
        $('#edit_task_modal #clinical-order-partials #tracking_number').val(arr.shipment_tracking_number);
        $('#edit_task_modal #clinical-order-partials #prescriber_name').val(arr.prescriber_name);
        $('#edit_task_modal #clinical-order-partials #order_date').val(arr.order_date);
        $('#edit_task_modal #clinical-order-partials #comments').val(arr.comments);

        populateNormalSelect(`#edit_task_modal #clinical-order-partials #clinics`, '#edit_task_modal #clinical-order-partials', '/admin/search/clinic', {}, arr.clinic_id)

        if(arr.file){
            // console.log(arr.file);
            let filename = arr.file.filename;
            if (filename.length > 30) {
                filename = filename.substring(0, 30) + '...';
            }
            $('#edit_task_modal #clinical-order-partials .file_name').text(filename);
            $("#edit_task_modal #clinical-order-partials #file_id").val(arr.file_id);
            $("#edit_task_modal #clinical-order-partials #chip_controller").show();
            $("#edit_task_modal #clinical-order-partials #file").hide();
            $('#edit_task_modal #clinical-order-partials .file_name').attr("href", "/store/procurement/pharmacy/"+menu_store_id+"/clinical-orders/download/"+arr.file_id+"");
                        
        }
        else{
            $("#edit_task_modal #clinical-order-partials #chip_controller").hide();
            $("#edit_task_modal #clinical-order-partials #file").show();
        }


        $.each(arr.items, function(i, item) {
            const k = i+1;
            addMore++;
            $('#edit_task_modal #clinical-order-partials #clinical_order_item_tbody').append(
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
            $(`#edit_task_modal #clinical-order-partials #med_${item.id}`).append("<option selected value='"+item.drug_id+"'>"+item.drugname+"</option>");
            searchItem(`#med_${item.id}`, 'edit_task_modal', null, item.id);   
        });
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

    // from task

    function loadTaskAttachments()
    {
        const task_id = $('#edit_task_modal #eid').val();
        console.log("call attachements",task_id)

        let params = {};
        $.ajax({    
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/store/bulletin/task/load-attachments/"+task_id,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(params),
            success: function(res) {
                console.log("call attachements res",res)
            
                const documents = res.data;

                $('#edit_task_modal #taskAttachmentsList').empty();
                $.each(documents, function(i, item) {
                    const url = item.url;
                    const k = i+1;
                    const ext = item.ext.toLowerCase();
                    const filename = item.name;
                    const fileIcon = fileUtil(ext, 'icon');
                    const fileClass = fileUtil(ext, 'class');
                    
                    let _attachment = '';
                    if(ext == 'png' || ext == 'jpeg' || ext == 'jpg' || ext == 'ico') {
                        _attachment = `<div class="mb-2 image-container">
                            <a class="pb-0 mb-0" target="_blank" href="${url}">
                                <img src="${url}" alt="${filename}" title="${filename}" class="responsive-img">
                            </a>
                        </div>`;
                    }

                    $('#edit_task_modal #taskAttachmentsList').append(`
                        <div class="mt-2 mb-0 border shadow-none card" id="attachment_card_${item.id}">
                            <div class="py-2 card-body">
                                ${_attachment}
                                <div class="p-0 m-0 row">
                                    <div class="px-0 mx-0 col-md-9">
                                        <a class="pb-0 mb-0" target="_blank" href="${url}">
                                            <i class="bx ${fileIcon} me-1 font-12 ${fileClass}"></i>
                                            <span class="document_filename_link text-primary">${filename}</span>
                                        </a>
                                    </div>
                                    <div class="px-0 mx-0 col-md-3 text-end">
                                        <div class="pb-0 mb-0">
                                            <a href="/admin/store-document/download/s3/${item.id}">
                                                <div class="btn btn-xs btn-primary">
                                                    <i class="fa fa-download"></i>
                                                </div>
                                            </a>
                                            <div class="btn btn-xs btn-danger" onclick="clickDeleteDocumentBtn(event, ${item.id})">
                                                <i class="fa fa-trash-can"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <small class="" style="color: #a9a9a9;">${currentDateToYMDHMS(item.created_at, 'M d, Y H:i A')}</small>
                            </div>
                        </div>
                    `); 
                });

                // var container = $('#edit_task_modal #taskAttachmentsList');
                // container.animate({
                //     scrollTop: container.prop("scrollHeight")
                // }, 500);

            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function loadTaskComments()
    {
        const task_id = $('#edit_task_modal #eid').val();
        const auth_emp_id = {{ $authEmployee->id }};
        const assigned_to_employee_id = $('#edit_task_modal #eassigned_to_employee_id').val();

        let params = {};
        $.ajax({    
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/store/bulletin/task/load-comments/"+task_id,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(params),
            success: function(res) {
            
                const comments = res.data;

                $('#edit_task_modal #taskCommentsList').empty();
                $.each(comments, function(i, item) {
                    let comment_user = item.user;
                    let comment_employee = comment_user.employee;
                    let comment_created_at = item.formatted_pst_created_at;

                    var comment_employee_firstname = comment_employee['firstname'];
                    var comment_employee_lastname = comment_employee['lastname'];
                    let comment_employee_fullname = `${comment_employee_firstname} ${comment_employee_lastname}`;
                    let comment_employee_initials = comment_employee_firstname.charAt(0) +''+ comment_employee_lastname.charAt(0);
                    comment_employee_initials = comment_employee_initials.toUpperCase();

                    let comment_documents = item.documents ?? [];

                    let comment_document_list = '';
                    $.each(comment_documents, function(d, document) {
                        let document_path = document.path;

                        if(document.ext == 'png' || document.ext == 'jpeg' || document.ext == 'jpg' || document.ext == 'ico') {
                            comment_document_list += `<li class="list-group-item document_filename_link text-primary" id="task_comment_document_li_${document.id}">
                                <a class="text-primary" href="${document.url}" target="_blank">
                                    <div class="mb-2 image-container">
                                        <img src="${document.url}" alt="${document.name}" title="${document.name}" class="responsive-img">
                                    </div>
                                    <i class="fa fa-paperclip me-2"></i>${document.name}
                                </a>
                            </li>`;
                        } else {
                            comment_document_list += `<li class="list-group-item document_filename_link text-primary" id="task_comment_document_li_${document.id}">
                                <a class="text-primary" href="${document.url}" target="_blank">
                                    <i class="fa fa-paperclip me-2"></i>${document.name}
                                </a>
                            </li>`;
                        }

                    });

                    if(comment_document_list != '')
                    {
                        comment_document_list = `<ul class="mb-2 list-group">${comment_document_list}</ul>`;
                    }

                    let border_color = (auth_emp_id == comment_employee['id']) ? 'ticket_comment_section_body_row_card' : (assigned_to_employee_id == comment_employee['id'] ? 'ticket_comment_section_body_row_card2' : 'ticket_comment_section_body_row_card3');

                    let comment_description =  item.comment.replace(/\n/g, '<br>');
                    let task_comment_section_cols = ``;
                    if(comment_employee['image'] != '' && comment_employee['image'] != null) {
                        task_comment_section_cols = `
                            <div class="mt-3 col-md-12">
                                <div class="card mb-0 ${border_color}">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <img src="/upload/userprofile/${comment_employee['image']}" width="45" height="45" class="rounded-circle image-has-border" alt="">
                                            <div class="flex-grow-1 ms-3">
                                                <p id="show-assign-to-fullname" class="mb-0 font-weight-bold font-12">
                                                    <b>${comment_employee_fullname}</b>
                                                </p>
                                                <small>${comment_created_at}</small>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            ${comment_description}
                                            ${comment_document_list}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        task_comment_section_cols = `
                            <div class="mt-3 col-md-12">
                                <div class="card mb-0 ${border_color}">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="employee-avatar-${comment_employee['initials_random_color']}-initials hr-employee" style="width: 45px !important; height: 45px !important; font-size: 20px !important;">
                                                ${comment_employee_initials}
                                            </div>
                                            <div class="font-weight-bold ms-3 font-12">
                                                <p class="mb-0"><b>${comment_employee_fullname}</b></p>
                                                <small>${comment_created_at}</small>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            ${comment_description}
                                            ${comment_document_list}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    $('#edit_task_modal #taskCommentsList').append(task_comment_section_cols);
                });

                var container = $('#edit_task_modal #taskCommentsList');
                container.animate({
                    scrollTop: container.prop("scrollHeight")
                }, 500);

            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function resolveTaskEmployeeAvatar(employee)
    {
        var firstname = employee['firstname'];
        var lastname = employee['lastname'];
        let fullname = `${firstname} ${lastname}`;
        let initials = firstname.charAt(0) +''+ lastname.charAt(0);
        initials = initials.toUpperCase();

        let selectEmp = ``;
        if(employee['image'] != '' && employee['image'] != null) {
            selectEmp = `
                <div class="d-flex">
                    <img src="/upload/userprofile/${employee['image']}" width="35" height="35" class="rounded-circle image-has-border" alt="">
                    <div class="mt-2 flex-grow-1 ms-3">
                        <p id="show-assign-to-fullname" class="mb-0 font-weight-bold font-12"><b>${fullname}</b></p>
                    </div>
                </div>
                <span class="ms-auto"><i class="fa fa-edit ms-5 edit-icon"></i></span>
            `;
        } else {
            selectEmp = `
                <div class="d-flex">
                    <div class="employee-avatar-${employee['initials_random_color']}-initials hr-employee" style="width: 35px !important; height: 35px !important; font-size: 12px !important;">
                    ${initials}
                    </div>
                    <p class="mt-2 mb-0 font-weight-bold ms-3 font-12"><b>${fullname}</b></p>
                </div>
                <span class="ms-auto"><i class="fa fa-edit ms-5 edit-icon"></i></span>
            `;
        }
        $('#edit_task_modal #assigned_to').html(selectEmp);
        $('#edit_task_modal #eassigned_to_employee_id').val(employee['id']);
    }
</script>