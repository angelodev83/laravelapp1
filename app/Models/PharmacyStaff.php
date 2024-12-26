<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyStaff extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pharmacy_store_id',
        'employee_id',
        'schedule'
    ];

    public function store()
    {
        return $this->belongsTo(PharmacyStore::class, 'pharmacy_store_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function schedules()
    {
        return $this->hasMany(PharmacyStaffSchedule::class, 'pharmacy_staff_id')->orderBy('date_from', 'desc');
    }

    public function inTodaySchedule()
    {
        return $this->hasOne(PharmacyStaffSchedule::class, 'pharmacy_staff_id')
            ->where('date_from', '<=', date('Y-m-d'))
            ->where('date_to', '>=', date('Y-m-d'));
    }
}
