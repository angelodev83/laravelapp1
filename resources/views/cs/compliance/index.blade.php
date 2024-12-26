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
                <div class="table-responsive">
                    <table id="employee_table" class="table row-border hover" style="width:100%">
                        <thead></thead>
                        <tbody>                                   
                        </tbody>
                        <tfooter></tfooter>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('cs/modals/add-employee-form')
    @include('cs/modals/edit-employee-form')
    @include('cs/compliance/delete-form')
</div>
@stop

@section('pages_specific_scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let table_employee;

    function showAddNewForm(){
        $('#addEmployee_modal').modal('show');
    }

    $("#updateEmployee_modal").on('show.bs.modal', function (e) {
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5', 
			modal: true,
			//beforeShowDay: today,
			autoclose: true,
   			orientation: "right",
        });
        let triggerLink = $(e.relatedTarget);
        let lastname = triggerLink.data("lastname");
        let firstname = triggerLink.data("firstname");
        let dob = triggerLink.data("dob");
        let email = triggerLink.data("email");
        let position = triggerLink.data("position");
        let id = triggerLink.data('id');
        let startdate = triggerLink.data('startdate');
        let enddate = triggerLink.data('enddate');
        let location = triggerLink.data('location');
        let status = triggerLink.data('status');
        let oigStatus = triggerLink.data('oigstatus');

        if(status == 'Active'){
            $('#estatus').prop('checked', true);
        }
        else{
            $('#estatus').prop('checked', false);
        }

        let arr = ['Match', 'No Match'];
        var arr_len = arr.length;
        for( var a = 0; a<arr_len; a++){

            if(arr[a]==oigStatus){$("#eoig_status").append("<option selected value='"+arr[a]+"'>"+arr[a]+"</option>");}
            else{
                $("#eoig_status").append("<option value='"+arr[a]+"'>"+arr[a]+"</option>");
            }
        }

        const startDateInput = document.getElementById('estartdate');
        startDateInput.value = startdate;
        const endDateInput = document.getElementById('eenddate');
        endDateInput.value = enddate;
        const birthDate = document.getElementById('ebirthdate');
        birthDate.value = dob;
        
        $("input#elastname").val(lastname);
        $("input#efirstname").val(firstname);
        $("input#eemail").val(email);
        $("input#eposition").val(position);
        $("input#eid").val(id);
        $("input#elocation").val(location);
        $("input#eold_oig_status").val(oigStatus);
    });

    function checkOIGOffline(){
        sweetAlertLoading();
        $.ajax({
            url: "oig_list/update_offline",
            type: 'get',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                table_employee.ajax.reload(null, false);
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }

    function deleteEmployee(id){
        sweetAlertLoading();
        $.ajax({
            url: "/admin/oig_check/delete_employee",
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
                table_employee.ajax.reload(null, false);
            },
            error: function(msg) {
                handleErrorResponse(msg);
            }
        });
    }
    
    $('#addEmployee_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $('#updateEmployee_modal').on('hidden.bs.modal', function(){
        //$(this).find('#employeeUpdateForm')[0].reset(); 
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
    });

    $(document).ready(function() {
        const employee_table = $('#employee_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            dom: 'fBtip',
            buttons: [
                
                { extend: 'csv', className: 'btn btn-primary', text:'Download CSV' },
                
                // { text: 'Add New', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                //     showAddNewForm();
                // }},
                { text: 'Check Now', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    checkOIGOffline();
                }},
            ],
            pageLength: 50,
            searching: true,
            ajax: {
                url: "/admin/oig_check/data",
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
                { data: 'id', name: 'id', title: 'ID', visible: true},
                { data: 'oig_status', name: 'oig_status', title: 'OIG Status' , orderable: true},
                { data: 'lastname', name: 'lastname', title: 'Last Name' },
                { data: 'firstname', name: 'firstname', title: 'First Name' },
                { data: 'email', name: 'email', title: 'Email' },
                { data: 'position', name: 'position', title: 'Position' },
                { data: 'status', name: 'status', title: 'Status' },
                { data: 'location', name: 'location', title: 'Location' },
                { data: 'updated_at', name: 'updated_at', title: 'Date Check' },
               
            ],
            initComplete: function( settings, json ) {
                selected_len = employee_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_employee = employee_table;
        // Placement controls for Table filters and buttons
		table_employee.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(employee_table.search());
		$('#search_input').keyup(function(){ table_employee.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_employee.page.len($(this).val()).draw() });
    });

    function updateOig()
    {   
        sweetAlertLoading();
        $.ajax({
            type:'GET',
            url: "/admin/oig_list/update_oig",
            cache:false,
            contentType: false,
            processData: false,
            datatype: 'json',
            success:function(response){
                sweetAlert2('success', 'Upload Complete.');
                table_employee.ajax.reload(null, false);
            },
            error: function(response){
                handleErrorResponse(response);
                console.log("error");
                console.log(response);
            }
        });  
    }

    function sweetAlertLoading(){
        Swal.fire({
            title: 'Processing... please wait.',
            //html: 'data uploading',// add html attribute if you want or remove
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading()
            },
        });   
    }

    function sweetAlert2(icon, title){
        Swal.fire({
            position: 'center',
            icon: icon,
            title: title,
            showConfirmButton: false,
            timer: 4000
        });
    }
</script>

<style>
    .swal-confirmButton{
        border: 0;
        border-radius: .25em;
        background: initial;
        background-color: #2778c4;
        color: #fff;
        font-size: 1.0625em;
        margin: .3125em;
        padding: .625em 1.1em;
        box-shadow: none;
        font-weight: 500;
    }
    .swal-confirmButton:focus {
        outline: 0;
        box-shadow: 0 0 0 3px rgba(100,150,200,.5);
    }
    .swal-confirmButton:hover{
        background-image: linear-gradient(rgba(0,0,0,.1),rgba(0,0,0,.1));
    }
    .swal-cancelButton{
        border-style: solid;
        border-width: thin;
        border-radius: .25em;
        background: initial;
        background-color: #fff;
        border-color: rgba(14,14,14,.26);
        color: #464b51;
        font-size: 1.0625em;
        margin: .3125em;
        padding: .625em 1.1em;
        box-shadow: none;
        font-weight: 500;
    }
    .swal-cancelButton:hover{
        background-color: rgba(14,14,14,.26);
        color: #fff;
    }
    .swal-cancelButton:focus {
        outline: 0;
        box-shadow: 0 0 0 3px rgba(14,14,14,.26);
    }
</style>
<style>
    .img-flag {
        height: 56px;
        width: 56px;
        margin-top:-4px;
        margin-right:5px;
    }
</style>
@stop