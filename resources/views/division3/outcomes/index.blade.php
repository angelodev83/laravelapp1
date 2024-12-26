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
				<h6 class="m-2 float-start ms-3">Outcomes Daily Report</h6>
                @can('menu_store.clinical.mtm_outcomes_report.create')
				    <button class="dt-button btn btn-primary float-end" tabindex="0" aria-controls="" type="button" onclick="showUploadForm()"><span>Upload CSV/XLSX</span></button>
                @endcan
			</div>
			<div class="card-body">
				<div class="table-responsive">
							<table id="outcomes_table" class="table" style="width:100%">
								<thead></thead>
								<tbody>                                   
								</tbody>
								
							</table>
						</div>
			</div>
		</div>
	</div>

@include('sweetalert2/script')
@include('division3/outcomes/modal/delete-report-confirmation')
@include('division3/outcomes/modal/csv-upload-form')
</div>
<!--end page wrapper -->
@stop
@section('pages_specific_scripts')  
<script>
$(document).ready(function() {	
    let menu_store_id = {{request()->id}}
	outcomes_table = $('#outcomes_table').DataTable({
		scrollX: true,
		serverSide: true,
		stateSave: false,
		dom: 'fBtip',
		buttons: [],
		lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
		order: ['4', 'DESC'],
		pageLength: 50,
		searching: true,
		// buttons: [
		// 	{ text: 'Upload CSV', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
		// 		showUploadForm();
		// 	}},
		// ],
		ajax: {
			url: "/store/clinical/mtm-outcomes-reports/data",
			type: "POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: function (data) {
				data.search = $('input[type="search"]').val();
                data.pharmacy_store_id = menu_store_id;
			},
			error: function (msg) {
				handleErrorResponse(msg);
			}
		},
		columns: [
			{ data: 'id', name: 'id', title: 'ID', visible: true, sortable:  false},
			{ data: 'date_reported', name: 'date_reported', title: 'Date Reported', visible: true, sortable:  false},
			{ data: 'patients', name: 'patients', title: 'Patients', visible: true, sortable:  false},
			{ data: 'tips_completed', name: 'tips_completed', title: 'Tips Completed', visible: true, sortable:  false},
			{ data: 'tips_completion_rate', name: 'tips_completion_rate', title: 'Tips Completion Rate', visible: true, sortable:  false},
			{ data: 'cmrs_completed', name: 'cmrs_completed', title: 'CMRs Completed', visible: true, sortable:  false},
			{ data: 'cmrs_completion_rate', name: 'cmrs_completion_rate', title: 'CMRs Completion Rate', visible: true, sortable:  false},
			{ data: 'created_at', name: 'created_at', title: 'Date Created', visible: true, sortable:  false, render: function(data, type, row) {
                    return `${row.formatted_created_at}`;
            } },
			{ data: 'actions', name: 'actions', title: 'Action' , orderable: false, sortable:  false},
		],
	});

	// Placement controls for Table filters and buttons
	outcomes_table.buttons().container().appendTo( '.dt-card-header' );
	$('#search_input').keyup(function(){ outcomes_table.search($(this).val()).draw() ; })
	$('#length_change').change( function() { outcomes_table.page.len($(this).val()).draw() });
});

function updateItem(itemId, value) {
        
	$.ajax({
		url: '/admin/monthly_report/update_report',
		type: 'POST',
		data: {
			'id': itemId,
			'value': value
		},
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		success: function(response) {
			console.log('Update successful');
		},
		error: function(jqXHR, textStatus, errorThrown) {
			handleErrorResponse(errorThrown);
			console.log('Update failed: ' + errorThrown);
		}
	});

}

	function showUploadForm(){
        $("#file").hide();
        $('#droparea_text').text('');
		$('.file_title').remove();
        $('#csvUpload_modal').modal('show');
        $('#modal_title').text('CSV/XLSX UPLOAD');
        $('#droparea_text').append('<i style="font-size: 50px;" class="file_title fw-bold lead bx bx-cloud-upload"></i><p class="file_title">DROP FILE OR CLICK TO UPLOAD CSV/XLSX</p>');
        $('#csvUpload_modal #modal_title').text('XLSX UPLOAD FORM');

        $('#fileDropArea').on('dragover', function (e) {
            e.preventDefault();
            $(this).css('border-style', 'dotted'); // Change border style to dotted on dragover
            $(this).css('background', '#eee'); // Change border style to dotted on dragover
            e.stopPropagation();
        }).on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            $(this).css('background', '#fff'); // Change border style to dotted on dragover
            e.stopPropagation();
        });

        $('#fileDropArea').on('dragenter', function (e) {
            e.preventDefault();
            e.stopPropagation();
        })

        $('#fileDropArea').on('drop', function (e) {
            e.preventDefault();
            // Get a reference to our file input
            const fileInput = document.querySelector('#file');

            var file = e.originalEvent.dataTransfer.files;
            
            $('#droparea_text').text('' + file[0].name + '');
            //transfer file in input file
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file[0]);
            fileInput.files = dataTransfer.files;

        });

        $('#file').change(function (){
            $('#droparea_text').text('' + $('#file')[0].files[0].name + '');
        });
    }
</script> 


@stop
