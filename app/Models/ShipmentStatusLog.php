<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentStatusLog extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'shipment_status_id', 'changed_at'];
    protected $dates = ['changed_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function shipmentStatus()
    {
        return $this->belongsTo(ShipmentStatus::class);
    }

    
}
