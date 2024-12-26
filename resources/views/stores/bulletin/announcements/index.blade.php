@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">

            <div class="page-content">
                <!-- PAGE-HEADER -->
                @include('layouts/pageContentHeader/index')
                <!-- PAGE-HEADER END -->
                
                <div class="card">
                    <div class="card-header dt-card-header"> </div>
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
            @include('stores/bulletin/announcements/modal/show')
            @include('stores/bulletin/announcements/modal/add')
            @include('stores/bulletin/announcements/modal/edit')
            @include('stores/bulletin/announcements/modal/delete')
        
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
        let content = triggerLink.data("content");

        tinymce.get("econtent").setContent(content);

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
            error: function (msg) {
                handleErrorResponse(msg);
            }
        });
    }

    $(document).ready(function() {
        let menu_store_id = {{request()->id}};

        tinymce.init({
		  selector: 'textarea.tinymce-content',
          branding: false
		});
        const announcement_table = $('#announcement_dt_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBtip',
            buttons: [
                @can('menu_store.bulletin.announcements.create')
                { text: '+ New Announcement', className: 'btn btn-primary mt-0 mb-2', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
                @endcan
            ],
            pageLength: 50,
            searching: true,
            order: [[2, 'desc']],
            ajax: {
                url: "/store/bulletin/announcements/data",
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
                { data: 'created_by', name: 'created_by', title: 'Created By', render: function(data, type, row) {
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
