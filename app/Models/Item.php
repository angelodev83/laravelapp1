<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'name', 'sig', 'days_supply', 'refills_remaining', 'ndc', 'rx_stage', 'rx_status', 'inventory_type'];

    public function rxStage()
    {
        return $this->belongsTo(RXStage::class, 'rx_stage');
    }
        public function rxStatus()
    {
        return $this->belongsTo(RXStatus::class, 'rx_status');
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function clinicalOrder()
    {
        return $this->belongsTo(ClinicalOrder::class, 'order_id', 'id');
    }

    public function medication()
    {
        return $this->belongsTo(Medication::class, 'medication_id', 'med_id');
    }
}
