<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalOrder extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'status_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'id');
    }

    public function shipmentStatus()
    {
        return $this->belongsTo(ShipmentStatus::class, 'shipment_status_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(ClinicOrderItem::class, 'clinic_order_id');
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'order_number', 'order_number');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function status()
    {
        return $this->belongsTo(StoreStatus::class, 'status_id');
    }
}
