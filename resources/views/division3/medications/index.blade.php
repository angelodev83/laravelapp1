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
				</select>
				<input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table id="medication_table" class="table row-border hover" style="width:100%">
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
	@include('division3/medications/modal/csv-upload-form')
</div>
<!--end page wrapper -->
@stop
@section('pages_specific_scripts')  
<script>
	let table_medication;

	$(document).ready(function() {
        
        const medication_table = $('#medication_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            dom: 'fBtip',
            buttons: [
                {
					extend: 'csv',
					className: 'btn btn-info', text:'Export to CSV',
					exportOptions: {
						columns: ' :not(.not_exportable)'
					}
				},
                { text: 'Upload CSV', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
            ],
            pageLength: 50,
            searching: true,
            ajax: {
                url: "/admin/medications/data",
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
                { data: 'med_id', name: 'med_id', title: 'ID', visible: true, },
                { data: 'name', name: 'name', title: 'NAME'},
                { data: 'ndc', name: 'ndc', title: 'NDC' },
                { data: 'package_size', name: 'package_size', title: 'PACKAGE SIZE' },
                { data: 'balance_on_hand', name: 'balance_on_hand', title: 'BALANCE ON HAND' },
                { data: 'therapeutic_class', name: 'therapeutic_class', title: 'THERAPEUTIC CLASS' },
                { data: 'category', name: 'category', title: 'CATEGORY' },
                { data: 'manufacturer', name: 'manufacturer', title: 'MANUFACTURER' },
				{ data: 'rx_price', name: 'rx_price', title: 'RX PRICE' },
				{ data: '340b_price', name: '340b_price', title: '340B PRICE' },
				{ data: 'last_update_date', name: 'last_update_date', title: 'LAST UPDATE' },
            ],
            initComplete: function( settings, json ) {
                selected_len = table_medication.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_medication = medication_table;

        // Placement controls for Table filters and buttons
		medication_table.buttons().container().appendTo( '.dt-card-header' );
        $('#search_input').val(medication_table.search());
		$('#search_input').keyup(function(){ medication_table.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { medication_table.page.len($(this).val()).draw() });

    });

    $('#fileDropArea').on('click', function (e) {
		e.preventDefault();
		$('#file').trigger('click');//open file selection
		e.stopPropagation();
	});

	function showAddNewForm(){
        $("#file").hide();
        $('#droparea_text').text('');
		$('.file_title').remove();
        $('#csvUpload_modal').modal('show');
        $('#modal_title').text('CSV UPLOAD');
        $('#droparea_text').append('<i style="font-size: 50px;" class="file_title fw-bold lead bx bx-cloud-upload"></i><p class="file_title">DROP FILE OR CLICK TO UPLOAD CSV</p>');
        $('#csvUpload_modal #modal_title').text('CSV UPLOAD FORM');

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

    $('#csvUpload_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
        $('.file_title').remove();
    });
	
</script> 
@stop
