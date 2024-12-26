<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inmar extends BaseModel
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(InmarItem::class, 'inmar_id', 'id');
    }

    public function wholesaler()
    {
        return $this->belongsTo(Wholesaler::class, 'wholesaler_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function status()
    {
        return $this->belongsTo(StoreStatus::class, 'status_id');
    }

    public function task() {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
