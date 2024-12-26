<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalOutreach extends BaseModel
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }

    public function inCharge()
    {
        return $this->belongsTo(Employee::class, 'in_charge', 'id')->withDefault();
    }

    public function providerStatus()
    {
        return $this->belongsTo(StoreStatus::class, 'store_provider_status_id')->withDefault();
    }

    public function callStatus()
    {
        return $this->belongsTo(StoreStatus::class, 'store_call_status_id')->withDefault();
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function diagnoses()
    {
        return $this->hasMany(ClinicalDiagnosis::class, 'parent_id');
    }


}
