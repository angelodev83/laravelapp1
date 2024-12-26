<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyStaffSchedule extends Model
{
    use HasFactory;

    public function dailies()
    {
        return $this->hasMany(PharmacyStaffScheduleDaily::class, 'pharmacy_staff_schedule_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pharmacyStaff()
    {
        return $this->belongsTo(PharmacyStaff::class, 'pharmacy_staff_id');
    }
}
