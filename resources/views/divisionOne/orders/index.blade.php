@extends('layouts.master')

@section('content')

 <!-- PAGE-HEADER -->
      <div class="page-header">
        <div>
          <h1 class="page-title">Orders</h1>
        </div>
      </div>
      <!-- PAGE-HEADER END -->

     
     <!-- EOF ERX ORDER -->
 <div class="row">
            <div class="col">
                  <label> From: <input type="text" id="min" class="datepicker form-control"></label>
                 <label>To: <input type="text" id="max" class="datepicker form-control"></label>
            </div>
            
  </div>
   <div class="mb-3 row">
          
            <div class="col">
                <select id="statusFilter" class="form-select form-select-sm select2">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                

                <select id="stageFilter" class="form-select form-select-sm select2">
                    <option value="">All Stages</option>
                    @foreach($stages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                    @endforeach
                </select>
            </div>
             <div class="col">
                

                <select id="shipmentStatusFilter" class="form-select form-select-sm select2">
                    <option value="">All Shipment Status</option>
                    @foreach($shipment_statuses as $shipment_status)
                        <option value="{{ $shipment_status->id }}">{{ $shipment_status->name }}</option>
                    @endforeach
                </select>
            </div>
  </div>
      <div class="p-5 bg-light rounded-3" >
     
            <table id="orders_table" class="table text-center table-light" style="width:100%">
                <thead></thead>
                <tbody>
                    
                </tbody>
            </table>
    </div>

    

@stop

@section('pages_specific_scripts')
            <script>

        
             
$(document).ready(function() {
                    const datepicker = $(".datepicker").datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: 'yy-mm-dd'  
                    });

                    const filters = $('#stageFilter, #statusFilter, #min, #max,#shipmentStatusFilter');

                    const orders_table = $('#orders_table').DataTable({
                        scrollX: true,
                        serverSide: true,
                        pageLength: 25,
                        dom: 'fBltip',
                         buttons: [
                            { extend: 'csv', className: 'btn btn-info', text:'Export to CSV' },
                            { text: 'Add new Order', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                                ShowAddOrderForm();
                            }},
                        ],
                        lengthMenu: [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
                        ajax: {
                            url: "/admin/orders/data",
                            type: "GET",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: function(d) {
                              d.minDate = $('#min').val();
                              d.maxDate = $('#max').val();
                              d.stage = $('#stageFilter').val();
                              d.status = $('#statusFilter').val();
                              d.shipment_status = $('#shipmentStatusFilter').val();
                              @if(isset($div3))
                                d.view = '340B';
                              @endif
                            },
                            error: function (msg) {
                                handleErrorResponse(msg);
                            }
                        },
                        columns: [
                            { 
                              data: 'order_number', 
                              name: 'order_number', 
                              title: 'Order No.',
                              createdCell: function (td, cellData, rowData, row, col) {
                                $(td).addClass('editable').attr('data-column', 'order_number')
                                .attr('data-order_id', rowData.order_id);
                              }
                            },
                            { data: 'patient_name', name: 'patient_name', title: 'Patient'},
                            { 
                              data: 'name', 
                              name: 'name', 
                              title: 'Medication',
                              createdCell: function (td, cellData, rowData, row, col) {
                                $(td).addClass('editable').attr('data-column', 'name');
                              }
                            },
                            { 
                              data: 'sig', 
                              name: 'sig', 
                              title: 'SIG',
                              createdCell: function (td, cellData, rowData, row, col) {
                                $(td).addClass('editable').attr('data-column', 'sig');
                              }
                            }, 
                            { 
                              data: 'days_supply', 
                              name: 'days_supply', 
                              title: 'Days Supply',
                              createdCell: function (td, cellData, rowData, row, col) {
                                $(td).addClass('editable').attr('data-column', 'days_supply');
                              }
                            },
                            { 
                              data: 'refills_remaining', 
                              name: 'refills_remaining', 
                              title: 'Refills left',
                              createdCell: function (td, cellData, rowData, row, col) {
                                $(td).addClass('editable').attr('data-column', 'refills_remaining');
                              }
                            },
                            { 
                              data: 'ndc', 
                              name: 'ndc', 
                              title: 'NDC',
                              createdCell: function (td, cellData, rowData, row, col) {
                                $(td).addClass('editable').attr('data-column', 'ndc');
                              }
                            },
                            { 
                              data: 'inventory_type', 
                              name: 'inventory_type', 
                              title: 'Inventory Type',
                              createdCell: function (td, cellData, rowData, row, col) {
                                $(td).addClass('editable').attr('data-column', 'inventory_type');
                              }
                            },
                            { 
                              data: 'rxStatus', 
                              name: 'rxStatus', 
                              title: 'RX Status',
                              createdCell: function (td, cellData, rowData, row, col) {
                                $(td).attr('onclick', `changeToSelect(this, 'rxStatus', ${rowData.id}, ${rowData.rxStatus.id})`).attr('data-status-id', rowData.rxStatus.id);
                              }
                            },
                            { 
                              data: 'rxStage', 
                              name: 'rxStage', 
                              title: 'RX Stage',
                              createdCell: function (td, cellData, rowData, row, col) {
                                $(td).attr('onclick', `changeToSelect(this, 'rxStage', ${rowData.id}, ${rowData.rxStage.id})`).attr('data-stage-id', rowData.rxStage.id);
                              }
                            },
                            { 
                              data: 'shipment_status', 
                              name: 'shipment_status', 
                              title: 'Shipment Status',
                              createdCell: function (td, cellData, rowData, row, col) {
                                $(td).attr('onclick', `changeToSelect(this, 'shipmentStatus', ${rowData.order_id}, ${rowData.shipment_status_id})`).attr('data-stage-id', rowData.shipment_status_id);
                              }
                            },
                            { 
                              data: 'tracking_number', 
                              name: 'tracking_number', 
                              title: 'Tracking No.',
                              createdCell: function (td, cellData, rowData, row, col) {
                                $(td).addClass('editable').attr('data-column', 'shipment_tracking_number')
                                .attr('data-order_id', rowData.order_id);
                              }
                            },
                            { data: 'created_at', name: 'created_at', title: 'Date Created' },
                            { data: 'updated_at', name: 'updated_at', title: 'Last Update' },
                            { data: 'rxImage', name: 'rxImage', title: 'RX Image' , orderable: false},
                            { data: 'intakeForm', name: 'intakeForm', title: 'In-take Form' , orderable: false},
                            { data: 'actions', name: 'actions', title: 'Action' , orderable: false},
                        ],
                        createdRow: function (row, data) {
                            $(row).attr('id', 'row-' + data.id).attr('data-id', data.id);
                        },
                    });

                    filters.change(function () {
                        orders_table.draw();
                    });
                });



                $(document).ready(function() {

        $("#patient_name").on("keyup", function() {
               
                // Perform an AJAX request
                      var data = {};
                      data.patient_name = $(this).val();
                       if(data.patient_name != ""){
                            $.ajax({
                                 
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                type: "POST",
                                url: "/admin/patients/search",
                                data: JSON.stringify(data),
                                contentType: "application/json; charset=utf-8",
                                dataType: "json",
                                success: function(msg) {
                                    if(msg.errors){
                                      console.log(msg);
                                    }else{
                                        //success
                                        $("#search_results ul").empty();
                                        const patients =  msg.patients;
                                        
                                        patients.forEach(function(patient) {
                                          console.log(patient.firstname);
                                              // Create a new <li> element with the customer's name
                                              const newLi = $("<li class='list-group-item'  >" + patient.firstname + " " + patient.lastname +"</li>");

                                               newLi.on("click", function() {
                                                  // Get the value (patient's name) of the clicked <li>
                                                  const patientName = $(this).text();

                                                  // Copy the customer's name to the name input box
                                                  $("#patient_name").val(patientName);
                                                  $("#patient_id").val(patient.id);
                                                  $("#search_results ul").empty();


                                              });
                  

                                              // Append the new <li> to the <ul> inside #search_results
                                              $("#search_results ul").append(newLi);
                                          });
                                    }
                                },error: function(msg) {
                                  handleErrorResponse(msg);
                                    console.log("Error");
                                    console.log(msg.responseText);
                                }


                            });
                    }else{
                       $("#search_results ul").empty();
                    }
            });
    
    
     });

            $(document).ready(function() {
                $(document).on('click', 'td.editable', function() {
                  // Check if the td already contains an input element
                  if ($(this).find('input').length > 0) {
                    return;
                  }

                  var originalText = $(this).text();
                  var column = $(this).attr('data-column'); //  each td has a data-column attribute
                  var orderID = $(this).attr('data-order_id'); 
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

                    if (column === "shipment_tracking_number" || column === "order_number") {
                      updateItem(orderID, column, userEnteredText);
                    } else {
                      updateItem(itemId, column, userEnteredText);
                    }
                  });
                });
            });           
          


          function updateItem(itemId, column, value) {
            let url;
            switch (column) {
                case 'shipment_tracking_number':
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
                    console.log('Update successful');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                  handleErrorResponse(errorThrown);
                    console.log('Update failed: ' + errorThrown);
                }
            });
        }


        var rxStatuses = @json(\App\Models\RXStatus::all());
             var rxStages = @json(\App\Models\RXStage::all());
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
                badge.className = 'badge p-3';
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
        </script>
@stop

@include('cs/modals/add-order-form')
@include('cs/modals/delete-order-confirmation')

