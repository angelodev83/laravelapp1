<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalTherapyChangeAndReco extends BaseModel
{
    protected $table = 'clinical_therapy_change_and_reco';

    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
