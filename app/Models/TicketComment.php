<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class TicketComment extends BaseModel
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documents()
    {
        return $this->belongsToMany(StoreDocument::class, 'ticket_comment_documents', 'ticket_comment_id', 'document_id');
    }
}
