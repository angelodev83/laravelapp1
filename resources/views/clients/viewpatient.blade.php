@extends('layouts.master')

@section('content')
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
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

                
                    
                        @if(request()->has('search'))
                            <h2 class="mt-4">Prescriptions | Search results for: {{ request('search') }} </h2>
                        @else
                            <h2 class="mt-4 float-start">Prescriptions</h2>
                        @endif
                   <div class="clearfix"></div>

                    <div class="table-responsive mt-4">
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
                                       
                                   
                                      
                                    </tr>
                                @endforeach

                            </tbody>
                            </table>
                    </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">MGMT88 Portal</div>
                            
                        </div>
                    </div>
                </footer>
            </div>

@include('cs/modals/edit-prescription-form')     
@include('cs/modals/add-prescription-form')
@include('cs/modals/delete-prescription-confirmation')

@stop

@section('pages_specific_scripts')
            <script>
              $(document).ready(function() {
                
                    $( ".datepicker" ).datepicker();                 
              });
        </script>
@stop
