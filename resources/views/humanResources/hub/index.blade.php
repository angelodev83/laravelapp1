@extends('layouts.master')
@section('content')

<style>
    /* scrollable div left and right */
    .scrollable-content {
    width: 100%; /* Adjust width as needed */
    height: 490px; /* Adjust height as needed */
    overflow-y: auto;
    /* border: 1px solid #ccc; */
    padding: 10px;
    }

    /* sms */
    #center-text {
    display: flex;
    flex: 1;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100%;
    }

    .chat-box {
    display: none;
    background: #efefef;
    border-radius: 5px;
    margin-top: 10px;
    margin-left: 1.5%;
    position: relative;
    width: 100%;
    height: 100%; /* Ensure the chat box takes up the full height of its container */
    }

    .chat-box-body {
        position: relative;
        /* height: calc(100% - 47px);  */
        overflow-y: auto; /* Enable vertical scrollbar if needed */
        /* border: 1px solid #ccc; */
        /* overflow: hidden; */
        border-top-right-radius: 4px;
        border-top-left-radius: 4px;
        background: #ffffff;
    }

    .chat-input {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: auto; /* Allow the chat input to expand */
    }

    #textarea-container {
    position: relative;
    min-height: 47px; /* Set minimum height for the container */
    overflow: hidden; /* Hide overflow to prevent scrollbars */
    /* border: 1px solid #ccc;
    border-top: none; */
    border-radius: 10px;
    /* border-bottom-right-radius: 5px;
    border-bottom-left-radius: 5px; */
        margin: 5px 15px 0px 15px;
    }

    #chat-input {
    background: white;
    width: 100%; /* Adjust for button width */
    height: auto; /* Allow the textarea to expand */
    resize: none;
    outline: none;
    border: 1px solid #A8A7A7;
    padding: 10px 50px 10px 15px; /* Adjust padding as needed */
    color: #888;
    box-sizing: border-box;
    border-radius: 10px;
    /* border-bottom-right-radius: 5px;
    border-bottom-left-radius: 5px; */
    }

    #textarea-inner {
    position: relative;
    }

    .chat-input > form {
    margin-bottom: 0;
    }

    #chat-input::-webkit-input-placeholder {
    /* Chrome/Opera/Safari */
    color: #ccc;
    }

    #chat-input::-moz-placeholder {
    /* Firefox 19+ */
    color: #ccc;
    }

    #chat-input:-ms-input-placeholder {
    /* IE 10+ */
    color: #ccc;
    }

    #chat-input:-moz-placeholder {
    /* Firefox 18- */
    color: #ccc;
    }

    .chat-submit {
    /* margin-bottom: 2px; */
    margin: 5px 10px 9px 4px;
    border-radius: 5px;
    position: absolute;
    right: 0;
    bottom: 0;
    width: 40px; /* Adjust button width */
    height: 68%;
    border: none;
    /* background-color: transparent; */
    background-color: #15a0a3;
    /* color: #15a0a3; */
    color: white;
    }

    .chat-logs {
    padding-top: 25px;
    padding-left: 15px;
    padding-right: 15px;
    height: 430px; /* Set max height to limit the chat logs */
    overflow-y: auto; /* Enable vertical scrolling */
    margin-bottom: 15px;
    background: #ffff;
    }

    .chat-logs::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
    background-color: #f5f5f5;
    }

    .chat-logs::-webkit-scrollbar {
    width: 5px;
    background-color: #f5f5f5;
    }

    .chat-logs::-webkit-scrollbar-thumb {
    background-color: #15a0a3;
    }

    #chat-input::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
    background-color: #f5f5f5;
    }

    #chat-input::-webkit-scrollbar {
    width: 5px;
    background-color: #f5f5f5;
    }

    #chat-input::-webkit-scrollbar-thumb {
    background-color: #15a0a3;
    }

    @media only screen and (max-width: 991.2px) {
    .chat-logs {
        height: 40vh;
        padding-bottom: 30px;
    }

    .chat-box-body {
        margin-right: 15px;
        margin-left: -5px;
    }

    #textarea-container {
        left: -5px;
        padding-right: 10px;
    }
    }
    .cm-msg-text {
    background: #f0f0f0;
    padding: 10px 15px 10px 15px;
    color: #666;
    max-width: 90%;
    float: left;
    margin-left: 10px;
    position: relative;
    /* margin-bottom: 20px; */
    border-radius: 30px;
    }

    .chat-msg {
    clear: both;
    }

    .chat-msg.Outbound > .cm-msg-text {
    float: right;
    margin-right: 10px;
    background: #15a0a3;
    color: white;
    }

    .chat-msg.Inbound > .cm-msg-text {
    margin-bottom: 20px;
    }

    .msg-outbound-by {
    display: block;
    clear: both;
    text-align: right;
    color: rgb(138, 132, 132); /* Adjust color as needed */
    margin-bottom: -10px;
    margin-right: 15px;
    }

    .msg-datetime {
    margin-right: 10px; /* Adjust spacing as needed */
    }

    .msg-header {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 0px; /* Adjust spacing as needed */
    }

    /* chat message styles */
    .chat-msg {
    position: relative;
    padding: 0px;
    margin-bottom: 10px;
    border-radius: 10px;
    }

    /* header */
    .msg-header {
    display: none;
    position: absolute;
    top: -30px;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgb(255, 255, 255);
    /* padding: 10px 10px; */
    margin-top: 11px;
    border-radius: 5px;
    }

    .chat-msg:hover .msg-header {
    display: block;
    }

    /* datetime */
    .msg-datetime {
    margin: 0;
    }

    .disable-scroll {
    overflow: hidden;
    }

</style>

<!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!-- PAGE-HEADER -->
            @include('layouts/pageContentHeader/index')
            <!-- PAGE-HEADER END -->

            <div class="banner">
                <img src="/source-images/hr-hub/banner.png" alt="Banner Image">
                <div class="banner-text">
                    Welcome to MGMT HR Hub
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-8">
                    @include('humanResources/hub/partials/quick-links')
                    @include('humanResources/hub/partials/hr-hub')
                </div>

                <div class="col-4">
                    @include('humanResources/hub/partials/social-corner')
                    @include('humanResources/hub/partials/new-hires')
                </div>
            </div>

        </div>
    </div>
<!--end page wrapper -->
@stop
@section('pages_specific_scripts') 
<style>
    .banner {
        position: relative;
        text-align: center;
        color: white;
    }

    .banner img {
        width: 100%;
        height: auto;
        border-radius: 10px;
    }

    .banner-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2em;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

</style>
<script>
    let social_corner_limit = 0;
    let social_corner_load_items = 0;
    let auth_user_id = {{$user->id}};

    $(document).ready(function() {
        social_corner_limit = 0;    
        social_corner_load_items = 0;
        auth_user_id = {{$user->id}};
        
        loadMoreSocialCorner(2);
    });

    function loadMoreSocialCorner(limit = 1)
    {
        loadingSocialCorner();
        social_corner_limit += limit;
        let data = {
            limit: social_corner_limit
        };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/admin/social-corner/load-more`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(res) {

                console.log("get res",res)

                const items = res.data;

                let html = '';

                const len = items.length;

                for(let i in items) {
                    const item = items[i];

                    let timeAgo = computeTime(item.created_at);

                    let post = item.post;
                    post =  post.replace(/\n/g, '<br>');

                    let hearts = item.hearts ? item.hearts : [];
                    let heart_reactions = hearts.length;
                    let is_hearted = hearts.some(heart => heart.user_id === auth_user_id);

                    let heart_class = is_hearted ? 'fa-solid' : 'fa-regular';
                    let heart_title = is_hearted ? 'Unlike' : 'Like';

                    let user = item.user ?? null;
                    let employee = user.employee ?? null;

                    let avatar = employee.image ? '/upload/userprofile/'+employee.image : '/source-images/hr-hub/avatar.png';

                    let fullname = employee.firstname ? employee.firstname+' '+employee.lastname : 'MGMT HR';

                    html+= `
                        <div class="col-12 mt-2 border-bottom">
                            <div class="chip chip-md bg-white mb-0 pb-0 w-100" style="border:none; cursor: auto;">
                                <img src="${avatar}" width="45" height="45" alt="Contact Person">
                                <span class="d-flex">
                                    <span style="color: #8338ed;">${fullname}</span>
                                    <small class="ms-auto text-end">${timeAgo}</small>
                                </span>
                            </div>

                            <div class="mt-0 pt-2">
                                ${post}
                            </div>

                            <div class="mt-0 py-2 ms-auto text-end">
                                <i id="social_corner_heart_${item.id}" class="${heart_class} fa-heart" style="color: #9656ef; cursor: pointer;" data-toggle="tooltip" title="${heart_title}" onclick="doHeartPost(${item.id}, ${is_hearted}, ${heart_reactions})"></i> <span id="social_corner_heart_reaction_${item.id}">${heart_reactions}</span>
                            </div>
                        </div>
                    `;
                }

                $('#social_corner_div').html(html);

                if(len == social_corner_load_items && social_corner_limit > len) {
                    // $('#social_corner_load_more_btn').attr('disabled', true);
                    $('#social_corner_load_more_btn').addClass('d-none');
                    $('#social_corner_load_more_p').html(`Nothing to load more.`);

                    social_corner_limit = len;
                } else {
                    social_corner_load_items = len;
                    $('#social_corner_load_more_btn').removeAttr('disabled');
                }


            },error: function(msg) {
                handleErrorResponse(msg);
                if(msg.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                console.log(msg.responseText);
            }
        });
    }

    function doHeartPost(id, is_hearted, heart_raections)
    {
        let data = {
            id: id
        };
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/admin/social-corner/react`,
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
            
                loadMoreSocialCorner(0);

            }, error: function(msg) {
                handleErrorResponse(msg);
                if(msg.status == 403) {
                    sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                }
                //general error
                console.log("Error");
                console.log(msg);
                $.each(msg.responseJSON.errors,function (key , val){
                    sweetAlert2('warning', 'Check field inputs.');
                    $("#"+key ).after( '<span class="error_txt" style="color:red; text-indent:15px;">'+val[0]+'</span>' );
                    console.log(key);
                });
            }
        });
    }

    function loadingSocialCorner()
    {
        $('#social_corner_load_more_btn').attr('disabled', true);
        $('#social_corner_div').html(`
            <div class="col-12 mt-2">
                <button class="btn btn-dark w-100" type="button" disabled> 
                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                    Loading... Please wait
                </button>
            </div>
        `);
    }

    function computeTime(dateString) {
        // Convert the provided date string (assumed to be in UTC) to a Date object
        let past = new Date(dateString);
        
        // Get the current time in Pacific Time
        const now = new Date(new Date().toLocaleString('en-US', { timeZone: 'America/Los_Angeles' }));
        
        // Calculate the time difference
        const diffInSeconds = Math.floor((now - past) / 1000);
        const diffInMinutes = Math.floor(diffInSeconds / 60);
        const diffInHours = Math.floor(diffInMinutes / 60);
        const diffInDays = Math.floor(diffInHours / 24);
        const diffInWeeks = Math.floor(diffInDays / 7);
        const diffInMonths = Math.floor(diffInDays / 30); // Approximate
        const diffInYears = Math.floor(diffInDays / 365); // Approximate

        // Return the appropriate time difference
        if (diffInSeconds < 60) {
            return `${diffInSeconds}s ago`;
        } else if (diffInMinutes < 60) {
            return `${diffInMinutes}m ago`;
        } else if (diffInHours < 24) {
            return `${diffInHours}h ago`;
        } else if (diffInDays < 7) {
            return `${diffInDays}d ago`;
        } else if (diffInWeeks < 4) {
            return `${diffInWeeks}w ago`;
        } else if (diffInMonths < 12) {
            return `${diffInMonths}mos ago`;
        } else {
            return `${diffInYears}yrs ago`;
        }
    }

</script>  
@stop
