<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;

    protected $connection = 'intranet';

    // public function getCreatedAtAttribute($value)
    // {
    //     return Carbon::parse($value)->format('Y-m-d H:i:s');
    // }

    public function getFormattedCreatedAtAttribute()
    {
        // Access the 'created_at' attribute from the model instance
        $createdAt = $this->getAttribute('created_at');

        // Convert to a Carbon instance (date/time library included with Laravel)
        $carbonDate = Carbon::parse($createdAt);

        // Format the Carbon date/time object as desired (e.g., 'Y-m-d H:i:s')
        return $carbonDate->format('M d, Y g:iA');
    }


    public function getFormattedPstCreatedAtAttribute()
    {
        // Access the 'created_at' attribute from the model instance
        $createdAt = $this->getAttribute('created_at');

        $createdAt = $createdAt->setTimezone('America/Los_Angeles');

        // Convert to a Carbon instance (date/time library included with Laravel)
        $carbonDate = Carbon::parse($createdAt);

        // Format the Carbon date/time object as desired (e.g., 'Y-m-d H:i:s')
        return $carbonDate->format('M d, Y g:i A').' PST';
    }

    public function getPstCreatedAtAttribute()
    {
        // Access the 'created_at' attribute from the model instance
        $createdAt = $this->getAttribute('created_at');

        $createdAt = $createdAt->setTimezone('America/Los_Angeles');

        // Convert to a Carbon instance (date/time library included with Laravel)
        $carbonDate = Carbon::parse($createdAt);

        // Format the Carbon date/time object as desired (e.g., 'Y-m-d H:i:s')
        return $carbonDate->format('Y-m-d H:i:s');
    }
}
