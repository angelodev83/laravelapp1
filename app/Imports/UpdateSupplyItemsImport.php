<?php

namespace App\Imports;

use App\Models\CollectedPayment;
use App\Models\GrossRevenueAndCog;
use App\Models\OperationOrder;
use App\Models\SupplyItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;

class UpdateSupplyItemsImport implements ToCollection
{
    private $wholesaler;

    public function __construct($wholesaler)
    {
        $this->wholesaler = $wholesaler;
    }

    public function collection(Collection $rows)
    {   
        foreach ($rows as $k => $row) 
        {
            // Storage::disk('local')->append('file.txt', json_encode($row[0]));
        
            $itemNumber = trim($row[0]);

            if($k > 0 && !empty($itemNumber)) {
                
                $description = trim($row[1]);
                SupplyItem::updateOrCreate(
                    ['item_number' => $itemNumber], // Attributes to check for existing record
                    [ // Attributes to update or create with
                        'description' => $description,
                        'wholesaler_name' => $this->wholesaler,
                    ]
                );
                
            }
        }
    }
}