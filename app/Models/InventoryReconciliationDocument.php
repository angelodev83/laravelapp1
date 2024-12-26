<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\FileTrait;

class InventoryReconciliationDocument extends Model
{
    use HasFactory, FileTrait;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
