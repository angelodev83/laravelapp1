<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use Carbon\Carbon;

class Announcement extends BaseModel
{
    protected $fillable = ['subject', 'content', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFormattedSubjectAttribute()
    {
        // Access the 'subject' attribute from the model instance
        $subject = $this->getAttribute('subject');
        return strlen($subject) >= 30 ? substr($subject,0,30).'...' : $subject;
    }
}
