<?php

namespace App\Imports;

use App\Models\CollectedPayment;
use App\Models\GrossRevenueAndCog;
use App\Models\OperationOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;

class GrossRevenueAndCogsImport implements ToCollection
{
    protected $params;
    use RemembersRowNumber;
    
    public function __construct($params)
    {
        $this->params = $params;
    }

    public function collection(Collection $rows)
    {   
        $currentRowNumber = $this->getRowNumber();
        foreach ($rows as $k => $row) 
        {
            $rxNumber = trim($row[0]);
            $refillNumber = trim($row[53]);
            
            if($k > 0 && !empty($rxNumber)) {
                
                $patientFullname = trim($row[1]);
                $prescriberFullname = trim($row[2]);
                $prescribedItem = trim($row[3]);
                $dispensedItemName = trim($row[4]);
                
                if (!empty($row[5])) {
                    if(is_numeric($row[5])){
                        $dateFilled = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row[5]))->format('Y-m-d');
                    }
                    else{
                        $dateFilled = date('Y-m-d', strtotime($row[5]));
                    }
                }
                else{
                    $dateFilled = null;
                }
                
                if (!empty($row[6])) {
                    if(is_numeric($row[6])){
                        $dateWritten = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row[6]))->format('Y-m-d');
                    }
                    else{
                        $dateWritten = date('Y-m-d', strtotime($row[6]));
                    }
                }
                else{
                    $dateWritten = null;
                }
                
                $rxStatus = trim($row[7]);
                if (!empty($row[8])) {
                    if(is_numeric($row[8])){
                        $rxStatusChangedOn = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row[8]))->format('Y-m-d H:i:s');
                    }
                    else{
                        $rxStatusChangedOn = date('Y-m-d', strtotime($row[8]));
                    }

                }
                else{
                    $rxStatusChangedOn = null;
                }
                $writtenQuantity = trim($row[9]);
                $payMethod = trim($row[10]);
                $pricingMethod = trim($row[11]);
                $primary = trim($row[12]);
                $secondary = trim($row[13]);
                $origin = trim($row[14]);
                $priority = trim($row[15]);
                $daw = trim($row[16]);
                $bin = trim($row[17]);
                $currentTransactionStatus = trim($row[18]);
                
                if (!empty($row[19])) {
                    if(is_numeric($row[19])){
                        $currentTransactionStatusDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row[19]))->format('Y-m-d H:i:s');
                    }
                    else{
                        $currentTransactionStatusDate = date('Y-m-d H:i:s', strtotime($row[19]));
                    }
                }
                else{
                    $currentTransactionStatusDate = null;
                }
                $pharmacist = trim($row[20]);
                $dispensedQuantity = trim($row[21]);
                $daysSupply = trim($row[22]);
                
                $totalPriceSubmitted = preg_replace('/[^\d.-]/', '', trim($row[23]));
                if ($totalPriceSubmitted === '') {
                    $totalPriceSubmitted = null;
                }
                $totalPricePaid = preg_replace('/[^\d.-]/', '', trim($row[24]));
                if ($totalPricePaid === '') {
                    $totalPricePaid = null;
                }
                $patientPaidAmount = preg_replace('/[^\d.-]/', '', trim($row[25]));
                if ($patientPaidAmount === '') {
                    $patientPaidAmount = null;
                }
                $acquisitionCost = preg_replace('/[^\d.-]/', '', trim($row[26]));
                if ($acquisitionCost === '') {
                    $acquisitionCost = null;
                }
                $grossProfit = preg_replace('/[^\d.-]/', '', trim($row[27]));
                if ($grossProfit === '') {
                    $grossProfit = null;
                }
                
                $dispensedItemNdc = trim($row[28]);
                $facilityName = trim($row[29]);
                $deaSchedule = trim($row[30]);
                $counseledByPharmacist = trim($row[31]);
                $counseledStatus = trim($row[32]);
                $labelType = trim($row[33]);
                $fillRequestedMethod = trim($row[34]);
                $dispensedAwp = preg_replace('/[^\d.-]/', '', trim($row[35]));
                if ($dispensedAwp === '') {
                    $dispensedAwp = 0;
                }
                $refillOrNew = trim($row[36]);
                if (!empty($row[37])) {
                    if(is_numeric($row[37])){
                        $dataEntryOn = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row[37]))->format('Y-m-d H:i:s');
                    }
                    else{
                        $dataEntryOn = date('Y-m-d H:i:s', strtotime($row[37]));
                    }
                }
                else{
                    $dataEntryOn = null;
                }
                $prescriberPrimaryCategory = trim($row[38]);
                $secondaryRemitAmount = preg_replace('/[^\d.-]/', '', trim($row[39]));
                if ($secondaryRemitAmount === '') {
                    $secondaryRemitAmount = null;
                }
                $primaryRemitAmount = preg_replace('/[^\d.-]/', '', trim($row[40]));
                if ($primaryRemitAmount === '') {
                    $primaryRemitAmount = null;
                }
                if (!empty($row[41])) {
                    if(is_numeric($row[41])){
                        $completedOn = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row[41]))->format('Y-m-d H:i:s');
                    }
                    else{
                        $completedOn = date('Y-m-d H:i:s', strtotime($row[41]));
                    }  
                }
                else{
                    $completedOn = null;
                }
                $dispensedItemInventoryGroup = trim($row[42]);
                $trackingNumber = trim($row[43]);
                $shippingName = trim($row[44]);
                $dirFee = preg_replace('/[^\d.-]/', '', trim($row[45]));
                if ($dirFee === '') {
                    $dirFee = null;
                }
                if (!empty($row[46])) {
                    if(is_numeric($row[46])){
                        $filledOn = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row[46]))->format('Y-m-d H:i:s');
                    }
                    else{
                        $filledOn = date('Y-m-d H:i:s', strtotime($row[46]));
                    }

                }
                else{
                    $filledOn = null;
                }
                $transferType = trim($row[47]);
                $transferredFromPharmacy = trim($row[48]);
                $transferredToPharmacy = trim($row[49]);
                $turnaroundTimeHours = preg_replace('/[^\d.-]/', '', trim($row[50]));
                if ($turnaroundTimeHours === '') {
                    $turnaroundTimeHours = null;
                }
                $rebate = preg_replace('/[^\d.-]/', '', trim($row[51]));
                if ($rebate === '') {
                    $rebate = null;
                }
                // dd($completedOn);
                $gender = trim($row[52]);
                                //dd(trim($row[1]));
                $check = GrossRevenueAndCog::where('rx_number', $rxNumber)
                    ->where('refill_number', $refillNumber)
                    ->where('pharmacy_store_id', $this->params['pharmacy_store_id'])->first();
                if(!isset($check->id)) {
                    GrossRevenueAndCog::create([
                        'rx_number'                                 => $rxNumber,
                        'patient_fullname'                          => $patientFullname,
                        'prescriber_full_name'                      => $prescriberFullname,
                        'prescribed_item'                           => $prescribedItem,
                        'dispensed_item_name'                       => $dispensedItemName,
                        'date_filed'                                => $dateFilled,
                        'date_written'                              => $dateWritten,
                        'rx_status'                                 => $rxStatus,
                        'rx_status_changed_on'                      => $rxStatusChangedOn,
                        'written_quantity'                          => $writtenQuantity,
                        'pay_method'                                => $payMethod,
                        'pricing_method'                            => $pricingMethod,
                        'primary'                                   => $primary,
                        'secondary'                                 => $secondary,
                        'origin'                                    => $origin,
                        'priority'                                  => $priority,
                        'daw'                                       => $daw,
                        'bin'                                       => $bin,
                        'current_transaction_status'                => $currentTransactionStatus,
                        'current_transaction_status_date'           => $currentTransactionStatusDate,
                        'pharmacist'                                => $pharmacist,
                        'dispensed_quantity'                        => $dispensedQuantity,
                        'days_supply'                               => $daysSupply,
                        'total_price_submitted'                     => $totalPriceSubmitted,
                        'total_price_paid'                          => $totalPricePaid,
                        'patient_paid_amount'                       => $patientPaidAmount,
                        'acquisition_cost'                          => $acquisitionCost,
                        'gross_profit'                              => $grossProfit,
                        'dispensed_item_ndc'                        => $dispensedItemNdc,
                        'facility_name'                             => $facilityName,
                        'dea_schedule'                              => $deaSchedule,
                        'counseled_by_pharmacist'                   => $counseledByPharmacist,
                        'counseled_status'                          => $counseledStatus,
                        'label_type'                                => $labelType,
                        'fill_requested_method'                     => $fillRequestedMethod,
                        'dispensed_awp'                             => $dispensedAwp,
                        'refill_or_new'                             => $refillOrNew,
                        'data_entry_on'                             => $dataEntryOn,
                        'prescriber_primary_category'               => $prescriberPrimaryCategory,
                        'secondary_remit_amount'                    => $secondaryRemitAmount,
                        'primary_remit_amount'                      => $primaryRemitAmount,
                        'completed_on'                              => $completedOn,
                        'dispensed_item_inventory_group'            => $dispensedItemInventoryGroup,
                        'tracking_number'                           => $trackingNumber,
                        'shipper_name'                              => $shippingName,
                        'dir_fee'                                   => $dirFee,
                        'filled_on'                                 => $filledOn,
                        'transfer_type'                             => $transferType,
                        'transferred_from_pharmacy'                 => $transferredFromPharmacy,
                        'transferred_to_pharmacy'                   => $transferredToPharmacy,
                        'turnaround_time_hours'                     => $turnaroundTimeHours,
                        'rebate'                                    => $rebate,
                        'gender'                                    => $gender,
                        'refill_number'                             => $refillNumber,
                        'user_id'                                   => auth()->user()->id,
                        'pharmacy_store_id'                         => $this->params['pharmacy_store_id'],
                        'import_excel_file_id'                      => $this->params['file_id']
                    ]);
                }
                
            }
            
        }
    }
}