<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreFileTag extends Model
{
    use HasFactory;

    public function file()
    {
        return $this->belongsTo(StoreFile::class, 'file_id');
    }
}
