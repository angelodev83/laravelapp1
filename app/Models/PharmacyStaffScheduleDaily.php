<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyStaffScheduleDaily extends Model
{
    use HasFactory;

    protected $table = 'pharmacy_staff_schedules_daily';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function schedule()
    {
        return $this->belongsTo(PharmacyStaffSchedule::class, 'pharmacy_staff_schedule_id');
    }
}
