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
					<table id="task_table" class="table row-border hover" style="width:100%">
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
	@include('division3/task/modal/add-form')
	@include('division3/task/modal/edit-form')
	@include('division3/task/modal/view-form')
	@include('division3/task/modal/delete-form')
	@include('division3/task/modal/bulk-upload-form')
</div>
<!--end page wrapper -->
@stop
@section('pages_specific_scripts')  
<script>
	let table_task;

	$(document).ready(function() {
        
        const task_table = $('#task_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: true,
            dom: 'fBtip',
            buttons: [
                { text: 'Add New', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showAddNewForm();
                }},
				{
					extend: 'csv',
					className: 'btn btn-info', text:'Export to CSV',
					// charset: 'UTF-16LE',
					// //fieldSeparator: '\t',
					// bom: true,
					exportOptions: {
						columns: [1,2,3,5,6,7,8,9,10,11,12,13,14,15],
						format:{
							body: function (data, row, column, node) {
								// Strip $ from salary column to make it numeric
								if(column === 3){
									splitData = data.split('\n');
									data = '';
									for (i=0; i < splitData.length; i++) {
										//add escaped double quotes around each line
										
										if (i + 1 < splitData.length) {
											data += splitData[i] + ',';
										}
										else{
											data += splitData[i];
										}
									}
									return data;
								}
								if(column === 2){
									if(data != ''){
										splitDate = data.split('-');
										data = splitDate[1]+'/'+splitDate[2]+'/'+splitDate[0];
										return data;
									}
									else{
										data = '';

										return data;
									}
								}
								if(column === 4){
									if(data != ''){
										splitDate = data.split('-');
										data = splitDate[1]+'/'+splitDate[2]+'/'+splitDate[0];
										return data;
									}
									else{
										data = '';

										return data;
									}
								}
								if(column === 5){
									if(data != ''){
										splitDate = data.split('-');
										data = splitDate[1]+'/'+splitDate[2]+'/'+splitDate[0];
										return data;
									}
									else{
										data = '';

										return data;
									}
								}
								if(column === 6){
									if(data != ''){
										splitDate = data.split('-');
										data = splitDate[1]+'/'+splitDate[2]+'/'+splitDate[0];
										return data;
									}
									else{
										data = '';

										return data;
									}
								}
								if(column === 8){
									if(data != ''){
										splitDate = data.split('-');
										data = splitDate[1]+'/'+splitDate[2]+'/'+splitDate[0];
										return data;
									}
									else{
										data = '';

										return data;
									}
								}
								if(column === 9){
									if(data != ''){
										splitDate = data.split('-');
										data = splitDate[1]+'/'+splitDate[2]+'/'+splitDate[0];
										return data;
									}
									else{
										data = '';

										return data;
									}
								}
								
								return data;
							}
						}
						// format: {
						// 	body: function ( data, row, column, node ) {
						// 		// if (column === 5) {
						// 		// 	data = data.replace('\n', ',');
						// 		// 	// //need to change double quotes to single
						// 		// 	// data = data.replace( /"/g, "'" );
						// 		// 	// //split at each new line
						// 		// 	// splitData = data.split('\n');
						// 		// 	// data = '';
						// 		// 	// for (i=0; i < splitData.length; i++) {
						// 		// 	// 	//add escaped double quotes around each line
						// 		// 	// 	data += '\"' + splitData[i] + '\"';
						// 		// 	// 	//if its not the last line add CHAR(13)
						// 		// 	// 	if (i + 1 < splitData.length) {
						// 		// 	// 		data += ', CHAR(13), ';
						// 		// 	// 	}
						// 		// 	// }
						// 		// 	// //Add concat function
						// 		// 	// //data = 'CONCATENATE(' + data + ')';
						// 		// 	// return data;
						// 		// 	return data;
						// 		// }
						// 		if(column === 1){
						// 			splitDate = data.split('-');
						// 			data = splitDate[1]+'/'+splitDate[2]+'/'+splitDate[0];
						// 			data = 'dasdad';
						// 			return data;
						// 		}
						// 		return data;
						// 	}
						// }
						
					}
				},
				{ text: 'Upload CSV', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                    showBulkUploadNewForm();
                }},
            ],
            //lengthMenu: [[10,50,100], [10,50,100]],
            pageLength: 50,
            searching: true,
            ajax: {
                url: "/admin/divisionthree/task/data",
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
                { data: 'id', name: 'id', title: 'ID', visible: true, class:'not_exportable'},
                { data: 'name', name: 'name', title: 'TASK'},
                { data: 'patient_name', name: 'patient_name', title: 'PATIENT NAME' },
                { data: 'patient_birthdate', name: 'patient_birthdate', title: 'PATIENT BIRTHDATE' },

                { data: 'medications', name: 'medications', title: 'MEDICATIONS', class:'not_exportable', 
					render: function (data, type) {
						return type === 'display' && JSON.stringify(data).length > 1000 ?
							'<span id="med_outer" title="' + data + '">' + data.substr(0, 17) + '</span><span id="med_show">...</span>' :
							data;
					}
					// splitDate = data.split('-');
					// data = splitDate[1]+'/'+splitDate[2]+'/'+splitDate[0];
					// data = 'dasdad';
					// return data;
				},
                { data: 'medications2', name: 'medications2', title: 'MEDICATIONS', class:'export_med', visible: false, orrderable:false, searchable:false},
                { data: 'completed_date', name: 'completed_date', title: 'COMPLETED DATE' },
                { data: 'date_of_interaction', name: 'date_of_interaction', title: 'DATE OF INTERACTION' },
                { data: 'date_of_initiation', name: 'date_of_initiation', title: 'DATE OF INITIATION' },
				{ data: 'side_effects', name: 'side_effects', title: 'SIDE EFFECTS' },
				{ data: 'date_side_effects', name: 'date_side_effects', title: 'DATE OF SIDE EFFECTS' },
				{ data: 'date_follow_up', name: 'date_follow_up', title: 'DATE FOLLOW UP' },
				{ data: 'recommended_vitamins', name: 'recommended_vitamins', title: 'RECOMMENDED VITAMINS' },
				{ data: 'outlier_type', name: 'outlier_type', title: 'OUTLIER TYPE' },
				{ data: 'pdc_rate', name: 'pdc_rate', title: 'PDC RATE' },

				{ data: 'comments', name: 'comments', title: 'COMMENTS' },
                { data: 'actions', name: 'actions', title: 'Action' , orderable: false, searchable: false, class:"not_exportable"},
            ],
			initComplete: function( settings, json ) {
                selected_len = task_table.page.len()
				// console.log(task_table.page.len());
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
        });

        table_task = task_table;
		
        // Placement controls for Table filters and buttons
		task_table.buttons().container().appendTo( '.dt-card-header' );
		$('#search_input').val(task_table.search());
		$('#search_input').keyup(function(){ task_table.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { task_table.page.len($(this).val()).draw() });

    });

	//datatable show more, less
	$('#med_show').click(function() {
		var text = $('#med_outer').attr('title');
		//console.log('text', text.length);
		$(this).text(text);
		$('#med_show').after('<a id="med_less" onclick="showLess()" href="#"> Show less</a>');
		$('#med_outer').text('');
	});
	function showLess() {
		//console.log('test');
		$('#med_less').remove();
		var txt = $('#med_outer').attr('data-shrink');
		$('#med_show').text('');
		$('#med_outer').text(txt);
		$('#med_show').text('...');
	}

	$("textarea").keyup(function(e) {
		$(this).height(2);
		// while($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))) {
		// 	$(this).height($(this).height()+1);
		// };
		$(this).height(($(this).val().split("\n").length)*25);
	});

	$('#add_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
		$('.div_medications').hide();
		$('.div_completed_date').hide();
		$('.div_date_of_interaction').hide();
		$('.div_date_of_initiation').hide();
		$('.div_side_effects').hide();
		$('.div_date_side_effects').hide();
		$('.div_date_follow_up').hide();
		$('.div_recommended_vitamins').hide();
		$('.div_pdc_rate').hide();
		$('.div_outlier_type').hide();
		$('medications').attr('rows', 1);
		$('.datepicker').datepicker("refresh");
		
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
    });

	$('#edit_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
		$('.div_medications').hide();
		$('.div_completed_date').hide();
		$('.div_date_of_interaction').hide();
		$('.div_date_of_initiation').hide();
		$('.div_side_effects').hide();
		$('.div_date_side_effects').hide();
		$('.div_date_follow_up').hide();
		$('.div_recommended_vitamins').hide();
		$('.div_pdc_rate').hide();
		$('.div_outlier_type').hide();
		$('medications').attr('rows', 1);
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
    });

	$('#view_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
		$('#view_modal ul').empty()
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
    });

	$('#bulkUpload_modal').on('hidden.bs.modal', function(){
        $('.error_txt').remove();
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();    
    });

	$('#fileDropArea').on('click', function (e) {
		e.preventDefault();
		$('#file').trigger('click');//open file selection
		e.stopPropagation();
	});

	function showBulkUploadNewForm(){
		$("#file").hide();
		$('#droparea_text').text('');
		$('.file_title').remove();
		$('#bulkUpload_modal').modal('show');
		$('#droparea_text').append('<i style="font-size: 50px;" class="file_title fw-bold lead bx bx-cloud-upload"></i><p class="file_title">DROP FILE OR CLICK TO UPLOAD CSV</p>');
        $('#bulkUpload_modal #modal_title').text('CSV UPLOAD FORM');

		
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

	function showAddNewForm(){
		$('.div_medications').hide();
		$('.div_completed_date').hide();
		$('.div_date_of_interaction').hide();
		$('.div_date_of_initiation').hide();
		$('.div_side_effects').hide();
		$('.div_date_side_effects').hide();
		$('.div_date_follow_up').hide();
		$('.div_recommended_vitamins').hide();
		$('.div_pdc_rate').hide();
		$('.div_outlier_type').hide();

		$('#task_name').on('change', function(){
			
			switch ($(this).val()) {
				case "Post Antibiotics Follow-Up":
					$('.div_medications').show();
					$('.div_completed_date').show();
					$('.div_date_of_interaction').show();
					$('.div_date_of_initiation').hide();
					$('.div_side_effects').hide();
					$('.div_date_side_effects').hide();
					$('.div_date_follow_up').hide();
					$('.div_recommended_vitamins').hide();
					$('.div_pdc_rate').hide();
					$('.div_outlier_type').hide();
					break;
				case "New Medication Initiation":
					$('.div_medications').hide();
					$('.div_completed_date').hide();
					$('.div_date_of_interaction').hide();
					$('.div_date_of_initiation').show();
					$('.div_side_effects').hide();
					$('.div_date_side_effects').hide();
					$('.div_date_follow_up').hide();
					$('.div_recommended_vitamins').hide();
					$('.div_pdc_rate').hide();
					$('.div_outlier_type').hide();
					break;
				case "Side Effects Monitoring":
					$('.div_medications').hide();
					$('.div_completed_date').hide();
					$('.div_date_of_interaction').hide();
					$('.div_date_of_initiation').hide();
					$('.div_side_effects').show();
					$('.div_date_side_effects').show();
					$('.div_date_follow_up').show();
					$('.div_recommended_vitamins').hide();
					$('.div_pdc_rate').hide();
					$('.div_outlier_type').hide();
					break;
				case "Vitamin Deficiency Mangement":
					$('.div_medications').hide();
					$('.div_completed_date').hide();
					$('.div_date_of_interaction').show();
					$('.div_date_of_initiation').hide();
					$('.div_side_effects').hide();
					$('.div_date_side_effects').hide();
					$('.div_date_follow_up').hide();
					$('.div_recommended_vitamins').show();
					$('.div_pdc_rate').hide();
					$('.div_outlier_type').hide();
					break;
				case "Adherence Promotion":
					$('.div_medications').show();
					$('.div_completed_date').hide();
					$('.div_date_of_interaction').hide();
					$('.div_date_of_initiation').show();
					$('.div_side_effects').hide();
					$('.div_date_side_effects').hide();
					$('.div_date_follow_up').hide();
					$('.div_recommended_vitamins').hide();
					$('.div_pdc_rate').show();
					$('.div_outlier_type').show();
					break;
			
				default:
					break;
			}
		});

        $('#add_modal').modal('show');
        $('#modal_title').text('TASK FORM');
		$('#medications').attr('rows', 1);
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5', 
			modal: true,
			//beforeShowDay: today,
			autoclose: true,
   			orientation: "right",
        });

		$( '#task_name' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#add_modal'),
		});

		$( '#outlier_type' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#add_modal'),
		});

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/admin/divisionthree/task/get_dropdown_data",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
                var len = data.task.length;
                $("#task_name").empty();
				$("#outlier_type").empty();

				var len2 = data.outlier.length;
                $("#task_name").append("<option value=''></option>");
				$("#outlier_type").append("<option value=''></option>");
                
                for( var b = 0; b<len; b++){
                    var name = data.task[b];
                    $("#task_name").append("<option value='"+name+"'>"+name+"</option>");
                }
				for( var b2 = 0; b2<len2; b2++){
                    var name2 = data.outlier[b2];
                    $("#outlier_type").append("<option value='"+name2+"'>"+name2+"</option>");
                }
            },
			error: function (msg) {
				handleErrorResponse(msg);
			}
        });
    }

	function showEditForm(data){
		$('.div_medications').hide();
		$('.div_completed_date').hide();
		$('.div_date_of_interaction').hide();
		$('.div_date_of_initiation').hide();
		$('.div_side_effects').hide();
		$('.div_date_side_effects').hide();
		$('.div_date_follow_up').hide();
		$('.div_recommended_vitamins').hide();
		$('.div_pdc_rate').hide();
		$('.div_outlier_type').hide();
		
        $('#edit_modal').modal('show');
		$('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5', 
			modal: true,
			autoclose: true,
   			orientation: "right",
        });

		$( '#edit_modal #outlier_type' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#edit_modal'),
		});
        
        let name = $(data).data('name');
        let id = $(data).data('id');
        let patientName = $(data).data('patientname');
        let patientBirthdate = $(data).data('patientbirthdate');
        let medications = $(data).data('medications');
        let completedDate = $(data).data('completeddate');
        let dateOfInteraction = $(data).data('dateofinteraction');
        let dateOfInitiation = $(data).data('dateofinitiation');
        let sideEffects = $(data).data('sideeffects');
        let dateSideEffects = $(data).data('datesideeffects');
        let dateFollowUp = $(data).data('datefollowup');
        let recommendedVitamins = $(data).data('recommendedvitamins');
        let pdcRate = $(data).data('pdcrate');
		let outlierType = $(data).data('outliertype');
		let comments = $(data).data('comments');

		switch (name) {
			case "Post Antibiotics Follow-Up":
				$('.div_medications').show();
				$('.div_completed_date').show();
				$('.div_date_of_interaction').show();
				$('.div_date_of_initiation').hide();
				$('.div_side_effects').hide();
				$('.div_date_side_effects').hide();
				$('.div_date_follow_up').hide();
				$('.div_recommended_vitamins').hide();
				$('.div_pdc_rate').hide();
				$('.div_outlier_type').hide();
				break;
			case "New Medication Initiation":
				$('.div_medications').hide();
				$('.div_completed_date').hide();
				$('.div_date_of_interaction').hide();
				$('.div_date_of_initiation').show();
				$('.div_side_effects').hide();
				$('.div_date_side_effects').hide();
				$('.div_date_follow_up').hide();
				$('.div_recommended_vitamins').hide();
				$('.div_pdc_rate').hide();
				$('.div_outlier_type').hide();
				break;
			case "Side Effects Monitoring":
				$('.div_medications').hide();
				$('.div_completed_date').hide();
				$('.div_date_of_interaction').hide();
				$('.div_date_of_initiation').hide();
				$('.div_side_effects').show();
				$('.div_date_side_effects').show();
				$('.div_date_follow_up').show();
				$('.div_recommended_vitamins').hide();
				$('.div_pdc_rate').hide();
				$('.div_outlier_type').hide();
				break;
			case "Vitamin Deficiency Mangement":
				$('.div_medications').hide();
				$('.div_completed_date').hide();
				$('.div_date_of_interaction').show();
				$('.div_date_of_initiation').hide();
				$('.div_side_effects').hide();
				$('.div_date_side_effects').hide();
				$('.div_date_follow_up').hide();
				$('.div_recommended_vitamins').show();
				$('.div_pdc_rate').hide();
				$('.div_outlier_type').hide();
				break;
			case "Adherence Promotion":
				$('.div_medications').show();
				$('.div_completed_date').hide();
				$('.div_date_of_interaction').hide();
				$('.div_date_of_initiation').show();
				$('.div_side_effects').hide();
				$('.div_date_side_effects').hide();
				$('.div_date_follow_up').hide();
				$('.div_recommended_vitamins').hide();
				$('.div_pdc_rate').show();
				$('.div_outlier_type').show();
				break;
		
			default:
				break;
		}
        
        $('#edit_modal #modal_title').html('Edit Form');
        $("#edit_modal #id").val(id);
        $("#edit_modal #task_name").val(name);
        $("#edit_modal #patient_name").val(patientName);
        $("#edit_modal #medications").val(medications);
        $("#edit_modal #side_effects").val(sideEffects);
        $("#edit_modal #recommended_vitamins").val(recommendedVitamins);
		$("#edit_modal #pdc_rate").val(pdcRate);
		$("#edit_modal #comments").val(comments);
		$('#edit_modal #patient_birthdate').val(patientBirthdate);
        $('#edit_modal #completed_date').val(completedDate);
		$('#edit_modal #patient_birthdate').val(patientBirthdate);
		$('#edit_modal #date_of_interaction').val(dateOfInteraction);
		$('#edit_modal #date_of_initiation').val(dateOfInitiation);
        $('#edit_modal #date_side_effects').val(dateSideEffects);
		$('#edit_modal #date_follow_up').val(dateFollowUp);

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/admin/divisionthree/task/get_dropdown_data",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                
                var len = data.task.length;
				$("#edit_modal #outlier_type").empty();

				var len2 = data.outlier.length;
				$("#edit_modal #outlier_type").append("<option value=''></option>");
                
				for( var b2 = 0; b2<len2; b2++){
                    var name2 = data.outlier[b2];
					if(name2 == outlierType){$("#edit_modal #outlier_type").append("<option selected value='"+name2+"'>"+name2+"</option>");}
                    else{$("#edit_modal #outlier_type").append("<option value='"+name2+"'>"+name2+"</option>");}
                }
            },
			error: function (msg) {
				handleErrorResponse(msg);
			}
        });
		
		$("#edit_modal #medications").height(($("#edit_modal #medications").val().split("\n").length)*25);
		
    }

	function showViewForm(data){
		$('.div_medications').hide();
		$('.div_completed_date').hide();
		$('.div_date_of_interaction').hide();
		$('.div_date_of_initiation').hide();
		$('.div_side_effects').hide();
		$('.div_date_side_effects').hide();
		$('.div_date_follow_up').hide();
		$('.div_recommended_vitamins').hide();
		$('.div_pdc_rate').hide();
		$('.div_outlier_type').hide();
		
        $('#view_modal').modal('show');
		$('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            uiLibrary: 'bootstrap5', 
			modal: true,
			autoclose: true,
   			orientation: "right",
        });

		$( '#edit_modal #outlier_type' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $('#edit_modal'),
		});
        
        let name = $(data).data('name');
        let id = $(data).data('id');
        let patientName = $(data).data('patientname');
        let patientBirthdate = $(data).data('patientbirthdate');
        let medications = $(data).data('medications');
        let completedDate = $(data).data('completeddate');
        let dateOfInteraction = $(data).data('dateofinteraction');
        let dateOfInitiation = $(data).data('dateofinitiation');
        let sideEffects = $(data).data('sideeffects');
        let dateSideEffects = $(data).data('datesideeffects');
        let dateFollowUp = $(data).data('datefollowup');
        let recommendedVitamins = $(data).data('recommendedvitamins');
        let pdcRate = $(data).data('pdcrate');
		let outlierType = $(data).data('outliertype');
		let comments = $(data).data('comments');

		switch (name) {
			case "Post Antibiotics Follow-Up":
				$('.div_medications').show();
				$('.div_completed_date').show();
				$('.div_date_of_interaction').show();
				$('.div_date_of_initiation').hide();
				$('.div_side_effects').hide();
				$('.div_date_side_effects').hide();
				$('.div_date_follow_up').hide();
				$('.div_recommended_vitamins').hide();
				$('.div_pdc_rate').hide();
				$('.div_outlier_type').hide();
				break;
			case "New Medication Initiation":
				$('.div_medications').hide();
				$('.div_completed_date').hide();
				$('.div_date_of_interaction').hide();
				$('.div_date_of_initiation').show();
				$('.div_side_effects').hide();
				$('.div_date_side_effects').hide();
				$('.div_date_follow_up').hide();
				$('.div_recommended_vitamins').hide();
				$('.div_pdc_rate').hide();
				$('.div_outlier_type').hide();
				break;
			case "Side Effects Monitoring":
				$('.div_medications').hide();
				$('.div_completed_date').hide();
				$('.div_date_of_interaction').hide();
				$('.div_date_of_initiation').hide();
				$('.div_side_effects').show();
				$('.div_date_side_effects').show();
				$('.div_date_follow_up').show();
				$('.div_recommended_vitamins').hide();
				$('.div_pdc_rate').hide();
				$('.div_outlier_type').hide();
				break;
			case "Vitamin Deficiency Mangement":
				$('.div_medications').hide();
				$('.div_completed_date').hide();
				$('.div_date_of_interaction').show();
				$('.div_date_of_initiation').hide();
				$('.div_side_effects').hide();
				$('.div_date_side_effects').hide();
				$('.div_date_follow_up').hide();
				$('.div_recommended_vitamins').show();
				$('.div_pdc_rate').hide();
				$('.div_outlier_type').hide();
				break;
			case "Adherence Promotion":
				$('.div_medications').show();
				$('.div_completed_date').hide();
				$('.div_date_of_interaction').hide();
				$('.div_date_of_initiation').show();
				$('.div_side_effects').hide();
				$('.div_date_side_effects').hide();
				$('.div_date_follow_up').hide();
				$('.div_recommended_vitamins').hide();
				$('.div_pdc_rate').show();
				$('.div_outlier_type').show();
				break;
		
			default:
				break;
		}
        
        $('#view_modal #modal_title').html('View Form');
        $("#view_modal #id").val(id);
        $("#view_modal #task_name").append('<li class="list-group-item">'+name+'</li>');
		$("#view_modal #task_name").append('<li class="list-group-item"></li>');
        $("#view_modal #patient_name").append('<li class="list-group-item">'+patientName+'</li>');
		$("#view_modal #patient_name").append('<li class="list-group-item"></li>');
        $("#view_modal #side_effects").append('<li class="list-group-item">'+sideEffects+'</li>');
		$("#view_modal #side_effects").append('<li class="list-group-item"></li>');
        $("#view_modal #recommended_vitamins").append('<li class="list-group-item">'+recommendedVitamins+'</li>');
		$("#view_modal #recommended_vitamins").append('<li class="list-group-item"></li>');
		$("#view_modal #pdc_rate").append('<li class="list-group-item">'+pdcRate+'</li>');
		$("#view_modal #pdc_rate").append('<li class="list-group-item"></li>');
		$("#view_modal #comments").append('<li class="list-group-item">'+comments+'</li>');
		$("#view_modal #comments").append('<li class="list-group-item"></li>');
		$('#view_modal #patient_birthdate').append('<li class="list-group-item">'+patientBirthdate+'</li>');
		$("#view_modal #patient_birthdate").append('<li class="list-group-item"></li>');
        $('#view_modal #completed_date').append('<li class="list-group-item">'+completedDate+'</li>');
		$("#view_modal #completed_date").append('<li class="list-group-item"></li>');
		$('#view_modal #date_of_interaction').append('<li class="list-group-item">'+dateOfInteraction+'</li>');
		$("#view_modal #date_of_interaction").append('<li class="list-group-item"></li>');
		$('#view_modal #date_of_initiation').append('<li class="list-group-item">'+dateOfInitiation+'</li>');
		$("#view_modal #date_of_initiation").append('<li class="list-group-item"></li>');
        $('#view_modal #date_side_effects').append('<li class="list-group-item">'+dateSideEffects+'</li>');
		$("#view_modal #date_side_effects").append('<li class="list-group-item"></li>');
		$('#view_modal #date_follow_up').append('<li class="list-group-item">'+dateFollowUp+'</li>');
		$("#view_modal #date_follow_up").append('<li class="list-group-item"></li>');
		$('#view_modal #outlier_type').append('<li class="list-group-item">'+outlierType+'</li>');
		$("#view_modal #outlier_type").append('<li class="list-group-item"></li>');

		let medication_array = medications.split("\n");
		medication_array.forEach(function(item) {
			$("#view_modal #medications").append('<li class="list-group-item">'+item+'</li>');
		});
		$("#view_modal #medications").append('<li class="list-group-item"></li>');
    }
</script> 
@stop
