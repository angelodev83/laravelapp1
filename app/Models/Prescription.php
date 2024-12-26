<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

   public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function file() {
        return $this->hasOne(File::class);
    }

    public function requestType()
    {
        return $this->belongsTo(RequestType::class);
    }

    public function clinicalOrder()
    {
        return $this->belongsTo(ClinicalOrder::class, 'order_number', 'order_number');
    }

}
