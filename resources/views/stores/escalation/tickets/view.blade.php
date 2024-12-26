@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
			<!-- PAGE-HEADER -->
            @include('layouts/pageContentHeader/store')
            <!-- PAGE-HEADER END -->

            <div class="mt-4 card">
                <div class="card-body">
                    <div class="row">
                        <div class="d-flex">
                            <h2 class="text-primary">
                                {{ $ticket->subject }}
                            </h2>
                            <div class="float-end ms-auto">
                                @if (isset($ticket->status->name))
                                    <h2><span class="badge badge-ticket bg-{{$ticket->status->color}} px-5 ms-auto">{{$ticket->status->name}}</span></h2>
                                @endif
                            </div>
                        </div>
                        <p class="mb-0">
                            Assigned to: <b>{{ isset($ticket->assignedTo->id) ? $ticket->assignedTo->firstname. ' ' .$ticket->assignedTo->lastname : 'NA' }}</b>
                        </p>
                        <hr class="mt-1">
                        <div class="mb-3 text-truncated col-12" id="text-truncated">
                            {!! $ticket->description !!}
                        </div>
                        <div class="px-0 mx-0 col-12 text-truncated-btn-div" id="text-truncated-btn-div">
                            <button class="mx-3 mt-2 btn btn-sm btn-outline-dark" onclick="expandText()"><span id="text-truncated-btn"><i class="bx bx-chevron-right-circle "></i>Show More</span></button>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex">
                    <small>Created by: <b>{{ $ticket->user->employee->getFullName() }}</b></small>
                    <small class="ms-auto">{{ date('M d, Y H:iA', $ticket->create_at) }}</small>
                </div>
            </div>


            <div class="mt-0 card">
                <div class="card-header dt-card-header">
                    <select name='length_change' id='length_change' class="table_length_change form-select">
                    </select>
                    <input type="file" id="upload_documents" name="files[]" multiple hidden>
                    <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
                </div>
                <div class="card-body">
                    <p class="ms-3 text-primary">*Only accepts maximum size of 100 MB per file</p>
                    <div class="table-responsive">
                        <table id="dt_table" class="table row-border hover" style="width:100%">
                            <thead></thead>
                            <tbody>                                   
                            </tbody>
                            <tfooter></tfooter>
                        </table>
                    </div>
                </div>
            </div>
		</div>
		<!--end page wrapper -->
        @include('sweetalert2/script')
        @include('stores/escalation/ticketDocuments/modals/delete')
@stop

@section('pages_specific_scripts')  
<style>

div.text-truncated {
    max-height: 170px !important;
    overflow: hidden;
}

.text-truncated-btn-div {
    box-shadow: 0 -10px 20px -5px rgba(166, 166, 166, 0.75);
}

.badge-ticket {
    border-radius: 25px !important;
}

</style>
<script>
    let table_document;
    let menu_store_id = {{request()->id}};
    let tix_id = {{request()->tix_id}};

    // onload
    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        tix_id = {{request()->tix_id}};
        
        // tinymce.init({
		//   selector: 'textarea.tinymce-content',
        //   height: 225
		// });

        // $('.imageuploadify-file-general-class').imageuploadify();
        checkHeight();
        loadDocuments();
    });


    // functions
    function loadDocuments() {
        let data = {};        
        
        const dt_table = $('#dt_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            pageLength: 50,
            dom: 'fBtip',
            buttons: [
                {
                    text: '<i class="bx bx-cloud-upload me-2"></i>Upload Attachment(s)', 
                    className: 'btn btn-primary', 
                    action: function ( e, dt, node, config ) {
                        clickUploadBtn();
                    }
                },
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/store/escalation/ticket-documents/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.parent_id = tix_id;
                    data.order[0]['column'] = 3;
                    data.category = 'ticket';
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'file', name: 'file', title: 'File' },
                { data: 'size', name: 'size', title: 'Size' , orderable: false, searchable: false },
                { data: 'last_modified', name: 'last_modified', title: 'Last Modified', searchable: false },
                { data: 'created_at', name: 'created_at', title: 'Date Created' },
                { data: 'created_by', name: 'created_by', title: 'Created By' },
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = dt_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_document = dt_table;
        table_document.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(table_document.search());
		$('#search_input').keyup(function(){ table_document.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_document.page.len($(this).val()).draw() });
    }

    function clickUploadBtn() {
        $('#upload_documents').click();
    }

    let expandedBool = false;
    function expandText()
    {
        let btnText = '<i class="bx bx-chevron-right-circle "></i>Show More';
        const bool = !expandedBool;
        if(bool === true) {
            $('#text-truncated').removeClass('text-truncated');
            $('#text-truncated-btn-div').removeClass('text-truncated-btn-div');
            btnText = '<i class="bx bx-chevron-up-circle "></i>Show Less';
        } else {
            $('#text-truncated').addClass('text-truncated');
            $('#text-truncated-btn-div').addClass('text-truncated-btn-div');
        }
        $('#text-truncated-btn').html(btnText);
        expandedBool = bool;
    }

    function checkHeight() {
        if ($('#text-truncated').height() < 170) {
            $('#text-truncated-btn-div').hide();
        }
    }

    // events
    $('#upload_documents').change(function () {
        if(this.files.length){
            var uploadFiles = event.target.files;  
            var formData = new FormData();
            formData.append("ticket_id", tix_id);

            for (let i = 0; i < uploadFiles.length; i++) {
                formData.append("files[]", uploadFiles[i]);
                var kbSize = uploadFiles[i].size/1024;
                if(kbSize > 100000) {
                    sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                    return;
                }
            }

            $.ajax({
                //laravel requires this thing, it fetches it from the meta up in the head
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: `/store/escalation/ticket-documents/add`,
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(data) {
                    table_document.ajax.reload(null, false);
                    sweetAlert2(data.status, "Files has been uploaded");
                },error: function(msg) {
                    handleErrorResponse(msg);
                    if(msg.status == 403) {
                        sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
                    }
                    //general error
                    console.log("Error");
                    console.log(msg);
                    $.each(msg.responseJSON.errors,function (key , val){
                        sweetAlert2('warning', 'Check field inputs.');
                    });
                }
            });
        }
    });

</script>
@stop
