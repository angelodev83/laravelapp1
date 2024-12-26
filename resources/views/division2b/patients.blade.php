@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!-- PAGE-HEADER -->
				@include('layouts/pageContentHeader/index')
				<!-- PAGE-HEADER END -->
				<div class="card">
					<div class="card-header dt-card-header">
					<select name='length_change' id='length_change' class="table_length_change form-select">
						<option value='50'>Show 50</option>
						<option value='50'>50</option>
						<option value='100'>100</option>
						<option value='150'>150</option>
						<option value='200'>200</option>
					</select>
					<input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id="orders_table" class="table table-striped table-bordered" style="width:100%">
								<thead></thead>
								<tbody>                                   
								</tbody>
								
							</table>
						</div>
					</div>
				</div>
			</div>
			@include('sweetalert2/script')
			@include('division2b/mail_orders/modal/add-patient-form')
			@include('division2b/mail_orders/modal/edit-patient-form')
			@include('division2b/mail_orders/modal/delete-patient-confirmation')

		</div>
		<!--end page wrapper -->

@stop
@section('pages_specific_scripts')   
<script>

	function showAddNewForm(){
        $('#add_patient_modal').modal('show');
    }
	function ShowEditForm(id, firstname, lastname, birthdate, address, city, state, zip_code, phone_number) {
		$('#edit_patient_modal').modal('show');
		$('#EditForm #patient_id').val(id);
		$('#edit_firstname').val(firstname);
		$('#edit_lastname').val(lastname);
		$('#edit_birthdate').val(birthdate);
		$('#edit_address').val(address); 
		$('#edit_city').val(city);      
		$('#edit_state').val(state);    
		$('#edit_zip_code').val(zip_code);
		$('#edit_phone_number').val(phone_number); 
		console.log('fire');
	}


	$(document).ready(function() {
	
        const employee_table = $('#orders_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            dom: 'fBtip',
			buttons: [
				{ 
					text: '+ Add New Patient', 
					className: 'btn btn-primary btn-sm', 
					action: function ( e, dt, node, config ) {
						showAddNewForm();
					}
				},
			],
            lengthMenu: [[10, 25, 50,100], [10, 25, 50,100]],
            order: ['4', 'DESC'],
            pageLength: 10,
            searching: true,
            ajax: {
                url: "/admin/divisiontwob/patients/patients_get_data",
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
				{ data: 'firstname', name: 'firstname', title: 'First Name', visible: true},
				{ data: 'lastname', name: 'lastname', title: 'Last Name', visible: true},
				{ data: 'birthdate', name: 'birthdate', title: 'Birthdate', visible: true},
				{ data: 'created_at', name: 'created_at', title: 'Date Created', visible: true},
				{ data: 'updated_at', name: 'updated_at', title: 'Updated At', visible: true},
				{ data: 'address', name: 'address', title: 'Address', visible: true},
				{ data: 'city', name: 'city', title: 'City', visible: true},
				{ data: 'state', name: 'state', title: 'State', visible: true},
				{ data: 'zip_code', name: 'zip_code', title: 'Zip Code', visible: true},
				{ data: 'phone_number', name: 'phone_number', title: 'Phone Number', visible: true},
				
				 { data: 'actions', name: 'actions', title: 'Action' , orderable: false},
			],
        });

		// Placement controls for Table filters and buttons
		employee_table.buttons().container().appendTo( '.dt-card-header' );
		$('#search_input').keyup(function(){ employee_table.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { employee_table.page.len($(this).val()).draw() });

    });

	    
	

</script>


@stop
