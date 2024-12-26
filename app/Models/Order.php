<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id', 'order_number', 'shipment_status_id'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function files() {
        return $this->hasMany(File::class);
    }
    
    public function podProofOfDelivery()
    {
        return $this->hasOne(File::class, 'id', 'pod_proof_of_delivery')->where('document_type', 'pod');
    }

    public function rxImage()
    {
        return $this->hasOne(File::class, 'id', 'rx_image')->where('document_type', 'rx_image');
    }

    public function intakeForm()
    {
        return $this->hasOne(File::class, 'id', 'intake_form')->where('document_type', 'intake_form');
    }

    public function items()
    {
        return $this->hasMany(Item::class)->where("order_type","mail");
    }

    public function shipmentStatus()
    {
        return $this->belongsTo(ShipmentStatus::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function pharmacyStore()
    {
        return $this->belongsTo(PharmacyStore::class, 'pharmacy_store_id');
    }
    
}
