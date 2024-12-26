<style>
  .custom-modal-fullsize {
    max-width: 97%;
    width: 97%;
  }
  
  /* #assignees, span {
      display: inline-block; 
  } */

  td {
    text-transform: none; /* Ensure text case is not transformed */
  }

  .scrollable-content {
    width: 100%; /* Adjust width as needed */
    height: 500px; /* Adjust height as needed */
    overflow-y: auto;
    /* border: 1px solid #ccc; */
    padding: 10px;
  }

  .commentary-section {
    width: 100%; /* Adjust width as needed */
    height: 410px; /* Adjust height as needed */
    overflow-y: auto;
    /* border: 1px solid #ccc; */
    padding: 10px;
  }

  #comment-container{
    padding: 10px !important;
    background: white;
    box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;
  }

  .date{
    font-size: 12px;
  }

  .comment-text{
    font-size: 14px;
    line-height: 1.2rem;
  }

  .fs-14{
    font-size: 14px;
  }

  .name{
    color: #212529;
  }

  .cursor{
    cursor: pointer;
  }

  .cursor:hover{
    color: blue;
  }
  
</style>
<div class="modal " id="edit_modal" style="display:none;" tabindex="-1">
  <div class="modal-dialog custom-modal-fullsize">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title"></h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-8 col-md-12">
            <div class="scrollable-content">
              <div class="container">
                <input type="hidden" id="id" name="id">
                <input type="hidden" id="task_id" name="task_id">
                <div class="col-12">
                    <div class="mb-3">
                        <label label for="task"  class="form-label">Status</label>
                        <div class="gap-2 d-grid" id="selected_status"></div>
                    </div>
                </div>
                <div class="col-12">
                  <label label for="task"  class="form-label">Assignees</label>
                  
                  <div class="chip chip-lg form-control" id="add_assignee">
                    <span id="assignees"></span>
                    <span class="closebtn"><i class="fa-solid fa-user-plus"></i></span>
                  </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label label for="task"  class="form-label">Due Date</label>
                        <input type="text" readonly class="form-control" id="due_date">
                    </div>
                </div>
                <div class="col-12">
                  <div class="table-responsive">
                    <table class="table table-bordered ">
                      
                      <tbody>
                        <tr>
                          <td width="40%" class="fw-bold">First Name</td>
                          <td width="60%" class="td-editable" id="firstname"></td>
                        </tr>
                        <tr>
                          <td width="40%" class="fw-bold">Last Name</td>
                          <td width="60%" class="td-editable" id="lastname"></td>
                        </tr>
                        <tr>
                          <td width="40%" class="fw-bold">Address</td>
                          <td width="60%" class="td-editable" id="home_address"></td>
                        </tr>
                        <tr>
                          <td width="40%" class="fw-bold">City</td>
                          <td width="60%" class="td-editable" id="city"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">State</td>
                          <td class="td-editable" id="state"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">County</td>
                          <td class="td-editable" id="county"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Zip</td>
                          <td class="td-editable" id="zip"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">CB#</td>
                          <td class="td-editable" id="phone_number"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Email</td>
                          <td class="td-editable" id="email"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Gender</td>
                          <td class="td-editable" id="gender"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Medication Info</td>
                          <td class="td-editable" id="medication"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Affiliation</td>
                          <td class="td-editable" id="affiliated"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Preferred Communication</td>
                          <td class="td-editable" id="communication"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Date of Birth</td>
                          <td class="td-editable" id="birthdate"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Current Pharmacy</td>
                          <td class="td-editable" id="current_pharmacy"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Pharmacy Phone #</td>
                          <td class="td-editable" id="pharmacy_phone_number"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Pharmacy Address</td>
                          <td class="td-editable" id="pharmacy_address"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Pharmacy City</td>
                          <td class="td-editable" id="pharmacy_city"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Pharmacy State</td>
                          <td class="td-editable" id="pharmacy_state"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Pharmacy Zip</td>
                          <td class="td-editable" id="pharmacy_zip"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Prescriber First Name</td>
                          <td class="td-editable" id="prescriber_firstname"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Prescriber Last Name</td>
                          <td class="td-editable" id="prescriber_lastname"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Prescriber Phone#</td>
                          <td class="td-editable" id="prescriber_phone_number"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Prescriber Fax#</td>
                          <td class="td-editable" id="prescriber_fax_number"></td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Notes</td>
                          <td class="td-editable" id="notes"></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              @can('menu_store.patient_support.transfer_rx.upload')
              <div class="container">
                <h4>Attachment(s)</h4>
                <div class="col-md-12">
                  <form action="" method="POST" id="#editFileForm" enctype="multipart/form-data">
                    <div class="mt-3" id="files_uploaded">
                    </div>
                    <label for="file" class="form-label">File</label>
                    <div id="fileDropArea" class="p-5 text-center border d-flex border-3 align-items-center justify-content-center">
                        <span class="fw-bold lead" id="droparea_text"></span>
                    </div>
                    <input type="file" name="file[]" class="form-control" id="file" multiple>
                  </form>
                </div>
              </div> 
              @endcan  
            </div>
          </div>
          <div class="col-lg-4 col-md-12">
            <h4>Comments</h4>
            <div class="commentary-section">
              
              <!-- <div class="p-1 mb-1 bg-white rounded">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-12">
                        <div class="d-flex flex-column" id="comment-container">
                            <div class="bg-white">
                                <div class="flex-row d-flex">
                                    <div src="" width="40" class="rounded-circle user-avatar-initials"></div>
                                    <div class="ml-2 d-flex flex-column justify-content-start">
                                    <span class="d-block font-weight-bold name">Wonder Woman</span>
                                    <span class="date text-black-50">Public - 09Jun, 2021</span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <p class="comment-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed facilisis velit lorem, et condimentum est tempus sed. Integer tristique malesuada diam at mollis. Quisque id finibus mauris. Donec turpis justo, euismod nec commodo quis, elementum nec risus. Praesent blandit in lacus sed pretium. Duis in velit augue. Integer velit urna, convallis eget fermentum sed, aliquet at quam.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div> -->
            </div>
            @can('menu_store.patient_support.transfer_rx.comment')
            <textarea class="form-control" id="commentInput" rows="3" placeholder="Type your comment here..."></textarea>
            @endcan
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>

 
  function showEditModal(data, menu_store_id) {
    let id = $(data).data('id');
    let taskId = $(data).data('taskid');
    let taskStatusId = $(data).data('taskstatusid');
    let logsId = $(data).data('logsid');
    let firstname = $(data).data('firstname');
    let lastname = $(data).data('lastname');
    let assignees = $(data).data('assignees');
    let dueDate = $(data).data('duedate');
    let userFirstname = $(data).data('userfirstname');
    let userLastname = $(data).data('userlastname');
    let userId = $(data).data('userid');
    // Get the current URL
    var currentUrl = window.location.href;
    // Split the URL by '/' to get individual segments
    var segments = currentUrl.split('/');
    // Find the segment containing the desired value (in this case, the segment at index 4)
    var transferListId = segments[7];

    // Get the textarea element
    const commentInput = document.getElementById('commentInput');
    // Get the comment container
    const commentContainer = document.getElementById('comment-container');

    $('#edit_modal').modal('show');
    $('#edit_modal .modal-title').text(firstname+' '+lastname);
    $('#edit_modal #assignees').text(assignees);
    $('#edit_modal #due_date').val(dueDate);

    @php
      if(Auth::user()->can('menu_store.patient_support.transfer_rx.comment')) {
    @endphp
    // comment
    function handleEnterKeyPress(event) {
      // Check if Enter is pressed without the Shift key
      if (event.key === 'Enter' && !event.shiftKey) {
        // Prevent default behavior of Enter key (form submission)
        event.preventDefault();

        // Get the value of the textarea
        const commentText = commentInput.value.trim();

        // Check if the textarea is not empty
        if (commentText !== '') {
          // Get current date and time
          const currentDate = new Date().toLocaleString('en-US', { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' });
          
          // Create a new comment card element
          const commentCard = document.createElement('div');
          commentCard.classList.add('bg-white', 'rounded', 'p-1', 'mb-1', 'comment-card');

          // Construct the comment card HTML structure
          commentCard.innerHTML = `
            <div class="row d-flex justify-content-center">
                <div class="col-md-12">
                    <div class="d-flex flex-column" id="comment-container">
                        <div class="bg-white">
                            <div class="flex-row d-flex">
                              @if(isset($authEmployee->id))
                                  @if(!empty($authEmployee->image))
                                      <img src="/upload/userprofile/{{$authEmployee->image}}" class="user-img" alt="user avatar">
                                  @else
                                      <div class="col-auto">
                                          <div class="user-avatar-initials">
                                              {{ strtoupper(substr($authEmployee->firstname, 0, 1)) }}{{ strtoupper(substr($authEmployee->lastname, 0, 1)) }}
                                          </div>
                                      </div>
                                  @endif
                              @else
                                  <div class="col-auto">
                                      <div class="user-avatar-initials">
                                          {{ strtoupper(substr($authEmployee->firstname, 0, 1)) }}{{ strtoupper(substr($authEmployee->lastname, 0, 1)) }}
                                      </div>
                                  </div>
                              @endif
                                <div class="ml-2 d-flex flex-column justify-content-start">
                                <span class="d-block font-weight-bold name ms-2"> ${userFirstname+' '+userLastname}</span>
                                <span class="date text-black-50 ms-2"> ${currentDate}</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p class="comment-text">${commentText}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          `;

          // Append the comment card to the container
          const container = document.querySelector('.commentary-section');
          container.appendChild(commentCard);

          // Scroll to the bottom of the container
          container.scrollTop = container.scrollHeight;

          //ajax
          
          //sweetAlertLoading();
          $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/comment_store`,
            data: JSON.stringify({comment: commentText, logs_id: logsId, status_id: taskStatusId, today: currentDate}),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
              console.log(data);
              
            },error: function(msg) {
              handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }
          });

          // Clear the textarea
          commentInput.value = '';
        }
      }
    }

    commentInput.addEventListener('keydown', handleEnterKeyPress);
    @php
      }
    @endphp
    
    $('#assignee').select2( {
      theme: "bootstrap-5",
      width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
      placeholder: $( this ).data( 'placeholder' ),
      closeOnSelect: true,
      dropdownParent: $('#default_assignee_modal .modal-content'),
		});

    //getting data
    $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: "GET",
        url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/get_patient_data`,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        data: {
            id: id,
            task_id: taskId,
            list_id: transferListId,
        },
        success: function(result){
          result.task.forEach(function(task) {
            if(task.id === taskId){
              $('#edit_modal #selected_status').append('<button class="'+task.class+'" style="background-color:'+task.color+'; color:'+task.text_color+';" data-task-id="' + task.id + '">' + task.name + '</button>');
            }
            $('#edit_modal #firstname').text(result.task_patient.firstname);
            $('#edit_modal #lastname').text(result.task_patient.lastname);
            $('#edit_modal #home_address').text(result.task_patient.home_address);
            $('#edit_modal #city').text(result.task_patient.city);
            $('#edit_modal #state').text(result.task_patient.state);
            $('#edit_modal #county').text(result.task_patient.county);
            $('#edit_modal #affiliated').text(result.task_patient.affiliated);
            $('#edit_modal #zip').text(result.task_patient.zip);
            $('#edit_modal #phone_number').text(result.task_patient.phone_number);
            $('#edit_modal #email').text(result.task_patient.email);
            $('#edit_modal #communication').text(result.task_patient.communication);
            $('#edit_modal #birthdate').text(result.task_patient.birthdate);
            $('#edit_modal #current_pharmacy').text(result.task_patient.current_pharmacy);
            $('#edit_modal #pharmacy_phone_number').text(result.task_patient.pharmacy_phone_number);
            $('#edit_modal #pharmacy_address').text(result.task_patient.pharmacy_address);
            $('#edit_modal #pharmacy_city').text(result.task_patient.pharmacy_city);
            $('#edit_modal #pharmacy_state').text(result.task_patient.pharmacy_state);
            $('#edit_modal #pharmacy_zip').text(result.task_patient.pharmacy_zip);
            $('#edit_modal #prescriber_firstname').text(result.task_patient.prescriber_firstname);
            $('#edit_modal #prescriber_lastname').text(result.task_patient.prescriber_lastname);
            $('#edit_modal #prescriber_phone_number').text(result.task_patient.prescriber_phone_number);
            $('#edit_modal #prescriber_fax_number').text(result.task_patient.prescriber_fax_number);
            $('#edit_modal #gender').text(result.task_patient.gender);
            $('#edit_modal #medication').css('white-space', 'pre-wrap').html(result.task_patient.medication_details);
            $('#edit_modal #notes').css('white-space', 'pre-wrap').html(result.task_patient.notes);

          });

          result.comments.forEach(function(comment){
            let currentDate = comment.created_at;
             

            // Parse the date string into a Date object
            let date = new Date(currentDate);

            // Adjust to your timezone offset (e.g., PST is UTC-8)
            date.setHours(date.getHours() - 8); // Adjust for UTC-8 timezone

            // Get the month abbreviation
            let monthAbbreviation = date.toLocaleString('default', { month: 'short' });

            // Get the day, year, hour, and minute
            let day = date.getDate();
            let year = date.getFullYear();
            let hour = date.getHours();
            let minute = date.getMinutes();

            // Determine if it's AM or PM
            let period = hour >= 12 ? 'PM' : 'AM';

            // Adjust the hour to 12-hour format
            hour = hour % 12 || 12;

            // Format the date
            let formattedDate = `${monthAbbreviation} ${day}, ${year} ${hour}:${minute.toString().padStart(2, '0')} ${period}`;

            // Create a new comment card element
            let commentCard = document.createElement('div');
            commentCard.classList.add('bg-white', 'rounded', 'p-1', 'mb-1', 'comment-card');
            let commentName = comment.firstname+' '+comment.lastname;

            let comment_description =  comment.comment.replace(/\n/g, '<br>');
            // Construct the comment card HTML structure
            commentCard.innerHTML = `
              <div class="row d-flex justify-content-center comment-card">
                  <div class="col-md-12">
                      <div class="d-flex flex-column" id="comment-container">
                          <div class="bg-white">
                              <div class="flex-row d-flex">
                                  
                                  @if(isset($authEmployee->id))
                                      @if(!empty($authEmployee->image))
                                          <img src="/upload/userprofile/{{$authEmployee->image}}" class="user-img" alt="user avatar">
                                      @else
                                          <div class="col-auto">
                                              <div class="user-avatar-initials">
                                                  {{ strtoupper(substr($authEmployee->firstname, 0, 1)) }}{{ strtoupper(substr($authEmployee->lastname, 0, 1)) }}
                                              </div>
                                          </div>
                                      @endif
                                  @else
                                      <div class="col-auto">
                                          <div class="user-avatar-initials">
                                              {{ strtoupper(substr($authEmployee->firstname, 0, 1)) }}{{ strtoupper(substr($authEmployee->lastname, 0, 1)) }}
                                          </div>
                                      </div>
                                  @endif
                                  <div class="ml-2 d-flex flex-column justify-content-start">
                                  <span class="d-block font-weight-bold name ms-2"> ${commentName}</span>
                                  <span class="date text-black-50 ms-2"> ${formattedDate}</span>
                                  </div>
                              </div>
                              <div class="mt-3">
                                  <p class="comment-text">${comment_description}</p>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
            `;

            // Append the comment card to the container
            const container = document.querySelector('.commentary-section');
            container.appendChild(commentCard);

            // Scroll to the bottom of the container
            container.scrollTop = container.scrollHeight;

          });
          
          result.files.forEach(function(file) {
            let parts = file.filename.split('_'); // Split the filename by underscore
            let newFilename = parts.slice(1).join('_'); // Join the parts except the first one using underscore
            
            $("#edit_modal #files_uploaded").append('<div class="chip chip-lg form-control chip_file delete_file_'+file.id+'"><a href="/store/patient-support/'+menu_store_id+'/transfer_rx/tribe_members/file_download/'+file.id+'" class="">'+newFilename+'</a><span class="closebtn" onclick="removeFile('+file.id+');">×</span></div>');
          });

          
        },
        error: function(msg) {
          handleErrorResponse(msg);
        }
    });

    //open change-task
    $('#edit_modal #selected_status').off('click').on('click', function(e) {
        // Prevent the default form submission behavior
        e.preventDefault();
        // Call the ShowChangeTaskModal function with the necessary parameters
        ShowChangeTaskModal(id, taskId);
    });

    //open add-assignee
    $('#edit_modal #add_assignee').off('click').on('click', function(e) {
        showAssigneeModal(id, taskId, taskStatusId);
    });

    @php
      if(Auth::user()->can('menu_store.patient_support.transfer_rx.update')) {
    @endphp
    //editable table upon click
    $('#edit_modal .td-editable').off('click').on('click', function() {
      let cell = $(this);

      // Check if the cell is already in edit mode
      if (!cell.hasClass('edit-mode')) {
        let currentValue = cell.text();
        let leftFieldText = cell.closest('tr').find('td:first').text(); // Get the ID from the first column of the current row
        let idName = cell.attr('id');

        // // Replace the cell content with an input field
        // let inputField = $('<input type="text" class="form-control" value="' + currentValue + '">');
        // cell.html(inputField);

        let inputField;

        // Check if idName is 'notes', then create a textarea, otherwise create an input field
        if(idName === 'notes') {
            inputField = $('<textarea class="form-control" rows="5">' + currentValue + '</textarea>');
        }
        else if(idName === 'birthdate') {
            let inputText = $('<input type="text" class="form-control" value="' + currentValue + '">');
            let spanElement = $('<span class="text-muted ms-3" style="font-size: 11.5px;">YYYY-MM-DD</span>');

            // Create a container element (div) to wrap both the input field and the span
            inputField = $('<div></div>').append(inputText).append(spanElement);
        }
        else if(idName === 'medication'){
          showMedicationForm(id, taskId);
          return;
        }
        else{
            inputField = $('<input type="text" class="form-control" value="' + currentValue + '">');
        }

        // Replace the cell content with the input field
        cell.html(inputField);

        // Focus on the input field
        inputField.focus();

        // Add a class to mark the cell as in edit mode
        cell.addClass('edit-mode');

        // Handle blur event on the input field
        let eventHandled = false;
        inputField.off('blur keypress').on('blur keypress', function(event) {
          if (eventHandled) {
              return; // Exit if the event has already been handled
          }
          if ((event.type === 'blur') || ((event.which === 13 || event.keyCode === 13) && !event.shiftKey && event.type === 'keypress')) {
            event.preventDefault();
            eventHandled = true; // Set the flag to true to indicate that the event has been handled
        
            let newValue = inputField.val().trim();

            if(newValue === currentValue){
                cell.text(newValue);
            }
            else{
                sweetAlertLoading();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "PUT",
                    url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/update_clicked_column`,
                    data: JSON.stringify({
                        task_log_id: logsId,
                        column_name: idName,
                        value: newValue
                    }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data) {
                        if(data.errors) {
                          cell.text(currentValue);
                          sweetAlert2(data.status, data.errors);
                        } 
                        else {
                          if(idName === 'notes'){
                            cell.css('white-space', 'pre-wrap').html(newValue);
                          }
                          else{
                            cell.text(newValue);
                          }
                          tableTasks['table_task_' + taskId].ajax.reload(null, false);
                          Swal.close();
                          //sweetAlert2(data.status, data.message);
                        }
                    },
                    error: function(data) {
                      handleErrorResponse(data);
                        console.log("Error");
                        console.log(data.responseText);
                    },
                    complete: function() {
                        eventHandled = false; // Reset the flag after the AJAX request is complete
                    }
                });
            }

            // Remove the edit-mode class to allow editing again
            cell.removeClass('edit-mode');
            event.stopPropagation();
          }
            
        });
      }
    });
    @php
      }
    @endphp

    @php
      if(Auth::user()->can('menu_store.patient_support.transfer_rx.update')) {
    @endphp
    //edit duedate
    $('#edit_modal #due_date').off('click').on('click', function() {
      let div = $(this);
      
      if (!div.hasClass('edit-mode')) {
        let currentValue = div.val().trim();
        let idName = div.attr('id');

        let datePicker = new tempusDominus.TempusDominus(div[0], {
          useCurrent: false,
          stepping: 1,
          display: {
              viewMode: 'calendar',
              components: {
                  clock: false,
                  hours: false,
                  minutes: false,
                  seconds: false,
                  useTwentyfourHour: undefined
              },
          },
          localization: {
              format: 'MMM dd, yyyy', // Modified format
          }
        });

        // Manually show the datepicker
        datePicker.show();

        // Add a class to mark the cell as in edit mode
        div.addClass('edit-mode');
        div.off('change').on('change', function() {
          let newValue = div.val().trim();
          
          if (newValue === currentValue){
              div.text(newValue);
          }
          else {
              sweetAlertLoading();
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  type: "PUT",
                  url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/update_clicked_column`,
                  data: JSON.stringify({
                      task_log_id: logsId,
                      column_name: idName,
                      value: newValue
                  }),
                  contentType: "application/json; charset=utf-8",
                  dataType: "json",
                  success: function(data) {
                      if (data.errors) {
                        div.text(currentValue);
                        sweetAlert2(data.status, data.errors);
                      } else {
                        div.text(newValue);
                        tableTasks['table_task_' + data.task_to].ajax.reload(null, false);
                        Swal.close();
                        //sweetAlert2(data.status, data.message);
                      }
                  },
                  error: function(data) {
                    handleErrorResponse(data);
                      console.log("Error");
                      console.log(data.responseText);
                  }
              });
          }

          // Remove the edit-mode class to allow editing again
          div.removeClass('edit-mode');
        });
      }
    });
    @php
      }
    @endphp
    
    
    $("#edit_modal #file").hide();
    $('#edit_modal .file_title').remove();
    $('#edit_modal #droparea_text').append('<i style="font-size: 50px;" class="file_title fw-bold lead bx bx-cloud-upload"></i><p class="file_title">DROP FILE OR CLICK TO UPLOAD FILE</p>');
    
    $('#edit_modal #fileDropArea').on('dragover', function (e) {
        e.preventDefault();
        $(this).css('border-style', 'dotted'); // Change border style to dotted on dragover
        $(this).css('background', '#eee'); // Change border style to dotted on dragover
        e.stopPropagation();
    }).on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        $(this).css('background', '#fff'); // Change border style to dotted on dragover
        e.stopPropagation();
    });

    $('#edit_modal #fileDropArea').on('dragenter', function (e) {
        e.preventDefault();
        e.stopPropagation();
    })

    $('#edit_modal #fileDropArea').off('drop').on('drop', function (e) {
      e.preventDefault();
      // Get a reference to our file input
      const fileInput = document.querySelector('#file');

      var files = e.originalEvent.dataTransfer.files;

      
      // $('#edit_modal #droparea_text').text('' + file[0].name + '');
      // //transfer file in input file
      // const dataTransfer = new DataTransfer();
      // dataTransfer.items.add(file[0]);
      // fileInput.files = dataTransfer.files;

      // Update drop area text with file names
      var fileNames = [];
      for (var i = 0; i < files.length; i++) {
          fileNames.push(files[i].name);
      }
      $('#edit_modal #droparea_text').text(fileNames.join(', '));

      // Create a new DataTransfer object to transfer files to the input file
      const dataTransfer = new DataTransfer();
      // Add all dropped files to the DataTransfer object
      for (var i = 0; i < files.length; i++) {
          dataTransfer.items.add(files[i]);
      }
      // Set the files from the DataTransfer object to the input file
      fileInput.files = dataTransfer.files;


      // Trigger change event manually after setting the file
      $(fileInput).trigger('change');
    });

    // $('#edit_modal #file').change(function (){
    //     $('#edit_modal #droparea_text').text('' + $('#file')[0].files[0].name + '');
    // });
    $('#edit_modal #file').off('change').on('change', function() {
      var files = $('#file')[0].files;
      
      // Check if files is not null before proceeding
      if (files !== null && files.length > 0) {
          // Update drop area text with file names
          var fileNames = [];
          for (var i = 0; i < files.length; i++) {
              fileNames.push(files[i].name);
          }
          $('#edit_modal #droparea_text').text(fileNames.join(', '));

          // Create FormData object
          var formData = new FormData();
          
          // Append files to FormData
          for (var i = 0; i < files.length; i++) {
              formData.append('files[]', files[i]);
          }

          // Append other data as needed
          formData.append('status_id', taskStatusId);

          sweetAlertLoading();
          // Send AJAX request
          $.ajax({
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "POST",
              url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/file_upload`,
              data: formData,
              contentType: false,
              processData: false,
              success: function(data) {
                  //console.log(data);
                if(data.errors){
                  $.each(data.errors,function (key , val){
                      sweetAlert2('warning', data.message);
                  });
                }
                else{
                  data.files_save.forEach(function(file) {
                    let parts = file.filename.split('_'); // Split the filename by underscore
                    let newFilename = parts.slice(1).join('_'); // Join the parts except the first one using underscore
                    
                    $("#edit_modal #files_uploaded").append('<div class="chip chip-lg form-control chip_file delete_file_'+file.id+'"><a href="/store/patient-support/'+menu_store_id+'/transfer_rx/tribe_members/file_download/'+file.id+'" class="">'+newFilename+'</a><span class="closebtn" onclick="removeFile('+file.id+');">×</span></div>');
                  });
                  sweetAlert2(data.status, data.message);
                  // Clear the file input
                  $('#edit_modal #file').val('');
                }
              },
              error: function(xhr, status, error) {
                handleErrorResponse(error);
                  console.error("Error:", error);
              }
          });
      } else {
          console.log("No files selected");
      }
    });



    $('#change_task_modal').on('hidden.bs.modal', function () {
      $('#change_task_modal #selected_button, #change_task_modal #selection_button').empty(); // Remove existing buttons
      $('.error_txt').remove();
      $(this)
      .find("input,textarea,select")
      .val('')
      .end(); 
    });

    $('#edit_modal').on('hidden.bs.modal', function () {
        $('#edit_modal #selected_status').empty(); // Remove existing buttons
        $('#edit_modal .comment-card').remove();
        $('#edit_modal #droparea_text').empty();
        $("#edit_modal .chip_file").remove();
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end(); 
        @php
          if(Auth::user()->can('menu_store.patient_support.transfer_rx.comment')) {
        @endphp
        commentInput.removeEventListener('keydown', handleEnterKeyPress);
        @php
          }
        @endphp
    });
  }

  
  function removeFile(id){
      sweetAlertLoading();
      $.ajax({
          headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
          type: "DELETE",
          url: `/store/patient-support/${menu_store_id}/transfer_rx/tribe_members/delete_file`,
          contentType: "application/json; charset=utf-8",
          dataType: "json",
          data: JSON.stringify({
              id: id,
          }),
          success: function(result){
              $("#edit_modal .delete_file_"+id+"").remove();
              sweetAlert2(result.status, result.message);
          },
          error: function (msg) {
            handleErrorResponse(msg);
          }
      });
  }
</script>