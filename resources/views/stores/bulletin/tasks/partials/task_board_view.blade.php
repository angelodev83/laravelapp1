<script>
    // TASK - TO DO
    function task201(data, year, month) {
        renderTask($('#task-201'), $('#task-201-count'), data, year, month);
    }
    // TASK - IN PROGRESS
    function task202(data, year, month) {
        renderTask($('#task-202'), $('#task-202-count'), data, year, month);
    }
    // TASK - TO ANALYZE
    function task203(data, year, month) {
        renderTask($('#task-203'), $('#task-203-count'), data, year, month);
    }
    // TASK - TO VERIFY
    function task204(data, year, month) {
        renderTask($('#task-204'), $('#task-204-count'), data, year, month);
    }
    // TASK - WAITING
    function task205(data, year, month) {
        renderTask($('#task-205'), $('#task-205-count'), data, year, month);
    }
    // TASK - COMPLETE
    function task206(data, year, month) {
        renderTask($('#task-206'), $('#task-206-count'), data, year, month);
    }
    // render function for task
    function renderTask(container, count, data, year, month) {
        const $container = container;
        const $countElement = count;

        let html = '';
        let itemCount = 0;

        data.forEach(data => {
            itemCount++;
            html += `
                <div class="hover-card card" data-task-id="${data.id}">
                    <div class="card-body" onclick="showTaskEditModal(${data.id})">
                        <h6 class="card-title" style="width: 90%;">${data.subject}</h6>
                        <div class="d-flex gap-2 align-items-end">
                            ${ 
                                data.image
                                ? `<img src="/upload/userprofile/${data.image}" class="rounded-circle" data-bs-toggle="tooltip" data-bs-title="${data.assigned_to}" style="width: 35px; height: 35px;">`
                                : `<span class="rounded-circle employee-avatar-${data.initials_random_color}-initials" data-bs-toggle="tooltip" data-bs-title="${data.assigned_to}">${data.initials}</span>`
                            }
                            <div class="px-1 border border-2 rounded border-secondary">
                                <i class="fa-solid fa-calendar-day"></i>
                                <span class="text-success ms-1">${data.due_date || ''}</span>
                            </div>
                            <div class="px-1 text-white rounded" style="background-color: ${data.priority_color || ''}; border: 2px solid ${data.priority_color};">
                                <span><i class="fa-solid fa-flag me-1"></i> ${data.priority_name || ''}</span>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown dots">
                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                        <li><a class="dropdown-item fw-medium d-flex" href="javascript:clickArchiveBtn(${data.id});"> Archive <i class="fa fa-box-archive ms-auto text-end text-danger"></i></a></li>
                        <li><a class="dropdown-item fw-medium d-flex text-danger" href="javascript:clickDeleteBtn(${data.id});"> Delete <i class="fa fa-trash-can ms-auto text-end text-danger"></i></a></li>
                        </ul>
                    </div>
                </div>
            `;
        });

        $container.html(html || '<p class="fst-italic">No record found.</p>');
        $countElement.text(itemCount);

        // activate tooltip
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    }
</script>