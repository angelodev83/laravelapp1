<?php

namespace App\Models;

use App\Models\Traits\StoreFolderScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreFolder extends Model
{
    use HasFactory, StoreFolderScopes;

    /**
     * Get the user that owns the StoreFolder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subFolders()
    {
        return $this->hasMany(StoreFolder::class, 'parent_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(StoreFile::class, 'folder_id');
    }

    public function page()
    {
        return $this->belongsTo(StorePage::class, 'page_id');
    }

}
