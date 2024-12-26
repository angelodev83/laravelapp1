<?php

namespace App\Models\CTCLUSI;

use Illuminate\Database\Eloquent\Model as Eloquent;

class BaseModel extends Eloquent
{
    protected $connection = 'intranet';
}
