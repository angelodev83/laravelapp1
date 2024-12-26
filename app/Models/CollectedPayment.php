<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectedPayment extends Model
{
    use HasFactory;

    protected $table = 'collected_payments'; // Define the table name

    protected $fillable = [
        'account_number',
        'account_name',
        'pharmacy_store_id',
        'user_id',
        'paid_amount',
        'rx_number',
        'reconciling_account_name',
        'pos_sales_date',
        'posting_of_payment_date',
    ];
}

