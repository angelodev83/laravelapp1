@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
			<!-- PAGE-HEADER -->
            @include('layouts/pageContentHeader/store')
            <!-- PAGE-HEADER END -->
            
            <!--start row-->
            <div class="row">
                <div class="col-12 col-lg-3">
                    @include('stores/releaseOfInformation/partials/menu')
                </div>

                <div class="col-12 col-lg-9">
                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-9">
                                    <div class="fm-search">
                                        <div class="mb-0">
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text bg-transparent"><i class='fa fa-search'></i></span>
                                                <input type="text" id="search_input" class="table_search_input form-control" placeholder="Search" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select mt-2" id="length_change">
                                    </select>
                                </div>
                            </div>


                            @include('stores/releaseOfInformation/partials/table')

                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->
            
            
		</div>
		<!--end page wrapper -->
        @include('sweetalert2/script')
        @include('stores/releaseOfInformation/modals/import')
@stop

@section('pages_specific_scripts')  
<script>

let table_patients;
    let menu_store_id = {{request()->id}};
    let folder_id;
    let page_id;
    let facility_name = '';

    $(document).ready(function() {
        menu_store_id = {{request()->id}};
        facility_name = '';

        // $('#clinical-lgi-all').addClass('selected');

        loadPatients();
    });

    function loadPatients()
    {
        const patients_table = $('#dt_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBtp',
            order: [[6, 'desc']],
			buttons: [

			],
            pageLength: 50,
            searching: true,
            ajax: {
                url: "/store/jot-form/release-of-information/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                }
            },
			columns: [
				{ data: 'jot_form_uid', name: 'jot_form_uid', title: 'JF-UID', visible: true},
				{ data: 'patient_firstname', name: 'patient_firstname', title: 'First Name', visible: true,
					render: function(data, type, row) {
						return '<b>' + data + '</b>';
					}
				},
				{ data: 'patient_lastname', name: 'patient_lastname', title: 'Last Name', visible: true,
					render: function(data, type, row) {
						return '<b>' + data + '</b>';
					}
				},
				{ data: 'patient_birth_date', name: 'patient_birth_date', title: 'Birthdate', visible: true},
				{ data: 'signed_date', name: 'signed_date', title: 'Signed Date', visible: true,
					render: function(data, type, row) {
						return row.formatted_signed_date;
					}
                },
                { data: 'relationship_to_patient', name: 'relationship_to_patient', title: 'Relationship to Patient', visible: true},
                { data: 'jot_form_created_at', name: 'jot_form_created_at', title: 'JF Date Created', visible: true,
					render: function(data, type, row) {
						return row.formatted_jot_form_created_at;
					}
				},
			],
            initComplete: function( settings, data ) {
                selected_len = patients_table.page.len();

                // $('#total_pioneer_patients').html(data.recordsTotal);
				
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            },
            drawCallback: function(settings) {
                var api = this.api();
                var dataLength = api.rows().data().length;

                // console.log("fire init drawCallback");

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
		$('#dt_table').on('click', 'tr', function() {
			// Check if the clicked element is a button or is contained within a button
			if ($(event.target).is('button') || $(event.target).closest('button').length > 0) {
				return; // Do nothing if it's a button or contained within a button
			}
			id = table_patients.row(this).data().id;
			console.log(id);
			window.location.href = `/store/jot-form/${menu_store_id}/release-of-information/details/${id}`;
		});
		$('#dt_table').css('cursor', 'pointer');
    }

    $('.imageuploadify-file-general-class').click(function () {
        $('.imageuploadify-container').remove();
    }); 

    function clickUploadBtn() {
        $('.imageuploadify-message').html('Drag&Drop<br> Only accepts <b>CSV</b> or <b>XLSX</b> Single Excel File Only');
        $('#import_pioneer_patient_modal').modal('show');
    }

    function reloadDataTable(refresh = false)
    {
        if(refresh === false) {
            table_patients.ajax.reload(null, false);
        } else {
            location.reload();
        }
    }

    function clickSideMenuFilter(n = '', c = 'all')
    {
        $('.clinical-lgi').removeClass('selected');
        $(`#clinical-lgi-${c}`).addClass('selected');
        facility_name = n;
        table_patients.draw();
    }

</script>
@stop
