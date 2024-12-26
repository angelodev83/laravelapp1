<?php

namespace App\Exports;

use App\Models\ClinicalRxDailyTransfer;
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

class ClinicalRxDailyTransferCustomExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize, WithStyles
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
        $query = ClinicalRxDailyTransfer::with(['patient']);

        if(isset($this->filterArray['pharmacy_store_id'])) {
            $query = $query->where('pharmacy_store_id', $this->filterArray['pharmacy_store_id']);
        }

        if(isset($this->filterArray['date'])) {
            $query = $query->where('date', $this->filterArray['date']);
        }

        if(isset($this->filterArray['status'])) {
            $status = strtolower($this->filterArray['status']);
            if($status == 'pending') {
                $query = $query->where('status', $status);
            }
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
                    $q->orWhere('date_called', 'like', $term);
                    $q->orWhere('call_status', 'like', $term);
                    $q->orWhere('provider', 'like', $term);
                    $q->orWhere('is_received', 'like', $term);
                    $q->orWhere('medication_description', 'like', $term);
                    $q->orWhere('previous_pharmacy', 'like', $term);
                    $q->orWhere('expected_rx', 'like', $term);
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
        $arr = [
            'Date'
            , 'Patient Name'
            , 'DOB'
            , 'Meds'
            , 'Call Status'
            , 'Transfer'
            , 'MA'
            , 'Provider'
            , 'Pharmacy'
            , 'Scripts Expected'
            , 'Received'
            , 'Remarks'
            , 'Date Created'
        ];

        $status = '';
        if(isset($this->filterArray['status'])) {
            $status = strtolower($this->filterArray['status']);
        }

        if($status == 'pending') {
            unset($arr[4], $arr[5]);
        }

        // Define the headings
        return $arr;
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

        $date = !empty($row->date) ? Carbon::parse($row->date)->format('m/d/Y') : '';
        $birth_date = !empty($row->birth_date) ? Carbon::parse($row->birth_date)->format('m/d/Y') : '';

        $created_at = Carbon::parse($row->pst_created_at)->format('m/d/Y g:i A');

        $scripts_received = '';
        if($row->scripts_received !== null) {
            $scripts_received = ($row->scripts_received*1);
            if(empty($scripts_received)) {
                $scripts_received = '0';
            }
        }

        $arr = [
            $date
            , $patient_fullname
            , $birth_date
            , $row->medication_description
            , $row->call_status
            , $row->is_transfer
            , $row->is_ma
            , $row->provider
            , $row->fax_pharmacy
            , $row->expected_rx
            , $row->is_received
            , $row->remarks
            , $created_at
        ];

        $status = '';
        if(isset($this->filterArray['status'])) {
            $status = strtolower($this->filterArray['status']);
        }

        if($status == 'pending') {
            unset($arr[4], $arr[5]);
        }

        return $arr;
    }

    public function registerEvents(): array
    {
        $status = '';
        if(isset($this->filterArray['status'])) {
            $status = strtolower($this->filterArray['status']);
        }

        $endColumnLetter = 'M';
        if($status == 'pending') {
            $endColumnLetter = 'K';
        }

        return [
            AfterSheet::class => function(AfterSheet $event) use ($endColumnLetter) {
                $sheet = $event->sheet->getDelegate();
                // Apply styling here
                $sheet->getStyle('A1:'.$endColumnLetter.'1')->applyFromArray([
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
