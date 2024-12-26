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
					<select name='shipment_from_store_select' id='shipment_from_store_select' class="table_store_select form-select">
						<option value=''>Shipment from Store</option>
						@foreach($stores as $store)
							<option value="{{ $store }}">{{ $store }}</option>
						@endforeach
					</select>
					<select name='requested_by_store_select' id='requested_by_store_select' class="table_store_select form-select">
						<option value=''>Requested by Store</option>
						@foreach($stores as $store)
							<option value="{{ $store }}">{{ $store }}</option>
						@endforeach
					</select>

					<input type="button" value="Clear" class="m-1 btn btn-primary btn-sm ms-2" onclick="location.reload();" />
					<input type="button" value="+ New Order" class="m-1 btn btn-primary btn-sm ms-2" onclick="showAddNewForm();" />
					
					
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
			@include('division2b/mail_orders/modal/add-order-form')
			@include('division2b/mail_orders/modal/edit-order-form')
			@include('division2b/mail_orders/modal/view-mail-order')
			@include('division2b/mail_orders/modal/delete-order-confirmation')
			@include('division2b/mail_orders/modal/delete-item-confirmation')

		</div>
		<!--end page wrapper -->

@stop
@section('pages_specific_scripts')   
<script>
	function ViewMailOrder(order_id){
        $('#view_order_modal').modal('show');
		$('#view_order_modal #medications_table').empty(); 

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/admin/mail_orders/" + order_id,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
				success: function(data) {

					
						$('#patient').html(data.patient.firstname+" "+data.patient.lastname);
						
						$('#view_order_modal #tracking_no').html(data.shipment_tracking_number);
						$('#view_order_modal #order_no').html(data.order_number);
						$('#view_order_modal #shipping_status').html(data.shipment_status.name);
						$('#view_order_modal #shipment_from_store').html(data.shipment_from_store);
						$('#view_order_modal #requested_by_store').html(data.requested_by_store);


					$.each(data.items, function(i, item) {
						
						$('#view_order_modal #medications_table').append(
							'<tr>' +
								'<td>' + (item.name ? item.name : '') + '</td>' +
								'<td>' + (item.sig ? item.sig : '') + '</td>' +
								'<td>' + (item.days_supply ? item.days_supply : '') + '</td>' +
								'<td>' + (item.refills_remaining ? item.refills_remaining : '') + '</td>' +
								'<td>' + (item.ndc ? item.ndc : '') + '</td>' +
								'<td>' + (item.inventory_type ? item.inventory_type : '') + '</td>' +
							'</tr>'
						);
					});
				},
            error: function(msg) {
				handleErrorResponse(msg);
               // console.log(msg);
            }
        });
    }

	
	function showAddNewForm(){
        $('#add_order_modal	').modal('show');
    }
	 function ShowEditOrderForm(order_id){
        $('#edit_order_modal').modal('show');
        $('.added_options').remove();

		

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/admin/orders/" + order_id,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
				$('#edit_order_modal #medications_table').empty();
				
				$.each(data.items, function(i, item) {
					console.log(item);
					$('#edit_order_modal #medications_table').append(`
						<tr id="item_row_${item.id}">>
							<td>
								<input type="text" class="form-control" name="item_name[]" id="name${item.id}" value="${item.name || ''}" data-item_id="${item.id}" data-item_field="name" data-item_value="${item.name || ''}" >
								<input type="hidden" class="form-control " name="item_id[]" id="item_id${item.id}" value="${item.id || ''}" data-item_id="${item.id}" data-item_field="id" data-item_value="${item.id || ''}">
								<div id="name${item.id}_search_results" class="search_results">
									<ul class="list-group text-start"></ul>
								</div>
							</td>
							<td><input type="text" class="form-control " name="item_sig[]" id="sig${item.id}" value="${item.sig || ''}" data-item_id="${item.id}" data-item_field="sig" data-item_value="${item.sig || ''}"></td>
							<td><input type="text" class="form-control " name="item_days_supply[]" id="days_supply${item.id}" value="${item.days_supply || ''}" data-item_id="${item.id}" data-item_field="days_supply" data-item_value="${item.days_supply || ''}"></td>
							<td><input type="text" class="form-control " name="item_refills_left[]" id="refills_left${item.id}" value="${item.refills_remaining || ''}" data-item_id="${item.id}" data-item_field="refills_remaining" data-item_value="${item.refills_remaining || ''}"></td>
							<td><input type="text" class="form-control " name="item_ndc[]" id="ndc${item.id}" value="${item.ndc || ''}" data-item_id="${item.id}" data-item_field="ndc" data-item_value="${item.ndc || ''}"></td>
							<td>
								<select class="form-select" name="item_inventory_type[]" id="inventory_type${item.id}" data-item_id="${item.id}" data-item_field="inventory_type" data-item_value="${item.inventory_type || ''}">
									<option value="">Select</option>
									<option value="RX" ${item.inventory_type === 'RX' ? 'selected' : ''}>RX</option>
									<option value="340B" ${item.inventory_type === '340B' ? 'selected' : ''}>340B</option>
								</select>
							</td>
							<td>
								<button type="button" onclick="SaveItemRow(${item.id})" class="btn btn-primary btn-sm me-2" ><i class="fa-solid fa-save"></i></button>
								<button type="button" onclick="ShowConfirmDeleteItemForm(${item.id},'${item.name}')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
							</td>
						</tr>
					`);
				});

				
				
                $('#edit_order_modal #patient_name').val(data.patient.firstname+" "+data.patient.lastname);
                $('#edit_order_modal #patient_id').val(data.patient.id);
                $('#edit_order_modal #order_id').val(data.id);
                $('#edit_order_modal #order_number').val(data.order_number);
                $('#edit_order_modal #order_number').data('item_id', data.id);
                $('#edit_order_modal #patient_id').data('item_id', data.id);
                $('#edit_order_modal #shipment_from_store').data('item_id', data.id);
                $('#edit_order_modal #requested_by_store').data('item_id', data.id);

                $('#edit_order_modal #shipment_tracking_number').val(data.shipment_tracking_number);
				$('#edit_order_modal #shipment_tracking_number').data('item_id', data.id);

				
				
				$('#edit_order_modal #shipmentStatusFilter').data('item_id', data.id);
				var shipmentStatusName = data.shipment_status.name;
				var shipmentStatusId = data.shipment_status.id;
				
				$('#edit_order_modal #shipmentStatusFilter').prepend($('<option>', {
					value: '---',
					text: '---',
					selected: true,
					disabled: true,
					class: 'added_options'
				}));
				$('#edit_order_modal #shipmentStatusFilter').prepend($('<option>', {
					value: shipmentStatusId,
					text: shipmentStatusName,
					selected: true,
					disabled: false,
					class: 'added_options'
				}));
	

				$('#edit_order_modal #shipment_from_store').prepend($('<option>', {
					value: '---',
					text: '---',
					selected: true,
					disabled: true,
					class: 'added_options'
				}));
				$('#edit_order_modal #shipment_from_store').prepend($('<option>', {
					value: data.shipment_from_store ? data.shipment_from_store : '',
					text: data.shipment_from_store ? data.shipment_from_store : '',
					selected: true,
					class: 'added_options'
				}));	


				$('#edit_order_modal #requested_by_store').prepend($('<option>', {
					value: '---',
					text: '---',
					selected: true,
					disabled: true,
					class: 'added_options'
				}));
				$('#edit_order_modal #requested_by_store').prepend($('<option>', {
					value: data.requested_by_store ? data.requested_by_store : '',
					text: data.requested_by_store ? data.requested_by_store : '',
					selected: true,
					class: 'added_options'
				}));
            },
            error: function(msg) {
				handleErrorResponse(msg);
                console.log(msg.responseText);
            }
        });
    }



	$(document).ready(function() {
		var i = $('#edit_order_modal #medications_table tr').length;
		$('#edit_order_modal #add_more_link').click(function() {
			$('#edit_order_modal #medications_table').append(`
				<tr>
					<td>
						<input type="text" class="form-control" name="item_name[]" id="name${i}">
						<div id="name${i}_search_results" class="search_results">
							<ul class="list-group text-start"></ul>
						</div>
					</td>
					<td><input type="text" class="form-control" name="item_sig" id="sig"></td>
					<td><input type="text" class="form-control" name="item_days_supply" id="days_supply"></td>
					<td><input type="text" class="form-control" name="item_refills_left" id="refills_left"></td>
					<td><input type="text" class="form-control" name="item_ndc" id="ndc"></td>
					<td>
						<select class="form-select" name="item_inventory_type" id="inventory_type">
							<option value="">Select</option>
							<option value="RX">RX</option>
							<option value="340B">340B</option>
						</select>
					</td>
					<td>
                    <button type="button" onclick="SaveNewItemRow(this)" class="btn btn-primary btn-sm me-2" ><i class="fa-solid fa-save"></i></button>
                </td>
				</tr>
			`);
			i++;
		});


		var i = $('#add_order_modal #medications_table tr').length;
		$('#add_order_modal #add_more_link').click(function() {
			$('#add_order_modal #medications_table').append(`
				<tr>
					<td>
						<input type="text" class="form-control" name="item_name[]" id="name${i}">
						<div id="name${i}_search_results" class="search_results">
							<ul class="list-group text-start"></ul>
						</div>
					</td>
					<td><input type="text" class="form-control" name="item_sig" id="sig"></td>
					<td><input type="text" class="form-control" name="item_days_supply" id="days_supply"></td>
					<td><input type="text" class="form-control" name="item_refills_left" id="refills_left"></td>
					<td><input type="text" class="form-control" name="item_ndc" id="ndc"></td>
					<td>
						<select class="form-select" name="item_inventory_type" id="inventory_type">
							<option value="">Select</option>
							<option value="RX">RX</option>
							<option value="340B">340B</option>
						</select>
					</td>
				
				</tr>
			`);
			i++;
		});

	});

	function SaveNewItemRow(button) {
			var $row = $(button).closest('tr');

			// Extract the data from the inputs in the row
			var itemData = {
				name: $row.find('[id^="name"]').val(),
				order_id: $('#edit_order_modal #order_id').val(),
				sig: $row.find('#sig').val(),
				days_supply: $row.find('#days_supply').val(),
				refills_left: $row.find('#refills_left').val(),
				ndc: $row.find('#ndc').val(),
				inventory_type: $row.find('#inventory_type').val()
			};

			console.log(itemData);
			// Send the data to the server
			$('.alert').alert('close');
			$.ajax({
				url: '/admin/item_row/SaveNewItemRow',
				type: 'POST',
				data: itemData,
				headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function(response) {
					// Handle success
					console.log('Item saved: ' + response);
					$('#medication_label').after('<div class="alert alert-warning">Item successfully updated.</div>');
					
					setTimeout(function() {
						$('.alert').alert('close');
					}, 5000);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					handleErrorResponse(errorThrown);
					// Handle error
					console.log('Failed to save item: ' + errorThrown);
				}
			});
		}
	
	function SaveItemRow(i) {
		 $('.alert').remove(); 
		// Gather data from the row
		var itemId = $('#item_id' + i).val();
		var itemName = $('#name' + i).val();
		var itemSig = $('#sig' + i).val();
		var itemDaysSupply = $('#days_supply' + i).val();
		var itemRefillsLeft = $('#refills_left' + i).val();
		var itemNdc = $('#ndc' + i).val();
		var itemInventoryType = $('#inventory_type' + i).val();

		console.log('itemId:'+ itemId, 'itemName:'+ itemName, 'itemSig:'+ itemSig, 'itemDaysSupply:'+ itemDaysSupply, 'itemRefillsLeft:'+ itemRefillsLeft, 'itemNdc:'+ itemNdc, 'itemInventoryType:'+ itemInventoryType);

		// Send AJAX request to save data
		$.ajax({
			url: '/admin/item_row/update',
			type: 'POST',
			data: {
				'id': itemId,
				'name': itemName,
				'sig': itemSig,
				'days_supply': itemDaysSupply,
				'refills_left': itemRefillsLeft,
				'ndc': itemNdc,
				'inventory_type': itemInventoryType
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(response) {
				// Handle success
				console.log('Update successful: ' + response);
				$('#medication_label').after('<div class="alert alert-warning">Item successfully updated.</div>');
				setTimeout(function() {
					$('.alert').alert('close');


				}, 5000);
			},	
			error: function(jqXHR, textStatus, errorThrown) {
				handleErrorResponse(errorThrown);
				// Handle error
				console.log('Update failed: ' + errorThrown);
			}
		});
	 }

	 function updateItem(itemId, column, value) {
			
            let url;
            switch (column) {
                case 'requested_by_store':
                case 'shipment_from_store':
                case 'shipment_tracking_number':
                case 'shipment_status_id':
                case 'order_number':
                    url = '/admin/order/update';
                break;
               
                default:
                    url = '/admin/item/update';
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    'id': itemId,
                    'column': column,
                    'value': value
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    	//success message
						$('#orders_table').DataTable().ajax.reload();
                },
                error: function(jqXHR, textStatus, errorThrown) {
					handleErrorResponse(errorThrown);
                    console.log('Update failed: ' + errorThrown);
					$('#status_message').html('Saving failed');
                }
            });
        }


		// Debounce function
		function debounce(func, delay) {
			let debounceTimer;
			return function() {
				const context = this;
				const args = arguments;
				clearTimeout(debounceTimer);
				debounceTimer = setTimeout(() => func.apply(context, args), delay);
			}
		}

		// Usage
		$(document).on('input', '.editable', debounce(function() {
			var itemId = $(this).data('item_id');
			var column = $(this).data('item_field');
			var column_value = $(this).val();
			
			console.log(itemId, column, column_value);
			updateItem(itemId, column, column_value);
		}, 500)); 


	$(document).ready(function() {	
		
		

		mail_orders_table = $('#orders_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBtip',
			buttons: [
				
			],
            lengthMenu: [[1,110, 25, 50,100], [1,10, 25, 50,100]],
            order: ['4', 'DESC'],
            pageLength: 50,
            searching: true,
            ajax: {
                url: "/admin/divisiontwob/mail_orders/data",
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
				{ data: 'order_number', name: 'order_number', title: 'Order No.', visible: true},
				{ data: 'patient', name: 'firstname', title: 'Patient', visible: true},
				{ data: 'shipment_status', name: 'shipment_status', title: 'Shipment Status', visible: true},
				{ data: 'shipment_tracking_number', name: 'shipment_tracking_number', title: 'Tracking No.', visible: true},
				{ data: 'created_at', name: 'created_at', title: 'Date Added', visible: true},
				{ data: 'shipment_from_store', name: 'shipment_from_store', title: 'Shipment From', visible: true},
				{ data: 'requested_by_store', name: 'requested_by_store', title: 'Requested by', visible: true},
				{ data: 'actions', name: 'actions', title: 'Action' , orderable: false},
			],
        });

		// Placement controls for Table filters and buttons
		mail_orders_table.buttons().container().appendTo( '.dt-card-header' );
		$('#search_input').keyup(function(){ mail_orders_table.search($(this).val()).draw() ; })
	    $('#length_change').change( function() { mail_orders_table.page.len($(this).val()).draw() });
		
		$('#shipment_from_store_select').change( function() { mail_orders_table.column(6).search($(this).val()).draw() });
		$('#requested_by_store_select').change( function() { mail_orders_table.column(7).search($(this).val()).draw() });

    });

	          $(document).ready(function() {
						$("#add_order_modal #patient_name").on("keyup", function() {
						var data = { patient_name: $(this).val() };
						var searchResultsUl = $("#add_order_modal #search_results ul");
						var patientNameInput = $("#add_order_modal #patient_name");
						var patientIdInput = $("#add_order_modal #patient_id");

						if(data.patient_name != ""){
							$.ajax({
								headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
								type: "POST",
								url: "/admin/patients/search",
								data: JSON.stringify(data),
								contentType: "application/json; charset=utf-8",
								dataType: "json",
								success: function(msg) {
									if(!msg.errors){
										searchResultsUl.empty();
										msg.patients.forEach(function(patient) {
											const birthdate = new Date(patient.birthdate);
											const formattedBirthdate = birthdate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

											const newLi = $("<li class='list-group-item'>" + patient.firstname + " " + patient.lastname +" ("+ formattedBirthdate +")</li>").on("click", function() {
												patientNameInput.val(patient.firstname + " " + patient.lastname);
												patientIdInput.val(patient.id);
												searchResultsUl.empty();
											});
											searchResultsUl.append(newLi);
										});
									}
								},
								error: function(msg) {
									handleErrorResponse(msg);
									console.log("Error");
								}
							});
						} else {
							searchResultsUl.empty();
						}
					});
			 });

				$(document).on("keyup", "#add_order_modal  [id^='name']", function() {
					var data = { name: $(this).val() };
					var currentId = $(this).attr('id');
					var searchResultsUl = $("#add_order_modal  #"+currentId+"_search_results ul");
					var currentNameInput = $("#add_order_modal #" + currentId);
					var currentNdcInput = $("#add_order_modal #ndc" + currentId.replace('name', ''));
					searchResultsUl.empty();


					if(data.name != ""){
						$.ajax({
							headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
							type: "POST",
							url: "/admin/medications/suggest",
							data: JSON.stringify(data),
							contentType: "application/json; charset=utf-8",
							dataType: "json",
							success: function(msg) {
								if(!msg.errors){
									searchResultsUl.empty();
									var fragment = document.createDocumentFragment();
									msg.medications.forEach(function(medication) {
										const newLi = $("<li class='list-group-item'>" + medication.name +"</li>")
										.data('medication-ndc', medication.ndc);
										fragment.appendChild(newLi[0]);
									});
									searchResultsUl.append(fragment);

									searchResultsUl.on("click", "li", function() {
										currentNameInput.val($(this).text());
										console.log($(this).text());
										currentNdcInput.val($(this).data('medication-ndc'));
										searchResultsUl.empty();
									});
								}
							},
							error: function(msg) {
								handleErrorResponse(msg);
								console.log("Error");
							}
						});
					} else {
						searchResultsUl.empty();
					}
				});

				


				$(document).on("keyup", "#edit_order_modal [id^='name']", function() {	
					var data = { name: $(this).val() };
					var currentId = $(this).attr('id');
					var searchResultsUl = $("#edit_order_modal  #"+currentId+"_search_results ul");
					var currentNameInput = $("#edit_order_modal #" + currentId);
					var currentItemIDInput = $("#edit_order_modal #item_id" + currentId).val();
						var currentNdcInput = $("#edit_order_modal #ndc" + currentId.replace('name', ''));

					if(data.name != ""){
						$.ajax({
							headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
							type: "POST",
							url: "/admin/medications/suggest",
							data: JSON.stringify(data),
							contentType: "application/json; charset=utf-8",
							dataType: "json",
							success: function(msg) {
								if(!msg.errors){
									searchResultsUl.empty();
									msg.medications.forEach(function(medication) {
										const newLi = $("<li class='list-group-item'>" + medication.name +"</li>")
										.on("click", function() {
											currentNameInput.val($(this).text());
											currentNdcInput.val(medication.ndc);

											$('.search_results ul').empty();
											
										});
										searchResultsUl.append(newLi);
									});
								}
							},
							error: function(msg) {
								handleErrorResponse(msg);
								console.log("Error");
							}
						});
					} else {
						searchResultsUl.empty();
					}
				});

				$(document).ready(function() {
						$("#edit_order_modal #patient_name").on("keyup", function() {
						var data = { patient_name: $(this).val() };
						var searchResultsUl = $("#edit_order_modal #search_results ul");
						var patientNameInput = $("#edit_order_modal #patient_name");
						var patientIdInput = $("#edit_order_modal #patient_id");

						if(data.patient_name != ""){
							$.ajax({
								headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
								type: "POST",
								url: "/admin/patients/search",
								data: JSON.stringify(data),
								contentType: "application/json; charset=utf-8",
								dataType: "json",
								success: function(msg) {
									if(!msg.errors){
										searchResultsUl.empty();
										msg.patients.forEach(function(patient) {
											const newLi = $("<li class='list-group-item'>" + patient.firstname + " " + patient.lastname +"</li>")
											.on("click", function() {
												patientNameInput.val($(this).text());
												patientIdInput.val(patient.id).trigger('input');
												searchResultsUl.empty();
											});
											searchResultsUl.append(newLi);
										});
									}
								},
								error: function(msg) {
									handleErrorResponse(msg);
									console.log("Error");
								}
							});
						} else {
							searchResultsUl.empty();
						}
					});
			 });
            
            
           


	

</script>


@stop
