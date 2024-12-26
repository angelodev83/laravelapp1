<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\DB;

trait PatientScopes
{
    public function scopePioneer($query)
    {
        return $query->where('source', 'pioneer');
    }

    public function scopeJotForm($query)
    {
        return $query->where('source', 'jotform');
    }

    public function scopeCtclusiTm5($query)
    {
        return $query->where(DB::raw('UPPER(facility_name)'), 'CTCLUSI TM5');
    }

    public function scopeTMO5($query)
    {
        return $query->where(DB::raw('UPPER(facility_name)'), 'TMO5');
    }

    public function scopeUnsorted($query)
    {
        return $query->where(function ($query) {
            $query->whereNotIn(DB::raw('UPPER(facility_name)'), ['CTCLUSI TM5', 'TMO5'])
                  ->orWhereNull('facility_name')
                  ->orWhere('facility_name', '');
        });
    }

}