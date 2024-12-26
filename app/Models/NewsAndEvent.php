<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsAndEvent extends Model
{
    use HasFactory;

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function storeDocuments()
    {
        return $this->hasOne(StoreDocument::class, 'parent_id')->where('category', 'newsAndEvents');
    }
}
