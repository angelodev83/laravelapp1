<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyServiceSatisfactionSurvey extends BaseModel
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }

    public function evaluations()
    {
        return $this->hasMany(PharmacyServiceSatisfactionSurveyEvaluation::class, 'survey_id', 'id');
    }

}
