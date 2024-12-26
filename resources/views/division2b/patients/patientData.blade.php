@extends('layouts.master')
@section('content')
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!-- PAGE-HEADER -->
        @include('layouts/pageContentHeader/index')
        <!-- PAGE-HEADER END -->
        
        <ul class="mb-3 nav nav-pills" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="btn btn-primary" data-bs-toggle="pill" href="javascript:;" role="tab" aria-selected="true">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-regular fa-user"></i>&nbsp;
                        </div>
                        <div class="tab-title">Facesheet</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-default"  href="/store/clinical/{{request()->id}}/tebra-patients/medications/{{$profileData['id']}}" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-capsules"></i></i>&nbsp;
                        </div>
                        <div class="tab-title">Medications</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/allergies/{{$profileData['id']}}" aria-selected="true">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-person-dots-from-line"></i>&nbsp;
                        </div>
                        <div class="tab-title">Allergies</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/demographics/{{$profileData['id']}}" role="tab" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-id-card"></i>&nbsp;
                        </div>
                        <div class="tab-title">Demographics</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/notes/{{$profileData['id']}}" role="tab" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-pencil"></i>&nbsp;
                        </div>
                        <div class="tab-title">Notes</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/immunization/{{$profileData['id']}}" role="tab" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-syringe"></i>&nbsp;
                        </div>
                        <div class="tab-title">Immunization</div>
                    </div>
                </a>
            </li>
        </ul>
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    
                        
                    <div class="card-body">
                        
                        <input type="file" name="file" class="form-control d-none" id="file">
                        <div class="row g-3">
                            
                            <label class="col-sm-5 col-form-label"><b>FIRSTNAME:</b></label>
                            <div class="col-sm-7">
                                <label id="patient_bdate" class="col-sm-12 col-form-label">{{$profileData['firstname']}}</label>
                            </div>
                            <label class="col-sm-5 col-form-label"><b>LASTNAME:</b></label>
                            <div class="col-sm-7">
                                <label id="patient_age" class="col-sm-12 col-form-label">{{$profileData['lastname']}}</label>
                            </div>
                            <label class="col-sm-5 col-form-label"><b>BIRTH DATE:</b></label>
                            <div class="col-sm-7">
                                <label id="patient_bdate" class="col-sm-12 col-form-label">{{$profileData['birthdate']}}</label>
                            </div>
                            <label class="col-sm-5 col-form-label"><b>AGE:</b></label>
                            <div class="col-sm-7">
                                <label id="patient_age" class="col-sm-12 col-form-label">{{$profileData['age']}}</label>
                            </div>
                            <label class="col-sm-5 col-form-label"><b>GENDER:</b></label>
                            <div class="col-sm-7">
                                <label id="patient_bdate" class="col-sm-12 col-form-label">
                                    @if ($profileData['gender'] === 'M')
                                        Male
                                    @elseif ($profileData['gender'] === 'F')
                                        Female
                                    @else
                                        {{ $profileData['gender'] }}
                                    @endif
                                </label>
                            </div>
                            <label class="col-sm-5 col-form-label"><b>MOBILE #:</b></label>
                            <div class="col-sm-7">
                                <label id="patient_age" class="col-sm-12 col-form-label">{{$profileData['phone_number']}}</label>
                            </div>
                        </div>
                        
                    </div>
                        
                    
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header dt-card-header" style="padding-top: -10px;">
                        <h4 style="padding-left: 2%;">Medications</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="medications_table" class="table row-border table-hover" style="width:100%;">
                                <thead></thead>
                                <tbody>                                   
                                </tbody> 
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header dt-card-header" style="padding-top: -10px;">
                        <h4 style="padding-left: 2%;">Allergies</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="allergies_table" class="table row-border table-hover" style="width:100%;">
                                <thead></thead>
                                <tbody>                                   
                                </tbody> 
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('sweetalert2/script')
</div>
<!--end page wrapper -->
@stop
@section('pages_specific_scripts')
<script>
    $(document).ready(function(){
        
        const medications_table = $('#medications_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBti',
            buttons: [],
            pageLength: 10,
            order: [[0, 'desc']],
            ajax: {
                url: "/admin/divisiontwob/patients/get_patient_medications_data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.patient_id = "{{$profileData['id']}}";
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'id', name: 'id', title: 'ID', visible: true, orderable: true, width: "5%"},
                { data: 'name', name: 'name', title: 'Medication', orderable: false },
                { data: 'quantity', name: 'quantity', title: 'Quantity', orderable: false },
                { data: 'refills', name: 'refills', title: 'Refills', orderable: false },
                { data: 'prescribed_on', name: 'prescribed_on', title: 'Prescribed On', orderable: false },
                { data: 'prescribed_by', name: 'prescribed_by', title: 'Prescribed By', orderable: false },
                { data: 'store_location', name: 'store_location', title: 'Store Location', orderable: false },
            
            ],
        });

        const allergies_table = $('#allergies_table').DataTable({
            scrollX: true,
            serverSide: true,
            stateSave: false,
            dom: 'fBti',
            buttons: [],
            pageLength: 10,
            order: [[0, 'desc']],
            ajax: {
                url: "/admin/divisiontwob/patients/get_patient_allergies_data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data.patient_id = "{{$profileData['id']}}";
                },
                error: function (msg) {
                    handleErrorResponse(msg);
                }
            },
            columns: [
                { data: 'id', name: 'id', title: 'ID', visible: true, width: "5%"},
                { data: 'name', name: 'name', title: 'Medication', orderable: false  },
                { data: 'description', name: 'description', title: 'Description', orderable: false  }
            
            ],
        });

    });
</script>
@stop