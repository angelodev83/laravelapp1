<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreDocumentTagTask extends Model
{
    use HasFactory;

    public function documentTag()
    {
        return $this->belongsTo(StoreDocumentTag::class, 'store_document_tag_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
