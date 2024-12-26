<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wholesaler extends Model
{
    use HasFactory;

    public function procurement()
    {
        return $this->where('category', 'procurement');
    }

    public function supply()
    {
        return $this->where('category', 'supply');
    }
}
