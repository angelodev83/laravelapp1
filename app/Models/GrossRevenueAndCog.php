<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrossRevenueAndCog extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'rx_number',
        'patient_fullname',
        'prescriber_full_name',
        'prescribed_item',
        'dispensed_item_name',
        'date_filed',
        'date_written',
        'rx_status',
        'pharmacy_store_id',
        'user_id',
        'import_excel_file_id',
        'rx_status_changed_on',
        'written_quantity',
        'pay_method',
        'pricing_method',
        'primary',
        'secondary',
        'origin',
        'priority',
        'daw',
        'bin',
        'current_transaction_status',
        'current_transaction_status_date',
        'pharmacist',
        'dispensed_quantity',
        'days_supply',
        'total_price_submitted',
        'total_price_paid',
        'patient_paid_amount',
        'acquisition_cost',
        'gross_profit',
        'dispensed_item_ndc',
        'facility_name',
        'dea_schedule',
        'counseled_by_pharmacist',
        'counseled_status',
        'label_type',
        'fill_requested_method',
        'dispensed_awp',
        'refill_or_new',
        'data_entry_on',
        'prescriber_primary_category',
        'secondary_remit_amount',
        'primary_remit_amount',
        'completed_on',
        'dispensed_item_inventory_group',
        'tracking_number',
        'shipper_name',
        'dir_fee',
        'filled_on',
        'transfer_type',
        'transferred_from_pharmacy',
        'transferred_to_pharmacy',
        'turnaround_time_hours',
        'rebate',
        'gender',
        'refill_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
