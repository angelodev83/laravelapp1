<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationRtsComment extends BaseModel
{
    use HasFactory;

    public function rts()
    {
        return $this->belongsTo(OperationRts::class, 'operation_rts_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documents()
    {
        return $this->belongsToMany(StoreDocument::class, 'operation_rts_comment_documents', 'operation_rts_comment_id', 'document_id');
    }
}
