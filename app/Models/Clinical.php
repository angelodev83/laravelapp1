<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinical extends Model
{
    use HasFactory;

    public function monthly_reports()
    {
        return $this->Hasmany(MonthlyClinicalReport::class);
    }
}
