@extends('layouts.master')

@section('content')

 <!-- PAGE-HEADER -->
      <div class="page-header">
        <div>
          <h1 class="page-title">Prescriptions</h1>
        </div>
      </div>
      <!-- PAGE-HEADER END -->

     
     <!-- EOF ERX ORDER -->
        <div class="row">
            <div class="col">
                  <label>Submitted From: <input type="text" id="min" class="datepicker form-control"></label>
                 <label>To: <input type="text" id="max" class="datepicker form-control"></label>
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
                <select id="statusFilter" class="form-select form-select-sm select2">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

  
        
      <div class="p-5 bg-light rounded-3" >
   
            <table id="orx_table" class="table text-center table-light" style="width:100%">
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

                    const filters = $('#stageFilter, #statusFilter, #min, #max');

                    const orx_tbl = $('#orx_table').DataTable({
                        scrollX: true,
                        serverSide: true,
                        pageLength: 50,
                        dom: 'fBltip',
                        buttons: [
                            { extend: 'csv', className: 'btn btn-info', text:'Export to CSV' },
                        ],
                        lengthMenu: [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
                        ajax: {
                            url: "/admin/prescriptions/data",
                            type: "GET",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: function(d) {
                                d.minDate = $('#min').val();
                                d.maxDate = $('#max').val();
                                d.stage = $('#stageFilter').val();
                                d.status = $('#statusFilter').val();
                            },
                            error: function (msg) {
                                handleErrorResponse(msg);
                            }
                        },
                        columns: [
                            
                            { data: 'order_number', name: 'order_number', title: 'Order No.' , className: 'order_number'} ,
                            { data: 'pdf', name: 'pdf', title: 'PDF' , className: 'pdf'} ,  
                            { data: 'patient_name', name: 'patient_name', title: 'Patient', width: '400',  orderable: true},
                            { data: 'medications', name: 'medications', title: 'Medications' },
                            { data: 'stage', name: 'stage', title: 'Stage' },
                            { data: 'status', name: 'status', title: 'Status' },
                           
                            { data: 'days_supply', name: 'days_supply', title: 'Days Supply' },
                            { data: 'refills_requested', name: 'refills_requested', title: 'Refills Requested' },
                            { data: 'sig', name: 'sig', title: 'SIG' },
                            { data: 'npi', name: 'npi', title: 'NPI' },
                            { data: 'request_type', name: 'request_type', title: 'Request Type' },
                           
                            { data: 'prescriber_name', name: 'prescriber_name', title: 'Prescriber Name' },
                            { data: 'prescriber_phone', name: 'prescriber_phone', title: 'Prescriber Phone' },
                            { data: 'prescriber_fax', name: 'prescriber_fax', title: 'Prescriber Fax' },
                            { data: 'special_instructions', name: 'special_instructions', title: 'Special Instructions' },
                            { data: 'submitted_at', name: 'submitted_at', title: 'Submitted At' }, 
                           
                            { data: 'sent_at', name: 'sent_at', title: 'Sent At' },
                            { data: 'received_at', name: 'received_at', title: 'Received At' },
                            { data: 'submitted_by', name: 'submitted_by', title: 'Submitted By' },
                           
                        ],
                        createdRow: function (row, data) {
                            $(row).attr('id', 'row-' + data.id);
                        },
                    });

                    filters.change(function () {
                        orx_tbl.draw();
                    });
                });
        </script>
@stop
@include('cs/modals/edit-prescription-form')
@include('cs/modals/delete-prescription-confirmation')