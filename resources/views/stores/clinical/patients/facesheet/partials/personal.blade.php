{{-- <div class="scrollable-content"> --}}
    <div class="card">
        <div class="card-header my-0 ps-4 py-0" style="background-color: white !important;">
            <h6 class="mt-3" style="font-weight: 700 !important;">ID# {{$profileData->id}}</h6>
        </div>
        <div class="card-body custom-fw-bold ms-2 my-0">
            <input type="file" name="file" class="form-control d-none" id="file">
            <div class="row py-0">
                
                <label class="col-sm-4 col-form-label text-primary">
                    <i class="fa-regular fa-user me-2"></i>First Name
                </label>
                <div class="col-sm-8">
                    <label id="patient_bdate" class="col-sm-12 col-form-label">
                        {{$profileData->getDecryptedFirstname()}}
                    </label>
                </div>
                <label class="col-sm-4 col-form-label text-primary">
                    <i class="fa-regular fa-user me-2"></i>Last Name
                </label>
                <div class="col-sm-8">
                    <label id="patient_age" class="col-sm-12 col-form-label">
                        {{$profileData->getDecryptedLastname()}}
                    </label>
                </div>
                <label class="col-sm-4 col-form-label text-primary">
                    <i class="fa-solid fa-cake-candles me-2"></i>Birthdate
                </label>
                <div class="col-sm-8">
                    <label id="patient_bdate" class="col-sm-12 col-form-label">
                        {{ date('F d, Y', strtotime($profileData->getDecryptedBirthdate())) }}
                    </label>
                </div>
                <label class="col-sm-4 col-form-label text-primary">
                    <i class="fa-solid fa-phone me-2"></i>Phone Number
                </label>
                <div class="col-sm-8">
                    <label id="patient_age" class="col-sm-12 col-form-label">
                        {{ $profileData['phone_number'] }}
                    </label>
                </div>
                <label class="col-sm-4 col-form-label text-primary">
                    <i class="fa-solid fa-stethoscope me-2"></i>Pioneer ID
                </label>
                <div class="col-sm-8">
                    <label id="patient_age" class="col-sm-12 col-form-label">
                        {{ $profileData->pioneer_id }}
                    </label>
                </div>
                <label class="col-sm-4 col-form-label text-primary">
                    <i class="fa-solid fa-location-dot me-2"></i>Address
                </label>
                <div class="col-sm-8">
                    <label id="patient_age" class="col-sm-12 col-form-label">
                        {{ $profileData->getDecryptedAddress() }} {{ $profileData->getDecryptedState() }} {{ $profileData->zip_code }}
                    </label>
                </div>
            </div>
            
        </div>
    </div>
{{-- </div> --}}