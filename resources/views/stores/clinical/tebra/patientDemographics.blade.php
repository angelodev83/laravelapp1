@extends('layouts.master')
@section('content')
<!--start page wrapper -->
<style>
    hr{
        color: #9a9595;
    }
</style>
<div class="page-wrapper">
    <div class="page-content">
        <!-- PAGE-HEADER -->
        @include('layouts/pageContentHeader/store')
        <!-- PAGE-HEADER END -->
        
        <ul class="mb-3 nav nav-pills" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/facesheet/{{$id}}" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-regular fa-user"></i>&nbsp;
                        </div>
                        <div class="tab-title">Facesheet</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/medications/{{$id}}" role="tab" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-capsules"></i></i>&nbsp;
                        </div>
                        <div class="tab-title">Medications</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/allergies/{{$id}}">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-person-dots-from-line"></i>&nbsp;
                        </div>
                        <div class="tab-title">Allergies</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-primary" href="javascript:;" data-bs-toggle="pill" role="tab" aria-selected="true">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-id-card"></i></i>&nbsp;
                        </div>
                        <div class="tab-title">Demographics</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/notes/{{$id}}" role="tab" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-pencil"></i>&nbsp;
                        </div>
                        <div class="tab-title">Notes</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="btn btn-default" href="/store/clinical/{{request()->id}}/tebra-patients/immunization/{{$id}}" role="tab" tabindex="-1">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class="fa-solid fa-syringe"></i>&nbsp;
                        </div>
                        <div class="tab-title">Immunization</div>
                    </div>
                </a>
            </li>
        </ul>

        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-primary" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" style="color:#212529" data-bs-toggle="tab" href="#primaryprofile" role="tab" aria-selected="true">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class="bx bxs-user-pin font-18 me-1"></i>
                                </div>
                                <div class="tab-title">Profile</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" style="color:#212529" data-bs-toggle="tab" href="#primarycontact" role="tab" aria-selected="false" tabindex="-1">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class="bx bx-message-add font-18 me-1"></i>
                                </div>
                                <div class="tab-title">Additional Info</div>
                            </div>
                        </a>
                    </li>
                </ul>
                <div class="py-3 tab-content">
                    <div class="tab-pane fade show active" id="primaryprofile" role="tabpanel">
                        <h6 id="patient_name"></h6>
                        <hr>
                        <div class="mb-3 row">
                            <label for="inputEnterYourName" class="col-sm-2 col-form-label"><b>DOB:</b></label>
                            <div class="col-sm-4">
                                <label id="patient_bdate" class="col-sm-12 col-form-label"></label>
                            </div>
                            <label for="inputEnterYourName" class="col-sm-2 col-form-label"><b>Age:</b></label>
                            <div class="col-sm-4">
                                <label id="patient_age" class="col-sm-12 col-form-label"></label>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="inputEnterYourName" class="col-sm-2 col-form-label"><b>Sex:</b></label>
                            <div class="col-sm-4">
                                <label id="patient_gender" class="col-sm-12 col-form-label"></label>
                            </div>
                            <label for="inputEnterYourName" class="col-sm-2 col-form-label"><b>SSN:</b></label>
                            <div class="col-sm-4">
                                <label id="patient_ssn" class="col-sm-12 col-form-label"></label>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="inputEnterYourName" class="col-sm-2 col-form-label"><b>Address:</b></label>
                            <div class="col-sm-4">
                                <label id="patient_address" class="col-sm-12 col-form-label"></label>
                            </div>
                            <label for="inputEnterYourName" class="col-sm-2 col-form-label"><b>Email Address:</b></label>
                            <div class="col-sm-4">
                                <label id="patient_email" class="col-sm-12 col-form-label"></label>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="inputEnterYourName" class="col-sm-2 col-form-label"><b>Mobile Phone:</b></label>
                            <div class="col-sm-4">
                                <label id="patient_mobile" class="col-sm-12 col-form-label"></label>
                            </div>
                            <label for="inputEnterYourName" class="col-sm-2 col-form-label"><b>Home Phone:</b></label>
                            <div class="col-sm-4">
                                <label id="patient_home_phone" class="col-sm-12 col-form-label"></label>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="inputEnterYourName" class="col-sm-2 col-form-label"><b>Work Phone:</b></label>
                            <div class="col-sm-4">
                                <label id="patient_work_phone" class="col-sm-12 col-form-label"></label>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="tab-pane fade" id="primarycontact" role="tabpanel">
                        <hr>
                        <div class="mb-3 row">
                            <label for="inputEnterYourName" class="col-sm-2 col-form-label"><b>PHARMACIES:</b></label>
                            <div class="col-sm-4">
                                <label id="patient_pharmacies" class="col-sm-12 col-form-label"></label>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3 row">
                            <label for="inputEnterYourName" class="col-sm-2 col-form-label"><b>Referred By:</b></label>
                            <div class="col-sm-10">
                                <label id="patient_ref_by" class="col-sm-12 col-form-label"></label>
                            </div>
                            <label for="inputEnterYourName" class="col-sm-2 col-form-label"><b>Referring Provider:</b></label>
                            <div class="col-sm-10">
                                <label id="patient_ref_provider" class="col-sm-12 col-form-label"></label>
                            </div>
                            <label for="inputEnterYourName" class="col-sm-2 col-form-label"><b>Referring Source:</b></label>
                            <div class="col-sm-10">
                                <label id="patient_ref_source" class="col-sm-12 col-form-label"></label>
                            </div>
                        </div>
                        <hr>
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
    // Get the URL
    let url = window.location.href;
    // Split the URL by '/'
    let parts = url.split('/');
    // Get the last part of the URL
    let urlPatientId = parts[parts.length - 1];

    let patientId = {{$id}};

    
    $(document).ready(function(){
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/store/clinical/tebra-patients/get_patient_data/"+patientId,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                $('#patient_name').text(data.data.firstname+' '+data.data.lastname);
                $('#patient_bdate').text(data.data.birthdate);
                $('#patient_age').text(' ('+data.data.age+' yo)');
                if(data.data.gender == 'M'){
                    gender = "Male";
                }
                else if(data.data.gender == 'F'){
                    gender = "Female";
                }
                else{
                    gender = data.data.gender;
                }
                $('#patient_gender').text(gender);
                $('#patient_ssn').text(data.data.ssn);
                $('#patient_address').text(data.data.address);
                $('#patient_email').text(data.data.email);
                $('#patient_mobile').text(data.data.phone_number);
                $('#patient_home_phone').text(data.data.home_phone);
                $('#patient_work_phone').text(data.data.work_phone);

                //additional info
                $('#patient_pharmacies').text(data.data.practice_name);
                $('#patient_ref_by').text(data.data.referring_provider_fullname);
                $('#patient_ref_provider').text(data.data.referring_provider_id);
                $('#patient_ref_source').text(data.data.referral_source);
            },
            error: function (msg) {
                handleErrorResponse(msg);
            }
        });
    });

    $('#add_modal').on('hidden.bs.modal', function(){
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
        $('.error_txt').remove();
    });

    $('#edit_modal').on('hidden.bs.modal', function(){
        $(this)
        .find("input,textarea,select")
        .val('')
        .end();
        $('.error_txt').remove();
    });

    function showAddNewForm(){
        $('#add_modal').modal('show');
        $('#modal_title').text('ALLERGY FORM');
        $('#add_modal #patient_id').val(patientId);
    }

    function showEditForm(data){
        let id = $(data).data('id');
        let name = $(data).data('name');
        let description = $(data).data('description');
        $('#edit_modal #modal_title').html('Allergy ID: '+id);
        $('#edit_modal').modal('show');

        $('#edit_modal #allergy_id').val(id);
        $('#edit_modal #name').val(name);
        $('#edit_modal #description').val(description);
    }
</script>
@stop
