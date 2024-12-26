<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorePage extends Model
{
    use HasFactory;

    public function scopeFinancialReports($query)
    {
        return $query->where('parent_id', 54);
    }

    public function scopeEODReports($query)
    {
        return $query->where('parent_id', 60);
    }

    public function scopeTransactionReceipts($query)
    {
        return $query->where('parent_id', 68);
    }

    public function scopeAccountingAndFinance($query)
    {
        return $query->where('parent_id', 73);
    }

    public function folders()
    {
        return $this->hasMany(StoreFolder::class, 'page_id');
    }
}
