<div class="row mt-2 border-top">
                
    <label class="col-sm-5 mt-2 col-form-label text-primary">
        Current Primary Care Provider
    </label>
    <div class="col-sm-7 mt-2">
        <label class="col-sm-12 col-form-label">
            {{ $profileData->jotForm->current_primary_care_provider }}
        </label>
    </div>

    <label class="col-sm-5 col-form-label text-primary">
        Current Preferred Pharmacy
    </label>
    <div class="col-sm-7">
        <label class="col-sm-12 col-form-label">
            {{ $profileData->jotForm->current_preferred_pharmacy }}
        </label>
    </div>

    <label class="col-sm-5 col-form-label text-primary">
        Other Current Healthcare Providers
    </label>
    <div class="col-sm-7">
        <label class="col-sm-12 col-form-label">
            {{ $profileData->jotForm->other_current_healthcare_providers }}
        </label>
    </div>

    <label class="col-sm-5 col-form-label text-primary">
        Are you a current or past patient at the CTCLUSI Dental Clinic?
    </label>
    <div class="col-sm-7">
        <label class="col-sm-12 col-form-label">
            {{ $profileData->jotForm->is_current_or_past_patient_at_ctclusi_dental_clinic }}
        </label>
    </div>

    <label class="col-sm-5 col-form-label text-primary">
        Are you a CTCLUSI tribal member?
    </label>
    <div class="col-sm-7">
        <label class="col-sm-12 col-form-label">
            {{ $profileData->jotForm->is_ctclusi_tribal_member }}
        </label>
    </div>

    <label class="col-sm-5 col-form-label text-primary">
        Do you have health insurance?
    </label>
    <div class="col-sm-7">
        <label class="col-sm-12 col-form-label">
            {{ $profileData->jotForm->has_health_insurance }}
        </label>
    </div>

    <label class="col-sm-5 col-form-label text-primary">
        Current Insurance Provider
    </label>
    <div class="col-sm-7">
        <label class="col-sm-12 col-form-label">
            {{ $profileData->jotForm->current_insurance_provider }}
        </label>
    </div>

    <label class="col-sm-5 col-form-label text-primary">
        Are you a Head of Household?
    </label>
    <div class="col-sm-7">
        <label class="col-sm-12 col-form-label">
            {{ $profileData->jotForm->is_head_of_household }}
        </label>
    </div>

    <label class="col-sm-5 col-form-label text-primary">
        Policy Holder's Date of Birth
    </label>
    <div class="col-sm-7">
        <label class="col-sm-12 col-form-label">
            {{ !empty($profileData->jotForm->policy_holder_birth_date) ? date('F d, Y', strtotime($profileData->jotForm->policy_holder_birth_date)) : '' }}
        </label>
    </div>

    <label class="col-sm-5 col-form-label text-primary">
        Do you have School Based Health Center Affiliation
    </label>
    <div class="col-sm-7">
        <label class="col-sm-12 col-form-label">
            {{ $profileData->jotForm->school_based_health_center_affiliation }}
        </label>
    </div>

    <label class="col-sm-5 col-form-label text-primary">
        Photo of your insurance policy
    </label>
    <div class="col-sm-7">
        @if(!empty($profileData->jotForm->insurance_policy_image_url))
            <a href="{{ $profileData->jotForm->insurance_policy_image_url }}" target="_blank">
                <img src="{{ $profileData->jotForm->insurance_policy_image_url }}" height="100">
            </a>
        @endif
    </div>

</div>