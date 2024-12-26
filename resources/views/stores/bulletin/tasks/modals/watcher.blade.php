<div class="modal" id="watcher_task_modal" tabindex="2">
    <div id="watcher_task_modal_fullscreen" class="shadow modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">

            <div class="col-md-4">
                <div class="ms-3" id="assigned_to"></div>
            </div><div class="col-md-2">
                <div id="show_due_date"></div>
            </div><div class="col-md-2">
                <div class="me-3" id="priority"></div>
            </div><div class="col-md-3">
                <div id="status"></div>
            </div><div class="col-md-1">
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
                                <small>Watchers for Task:</small>
                                <h6 id="task_subject"></h6>
                                <div class="mt-3 fm-search">
                                    <div class="mb-0">
                                        <div class="input-group input-group-lg">
                                            <span class="bg-transparent input-group-text" style="border-color: #15ca20;"><i class='fa fa-search text-success'></i></span>
                                            <input type="text" id="watcher_search_task_input" class="table_search_input form-control" placeholder="Search employee names" autocomplete="off" style="border-color: #15ca20;">
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="watcher_dt_task_table" class="table table-hover" style="width:100%">
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

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        
      </div>
    </div>
  </div>
  
<script> 
    function showTaskWatcherModal(id)
    {
        table_task_watcher_selected_id = id;
        var btn = document.querySelector(`#task-edit-btn-${id}`);
        let data = btn.dataset;
        console.log("fire",data);
        let subject = data.subject;
        let assigned_to_employee_id = data.assigned_to_employee_id;
        let assigned_to = data.assigned_to;
        let due_date = data.due_date;
        let formatted_due_date = data.formatted_due_date;
        let status_id = data.status_id;
        let priority_status_id = data.priority_status_id;

        if(assigned_to_employee_id) {
            let fullname = assigned_to;
            let initials = data.assigned_to_initials;
            let image = data.assigned_to_image;
            let initials_random_color = data.assigned_to_initials_random_color;

            let selectEmp = ``;
            if(image != '' && image != null) {
                selectEmp = `
                    <div class="d-flex">
                        <img src="/upload/userprofile/${image}" width="45" height="45" class="shadow rounded-circle" alt="">
                        <div class="mt-2 flex-grow-1 ms-3">
                            <p id="show-assign-to-fullname" class="mb-0 font-weight-bold font-20"><b>${fullname}</b></p>
                        </div>
                    </div>
                `;
            } else {
                selectEmp = `
                    <div class="d-flex">
                        <div class="employee-avatar-${initials_random_color}-initials hr-employee" style="width: 45px !important; height: 45px !important; font-size: 20px !important;">
                        ${initials}
                        </div>
                        <p class="mt-2 mb-0 font-weight-bold ms-3 font-20"><b>${fullname}</b></p>
                    </div>
                `;
            }

            let status_data = JSON.parse(data.statusArray);
            let priority_data = JSON.parse(data.priorityArray);

            const status = `<button class="btn btn-sm w-100 btn-${status_data.class}">${status_data.name}</button>`;
            const priority = `<button class="btn btn-sm w-100 btn-outline-${priority_data.class}"><i class="fa fa-flag me-2"></i>${priority_data.name}</button>`;

            let dueDateHtml = '';
            if(formatted_due_date) {
                dueDateHtml += ' <span class="me-2">Due Date on '+formatted_due_date+'</span>';
            }
            $('#watcher_task_modal #assigned_to').html(selectEmp);
            $('#watcher_task_modal #status').html(status);
            $('#watcher_task_modal #priority').html(priority);
            $('#watcher_task_modal #show_due_date').html(dueDateHtml);
            $('#watcher_task_modal #task_subject').html(subject);

            $('#watcher_task_modal').modal('show');

            console.log("show watcher")

            table_task_watcher.draw()
        }
    }

    function loadTaskWatchers() {
        let data = {};        
        const watcher_dt_task_table = $('#watcher_dt_task_table').DataTable({
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
                url: "/store/bulletin/task/watchers",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('#watcher_search_task_input').val();
                    data.pharmacy_store_id = menu_store_id;
                    data.task_id = table_task_watcher_selected_id;
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
            //     if (row.is_watcher == true) {
            //         $(row).css('background-color', '#ffcccb');
            //     }
            // },
            initComplete: function( settings, json ) {
                
            }
        });

        table_task_watcher = watcher_dt_task_table;
        $('#watcher_search_task_input').val(table_task_watcher.search());
		$('#watcher_search_task_input').keyup(function(){ table_task_watcher.search($(this).val()).draw() ; })
    }

    function addTaskWatcher(task_id, employee_id)
    {
        var data = {
            task_id: task_id,
            employee_id: employee_id
        };
          
          //console.log(data);
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/store/bulletin/task/watchers/add",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(msg) {
                reloadDataTable();
                table_task_watcher.ajax.reload(null, false);
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

    function deleteTaskWatcher(task_id, employee_id)
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
            url: "/store/bulletin/task/watchers/delete",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(msg) {
                reloadDataTable();
                table_task_watcher.ajax.reload(null, false);
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