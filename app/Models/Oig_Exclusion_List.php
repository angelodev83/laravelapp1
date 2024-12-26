<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oig_Exclusion_List extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'oig__exclusion__lists';
}
