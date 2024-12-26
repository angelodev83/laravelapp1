<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyStaffLeave extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pharmacyStaff()
    {
        return $this->belongsTo(PharmacyStaff::class, 'pharmacy_staff_id');
    }

    public function status()
    {
        return $this->belongsTo(StoreStatus::class, 'status_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function documents()
    {
        return $this->hasMany(StoreDocument::class, 'parent_id')->where('category','pharmacyStaffLeave');
    }
}
