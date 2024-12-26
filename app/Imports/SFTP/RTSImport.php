<?php

namespace App\Imports\SFTP;

use App\Models\OperationRts;
use App\Models\Patient;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class RTSImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $k => $row) 
        {
            $a_title = strtolower(trim($row[0]));
            $a_title = str_replace('  ', ' ',$a_title);

            $word = 'serial';
            if (strpos($a_title, $word) !== false) {
                $a_title = 'patient serial number';
            }

            switch($a_title) {

                case 'rx no:':
                    $rx_number = trim($row[2]);
                    $insertDataArray = [];

                    $rts = OperationRts::where('rx_number', $rx_number)->where('is_archived', 0)->first();
                    if(isset($rts->id)) {
                        break;
                    }

                    $price_paid = trim($row[7]);

                    $patient_paid_amount = preg_replace('/[^\d.-]/', '', $price_paid);
                    if ($patient_paid_amount === '') {
                        $patient_paid_amount = 0;
                    }
                    $patient_paid_amount = (float) $patient_paid_amount;

                    $insertDataArray = [
                        'rx_number'             => $rx_number,
                        'pay_method'            => trim($row[4]),
                        'patient_paid_amount'   => $patient_paid_amount,
                        'pharmacy_store_id'     => 1,
                        'user_id'               => 1,
                        'created_at'            => Carbon::now()
                    ];
                    break;

                case 'date filled:':
                    if(!isset($insertDataArray['rx_number'])) {
                        break;
                    }
                    $fill_date = trim($row[2]);
                    if(!empty($fill_date)) {
                        if(is_numeric($fill_date)) {
                            $fill_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fill_date)->format('Y-m-d');
                        } else {
                            $dateString = DateTime::createFromFormat('m/d/Y', $fill_date);
                            if ($dateString !== false) {
                                $fill_date = $dateString->format('Y-m-d');
                            } else {
                                $fill_date = null;
                            }
                        }
                    } else {
                        $fill_date = null;
                    }
                    $expiration_date = trim($row[7]);
                    if(!empty($expiration_date)) {
                        if(is_numeric($expiration_date)) {
                            $expiration_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($expiration_date)->format('Y-m-d');
                        } else {
                            $dateString = DateTime::createFromFormat('m/d/Y', $expiration_date);
                            if ($dateString !== false) {
                                $expiration_date = $dateString->format('Y-m-d');
                            } else {
                                $expiration_date = null;
                            }
                        }
                    } else {
                        $expiration_date = null;
                    }
                    $date_today = $this->getCurrentPSTDate('Y-m-d');
                    $date1 = Carbon::createFromFormat('Y-m-d', $fill_date);
                    $date2 = Carbon::createFromFormat('Y-m-d', $date_today);
                    $days = $date1->diffInDays($date2);
                    $status_id = $days > 7 ? 922 : 921;

                    $insertDataArray['fill_date'] = $fill_date;
                    $insertDataArray['primary_tp'] = trim($row[4]);
                    $insertDataArray['status_id'] = $status_id;
                    $insertDataArray['expiration_date'] = $expiration_date;
                    break;

                case 'patient:':
                    if(!isset($insertDataArray['rx_number'])) {
                        break;
                    }
                    $patient_name = trim($row[2]);
                    $patient_name = str_replace('  ', ' ',$patient_name);

                    $patients = Patient::all()->filter(function ($patients) use ($patient_name) {
                        return strtolower($patients->getDecryptedFirstname().' '.$patients->getDecryptedLastname()) === strtolower(trim($patient_name));
                    });

                    $patient_id = null;
                    if($patients->count() > 0) {
                        foreach($patients as $p) {
                            $patient_id = $p->id;
                        }
                    }

                    $insertDataArray['patient_id'] = $patient_id;
                    $insertDataArray['secondary_tp'] = trim($row[4]);
                    $insertDataArray['origin'] = trim($row[7]);
                    break;

                case 'patient serial number':
                    if(!isset($insertDataArray['rx_number'])) {
                        break;
                    }

                    $serial_number = trim($row[2]);
                    $p = Patient::where('pioneer_id', $serial_number)->first();

                    if(isset($p->id)) {
                        $insertDataArray['patient_id'] = $p->id;
                    }

                    if(empty($insertDataArray['patient_id'])) {
                        break;
                    }

                    $insertDataArray['priority_name'] = trim($row[7]);
                    break;

                case 'pharmacist:':
                    if(!isset($insertDataArray['rx_number'])) {
                        break;
                    }
                    $insertDataArray['pharmacist'] = trim($row[2]);
                    $insertDataArray['lot_number'] = trim($row[7]);
                    break;

                case 'prescribed item:':
                    if(!isset($insertDataArray['rx_number'])) {
                        break;
                    }
                    $insertDataArray['prescribed_item_name'] = trim($row[2]);
                    break;

                case 'dispensed item:':
                    if(!isset($insertDataArray['rx_number']) || !isset($insertDataArray['patient_id'])) {
                        break;
                    }
                    $insertDataArray['dispensed_item_name'] = trim($row[2]);
                    $insertDataArray['original_status_name'] = trim($row[8]);
                    break;

                case 'dispensed item ndc:':
                    if(!isset($insertDataArray['rx_number'])) {
                        break;
                    }
                    $original_status_changed_date = trim($row[8]);
                    if(!empty($original_status_changed_date)) {
                        if(is_numeric($original_status_changed_date)) {
                            $original_status_changed_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($original_status_changed_date)->format('Y-m-d');
                        } else {
                            $dateString = DateTime::createFromFormat('m/d/Y', $original_status_changed_date);
                            if ($dateString !== false) {
                                $original_status_changed_date = $dateString->format('Y-m-d');
                            } else {
                                $original_status_changed_date = null;
                            }
                        }
                    } else {
                        $original_status_changed_date = null;
                    }
                    $insertDataArray['original_status_changed_date'] = $original_status_changed_date;

                    $rts = OperationRts::where('rx_number', $insertDataArray['rx_number'])->where('is_archived', 0)->first();
                    if(!isset($rts->id) && !empty($insertDataArray['patient_id'])) {
                        OperationRts::insertOrIgnore($insertDataArray);
                    }
                    break;

                case 'daw:':
                    if(!isset($insertDataArray['rx_number'])) {
                        break;
                    }
                    $insertDataArray['daw'] = trim($row[2]);
                    break;

                default:
                    break;
            }

            $c_title = trim($row[2]);
            if(empty($a_title) && !empty($c_title) && isset($insertDataArray['rx_number'])) {
                $insertDataArray['dispensed_item_ndc'] = $c_title;
            }

            $g_title = strtolower(trim($row[6]));
            if($g_title == 'legacy number:') {
                if(isset($insertDataArray['rx_number'])) {
                    $insertDataArray['legacy_number'] = trim($row[7]);
                }
            }
            // if($g_title == 'transaction status:') {
            //     if(isset($insertDataArray['rx_number'])) {
            //         $insertDataArray['transaction_status_name'] = trim($row[8]);
            //     }
            // }
            // if($g_title == 'transaction status date:') {
            //     if(isset($insertDataArray['rx_number'])) {
            //         $transaction_status_changed_date = trim($row[8]);
            //         if(!empty($transaction_status_changed_date)) {
            //             if(is_numeric($transaction_status_changed_date)) {
            //                 $transaction_status_changed_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($transaction_status_changed_date)->format('Y-m-d');
            //             } else {
            //                 $dateString = DateTime::createFromFormat('m/d/Y', $transaction_status_changed_date);
            //                 if ($dateString !== false) {
            //                     $transaction_status_changed_date = $dateString->format('Y-m-d');
            //                 } else {
            //                     $transaction_status_changed_date = null;
            //                 }
            //             }
            //         } else {
            //             $transaction_status_changed_date = null;
            //         }
            //         $insertDataArray['transaction_status_changed_date'] = $transaction_status_changed_date;

            //         $rts = OperationRts::where('rx_number', $insertDataArray['rx_number'])->where('is_archived', 0)->first();
            //         if(!isset($rts->id) && !empty($insertDataArray['patient_id'])) {
            //             OperationRts::insertOrIgnore($insertDataArray);
            //         }
            //     }
            // }

        }

    }

    protected function getCurrentPSTDate($format = 'Y-m-d', $date = null)
    {

        if(!empty($date)) {
            $pst = Carbon::createFromFormat('Y-m-d', $date);
            $pst = $pst->setTimezone('America/Los_Angeles');
        }else {
            $pst = Carbon::now('America/Los_Angeles');
        }
        
        return $pst->format($format);
    }
}
