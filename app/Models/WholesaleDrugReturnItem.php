<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WholesaleDrugReturnItem extends Model
{
    use HasFactory;

    public function order()
    {
        return $this->belongsTo(WholesaleDrugReturn::class, 'return_id', 'id');
    }

    /**
     * Get the user that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function medication()
    {
        return $this->belongsTo(Medication::class, 'med_id', 'med_id');
    }
}
