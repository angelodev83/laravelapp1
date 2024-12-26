<div class="card">
    <div class="m-2 card-body">

        <!-- subject -->
        <div class="mb-4 row">
            <div class="col">
                <div class="d-flex" id="esubject_text" onclick="editSubject(event)" style="cursor: pointer;">
                    <span class="fw-bold" id="subject_text" style="font-size: large;"></span>
                    <span class="ms-auto"><i class="fa fa-edit ms-5 edit-icon"></i></span>
                </div>
                <input type="text" name="subject" id="esubject" class="form-control w-100 d-none" placeholder="Subject" autocomplete="off" required>
                <div class="invalid-feedback">
                    Subject field is required
                </div>
            </div>
        </div>

        <!-- row start -->
        <div class="row gy-1">
            <!-- assignee -->
            <label class="col-sm-4 col-form-label">
                <i class="fa-regular fa-user me-2"></i>Assignee
            </label>
            <div class="mb-2 col-sm-8">
                <div class="d-flex" id="assigned_to" onclick="showAssigneeModal()" style="cursor: pointer;"></div>

                <input id="eassigned_to_employee_id" class="form-control form-control-sm" name="assigned_to_employee_id" type="hidden" value="" autocomplete="off"/>
                <input id="eid" class="form-control form-control-sm" name="id" type="hidden" value="" autocomplete="off"/>
            </div>
            <!-- due date -->
            <label class="col-sm-4 col-form-label">
                <i class="fa-regular fa-calendar me-2"></i>Due Date
            </label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm" id="edue_date" name="due_date" placeholder="yyyy-mm-dd" autocomplete="off" aria-describedby="icon-due-date">
            </div>
            <!-- status -->
            <label class="col-sm-4 col-form-label">
                <i class="fa-regular fa-circle me-2"></i>Status
            </label>
            <div class="col-sm-8">
                <div class="dropdown">
                    <button class="btn btn-sm btn-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="statusDropDown" style="text-align: left;">
                        <i class="fa fa-circle ms-2 me-3"></i>To do
                    </button>
                    <ul class="dropdown-menu w-100" id="statusDropDownUl"></ul>
                </div>
            </div>
            <!-- priority -->
            <label class="col-sm-4 col-form-label">
                <i class="fa-regular fa-flag me-2"></i>Priority
            </label>
            <div class="col-sm-8">
                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="priorityStatusDropDown" style="text-align: left; border-color: #ccc;">
                        <i class="fa fa-flag ms-2 me-3 text-secondary"></i>Low
                    </button>
                    <ul class="dropdown-menu w-100" id="priorityStatusDropDownUl"></ul>
                </div>
            </div>
            <!-- created by -->
            <label class="mt-1 col-sm-4 col-form-label">
                <i class="fa-regular fa-user me-2"></i>Created by
            </label>
            <div class="mt-1 col-sm-8">
                <div class="d-flex" id="created_by"></div>
            </div>
            <!-- watcher -->
            <label class="mt-3 col-sm-4 col-form-label">
                <i class="fa-regular fa-circle-dot me-2"></i>Watcher
            </label>
            <div class="mt-3 col-sm-8">
                <div id="ewatchers"></div>
            </div>
            <!-- description -->
            <label class="col-sm-12 col-form-label">
                Description
            </label>
            <div class="col-sm-12">
                <textarea class="form-control tinymce-content" name="edescription" id="edescription" rows="8" placeholder="Description" onchange="changeDescription(event)"></textarea>
            </div>
        </div>
        <!-- row end -->

    </div>
</div>

<script>
    function editSubject(event)
    {
        event.preventDefault();
        $('#edit_ticket_modal #esubject_text').addClass('d-none');
        $('#edit_ticket_modal #esubject').removeClass('d-none');
        
    }

    function updateDetails(event, field, value)
    {
        event.preventDefault();
        
        $(".form-control").removeClass("is-invalid");

        const id = $('#edit_ticket_modal #eid').val();
        if(!id) {
            return;
        }

        let data = {
            id: id
        };
        
        data[field] = value;

        console.log(data);
        sweetAlertLoading();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PATCH",
            url: "/store/escalation/tickets/update-details",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {
                // sweetAlert2('success', 'Record has been updated.');
                reloadDataTable();

                if(field == 'status_id')
                {
                    getTaskStatusDropDownUl('#statusDropDown', '#statusDropDownUl', value, 'task');
                }

                if(field == 'priority_status_id')
                {
                    getPriorityStatusDropDownUl('#priorityStatusDropDown', '#priorityStatusDropDownUl', value, 'priority');
                }

                Swal.fire({
                    position: 'center',
                    icon: res.status,
                    title: res.message,
                    showConfirmButton: false,
                    timer: 1500
                });

            },error: function(res) {
                handleErrorResponse(msg);
                if(res.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                console.log(res.responseText);
            }

        });
    }

</script>