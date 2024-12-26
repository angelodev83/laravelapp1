<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTag extends Model
{
    use HasFactory;

    /**
     * Get the document that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function selfDocument()
    {
        return $this->belongsTo(ComplianceDocument::class, 'document_id');
    }

    public function selfAuditDocument()
    {
        return $this->belongsTo(ComplianceDocument::class, 'document_id');
    }

    public function taskDocument()
    {
        return $this->belongsTo(StoreDocument::class, 'document_id')->where('category','task');
    }

    public function auditTag()
    {
        return $this->belongsTo(Tag::class, 'tag_id')->where('type', 'audit');
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }
}
