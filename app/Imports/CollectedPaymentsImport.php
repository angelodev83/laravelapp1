<?php

namespace App\Imports;

use App\Models\CollectedPayment;
use App\Models\OperationOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;

class CollectedPaymentsImport implements ToCollection
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
            $accountNumber = trim($row[0]);
            $accountName = trim($row[1]);
            $reconAccountName = trim($row[2]);

            if($k > 4 && (!empty($accountName) || !empty($reconAccountName)) ) {                
                
                $primaryPhone = trim($row[3]);

                if (!empty($row[4])) {
                    if(is_numeric($row[4])){
                        $pos_sales_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row[4]))->format('Y-m-d');
                    }
                    else{
                        $pos_sales_date = date('Y-m-d', strtotime($row[4]));
                    }
                }
                else{
                    $pos_sales_date = null;
                }

                if (!empty($row[5])) {
                    if(is_numeric($row[5])){
                        $posting_of_payment_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row[5]))->format('Y-m-d');
                    }
                    else{
                        $posting_of_payment_date = date('Y-m-d', strtotime($row[5]));
                    }
                }
                else{
                    $posting_of_payment_date = null;
                }

                $paidAmount = null;
                if(isset($row[6])) {
                    $paidAmount = preg_replace('/[^\d.-]/', '', trim($row[6]));
                    if ($paidAmount === '') {
                        $paidAmount = null;
                    }
                }
                $rxNumber = null;
                if(isset($row[7])) {
                    $rxNumber = trim($row[7]);
                }

                // if (!empty($row[3])) {
                //     if(is_numeric($row[3])){
                //         $lastPaymentDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row[3]))->format('Y-m-d');
                //     }
                //     else{
                //         $lastPaymentDate = date('Y-m-d', strtotime($row[3]));
                //     }
                // }
                // else{
                //     $lastPaymentDate = null;
                // }
                // $lastPaymentAmount = preg_replace('/[^\d.-]/', '', trim($row[4]));
                // if ($lastPaymentAmount === '') {
                //     $lastPaymentAmount = null;
                // }
                // if (!empty($row[5])) {
                //     if(is_numeric($row[5])){
                //         $beginningBalanceDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row[5]))->format('Y-m-d');
                //     }
                //     else{
                //         $beginningBalanceDate = date('Y-m-d', strtotime($row[5]));
                //     }
                // }
                // else{
                //     $beginningBalanceDate = null;
                // }
                // $beginningBalanceAmount = preg_replace('/[^\d.-]/', '', trim($row[6]));
                // if ($beginningBalanceAmount === '') {
                //     $beginningBalanceAmount = null;
                // }
                // $runningBalanceAsOfDate = preg_replace('/[^\d.-]/', '', trim($row[7]));
                // if ($runningBalanceAsOfDate === '') {
                //     $runningBalanceAsOfDate = null;
                // }
            
                // $check = CollectedPayment::where('account_number', $accountNumber)
                //     ->where('pharmacy_store_id', $this->params['pharmacy_store_id'])->first();
                $check = CollectedPayment::where('posting_of_payment_date', $posting_of_payment_date)
                    ->where('rx_number', $rxNumber)
                    ->where('pharmacy_store_id', $this->params['pharmacy_store_id'])
                    ->first();
                if(!isset($check->id)) {
                    CollectedPayment::insertOrIgnore([
                        'account_number'                => $accountNumber,
                        'account_name'                  => $accountName,
                        'primary_phone'                 => $primaryPhone,
                        // 'last_payment_date'             => $lastPaymentDate,
                        // 'last_payment_amount'           => $lastPaymentAmount,
                        // 'beginning_balance_date'        => $beginningBalanceDate,
                        // 'beginning_balance_amount'      => $beginningBalanceAmount,
                        // 'running_balance_as_of_date'    => $runningBalanceAsOfDate,
                        // 'payment_date'                  => $paymentDate,
                        'reconciling_account_name'      => $reconAccountName,
                        'posting_of_payment_date'       => $posting_of_payment_date,
                        'pos_sales_date'                => $pos_sales_date,
                        'paid_amount'                   => $paidAmount,
                        'rx_number'                     => $rxNumber,
                        'user_id'                       => auth()->user()->id,
                        'pharmacy_store_id'             => $this->params['pharmacy_store_id'],
                        'import_excel_file_id'          => $this->params['file_id'],
                        'created_at'                    => Carbon::now(),
                        'updated_at'                    => Carbon::now(), 
                    ]);
                }
                
            }
        }
    }
}