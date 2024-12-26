<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationRts extends BaseModel
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function status()
    {
        return $this->belongsTo(StoreStatus::class, 'status_id');
    }

    public function comments()
    {
        return $this->hasMany(OperationRtsComment::class, 'operation_rts_id');
    }

}
