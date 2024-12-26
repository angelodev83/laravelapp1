@extends('layouts.master')
@include('cs/modals/edit-prescription-form')
@include('cs/modals/delete-prescription-confirmation')
@section('content')

 <!-- PAGE-HEADER -->
      <div class="page-header">
        <div>
          <h1 class="page-title">Dashboard</h1>
        </div>
      </div>
      <!-- PAGE-HEADER END -->

      <div class="row">
        <div class="col-md-8">
          <div class="overflow-hidden card">
            <div class="card-body" style="background:#A39FFC; min-height:395px;">
              <div class="row">
                <div class="my-auto col-md-8 col-sm-12 d-flex">
                  <span class="text-white">
                    <h1 class="mb-2 fw-bold">Good Day, {{$user->name}} </h1>
                    <h3 class="fw-semibold">{{$quotes[array_rand($quotes)]}}</h3>
                  </span>
                </div>
                <div class="col-md-4 col-sm-12">
                  <img src="/assets/images/tin_files/work_place.svg">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div id="calendar2"></div>
        </div>
      </div>


<div class="mt-5 row">
        <div class="col-md-4">
         <a href="your-link-here">
            <div class="overflow-hidden card">
                <div class="card-body">
                    <div class="row">
                        <div class="my-auto col-md-8 col-sm-12 d-flex justify-content-center">
                            <span class="">
                                <h3 class="mb-2 fw-bold">ERX ORDERS</h3>
                            </span>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <img src="/assets/images/tin_files/ERX.png">
                        </div>
                    </div>
                </div>
            </div>
             </a>
        </div>
         <div class="col-md-4">
         <a href="your-link-here">
            <div class="overflow-hidden card">
                <div class="card-body">
                    <div class="row">
                        <div class="my-auto col-md-8 col-sm-12 d-flex justify-content-center">
                            <span class="">
                                <h3 class="mb-2 fw-bold">FULFILLMENT ORDERS</h3>
                            </span>
                        </div>
                        <div class="col-md-4 col-sm-12">
                           <img src="/assets/images/tin_files/fulfillment.png" width="125" height="135">  
                        </div>
                    </div>
                </div>
            </div>
             </a>
        </div>
       
        <div class="col-md-4">
         <a href="your-link-here">
            <div class="overflow-hidden card">
                <div class="card-body">
                    <div class="row">
                        <div class="my-auto col-md-8 col-sm-12 d-flex justify-content-center">
                        <span class="">
                            <h3 class="mb-2 fw-bold">TELEHEALTH</h3>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <img src="/assets/images/tin_files/telehealth.png" width="125" height="135">  
                        </div>
                    </div>
                </div>
            </div>
             </a>
        </div>
</div>

    <!-- ERX ORDER -->
    <div class="p-5 my-3 bg-light rounded-3" >
        <div class="row">
           
        <div class="col-md-4">
            <div class="py-1 overflow-hidden card" style="background:#CADDFF;"> 
                <div class="m-3 card-body">
                    <div class="text-end" style="color:black!important;">
                        <h1 class="mb-2" style="font-size:80px;">{{ $totalPrescriptions }}</h1>
                        <h1>No. of Scripts</h1>
                    </div>
                    <img class="pt-5" src="/assets/images/tin_files/scripts.png">
                </div>
            </div>
        </div>

        <div class="col-md-4">

          @foreach ($stages as $stage)
                    <div class="overflow-hidden card" style="background:#CADDFF;">
                            <a href="/admin/prescriptions/stage/{{ $stage->id }}">
                        <div class="p-5 card-body d-flex justify-content-center">
                            <div class="row">
                                <div class="my-auto col-md-6 col-sm-12">
                                <span class="mx-auto" style="color:black!important;">
                                    <h4><b>{{ $stage->name }}</b></h4>
                                    <h1 style="font-size:50px;">{{ $stage->prescriptions_count }}</h1>
                                </div>
                                <div class="text-center col-md-6 col-sm-12">
                                    @switch($stage->name)
                                        @case('Pending')
                                            <img class="w-75" src="/assets/images/tin_files/24.png">
                                            @break

                                        @case('In-progress')
                                            <img class="w-75" src="/assets/images/tin_files/23.png">
                                            @break

                                        @case('Uploaded')
                                            <img class="w-75" src="/assets/images/tin_files/22.png">
                                            @break

                                        @default
                                            <img class="w-75" src="/assets/images/tin_files/24.png">
                                    @endswitch
                                </div>
                            </div>
                        </div>
                        </a >
                    </div>
            @endforeach
            
        </div>
        <div class="my-auto col-md-4">
                @foreach ($statuses as $status)
                                <div class="overflow-hidden card" style="background-color: {{ $status->color }};">
                                <a href="/admin/prescriptions/status/{{ $status->id }}">
                                <div class="p-5 card-body" style="color:black!important;">          
                                    <div class="pt-2 row">
                                        <div class="col-8 text-start" >
                                            <span class="lead fw-500"> {{ $status->name }}</span>
                                        </div>
                                        <div class="my-auto text-center col-4">
                                            <span class="fw-semibold"style="font-size:20px;">{{ $status->prescriptions_count }}</span>
                                        </div>
                                    </div>
                                </div>
                                </a>
                            </div>
                @endforeach  
        </div>

        </div>
    </div>
     <!-- EOF ERX ORDER -->

      <div class="p-5 my-5 bg-light rounded-3" >
       
        <div class="clearfix"></div>
            <table id="orx_table" class="table text-center table-light" style="width:100%">
                <thead></thead>
                <tbody>
                    
                </tbody>
            </table>
    </div>

    

@stop

@section('pages_specific_scripts')
            <script>

               document.addEventListener('DOMContentLoaded', function() {
                    var calendarEl = document.getElementById('calendar2');
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                    themeSystem: 'bootstrap5'
                    });
                    calendar.render();
                });
        
              $(document).ready(function() {
                    $( ".datepicker" ).datepicker({
                        changeMonth: true,
                        changeYear: true ,
                        dateFormat: 'yy-mm-dd'  
                    });     

                    
                         $('#downloadLink').on('click', function (e) {
                  
                                const startDate = $('.start_date').val();
                                const endDate = $('.end_date').val();
                                const redirectUrl = `/export-prescriptions?start_date=${startDate}&end_date=${endDate}`;
                               $(this).attr('href', redirectUrl);
                        });            
              });


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
                        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
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
                            { data: 'actions', name: 'actions', title: 'Actions' },    
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
