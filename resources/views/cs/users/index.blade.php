@extends('layouts.master')

@section('content')
<div class="page-wrapper">
	<div class="page-content">
        <!-- PAGE-HEADER -->
        @include('layouts/pageContentHeader/index')
        <!-- PAGE-HEADER END -->

        <h6 class="mb-0 text-uppercase">DataTable</h6>
        <hr/>
        <div class="card">
            <div class="card-header dt-card-header">
                <select name='length_change' id='length_change' class="table_length_change form-select">
                </select>
                <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
            </div>
            <div class="card-body">
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
    @include('cs/users/modals/add-user-form')
    @include('cs/users/modals/edit-user-form')
    @include('cs/users/modals/delete-form')
</div>
@stop

@section('pages_specific_scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let table_user;

    function showAddNewForm(){
        $('#addUser_modal').modal('show');
        let data = {};
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/user/get_roles",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
                var len = data.data.length;
                
                $("#role_id").empty();
                for( var i = 0; i<len; i++){
                    var id = data.data[i]['id'];
                    var name = data.data[i]['name'];
                    // if(i==0){$("#role_id").append("<option selected value='"+id+"'>"+name+"</option>");}
                    $("#role_id").append("<option value='"+id+"'>"+name+"</option>");
                }
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    $("#editUser_modal").on('show.bs.modal', function (e) {
        let triggerLink = $(e.relatedTarget);
        let name = triggerLink.data("name");
        let email = triggerLink.data("email");
        let id = triggerLink.data("id");
        let roleid = triggerLink.data("roleid");
        
        $("input#ename").attr("value", name);
        $("input#eemail").attr("value", email);
        $("input#eid").attr("value", id);
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/admin/user/get_roles",
            data: '',
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
                var len = data.data.length;
                
                $("#role_id").empty();
                for( var i = 0; i<len; i++){
                    var id = data.data[i]['id'];
                    var name = data.data[i]['name'];
                    if(id==roleid){$("#erole_id").append("<option selected value='"+id+"'>"+name+"</option>");}
                    else{$("#erole_id").append("<option value='"+id+"'>"+name+"</option>");}
                }
            },
            error: function (msg)
            {
                handleErrorResponse(msg);
            }
        });
    });

    $(document).ready(function() {
        const user_table = $('#dt_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            pageLength: 50,
            dom: 'fBtip',
            buttons: [
                { text: 'Add User', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
                // {   extend: 'colvis',
                //     collectionLayout: 'fixed columns',
                //     collectionTitle: 'Column visibility control' }
            ],
            searching: true,
            ajax: {
                url: "/admin/user/data",
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
                { data: 'email', name: 'email', title: 'Email' },
                { data: 'role', name: 'role', title: 'Role' },
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false},
            ],
            initComplete: function( settings, json ) {
                selected_len = user_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_user = user_table;
        // Placement controls for Table filters and buttons
		table_user.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(table_user.search());
		$('#search_input').keyup(function(){ table_user.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_user.page.len($(this).val()).draw() });

    });

    function deleteUser(id){
        sweetAlertLoading();
        $.ajax({
            url: "/admin/user/delete_user",
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
                table_user.ajax.reload(null, false);
            },
            error: function(msg)
            {
                handleErrorResponse(msg);
            }
        });
    }

</script>


@stop