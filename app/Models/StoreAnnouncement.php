<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class StoreAnnouncement extends BaseModel
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pharmacyStore()
    {
        return $this->belongsTo(PharmacyStore::class, 'pharmacy_store_id');
    }

    public function getFormattedSubjectAttribute()
    {
        // Access the 'subject' attribute from the model instance
        $subject = $this->getAttribute('subject');
        return strlen($subject) >= 30 ? substr($subject,0,30).'...' : $subject;
    }
}
