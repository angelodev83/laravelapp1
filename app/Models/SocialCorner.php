<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialCorner extends BaseModel
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hearts()
    {
        return $this->hasMany(SocialCornerReaction::class, 'social_corner_id')->where('reaction', 'heart');
    }

}
