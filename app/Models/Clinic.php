<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\BaseModel;

class Clinic extends BaseModel
{
    use HasFactory;

    public function clinicalOrders()
    {
        return $this->hasMany(ClinicalOrder::class, 'clinic_id');
    }
}
