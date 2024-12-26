<?php

namespace App\Imports;

use App\Models\OperationOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use DateTime;

class OperationOrdersImport implements ToCollection
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
            if($k > 0) {
                $fullname = trim($row[0]);
                $fullnameArr = explode(',',$fullname);
                if(!empty($fullname))
                {
                    $firstname = isset($fullnameArr[1]) ? trim($fullnameArr[1]) : null;
                    $lastname = isset($fullnameArr[0]) ? trim($fullnameArr[0]) : null;
                    $dob = trim($row[1]);
                    if(!empty($dob)) {
                        if(is_numeric($dob)) {
                            $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dob)->format('Y-m-d');
                        } else {
                            $dob = date('Y-m-d', strtotime($dob));
                        }
                    } else {
                        $dob = null;
                    }
                    $rx_numbers = str_replace("\n",',',trim($row[7]));
                    $ship_by_date = trim($row[9]);
                    if(!empty($ship_by_date)) {
                        if(is_numeric($ship_by_date)) {
                            $ship_by_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($ship_by_date)->format('Y-m-d');
                        } else {
                            // $_date = DateTime::createFromFormat('m/d/y', $ship_by_date);
                            $ship_by_date = date('Y-m-d', strtotime($ship_by_date));// $_date->format('Y-m-d');
                        }
                    } else {
                        $ship_by_date = null;
                    }
                        
                    // $check = OperationOrder::where('firstname',$firstname)->where('lastname',$lastname)->where('dob',$dob)->where('status', $this->params['status'])->first();
                    $check = OperationOrder::where('tracking_number', trim($row[8]))
                        ->where('status', $this->params['status'])->first();
                    if(!isset($check->id)) {
                        $inserArr = [
                            'patient_name'      => $fullname,
                            'firstname'         => $firstname,
                            'lastname'          => $lastname,
                            'dob'               => $dob,
                            'address'           => trim($row[2]),
                            'city'              => trim($row[3]),
                            'state'             => trim($row[4]),
                            'phone_number'      => trim($row[5]),
                            'email'             => trim($row[6]),
                            'rx_number'         => $rx_numbers,
                            'tracking_number'   => trim($row[8]),
                            'shipping_label'    => trim($row[10]),
                            'user_id'           => auth()->user()->id,
                            'created_at'        => Carbon::now(),
                            'updated_at'        => Carbon::now(),
                            'pharmacy_store_id' => $this->params['pharmacy_store_id'],
                            'status'            => $this->params['status'],
                            'import_excel_file_id'  => $this->params['file_id']
                        ];
                        if($this->params['status'] == "For Shipping Today") {
                            $inserArr['ship_by_date'] = $ship_by_date;
                        } else {
                            $inserArr['labeled_date'] = $ship_by_date;
                        }
                        OperationOrder::create($inserArr);
                    }
                }
            }
        }
    }
}