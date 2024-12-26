@extends('layouts.master')

@section('content')

 <!-- PAGE-HEADER -->
      <div class="page-header">
        <div>
          <h1 class="page-title">D2 - Patients</h1>
        </div>
      </div>
      <!-- PAGE-HEADER END -->

     
     <!-- EOF ERX ORDER -->

      <div class="p-5 bg-light rounded-3" >
            <table id="div2_patients_table" class="table text-center table-light" style="width:100%">
                <thead></thead>
                <tbody>
                    
                </tbody>
            </table>
    </div>

    

@stop

@section('pages_specific_scripts')
            <script>

                $(document).ready(function() {

                    var patients_table = $('#div2_patients_table').DataTable({
                        colReorder: true,
                        scrollX: true,
                        serverSide: true,
                        pageLength: 50,
                        lengthMenu: [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
                       dom: 'fBltip',
                        buttons: [
                            { extend: 'csv', className: 'btn btn-info', text:'Export to CSV' },
                            { text: 'Add new Patient', className: 'btn btn-primary', action: function ( e, dt, node, config ) {
                                ShowAddPatientForm();
                            }},
                        ],
                        ajax: {
                            url: "/admin/patients/data?v=div2",
                            type: "GET",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function (msg) {
                                handleErrorResponse(msg);
                            }
                        },
                       
                        columns: [
                            
                            { data: 'firstname', name: 'firstname', title: 'First Name' },
                            { data: 'lastname', name: 'lastname', title: 'Last Name' },
                            { data: 'birthdate', name: 'birthdate', title: 'Birthdate' },
                          
                            { data: 'address', name: 'address', title: 'Address' },
                            { data: 'city', name: 'city', title: 'City' },
                            { data: 'state', name: 'state', title: 'State' },
                            { data: 'zip_code', name: 'zip_code', title: 'Zip Code' },
                            { data: 'phone_number', name: 'phone_number', title: 'Phone Number' },
                            { data: 'created_at_date', name: 'created_at', title: 'Date Created' },
                            { data: 'updated_at_date', name: 'updated_at', title: 'Updated At' },
                            { data: 'actions', name: 'actions', title: 'Actions', orderable: false, searchable: false },
                            
                        ],

                        createdRow: function (row, data, dataIndex) {
                           
                            $(row).attr('id', 'row-' + data.id);
                        },
                    });
                    

                });
             
                 $(document).ready(function() {
                    $( ".datepicker" ).datepicker({
                        changeMonth: true,
                        changeYear: true ,
                        dateFormat: 'yy-mm-dd'  
                    });     

                    
                                  
              });


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
        </script>
@stop

@include('cs/modals/add-patient-form')
@include('cs/modals/edit-patient-form')
@include('cs/modals/delete-patient-confirmation')
