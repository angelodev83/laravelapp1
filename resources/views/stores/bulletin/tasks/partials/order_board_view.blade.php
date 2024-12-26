<script>
    // TASK ORDER - NEW REQUEST
    function order701(data, year, month) {
        renderTaskOrder($('#order-701'), $('#order-701-count'), data, year, month);
    }
    // TASK ORDER - RECEIVED
    function order702(data, year, month) {
        renderTaskOrder($('#order-702'), $('#order-702-count'), data, year, month);
    }
    // TASK ORDER - IN TRANSIT
    function order703(data, year, month) {
        renderTaskOrder($('#order-703'), $('#order-703-count'), data, year, month);
    }
    // TASK ORDER - SUBMITTED
    function order704(data, year, month) {
        renderTaskOrder($('#order-704'), $('#order-704-count'), data, year, month);
    }
    // TASK ORDER - MISSING ORDER
    function order705(data, year, month) {
        renderTaskOrder($('#order-705'), $('#order-705-count'), data, year, month);
    }
    // TASK ORDER - COMPLETED
    function order706(data, year, month) {
        renderTaskOrder($('#order-706'), $('#order-706-count'), data, year, month);
    }
    // render function for task order
    function renderTaskOrder(container, count, data, year, month) {
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
                        ${
                            data.drug_orders
                            ? `<span class="py-1 fw-medium card-subtitle text-body-secondary">PO Memo: ${data.drug_orders.po_memo || ''}</span>
                               <span class="py-1 fw-medium card-subtitle text-body-secondary">Account Number: ${data.drug_orders.account_number || ''}</span>`
                            : data.supply_orders
                            ? `<span class="py-1 fw-medium card-subtitle text-body-secondary">Order Number: ${data.supply_orders.order_number || ''}</span>`
                            : data.inmars
                            ? `<span class="py-1 fw-medium card-subtitle text-body-secondary">PO Name: ${data.inmars.po_name || ''}</span>
                               <span class="py-1 fw-medium card-subtitle text-body-secondary">Account Number: ${data.inmars.account_number || ''}</span>`
                            : data.clinical_orders
                            ? `<span class="py-1 fw-medium card-subtitle text-body-secondary">Order Number: ${data.clinical_orders.order_number || ''}</span>`
                            : ''
                        }
                        <div class="d-flex gap-2 align-items-end">
                            ${ 
                                data.image
                                ? `<img src="/upload/userprofile/${data.image}" class="rounded-circle" data-bs-toggle="tooltip" data-bs-title="${data.assigned_to}" style="width: 35px; height: 35px;">`
                                : `<span class="rounded-circle employee-avatar-${data.initials_random_color}-initials" data-bs-toggle="tooltip" data-bs-title="${data.assigned_to}">${data.initials}</span>`
                            }
                            <div class="px-1 border border-2 rounded border-secondary">
                                <i class="fa-solid fa-calendar-day"></i>
                                ${
                                    data.drug_orders
                                    ? `<span class="text-success ms-1">${data.drug_orders.order_date || ''}</span>`
                                    : data.supply_orders
                                    ? `<span class="text-success ms-1">${data.supply_orders.order_date || ''}</span>`
                                    : data.inmars
                                    ? `<span class="text-success ms-1">${data.inmars.return_date || ''}</span>`
                                    : data.clinical_orders
                                    ? `<span class="text-success ms-1">${data.clinical_orders.order_date || ''}</span>`
                                    : ''
                                }
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