<?php

namespace App\Exports;

use App\Models\OperationRts;
use App\Models\Patient;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OperationRtsCustomExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize, WithStyles
{
    private $filterArray = [];

    public function __construct(array $filterArray)
    {
        $this->filterArray = $filterArray;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = OperationRts::with(['patient', 'status'])
            ->select('patient_id', 'rx_number', 'fill_date', 'call_attempts', 'status_id', 'dispensed_item_name', 'priority_name', 'patient_paid_amount', 'created_at');

        if(isset($this->filterArray['pharmacy_store_id'])) {
            $query = $query->where('pharmacy_store_id', $this->filterArray['pharmacy_store_id']);
        }

        if(isset($this->filterArray['is_archived'])) {
            $query = $query->where('is_archived', $this->filterArray['is_archived']);
        }

        if(isset($this->filterArray['search'])) {
            $term = trim($this->filterArray['search']);
            if(!empty($term)) {
                $patientIdArr = Patient::query()->get()->filter(function ($encryptedQuery) use ($term) {
                    return stristr($encryptedQuery->getDecryptedFirstname(), trim($term)) !== false
                        || stristr($encryptedQuery->getDecryptedLastname(), trim($term)) !== false
                        || stristr($encryptedQuery->getDecryptedLastname().', '.$encryptedQuery->getDecryptedFirstname(), trim($term)) !== false
                        || stristr($encryptedQuery->getDecryptedFirstname().' '.$encryptedQuery->getDecryptedLastname(), trim($term)) !== false;
                })->pluck('id');

                $term = '%'.$term.'%';
                $query = $query->where(function($q) use($term, $patientIdArr){
                    $q->orWhere('rx_number', 'like', $term);
                    $q->orWhereIn('patient_id', $patientIdArr);
                });
            }
        }

        $query = $query->orderBy('created_at', 'desc')->get();
        
        return $query;
    }

    public function headings(): array
    {
        // Define the headings
        return [
            'Serial #'
            , 'Patient Name'
            , 'Rx Number'
            , 'Days in Queue'
            , 'Fill Date'
            , 'Call Attempts'
            , 'Status'
            , 'Dispensed Item'
            , 'Patient Paid Amount'
            , 'Priority'
            , 'Date Created'
        ];
    }

    public function map($row): array
    {
        $patient = $row->patient ? $row->patient : null;
        $patient_fullname = '';
        $serial_number = '';
        if(isset($patient->firstname) || isset($patient->lastname)) {
            $fname = $patient->getDecryptedFirstname();
            $lname = $patient->getDecryptedLastname();
            $patient_fullname = $lname.', '.$fname;
            $serial_number = $patient->pioneer_id;
        }

        $fill_date = !empty($row->fill_date) ? Carbon::parse($row->fill_date)->format('m/d/Y') : '';

        $date_today = $this->getCurrentPSTDate('Y-m-d');
        $date1 = Carbon::createFromFormat('Y-m-d', $row->fill_date);
        $date2 = Carbon::createFromFormat('Y-m-d', $date_today);
        $days = $date1->diffInDays($date2);

        $status_name = $row->status ? $row->status->name : '';
        $created_at = Carbon::parse($row->pst_created_at)->format('m/d/Y g:i A');

        return [
            $serial_number
            , $patient_fullname
            , $row->rx_number
            , $days.' Days'
            , $fill_date
            , $row->call_attempts
            , $status_name
            , $row->dispensed_item_name
            , $row->patient_paid_amount
            , $row->priority_name
            , $created_at
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                // Apply styling here
                $sheet->getStyle('A1:Z1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '000000']
                    ]
                ]);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],

            // Styling a specific cell
            // 'A1' => ['font' => ['italic' => true]],

            // Styling columns
            // 'A' => ['font' => ['size' => 12]],
        ];
    }

}
