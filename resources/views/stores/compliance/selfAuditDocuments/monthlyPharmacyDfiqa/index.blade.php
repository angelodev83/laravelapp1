@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
			<!-- PAGE-HEADER -->
            @include('layouts/pageContentHeader/store')
            <!-- PAGE-HEADER END -->

            <div class="mt-4 card">
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
        @include('stores/compliance/selfAuditDocuments/monthlyPharmacyDfiqa/modals/delete')
        @include('stores/compliance/selfAuditDocuments/monthlyPharmacyDfiqa/modals/add')
@stop

@section('pages_specific_scripts')  
<script>

    let table_document;
    let menu_store_id = {{request()->id}};
    let nav_code = 'm_p_dfiqa';

    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        nav_code = 'm_p_dfiqa';
        loadDocuments();

    });

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
                    text: '<i class="bx bx-cloud-upload me-2"></i>Upload Document(s)', 
                    className: 'btn btn-primary', 
                    action: function ( e, dt, node, config ) {
                        //clickUploadBtn();
                        showAddModal();
                    }
                },
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/store/compliance/self-audit-documents/monthly-pharmacy-dfiqa/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                    data.tag_code = 'm_p_dfiqa';
                    data.order[0]['column'] = 3;
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
                { data: 'task_month', name: 'task_month', title: 'Task Month Year', searchable: false, orderable: false},
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

    function searchSelect2Api(_select_id, _modal_id, _url) {
        $(`#${_select_id}`).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $(`#${_modal_id}`),

            multiple: false,
            minimumInputLength: 1,
            minimumResultsForSearch: 10,
            ajax: {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: _url,
                dataType: "json",
                type: "POST",
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        limit: 10,
                        pharmacy_store_id: menu_store_id
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
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

    // function clickUploadBtn() {
    //     $('#upload_documents').click();
    // }

    // $('#upload_documents').change(function () {
    //     if(this.files.length){
    //         var uploadFiles = event.target.files;  
    //         var formData = new FormData();

    //         for (let i = 0; i < uploadFiles.length; i++) {
    //             formData.append("files[]", uploadFiles[i]);
    //             var kbSize = uploadFiles[i].size/1024;
    //             if(kbSize > 100000) {
    //                 sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
    //                 return;
    //             }
    //         }
    //         formData.append("pharmacy_store_id", menu_store_id);
    //         formData.append("tag_code", nav_code);

    //         $.ajax({
    //             //laravel requires this thing, it fetches it from the meta up in the head
    //             headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //             type: "POST",
    //             url: `/store/compliance/self-audit-documents/monthly-pharmacy-dfiqa/add`,
    //             data: formData,
    //             contentType: false,
    //             processData: false,
    //             dataType: "json",
    //             success: function(data) {
    //                 table_document.ajax.reload(null, false);
    //                 sweetAlert2(data.status, "Files has been uploaded");
    //             },error: function(msg) {
    //                 if(msg.status == 403) {
    //                     sweetAlert2('warning', "403 Forbidden: You don't have permission to do this action");
    //                 }
    //                 //general error
    //                 console.log("Error");
    //                 console.log(msg);
    //                 $.each(msg.responseJSON.errors,function (key , val){
    //                     sweetAlert2('warning', 'Check field inputs.');
    //                 });
    //             }
    //         });
    //     }
    // });

    

</script>
@stop
