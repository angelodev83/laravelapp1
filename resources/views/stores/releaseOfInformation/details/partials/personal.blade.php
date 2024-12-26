{{-- <div class="scrollable-content"> --}}
    <div class="card">
        <div class="card-header my-0 ps-4 py-0" style="background-color: white !important;">
            <h6 class="mt-3" style="font-weight: 700 !important;">JF-ID# {{$details->jot_form_uid}}</h6>
        </div>
        <div class="card-body custom-fw-bold ms-2 my-0">
            <input type="file" name="file" class="form-control d-none" id="file">
            <div class="row py-0">
                
                <label class="col-sm-4 col-form-label text-primary">
                    <i class="fa-regular fa-user me-2"></i>First Name
                </label>
                <div class="col-sm-8">
                    <label id="patient_bdate" class="col-sm-12 col-form-label">
                        {{$details->getDecryptedPatientFirstname()}}
                    </label>
                </div>
                <label class="col-sm-4 col-form-label text-primary">
                    <i class="fa-regular fa-user me-2"></i>Last Name
                </label>
                <div class="col-sm-8">
                    <label id="patient_age" class="col-sm-12 col-form-label">
                        {{$details->getDecryptedPatientLastname()}}
                    </label>
                </div>
                <label class="col-sm-4 col-form-label text-primary">
                    <i class="fa-solid fa-cake-candles me-2"></i>Birthdate
                </label>
                <div class="col-sm-8">
                    <label id="patient_bdate" class="col-sm-12 col-form-label">
                        {{ date('F d, Y', strtotime($details->getDecryptedPatientBirthDate())) }}
                    </label>
                </div>

                <!-- Hereby details -->
                <label class="col-sm-12 col-form-label text-primary border-top">
                    <u>I hereby authorize:</u>
                </label>
                <label class="col-sm-4 col-form-label text-primary">
                    Person/Org
                </label>
                <label class="col-sm-8 col-form-label">
                    {{ $details->hereby_authorize_person_name }}
                </label>

                @if(!empty($details->hereby_authorize_person_address))
                    <label class="col-sm-4 col-form-label text-primary">
                        Address
                    </label>
                    <div class="col-sm-8">
                        <label class="col-sm-12 col-form-label">
                            {{ $details->hereby_authorize_person_address }}
                        </label>
                    </div>
                    <label class="col-sm-4 col-form-label text-primary">
                        Phone Number
                    </label>
                    <div class="col-sm-8">
                        <label class="col-sm-12 col-form-label">
                            {{ $details->hereby_authorize_person_phone_number }}
                        </label>
                    </div>
                    <label class="col-sm-4 col-form-label text-primary">
                        Fax Number
                    </label>
                    <div class="col-sm-8">
                        <label class="col-sm-12 col-form-label">
                            {{ $details->hereby_authorize_person_fax_number }}
                        </label>
                    </div>
                @endif

                <!-- To release details -->
                <label class="col-sm-12 col-form-label text-primary border-top">
                    <u>To release information to :</u>
                </label>
                <label class="col-sm-4 col-form-label text-primary">
                    Person/Org
                </label>
                <label class="col-sm-8 col-form-label">
                    {{ $details->to_person_name }}
                </label>

                @if(!empty($details->to_person_address))
                    <label class="col-sm-4 col-form-label text-primary">
                        Address
                    </label>
                    <div class="col-sm-8">
                        <label class="col-sm-12 col-form-label">
                            {{ $details->to_person_address }}
                        </label>
                    </div>
                    <label class="col-sm-4 col-form-label text-primary">
                        Phone Number
                    </label>
                    <div class="col-sm-8">
                        <label class="col-sm-12 col-form-label">
                            {{ $details->to_person_phone_number }}
                        </label>
                    </div>
                    <label class="col-sm-4 col-form-label text-primary">
                        Fax Number
                    </label>
                    <div class="col-sm-8">
                        <label class="col-sm-12 col-form-label">
                            {{ $details->to_person_fax_number }}
                        </label>
                    </div>
                @endif


                <!-- Checkboxes -->
                <label class="col-sm-4 col-form-label text-primary">
                    Information to be released Includes
                </label>
                <div class="col-sm-8">
                    @foreach ($items as $item)
                        <label class="col-sm-12 col-form-label">
                            <i class="fa-regular fa-square-check me-3 text-primary"></i>{{ $item }}
                        </label>
                    @endforeach
                </div>


                <!-- purpose -->
                <label class="col-sm-4 col-form-label text-primary">
                    Purpose
                </label>
                <div class="col-sm-8">
                    <label class="col-sm-12 col-form-label">
                        {{ $details->purpose }}
                    </label>
                </div>

                <label class="col-sm-4 col-form-label text-primary">
                    Expiration Date
                </label>
                <div class="col-sm-8">
                    <label class="col-sm-12 col-form-label">
                        {{ date('F d, Y', strtotime($details->expiration_date)) }}
                    </label>
                </div>

                <label class="col-sm-4 col-form-label text-primary">
                    Signed Date
                </label>
                <div class="col-sm-8">
                    <label class="col-sm-12 col-form-label">
                        {{ date('F d, Y', strtotime($details->signed_date)) }}
                    </label>
                </div>

                <label class="col-sm-4 col-form-label text-primary">
                    Relationship to Patient
                </label>
                <div class="col-sm-8">
                    <label class="col-sm-12 col-form-label">
                        {{ $details->relationship_to_patient }}
                    </label>
                </div>

            </div>
            
        </div>
    </div>
{{-- </div> --}}