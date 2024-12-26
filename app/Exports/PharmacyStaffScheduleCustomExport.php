<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PharmacyStaffScheduleCustomExport implements FromArray, WithHeadings, WithStyles
{

    private $scheduleCustomArr = [];

    public function __construct(array $scheduleCustomArr)
    {
        $this->scheduleCustomArr = $scheduleCustomArr;
    }
    /**
     * Return the array of data to be written into the Excel sheet.
     *
     * @return array
     */
    public function array(): array
    {
        // Your custom data rows
        return $this->scheduleCustomArr;
    }

    /**
     * Define the headings for the Excel sheet.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Name',
            'Date',
            'Time Start',
            'Time End',
            'Day',
        ];
    }

    /**
     * Apply styles to the worksheet.
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }

}
