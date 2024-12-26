<div class="modal" id="file_modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title">Files</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">      
            <div class="row">
                <div class="col-md-6">
                    <label for="date" class="form-label">Date</label>
                    <p id="date"></p>
                </div>
                <div class="col-md-6">
                    <label for="total_cash_received" class="form-label">Total Cash Received</label>
                    <p id="total_cash_received">Total Cash Received</p>
                </div>
                <div class="col-md-6">
                    <label for="total_cash_deposited_to_bank" class="form-label">Total Cash Deposited to Bank</label>
                    <p id="total_cash_deposited_to_bank">Total Cash Deposited to Bank</p>
                </div>
                <div class="col-md-6">
                    <label for="total_check_received" class="form-label">Total Check Received</label>
                    <p id="total_check_received">Total Check Received</p>
                </div>
                <div class="card">
                    <input type="hidden" id="id">
                    <div class="card-header dt-card-header2">
                        <select name='length_change' id='length_change' class="table_length_change form-select">
                        </select>
                        <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table" class="table row-border table-hover" style="width:100%">
                                <thead></thead>
                                <tbody>                                   
                                </tbody>
                                <tfooter></tfooter>
                            </table>
                        </div>
                    </div>
                </div>
            </div><!--end row-->
        </div>
    </div>
  </div>
</div>

<script>
    function showFileModal(id)
    {   
        $('#file_modal #id').val(id);
        $('#file_modal').modal('show');   

        var btn = document.querySelector(`#data-file-btn-${id}`);
        let arr = JSON.parse(btn.dataset.array);
        // console.log('fire-------------', arr);
        
        $('#file_modal #date').text(arr.date);
        $('#file_modal #total_cash_received').text(arr.total_cash_received);
        $('#file_modal #total_cash_deposited_to_bank').text(arr.total_cash_deposited_to_bank);
        $('#file_modal #total_check_received').text(arr.total_check_received);

        
        $(document).ready(function() {
        
            const fileTable = $('#file_modal #table').DataTable({
                scrollX: true,
                serverSide: true,
                stateSave: true,
                dom: 'fBtip',
                order: [[0, 'desc']],
                buttons: [
                    @can('menu_store.eod_register_report.register.update')
                    { text: '+ New', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                        showAddFileForm(id);
                    }},
                    @endcan
                ],
                pageLength: 5,
                searching: true,
                ajax: {
                    url: `/store/eod-register-report/${menu_store_id}/register/get_file-data`,
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (data) {
                        data.search = $('#file_modal input[type="search"]').val();
                        data.id = $('#file_modal #id').val();
                    },
                    error: function (msg) {
                        handleErrorResponse(msg);
                    }
                },
                columns: [
                    { data: 'id', name: 'id', title: 'ID'},
                    { data: 'name', name: 'file_id', title: 'Name'},
                    { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false},
                ],
                initComplete: function( settings, json ) {
                    selected_len = fileTable.page.len();
                    $('#file_modal #length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
                    $('#file_modal #length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
                    $('#file_modal #length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
                    $('#file_modal #length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
                    $('#file_modal #length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
                }
            });

            file_table = fileTable;

            // Placement controls for Table filters and buttons
            fileTable.buttons().container().appendTo( '#file_modal .dt-card-header2' ); 
            $('#file_modal #search_input').val(fileTable.search());
            $('#file_modal #search_input').keyup(function(){ fileTable.search($(this).val()).draw() ; })
            $('#file_modal #length_change').change( function() { fileTable.page.len($(this).val()).draw() });

        });
    }

    function showAddFileForm(id)
    {   
        let fileInput = $('<input/>', {
            id: 'file',
            class: 'imageuploadify-file-general-class',
            name: 'file',
            type: 'file',
            accept: '*',
            multiple: ''
        });
        $('#bulk_modal #for-file').html(fileInput); 
        $('#bulk_modal #file').imageuploadify();
        $("#bulk_modal .imageuploadify-container").remove();
        $('#bulk_modal .imageuploadify-message').html('Drag&Drop Your File(s) Here To Upload');     
        
        $('#bulk_modal #id').val(id);
        $('#bulk_modal').modal('show'); 
    }

    function ShowDeleteFile(file_id){
        let data = {
            id: file_id,
        };
        console.log(file_id);
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#15a0a3",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
            }).then((result) => {
            if (result.isConfirmed) {
                sweetAlertLoading();
                $.ajax({
                    //laravel requires this thing, it fetches it from the meta up in the head
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: "DELETE",
                    url: `/store/eod-register-report/register/delete_file`,
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data) {
                        if(data.errors){
                            
                        }
                        else{
                            reloadFileDataTable();
                            Swal.close();
                        }
                    },error: function(msg) {
                        handleErrorResponse(msg);
                        //general error
                        console.log("Error");
                        console.log(msg.responseText);
                    }

                });

                // Swal.fire({
                //     title: "Deleted!",
                //     text: "Your file has been deleted.",
                //     icon: "success"
                // });
            }
        });
        

        
    }

</script>