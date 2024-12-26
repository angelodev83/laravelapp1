<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferPatient extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function status()
    {
        return $this->hasOne(TransferTaskStatus::class, 'transfer_patient_id');
    }
}
