@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!-- PAGE-HEADER -->
				@include('layouts/pageContentHeader/store')
				<!-- PAGE-HEADER END -->
				<div class="card">
					<div class="card-header">
					<select name='length_change' id='length_change' class="table_length_change form-select">
                    </select>
					<input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id="patients_table" class="table row-border table-hover" style="width:100%">
								<thead></thead>
								<tbody>                                   
								</tbody>
								
							</table>
						</div>
					</div>
				</div>
			</div>
			@include('sweetalert2/script')
			@include('stores/clinical/tebra/modal/add-patient-form')
			@include('stores/clinical/tebra/modal/edit-patient-form')
			@include('stores/clinical/tebra/modal/delete-patient-confirmation')

		</div>
		<!--end page wrapper -->

@stop
@section('pages_specific_scripts')   
<script>
    let table_patients = '';
	let menu_store_id = {{request()->id}};

	function showAddNewForm(){
        $('#add_patient_modal').modal('show');
    }
	function ShowEditForm(id, ka, ma, birthdate, address, city, state, zip_code, phone_number) {
		$('#edit_patient_modal').modal('show');
		$('#EditForm #patient_id').val(id);
		// $('#edit_firstname').val(firstname);
		// $('#edit_lastname').val(lastname);
		// $('#edit_birthdate').val(birthdate);
		// $('#edit_address').val(address); 
		// $('#edit_city').val(city);      
		// $('#edit_state').val(state);    
		// $('#edit_zip_code').val(zip_code);
		// $('#edit_phone_number').val(phone_number); 
		if(ka == '1'){
			$('#known_allergies').prop('checked', true);
		}
		else{
			$('#known_allergies').prop('checked', false);
		}
		if(ma == '1'){
			$('#medication_allergies').prop('checked', true);
		}
		else{
			$('#medication_allergies').prop('checked', false);
		}
		//console.log('fire');
	}

    function populateTable(){
        $(".pop-hide-control").text('Please wait!');
        $(".pop-hide-control").attr('disabled', 'disabled');
        sweetAlertLoading();
        $.ajax({
            //laravel requires this thing, it fetches it from the meta up in the head
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/admin/tebra/get_all_patients",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                $(".pop-hide-control").text('+ Populate From Tebra');
                $(".pop-hide-control").removeAttr('disabled');
                $(".add-hide-control").show();
                $(".pop-hide-control").hide();
                
                table_patients.ajax.reload(null, false);
                sweetAlert2(data.status, data.message);
                
            },
            error: function(msg, errorType, errorThrown) {
				handleErrorResponse(errorThrown);
                $(".pop-hide-control").text('+ Populate From Tebra');
                $(".pop-hide-control").removeAttr('disabled');
                //general error
                sweetAlert2(errorType, JSON.stringify(msg.responseJSON.message));
                console.log("Error");
                console.log(msg.responseText);
            }


        });
    }
	

	$(document).ready(function() {
        $('.add-hide-control').hide();
        $('.pop-hide-control').hide();

        const patients_table = $('#patients_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            dom: 'fBtip',
			buttons: [
				
				@can('menu_store.clinical.tebra_patients.create')
                { 
					text: '+ Populate From Tebra', 
					className: 'btn btn-primary btn-sm pop-hide-control', 
					action: function ( e, dt, node, config ) {
						populateTable();
					}
				},
				@endcan
			],
            pageLength: 50,
            searching: true,
            ajax: {
                url: "/admin/divisiontwob/patients/get_data",
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
				{ data: 'id', name: 'tebra_id', title: 'ID', visible: true},
				{ data: 'firstname', name: 'firstname', title: 'First Name', visible: true,
					render: function(data, type, row) {
						return '<b>' + data + '</b>';
					}
				},
				{ data: 'lastname', name: 'lastname', title: 'Last Name', visible: true,
					render: function(data, type, row) {
						return '<b>' + data + '</b>';
					}
				},
				{ data: 'birthdate', name: 'birthdate', title: 'Birthdate', visible: true},
				{ data: 'created_at', name: 'created_at', title: 'Date Created', visible: true},
				{ data: 'updated_at', name: 'updated_at', title: 'Updated At', visible: true},
				{ data: 'address', name: 'address', title: 'Address', visible: true},
				{ data: 'city', name: 'city', title: 'City', visible: true},
				{ data: 'state', name: 'state', title: 'State', visible: true},
				{ data: 'zip_code', name: 'zip_code', title: 'Zip Code', visible: true},
				{ data: 'phone_number', name: 'phone_number', title: 'Phone Number', visible: true},
				{ data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false},
			],
            initComplete: function( settings, data ) {
                selected_len = patients_table.page.len();
				if(data.totalCount === 0){
                    $('.add-hide-control').hide();
                    $('.pop-hide-control').show();
                }
                else{
                    $('.add-hide-control').show();
                    $('.pop-hide-control').hide();
                }
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            },
            drawCallback: function(settings) {
                var api = this.api();
                var dataLength = api.rows().data().length;

                if (dataLength === 0) {
                    // Do something when no data is available
                } else {
                    // Do something when there is available data
                }
            },
        });

		table_patients = patients_table;
        
        // Placement controls for Table filters and buttons
		table_patients.buttons().container().appendTo( '.card-header' );
        $('#search_input').val(table_patients.search());
		$('#search_input').keyup(function(){ table_patients.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { table_patients.page.len($(this).val()).draw() });
		$('#patients_table').on('click', 'tr', function() {
			// Check if the clicked element is a button or is contained within a button
			if ($(event.target).is('button') || $(event.target).closest('button').length > 0) {
				return; // Do nothing if it's a button or contained within a button
			}
			id = table_patients.row(this).data().id;
			console.log(id);
			// window.location.href = '/admin/divisiontwob/patients/facesheet/'+id;
			window.location.href = `/store/clinical/${menu_store_id}/tebra-patients/facesheet/${id}`;
		});

		$('#patients_table').css('cursor', 'pointer');

    });

	    
	

</script>

@stop
