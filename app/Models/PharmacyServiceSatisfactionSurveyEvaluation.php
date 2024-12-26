<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyServiceSatisfactionSurveyEvaluation extends BaseModel
{
    use HasFactory;

    public function survey()
    {
        return $this->belongsTo(PharmacyServiceSatisfactionSurvey::class, 'survey_id', 'id');
    }
}
