@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!-- PAGE-HEADER -->
				@include('layouts/pageContentHeader/store')
				<!-- PAGE-HEADER END -->
				<input type="file" id="import_csv_xlsx_file" name="file" hidden>
                @include('stores/clinical/patients/index')
			</div>
			@include('sweetalert2/script')
			@include('stores/clinical/pioneerPatients/modals/import')

		</div>
		<!--end page wrapper -->

@stop
@section('pages_specific_scripts')   
<style>
    /* #patients_table tbody tr td:nth-child(2),
    #patients_table tbody tr td:nth-child(3),
    #patients_table tbody tr td:nth-child(4) { 
        filter: blur(4px); 
        color: transparent;
        text-shadow: 0 0 4px rgba(0,0,0,0.5); 
    } */
</style>
<script>
    let table_patients = '';
    let menu_store_id = {{request()->id}};

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
		//console.log('fire');
	}

	$(document).ready(function() {
        let menu_store_id = {{request()->id}};
        // $('.imageuploadify-file-general-class').imageuploadify();

        loadPatients();

    });

    $('.imageuploadify-file-general-class').click(function () {
        $('.imageuploadify-container').remove();
    }); 

    function clickUploadBtn() {
        $('.imageuploadify-message').html('Drag&Drop<br> Only accepts <b>CSV</b> or <b>XLSX</b> Single Excel File Only');
        $('#import_pioneer_patient_modal').modal('show');
    }

    function loadPatients()
    {
        const patients_table = $('#patients_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBtp',
            order: [[11, 'desc']],
			buttons: [
				// { 
				// 	text: '+ Add New Patient', 
				// 	className: 'btn btn-primary btn-sm ', 
				// 	action: function ( e, dt, node, config ) {
				// 		showAddNewForm();
				// 	}
				// },
                {
                    text: '<i class="bx bx-cloud-upload me-2"></i>Import CSV or XLSX', 
                    className: 'btn btn-success btn-sm', 
                    action: function ( e, dt, node, config ) {
                        clickUploadBtn();
                    }
                },
			],
            pageLength: 50,
            searching: true,
            ajax: {
                url: "/store/clinical/pioneer-patients/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.source = 'pioneer';
                    data.pharmacy_store_id = menu_store_id;
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
			columns: [
				{ data: 'id', name: 'id', title: 'ID', visible: true},
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
				{ data: 'status_text', name: 'status_text', title: 'Status', visible: true},
				{ data: 'address', name: 'address', title: 'Address', visible: true},
				{ data: 'city', name: 'city', title: 'City', visible: true},
				{ data: 'state', name: 'state', title: 'State', visible: true},
				{ data: 'zip_code', name: 'zip_code', title: 'Zip Code', visible: true},
				{ data: 'phone_number', name: 'phone_number', title: 'Phone Number', visible: true},
				{ data: 'pioneer_id', name: 'pioneer_id', title: 'Pioneer ID', visible: true},
                { data: 'created_at', name: 'created_at', title: 'Date Created', visible: true},
				{ data: 'updated_at', name: 'updated_at', title: 'Updated At', visible: true},
				// { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false},
			],
            initComplete: function( settings, data ) {
                selected_len = patients_table.page.len();
				
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
		table_patients.buttons().container().appendTo( '.dt-card-header' );
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
			window.location.href = `/store/clinical/${menu_store_id}/pioneer-patients/facesheet/${id}`;
		});
		$('#patients_table').css('cursor', 'pointer');
    }
	

</script>


@stop
