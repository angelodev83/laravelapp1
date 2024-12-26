<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use App\Models\Employee;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function checkStorePermission($id, $permissions = [])
    {
        if(auth()->user()->hasPermissionTo('menu_store.'.$id) || auth()->user()->hasRole('super-admin')) {
            if(!empty($permissions)) {
                $this->checkHasAnyPermission($permissions);
            }
        } else {
            throw new \Exception("403");
        }
    }

    public function checkHasAnyPermission($permissions = [])
    {
        if(!empty($permissions)) {
            if(auth()->user()->canany($permissions)) {

            } else {
                throw new \Exception("403");
            }
        }
    }

    protected function getProcurementAssignee()
    {
        $user = User::where("email", 'erwin@mgmt88.com')->first();
        if($user) {
            $emp = Employee::where('user_id', $user->id)->first();
            if($emp) {
                return $emp->id;
            }
        }
        return 14;
    }
    
    protected function getWeeksStartAndEndDatesUTC($year, $month) {
        $startDate = new DateTime("$year-$month-01", new DateTimeZone('UTC'));
        $endDate = clone $startDate;
        $endDate->modify('last day of this month');
    
        $weeks = [];
        $weekStart = clone $startDate;

        $monthWeek = 1;
    
        while ($weekStart <= $endDate) {
            $weekEnd = clone $weekStart;
            $weekEnd->modify('next Sunday');
            
            if ($weekEnd > $endDate) {
                $weekEnd = clone $endDate;
            }

            $N = $weekStart->format('N');

            if($N > 0 && $N < 6) {
                $sDate = (int) $weekStart->format('d');
                $eDate = (int) $weekEnd->format('d');
                $title = 'W'.$monthWeek.' ('.$weekStart->format('F').' '.$sDate.'-'.$eDate.')';
                $weeks[] = [
                    'start' => $weekStart->format('Y-m-d'),
                    'end' => $weekEnd->format('Y-m-d'),
                    'monthWeek' => $monthWeek,
                    'startDate' => $sDate,
                    'endDate' => $eDate,
                    'title' => $title
                ];
                $monthWeek += 1;
            }
            
            // Move to the start of the next week
            $weekStart->modify('next Monday');
        }
    
        return $weeks;
    }

    protected function isValidTimeString($timeString) {
        // Use DateTime::createFromFormat to try to parse the time string
        $dateTime = DateTime::createFromFormat('g:i A', $timeString);
        
        // Check if the parsing succeeded and if the input matches the format exactly
        return $dateTime && $dateTime->format('g:i A') === $timeString;
    }
    
    protected function convertTo24HourFormat($timeString) {
        if (!$this->isValidTimeString($timeString)) {
            return false;
        }
    
        $dateTime = DateTime::createFromFormat('g:i A', $timeString);
        return $dateTime->format('H:i');
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

    protected function getMonths() {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
    }

    protected function getYears() {
        $last = 2022;
        $current = $this->getCurrentPSTDate('Y');

        $years = [];
        for($year = $current; $year >= $last; $year--)
        {
            $years[] = $year;
        }

        return $years;
    }

}
