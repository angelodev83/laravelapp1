<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationRtsCommentDocument extends BaseModel
{
    use HasFactory;

    public function comment()
    {
        return $this->belongsTo(OperationRtsComment::class, 'operation_rts_comment_id');
    }

    public function document()
    {
        return $this->belongsTo(StoreDocument::class, 'document_id');
    }
}
