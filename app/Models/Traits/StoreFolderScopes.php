<?php

namespace App\Models\Traits;

use File;

trait StoreFolderScopes
{
    /**************************
     * Knowledge Base -- start
     **************************/
    public function scopeSOP($query)
    {
        return $query->where('page_id', 35)->whereNull('parent_id');
    }

    public function scopePNP($query)
    {
        return $query->where('page_id', 36)->whereNull('parent_id');
    }

    public function scopeProcessDocuments($query)
    {
        return $query->where('page_id', 37)->whereNull('parent_id');
    }

    public function scopeHowToGuide($query)
    {
        return $query->where('page_id', 38)->whereNull('parent_id');
    }

    public function scopeBoardOfPharmacy($query)
    {
        return $query->where('page_id', 39)->whereNull('parent_id');
    }

    public function scopePharmacyForms($query)
    {
        return $query->where('page_id', 40)->whereNull('parent_id');
    }

    public function scopeKnowledgeBase($query)
    {
        return $query->whereIn('page_id', [35, 36, 37, 38])->whereNull('parent_id');
    }

    /****************************************
     * Finance (Financial Reports) -- start
     ****************************************/
    public function scopePharmacyGrossRevenue($query)
    {
        return $query->where('page_id', 55)->whereNull('parent_id');
    }

    public function scopePaymentsOverview($query)
    {
        return $query->where('page_id', 56)->whereNull('parent_id');
    }

    public function scopeCollectedPayments($query)
    {
        return $query->where('page_id', 57)->whereNull('parent_id');
    }

    public function scopeGrossRevenueAndCogs($query)
    {
        return $query->where('page_id', 58)->whereNull('parent_id');
    }

    public function scopeAccountReceivables($query)
    {
        return $query->where('page_id', 59)->whereNull('parent_id');
    }

    // public function scopeEODReports($query)
    // {
    //     return $query->where('page_id', 60)->whereNull('parent_id');
    // }

    public function scopeScalableExpAnalyzer($query)
    {
        return $query->where('page_id', 61)->whereNull('parent_id');
    }

    public function scopeCashFlow($query)
    {
        return $query->where('page_id', 62)->whereNull('parent_id');
    }

    public function scopeXeroPL($query)
    {
        return $query->where('page_id', 63)->whereNull('parent_id');
    }

    public function scopePayrollPercentage($query)
    {
        return $query->where('page_id', 64)->whereNull('parent_id');
    }

    public function scopeFinancialReports($query)
    {
        return $query->whereIn('page_id', [55, 56, 57, 58, 59, 61, 62, 63, 64, 72])->whereNull('parent_id');
    }

    public function scopeEODReports($query)
    {
        return $query->whereIn('page_id', [65, 66, 67])->whereNull('parent_id');
    }

    public function scopeTransactionReceipts($query)
    {
        return $query->whereIn('page_id', [69, 70, 71])->whereNull('parent_id');
    }

    public function scopeAccountingAndFinance($query)
    {
        return $query->whereIn('page_id', [74, 75, 76, 77, 82, 83, 84])->whereNull('parent_id');
    }
    
}