<?php

namespace App\Imports;

use App\Models\DrugOrderItemsImportData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;

class DrugOrderItemsImportDataImport implements ToCollection
{
    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $k => $row) 
        {
            $start_to_line = $this->params['start_to_line'] ?? 1;
            $excelRow = $this->params['excelRows'] ?? 1;
            $requiredRows = $this->params['requiredRows'] ?? [1];
            $flag = false;

            if($k >= $start_to_line) {

                $item = [];
                foreach($excelRow as $field => $r)
                {
                    $trimmed = trim($row[$r]);
                    if(in_array($r, $requiredRows)) {
                        if(!empty($trimmed)) {
                            $item[$field] = $trimmed;
                            $flag = true;
                        } else {
                            continue;
                        }
                    } else {
                        $item[$field] = $trimmed;
                    }
                }
                $item = array_merge($item, [
                    'created_at' => Carbon::now()
                    , 'updated_at' => Carbon::now()
                    , 'path' => $this->params['path']
                    , 'store_document_id' => $this->params['store_document_id']
                    , 'drug_order_id' => $this->params['drug_order_id']
                    , 'user_id' => auth()->user()->id
                ]);

                if($flag === true) {
                    DrugOrderItemsImportData::create($item);
                }
            }
        }
    }
}