<?php

namespace App\Exports;

use App\Models\PharmacyStaffSchedule;
use Maatwebsite\Excel\Concerns\FromCollection;

class PharmacyStaffScheduleExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return PharmacyStaffSchedule::all();
    }
}
