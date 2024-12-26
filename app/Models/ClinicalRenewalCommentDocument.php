<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalRenewalCommentDocument extends BaseModel
{
    use HasFactory;

    public function comment()
    {
        return $this->belongsTo(ClinicalRenewalComment::class, 'clinical_renewal_comment_id');
    }

    public function document()
    {
        return $this->belongsTo(StoreDocument::class, 'document_id');
    }
}
