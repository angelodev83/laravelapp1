<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugRecallNotificationItem extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function drugRecallNotification()
    {
        return $this->belongsTo(DrugRecallNotification::class, 'drug_recall_notification_id');
    }
}
