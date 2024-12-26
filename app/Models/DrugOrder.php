<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugOrder extends BaseModel
{
    use HasFactory;


    public function items()
    {
        return $this->hasMany(DrugOrderItem::class, 'order_id');
    }

    /**
     * Get the status that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // public function status()
    // {
    //     return $this->belongsTo(StoreStatus::class, 'shipment_status_id')->where('category','shipment');
    // }

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
     * Get the prescription that owns the PharmacyPrescription
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prescription()
    {
        return $this->belongsTo(PharmacyPrescription::class, 'pharmacy_prescription_id');
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

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function itemsImported()
    {
        return $this->hasMany(DrugOrderItemsImportData::class, 'drug_order_id');
    }

    public function wholesaler()
    {
        return $this->belongsTo(Wholesaler::class, 'wholesaler_id');
    }
    
}
