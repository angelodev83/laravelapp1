@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="/css/sms.css" />

<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!-- PAGE-HEADER -->
        @include('layouts/pageContentHeader/store')
        <!-- PAGE-HEADER END -->

        <div class="row">
            <div class="col-lg-7">
                <div class="scrollable-content">
                    <div class="card">
                        <div class="card-body">
                            <input type="file" name="file" class="form-control d-none" id="file">
                            <div class="row g-3">
                                
                                <label class="col-sm-5 col-form-label"><b>FIRSTNAME:</b></label>
                                <div class="col-sm-7">
                                    <label id="patient_bdate" class="col-sm-12 col-form-label">{{$profileData->getDecryptedFirstname()}}</label>
                                </div>
                                <label class="col-sm-5 col-form-label"><b>LASTNAME:</b></label>
                                <div class="col-sm-7">
                                    <label id="patient_age" class="col-sm-12 col-form-label">{{$profileData->getDecryptedLastname()}}</label>
                                </div>
                                <label class="col-sm-5 col-form-label"><b>BIRTH DATE:</b></label>
                                <div class="col-sm-7">
                                    <label id="patient_bdate" class="col-sm-12 col-form-label">{{ date('F d, Y', strtotime($profileData->getDecryptedBirthdate())) }}</label>
                                </div>
                                <label class="col-sm-5 col-form-label"><b>AGE:</b></label>
                                <div class="col-sm-7">
                                    <label id="patient_age" class="col-sm-12 col-form-label">{{$profileData['age']}}</label>
                                </div>
                                <label class="col-sm-5 col-form-label"><b>GENDER:</b></label>
                                <div class="col-sm-7">
                                    <label id="patient_bdate" class="col-sm-12 col-form-label">
                                        @if ($profileData['gender'] === 'M')
                                            Male
                                        @elseif ($profileData['gender'] === 'F')
                                            Female
                                        @else
                                            {{ $profileData['gender'] }}
                                        @endif
                                    </label>
                                </div>
                                <label class="col-sm-5 col-form-label"><b>MOBILE #:</b></label>
                                <div class="col-sm-7">
                                    <label id="patient_age" class="col-sm-12 col-form-label">{{$profileData['phone_number']}}</label>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="chat-box">
                    
                    <div class="chat-box-body">
                        <div class="chat-box-overlay">   
                        </div>
                        <div class="chat-logs">
                            
                        </div><!--chat-log -->
                    </div>
                    <div class="chat-input">      
                        <form>
                            <div id="textarea-container">
                                <div id="textarea-inner">
                                    <textarea type="text" rows="1" id="chat-input" placeholder="Send a message.."></textarea>
                                    <button type="submit" class="chat-submit" id="chat-submit"><i class="fa-solid fa-chevron-right"></i></button>
                                </div>
                            </div>
                             </form>      
                    </div>
                </div>   
            </div>
        </div>
    </div>
    @include('sweetalert2/script')
</div>
<!--end page wrapper -->
@stop
@section('pages_specific_scripts')
<script>
    let menu_store_id = {{request()->id}};
    let smsUser = {!! json_encode(htmlspecialchars_decode(config('app.name'))) !!};
    let phone_number = {!! json_encode(htmlspecialchars_decode($profileData['phone_number'])) !!};
    let patient_id = {!! json_encode(htmlspecialchars_decode($profileData['tebra_id'])) !!};
    let userFirstName = {!! json_encode(htmlspecialchars_decode($authEmployee->firstname)) !!};
    let userLastName = {!! json_encode(htmlspecialchars_decode($authEmployee->lastname)) !!};
    let userFullName = userFirstName+ ' ' +userLastName;
    let userId = {{$user->id}};
    let smsLastMessageTime = '';
    let currentPage = 1;
    let scrolledUp = false;
    // Define a flag to track if the AJAX call is in progress
    let ajaxInProgress = false;
    // Define a variable to store the height of the old messages
    let oldMessagesHeight = 0;
    let endOfHistory = false;

    $(document).ready(function(){

        populateLogs(false);
        if(phone_number == ''){
            $('#chat-submit').attr('disabled', true);
            $('#chat-input').prop('disabled', true);
            $('#chat-submit').html('<i class="fa-solid fa-xmark"></i>');
        }
        else{
            $('#chat-submit').attr('disabled', false);
            $('#chat-input').prop('disabled', false);
            $('#chat-submit').html('<i class="fa-solid fa-chevron-right"></i>');
        }
    });

    // Listen for scroll events on the chat logs
    $('.chat-logs').on('scroll', function() {
        if(endOfHistory){
            return;
        }
        // Check if the scroll position is at the top
        if ($(this).scrollTop() === 0 && !ajaxInProgress) {
            // Set the AJAX in progress flag to true
            ajaxInProgress = true;
            // Calculate the height of the old messages
            oldMessagesHeight = $('.chat-logs')[0].scrollHeight;
            //Increment the current page number
            currentPage++;
                    
            populateLogs(true);
        }
    });

    function formatDate(inputDate) {
        // Create a new Date object from the input date string
        let date = new Date(inputDate);
        
        // Get the month abbreviation
        let monthAbbreviation = new Intl.DateTimeFormat('en-US', { month: 'short' }).format(date);
        
        // Get the day and year
        let day = date.getDate();
        let year = date.getFullYear();
        
        // Get the hours and minutes
        let hours = date.getHours();
        let minutes = date.getMinutes();
        
        // Convert hours to 12-hour format and get AM/PM
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // Handle midnight (0 hours)
        
        // Pad single digit day and minutes with leading zeros
        day = day < 10 ? '0' + day : day;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        
        // Format the date as 'MMM dd, yyyy hh:mm AM/PM'
        let formattedDate = monthAbbreviation + ' ' + day + ', ' + year + ' ' + hours + ':' + minutes + ' ' + ampm;
        
        return formattedDate;
    }
    
    function populateLogs(history)
    {
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: `/store/clinical/tebra-patients/get-logs/${phone_number}/${currentPage}`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: {
                
            },
            success: function(data) {
                //console.log(data);
                if(history){
                    endOfHistory = (data.data.length === 0);
                    $.each(Object.values(data.data), function(index, record) {
                        userName = (record.user && record.user.employee) ? 'Sent by: '+record.user.employee.firstname + ' ' + record.user.employee.lastname : 'Sent by: '+smsUser;
                        dateTime = formatDate(record.creation_time);
                        let str="";
                        str += "<div id='cm-msg-"+record.id+"' class=\"chat-msg "+record.direction+"\">";
                        str += "<div class='msg-header'>";
                        str += "<span class='msg-datetime'>"+dateTime+"</span>";
                        str += "</div>";
                        str += "          <span class=\"msg-avatar\">";
                        str += "          <\/span>";
                        str += "          <div class=\"cm-msg-text\">";
                        str += record.subject;
                        str += "          <\/div>";
                        str += "<span class='msg-outbound-by'>";
                        str += userName;
                        str += "</span>";
                        str += "        <\/div>";
                        $(".chat-logs").prepend(str); 
                    });

                    // Calculate the height of the new messages
                    let newMessagesHeight = $('.chat-logs')[0].scrollHeight - oldMessagesHeight;

                    // Adjust the scroll position to maintain the previous view
                    $(".chat-logs").stop().animate({ scrollTop: newMessagesHeight }, 500);
                    
                    // Reset the AJAX in progress flag
                    ajaxInProgress = false;
                }

                else{
                    $.each(Object.values(data.data).reverse(), function(index, record) {
                        if(record.creation_time > smsLastMessageTime){
                            userName = (record.user && record.user.employee) ? 'Sent by: '+record.user.employee.firstname + ' ' + record.user.employee.lastname : 'Sent by: '+smsUser;
                            dateTime = formatDate(record.creation_time);
                            let str="";
                            str += "<div id='cm-msg-"+record.rc_id+"' class=\"chat-msg "+record.direction+"\">";
                            str += "<div class='msg-header'>";
                            str += "<span class='msg-datetime'>"+dateTime+"</span>";
                            str += "</div>";
                            str += "          <span class=\"msg-avatar\">";
                            str += "          <\/span>";
                            str += "          <div class=\"cm-msg-text\">";
                            str += record.subject;
                            str += "          <\/div>";
                            str += "<span class='msg-outbound-by'>";
                            str += userName;
                            str += "</span>";
                            str += "        <\/div>";
                            $(".chat-logs").append(str);
                            $(".chat-logs").stop().animate({ scrollTop: $(".chat-logs")[0].scrollHeight}, 1000);
                            smsLastMessageTime = record.creation_time; 
                        }
                    });
                    
                }
            },
            error: function(xhr, status, error) {
                handleErrorResponse(error);
                // Handle errors
                console.error(error);
                if(history){
                    currentPage--;
                    // Reset the AJAX in progress flag
                    ajaxInProgress = false;
                }
            }
            
        });
    }

    $('#chat-input').keydown(function(e) {
        if (e.keyCode === 13) { // Enter key is pressed
            // e.preventDefault(); // Prevent default form submission on Enter key
            if (!e.shiftKey) { // Check if Shift is not held
                let message = $(this).val().trim(); // Get the trimmed value of the input
                if (message !== '') { // Check if the message is not empty
                    let str="";
                    let subject = $("#chat-input").val();
                    str += "<div id='cm-msg-pending' class=\"chat-msg Outbound\">";
                    str += "<div class='msg-header'>";
                    str += "<span class='msg-datetime'></span>";
                    str += "</div>";
                    str += "          <span class=\"msg-avatar\">";
                    str += "          <\/span>";
                    str += "          <div class=\"cm-msg-text\">";
                    str += subject;
                    str += "          <\/div>";
                    str += "<span class='msg-outbound-by'>";
                    str += "Sending...";
                    str += "</span>";
                    str += "        <\/div>";
                    $(".chat-logs").append(str);
                    $(".chat-logs").stop().animate({ scrollTop: $(".chat-logs")[0].scrollHeight}, 1000);
                    sendMessage(); // Call the function to send the message
                }
            }
        }
    });
    
    $("#chat-submit").click(function(e) {
        e.preventDefault();
        
        let message = $('#chat-input').val().trim(); // Get the trimmed value of the input
        if (message !== '') { // Check if the message is not empty
            let str="";
            let subject = $("#chat-input").val();
            str += "<div id='cm-msg-pending' class=\"chat-msg Outbound\">";
            str += "<div class='msg-header'>";
            str += "<span class='msg-datetime'></span>";
            str += "</div>";
            str += "          <span class=\"msg-avatar\">";
            str += "          <\/span>";
            str += "          <div class=\"cm-msg-text\">";
            str += subject;
            str += "          <\/div>";
            str += "<span class='msg-outbound-by'>";
            str += "Sending...";
            str += "</span>";
            str += "        <\/div>";
            $(".chat-logs").append(str);
            $(".chat-logs").stop().animate({ scrollTop: $(".chat-logs")[0].scrollHeight}, 1000);
                
            sendMessage();
        } 
    });

    function sendMessage(){
        $('#chat-submit').attr('disabled', true);
        $('#chat-input').prop('disabled', true);
        $('#chat-submit').html('<i class="fa-solid fa-spinner"></i>');
        $('.chat-logs').addClass('disable-scroll');
        currentPageBeforeSend = currentPage;
        let data = {};
        data['subject'] = $("#chat-input").val(); 
        data['phone_number'] = phone_number;

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/clinical/tebra-patients/send-sms`,
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                //console.log(data);
                if(data.status == 'error'){
                    $('#chat-submit').html('<i class="fa-solid fa-triangle-exclamation" style="color: red;"></i>');
                    $('#cm-msg-pending').remove();
                }
                else{
                    currentPage = 1;
                    let timestamp = data.message.creationTime;
                    let formattedTimestamp = new Date(timestamp).toISOString().slice(0, 19).replace('T', ' ');
                    smsLastMessageTime = formattedTimestamp; 
                    dateTime = formatDate(formattedTimestamp);
                    $('#cm-msg-pending .msg-datetime').text(dateTime);
                    $('#cm-msg-pending .msg-outbound-by').text('Sent by: '+(userFullName??'Sent by: '+smsUser));
                    $('#cm-msg-pending').attr('id', data.message.id)
                    populateLogs(false);
                    clearTextareaHeight($('#chat-input'));
                    $('#chat-submit').html('<i class="fa-solid fa-chevron-right"></i>');
                    $('#chat-input').val('');
                }
                $('.chat-logs').removeClass('disable-scroll');
                $('#chat-submit').attr('disabled', false);
                $('#chat-input').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                handleErrorResponse(error);
                // Handle errors
                console.error(error);
                $('#chat-submit').html('<i class="fa-solid fa-triangle-exclamation" style="color: red;"></i>');
                $('.chat-logs').removeClass('disable-scroll');
                $('#chat-submit').attr('disabled', false);
                $('#chat-input').prop('disabled', false);
                $('#cm-msg-pending').remove();
            }
            
        });
        currentPage = currentPageBeforeSend;
    }

    $(document).ready(function() {
        $('#chat-input').on('input', function() {
            adjustTextareaHeight($(this));
        });
    });

    function adjustTextareaHeight($textarea) {
        // Count the number of newline characters in the textarea value
        var newlines = ($textarea.val().match(/\n/g) || []).length;

        // Add one to the newline count to account for the initial row
        var rows = newlines + 1;

        // Limit the maximum number of rows to 3
        rows = Math.min(rows, 7);

        // Adjust the textarea's rows to fit the content
        $textarea.attr('rows', rows);
    }

    function clearTextareaHeight($textarea) {
        // Adjust the textarea's rows to fit the content
        $textarea.attr('rows', 1);
    }

    $(document).ready(function() {    
        $(".chat-box").show('scale');
    })
</script>
@stop