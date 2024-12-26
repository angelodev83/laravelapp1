@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
			<!-- PAGE-HEADER -->
            @include('layouts/pageContentHeader/store')
            <!-- PAGE-HEADER END -->

            <div class="mt-4 card">
                <div class="card-header drug-recall-notifications-card-header">
                    <select name='length_change' id='length_change' class="table_length_change form-select">
                    </select>
                    <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dt_table" class="table row-border table-hover dt-table-fixed-first-column" style="width:100%">
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
        @include('stores/procurement/drugRecallNotifications/modals/add')
        @include('stores/procurement/drugRecallNotifications/modals/edit')
        @include('stores/procurement/drugRecallNotifications/modals/delete')
        @include('stores/procurement/drugRecallNotifications/modals/view')
        @include('stores/procurement/drugRecallNotifications/modals/documents')
        @include('components/modal/delete-store-document')
        
@stop

@section('pages_specific_scripts')  

<style>
    
</style>

<script>
    let show_drug_recall_notification_id = null;
    let show_drug_recall_notification_reference_number = null;
    let table_drug_recall_notifications;
    let table_drug_recall_notification_documents;
    let menu_store_id = {{request()->id}};

    // onload
    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        
        tinymce.init({
		  selector: 'textarea.tinymce-content',
          height: 310,
          branding: false
		});

        var addModal = new bootstrap.Modal(document.getElementById('add_drug_recall_notifications_modal'), {
            backdrop: 'static',  // Prevents clicking outside to close
            keyboard: true      // Prevents escape key to close
        });

        // var editModal = new bootstrap.Modal(document.getElementById('edit_drug_recall_notifications_modal'), {
        //     backdrop: 'static',  // Prevents clicking outside to close
        //     keyboard: true      // Prevents escape key to close
        // });

        $('#add_drug_recall_notifications_modal #notice_date').datepicker({
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
        });

        loadDrugRecallNotifications();
        loadDrugRecallNotificationDocuments();
    });


    // functions
    function loadDrugRecallNotifications() {
        let data = {};        
        const dt_table = $('#dt_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            pageLength: 10,
            dom: 'fBtip',
            order: [[6, 'desc']],
            buttons: [
                @can('menu_store.procurement.drug_recall_notifications.create')
                    { 
                        text: '+ New Return', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                            showAddDrugRecallNotificationModal();
                        }
                    },
                @endcan
            ],
            searching: true,
            destroy: true,
            ajax: {
                url: "/store/procurement/drug-recall-notifications/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('#search_input').val();
                    data.pharmacy_store_id = menu_store_id;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'reference_number', name: 'reference_number', title: 'Reference Number', render: function(data, type, row) {
                    return `<div class="document_filename_link" onclick="showViewDrugRecallNotificationModal(${row.id})"><b>${data}</b></div>`;
                } },
                { data: 'notice_date', name: 'notice_date', title: 'Notice Date', render: function(data, type, row) {
                    return `${row.formatted_notice_date}`;
                } },
                { data: 'count_drugs', name: 'count_drugs', title: 'Count Items', orderable: false, searchable: false},
                { data: 'count_documents', name: 'count_documents', title: 'Files', orderable: false, searchable: false, render: function(data, type, row) {
                    return `<u class="document_filename_link" onclick="clickDrugRecallNotificationDocuments(${row.id}, '${row.reference_number}')">${data} files</u>`;
                } },
                { data: 'wholesaler', name: 'wholesaler', title: 'Wholesaler',  orderable: false, searchable: false},
                // { data: 'created_by', name: 'created_by', title: 'Created By', render: function(data, type, row) {
                //     return `${row.empAvatar}`;
                // } },
                { data: 'supplier_name', name: 'supplier_name', title: 'Supplier Name'},
                { data: 'created_at', name: 'created_at', title: 'Date Created' },
                { data: 'actions', name: 'actions', title: '' , orderable: false, searchable: false},
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

        table_drug_recall_notifications = dt_table;
        table_drug_recall_notifications.buttons().container().appendTo( '.drug-recall-notifications-card-header' );
        $('#search_input').val(table_drug_recall_notifications.search());
		$('#search_input').keyup(function(){ table_drug_recall_notifications.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_drug_recall_notifications.page.len($(this).val()).draw() });
    }

    function loadDrugRecallNotificationDocuments() {
        let data = {};        
        const dt_table = $('#dt_document_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            pageLength: 10,
            dom: 'fBtip',
            order: [[1, 'desc']],
            buttons: [
                @can('menu_store.procurement.drug_recall_notifications.update')
                    { 
                        text: 'Upload Documents', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                            clickDrugRecallNotificationUploadDocuments();
                        }
                    },
                @endcan
            ],
            searching: true,
            destroy: true,
            responsive: true,
            ajax: {
                url: "/store/procurement/drug-recall-notifications/documents/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('#show_drug_recall_notification_documents_modal #search_document_input').val();
                    data.parent_id = show_drug_recall_notification_id;
                    data.category = 'drugRecallNotification';
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'name', name: 'name', title: 'File', render: function(data, type, row) {
                    return `<a href="${row.s3Url}" target="_blank" class="text-black document_filename_link"><b>${data}</b></a>`;
                }  },
                { data: 'created_at', name: 'created_at', title: 'Date Created' },
                { data: 'actions', name: 'actions', title: 'Actions' , orderable: false, searchable: false},
            ]
        });

        table_drug_recall_notification_documents = dt_table;
        table_drug_recall_notification_documents.buttons().container().appendTo( '.document-card-header' );
        $('#search_document_input').val(table_drug_recall_notification_documents.search());
		$('#search_document_input').keyup(function(){ table_drug_recall_notification_documents.search($(this).val()).draw() ; })
    }

    function clickDrugRecallNotificationDocuments(id, reference_number) {
        show_drug_recall_notification_id = id;
        show_drug_recall_notification_reference_number = reference_number;
        table_drug_recall_notification_documents.draw();
        table_drug_recall_notification_documents.columns.adjust();
        showDrugRecallNotificationDocuments();
    }

    function reloadDocumentDataTable(id)
    {
        reloadDataTable();
        table_drug_recall_notification_documents.ajax.reload(null, false);
    }
    
    // event listeners
    $('#add_drug_recall_notifications_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(".imageuploadify-container").remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();   
    });

    $('#edit_drug_recall_notifications_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(".imageuploadify-container").remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $('.imageuploadify-file-general-class').click(function () {
        $('.imageuploadify-container').remove();
    });

    $('#show_drug_recall_notification_documents_modal #documents').change(function () {
        var formData = new FormData();
        var uploadFiles = $('#show_drug_recall_notification_documents_modal #documents').get(0).files;
        
        for (let i = 0; i < uploadFiles.length; i++) {
            formData.append("files[]", uploadFiles[i]);
            var kbSize = uploadFiles[i].size/1024;
            if(kbSize > 100000) {
                sweetAlert2('warning', 'File(s) size max exceeds from 100 MB');
                return;
            }
        }

        formData.append("parent_id", show_drug_recall_notification_id);
        formData.append("category", 'drugRecallNotification');
        formData.append("pharmacy_store_id", menu_store_id);

        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: `/store/procurement/drug-recall-notifications/upload`,
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(res) {
                reloadDataTable();
                table_drug_recall_notification_documents.ajax.reload(null, false);
                sweetAlert2('success', 'Files has been uploaded.');

            },error: function(msg) {
                handleErrorResponse(msg);
                //general error
                console.log("Error");
                console.log(msg.responseText);
            }

        });
    })

    function populateNormalSelect(_selector, _model_id, _url, params = {}, _id = null)
    {
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: _url,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(params),
            success: function(data) {
            
                var len = data.data.length;
                
                for( var k = 0; k<len; k++){
                    var kid = data.data[k]['id'];
                    var kname = data.data[k]['name'];
                    if(kid==_id){$(_selector).append("<option selected value='"+kid+"'>"+kname+"</option>");}
                    else{
                        $(_selector).append("<option value='"+kid+"'>"+kname+"</option>");
                    }
                }
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function searchSelect2ApiDrug(_selector, _modal_id, _med_id = null)
    {
        $(_selector).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $(`#${_modal_id} .modal-content`),

            multiple: false,
            minimumInputLength: 1,
            minimumResultsForSearch: 10,
            ajax: {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: "/admin/medications/getNames",
                dataType: "json",
                type: "POST",
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        limit: 10
                    }
                    if(_med_id != null) {
                        var q = { med_id: _med_id, not: 'med_id' };
                        queryParameters = {...queryParameters, ...q}
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            return {
                                text: item.name,
                                id: item.med_id
                            }   
                        })
                    };
                }  
            },
        });
    }

    function reloadDataTable()
    {
        table_drug_recall_notifications.ajax.reload(null, false);
    }
</script>
@stop
