@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="/css/sms.css" />

<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!-- PAGE-HEADER -->
        @include('layouts/pageContentHeader/store')
        <!-- PAGE-HEADER END -->

        <div class="row g-3">
            <div class="col-lg-5">
                @include('stores/clinical/patients/facesheet/partials/personal')

                {{-- outreach --}}
                <div class="col-12" style="margin-top: -15px;">
                    <div class="card">
                        <div class="card-header dt-card-header">
                            <div class="row">
                                <div class="col-4">
                                    <h6 style="float: left;" class="table_search_input ms-3">Outreach</h6>
                                </div>
                                
                                <div class="col-4">
                                    <input style="float: right;" type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
                                </div>

                                <div class="col-4">
                                    @can('menu_store.clinical.kpi.create')
                                    <a style="width: fit-content;" class="btn btn-primary ms-2 table_search_input" onclick="showAddNewForm()">+ Schedule</a>
                                    @endcan
                                </div>
                                
                            </div>
                           
                            <select name='length_change' id='length_change' class="table_length_change form-select d-none">
                            </select>
                            
                        </div>
                        <div class="card-body">

                            <div class="table-responsive">
                                <table id="table" class="table row-border table-hover" style="width:100%">
                                    <thead></thead>
                                    <tbody>
                                            <tr>
                                            <td>
                                                <div class="text-center dt-loading-spinner">
                                                    <i class="fas fa-spinner fa-spin fa-3x"></i> <!-- Example: Font Awesome spinner icon -->
                                                </div>
                                            </td>
                                        </tr>                                     
                                    </tbody>
                                    <tfooter></tfooter>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-7">
                @include('stores/clinical/patients/facesheet/partials/sms')
            </div>
            
        </div>
    </div>
    @include('sweetalert2/script')
    @include('stores/clinical/patients/facesheet/modal/add')
</div>
<!--end page wrapper -->
@stop
<style>
    .custom-fw-bold {
        font-weight: 500 !important;
    }
</style>
@section('pages_specific_scripts')
<script>
    let menu_store_id = {{request()->id}};
    let smsUser = {!! json_encode(htmlspecialchars_decode(config('app.name'))) !!};
    let phone_number = {!! json_encode(htmlspecialchars_decode($profileData['phone_number'])) !!};
    let patient_id = {!! json_encode(htmlspecialchars_decode($profileData['tebra_id'])) !!};
    let userFirstName = {!! json_encode(htmlspecialchars_decode($authEmployee->firstname)) !!};
    let userLastName = {!! json_encode(htmlspecialchars_decode($authEmployee->lastname)) !!};
    let userFullName = userFirstName+ ' ' +userLastName;
    let p_id = {!! json_encode(htmlspecialchars_decode($profileData['id'])) !!};
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
            $('#chat-submit').html('<i class="fa-solid fa-paper-plane"></i>');
        }

        const table = $('#table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            processing: true,
            language: {
                loadingRecords: '&nbsp;',
                processing: '<div class="spinner"></div>'
            },
            dom: 'fBtip',
            order: [[0, 'desc']],
            buttons: [
                @can('menu_store.clinical.kpi.create')
                // { text: '+ New', className: 'btn btn-primary ms-auto', action: function ( e, dt, node, config ) {
                //     showAddNewForm();
                // }},
                @endcan
            ],
            pageLength: 50,
            searching: true,
            ajax: {
                url: `/store/clinical/outreach/data_schedule`,
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                    data.p_id = p_id;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'id', name: 'id', title: 'ID'},
                { data: 'date', name: 'date', title: 'Date'},
                { data: 'created_by', name: 'user_id', title: 'Created By' },
                { data: 'created_at', name: 'created_at', title: 'Date Created' },
                // { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            },
        });

        dt_table = table;
        // Placement controls for Table filters and buttons
		table.buttons().container().appendTo( '.dt-card-header' ); 
        $('#search_input').val(table.search());
		$('#search_input').keyup(function(){ table.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table.page.len($(this).val()).draw() });

    });

    function showAddNewForm(){  
        $('#add_modal').modal('show');
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5',
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true,
   			orientation: "right",
        });

        // Set the default date to today
        $('.datepicker').datepicker('setDate', new Date());
    }

    function reloadDataTable()
    {
        dt_table.ajax.reload(null, false);
    }

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
                    $('#chat-submit').html('<i class="fa-solid fa-paper-plane"></i>');
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