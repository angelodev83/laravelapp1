<?php

namespace App\Models\CTCLUSI\Procurement;

use Illuminate\Database\Eloquent\Model;

use App\Models\CTCLUSI\BaseModel;

use App\Models\StoreStatus;
use App\Models\Medication;
use App\Models\CTCLUSI\Clinic;
use App\Models\InmarItem;

class Inmar extends BaseModel
{
    //protected $connection = 'ctclusi';

    public function statuses()
    {
        return $this->belongsTo(StoreStatus::class, 'status', 'name')->where('category', 'procurement_order');
    }

    public function returnTypeStatus()
    {
        return $this->belongsTo(StoreStatus::class, 'type', 'name')->where('category', 'return_type');
    }

    public function medication()
    {
        return $this->belongsTo(Medication::class, 'drug_id', 'med_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(InmarItem::class, 'inmar_id', 'id');
    }
}
