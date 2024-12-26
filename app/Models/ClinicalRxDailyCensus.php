<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalRxDailyCensus extends BaseModel
{
    use HasFactory;

    protected $table = 'clinical_rx_daily_census';


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
