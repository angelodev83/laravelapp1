<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalDiagnosis extends Model
{
    use HasFactory;
    
    public function status()
    {
        return $this->belongsTo(StoreStatus::class, 'store_status_id');
    }

}

