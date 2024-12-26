<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends BaseModel
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->number = self::generateNumber();
        });
    }

    public static function generateNumber()
    {
        do {
            $number = self::generateRandomCode();
        } while (self::numberExists($number));

        return $number;
    }

    public static function generateRandomCode()
    {
        $numbers = range(0, 9);
        shuffle($numbers);
        return implode('', array_slice($numbers, 0, 6));
    }

    public static function numberExists($number)
    {
        return self::where('number', $number)->exists();
    }

    /**
     * Get all of the documents for the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany(StoreDocument::class, 'parent_id')->where('category','task');
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
        return $this->belongsTo(StoreStatus::class, 'status_id');
    }

    public function priorityStatus()
    {
        return $this->belongsTo(StoreStatus::class, 'priority_status_id');
    }

    /**
     * Get the drugOrder that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function drugOrder()
    {
        return $this->hasOne(DrugOrder::class, 'task_id');
    }

    public function supplyOrder()
    {
        return $this->hasOne(SupplyOrder::class, 'task_id');
    }

    public function inmar()
    {
        return $this->hasOne(Inmar::class, 'task_id');
    }

    public function clinicalOrder()
    {
        return $this->hasOne(ClinicalOrder::class, 'task_id');
    }

    public function drugOrderStatuses()
    {
        return $this->hasMany(StoreStatus::class, 'status_id')->where('category','procurement_order');
    }

    /**
     * Get the tag that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function watchers()
    {
        return $this->belongsToMany(Employee::class, 'task_watchers')->orderBy('firstname', 'asc')->orderBy('lastname', 'asc');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class, 'ticket_id');
    }

    public function newComment()
    {
        return $this->hasOne(TicketComment::class, 'ticket_id')->latest();
    }

    public function getFormattedPstCompletedAtAttribute()
    {
        // Access the 'completed_at' attribute from the model instance
        $completedAt = $this->getAttribute('completed_at');

        if(!empty($completedAt)) {
            $completedAt = Carbon::parse($completedAt);
            $carbonDate = $completedAt->setTimezone('America/Los_Angeles');            
    
            // Format the Carbon date/time object as desired (e.g., 'Y-m-d H:i:s')
            return $carbonDate->format('M d, Y g:i A').' PST';
        }
        return '';
    }

    public function getPstCompletedAtAttribute()
    {
        // Access the 'completed_at' attribute from the model instance
        $completedAt = $this->getAttribute('completed_at');

        if(!empty($completedAt)) {
            $completedAt = Carbon::parse($completedAt);
            $carbonDate = $completedAt->setTimezone('America/Los_Angeles');
    
            // Format the Carbon date/time object as desired (e.g., 'Y-m-d H:i:s')
            return $carbonDate->format('Y-m-d H:i:s');
        }
        return '';
    }

}
