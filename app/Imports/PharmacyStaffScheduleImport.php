<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\PharmacyStaff;
use App\Models\PharmacyStaffSchedule;
use App\Models\PharmacyStaffScheduleDaily;
use App\Models\User;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PharmacyStaffScheduleImport implements ToCollection
{
    private $pharmacyStoreId;

    public function __construct($pharmacyStoreId)
    {
        $this->pharmacyStoreId = $pharmacyStoreId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $k => $row) 
        {
            if($k > 0 && isset($row[3])) {

                $flag = false;

                $email = trim($row[0]);
                $fname = trim($row[1]);
                $lname = trim($row[2]);
                $dateFrom = trim($row[3]);
                $dateTo = trim($row[4]);

                $monTimeFrom = trim($row[5]);
                $monTimeTo = trim($row[6]);
                $tueTimeFrom = trim($row[7]);
                $tueTimeTo = trim($row[8]);
                $wedTimeFrom = trim($row[9]);
                $wedTimeTo = trim($row[10]);
                $thuTimeFrom = trim($row[11]);
                $thuTimeTo = trim($row[12]);
                $friTimeFrom = trim($row[13]);
                $friTimeTo = trim($row[14]);
                $satTimeFrom = trim($row[15]);
                $satTimeTo = trim($row[16]);
                $sunTimeFrom = trim($row[17]);
                $sunTimeTo = trim($row[18]);

                $weekArr = [
                    'mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6, 'sun' => 7
                ];

                if(!empty($dateFrom)) {
                    if(is_numeric($dateFrom)) {
                        $dateFrom = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateFrom)->format('Y-m-d');
                    } else {
                        $dateFrom = date('Y-m-d', strtotime($dateFrom));
                    }
                }
                if(!empty($dateTo)) {
                    if(is_numeric($dateTo)) {
                        $dateTo = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateTo)->format('Y-m-d');
                    } else {
                        $dateTo = date('Y-m-d', strtotime($dateTo));
                    }
                }

                $employee = null;
                if(!empty($email)) {
                    $user = User::where('email', $email)->first();
                    if(isset($user->id)) {
                        $employee = Employee::where('user_id', $user->id)->first();
                    } else {
                        $employee = Employee::where('email', $email)->first();
                    }
                }
                
                if(empty($employee)) {
                    $employee = Employee::where('firstname', $fname)->where('lastname', $lname)->first();
                }

                $pharmacyStaff = null;
                if(!empty($employee)) {
                    $pharmacyStaff = PharmacyStaff::where('employee_id', $employee->id)->where('pharmacy_store_id', $this->pharmacyStoreId)->first();
                }

                if(!empty($pharmacyStaff) && !empty($dateFrom) && !empty($dateTo)) {
                    $pharmacyStaffSchedule = PharmacyStaffSchedule::where('pharmacy_staff_id', $pharmacyStaff->id)
                        ->where('date_from', $dateFrom)->where('date_to', $dateTo)->first();
                    if(!isset($pharmacyStaffSchedule->id)) {
                        $pharmacyStaffSchedule = new PharmacyStaffSchedule();
                        $pharmacyStaffSchedule->pharmacy_staff_id = $pharmacyStaff->id;
                        $pharmacyStaffSchedule->date_from = $dateFrom;
                        $pharmacyStaffSchedule->date_to = $dateTo;
                        $pharmacyStaffSchedule->user_id = auth()->user()->id;
                        $pharmacyStaffSchedule->save();

                        foreach($weekArr as $dayTxt => $weekDay)
                        {
                            $labelFrom = $dayTxt.'TimeFrom';
                            $labelTo = $dayTxt.'TimeTo';
                            $timeFrom = $$labelFrom;
                            $timeTo = $$labelTo;

                            $timeFrom = preg_replace("/[a-zA-Z]/", "", $timeFrom);
                            $timeTo = preg_replace("/[a-zA-Z]/", "", $timeTo);

                            $a = is_numeric($timeFrom);
                            if($a) {
                                $timeFrom = ExcelDate::excelToDateTimeObject($timeFrom);
                                $timeFrom = $timeFrom->format('H:i');
                            }   
                            
                            $b = is_numeric($timeTo);
                            if($b) {
                                $timeTo = ExcelDate::excelToDateTimeObject($timeTo);
                                $timeTo = $timeTo->format('H:i');
                            }
                            
                            if(!empty($timeFrom) && !empty($timeTo)) {
                                $pharmacyStaffScheduleDaily = new PharmacyStaffScheduleDaily();
                                $pharmacyStaffScheduleDaily->week_day = $weekDay;
                                $pharmacyStaffScheduleDaily->time_from = $timeFrom;
                                $pharmacyStaffScheduleDaily->time_to = $timeTo;
                                $pharmacyStaffScheduleDaily->pharmacy_staff_schedule_id = $pharmacyStaffSchedule->id;
                                $pharmacyStaffScheduleDaily->user_id = auth()->user()->id;
                                $pharmacyStaffScheduleDaily->save();

                                $flag = true;
                            }
                        }
                        
                    }
                }
                
                
            }
        }
    }

}