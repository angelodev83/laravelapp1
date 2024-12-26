<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['prescription_id','filename', 'path', 'mime_type', 'user_id'];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}
