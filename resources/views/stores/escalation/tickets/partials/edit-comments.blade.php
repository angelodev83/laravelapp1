<div class="col-lg-4 col-md-12 col-sm-12" style="background-color: #f0f0f0 !important;">
    <div id="ticket_comment_section_header" style="background-color: white !important; border-bottom: 1px solid #c1c1c1;">
        <div class="p-2 row">
            <div class="col-md-12">
                <h6 class="ms-2">Comments<i class="fa-regular fa-comment-dots ms-2"></i></h6>
            </div>
        </div>
    </div>
    <div id="ticket_comment_section_body">
        <div class="row g-3 ps-2" id="ticket_comment_section_body_row">
            
        </div>
    </div>
    <div id="ticket_comment_section_footer" style="background-color: #f0f0f0 !important; border-top: 1px solid #c1c1c1;">
        <div class="px-2 mt-1 row g-1">
            <div class="col-md-12">
                <textarea type="text" name="ticket_comment_text" id="ticket_comment_text" class="form-control" placeholder="Write a comment" autocomplete="off"></textarea>
            </div>
            <div class="col-md-12 pe-3">
                <button id="ticket_comment_send_btn" class="px-3 btn btn-sm btn-primary" onclick="ticketCommentSend(event)"><i class="fa fa-paper-plane me-2"></i>Send</button>
                <button class="btn btn-sm btn-default ms-auto document_filename_link" onclick="ticketCommentAttach(event)" title="Attach file(s) to send on comment"><i class="fa fa-paperclip"></i></button>
                <input type="file" id="edit_ticket_modal_upload_documents" name="files[]" multiple hidden>
                <span id="comment_attachment_chip_span"  title="See Comment Attachment(s)">
                    
                </span>
            </div>
        </div>
    </div>
</div>

<script>

    function ticketCommentSend(event)
    {
        event.preventDefault();
        var formData = new FormData();
        let data = {
            comment: $('#ticket_comment_text').val(),
            ticket_id: $("#eid").val(),
            pharmacy_store_id: menu_store_id,
        };
        formData.append("data", JSON.stringify(data));

        var uploadFiles = $('#edit_ticket_modal_upload_documents').get(0).files;
        
        for (let i = 0; i < uploadFiles.length; i++) {
            formData.append("files[]", uploadFiles[i]);
            var kbSize = uploadFiles[i].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }

        $('#ticket_comment_send_btn').prop('disabled', true);
        $('#ticket_comment_send_btn').html(`<i class="fa-solid fa-spinner me-2"></i>Sending...`)

        // sweetAlertLoading();
          $.ajax({
              //laravel requires this thing, it fetches it from the meta up in the head
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "POST",
              url: `/store/escalation/tickets/store-comment`,
              data: formData,
              contentType: false,
              processData: false,
              dataType: "json",
              success: function(res) {
                  $('#ticket_comment_text').val('');
                  const data = res.data;
                    if(data == '' || data == null || data == undefined) {
                        return;
                    }
                    const comment = data.comment;
                    const employee = data.employee;
                    const comment_date = currentDateToYMDHMS(comment.created_at, 'M d, Y H:i A');//data.formatted_created_at;
                    let comment_documents = data.files;

                    var comment_employee_firstname = employee['firstname'];
                    var comment_employee_lastname = employee['lastname'];
                    let comment_employee_fullname = `${comment_employee_firstname} ${comment_employee_lastname}`;
                    let comment_employee_initials = comment_employee_firstname.charAt(0) +''+ comment_employee_lastname.charAt(0);
                    comment_employee_initials = comment_employee_initials.toUpperCase();

                    let comment_document_list = '';
                    $.each(comment_documents, function(d, document) {
                        let document_path = document.path;

                        // Split the string by slash '/'
                        let segments = document_path.split('/');

                        // Get the last segment (last string after the last '/')
                        let lastSegment = segments[segments.length - 1];

                        // comment_document_list += `<li class="list-group-item"><i class="fa fa-paperclip me-2"></i>${document}</li>`;
                        comment_document_list += `<li class="list-group-item document_filename_link text-primary" id="ticket_comment_document_li_${document.id}" title="Download ${lastSegment}" onclick="clickDownloadDocument(event, ${document.id}, '${document.path}')"><i class="fa fa-paperclip me-2"></i>${lastSegment}</li>`;

                        const ext = document.ext.toLowerCase();
                        const filename = getLastStringBySlash(document.path);
                        const fileIcon = fileUtil(ext, 'icon');
                        const fileClass = fileUtil(ext, 'class');
                        $('#attachment_item_tbody').append(
                            `<tr id="attachment_item_tbody_tr_${document.id}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div><i class="bx ${fileIcon} me-2 font-24 ${fileClass}"></i></div>
                                        <div class="font-weight-bold"><p class="document_filename_link" onclick="clickDownloadDocument(event, ${document.id}, '${document.path}')" title="Download #${filename}">${filename}</p></div>
                                    </div>
                                </td>
                                <td>${currentDateToYMDHMS(document.created_at, 'M d, Y H:i A')}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger" title="Delete ${filename}" onclick="clickDeleteDocumentBtn(event, ${document.id})"><i class="fa fa-trash-can"></i></button>
                                </td>
                            </tr>`
                        );  
                        $('.no_attachment_tr').remove(); 
                    });

                    if(comment_document_list != '')
                    {
                        comment_document_list = `<ul class="mb-2 list-group">${comment_document_list}</ul>`;
                    }


                    let ticket_comment_section_cols = ``;

                    let comment_description =  comment.comment.replace(/\n/g, '<br>');

                    if(employee['image'] != '' && employee['image'] != null) {
                        ticket_comment_section_cols = `
                            <div class="mt-3 col-md-12">
                                <div class="mb-0 card ticket_comment_section_body_row_card">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <img src="/upload/userprofile/${employee['image']}" width="45" height="45" class="shadow rounded-circle" alt="">
                                            <div class="flex-grow-1 ms-3">
                                                <p id="show-assign-to-fullname" class="mb-0 font-weight-bold font-12">
                                                    <b>${comment_employee_fullname}</b>
                                                </p>
                                                <small>${comment_date}</small>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            ${comment_document_list}
                                            ${comment_description}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        ticket_comment_section_cols = `
                            <div class="mt-3 col-md-12">
                                <div class="mb-0 card ticket_comment_section_body_row_card">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="employee-avatar-${employee['initials_random_color']}-initials hr-employee" style="width: 45px !important; height: 45px !important; font-size: 20px !important;">
                                                ${comment_employee_initials}
                                            </div>
                                            <div class="font-weight-bold ms-3 font-12">
                                                <p class="mb-0"><b>${comment_employee_fullname}</b></p>
                                                <small>${comment_date}</small>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            ${comment_document_list}
                                            ${comment_description}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    $('#ticket_comment_section_body_row').append(ticket_comment_section_cols);  
                    $('#ticket_comment_send_btn').prop('disabled', false);
                    $('#ticket_comment_send_btn').html(`<i class="fa fa-paper-plane me-2"></i>Send`);

                    resetCommentChipAttachment();

                    var container = $('#ticket_comment_section_body');
                        container.animate({
                        scrollTop: container.prop("scrollHeight")
                    }, 500);

                    reloadDataTable();
                    // reloadAttachmentsDatatable();
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

    function resetCommentChipAttachment()
    {
        $('#edit_ticket_modal_upload_documents').val(null);
        $('#comment_attachment_chip_span').empty();
    }

    function ticketCommentAttach(event)
    {
        event.preventDefault();
        $('#edit_ticket_modal_upload_documents').click();
    }

</script>