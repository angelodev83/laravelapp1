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
					@can('menu_store.operations.mail_orders.upload')
						<input type="button" value="+ New Order" class="m-1 btn btn-primary btn-sm ms-2" onclick="showAddNewForm();" />
					@endcan
					
					<select name='length_change' id='length_change' class="table_length_change form-select">
                    </select>
                
					<input type="text" id="search_input" class="table_search_input form-control" placeholder="Search...">

					
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id="orders_table" class="table row-border table-hover" style="width:100%">
								<thead></thead>
								<tbody>                                   
								</tbody>
								
							</table>
						</div>
					</div>
				</div>
			</div>

			<!-- <td>
                                <input type="text" class="form-control" name="items[${item.id}][med_id]" id="med_id${item.med_id}" autocomplete="off" hidden>
								<input type="text" class="form-control" name="item_name[]" id="name${item.id}" value="${item.name || ''}" data-item_id="${item.id}" data-item_field="name" data-item_value="${item.name || ''}" >
								
							</td> -->


			@include('sweetalert2/script')
			@include('stores/operations/mailOrders/modal/add-order-form')
			@include('stores/operations/mailOrders/modal/edit-order-form')
			@include('stores/operations/mailOrders/modal/view-mail-order')
			@include('stores/operations/mailOrders/modal/upload-order-form')
			@include('stores/operations/mailOrders/modal/delete-order-confirmation')
			@include('stores/operations/mailOrders/modal/delete-item-confirmation')

		</div>
		<!--end page wrapper -->

@stop
@section('pages_specific_scripts')   
<script>
    let menu_store_id = {{request()->id}};
    
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
				console.log(data);
				$.each(data.order.items, function(i, item) {
					console.log(item);
					$('#edit_order_modal #medications_table').append(`
						<tr id="item_row_${item.id}">>
							
							<td>
								<select class="form-select" data-placeholder="Select medication.." name="items[${item.id}][med_id]" id="med_id${item.id}" onchange="eMirrorSelectToInput(this.id)"></select>                                                                                       
								<input type="hidden" class="form-control" name="item_name[]" id="name${item.id}"  value="${item.name || ''}" data-item_id="${item.id}" data-item_field="name" data-item_value="${item.name || ''}" >
								
								<input type="hidden" class="form-control " name="item_id[]" id="item_id${item.id}" value="${item.id || ''}" data-item_id="${item.id}" data-item_field="id" data-item_value="${item.id || ''}">
							</td>
							<td><input type="text" class="form-control " name="item_sig[]" id="sig${item.id}" value="${item.sig || ''}" data-item_id="${item.id}" data-item_field="sig" data-item_value="${item.sig || ''}"></td>
							<td><input type="text" class="form-control " name="item_days_supply[]" id="days_supply${item.id}" value="${item.days_supply || ''}" data-item_id="${item.id}" data-item_field="days_supply" data-item_value="${item.days_supply || ''}"></td>
							<td><input type="text" class="form-control " name="item_refills_left[]" id="refills_left${item.id}" value="${item.refills_remaining || ''}" data-item_id="${item.id}" data-item_field="refills_remaining" data-item_value="${item.refills_remaining || ''}"></td>
							<td><input type="text" class="form-control " name="item_ndc[]" id="ndc${item.id}" value="${item.ndc || ''}" data-item_id="${item.id}" data-item_field="ndc" data-item_value="${item.ndc || ''}"></td>
							
							<td>
								<button type="button" onclick="SaveItemRow(${item.id})" class="btn btn-primary btn-sm me-2" ><i class="fa-solid fa-save"></i></button>
								<button type="button" onclick="ShowConfirmDeleteItemForm(${item.id},'${item.name}')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
							</td>
						</tr>
					`);
					$(`#edit_order_modal #med_id${item.id}`).append("<option value='"+item.id+"' selected>"+item.name+"</option>");
					searchSelect2ApiDrug(`#edit_order_modal #med_id${item.id}`, 'edit_order_modal');
				});

				
				
                $('#edit_order_modal #patient_name').val(data.order.patient.firstname+" "+data.order.patient.lastname);
                $('#edit_order_modal #patient_id').val(data.order.patient.id);
                $('#edit_order_modal #order_id').val(data.order.id);
                $('#edit_order_modal #order_number').val(data.order.order_number);
                $('#edit_order_modal #order_number').data('item_id', data.order.id);
                $('#edit_order_modal #patient_id').data('item_id', data.order.id);
                $('#edit_order_modal #shipment_from_store').data('item_id', data.order.id);
                $('#edit_order_modal #requested_by_store').data('item_id', data.order.id);

                $('#edit_order_modal #shipment_tracking_number').val(data.order.shipment_tracking_number);
				$('#edit_order_modal #shipment_tracking_number').data('item_id', data.order.id);

				
				
				$('#edit_order_modal #shipmentStatusFilter').data('item_id', data.order.id);
				var shipmentStatusName = data.order.shipment_status.name;
				var shipmentStatusId = data.order.shipment_status.id;
				
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
					value: data.order.shipment_from_store ? data.order.shipment_from_store : '',
					text: data.order.shipment_from_store ? data.order.shipment_from_store : '',
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
					value: data.order.requested_by_store ? data.order.requested_by_store : '',
					text: data.order.requested_by_store ? data.order.requested_by_store : '',
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
		let j = $('#edit_order_modal #medications_table tr').length;
		$('#edit_order_modal #add_more_link').click(function() {
			
			$('#edit_order_modal #medications_table').append(`
				<tr>
					<td>
                        
						<select class="form-select" data-placeholder="Select medication.." name="item_med_id[]" id="med_id${j}" onchange="eMirrorSelectToInput(this.id)"></select>                                                                                       
                        <input type="hidden" class="form-control" name="items[${j}][name]" id="name${j}" autocomplete="off">
						
					</td>
					<td><input type="text" class="form-control" name="item_sig" id="sig${j}"></td>
					<td><input type="text" class="form-control" name="item_days_supply" id="days_supply${j}"></td>
					<td><input type="text" class="form-control" name="item_refills_left" id="refills_left${j}"></td>
					<td><input type="text" class="form-control" name="item_ndc" id="ndc${j}"></td>
					
					<td>
                    	<button type="button" onclick="SaveNewItemRow(this, ${j})" class="btn btn-primary btn-sm me-2" ><i class="fa-solid fa-save"></i></button>
                	</td>
				</tr>
			`);
			searchSelect2ApiDrug(`#edit_order_modal #med_id${j}`, 'edit_order_modal');
			j++;
		});


		let i = $('#add_order_modal #medications_table tr').length;
		$('#add_order_modal #med_count').val(i);
		$('#add_order_modal #add_more_link').click(function() {
			$('#add_order_modal #medications_table').append(`
			
				<tr>
					<td>
						<select class="form-select" data-placeholder="Select medication.." name="item_med_id[]" id="medi_id${i}" onchange="mirrorSelectToInput(this.id)"></select>                                                                                       
                        <input type="hidden" class="form-control" name="items[${i}][name]" id="name${i}" autocomplete="off">
						
					</td>
					<td><input type="text" class="form-control" name="item_sig" id="sig${i}"></td>
					<td><input type="text" class="form-control" name="item_days_supply" id="days_supply${i}"></td>
					<td><input type="text" class="form-control" name="item_refills_left" id="refills_left${i}"></td>
					<td><input type="text" class="form-control" name="item_ndc" id="ndc${i}"></td>

				
				</tr>
			`);
			$('#add_order_modal #med_count').val(i);
			searchSelect2ApiDrug(`#add_order_modal #medi_id${i}`, 'add_order_modal');
			i++;
		});

		for (let b = 0; b < i; b++){
            searchSelect2ApiDrug(`#add_order_modal #medi_id${b}`, 'add_order_modal');
        }

	});

	function SaveNewItemRow(button, i) {
			var $row = $(button).closest('tr');

			// Extract the data from the inputs in the row
			var itemData = {
				name: $row.find(`[id^="name"]`).val(),
				order_id: $(`#edit_order_modal #order_id`).val(),
				sig: $row.find(`#sig${i}`).val(),
				days_supply: $row.find(`#days_supply${i}`).val(),
				refills_left: $row.find(`#refills_left${i}`).val(),
				ndc: $row.find(`#ndc${i}`).val(),
				inventory_type: $row.find(`#inventory_type${i}`).val(),
				med_id: $row.find(`#med_id${i}`).val(),
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
		var itemMedId = $('#med_id' + i).val();

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
				'inventory_type': itemInventoryType,
                'med_id': itemMedId
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
                case 'patient_id':
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
			
			updateItem(itemId, column, column_value);
		
			
			
		}, 500)); 


	$(document).ready(function() {	
		
		let menu_store_id = {{request()->id}};

		mail_orders_table = $('#orders_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBtip',
			buttons: [
				
			],
            dom: 'fBtp',
            pageLength: 50,
            order: [[0, 'desc']],
            searching: true,
            ajax: {
                url: `/store/operations/${menu_store_id}/mail_orders/data`,
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
				{ data: 'order_number', name: 'order_number', title: 'Order No.', visible: true},
				{ data: 'patient', name: 'firstname', title: 'Patient', visible: true},
				{ data: 'shipment_status', name: 'shipment_status', title: 'Shipment Status', visible: true},
				{ data: 'shipment_tracking_number', name: 'shipment_tracking_number', title: 'Tracking No.', visible: true},
				{ data: 'created_at', name: 'created_at', title: 'Date Added', visible: true},
				{ data: 'shipment_from_store', name: 'shipment_from_store', title: 'Shipment From', visible: true},
				{ data: 'requested_by_store', name: 'requested_by_store', title: 'Requested by', visible: true},
				{ data: 'file', name: 'file', title: 'Shipping Label', visible: true, orderable: false, searchable: false},
				{ data: 'actions', name: 'actions', title: 'Action' , orderable: false},
			],
			initComplete: function( settings, json ) {
                selected_len = mail_orders_table.page.len();
				$('#length_change').append($('<option>', {value: 5,text: 'Show 5',selected:(selected_len == "5") ?true: false}));
				$('#length_change').append($('<option>', {value: 10,text: 'Show 10',selected:(selected_len == "10") ?true: false}));
				$('#length_change').append($('<option>', {value: 50,text: 'Show 50',selected:(selected_len == "50") ?true: false}));
				$('#length_change').append($('<option>', {value: 100,text: 'Show 100',selected:(selected_len == "100") ?true: false}));
				$('#length_change').append($('<option>', {value: 1000,text: 'Show 1000',selected:(selected_len == "1000") ?true: false}));
            }
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
			var data = { patient_name: $(this).val(), source: 'pioneer' };
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

				// $(document).on("keyup", "#add_order_modal  [id^='name']", function() {
				// 	var data = { name: $(this).val() };
				// 	var currentId = $(this).attr('id');
				// 	var searchResultsUl = $("#add_order_modal  #"+currentId+"_search_results ul");
				// 	var currentNameInput = $("#add_order_modal #" + currentId);

				// 	var currentNdcInput = $("#add_order_modal #ndc" + currentId.replace('name', ''));
				// 	var currentProductInput = $("#add_order_modal #medication_id" + currentId.replace('name', ''));


				// 	searchResultsUl.empty();


				// 	if(data.name != ""){
				// 		$.ajax({
				// 			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				// 			type: "POST",
				// 			url: "/admin/medications/suggest",
				// 			data: JSON.stringify(data),
				// 			contentType: "application/json; charset=utf-8",
				// 			dataType: "json",
				// 			success: function(msg) {
				// 				if(!msg.errors){
                //                     let med_id;
				// 					searchResultsUl.empty();

				// 					var fragment = document.createDocumentFragment();

				// 					msg.medications.forEach(function(medication) {

                //                         med_id = medication.med_id;
				// 						const newLi = $("<li class='list-group-item'>" + medication.name +"</li>")
				// 						.data('medication-ndc', medication.ndc).on("click", function() {
                //                             console.log(medication.med_id);
                //                             $("#add_order_modal #med_id" + currentId.replace('name', '')).val(medication.med_id);
                //                             $("#add_order_modal #ndc" + currentId.replace('name', '')).val(medication.ndc);
                //                             currentNameInput.val($(this).text());
                //                             searchResultsUl.empty();
                //                         });

				// 						fragment.appendChild(newLi[0]);
				// 					});
				// 					searchResultsUl.append(fragment);


				// 					searchResultsUl.on("click", "li", function() {
				// 						currentNameInput.val($(this).text());
										

				// 						currentProductInput.val($(this).data('medication-id')); //place the product id


				// 						currentNdcInput.val($(this).data('medication-ndc'));
										
				// 						searchResultsUl.empty();
				// 					});


				// 				}
				// 			},
				// 			error: function() {
				// 				console.log("Error");
				// 			}
				// 		});
				// 	} else {
				// 		searchResultsUl.empty();
				// 	}
				// });

	function mirrorSelectToInput(selectId) {
		
		let selectedIndex = $("#add_order_modal #" + selectId).prop("selectedIndex");
		let inputId = selectId.replace("medi_id", "name");
		let selectedOptionText = $("#add_order_modal #" + selectId + " option:selected").text();
		$("#add_order_modal #" + inputId).val(selectedOptionText);
	}

	function eMirrorSelectToInput(selectId) {
		
		let selectedIndex = $("#edit_order_modal #" + selectId).prop("selectedIndex");
		let inputId = selectId.replace("med_id", "name");
		let selectedOptionText = $("#edit_order_modal #" + selectId + " option:selected").text();
		$("#edit_order_modal #" + inputId).val(selectedOptionText);
	}


	function searchSelect2ApiDrug(_selector, _modal_id, _med_id = null)
    {
        $(_selector).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,
            dropdownParent: $(`#${_modal_id} .modal-content`),

            multiple: false,
            minimumInputLength: 1,
            minimumResultsForSearch: 10,
            ajax: {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: "/admin/medications/getNames",
                dataType: "json",
                type: "POST",
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        limit: 10
                    }
                    if(_med_id != null) {
                        var q = { med_id: _med_id, not: 'med_id' };
                        queryParameters = {...queryParameters, ...q}
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            return {
                                text: item.name,
                                id: item.med_id
                            }   
                        })
                    };
                }  
            },
        });
    }

	

				


	$(document).on("keyup", "#edit_order_modal [id^='name']", function() {	
		var data = { name: $(this).val() };
		var currentId = $(this).attr('id');
		var searchResultsUl = $("#edit_order_modal  #"+currentId+"_search_results ul");
		var currentNameInput = $("#edit_order_modal #" + currentId);
		var currentItemIDInput = $("#edit_order_modal #item_id" + currentId).val();
			var currentNdcInput = $("#edit_order_modal #ndc" + currentId.replace('name', ''));
			var currentMedIDInput = $("#edit_order_modal #med_id" + currentId.replace('name', ''));

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
								currentMedIDInput.val(medication.med_id);

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
