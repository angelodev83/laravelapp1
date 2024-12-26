<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['firstname', 'lastname', 'nickname', 'date_of_birth', 'contact_number', 'email', 'address', 'image', 'department_id'];


    public function pharmacyStaffs()
    {
        return $this->hasMany(PharmacyStaff::class, 'employee_id');
    }

    public function pharmacySupports()
    {
        return $this->hasMany(PharmacySupport::class, 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFullName()
    {
        return $this->firstname. ' ' . $this->lastname;
    }

    public function getFullNameWithComma()
    {
        return $this->lastname. ', ' . $this->firstname;
    }

    public function supportHeadCategory()
    {
        return $this->hasOne(SupportEmployee::class, 'employee_id')->where('is_head_support',1);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    
}
