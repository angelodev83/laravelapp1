@extends('layouts.master')

@section('content')
            <div id="layoutSidenav_content">
                <main>
                    <div class="px-4 container-fluid">
                        <h1 class="mt-4">{{ $patient->firstname }} {{ $patient->lastname }}</h1>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Birthdate</th>
                                        <th>Address</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Zip Code</th>
                                        <th>Phone Number</th>
                                        <th>Date Created</th>
                                        <th>Updated at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <tr>
                                            <td>{{ $patient->firstname }}</td>
                                            <td>{{ $patient->lastname }}</td>
                                            <td>{{ date('M d, Y', strtotime($patient->birthdate)) }}</td>
                                            <td>{{ $patient->address }}</td>
                                            <td>{{ $patient->city }}</td>
                                            <td>{{ $patient->state }}</td>
                                            <td>{{ $patient->zip_code }}</td>
                                            <td>{{ $patient->phone_number }}</td>
                                            <td>{{ date('M d, Y H:i:s', strtotime($patient->created_at)) }}</td>
                                            <td>{{ date('M d, Y H:i:s', strtotime($patient->updated_at)) }}</td>
                                        </tr>
                                  
                                </tbody>
                            </table>
                        </div>

                
                    
                        @if($patient->prescriptions->isNotEmpty())
                            <h2 class="mt-4 float-start">Prescriptions</h2>
                            
                            <input class="mt-4 btn btn-primary btn-lg float-end" type="button" onclick="ShowAddPrescriptionForm()" id="upload_btn" value="+ Prescription">
                            <div class="clearfix"></div>

                            <div class="mt-4 table-responsive">
                                    <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                        <th>#</th>
                                        <th>Order No.</th>
                                        <th>Medication</th>
                                        <th>SIG</th>
                                        <th>Days Supply</th>
                                        <th>Refills</th>
                                        <th>Stage</th>
                                        <th>Status</th>
                                        <th>NPI</th>
                                        <th>Request Type</th>
                                        <th>Prescriber Name</th>
                                        <th>Prescriber Phone</th>
                                        <th>Prescriber Fax</th>
                                    
                                    
                                        <th>Special Instructions</th>
                                        <th>Submitted At</th>
                                        <th>Sent At</th>
                                        <th>Received At</th>
                                        <th>Submitted By</th>
                                        
                                    
                                        <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($patient->prescriptions as $prescription)
                                            <tr id="row-{{ $prescription->id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $prescription->order_number }}</td>
                                                <td>{{ $prescription->medications }}</td>

                                                <td>{{ $prescription->sig }}</td>
                                                <td>{{ $prescription->days_supply }}</td>
                                                <td>{{ $prescription->refills_requested }}</td>

                                                <td>
                                                    <span class="badge bg-{{ $prescription->stage->color ?? 'secondary' }} p-2"
                                                        style="color: #000; {{ isset($prescription->stage->color) ? 'background-color: ' . $prescription->stage->color : '' }}">
                                                        {{ $prescription->stage->name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $prescription->status->color ?? 'secondary' }} p-2"
                                                        style="color: #000; {{ isset($prescription->status->color) ? 'background-color: ' . $prescription->status->color : '' }}">
                                                        {{ $prescription->status->name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>{{ $prescription->npi }}</td>
                                                <td>{{ $prescription->request_type }}</td>
                                                <td>{{ $prescription->prescriber_name }}</td>
                                                <td>{{ $prescription->prescriber_phone }}</td>
                                                <td>{{ $prescription->prescriber_fax }}</td>
                                            
                                                
                            
                                            
                                            
                                                <td>{{ $prescription->special_instructions }}</td>
                                                <td>{{ $prescription->submitted_at }}</td>
                                            
                                                <td>{{ $prescription->sent_at }}</td>
                                                <td>{{ $prescription->received_at }}</td>
                                            <td>{{ $prescription->submitted_by }}</td>
                                                <td>
                                                    
                                                        <button 
                                                                data-order_number="{{ $prescription->order_number }}"
                                                                data-medications="{{ $prescription->medications }}"
                                                                data-sig="{{ $prescription->sig }}"
                                                            
                                                                data-days_supply="{{ $prescription->days_supply }}"
                                                                data-refills_requested="{{ $prescription->refills_requested }}"
                                                                data-patient_id="{{ $prescription->patient->id }}"

                                                                data-stage="{{ $prescription->stage_id ?? '1' }}"
                                                                data-status="{{ $prescription->status_id ?? '1' }}"

                                                                data-npi="{{ $prescription->npi }}"
                                                                data-request_type="{{ $prescription->request_type }}"
                                                            
                                                                data-prescriber_name="{{ $prescription->prescriber_name }}"
                                                                data-prescriber_phone="{{ $prescription->prescriber_phone }}"
                                                                data-prescriber_fax="{{ $prescription->prescriber_fax }}"
                                                                
                                                                data-submitted_at="{{ date('m/d/Y',strtotime($prescription->submitted_at)) }} "
                                                                data-received_at="{{ date('m/d/Y',strtotime($prescription->received_at)) }} "
                                                                data-sent_at="{{ date('m/d/Y',strtotime($prescription->sent_at)) }} "

                                                                data-special_instructions="{{ $prescription->special_instructions }}"
                                                                data-submitted_by="{{ $prescription->submitted_by }}"
                                                                type="button" class="btn btn-primary btn-sm"
                                                                onclick="ShowEditPrescriptionForm({{ $prescription->id }})"
                                                                id="edit_btn_{{ $prescription->id }}"
                                                                >
                                                                <i class="fa-solid fa-pencil"></i>
                                                            </button>


                                                        <button type="button" class="btn btn-secondary btn-sm" id="confirm_delete_product_btn" onclick="ShowConfirmDeleteForm('{{ $prescription->id}}')"  ><i class="fa-solid fa-trash-can"></i></button>
                                                                    
                                                        </td>
                                        
                                                <!-- Add more columns as needed -->
                                            </tr>
                                        @endforeach

                                    </tbody>
                                    </table>
                            </div>
                        @endif

                        @if($patient->orders->isNotEmpty())
                        <h1 class="mt-4 float-start">Orders</h1>
                           
                           
                         <div class="clearfix"></div>
                         <div class="mt-4 table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>    
                                <th>Order No.</th>
                                <th>Medication</th>
                                <th>SIG</th>
                            
                                <th>Days Supply</th>
                                <th>Refills left</th>
                                <th>NDC</th>
                                <th>Inventory Type</th>
                                <th>RX Status</th>
                                <th>RX Stage</th>
                                 <th>Shipment Status</th>
                                <th>Date Created</th>
                                <th>Last Update</th>
                                <th>Action</th>            
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($patient->orders as $order)
                                    @if ($order->items->isNotEmpty())
                                        @foreach ($order->items as $item)
                                            <tr class="row-{{ $order->id }}" data-id="{{ $item->id }}">
                                                <td onclick="ViewOrder({{ $order->id }},{{ $order->order_number }})">
                                                <a href="javascript:;">{{ $order->order_number }}</a>
                                                </td>
                                                <td class='editable' data-column="name">{{ $item->name }}</td>
                                                <td class='editable' data-column="sig">{{ $item->sig }}</td>
                                                
                                                <td class='editable' data-column="days_supply">{{ $item->days_supply }}</td>
                                                <td class='editable' data-column="refills_remaining">{{ $item->refills_remaining }}</td>
                                                <td class='editable' data-column="ndc">{{ $item->ndc }}</td>
                                                <td class='editable' data-column="inventory_type">{{ $item->inventory_type }}</td>
                                               
                                                <td class="ps-3 pe-3">
                                                    <span class="p-2 badge" style="color: #000; background-color: {{ $item->rxStatus->color }}" onclick="changeToSelect(this, 'rxStatus', {{ $item->id }}, {{ $item->rxStatus->id }})" data-status-id="{{ $item->rxStatus->id }}">
                                                        {{ $item->rxStatus->name }}
                                                    </span>
                                                </td>
                                                <td class="ps-3 pe-3">
                                                    <span class="p-2 badge" style="color: #000; background-color: {{ $item->rxStage->color }}" onclick="changeToSelect(this, 'rxStage', {{ $item->id }}, {{ $item->rxStage->id }})" data-stage-id="{{ $item->rxStage->id }}">
                                                        {{ $item->rxStage->name }}
                                                    </span>
                                                </td>

                                                 <td>
                                                    <span class="p-2 badge" style="color: #000; background-color: {{ $order->shipmentStatus->color }}" onclick="changeToSelect(this, 'shipmentStatus', {{ $order->id }}, {{ $order->shipment_status_id }})" data-stage-id="{{ $order->shipment_status_id }}" >
                                                        {{ $order->shipmentStatus->name }}
                                                    </span>
                                                </td>

                                                 <td>{{ $order->created_at->format('m/d/Y h:i A') }}</td>
                                                <td>{{ $order->updated_at->format('m/d/Y h:i A') }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-secondary btn-sm" id="confirm_delete_product_btn" onclick="ShowConfirmDeleteForm('{{ $order->id}}','{{ $order->order_number}}')"  ><i class="fa-solid fa-trash-can"></i></button>
                                                       
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                            </table>
                        </div>
                         @endif

                       
                       
                    </di    v>
                </main>
                <footer class="py-4 mt-auto bg-light">
                    <div class="px-4 container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">MGMT88 Portal</div>
                            
                        </div>
                    </div>
                </footer>
            </div>

@include('cs/modals/edit-prescription-form')     
@include('cs/modals/add-prescription-form')
@include('cs/modals/delete-prescription-confirmation')


<div class="modal modal-xl" id="OrderModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="OrderModalLabel">Order#</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <form action="" method="POST" id="AddForm">
          <div class="container">
            <form action="" method="POST" id="AddForm" class="table">
                <div class="container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Birthday</th>
                                <th>Address</th>
                                <th>Phone no.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td id="patient-name"></td>
                                <td id="patient-birthday"></td>
                                <td id="patient-address"></td>
                                <td id="patient-phone"></td>
                            </tr>
                        </tbody>
                    </table>

                    <h2>Medications</h2>
                
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>SIG</th>
                               
                                <th>Days Supply</th>
                                <th>Refills Remaining</th>
                                <th>NDC</th>
                                <th>Inventory Type</th>
                                <th>RX Status</th>
                                <th>RX Stage</th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            <!-- Items will be inserted here -->

                        </tbody>
                    </table>

                </div>
            </form>
          </div>
      </form>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@stop

@section('pages_specific_scripts')
            <script>
              $(document).ready(function() {
                
                    $( ".datepicker" ).datepicker();                 
              });

            var rxStatuses = @json(\App\Models\RXStatus::all());
             var rxStages = @json(\App\Models\RXStatus::all());
             var shipmentStatuses = @json(\App\Models\ShipmentStatus::all());


               function changeToSelect(element, type, id) {
                var statusId = element.dataset.statusId; // Get the current status id from the data- attribute
                var select = document.createElement('select');
                select.className = 'form-control';
                select.dataset.statusId = statusId; // Add the data- attribute to the select
                select.onchange = function() { 
                    UpdateStatus(id, this.value, type);
                };
                select.onblur = function() {
                    changeToBadge(this, type);
                };

            
                var options;
                switch (type) {
                    case 'rxStatus':
                        options = rxStatuses;
                        break;
                    case 'rxStage':
                        options = rxStages;
                        break;
                    case 'shipmentStatus':
                        options = shipmentStatuses;
                        break;
                    default:
                        options = [];
                }
                
                options.forEach(item => {
                    var option = document.createElement('option');
                    option.value = item.id;
                    option.text = item.name;
                    if (item.id == statusId) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });

                element.parentNode.replaceChild(select, element);
            }

            function changeToBadge(element, type) {
                var selectedOption = element.options[element.selectedIndex];
                var id = selectedOption.value;
                var name = selectedOption.text;
                var color;
                switch (type) {
                    case 'rxStatus':
                        color = rxStatuses.find(status => status.id == id).color;
                        break;
                    case 'rxStage':
                        color = rxStages.find(stage => stage.id == id).color;
                        break;
                    case 'shipmentStatus':
                        color = shipmentStatuses.find(status => status.id == id).color;
                        break;
                    default:
                        color = null;
                }

                var badge = document.createElement('span');
                badge.className = 'badge p-2';
                badge.style.color = '#000';
                badge.style.backgroundColor = color;
                badge.dataset.statusId = id; // Add the data- attribute to the badge
                badge.onclick = function() {
                    changeToSelect(this, type, id);
                };
                badge.innerText = name;

                element.parentNode.replaceChild(badge, element);
            }

            function UpdateStatus(id, selectedValue, type) {
                var url = '/admin/update/' + type + '/' + id;
                var data = {
                    id: id,
                    value: selectedValue
                };

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.message === 'Update successful') {
                            // Handle the success response
                               console.log(response);
                        } else {
                            // Handle the failure response
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        handleErrorResponse(errorThrown);
                        // Handle the error
                         console.log(errorThrown);
                    }
                });
            }

              function ViewOrder(id,ordernumber) {
                // Open the modal
                $('#OrderModal').modal('show');

                // Set the order id in the modal header
                $('#OrderModal .modal-title').text('Order ID: ' + ordernumber);

                // TODO: Fetch and display the patient details
                var url = '/admin/orders/' + id;
                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Populate the modal with the patient details
                        $('#patient-name').text(response.patient.firstname + ' ' + response.patient.lastname);
                        $('#patient-birthday').text(response.patient.birthdate);
                        $('#patient-address').text(response.patient.address);
                        $('#patient-phone').text(response.patient.phone_number);

                        // Populate the items-body with the items
                        var itemsBody = $('#items-body');
                        console.log(response.items);
                        itemsBody.empty();
                        $.each(response.items, function(index, item) {

                            
                            var row = $('<tr>');
                            row.append($('<td>').text(item.name));
                            row.append($('<td>').text(item.sig));
                          
                            row.append($('<td>').text(item.days_supply));
                            row.append($('<td>').text(item.refills_remaining));
                            row.append($('<td>').text(item.ndc));
                            row.append($('<td>').text(item.inventory_type));
                            row.append($('<td>').html(item.rx_status ? '<span class="p-2 badge" style="color: #000; background-color:' + item.rx_status.color + ';" onclick="changeToSelect(this, \'rxStatus\', ' + item.id + ', ' + item.rx_status.id + ')" data-status-id="' + item.rx_status.id + '">' + item.rx_status.name + '</span>' : ''));
                            row.append($('<td>').html(item.rx_stage ? '<span class="p-2 badge" style="color: #000; background-color:' + item.rx_stage.color + ';" onclick="changeToSelect(this, \'rxStage\', ' + item.id + ', ' + item.rx_stage.id + ')" data-stage-id="' + item.rx_stage.id + '">' + item.rx_stage.name + '</span>' : ''));
                            
                            itemsBody.append(row);
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        handleErrorResponse(errorThrown);
                        // Handle the error
                         console.log(errorThrown);
                    }
                });
            }

        

             $(document).ready(function() {
                $('td.editable').on('click', function() {
                    // Check if the td already contains an input element
                    if ($(this).find('input').length > 0) {
                        return;
                    }

                    var originalText = $(this).text();
                    var column = $(this).attr('data-column'); //  each td has a data-column attribute
                    var itemId = $(this).parent().attr('data-id'); //  each row has a data-id attribute

                    $(this).html('<input type="text" class="form-control" value="' + originalText + '">');
                    var inputField = $(this).children().first();
                    inputField.focus();
                    var val = inputField.val();
                    inputField.val('');
                    inputField.val(val);

                    inputField.blur(function() {
                        var userEnteredText = $(this).val();
                        $(this).parent().text(userEnteredText);
                        updateItem(itemId, column, userEnteredText);
                    });
                });
            });


        function updateItem(itemId, column, value) {
            $.ajax({
                url: '/admin/item/update',
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
                    console.log('Update successful');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    handleErrorResponse(errorThrown);
                    console.log('Update failed: ' + errorThrown);
                }
            });
        }
        </script>
@stop
