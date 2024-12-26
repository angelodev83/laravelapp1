<div class="modal" id="edit_rts_modal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content" id="edit_rts_modal_content">
            <div class="modal-header pb-1 pt-2">
                <div class="modal-title" style="font-weight: 1000;">Patient Fullname</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background-color: #efeff0;">     
                <div class="row">
                    <div id="patient-rts-container" class="gap-3 d-flex">
                    </div>
                </div><!--end row-->
            </div>
        </div>
    </div>
</div>

<script>
    function openRTSCard(rts_id, is_archived) {
        if(is_archived == 1) {
            sweetAlert2('error', "Please un-archive to open this card.");
            return;
        }
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: 'rts/patient-data/'+rts_id,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {
                loadPatientData(res.data);
            },error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function loadPatientData(items)
    {
        console.log("patient data",items);

        $('#edit_rts_modal #patient-rts-container').empty();

        for(let i in items) {
            const raw = items[i].raw;
            const formatted = items[i].formatted;

            if(i == 0) {
                const patientAvatar = `
                    <span class="d-flex align-items-start">
                        <img src="/source-images/store-gallery/patient-avatar.png" width="45" height="45" class="rounded-circle image-has-border ms-1 me-3" alt="">
                        <div class="m-0 p-0">
                            <h3 class"mb-0 pb-0" style="margin-bottom: 0px !important; padding-bottom: 0px !important;">${formatted.patient_fullname}</h3>
                            <small class"mt-0 pt-0">Intranet ID #${raw.patient.id}</small>
                        </div>
                    </span>
                `;
                $('#edit_rts_modal .modal-title').html(patientAvatar);
            }

            const detailsHtml = resolveDetails(items[i]);
            const commentsHtml = resolveComments(items[i]);

            let rxHtml = `
                <div class="card rounded-3" style="min-width: 25rem;" id="rts-item-${raw.id}">
                    <div class="card-body m-0 p-3">
                        <p class="fw-bold fs-6">RX# ${raw.rx_number}</p>
                        ${detailsHtml}
                        <hr class="my-2">
                        <div id="rts-comments-${raw.id}" class="mx-0 px-0">
                            ${commentsHtml}
                        </div>

                    </div>
                </div>
            `;

            $('#edit_rts_modal #patient-rts-container').append(rxHtml);

            new PerfectScrollbar(`#commentsList${raw.id}`);

            $(`#edit_rts_modal #fill_date_${raw.id}`).datepicker({
                format: "mm/dd/yyyy",
                todayHighlight: true,
                uiLibrary: 'bootstrap5',
                modal: true,
                icons: {
                    rightIcon: '<i class="material-icons"></i>'
                },
                showRightIcon: false,
                autoclose: true,
            }).on('changeDate', function(e) {
                // Your function here
                console.log("Date selected: " + e.format('yyyy-mm-dd'));

                const daysDifference = getTodayDayDifference(e.format('yyyy-mm-dd'));

                let bg_color = 'white';
                let text_color = 'black';

                if(daysDifference >= 14) {
                    bg_color = 'red';
                    text_color = 'white';
                }

                $('#edit_rts_modal #days_in_queue_'+raw.id).val(daysDifference+' Days');
                $('#edit_rts_modal #days_in_queue_'+raw.id).css('background-color', bg_color);
                $('#edit_rts_modal #days_in_queue_'+raw.id).css('color', text_color);

                let current_status_id = $(`#edit_rts_modal #status_id_${raw.id}`).val();
                if(current_status_id == 921 || current_status_id == 922) {
                    let new_status_id = daysDifference > 7 ? 922 : 921;
                    $(`#edit_rts_modal #status_id_${raw.id}`).val(new_status_id);

                    let stat = rtsStatus.filter(item => item.id == new_status_id);
        
                    $(`#edit_rts_modal #status_id_${raw.id}`).css('background-color', stat[0].color);
                    $(`#edit_rts_modal #status_id_${raw.id}`).css('color', stat[0].text_color);
                }

                updateDetails('fill_date', raw.id)
            });

            setTimeout(function() {
                $(`#commentsList${raw.id}`).animate({
                    scrollTop: $(`#commentsList${raw.id}`).prop("scrollHeight")
                }, 500);
            }, 300);
        }

        swal.close();
        $('#edit_rts_modal').modal('show');
    }


    function selectedRTSStatus(_id) {
        let value = $(`#edit_rts_modal #status_id_${_id}`).val();

        let stat = rtsStatus.filter(item => item.id == value);
        
        $(`#edit_rts_modal #status_id_${_id}`).css('background-color', stat[0].color);
        $(`#edit_rts_modal #status_id_${_id}`).css('color', stat[0].text_color);

        updateDetails('status_id', _id);
    }

    function updateDetails(field, rts_id, _value = null)
    {
        let value = _value;
        let _selector = field+'_'+rts_id;
        if(_value === null) {
            value = $(`#edit_rts_modal #${_selector}`).val();
            $(`#edit_rts_modal #${_selector}`).attr('disabled', true);
        }

        let data = {
            rts_id: rts_id,
            field: field,
            value: value
        };

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "PATCH",
            url: 'rts/update',
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {
                if(field == 'is_archived') {
                    loadBoardData();
                    let txt = 'archived.';
                    if(value == 0) {
                        txt = 'un-archived.';
                    }
                    sweetAlert2('success', "Record has been "+txt);
                } else {
                    if(_value === null) {
                        $(`#edit_rts_modal #${_selector}`).attr('disabled', false);
                    } else {
                        loadBoardData();
                    }
                    sweetAlert2('success', "Record has been updated.");
                }
            },error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function changeFileAttachments(rts_id, event)
    {
        comment_files = null;
        let comment_attachment_chip_card_list = '';

        var uploadFiles = event.target.files;  

        if(uploadFiles.length){

            $('.popover').remove();

            let attachedFileNames = [];

            for (let i = 0; i < uploadFiles.length; i++) {
                var kbSize = uploadFiles[i].size/1024;
                if(kbSize > 100000) {
                    sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                    return;
                }
                attachedFileNames.push(uploadFiles[i].name);
                comment_attachment_chip_card_list += `<li class="list-group-item"><i class="fa fa-paperclip me-2"></i>${uploadFiles[i].name}</li>`;
            }
            
            comment_files = uploadFiles;

            $('#comment_attachment_chip_span_'+rts_id).empty();
            $('#comment_attachment_chip_span_'+rts_id).html(`
                <div class="chip chip-lg" id="comment_attachment_chip_${rts_id}" data-bs-toggle="popover" data-bs-placement="top" data-bs-html="true" title="Comment Attachment(s)">
                    <span class="badge bg-secondary">${uploadFiles.length}</span> attached <span class="closebtn" onclick="resetCommentChipAttachment(${rts_id})" title="Remove (${uploadFiles.length}) Comment Attachment(s)">×</span>
                </div>
            `);

            if(comment_attachment_chip_card_list != '')
            {
                $('#comment_attachment_chip_card_'+rts_id).empty();
                $('#comment_attachment_chip_card_'+rts_id).html(`
                    <ul class="list-group">
                        ${comment_attachment_chip_card_list}
                    </ul>
                `);
            }

            const popoverTrigger = document.getElementById('comment_attachment_chip_'+rts_id);
            const popoverContent = document.getElementById('comment_attachment_chip_card_'+rts_id);

            new bootstrap.Popover(popoverTrigger, {
                content: popoverContent.innerHTML,
                boundary: 'viewport', // Optional: Ensure popover stays within the viewport
            });

        }
    }

    // Resolve html functions ------------------------------------------------------------//

    function resolveDetails(item)
    {
        const raw = item.raw;
        const formatted = item.formatted;

        let statusHtml = '';

        for(let rs in rtsStatus) {
            const stat = rtsStatus[rs];

            statusHtml += `
                <option class="bg-white text-black" ${stat.id == raw.status_id ? 'selected' : ''} value="${stat.id}">${stat.name}</option>
            `;
        }

        const daysDifference = getTodayDayDifference(raw.fill_date);

        const html = `
            <div class="row gy-1">

                <label class="col-sm-5 col-form-label">
                    <i class="fa-regular fa-calendar me-2"></i>Fill Date
                </label>
                <div class="col-sm-7">
                    <input type="text" class="form-control form-control-sm" value="${formatted.fill_date}" placeholder="mm/dd/yyyy" autocomplete="off" aria-describedby="icon-due-date" id="fill_date_${raw.id}" name="fill_date[]">
                </div>

                <label class="col-sm-5 col-form-label">
                    <i class="fa fa-clock me-2"></i>Days in Queue
                </label>
                <div class="col-sm-7">
                    <input type="text" class="form-control form-control-sm" value="${daysDifference} Days" style="background-color: ${formatted.days_in_queue_bg_color}; color: ${formatted.days_in_queue_text_color};" id="days_in_queue_${raw.id}" name="days_in_queue[]" readonly>
                </div>

                <label class="col-sm-5 col-form-label">
                    <i class="fa fa-phone me-2"></i>Call Attempts
                </label>
                <div class="col-sm-7">
                    <select class="form-select form-select-sm" value="${raw.call_attempts}" id="call_attempts_${raw.id}" name="call_attempts[]" onchange="updateDetails('call_attempts', ${raw.id})">
                        <option ${raw.call_attempts == 0 ? 'selected': ''}>0</option>
                        <option ${raw.call_attempts == 1 ? 'selected': ''}>1</option>
                        <option ${raw.call_attempts == 2 ? 'selected': ''}>2</option>
                        <option ${raw.call_attempts == 3 ? 'selected': ''}>3</option>
                    </select>
                </div>

                <label class="col-sm-5 col-form-label">
                    <i class="fa-regular fa-circle me-2"></i>Status
                </label>
                <div class="col-sm-7">
                    <select class="form-select form-select-sm" style="background-color: ${raw.status.color}; color: ${raw.status.text_color};" id="status_id_${raw.id}" name="status_id[]" onchange="selectedRTSStatus(${raw.id})">
                        ${statusHtml}
                    </select>
                </div>

                <label class="col-sm-5 col-form-label">
                    <i class="fa fa-prescription-bottle-medical me-2"></i>Dispensed Item
                </label>
                <div class="col-sm-7">
                    <input type="text" class="form-control form-control-sm" value="${raw.dispensed_item_name ?? ''}" id="dispensed_item_name_${raw.id}" name="dispensed_item_name[]" onchange="updateDetails('dispensed_item_name', ${raw.id})"/>
                </div>

                <label class="col-sm-5 col-form-label">
                    <i class="fa fa-dollar-sign me-2"></i>Patient Paid Amount
                </label>
                <div class="col-sm-7">
                    <input type="number" class="form-control form-control-sm text-end" value="${raw.patient_paid_amount ?? ''}" id="patient_paid_amount_${raw.id}" name="patient_paid_amount[]"  onchange="updateDetails('patient_paid_amount', ${raw.id})"/>
                </div>

                <label class="col-sm-5 col-form-label">
                    <i class="fa-regular fa-font-awesome me-2"></i>Priority
                </label>
                <div class="col-sm-7">
                    <input type="text" class="form-control form-control-sm" value="${raw.priority_name ?? ''}" id="priority_name_${raw.id}" name="priority_name[]"   onchange="updateDetails('priority_name', ${raw.id})"/>
                </div>

            </div>
        `;
        return html;
    }

    function resolveComments(item)
    {
        const raw = item.raw;
        const formatted = item.formatted;
        const comments = raw.comments ?? [];

        const auth_emp_id = {{ $authEmployee->id }};

        let commentHtml = '';

        for(let c in comments) {
            const comment = comments[c];

            let comment_user = comment.user;
            let comment_employee = comment_user.employee ?? null;
            
            let comment_created_at = formatted['comments'][comment['id']]['formatted_pst_created_at'];

            let formattedDocuments = formatted['comments'][comment['id']]['documents'] ?? [];

            var comment_employee_firstname = comment_employee['firstname'];
            var comment_employee_lastname = comment_employee['lastname'];
            let comment_employee_fullname = `${comment_employee_firstname} ${comment_employee_lastname}`;
            let comment_employee_initials = comment_employee_firstname.charAt(0) +''+ comment_employee_lastname.charAt(0);
            comment_employee_initials = comment_employee_initials.toUpperCase();

            let comment_documents = comment.documents ?? [];

            let comment_document_list = '';
            $.each(comment_documents, function(d, document) {
                let document_path = document.path;

                let s3_url = formattedDocuments[document.id]['s3_url'] ?? '';

                if(document.ext == 'png' || document.ext == 'jpeg' || document.ext == 'jpg' || document.ext == 'ico') {
                    comment_document_list += `<li class="list-group-item document_filename_link text-primary" id="rts_comment_document_li_${document.id}">
                        <a class="text-primary" href="${s3_url}" target="_blank">
                            <div class="mb-2 image-container">
                                <img src="${s3_url}" alt="${document.name}" title="${document.name}" class="responsive-img">
                            </div>
                            <i class="fa fa-paperclip me-2"></i>${document.name}
                        </a>
                    </li>`;
                } else {
                    comment_document_list += `<li class="list-group-item document_filename_link text-primary" id="rts_comment_document_li_${document.id}">
                        <a class="text-primary" href="${s3_url}" target="_blank">
                            <i class="fa fa-paperclip me-2"></i>${document.name}
                        </a>
                    </li>`;
                }

            });

            if(comment_document_list != '')
            {
                comment_document_list = `<ul class="mb-2 list-group">${comment_document_list}</ul>`;
            }

            let border_color = (auth_emp_id == comment_employee['id']) ? 'ticket_comment_section_body_row_card' : 'ticket_comment_section_body_row_card2';

            let rts_comment_section_cols = ``;
            let comment_description =  comment.comment.replace(/\n/g, '<br>');

            if(comment_employee['image'] != '' && comment_employee['image'] != null) {
                rts_comment_section_cols = `
                    <div class="mt-3 col-md-12">
                        <div class="card mb-0 ${border_color}">
                            <div class="card-body">
                                <div class="d-flex">
                                    <img src="/upload/userprofile/${comment_employee['image']}" width="35" height="35" class="rounded-circle image-has-border" alt="">
                                    <div class="flex-grow-1 ms-3">
                                        <p id="show-assign-to-fullname" class="mb-0 font-weight-bold font-11">
                                            <b>${comment_employee_fullname}</b>
                                        </p>
                                        <span style="font-size: 12px; color: gray;">${comment_created_at}</span>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    ${comment_description}
                                    ${comment_document_list}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                rts_comment_section_cols = `
                    <div class="mt-3 col-md-12">
                        <div class="card mb-0 ${border_color}">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="employee-avatar-${comment_employee['initials_random_color']}-initials hr-employee" style="width: 35px !important; height: 35px !important; font-size: 13px !important;">
                                        ${comment_employee_initials}
                                    </div>
                                    <div class="font-weight-bold ms-3 font-11">
                                        <p class="mb-0"><b>${comment_employee_fullname}</b></p>
                                        <span style="font-size: 12px; color: gray;">${comment_created_at}</span>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    ${comment_description}
                                    ${comment_document_list}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            commentHtml += rts_comment_section_cols;
        }

        const html = `
            <div id="comment_attachment_chip_card_${raw.id}" style="display: none;"></div>

            <div class="row gy-1">
                <h6 class="mb-1"><i class="bx bx-message-detail me-2"></i> Comments</h6>

                <div class="col-12" >

                    <div class="pb-2 mb-2 store-metrics" id="commentsList${raw.id}">
                        ${commentHtml}
                    </div>

                </div>

                <div class="col-12">
                    <div class="mt-1 row g-1">
                        <div class="col-md-12">
                            <textarea type="text" row="1" name="rts_comment_text[]" id="rts_comment_text_${raw.id}" class="form-control" placeholder="Write a comment" autocomplete="off"></textarea>
                        </div>
                        <div class="col-md-12 pe-3">

                            <button id="rts_comment_send_btn_${raw.id}" 
                                class="px-3 btn btn-sm btn-primary" 
                                onclick="rtsCommentSend(${raw.id})"
                            >
                                <i class="fa fa-paper-plane me-2"></i>Send
                            </button>

                            <button class="btn btn-sm btn-default ms-auto document_filename_link"
                                onclick="rtsCommentAttach(${raw.id})" 
                                title="Attach file(s) to send on comment"
                            >
                                <i class="fa fa-paperclip"></i>
                            </button>

                            <input type="file" class="edit_rts_modal_upload_documents"
                                id="edit_rts_modal_upload_documents_${raw.id}" 
                                name="files[]" multiple 
                                onchange="changeFileAttachments(${raw.id}, event)"
                            hidden />

                            <span id="comment_attachment_chip_span_${raw.id}"  
                                title="See Comment Attachment(s)"
                            ></span>

                        </div>
                    </div>
                </div>
            </div>
        `;

        return html;
    }


    // Comment functions -----------------------------------------------------------//

    function rtsCommentSend(rts_id)
    {
        var formData = new FormData();
        let data = {
            comment: $('#rts_comment_text_'+rts_id).val(),
            rts_id: rts_id
        };
        formData.append("data", JSON.stringify(data));

        var uploadFiles = $('#edit_rts_modal_upload_documents_'+rts_id).get(0).files;
        
        for (let i = 0; i < uploadFiles.length; i++) {
            formData.append("files[]", uploadFiles[i]);
            var kbSize = uploadFiles[i].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }

        $('#rts_comment_send_btn_'+rts_id).prop('disabled', true);
        $('#rts_comment_send_btn_'+rts_id).html(`<i class="fa-solid fa-spinner me-2"></i>Sending...`)

        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `rts/comment/store`,
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(res) {
                $('#rts_comment_text_'+rts_id).val('');
                $('#rts_comment_send_btn_'+rts_id).prop('disabled', false);
                $('#rts_comment_send_btn_'+rts_id).html(`<i class="fa fa-paper-plane me-2"></i>Send`);

                resetCommentChipAttachment(rts_id);
                
                incrementComment(res.data);
                
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

    function resetCommentChipAttachment(rts_id)
    {
        $('#edit_rts_modal_upload_documents_'+rts_id).val(null);
        $('#comment_attachment_chip_span_'+rts_id).empty();
    }

    function rtsCommentAttach(rts_id)
    {
        $('#edit_rts_modal_upload_documents_'+rts_id).click();
    }

    function getTodayDayDifference(givenDateStr)
    {
        var givenDateParts = givenDateStr.split('-');
        var givenDate = new Date(givenDateParts[0], givenDateParts[1] - 1, givenDateParts[2]);
        var today = new Date();
        var todayPSTStr = convertToPSTAndFormat(today);
        var todayParts = todayPSTStr.split('-');
        var todayPST = new Date(todayParts[0], todayParts[1] - 1, todayParts[2]);
        var daysDifference = getDaysDifference(givenDate, todayPST);
        return daysDifference;
    }

    function incrementComment(data) {
        const comment = data.comment;
        const employee = data.employee;
        const files = data.files;
        const formatted_pst_created_at = data.formatted_pst_created_at;

        const auth_emp_id = {{ $authEmployee->id }};

        let commentHtml = '';

        let comment_employee = employee ?? null;
        
        let comment_created_at = formatted_pst_created_at;
        
        var comment_employee_firstname = comment_employee['firstname'];
        var comment_employee_lastname = comment_employee['lastname'];
        let comment_employee_fullname = `${comment_employee_firstname} ${comment_employee_lastname}`;
        let comment_employee_initials = comment_employee_firstname.charAt(0) +''+ comment_employee_lastname.charAt(0);
        comment_employee_initials = comment_employee_initials.toUpperCase();

        let comment_documents = files ?? [];

        let comment_document_list = '';
        $.each(comment_documents, function(d, document) {
            let document_path = document.path;

            let s3_url = document.s3_url;

            if(document.ext == 'png' || document.ext == 'jpeg' || document.ext == 'jpg' || document.ext == 'ico') {
                comment_document_list += `<li class="list-group-item document_filename_link text-primary" id="rts_comment_document_li_${document.id}">
                    <a class="text-primary" href="${s3_url}" target="_blank">
                        <div class="mb-2 image-container">
                            <img src="${s3_url}" alt="${document.name}" title="${document.name}" class="responsive-img">
                        </div>
                        <i class="fa fa-paperclip me-2"></i>${document.name}
                    </a>
                </li>`;
            } else {
                comment_document_list += `<li class="list-group-item document_filename_link text-primary" id="rts_comment_document_li_${document.id}">
                    <a class="text-primary" href="${s3_url}" target="_blank">
                        <i class="fa fa-paperclip me-2"></i>${document.name}
                    </a>
                </li>`;
            }

        });

        if(comment_document_list != '')
        {
            comment_document_list = `<ul class="mb-2 list-group">${comment_document_list}</ul>`;
        }

        let border_color = (auth_emp_id == comment_employee['id']) ? 'ticket_comment_section_body_row_card' : 'ticket_comment_section_body_row_card2';

        let rts_comment_section_cols = ``;
        let comment_description =  comment.comment.replace(/\n/g, '<br>');

        if(comment_employee['image'] != '' && comment_employee['image'] != null) {
            rts_comment_section_cols = `
                <div class="mt-3 col-md-12">
                    <div class="card mb-0 ${border_color}">
                        <div class="card-body">
                            <div class="d-flex">
                                <img src="/upload/userprofile/${comment_employee['image']}" width="35" height="35" class="rounded-circle image-has-border" alt="">
                                <div class="flex-grow-1 ms-3">
                                    <p id="show-assign-to-fullname" class="mb-0 font-weight-bold font-11">
                                        <b>${comment_employee_fullname}</b>
                                    </p>
                                    <span style="font-size: 12px; color: gray;">${comment_created_at}</span>
                                </div>
                            </div>
                            <div class="mt-1">
                                ${comment_description}
                                ${comment_document_list}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            rts_comment_section_cols = `
                <div class="mt-3 col-md-12">
                    <div class="card mb-0 ${border_color}">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="employee-avatar-${comment_employee['initials_random_color']}-initials hr-employee" style="width: 35px !important; height: 35px !important; font-size: 13px !important;">
                                    ${comment_employee_initials}
                                </div>
                                <div class="font-weight-bold ms-3 font-11">
                                    <p class="mb-0"><b>${comment_employee_fullname}</b></p>
                                    <span style="font-size: 12px; color: gray;">${comment_created_at}</span>
                                </div>
                            </div>
                            <div class="mt-1">
                                ${comment_description}
                                ${comment_document_list}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        commentHtml = rts_comment_section_cols;

        $(`#commentsList${comment.operation_rts_id}`).append(commentHtml);

        setTimeout(function() {
            $(`#commentsList${comment.operation_rts_id}`).animate({
                scrollTop: $(`#commentsList${comment.operation_rts_id}`).prop("scrollHeight")
            }, 500);
        }, 300);
    }
</script>