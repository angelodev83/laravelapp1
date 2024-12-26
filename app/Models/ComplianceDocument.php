<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\FileTrait;

class ComplianceDocument extends Model
{
    use HasFactory, FileTrait;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function selfAuditDocumentTags()
    {
        return $this->hasMany(DocumentTag::class, 'document_id')->where('document_type', 'self')->where('tag_type', 'audit');
    }

    public function auditDocumentTags()
    {
        return $this->hasMany(DocumentTag::class, 'document_id')->where('tag_type', 'audit');
    }

    public function documentTags()
    {
        return $this->hasOne(DocumentTag::class, 'document_id');
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }
}
