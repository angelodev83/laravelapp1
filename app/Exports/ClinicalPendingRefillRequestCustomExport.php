<?php

namespace App\Exports;

use App\Models\ClinicalPendingRefillRequest;
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

class ClinicalPendingRefillRequestCustomExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize, WithStyles
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
        $query = ClinicalPendingRefillRequest::with(['patient'])
            ->select('date', 'patient_id', 'patient_name', 'rx_number', 'medication_description', 'send_date', 'provider', 'status_name', 'remarks', 'created_at');

        if(isset($this->filterArray['pharmacy_store_id'])) {
            $query = $query->where('pharmacy_store_id', $this->filterArray['pharmacy_store_id']);
        }

        if(isset($this->filterArray['date'])) {
            $query = $query->where('date', $this->filterArray['date']);
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
                    $q->orWhere('patient_name', 'like', $term);
                    $q->orWhere('rx_number', 'like', $term);
                    $q->orWhere('medication_description', 'like', $term);
                    $q->orWhere('provider', 'like', $term);
                    $q->orWhere('status_name', 'like', $term);
                    $q->orWhere('remarks', 'like', $term);
                    $q->orWhereIn('patient_id', $patientIdArr);
                });
            }
        }

        $query = $query->orderBy('patient_name', 'asc')->get();
        
        return $query;
    }

    public function headings(): array
    {
        // Define the headings
        return [
            'Date'
            , 'Script/Rx Number'
            , 'Patient Name'
            , 'Medication'
            , 'Provider'
            , 'Send Date'
            , 'Status'
            , 'Remarks'
            , 'Date Created'
        ];
    }

    public function map($row): array
    {
        $patient = $row->patient ? $row->patient : null;
        $patient_fullname = '';
        if(isset($patient->firstname) || isset($patient->lastname)) {
            $fname = $patient->getDecryptedFirstname();
            $lname = $patient->getDecryptedLastname();
            $patient_fullname = $lname.', '.$fname;
        } else {
            $patient_fullname = $row->patient_name;
        }

        $send_date = !empty($row->send_date) ? Carbon::parse($row->send_date)->format('m/d/Y g:i A') : '';
        $date = !empty($row->date) ? Carbon::parse($row->date)->format('m/d/Y') : '';

        $created_at = Carbon::parse($row->pst_created_at)->format('m/d/Y g:i A');

        return [
            $date
            , $row->rx_number
            , $patient_fullname
            , $row->medication_description
            , $row->provider
            , $send_date
            , $row->status_name
            , $row->remarks
            , $created_at
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                // Apply styling here
                $sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '15a0a3']
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
