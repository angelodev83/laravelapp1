<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Icon extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'store_page_id'];

    public function page()
    {
        return $this->belongsTo(StorePage::class, 'store_page_id', 'id');
    }
}
