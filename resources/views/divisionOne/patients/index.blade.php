@extends('layouts.master')

@section('content')

 <!-- PAGE-HEADER -->
      <div class="page-header">
        <div>
          <h1 class="page-title">Patients</h1>
        </div>
      </div>
      <!-- PAGE-HEADER END -->

     
     <!-- EOF ERX ORDER -->

      <div class="p-5 bg-light rounded-3" >
            <table id="patients_table" class="table text-center table-light" style="width:100%">
                <thead></thead>
                <tbody>
                    
                </tbody>
            </table>
    </div>

    

@stop

@section('pages_specific_scripts')
            <script>

        

                $(document).ready(function() {

                    var patients_table = $('#patients_table').DataTable({
                        colReorder: true,
                        scrollX: true,
                        serverSide: true,
                        pageLength: 50,
                        lengthMenu: [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
                       dom: 'fBltip',
                        buttons: [
                            { extend: 'csv', className: 'btn btn-info', text:'Export to CSV' },
                           ,
                        ],
                        ajax: {
                            url: "/admin/patients/data",
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
        </script>
@stop

@include('cs/modals/add-patient-form')
@include('cs/modals/edit-patient-form')
@include('cs/modals/delete-patient-confirmation')
