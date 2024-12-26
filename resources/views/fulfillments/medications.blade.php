@extends('layouts.master')

@section('content')

           

            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        
                        @if(request('search'))
                        <h1 class="mt-4 float-start">Medications | Search results for: {{ request('search') }}</h1>
                    @else
                       <h1 class="mt-4 float-start">Medications</h1>
                    @endif
                    @php
                        $user = Auth::user();
                    @endphp

                    @if($user->type_id != 2)
                        <a href="/admin/files/csv_upload" class="btn btn-primary btn-lg mt-4 float-end" tabindex="-1" role="button" aria-disabled="true">Upload CSV</a>
                    @endif
                            <div class="clearfix"></div>
                         <div class="table-responsive mt-4">
                            
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>NDC</th>
                                            <th>Package Size</th>
                                            <th>Balance Onhand</th>
                                            <th>Therapeutic Class</th>
                                            <th>Category</th>
                                            <th>Manufacturer</th>
                                            <th>RX Price</th>
                                            <th>340B Price</th>
                                            <th>Last Update</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                   @foreach($medications as $medication)
                                            <tr>
                                                <td>{{ ($medications->currentPage() - 1) * $medications->perPage() + $loop->iteration }}</td>
                                                <td>{{ $medication->name }}</td>
                                                <td>{{ $medication->ndc }}</td>
                                                <td>{{ $medication->package_size }}</td>
                                                <td>{{ $medication->balance_on_hand }}</td>
                                                <td>{{ $medication->therapeutic_class }}</td>
                                                <td>{{ $medication->category }}</td>
                                                <td>{{ $medication->manufacturer }}</td>
                                                <td class="{{ $medication->rx_price < $medication->getAttribute('340b_price') ? 'cheaper' : '' }}">
                                                    ${{ $medication->rx_price }}
                                                </td>
                                                <td class="{{ $medication->rx_price > $medication->getAttribute('340b_price') ? 'cheaper' : '' }}">
                                                    ${{ $medication->getAttribute('340b_price') }}
                                                </td>
                                                 <td>{{ $medication->last_update_date }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                           
                         </div>



                        <div class="d-flex justify-content-center">
                                {{ $medications->appends(['search' => request('search')])->links('pagination::bootstrap-4') }}
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


@stop
@section('pages_specific_scripts')
           
@stop