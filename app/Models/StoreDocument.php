<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\FileTrait;

class StoreDocument extends Model
{
    use HasFactory, FileTrait;

    public function scopeTickets($query)
    {
        return $query->where('category', 'ticket');
    }

    public function scopeTasks($query)
    {
        return $query->where('category', 'task');
    }

    public function scopeTags($query)
    {
        return $query->where('category', 'storeDocumentTag');
    }

    /**
     * Get the ticket that owns the StoreDocument
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'parent_id');
    }

    /**
     * Get the task that owns the StoreDocument
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /**
     * Get the user that owns the StoreDocument
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function taskAuditDocumentTags()
    {
        return $this->hasMany(DocumentTag::class, 'document_id')->where('document_type', 'task')->where('tag_type', 'audit');
    }

    public function ticketAuditDocumentTags()
    {
        return $this->hasMany(DocumentTag::class, 'document_id')->where('document_type', 'ticket');
    }

    public function news()
    {
        return $this->belongsTo(NewsAndEvent::class, 'parent_id');
    }

    public function storeDocumentTag()
    {
        return $this->belongsTo(StoreDocumentTag::class, 'parent_id', 'id');
    }
}
