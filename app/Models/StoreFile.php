<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreFile extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the StoreFile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the folder that owns the StoreFile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function folder()
    {
        return $this->belongsTo(StoreFolder::class, 'folder_id');
    }

    public function tag()
    {
        return $this->hasOne(StoreFileTag::class, 'file_id', 'id');
    }
}
