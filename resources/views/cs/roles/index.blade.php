@extends('layouts.master')

@section('content')
<div class="page-wrapper">
	<div class="page-content">
        <!-- PAGE-HEADER -->
        @include('layouts/pageContentHeader/index')
        <!-- PAGE-HEADER END -->

        <div class="card">
            <div class="card-header dt-card-header">
                <select name='length_change' id='length_change' class="table_length_change form-select">
                </select>
                <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
            </div>
            <div class="card-body">
                @if(session('role_status'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('role_status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
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
    @include('sweetalert2/script')
    @include('cs/roles/modals/add-role-form')
    @include('cs/roles/modals/edit-role-form')
</div>
@stop

@section('pages_specific_scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let table_role;

    function showAddNewForm(){
        $('#addRole_modal').modal('show');
    }

    $("#editRole_modal").on('show.bs.modal', function (e) {
        let triggerLink = $(e.relatedTarget);
        let name = triggerLink.data("name");
        let description = triggerLink.data("description");
        let id = triggerLink.data("id");
        
        $("input#ename").attr("value", name);
        $("#edescription").val(description);
        $("input#eid").attr("value", id);
    });

    $(document).ready(function() {
        const role_table = $('#dt_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            pageLength: 50,
            dom: 'fBtip',
            buttons: [
                { text: 'Add Role', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
            ],
            searching: true,
            ajax: {
                url: "/admin/role/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                
                { data: 'id', name: 'id', title: 'ID' },
                { data: 'name', name: 'name', title: 'Name' },
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = role_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_role = role_table;
        // Placement controls for Table filters and buttons
		table_role.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(table_role.search());
		$('#search_input').keyup(function(){ table_role.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_role.page.len($(this).val()).draw() });

    });

    function deleteRole(id){
        sweetAlertLoading();
        $.ajax({
            url: "/admin/role/delete_role",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: id
            },
            dataType: 'json',
            success:function(response){
                //sweetAlert(response.status,response.message);
                Swal.fire({
                    position: 'center',
                    icon: response.status,
                    title: response.message,
                    showConfirmButton: false,
                    timer: 4000
                });
                table_role.ajax.reload(null, false);
            }, 
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    

</script>



@stop