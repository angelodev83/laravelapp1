<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends BaseModel
{
    use HasFactory;

    // Automatically generate a unique random 6-digit number for a specified attribute when creating a new model
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->code = self::generateUniqueCode();
        });
    }

    public static function generateUniqueCode()
    {
        do {
            $number = self::generateRandomCode();
        } while (self::numberExists($number));

        return $number;
    }

    public static function generateRandomCode()
    {
        // $numbers = range(0, 9);
        // shuffle($numbers);
        // return implode('', array_slice($numbers, 0, 6));

        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 6; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function numberExists($number)
    {
        return self::where('code', $number)->exists();
    }

    /**
     * Get all of the documents for the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany(StoreDocument::class, 'parent_id')->where('category','ticket');
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
     * Get the assigned employee that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignedTo()
    {
        return $this->belongsTo(Employee::class, 'assigned_to_employee_id');
    }

    /**
     * Get the status that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(StoreStatus::class, 'status_id')->where('category','task');
    }

    /**
     * Get the status that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function priority()
    {
        return $this->belongsTo(StoreStatus::class, 'priority_status_id')->where('category','priority');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class, 'ticket_id');
    }

    public function newComment()
    {
        return $this->hasOne(TicketComment::class, 'ticket_id')->latest();
    }

    public function statusLogs()
    {
        return $this->hasMany(TicketStatusLog::class, 'ticket_id');
    }

    public function currentStatusLog()
    {
        return $this->hasOne(TicketStatusLog::class, 'ticket_id')->whereNotIn('status_id',[206,706])->latest();
    }

    public function watchers()
    {
        return $this->belongsToMany(Employee::class, 'ticket_watchers')->orderBy('firstname', 'asc')->orderBy('lastname', 'asc');
    }

}
