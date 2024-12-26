<?php

namespace App\Imports;

use App\Models\AccountReceivable;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class AccountReceivableImport implements ToCollection
{
    protected $params;
    private $as_of_date;
    
    public function __construct($params)
    {
        $this->params = $params;
        $this->as_of_date = null;
    }

    public function collection(Collection $rows)
    { 
        $as_of_date = null;  
        foreach ($rows as $k => $row) 
        {
            if($k == 4) {
                $as_of_date = trim($row[0]);
                $this->as_of_date = $as_of_date;
            }

            $accountNumber = trim($row[0]);
            $accountName = trim($row[1]);

            if($k > 7 && !empty($accountName)) {

                $as_of_date = $this->as_of_date;
                if(!empty($as_of_date)) {
                    if(is_numeric($as_of_date)) {
                        $as_of_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($as_of_date))->format('Y-m-d');
                    }
                    else{
                        $date = DateTime::createFromFormat('m/d/Y', $as_of_date);
                        $as_of_date = $date->format('Y-m-d');
                    }
                }

                $primaryPhone = trim($row[2]);

                if (!empty($row[3])) {
                    if(is_numeric($row[3])){
                        $dateLastPayment = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row[3]))->format('Y-m-d');
                    }
                    else{
                        $date = DateTime::createFromFormat('m/d/Y', trim($row[3]));
                        $dateLastPayment = $date->format('Y-m-d');
                    }
                }
                else{
                    $dateLastPayment = null;
                }

                $amountLastPayment = preg_replace('/[^\d.-]/', '', trim($row[6]));
                if ($amountLastPayment === '') {
                    $amountLastPayment = 0;
                }
                $amountLastPayment = (float) $amountLastPayment;

                $amountCreditLimit = preg_replace('/[^\d.-]/', '', trim($row[9]));
                if ($amountCreditLimit === '') {
                    $amountCreditLimit = 0;
                }
                $amountCreditLimit = (float) $amountCreditLimit;

                $amountNewCharges = preg_replace('/[^\d.-]/', '', trim($row[10]));
                if ($amountNewCharges === '') {
                    $amountNewCharges = 0;
                }
                $amountNewCharges = (float) $amountNewCharges;

                $amountInvoicedLessThan30 = preg_replace('/[^\d.-]/', '', trim($row[11]));
                if ($amountInvoicedLessThan30 === '') {
                    $amountInvoicedLessThan30 = 0;
                }
                $amountInvoicedLessThan30 = (float) $amountInvoicedLessThan30;

                $amount30Days = preg_replace('/[^\d.-]/', '', trim($row[12]));
                if ($amount30Days === '') {
                    $amount30Days = 0;
                }
                $amount30Days = (float) $amount30Days;

                $amount60Days = preg_replace('/[^\d.-]/', '', trim($row[13]));
                if ($amount60Days === '') {
                    $amount60Days = 0;
                }
                $amount60Days = (float) $amount60Days;

                $amount90Days = preg_replace('/[^\d.-]/', '', trim($row[14]));
                if ($amount90Days === '') {
                    $amount90Days = 0;
                }
                $amount90Days = (float) $amount90Days;

                $amount120Days = preg_replace('/[^\d.-]/', '', trim($row[15]));
                if ($amount120Days === '') {
                    $amount120Days = 0;
                }
                $amount120Days = (float) $amount120Days;

                $amountUnreconciled = preg_replace('/[^\d.-]/', '', trim($row[16]));
                if ($amountUnreconciled === '') {
                    $amountUnreconciled = 0;
                }
                $amountUnreconciled = (float) $amountUnreconciled;

                // $amountTotalBalance = preg_replace('/[^\d.-]/', '', trim($row[17]));
                // if ($amountTotalBalance === '') {
                //     $amountTotalBalance = 0;
                // }
                $amountTotalBalance = ($amountNewCharges
                    + $amountInvoicedLessThan30
                    + $amount30Days
                    + $amount60Days
                    + $amount90Days
                    + $amount120Days)
                    - $amountUnreconciled;// - $amountLastPayment;


                // $check = AccountReceivable::where('as_of_date', $as_of_date)
                //     ->where('pharmacy_store_id', $this->params['pharmacy_store_id'])
                //     ->whereNot(DB::raw('DATE(created_at)'), DB::raw('DATE(NOW())'))
                //     ->first();
                // if(!isset($check->id)) {
                    AccountReceivable::insertOrIgnore([
                        'as_of_date' => $as_of_date,
                        'account_number' => $accountNumber,
                        'account_name' => $accountName,
                        'primary_phone' => $primaryPhone,
                        'date_last_payment' => $dateLastPayment,
                        'amount_last_payment' => $amountLastPayment,
                        'amount_credit_limit' => $amountCreditLimit,
                        'amount_new_charges' => $amountNewCharges,
                        'amount_invoiced_less_than_30' => $amountInvoicedLessThan30,
                        'amount_30_days' => $amount30Days,
                        'amount_60_days' => $amount60Days,
                        'amount_90_days' => $amount90Days,
                        'amount_120_days' => $amount120Days,
                        'amount_unreconciled' => $amountUnreconciled,
                        'amount_total_balance' => $amountTotalBalance,

                        'user_id'               => auth()->user()->id,
                        'pharmacy_store_id'     => $this->params['pharmacy_store_id'],
                        'import_excel_file_id'  => $this->params['file_id'],
                        'created_at'            => Carbon::now(),
                        'updated_at'            => Carbon::now(),
                    ]);
                // }
                
            }
        }
    }
}