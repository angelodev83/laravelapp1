<?php

namespace App\Models;

use App\Models\Traits\PatientEncryptions;
use App\Models\Traits\PatientScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Patient extends BaseModel
{
    use PatientScopes, PatientEncryptions;
    
    protected $fillable = ['firstname', 'lastname', 'birthdate', 'address', 'city', 'state', 'zip_code', 'phone_number', 'patientid', 'withorder', 'source', 'email', 'pharmacy_store_id', 'pioneer_id', 'facility_name'];
 
    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function getFullNameWithCommaAttribute()
    {
        return $this->lastname . ', ' . $this->firstname;
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function patient_medications()
    {
        return $this->hasMany(PatientMedication::class);
    }

    public function jotForm()
    {
        return $this->hasOne(PatientJotForm::class, 'patient_id', 'id');
    }

    public function jotFormPrescriptionTransfer()
    {
        return $this->hasOne(PatientJotFormPrescriptionTransfer::class, 'patient_id', 'id');
    }
    
}
