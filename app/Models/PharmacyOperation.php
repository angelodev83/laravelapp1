<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PharmacyOperation extends Model
{
    use HasFactory, SoftDeletes; 

    public function pharmacySupports()
    {
        return $this->hasMany(PharmacySupport::class, 'pharmacy_operation_id');
    }
}
