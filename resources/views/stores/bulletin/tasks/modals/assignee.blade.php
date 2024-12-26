<div class="modal" id="assignee_task_modal" tabindex="2">
    <div id="assignee_task_modal_fullscreen" class="shadow modal-dialog modal-md">
      <div class="modal-content">

        <div class="modal-header">

            <div class="col-md-10">
                <div id="numberAssigneeDiv"></div>
                <div id="copiedNumberAssigneeAlert"></div>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn-close me-3 float-end" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>

        <div class="modal-body">
          
            <div class="form-body">
                <div class="row">
                
                    <div class="col-md-12">

                        
                        <div class="row g-0">

                            <!-- body info start -->
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <small id="task_subject_title"></small>
                                <h6 id="task_subject"></h6>
                                <div class="mt-3 fm-search">
                                    <div class="mb-0">
                                        <div class="input-group input-group-lg">
                                            <span class="bg-transparent input-group-text" style="border-color: #15ca20;"><i class='fa fa-search text-success'></i></span>
                                            <input type="text" id="task_assignee_search_input" class="table_search_input form-control" placeholder="Search employee names" autocomplete="off" style="border-color: #15ca20;">
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="assignee_dt_task_table" class="table table-hover" style="width:100%">
                                        <thead></thead>
                                        <tbody> 
                                            <tr>
                                                <td>
                                                    <div class="dt-loading-spinner">
                                                        <i class="fas fa-spinner fa-spin fa-3x"></i> <!-- Example: Font Awesome spinner icon -->
                                                    </div>
                                                </td>
                                            </tr>                               
                                        </tbody>
                                        <tfooter></tfooter>
                                    </table>
                                </div>
                            </div>
                            <!-- body info ends -->
                            
                        </div>
                    </div>

            
                </div><!--end row-->
            </div>
            
        </div>

        <!-- <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div> -->
        
      </div>
    </div>
  </div>
  
<script> 
    function showTaskAssigneeModal(id = '')
    {
        console.log("fire task assignee", id)
        if(id == '') {
            id = $('#edit_task_modal #eid').val();
        }
        table_task_assignee_selected_id = id;
        var btn = document.querySelector(`#task-edit-btn-${id}`);
        let data = btn.dataset;
        let formatted_due_date = data.formatted_due_date;
        let menuclass = data.menuclass;
        let arr = JSON.parse(data.array);
        let number = arr.number;
        let subject = arr.subject;
        let description = arr.description;
        let assigned_to_employee_id = arr.assigned_to_employee_id;
        let assigned_to = data.assigned_to;
        let due_date = data.due_date;
        let status_id = arr.status_id;
        let priority_status_id = arr.priority_status_id;
        // let assignees = arr.assignees;

        if(assigned_to_employee_id) {
            let assigned_to = arr.assigned_to;
            var firstname = assigned_to['firstname'];
            var lastname = assigned_to['lastname'];
            let fullname = `${firstname} ${lastname}`;
            let initials = firstname.charAt(0) +''+ lastname.charAt(0);
            initials = initials.toUpperCase();

            let selectEmp = ``;
            if(assigned_to['image'] != '' && assigned_to['image'] != null) {
                selectEmp = `
                    <div class="d-flex">
                        <img src="/upload/userprofile/${assigned_to['image']}" width="45" height="45" class="shadow rounded-circle" alt="">
                        <div class="mt-2 flex-grow-1 ms-3">
                            <p id="show-assign-to-fullname" class="mb-0 font-weight-bold font-20"><b>${fullname}</b></p>
                        </div>
                    </div>
                `;
            } else {
                selectEmp = `
                    <div class="d-flex">
                        <div class="employee-avatar-${assigned_to['initials_random_color']}-initials hr-employee" style="width: 45px !important; height: 45px !important; font-size: 20px !important;">
                        ${initials}
                        </div>
                        <p class="mt-2 mb-0 font-weight-bold ms-3 font-20"><b>${fullname}</b></p>
                    </div>
                `;
            }

            // let status_data = arr.status;
            // let priority_data = arr.priorityStatus;

            let status_data = JSON.parse(data.statusArray);
            let priority_data = JSON.parse(data.priorityArray);

            const status = `<button class="btn btn-sm w-100 btn-${status_data.class}">${status_data.name}</button>`;
            const priority = `<button class="btn btn-sm w-100 btn-outline-${priority_data.class}"><i class="fa fa-flag me-2"></i>${priority_data.name}</button>`;

            let dueDateHtml = '';
            if(formatted_due_date) {
                dueDateHtml += ' <span class="me-2">Due Date on '+formatted_due_date+'</span>';
            }
            $('#assignee_task_modal #assigned_to').html(selectEmp);
            $('#assignee_task_modal #task_subject_title').html(`Assignee for Task: <b>#${number}</b>`);
            $('#assignee_task_modal #task_subject').html(subject);

            $('#assignee_task_modal').modal('show');

            table_task_assignee.draw()
        }
    }

    function loadTaskAssignees() {
        let data = {};        
        const assignee_dt_task_table = $('#assignee_dt_task_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            pageLength: 5,
            dom: 'fBtp',
            // order: [[1, 'desc']],
            buttons: [

            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/store/bulletin/task/assignees",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('#task_assignee_search_input').val();
                    data.pharmacy_store_id = menu_store_id;
                    data.task_id = table_task_assignee_selected_id;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'id', name: 'id', title: 'Employee Name', orderable: false, searchable: false, render: function(data, type, row) {
                    return row.fullname;
                } },
                { data: 'actions', name: 'actions', title: 'Action', orderable: false, searchable: false},
            ],
            // "createdRow": function(row, data, dataIndex) {
            //     // Example condition: highlight row if value > 50
            //     if (row.is_assignee == true) {
            //         $(row).css('background-color', '#ffcccb');
            //     }
            // },
            initComplete: function( settings, json ) {
                
            }
        });

        table_task_assignee = assignee_dt_task_table;
        $('#task_assignee_search_input').val(table_task_assignee.search());
		$('#task_assignee_search_input').keyup(function(){ table_task_assignee.search($(this).val()).draw() ; })
    }

    function selectTaskAssignee(task_id, employee_id, employeeArr)
    {

        let employee = JSON.parse(employeeArr);

        let data = {
            id: task_id,
            assigned_to_employee_id: employee_id
        };

        console.log(data);
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PATCH",
            url: "/store/bulletin/task/update-details",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {
            //success  
                resolveTaskEmployeeAvatar(employee);

                Swal.fire({
                    position: 'center',
                    icon: res.status,
                    title: res.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                // sweetAlert2('success', 'Record has been updated.');
                $('#assignee_task_modal').modal('hide');
                reloadDataTable()
            },error: function(res) {
                handleErrorResponse(res);
                if(res.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                console.log(res.responseText);
            }

        });
    }

    function deleteTaskAssignee(task_id, employee_id)
    {
        var data = {
            task_id: task_id,
            employee_id: employee_id
        };
          
          //console.log(data);
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: "/store/bulletin/task/assignees/delete",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(msg) {
                reloadDataTable();
                table_task_assignee.ajax.reload(null, false);
                Swal.close();
            },error: function(msg) {
                handleErrorResponse(msg);
                if(msg.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                console.log(msg.responseText);
            }

        });
    }
</script>