<div class="modal" id="watcher_ticket_modal" tabindex="2">
    <div id="watcher_ticket_modal_fullscreen" class="shadow modal-dialog modal-lg">
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
                                <small id="ticket_subject_title"></small>
                                <h6 id="ticket_subject"></h6>
                                <div class="mt-3 fm-search">
                                    <div class="mb-0">
                                        <div class="input-group input-group-lg">
                                            <span class="bg-transparent input-group-text" style="border-color: #15ca20;"><i class='fa fa-search text-success'></i></span>
                                            <input type="text" id="watcher_search_input" class="table_search_input form-control" placeholder="Search employee names" autocomplete="off" style="border-color: #15ca20;">
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="watcher_dt_table" class="table table-hover" style="width:100%">
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
    function showWatcherModal(id)
    {
        table_ticket_watcher_selected_id = id;
        var btn = document.querySelector(`#ticket-edit-btn-${id}`);
        let data = btn.dataset;
        let formatted_due_date = data.formatted_due_date;
        let menuclass = data.menuclass;
        let arr = JSON.parse(data.array);
        let code = arr.code;
        let subject = arr.subject;
        let description = arr.description;
        let assigned_to_employee_id = arr.assigned_to_employee_id;
        let assigned_to = data.assigned_to;
        let due_date = data.due_date;
        let status_id = arr.status_id;
        let priority_status_id = arr.priority_status_id;
        let watchers = arr.watchers;

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

            let status_data = arr.status;
            let priority_data = arr.priority;

            const status = `<button class="btn btn-sm w-100 btn-${status_data.class}">${status_data.name}</button>`;
            const priority = `<button class="btn btn-sm w-100 btn-outline-${priority_data.class}"><i class="fa fa-flag me-2"></i>${priority_data.name}</button>`;

            let dueDateHtml = '';
            if(formatted_due_date) {
                dueDateHtml += ' <span class="me-2">Due Date on '+formatted_due_date+'</span>';
            }
            $('#watcher_ticket_modal #assigned_to').html(selectEmp);
            $('#watcher_ticket_modal #status').html(status);
            $('#watcher_ticket_modal #priority').html(priority);
            // $('#watcher_ticket_modal #show_due_date').html(dueDateHtml);
            $('#watcher_ticket_modal #ticket_subject_title').html(`Assignee for Ticket: <b>#${code}</b>`);
            $('#watcher_ticket_modal #ticket_subject').html(subject);

            $('#watcher_ticket_modal').modal('show');

            table_ticket_watcher.draw()
        }
    }

    function loadWatchers() {
        let data = {};        
        const watcher_dt_table = $('#watcher_dt_table').DataTable({
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
                url: "/store/escalation/tickets/watchers",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('#watcher_search_input').val();
                    data.pharmacy_store_id = menu_store_id;
                    data.ticket_id = table_ticket_watcher_selected_id;
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

        table_ticket_watcher = watcher_dt_table;
        $('#watcher_search_input').val(table_ticket_watcher.search());
		$('#watcher_search_input').keyup(function(){ table_ticket_watcher.search($(this).val()).draw() ; })
    }

    function addWatcher(ticket_id, employee_id)
    {
        var data = {
            ticket_id: ticket_id,
            employee_id: employee_id
        };
          
          //console.log(data);
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/store/escalation/tickets/watchers/add",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(msg) {
                reloadDataTable()
                table_ticket_watcher.ajax.reload(null, false);
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

    function deleteWatcher(ticket_id, employee_id)
    {
        var data = {
            ticket_id: ticket_id,
            employee_id: employee_id
        };
          
          //console.log(data);
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "DELETE",
            url: "/store/escalation/tickets/watchers/delete",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(msg) {
                reloadDataTable();
                table_ticket_watcher.ajax.reload(null, false);
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