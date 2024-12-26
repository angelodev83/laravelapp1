<?php

namespace App\Imports;

use App\Models\GrossSale;
use App\Models\Patient;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class GrossSaleImport extends BaseImport implements ToCollection
{
    protected $params;
    
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $k => $row) 
        {
            if($k > 0) {
                $rx_number = isset($row[20]) ? trim($row[20]) : null;
                $refill_number = isset($row[21]) ? trim($row[21]) : null;

                if(empty($rx_number)) {
                    continue;
                }

                $transmit_type = isset($row[0]) ? trim($row[0]) : null;
                $third_party_name = isset($row[1]) ? trim($row[1]) : null;
                $transaction_date = isset($row[2]) ? trim($row[2]) : null;
                $status_code = isset($row[4]) ? trim($row[4]) : null;
                $third_party_amount = isset($row[5]) ? trim($row[5]) : null;
                $third_party_tax_amount = isset($row[6]) ? trim($row[6]) : null;
                $third_party_total_amount = isset($row[7]) ? trim($row[7]) : null;
                $copay_amount = isset($row[8]) ? trim($row[8]) : null;
                $copay_tax_amount = isset($row[9]) ? trim($row[9]) : null;
                $copay_total_amount = isset($row[10]) ? trim($row[10]) : null;
                $flat_fee = isset($row[11]) ? trim($row[11]) : null;
                $script_amount = isset($row[12]) ? trim($row[12]) : null;
                $script_sales_tax_amount = isset($row[13]) ? trim($row[13]) : null;
                $is_taxable = isset($row[14]) ? trim($row[14]) : null;
                $acquisition_cost = isset($row[15]) ? trim($row[15]) : null;
                $estimated_dir_fee = isset($row[16]) ? trim($row[16]) : null;
                $gross_profit = isset($row[17]) ? trim($row[17]) : null;
                $drug_name = isset($row[18]) ? trim($row[18]) : null;
                $ndc = isset($row[19]) ? trim($row[19]) : null;
                $pharmacist = isset($row[22]) ? trim($row[22]) : null;
                $quantity = isset($row[23]) ? trim($row[23]) : null;
                $patient_name = isset($row[24]) ? trim($row[24]) : null;
                $card_holder_id = isset($row[25]) ? trim($row[25]) : null;
                $third_party_bin = isset($row[26]) ? trim($row[26]) : null;
                $prescriber_name = isset($row[27]) ? trim($row[27]) : null;
                $card_holder_name = isset($row[28]) ? trim($row[28]) : null;
                $dispensing_fee = isset($row[29]) ? trim($row[29]) : null;
                $dispensed_inventory_group = isset($row[30]) ? trim($row[30]) : null;
                $uc_amount = isset($row[31]) ? trim($row[31]) : null;
                $brand_group = isset($row[32]) ? trim($row[32]) : null;
                $net_rx_count = isset($row[33]) ? trim($row[33]) : null;
                $date_filled = isset($row[34]) ? trim($row[34]) : null;

                $patient_id = $this->getPatientIdByFirstnameLastname($patient_name);

                $insertArray = [
                    'patient_id'                => $patient_id,
                    'transmit_type'             => $transmit_type,
                    'third_party_name'          => $third_party_name,
                    'transaction_date'          => $this->resolveDate($transaction_date),
                    'status_code'               => $status_code,
                    'third_party_amount'        => $this->resolveFloatNumber($third_party_amount),
                    'third_party_tax_amount'    => $this->resolveFloatNumber($third_party_tax_amount),
                    'third_party_total_amount'  => $this->resolveFloatNumber($third_party_total_amount),
                    'copay_amount'              => $this->resolveFloatNumber($copay_amount),
                    'copay_tax_amount'          => $this->resolveFloatNumber($copay_tax_amount),
                    'copay_total_amount'        => $this->resolveFloatNumber($copay_total_amount),
                    'flat_fee'                  => $this->resolveFloatNumber($flat_fee),
                    'script_amount'             => $this->resolveFloatNumber($script_amount),
                    'script_sales_tax_amount'   => $this->resolveFloatNumber($script_sales_tax_amount),
                    'is_taxable'                => $is_taxable,
                    'acquisition_cost'          => $this->resolveFloatNumber($acquisition_cost),
                    'estimated_dir_fee'         => $this->resolveFloatNumber($estimated_dir_fee),
                    'gross_profit'              => $this->resolveFloatNumber($gross_profit),
                    'drug_name'                 => $drug_name,
                    'ndc'                       => $ndc,
                    'rx_number'                 => $rx_number,
                    'refill_number'             => $refill_number,
                    'pharmacist'                => $pharmacist,
                    'quantity'                  => $quantity,
                    'patient_name'              => $patient_name,
                    'card_holder_id'            => $card_holder_id,
                    'third_party_bin'           => $third_party_bin,
                    'prescriber_name'           => $prescriber_name,
                    'card_holder_name'          => $card_holder_name,
                    'dispensing_fee'            => $this->resolveFloatNumber($dispensing_fee),
                    'dispensed_inventory_group' => $dispensed_inventory_group,
                    'uc_amount'                 => $this->resolveFloatNumber($uc_amount),
                    'brand_group'               => $brand_group,
                    'net_rx_count'              => $net_rx_count,
                    'date_filled'               => $this->resolveDate($date_filled),
                    'user_id'                   => $this->params['user_id'],
                    'pharmacy_store_id'         => $this->params['pharmacy_store_id'],
                    'created_at'                => Carbon::now()
                ];

                GrossSale::insertOrIgnore($insertArray);

            }
        }

    }
}
