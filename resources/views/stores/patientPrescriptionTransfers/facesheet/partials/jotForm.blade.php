<div class="row mt-2 border-top">
                
    <label class="col-sm-5 mt-2 col-form-label text-primary">
        Group with which you are affiliated
    </label>
    <div class="col-sm-7 mt-2">
        <label class="col-sm-12 col-form-label">
            {{ $profileData->jotFormPrescriptionTransfer->group_affiliated }}
        </label>
    </div>

    <label class="col-sm-5 col-form-label text-primary">
        Preferred Form of Communication
    </label>
    <div class="col-sm-7">
        <label class="col-sm-12 col-form-label">
            {{ $profileData->jotFormPrescriptionTransfer->preferred_form_of_communication }}
        </label>
    </div>

     <!-- Current Pharmacy Details -->
     <label class="col-sm-12 col-form-label text-primary border-top">
        <u>Current Pharmacy Details:</u>
    </label>

    <label class="col-sm-5 col-form-label text-primary">
        Current Pharmacy
    </label>
    <div class="col-sm-7">
        <label class="col-sm-12 col-form-label">
            {{ $profileData->jotFormPrescriptionTransfer->current_pharmacy }}
        </label>
    </div>

    <label class="col-sm-5 col-form-label text-primary">
        Pharmacy Phone Number
    </label>
    <div class="col-sm-7">
        <label class="col-sm-12 col-form-label">
            {{ $profileData->jotFormPrescriptionTransfer->current_pharmacy_phone_number }}
        </label>
    </div>

    <label class="col-sm-5 col-form-label text-primary">
        Pharmacy Address
    </label>
    <div class="col-sm-7">
        <label class="col-sm-12 col-form-label">
            {{ $profileData->jotFormPrescriptionTransfer->current_pharmacy_address }} {{ $profileData->jotFormPrescriptionTransfer->current_pharmacy_address2 }} {{ $profileData->jotFormPrescriptionTransfer->current_pharmacy_city }}, {{ $profileData->jotFormPrescriptionTransfer->current_pharmacy_state }} {{ $profileData->jotFormPrescriptionTransfer->current_pharmacy_zip }}
        </label>
    </div>

    <!-- Prescriber Information -->
    <label class="col-sm-12 col-form-label text-primary border-top">
        <u>Prescriber Information:</u>
    </label>
    <label class="col-sm-5 col-form-label text-primary">
        Name
    </label>
    <label class="col-sm-7 col-form-label">
        {{ $profileData->jotFormPrescriptionTransfer->prescriber_firstname }} {{ $profileData->jotFormPrescriptionTransfer->prescriber_lastname }}
    </label>

    <label class="col-sm-5 col-form-label text-primary">
        Phone Number
    </label>
    <label class="col-sm-7 col-form-label">
        {{ $profileData->jotFormPrescriptionTransfer->prescriber_phone_number }}
    </label>

    <label class="col-sm-5 col-form-label text-primary">
        Fax Number
    </label>
    <label class="col-sm-7 col-form-label">
        {{ $profileData->jotFormPrescriptionTransfer->prescriber_fax_number }}
    </label>

    <!-- Medication Information -->
    <label class="col-sm-12 col-form-label text-primary border-top">
        <u>Medication Information:</u>
    </label>

    <label class="col-sm-5 col-form-label text-primary">
        Drug Name
    </label>
    <label class="col-sm-7 col-form-label">
        {{ $profileData->jotFormPrescriptionTransfer->medication_drug_name }}
    </label>

    <label class="col-sm-5 col-form-label text-primary">
        Strength
    </label>
    <label class="col-sm-7 col-form-label">
        {{ $profileData->jotFormPrescriptionTransfer->medication_strength }}
    </label>

</div>