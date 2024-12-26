@extends('layouts.master')
@section('content')
<style>
    /* scrollable div left and right */
    .scrollable-content {
        width: 100%; 
        height: 520px;
        /* overflow-y: auto; */
        overflow: hidden;
        position: relative;
        padding: 10px;
        
    }

    .scrollable-event {
        height: 200px; /* Adjust as needed */
        overflow: hidden;
        position: relative;
    }

    .ps__rail-y {
        z-index: 1;
        right: 0;
    }

    .scrollable-bday {
        width: 100%; /* Adjust width as needed */
        height: 200px; /* Adjust height as needed */
        /* overflow-y: auto; */
        /* border: 1px solid #ccc; */
        /* padding: 10px; */
        overflow: hidden;
        position: relative;
    }

    .col2-style{
        margin-top: 8px;
    }

    /* .card-img-container {
        position: relative;
        overflow: hidden;
        
        height: 100%;  
    } */

    /* .card-img-ne {
        
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover; 
        
    } */

    /* .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 70%; 
    } */

    /* .card-ne{
        height: 100%;
    } */

    .card-img-container{
        max-height: 300px;
        overflow: hidden;
    }

    .card-img-container img {
        width: 100%;
        height: 100%;
        
        object-fit: cover;
    }

    .card-body-ne {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    #btn-holder {
        margin-top: auto; 
        margin-bottom: 1rem; 
    }


    .upcoming-events-main-text{
        color: #58585a;
    }
    
    .upcoming-events-date{
        background-color: #438f9d !important;
        padding: 8px;
    }

    .upcoming-events-date h1{
        color: #ffffff;
        margin-top: -5px;
        margin-bottom: -10px;
        font-size: 30px;
    }

    .upcoming-events-date span{
        color: #ffffff;
        font-size: 14px;
    }

    /* .card-content{
        height: 80%;
    } */

    /* .position-relative {
        position: relative;
    } */

</style>
<!--start page wrapper -->
		<div class="page-wrapper">

            <div class="page-content">
                <!-- PAGE-HEADER -->
                @include('layouts/pageContentHeader/store')
                <!-- PAGE-HEADER END -->
                
                <div class="row">
                    <div class="col-lg-9">
                        <div class="scrollable-content">
                            <div id="newsContainer">
                                <!-- News & Events will be loaded here -->
                            </div>

                            
                            
                        </div>
                        <div class="mt-4 d-flex justify-content-center">
                            <div id="paginationLinks"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        @can('menu_store.marketing.news.create')
                        <div class="row col2-style" style="margin-left: 1px; margin-right: 1px;">
                            <button class="btn btn-primary" onclick="showAddNewForm()">+Add New Post</button>
                        </div>
                        @endcan
                        
                        <div class="mt-4 card">
                            <div class="card-body">
                                <h6 style="color: #f1237b;">Upcoming Birthdays</h6>
                                <div class="scrollable-bday">
                                @forelse($upcomingBirthdays as $profileData)
                                    <a class="mb-2 d-flex align-items-center">
                                        <input type="file" name="profile_avatar_file" hidden/>
                                        
                                            @if(!empty($profileData->image))
                                                <img src="/upload/userprofile/{{$profileData->image}}" class="user-img" alt="user avatar" style="width: 56px !important; height: 56px !important;">
                                            @else
                                                <div class="col-auto">
                                                    <div class="avatar-{{$profileData->initials_random_color}}-initials" style="width: 57px !important; height: 57px !important; font-size: 20px !important;">
                                                        {{ strtoupper(substr($profileData->firstname, 0, 1)) }}{{ strtoupper(substr($profileData->lastname, 0, 1)) }}
                                                    </div>
                                                </div>
                                            @endif
                                        
                                        @php
                                            $date = \Carbon\Carbon::parse($profileData->date_of_birth);
                                            $day = $date->day;
                                            $suffix = match ($day) {
                                                1, 21, 31 => 'st',
                                                2, 22 => 'nd',
                                                3, 23 => 'rd',
                                                default => 'th'
                                            };
                                            $formattedDate = $date->format('F j') . $suffix;
                                        @endphp

                                        <div class="user-info ps-3">
                                            <h6 class="mb-0" style="color: #58585a">{{ $profileData->firstname}} {{$profileData->lastname}}</h6>
                                            <p class="mb-0 designattion">{{ $profileData->position}}</p>
                                            <p class="mb-0 designattion"><i style="color: #f1237b;" class="fa-solid fa-cake-candles"></i> {{$formattedDate}}</p>
                                        </div>
                                    </a>
                                @empty
                                    <p>No upcoming birthdays found.</p>
                                @endforelse
                                </div>
                            </div>
                        </div> 

                        <div class="mt-2 card">
                            <div class="card-body">
                                <h6 style="color: #438f9d;">Upcoming Events</h6>
                                @can('menu_store.marketing.events.create')
                                <button class="top-0 m-2 mt-2 btn btn-primary btn-sm position-absolute end-0" onclick="showEventForm()">+Add</button>
                                @endcan
                                <div class="scrollable-event">
                                
                                    <div id="eventsContainer" class="mt-3 row ms-1">
                                        
                                    </div>
                                
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
            @include('sweetalert2/script')
            
            @include('stores/marketing/newsAndEvents/modal/event')
            @include('stores/marketing/newsAndEvents/modal/add')
            @include('stores/marketing/newsAndEvents/modal/show')
        
		</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts')  
<script>
    let menu_store_id = {{request()->id}};
                                        
    document.addEventListener('focusin', function (e) { 
        if (e.target.closest('.tox-tinymce-aux, .moxman-window, .tam-assetmanager-root') !== null) { 
            e.stopImmediatePropagation();
        } 
    });

    $(document).ready(function () {
        // Load events on page load
        loadNews();
        loadEvents();

        tinymce.init({
            selector: 'textarea.tinymce-content',
            height: 225,
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | link',
            plugins: 'link',
            branding: false
		});

        new PerfectScrollbar('.scrollable-event');
        new PerfectScrollbar('.scrollable-bday');
        new PerfectScrollbar('.scrollable-content');

        // Handle pagination links
        $(document).on('click', '#paginationLinks a', function (e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            loadNews(page);
        });
    });
    
    // Function to load news
    function loadNews(page = 1) {
        $.ajax({
            url: `/store/marketing/${menu_store_id}/news-and-events?page=${page}`,
            type: 'GET',
            success: function (response) {
                // console.log(response.pagination);
                // console.log(response);
                $('#newsContainer').html(response.html);
                $('#paginationLinks').html(response.pagination);
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    // Function to load events
    function loadEvents() {
        $.ajax({
            url: `/store/marketing/news-and-events/get-events`,
            type: 'GET',
            success: function (response) {
                // console.log(response.pagination);
                console.log(response);
                $('#eventsContainer').html(response.html);
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function deleteEvent(id){
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                sweetAlertLoading();
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: "DELETE",
                    url: "/store/marketing/news-and-events/delete-event",
                    data: JSON.stringify({ id: id }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(msg) {
                        loadEvents();
                        Swal.close();
                    },error: function(msg) {
                        handleErrorResponse(msg);
                        console.log(msg.responseText);
                    }
        
                });
            }
        });
    }

    function deleteNews(id){
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                sweetAlertLoading();
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: "DELETE",
                    url: "/store/marketing/news-and-events/delete-news",
                    data: JSON.stringify({ id: id }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(msg) {
                        loadNews();
                        Swal.close();
                    },error: function(msg) {
                        handleErrorResponse(msg);
                        console.log(msg.responseText);
                    }
        
                });
            }
        });
    }

    function showEventForm(){
        $('#event_modal #date').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5', 
            modal: true,
            icons: {
                rightIcon: '<i class="material-icons"></i>'
            },
            showRightIcon: false,
            autoclose: true,
        });
        $('#event_modal').modal('show');
    }

    function showAddNewForm(){
        let fileInput = $('<input/>', {
            id: 'file',
            class: 'imageuploadify-file-general-class',
            name: 'file',
            type: 'file',
            accept: 'image/*'
        });
         
        $('#add_modal #for-file').html(fileInput); 
        $('#add_modal #file').imageuploadify();
        
        $("#add_modal .imageuploadify-container").remove();
        $('#add_modal .imageuploadify-message').html('Drag&Drop Your File(s) Here To Upload');     
        
        select2Api('status_id', 'add_modal', "/store/marketing/news-and-events/type");
        $('#add_modal #url_holder').hide();
        $('#add_modal').modal('show');
        let data = {};
    }

    $('#add_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    

        tinymce.get("content").setContent('');
    });

    $('#add_modal #status_id').on('change', function() {
        let selectedValue = $(this).val();
        
        if(selectedValue == 802){
            $('#add_modal #url_holder').show();
        }
        else{
            $('#add_modal #url_holder').hide();
            $('#add_modal #url').val('');
        }
    });

    function select2Api(_select_id, _modal_id, _url, condition = null)
    {
        $('#'+_modal_id+ ' #' + _select_id).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $(`#${_modal_id} .modal-content`),

            multiple: false,
            minimumInputLength: 0,
            minimumResultsForSearch: 10,
            ajax: {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: _url,
                dataType: "json",
                type: "POST",
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        limit: 10
                    }
                    if(condition != null) {
                        queryParameters = {...queryParameters, ...condition};
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            // console.log("item", item);
                            // console.log("===============================================");
                            return {
                                text: item.name,
                                id: item.id
                            }   
                        })
                    };
                }  
            },
        });
    }
    
</script>  
@stop
