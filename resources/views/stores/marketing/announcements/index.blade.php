@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">

            <div class="page-content">
                <!-- PAGE-HEADER -->
                @include('layouts/pageContentHeader/store')
                <!-- PAGE-HEADER END -->
                
                <div class="card">
                    <div class="card-header dt-card-header">
                        <select name='length_change' id='length_change' class="table_length_change form-select">
                        </select>
                        <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="announcement_dt_table" class="table row-border hover" style="width:100%">
                                <thead></thead>
                                <tbody>                                   
                                </tbody>
                                <tfooter></tfooter>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @include('sweetalert2/script')
            @include('stores/marketing/announcements/modal/show')
            @include('stores/marketing/announcements/modal/add')
            @include('stores/marketing/announcements/modal/edit')
            @include('stores/marketing/announcements/modal/delete')
        
		</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts')  
<script>
    let table_announcement;

    function showAddNewForm(){
        $('#addAnnouncement_modal').modal('show');
        let data = {};
        
    }

    $('#addAnnouncement_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $("#updateAnnouncement_modal").on('show.bs.modal', function (e) {
    
        let triggerLink = $(e.relatedTarget);
        let id = triggerLink.data('id');
        let subject = triggerLink.data("subject");
        tinymce.get("econtent").setContent('Loading.. please wait..'); 
        
           
        $.ajax({
            url: '/store/marketing/announcements/get/' + id, 
            method: 'POST',
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            success: function(response) {
                let content = response.content; 
               // console.log(response);
                tinymce.get("econtent").setContent(content);    
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handleErrorResponse(errorThrown);
                // Handle any errors here
                console.error(textStatus, errorThrown);
            }
        });

        $("input#esubject").val(subject);
        $("textarea#econtent").val(content);
        $("input#eid").val(id);
    });

    function deleteAnnouncement(id){
        sweetAlertLoading();
        $.ajax({
            url: "/admin/human_resources/delete_announcement",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: id
            },
            dataType: 'json',
            success:function(response){
                Swal.fire({
                    position: 'center',
                    icon: response.status,
                    title: response.message,
                    showConfirmButton: false,
                    timer: 4000
                });
                table_employee.ajax.reload(null, false);
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    document.addEventListener('focusin', function (e) { 
        if (e.target.closest('.tox-tinymce-aux, .moxman-window, .tam-assetmanager-root') !== null) { 
            e.stopImmediatePropagation();
        } 
    });

    $(document).ready(function() {
        let menu_store_id = {{request()->id}};

        tinymce.init({
            selector: 'textarea.tinymce-content',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | link',
            plugins: 'link',
             license_key: 'gpl',
            branding: false
		});
        
        const announcement_table = $('#announcement_dt_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBtip',
            buttons: [
                @can('menu_store.marketing.announcements.create')
                { text: '+ New Announcement', className: 'btn btn-primary mt-0 mb-2', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
                @endcan
            ],
            pageLength: 50,
            searching: true,
            order: [[2, 'desc']],
            ajax: {
                url: "/store/marketing/announcements/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                // { data: 'id', name: 'id', title: 'ID', visible: true},
                { data: 'subject', name: 'subject', title: 'Subject', render: function(data, type, row) {
                    return `<div class="task-subject datatable-long-description-field-truncate" onclick="showStoreAnnouncementModal(${row.id})" style="cursor: pointer;">${data}</div>`;
                } },
                // { data: 'content', name: 'content', title: 'Content' },
                { data: 'created_by', name: 'created_by', title: 'Created By', orderable: false, searchable: false, render: function(data, type, row) {
                    return `${row.empAvatar}`;
                }  },
                { data: 'created_at', name: 'created_at', title: 'Date Created' },
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = announcement_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_announcement = announcement_table;
        
        // Placement controls for Table filters and buttons
		table_announcement.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(announcement_table.search());
		$('#search_input').keyup(function(){ table_announcement.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_announcement.page.len($(this).val()).draw() });
    });

    
</script>  
@stop
