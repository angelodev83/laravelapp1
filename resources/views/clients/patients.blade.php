@extends('layouts.master')

@section('content')

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
        
             @if(request()->has('search'))
                <h1 class="mt-4 float-start">Patients | Search results for: {{ request('search') }}</h1>
            @else
                <h1 class="mt-4 float-start">Patients</h1>
            @endif  
               
                    <table class="table table-hover" id="patients_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Birthdate</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Zip Code</th>
                                <th>Phone Number</th>
                                <th>Date Created</th>
                                <th>Updated At</th>
                                <!-- Add more columns as needed -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patients as $patient)
                            <tr id="row-{{ $patient->id }}">
                            <td>{{ $loop->iteration }}</td>
                                <td><a href="/admin/patient/{{ $patient->id }}">{{ $patient->firstname }} {{ $patient->lastname }}</a></td>
                                <td>{{ date('M d, Y', strtotime($patient->birthdate)) }}</td>
                                <td>{{ $patient->address }}</td>
                                <td>{{ $patient->city }}</td>
                                <td>{{ $patient->state }}</td>
                                <td>{{ $patient->zip_code }}</td>
                                <td>{{ $patient->phone_number }}</td>
                                <td>{{ date('M d, Y H:i:s', strtotime($patient->created_at)) }}</td>
                                <td>{{ date('M d, Y H:i:s', strtotime($patient->updated_at)) }}</td>
                            
                            
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

             <div class="d-flex justify-content-center">
                {{ $patients->links('pagination::bootstrap-4') }}
            </div>

        </div>
    </main>
    <footer class="py-4 bg-light mt-auto">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center justify-content-between small">
                <div class="text-muted">Ubacare Portal</div>

            </div>
        </div>
    </footer>
</div>
         
@include('cs/modals/add-patient-form')
@include('cs/modals/edit-patient-form')
@include('cs/modals/delete-patient-confirmation')


@stop
@section('pages_specific_scripts')
            <script>
              $(document).ready(function() {
                
                    $( ".datepicker" ).datepicker({
                        changeMonth: true,
                        changeYear: true,
                        minDate: new Date(1900,1-1,1), maxDate: '-1Y',
                        yearRange: '-80:-18'
                    });                 
              });
        </script>
@stop