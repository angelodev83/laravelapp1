<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalRenewalComment extends BaseModel
{
    use HasFactory;

    public function renewal()
    {
        return $this->belongsTo(ClinicalRenewal::class, 'clinical_renewal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documents()
    {
        return $this->belongsToMany(StoreDocument::class, 'clinical_renewal_comment_documents', 'clinical_renewal_comment_id', 'document_id');
    }
}
