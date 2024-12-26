<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugRecallNotification extends BaseModel
{
    use HasFactory;

    public function wholesaler()
    {
        return $this->belongsTo(Wholesaler::class, 'wholesaler_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(DrugRecallNotificationItem::class, 'drug_recall_notification_id');
    }

    public function documents()
    {
        return $this->hasMany(StoreDocument::class, 'parent_id', 'id')->where('category', 'drugRecallNotification');
    }
}
