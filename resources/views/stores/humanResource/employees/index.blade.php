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
			@include('sweetalert2/script')
			{{-- @include('humanResources/employeesRelations/modal/add-employee-form')
    		@include('humanResources/employeesRelations/modal/edit-employee-form')
    		@include('humanResources/employeesRelations/modal/delete-form') --}}

            @include('admin/employees/modals/add')
		</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts') 
<script>
    var store = {{ Js::from($menuStores[request()->id]) }};
    let menu_store_id = {{request()->id}};
    let table_employee;
    let is_offshore = 1;

    function showAddNewForm(){
        $('#addEmployee_modal').modal('show');
    }

    $("#updateEmployee_modal").on('show.bs.modal', function (e) {
        let triggerLink = $(e.relatedTarget);
        let lastname = triggerLink.data("lastname");
        let firstname = triggerLink.data("firstname");
        let email = triggerLink.data("email");
        let position = triggerLink.data("position");
        let id = triggerLink.data('id');
        let startdate = triggerLink.data('startdate');
        let enddate = triggerLink.data('enddate');
        let location = triggerLink.data('location');
        let status = triggerLink.data('status');
        let dob = triggerLink.data('dob');

        if(status == 'Active'){
            $('#estatus').prop('checked', true);
        }
        else{
            $('#estatus').prop('checked', false);
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
    });

    function deleteEmployee(id){
        sweetAlertLoading();
        $.ajax({
            url: "/admin/human_resources/delete_employee",
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
    
    $('#addEmployee_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

    $('#updateEmployee_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end(); 
    });

    $(document).ready(function() {
        $(`#bs-stepper-circle-1`).css('color', '#ffffff');
        $(`#bs-stepper-circle-1`).css('background-color', '#15a0a3');
        menu_store_id = {{request()->id}};
        const employee_table = $('#employee_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBtp',
            buttons: [
                // { text: 'Sync with Trinet HR', className: 'btn btn-sm btn-info2', action: function ( e, dt, node, config ) {
                //     syncZenefits();
                // }},
                { extend: 'csv', className: 'btn btn-sm btn-success', text:'Export' },
                { text: '+ New Employee', className: 'btn btn-sm btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewEmployeeForm(menu_store_id);
                }},
            ],
            pageLength: 50,
            searching: true,
            ajax: {
                url: "/store/human-resource/employees/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.pharmacy_store_id = menu_store_id;
                    data.is_offshore = is_offshore;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                // { data: 'id', name: 'id', title: 'ID', visible: true},
                // { data: 'fullname', name: 'fullname', title: 'Full Name', orderable: false},
                // { data: 'lastname', name: 'lastname', title: 'Last Name' },
                // { data: 'firstname', name: 'firstname', title: 'First Name' },
                // { data: 'avatar', name: 'avatar', title: 'Full Name' , orderable: false, searchable: false},
                { data: 'firstname', name: 'firstname', title: 'Fullname', render: function(data, type, row) {
                    return '<div>' + row.avatar + '</div>';
                } },
                // { data: 'email', name: 'email', title: 'Email' },
                { data: 'position', name: 'position', title: 'Position' },
                { data: 'status', name: 'status', title: 'Status' },
               
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

    function showAddNewEmployeeForm(pharmacy_store_id){
        $(".form-control").removeClass("is-invalid");
        $('#pharmacy_store_id').val(pharmacy_store_id);
        let obj = store;
        $("#pharmacy_name").html(obj['name']);
        $('#add_employee_modal').modal('show');
    }

    function syncZenefits(){
        sweetAlertLoading_trinet();
        $.ajax({
            url: "/admin/zenefits/sync/"+menu_store_id,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success:function(response){
                swal2(response.status, response.message);
                table_employee.ajax.reload(null, false);
            },
            error: function(xhr, status, error) {
                handleErrorResponse(error);
                swal2(xhr.responseJSON.status, xhr.responseJSON.message)
                table_employee.ajax.reload(null, false);
            }

        });
    }


    function sweetAlertLoading_trinet(){
        Swal.fire({
            title: 'Syncing with Trinet HR',
            html: 'This might take a few seconds, Please wait..',// add html attribute if you want or remove
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading()
            },
        });  
    }


    //
    function getRandomColor() {
        return '#' + Math.floor(Math.random()*16777215).toString(16);
    }

    // Function to calculate contrast color
    function getContrastColor(hexcolor) {
        // Convert hex color to RGB
        var r = parseInt(hexcolor.substr(1, 2), 16);
        var g = parseInt(hexcolor.substr(3, 2), 16);
        var b = parseInt(hexcolor.substr(5, 2), 16);
        // Calculate YIQ (luminance)
        var yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
        // Return black or white based on luminance
        return (yiq >= 128) ? '#000000' : '#ffffff';
    }

    // Apply randomized background color and contrast text color to each employee ID
    document.querySelectorAll('.hr-employee').forEach(function(element) {
        // Get the employee ID from the data-id attribute
        var employeeId = element.dataset.id;
        // Generate a random background color
        var bgColor = getRandomColor();
        // Apply the background color
        element.style.backgroundColor = bgColor;
        // Calculate the contrast text color
        var textColor = getContrastColor(bgColor);
        // Apply the text color
        element.style.color = textColor;
        // Update the content with employee ID
        element.textContent = 'Employee ID: ' + employeeId;
        
        // document.getElementById(`hr-emp-${employeeId}`).color = textColor;
        // document.getElementById(`hr-emp-${employeeId}`).backgroundColor = bgColor;
    });

    function reloadDataTable()
    {
        table_employee.ajax.reload(null, false);
    }
    
</script>  
@stop
