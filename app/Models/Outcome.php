<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outcome extends BaseModel
{
    use HasFactory;

    protected $fillable = ['date_reported', 'patients', 'tips_completed', 'cmrs_completed', 'mtm_score', 'pharmacy_store_id'];
}
