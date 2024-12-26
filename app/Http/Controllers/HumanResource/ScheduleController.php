<?php

namespace App\Http\Controllers\HumanResource;

use App\Exports\PharmacyStaffScheduleCustomExport;
use App\Http\Controllers\Controller;
use App\Interfaces\UploadInterface;
use App\Models\Employee;
use App\Models\PharmacyStaff;
use App\Models\PharmacyStaffLeave;
use App\Models\PharmacyStaffSchedule;
use App\Models\PharmacyStaffScheduleDaily;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ScheduleController extends Controller
{
    private UploadInterface $uploadRepository;

    public function __construct(UploadInterface $uploadRepository)
    {
        $this->uploadRepository = $uploadRepository;
        $this->middleware('permission:menu_store.hr.schedules.create|menu_store.hr.schedules.update|menu_store.hr.schedules.delete|menu_store.hr.schedules.import|menu_store.hr.schedules.index');
    }

    public function index($id, $is_offshore = 1)
    {
        try {
            $this->checkStorePermission($id);

            $current_week_date_from = date('Y-m-d', strtotime(date('Y') . 'W' . date('W') . '1'));
            $current_week_date_to = date('Y-m-d', strtotime(date('Y') . 'W' . date('W') . '7'));
            $current_date = date('Y-m-d');

            $breadCrumb = ['Human Resource', 'Schedule'];
            return view('/stores/humanResource/schedules/index', compact('breadCrumb', 'current_week_date_from', 'current_week_date_to', 'current_date'));
        } catch (\Exception $e) {
            if($e->getCode() == 403) {
                return response()->view('/errors/403/index', [], 403);
            }
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in Store ScheduleController.index.'
            ]);
        }
    }

    public function staff(Request $request)
    {
        try {
            $employee_id = $request->employee_id;
            $pharmacy_store_id = $request->pharmacy_store_id;
            $schedule_id = $request->schedule_id;

            $staffSchedule = PharmacyStaff::with('employee')
                ->where('pharmacy_store_id', $pharmacy_store_id)
                ->where('employee_id', $employee_id)
                ->first();

            $employee = null;
            $schedules = [];
            $events = [];

            $currentSchedule = PharmacyStaffSchedule::with('dailies')->where('id',$schedule_id)->first();
            $dateRange = '';

            if(!empty($currentSchedule)) {
                $dailies = isset($currentSchedule->dailies) ? $currentSchedule->dailies : [];
                $week = [];

                $dateRange = '<i class="fa fa-calendar-week me-2"></i>'.date('M d, Y', strtotime($currentSchedule->date_from)) .' to '.date('M d, Y', strtotime($currentSchedule->date_to));

                foreach($dailies as $day)
                {
                    $week[$day->week_day] = $day;
                    $week[$day->week_day]['formatted_time_from'] = !empty($day->time_from) ? date('H:i', strtotime($day->time_from)) : '';
                    $week[$day->week_day]['formatted_time_to'] = !empty($day->time_from) ? date('H:i', strtotime($day->time_to)) : '';
                    $formatted_time_range = '';
                    if(!empty($day->time_from)) {
                        $formatted_time_range .= $week[$day->week_day]['formatted_time_from'];
                    }
                    if(!empty($day->time_to)) {
                        $formatted_time_range .= ' - '.$week[$day->week_day]['formatted_time_to'];
                    }
                    $week[$day->week_day]['formatted_time_range'] = $formatted_time_range;
                }
            }

            $scheduleMonday = $week[1] ?? [];
            $scheduleTuesday = $week[2] ?? [];
            $scheduleWednesday = $week[3] ?? [];
            $scheduleThursday = $week[4] ?? [];
            $scheduleFriday = $week[5] ?? [];
            $scheduleSaturday = $week[6] ?? [];
            $scheduleSunday = $week[7] ?? [];

            $avatar = '';
            if(!empty($staffSchedule)) {
                $employee = $staffSchedule->employee;
                $schedules = $staffSchedule->schedules;

                if(!empty($employee->image)) {
                    $avatar = '
                        <div class="d-flex">
                            <img src="/upload/userprofile/'.$employee->image.'" width="45" height="45" class="rounded-circle shadow" alt="">
                            <div class="flex-grow-1 ms-3 mt-2">
                                <p class="font-weight-bold mb-0 font-20"><b>'.$employee->firstname.' '.$employee->lastname.'</b></p>
                            </div>
                        </div>
                    ';
                } else {
                    $avatar = '
                        <div class="d-flex">
                            <div class="employee-avatar-'.$employee->initials_random_color.'-initials hr-employee" style="width: 45px !important; height: 45px !important; font-size: 20px !important;">
                            '.strtoupper(substr($employee->firstname, 0, 1)).strtoupper(substr($employee->lastname, 0, 1)).'
                            </div>
                            <p class="font-weight-bold mb-0 ms-3 mt-2 font-20"><b>'.$employee->firstname.' '.$employee->lastname.'</b></p>
                        </div>
                    ';
                }

                foreach($schedules as $schedule) {
                    $dailies = isset($schedule->dailies) ? $schedule->dailies : [];

                    $dfrom = $schedule->date_from;
                    $dto = $schedule->date_to;

                    foreach($dailies as $daily) {
                        $timeFrom = $daily->time_from;
                        $timeTo = $daily->time_to;

                        if(!empty($timeFrom) && !empty($timeTo)) {
                            $day = $daily->week_day;
    
                            $weekDate = date('Y-m-d', strtotime(date('Y', strtotime($dfrom)) . 'W' . date('W', strtotime($dfrom)) . $day));
    
                            $events[] = [
                                'title' => date('H:i', strtotime($timeFrom)).' - '.date('H:i', strtotime($timeTo)),
                                'start' => $weekDate
                            ];
                        }

                    }
                }
            }

            return json_encode([
                'data'=> [
                    'schedule_id' => $schedule_id,
                    'employee' => $employee,
                    'employeeAvatar' => $avatar,
                    // 'schedules' => $schedules,
                    'events' => $events,
                    'currentSchedule' => $currentSchedule,
                    'currentScheduleDateRange' => $dateRange,
                    'currentWeek' => [
                        'scheduleMonday' => $scheduleMonday,
                        'scheduleTuesday' => $scheduleTuesday,
                        'scheduleWednesday' => $scheduleWednesday,
                        'scheduleThursday' => $scheduleThursday,
                        'scheduleFriday' => $scheduleFriday,
                        'scheduleSaturday' => $scheduleSaturday,
                        'scheduleSunday' => $scheduleSunday
                    ]
                ],
                'status'=>'success',
                'message'=>'Record has been retrieved.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in Store ScheduleController.index.'
            ]);
        }
    }


    public function data(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            $date_from = $request->date_from ?? date('Y-m-d', strtotime(date('Y') . 'W' . date('W') . '1'));
            $date_to = $request->date_to ?? date('Y-m-d', strtotime(date('Y') . 'W' . date('W') . '7'));

            // get data from products table
            $query = Employee::with('pharmacyStaffs')->where('is_test', 0)->whereNot('status','Terminated');

            if($request->has('pharmacy_store_id')) {
                $pharmacy_store_id = $request->pharmacy_store_id;
                $employee_id = $request->has('employee_id') ?? null;

                $query = $query->where(function($query) use ($pharmacy_store_id, $employee_id){
                    $query->whereHas('pharmacyStaffs', function($query) use ($pharmacy_store_id, $employee_id) {
                        $query->where('pharmacy_store_id', $pharmacy_store_id);
                        
                        if(!empty($employee_id)) {
                            $query->where('employee_id', $employee_id);
                        }
                    });
                });
            }

            /**
             * HARD CODED
             */
            $query = $query->whereNotIn('id', [208, 207]);
             /**
              * 
              */

            // Search //input all searchable fields
            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        if($column['name'] == 'firstname' ) {
                            $query->orWhere("lastname", 'like', "%".$search."%");
                        }
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }  
                }   
                $query->orWhereRaw('CONCAT(firstname," ", lastname) like  "%'.$search.'%"');  
                $query->orWhereRaw('CONCAT(lastname," ", firstname) like  "%'.$search.'%"');
            });

            //default field for order
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];

            if($orderByCol == 'firstname') {
                $query = $query->orderBy($orderByCol, $orderBy)->orderBy('lastname', $orderBy);
            } else {
                if($request->columns[$request->order[0]['column']]['searchable'] != 'false' &&
                    $request->columns[$request->order[0]['column']]['searchable'] != false
                ) {
                    $query = $query->orderBy($orderByCol, $orderBy);
                }
            }
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $hideU = 'hidden';
            $hideD = 'hidden';
            $hideAll = 'hidden';
            if(auth()->user()->can('menu_store.hr.schedules.update'))
            {
                $hideU = ''; $hideAll = '';
            }
            if(auth()->user()->can('menu_store.hr.schedules.delete'))
            {
                $hideD = ''; $hideAll = '';
            }

            $newData = [];
            foreach ($data as $value) {

                if(!empty($value->image)) {
                    $avatar = '
                        <div class="d-flex">
                            <img src="/upload/userprofile/'.$value->image.'" width="35" height="35" class="rounded-circle" alt="">
                            <div class="flex-grow-1 ms-3 mt-2">
                                <p class="font-weight-bold mb-0">'.$value->firstname.' '.$value->lastname.'</p>
                            </div>
                        </div>
                    ';
                } else {
                    $avatar = '
                        <div class="d-flex">
                            <div class="employee-avatar-'.$value->initials_random_color.'-initials hr-employee" data-id="'.$value->id.'">
                            '.strtoupper(substr($value->firstname, 0, 1)).strtoupper(substr($value->lastname, 0, 1)).'
                            </div>
                            <p class="font-weight-bold mb-0 ms-3 mt-2">'.$value->firstname.' '.$value->lastname.'</p>
                        </div>
                    ';
                }

                $pharmacyStaffs = isset($value->pharmacyStaffs[0]->id) ? $value->pharmacyStaffs[0] : null;
                $pharmacy_staff_id = isset($pharmacyStaffs->id) ? $pharmacyStaffs->id : null;
                // $filteredSchedule = isset($pharmacyStaffs->schedules) ? $pharmacyStaffs->schedules : null;
                $filteredSchedule = PharmacyStaffSchedule::with('dailies')->where('date_from', '>=', $date_from)->where('date_to', '<=', $date_to)->where('pharmacy_staff_id', $pharmacy_staff_id)->first();
                $scheduleDateFrom = isset($filteredSchedule->date_from) ? $filteredSchedule->date_from : '';
                $scheduleDateTo = isset($filteredSchedule->date_to) ? $filteredSchedule->date_to : '';

                $dailies = isset($filteredSchedule->dailies) ? $filteredSchedule->dailies : [];

                $scheduleDateRange = '';
                $scheduleFormattedDateFrom = '';
                $scheduleFormattedDateTo = '';
                if(!empty($scheduleDateFrom)) {
                    $scheduleFormattedDateFrom = date('M d, Y', strtotime($scheduleDateFrom));
                    $scheduleDateRange .= date('M d', strtotime($scheduleDateFrom));
                }
                if(!empty($scheduleDateTo)) {
                    $scheduleFormattedDateTo = date('M d, Y', strtotime($scheduleDateTo));
                    $scheduleDateRange .= ' - '.date('M d', strtotime($scheduleDateTo));
                }

                $week = [];

                foreach($dailies as $day)
                {
                    $week[$day->week_day] = $day;
                    // $week[$day->week_day]['day'] = date('l', strtotime("Sunday +$day->week_day days"));
                    $week[$day->week_day]['formatted_time_from'] = !empty($day->time_from) ? date('h:ia', strtotime($day->time_from)) : '';
                    $week[$day->week_day]['formatted_time_to'] = !empty($day->time_from) ? date('h:ia', strtotime($day->time_to)) : '';
                    $formatted_time_range = '';
                    if(!empty($day->time_from)) {
                        $formatted_time_range .= $week[$day->week_day]['formatted_time_from'];
                    }
                    if(!empty($day->time_to)) {
                        $formatted_time_range .= ' - '.$week[$day->week_day]['formatted_time_to'];
                    }
                    $week[$day->week_day]['formatted_time_range'] = $formatted_time_range;
                }

                $scheduleMonday = $week[1] ?? [];
                $scheduleTuesday = $week[2] ?? [];
                $scheduleWednesday = $week[3] ?? [];
                $scheduleThursday = $week[4] ?? [];
                $scheduleFriday = $week[5] ?? [];
                $scheduleSaturday = $week[6] ?? [];
                $scheduleSunday = $week[7] ?? [];

                $schedule_id = isset($filteredSchedule->id) ? $filteredSchedule->id : null;

                $editBtn = '';
                if(!empty($schedule_id)) {
                    $editBtn = '<button type="button" class="btn btn-primary btn-sm me-2" onclick="showEditPharmacyStaffScheduleModal(' . $value->id . ', ' .$schedule_id. ')" '.$hideU.'><i class="fa-solid fa-pencil"></i></button>';
                }

                $newData[] = [
                    'id' => $value->id,
                    'fullname' => $value->firstname.' '.$value->lastname,
                    'avatar' => $avatar,
                    'firstname' => $value->firstname,
                    'lastname' => $value->lastname,

                    'filteredDateFrom' => $date_from,
                    'filteredDateTo' => $date_to,
                    'filteredSchedule' => $filteredSchedule,

                    'schedule_monday_formatted_time_range' => $scheduleMonday['formatted_time_range'] ?? '',
                    'schedule_tuesday_formatted_time_range' => $scheduleTuesday['formatted_time_range'] ?? '',
                    'schedule_wednesday_formatted_time_range' => $scheduleWednesday['formatted_time_range'] ?? '',
                    'schedule_thursday_formatted_time_range' => $scheduleThursday['formatted_time_range'] ?? '',
                    'schedule_friday_formatted_time_range' => $scheduleFriday['formatted_time_range'] ?? '',
                    'schedule_saturday_formatted_time_range' => $scheduleSaturday['formatted_time_range'] ?? '',
                    'schedule_sunday_formatted_time_range' => $scheduleSunday['formatted_time_range'] ?? '',
                    'schedule_date_from' => $scheduleDateFrom,
                    'schedule_date_to' => $scheduleDateTo,
                    'schedule_formatted_date_range' => $scheduleDateRange,
                    'schedule_formatted_date_from' => $scheduleFormattedDateFrom,
                    'schedule_formatted_date_to' => $scheduleFormattedDateTo,

                    'actions' =>  '<div class="d-flex order-actions" '.$hideAll.'>
                        '.$editBtn.'
                    </div>'
                ];

                // <button type="button" onclick="clickDeleteBtn(' . $value->id . ')" class="btn btn-danger btn-sm" ><i class="fa-solid fa-trash-can" '.$hideD.'></i></button>
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }

    public function staffData(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // get data from products table
            $staff = PharmacyStaff::with('schedules.dailies')
                ->where('pharmacy_store_id', $request->pharmacy_store_id)
                ->where('employee_id', $request->employee_id)->first();

            if(isset($staff->id)) {
                $query = PharmacyStaffSchedule::with('dailies')->where('pharmacy_staff_id', $staff->id);
            } else {
                $query = PharmacyStaffSchedule::with('dailies');
            }

            

            //default field for order
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];

            if($request->columns[$request->order[0]['column']]['searchable'] != 'false' &&
                $request->columns[$request->order[0]['column']]['searchable'] != false
            ) {
                $query = $query->orderBy($orderByCol, $orderBy);
            }

            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {

                $dailies = isset($value->dailies) ? $value->dailies : [];

                $week = [];

                foreach($dailies as $day)
                {
                    $week[$day->week_day] = $day;
                    $week[$day->week_day]['formatted_time_from'] = !empty($day->time_from) ? date('h:ia', strtotime($day->time_from)) : '';
                    $week[$day->week_day]['formatted_time_to'] = !empty($day->time_from) ? date('h:ia', strtotime($day->time_to)) : '';
                    $formatted_time_range = '';
                    // $weekday = date('l', strtotime("Sunday +{$day->week_day} days"));
                    // $weekday = substr($weekday, 0, 3);
                    if(!empty($day->time_from)) {
                        $formatted_time_range .= $week[$day->week_day]['formatted_time_from'];
                    }
                    if(!empty($day->time_to)) {
                        $formatted_time_range .= ' - '.$week[$day->week_day]['formatted_time_to'];
                    }
                    $week[$day->week_day]['formatted_time_range'] = $formatted_time_range;
                }

                $scheduleMonday = $week[1] ?? [];
                $scheduleTuesday = $week[2] ?? [];
                $scheduleWednesday = $week[3] ?? [];
                $scheduleThursday = $week[4] ?? [];
                $scheduleFriday = $week[5] ?? [];
                $scheduleSaturday = $week[6] ?? [];
                $scheduleSunday = $week[7] ?? [];

                $newData[] = [
                    'id' => $value->id,
                    'date_from' => $value->date_from,
                    'date_to' => $value->date_to,
                    'dailies' => $dailies,
                    'schedule_date_from_formatted' => date('M d, Y', strtotime($value->date_from)),
                    'schedule_date_to_formatted' => date('M d, Y', strtotime($value->date_to)),
                    'schedule_monday_formatted_time_range' => $scheduleMonday['formatted_time_range'] ?? '',
                    'schedule_tuesday_formatted_time_range' => $scheduleTuesday['formatted_time_range'] ?? '',
                    'schedule_wednesday_formatted_time_range' => $scheduleWednesday['formatted_time_range'] ?? '',
                    'schedule_thursday_formatted_time_range' => $scheduleThursday['formatted_time_range'] ?? '',
                    'schedule_friday_formatted_time_range' => $scheduleFriday['formatted_time_range'] ?? '',
                    'schedule_saturday_formatted_time_range' => $scheduleSaturday['formatted_time_range'] ?? '',
                    'schedule_sunday_formatted_time_range' => $scheduleSunday['formatted_time_range'] ?? '',
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }

    public function import(Request $request)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();
                $this->uploadRepository->uploadPharmacyStaffSchedules($request);
                DB::commit();
                
                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in PharmacyStaff ScheduleController.upload.'
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();

                $pharmacy_store_id = $request->pharmacy_store_id;
                $employee_id = $request->employee_id;
                $date_from = $request->date_from;
                $date_to = $request->date_to;
                $time_from = $request->time_from;
                $time_to = $request->time_to;

                $time_from_am_pm = $request->time_from_am_pm ?? 'AM';
                $time_to_am_pm = $request->time_to_am_pm ?? 'AM';

                if(!empty($time_from_am_pm)) {
                    $timeString = $time_from.' '.$time_from_am_pm;

                    if ($this->isValidTimeString($timeString)) {
                        $time_from = $this->convertTo24HourFormat($timeString);
                    }
                }

                if(!empty($time_to_am_pm)) {
                    $timeString = $time_to.' '.$time_to_am_pm;

                    if ($this->isValidTimeString($timeString)) {
                        $time_to = $this->convertTo24HourFormat($timeString);
                    }
                }

                $recurringArr = $request->recurring;

                $data = [];
                
                $pharmacyStaff = PharmacyStaff::where('pharmacy_store_id', $pharmacy_store_id)
                    ->where('employee_id', $employee_id)
                    ->first();

                $flag = false;

                if(!isset($pharmacyStaff->employee_id)) {
                    $pharmacyStaff = new PharmacyStaff();
                    $pharmacyStaff->pharmacy_store_id = $pharmacy_store_id;
                    $pharmacyStaff->employee_id = $employee_id;
                    $save = $pharmacyStaff->save();
                    if($save) {
                        $flag = true;
                    }
                } else {
                    $flag = true;
                }

                if($flag === true) {

                    $startDate = new DateTime($date_from, new DateTimeZone('UTC'));
                    $endDate = new DateTime($date_to, new DateTimeZone('UTC'));

                    $weekStart = clone $startDate;
                

                    while ($weekStart <= $endDate) {
                        $weekEnd = clone $weekStart;
                        $weekEnd->modify('next Sunday');
                        
                        if ($weekEnd >= $endDate) {
                            $weekEnd = clone $endDate;
                        }

                        $pharmacyStaffSchedule = PharmacyStaffSchedule::where('pharmacy_staff_id', $pharmacyStaff->id)
                            ->where('date_from', $weekStart->format('Y-m-d'))
                            ->where('date_to', $weekEnd->format('Y-m-d'))
                            ->first();

                        if(!isset($pharmacyStaffSchedule->id)) {
                            $pharmacyStaffSchedule = new PharmacyStaffSchedule();
                            $pharmacyStaffSchedule->user_id = auth()->user()->id;
                            $pharmacyStaffSchedule->pharmacy_staff_id = $pharmacyStaff->id;
                        }

                        $pharmacyStaffSchedule->date_from = $weekStart->format('Y-m-d');
                        $pharmacyStaffSchedule->date_to = $weekEnd->format('Y-m-d');
                        
                        $save = $pharmacyStaffSchedule->save();

                        if($save) {
                            $N = $weekEnd->format('N');

                            $N2 = $weekStart->format('N');

                            foreach($recurringArr as $week_day) {
                                if($N >= $week_day && $week_day >= $N2) {

                                    $pharmacyStaffScheduleDaily = PharmacyStaffScheduleDaily::where('pharmacy_staff_schedule_id', $pharmacyStaffSchedule->id)
                                        ->where('time_from', $time_from)
                                        ->where('time_to', $time_to)
                                        ->where('week_day', $week_day)
                                        ->first();
                                        
                                    if(!isset($pharmacyStaffScheduleDaily->id)) {
                                        $pharmacyStaffScheduleDaily = new PharmacyStaffScheduleDaily();
                                        $pharmacyStaffScheduleDaily->time_from = $time_from;
                                        $pharmacyStaffScheduleDaily->time_to = $time_to;
                                        $pharmacyStaffScheduleDaily->week_day = $week_day;
                                        $pharmacyStaffScheduleDaily->pharmacy_staff_schedule_id = $pharmacyStaffSchedule->id;
                                        $pharmacyStaffScheduleDaily->user_id = auth()->user()->id;
                                        $pharmacyStaffScheduleDaily->save();
                                    }

                                }
                            }
                        }
                        
                        
                        // Move to the start of the next week
                        $weekStart->modify('next Monday');
                    }
                }

                DB::commit();
                
                return json_encode([
                    'data'=> $data,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in ScheduleController.upload.'
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();
                
                $pharmacy_staff_schedule_id = $request->pharmacy_staff_schedule_id;
                $dailies = $request->dailies;

                foreach($dailies as $daily) {
                    $time_from = $daily['time_from'];
                    $time_to = $daily['time_to'];

                    $pharmacyStaffScheduleDaily = PharmacyStaffScheduleDaily::where('pharmacy_staff_schedule_id', $pharmacy_staff_schedule_id)->where('week_day', $daily['week_day'])->first();

                    if(!empty($time_from) && !empty($time_to)) {
                        if(!isset($pharmacyStaffScheduleDaily->id)) {
                            $pharmacyStaffScheduleDaily = new PharmacyStaffScheduleDaily();
                            $pharmacyStaffScheduleDaily->pharmacy_staff_schedule_id = $pharmacy_staff_schedule_id;
                            $pharmacyStaffScheduleDaily->week_day = $daily['week_day'];
                            $pharmacyStaffScheduleDaily->user_id = auth()->user()->id;
                        }
                        $pharmacyStaffScheduleDaily->time_from = date('H:i', strtotime($time_from));
                        $pharmacyStaffScheduleDaily->time_to = date('H:i', strtotime($time_to));
                        $pharmacyStaffScheduleDaily->save();
                    } else {
                        if(isset($pharmacyStaffScheduleDaily->id)) {
                            $pharmacyStaffScheduleDaily->delete();
                        }
                    }
                }

                DB::commit();
                
                return json_encode([
                    'pharmacy_staff_schedule_id'=> $pharmacy_staff_schedule_id,
                    'data'=> $dailies,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in ScheduleController.upload.'
            ]);
        }
    }

    public function events(Request $request) 
    { 
        $is_offshore = $request->is_offshore ?? 0;

        $year = date('Y');
        if($request->has('year')) {
            if(!empty($request->year)) {
                $year = $request->year;
            }
        }
        $currWeek = date('W');
        $query = PharmacyStaffSchedule::with('dailies', 'pharmacyStaff.employee')
            // ->where(function  ($query) use(&$month) {
            //     $query->orWhere(DB::raw("MONTH(date_from)"), $>month);
            //     $query->orWhere(DB::raw("MONTH(date_to)"), $month);
            // })
            ->where(function  ($query) use(&$year) {
                $query->orWhere(DB::raw("YEAR(date_from)"), $year);
                $query->orWhere(DB::raw("YEAR(date_to)"), $year);
            })
            ->whereHas('pharmacyStaff', function($query) use ($is_offshore) {
                $query->whereHas('employee', function($query) use ($is_offshore) {
                    $query->where('is_offshore', $is_offshore);
                });
            })
            // ->orderBy('pharmacy_staff_id', 'asc')
            ->orderBy('date_from', 'asc')
            ->get();
        
        $monthly = [];
        $monthlyv2 = [];
        $weekly = [];
        $list = [];
        
        foreach($query as $q)
        {
            $schedule_id = $q->id;
            $employee = $q->pharmacyStaff->employee;
            $emp_id = $employee->id;

            $emp_number = $employee->initials_random_color;

            $textColor = in_array($emp_number, [1,4,8,5]) ? 'black' : 'white';

            $dailies = $q->dailies;

            if(!isset($monthly[$emp_id])) {
                $monthly[$emp_id] = [];
            }

            $date_from = $q->date_from;
            $date_to = $q->date_to;

            $temp = '';
            $x = [];
            $a = 0;

            $title = '';
            $title2 = '';
            $avatar = '';
            $is_image = false;
            if(!empty($employee->image)) {
                $is_image = true;
                $avatar = '
                    <img src="/upload/userprofile/'.$employee->image.'" width="25" height="25" class="rounded-circle" alt="" title="'.$employee->firstname.' '.$employee->lastname.'">
                ';
            } else {
                $avatar = '
                    <div class="rounded-circle employee-avatar-'.$emp_number.'-initials hr-employee" style="width: 25px !important; height: 25px !important; font-size: 10px !important;" data-id="'.$employee->id.'" title="'.$employee->firstname.' '.$employee->lastname.'">
                    '.strtoupper(substr($employee->firstname, 0, 1)).strtoupper(substr($employee->lastname, 0, 1)).'
                    </div>
                ';
            }

            $week = date('W', strtotime($date_from));
            $year = date('Y', strtotime($date_from));

            $class = 'fullcalendar-emp-schedule-x';
            if($week >= $currWeek) {
                $class = 'fullcalendar-emp-schedule-'.$emp_number;
            } else {
                $textColor = 'black';
            }

            foreach($dailies as $k => $d)
            {
                $daily_id = $d->id;
                $time_from = $d->time_from;
                $time_to = $d->time_to;
                $week_day = $d->week_day;

                $start_date = date('Y-m-d', strtotime($year . 'W' . $week . $week_day));

                $hideD = '';
                if(auth()->user()->cannot('menu_store.hr.schedules.delete')) {
                    $hideD = 'd-none';
                }
                if($is_image === true) {
                    $title = '<div class="d-flex">
                        '.$avatar.'
                        <div class="flex-grow-1 ms-1 mt-1">
                            <b class="mb-0">'.date('g:i A', strtotime($time_from)).' - '.date('g:i A', strtotime($time_to)).'</b>
                            <i class="fa fa-circle-xmark ms-1 float-end '.$hideD.'" style="cursor: pointer;" title="DELETE SCHEDULE" onclick="confirmDeleteScheduleDaily('.$d->id.')"></i>
                        </div>
                    </div>';
                    $title2 = '<div class="d-flex">
                        '.$avatar.'
                        <div class="flex-grow-1 ms-3 mt-1">
                            <b class="mb-0">'.$employee->firstname.' '.$employee->lastname.'</b>
                            <i class="fa fa-circle-xmark ms-1  float-end '.$hideD.'" style="cursor: pointer;" title="DELETE SCHEDULE" onclick="confirmDeleteScheduleDaily('.$d->id.')"></i>
                        </div>
                    </div>';
                } else {
                    $title = '<div class="d-flex">
                        '.$avatar.'
                        <b class="mb-0 ms-1 me-1 mt-1">'.date('g:i A', strtotime($time_from)).' - '.date('g:i A', strtotime($time_to)).'</b>
                        <i class="fa fa-circle-xmark ms-auto mt-1  float-end '.$hideD.'" style="cursor: pointer;" title="DELETE SCHEDULE" onclick="confirmDeleteScheduleDaily('.$d->id.')"></i>
                    </div>';
                    $title2 = '<div class="d-flex">
                        '.$avatar.'
                        <b class="mb-0 ms-3 me-1 mt-1">'.$employee->firstname.' '.$employee->lastname.'</b>
                        <i class="fa fa-circle-xmark ms-auto mt-1  float-end '.$hideD.'" style="cursor: pointer;" title="DELETE SCHEDULE" onclick="confirmDeleteScheduleDaily('.$d->id.')"></i>
                    </div>';
                }

                $monthlyv2[] = [
                    'title' => $title,
                    'start' => $start_date.'T'.$time_from,
                    'end' => $start_date.'T'.$time_to,
                    // backgroundColor: detail.backgroundColor,
                    // borderColor: detail.borderColor,
                    'textColor' => $textColor,
                    'classNames' => [$class],
                    'extendedProps' => [
                        'description' => $employee->firstname.' '.$employee->lastname.' '.date('h:i a',strtotime($time_from)).' - '.date('h:i a',strtotime($time_to)),
                        'leave_id' => null,
                        'schedule_id' => $schedule_id,
                        'daily_id' => $daily_id,
                        'is_present' => 1,
                    ]
                ];
                
                // start: uncomment this codes if the design is V1
                // if($temp != $emp_id.'~'.$time_from.$time_to) {
                //     $temp = $emp_id.'~'.$time_from.$time_to;
                //     $a++;

                //     $x[$a] = [
                //         'employee' => $employee,
                //         'leave_id' => null,
                //         'schedule_id' => $schedule_id,
                //         'daily_id' => $daily_id,
                //         'date_from' => $date_from,
                //         'date_to' => $date_to,
                //         'time_from' => $time_from,
                //         'time_to' => $time_to,
                //         'week_day_starts' => $week_day,
                //         'week_day_ends' => $week_day,
                //         'start' => $start_date,
                //         'end' => $start_date,
                //         'week' => $week,
                //         'title' => $title,
                //         'is_present' => 1,
                //         'hover_title' => $employee->firstname.' '.$employee->lastname.' '.date('h:i a',strtotime($time_from)).' - '.date('h:i a',strtotime($time_to)),
                //         // 'title' => '('.strtoupper(substr($employee->firstname, 0, 1)).strtoupper(substr($employee->lastname, 0, 1)).') '.date('H:iA',strtotime($time_from)).' - '.date('H:iA',strtotime($time_to)),
                //         'classNames' => [$class],
                //         'textColor' => $textColor
                //     ];
                // } else {
                //     $end_date = date('Y-m-d', strtotime($year . 'W' . $week . $week_day));
                //     $end_date = date('Y-m-d', strtotime($end_date.' +1 day'));
                //     $x[$a]['week_day_ends'] = $week_day;
                //     $x[$a]['end'] = $end_date;
                // }
                // end: uncomment this codes if the design is V1

                $_date = date('Y-m-d', strtotime($year . 'W' . $week . $week_day));

                $dateTimeFrom = DateTime::createFromFormat('H:i:s', $time_from);
                $dateTimeTo = DateTime::createFromFormat('H:i:s', $time_to);

                $addDay = false;

                if ($dateTimeFrom > $dateTimeTo) {
                    $addDay = true;
                }

                if($addDay === true) {
                    $_dateTo = date('Y-m-d', strtotime($_date.' +1 day'));
                    $_dateFrom = $_date;
                    $weekly[] = [
                        'hover_title' => $employee->firstname.' '.$employee->lastname.' '.date('h:i a',strtotime($time_from)).' - '.date('h:i a',strtotime($time_to)),
                        'is_present' => 1,
                        'leave_id' => null,
                        'schedule_id' => $schedule_id,
                        'daily_id' => $daily_id,
                        'start' => $_dateFrom.'T'.$time_from,
                        'end' => $_dateFrom.'T24:00',
                        'title' => $avatar,
                        'classNames' => [$class],
                        'textColor' => $textColor
                    ];
                    $weekly[] = [
                        'hover_title' => $employee->firstname.' '.$employee->lastname.' '.date('h:i a',strtotime($time_from)).' - '.date('h:i a',strtotime($time_to)),
                        'is_present' => 1,
                        'leave_id' => null,
                        'schedule_id' => $schedule_id,
                        'daily_id' => $daily_id,
                        'start' => $_dateTo.'T00:00',
                        'end' => $_dateTo.'T'.$time_to,
                        'title' => $avatar,
                        'classNames' => [$class],
                        'textColor' => $textColor
                    ];
                    $list[] = [
                        'hover_title' => $employee->firstname.' '.$employee->lastname.' '.date('h:i a',strtotime($time_from)).' - '.date('h:i a',strtotime($time_to)),
                        'is_present' => 1,
                        'leave_id' => null,
                        'schedule_id' => $schedule_id,
                        'daily_id' => $daily_id,
                        'start' => $_dateFrom.'T'.$time_from,
                        'end' => $_dateFrom.'T24:00',
                        'title' => $title2,
                        'classNames' => [],
                        'textColor' => 'black'
                    ];
                    $list[] = [
                        'hover_title' => $employee->firstname.' '.$employee->lastname.' '.date('h:i a',strtotime($time_from)).' - '.date('h:i a',strtotime($time_to)),
                        'is_present' => 1,
                        'leave_id' => null,
                        'schedule_id' => $schedule_id,
                        'daily_id' => $daily_id,
                        'start' => $_dateTo.'T00:00',
                        'end' => $_dateTo.'T'.$time_to,
                        'title' => $title2,
                        'classNames' => [],
                        'textColor' => 'black'
                    ];
                } else {
                    $weekly[] = [
                        'hover_title' => $employee->firstname.' '.$employee->lastname.' '.date('h:i a',strtotime($time_from)).' - '.date('h:i a',strtotime($time_to)),
                        'is_present' => 1,
                        'leave_id' => null,
                        'schedule_id' => $schedule_id,
                        'daily_id' => $daily_id,
                        'start' => $_date.'T'.$time_from,
                        'end' => $_date.'T'.$time_to,
                        'title' => $avatar,
                        'classNames' => [$class],
                        'textColor' => $textColor
                    ];
                    $list[] = [
                        'hover_title' => $employee->firstname.' '.$employee->lastname.' '.date('h:i a',strtotime($time_from)).' - '.date('h:i a',strtotime($time_to)),
                        'is_present' => 1,
                        'leave_id' => null,
                        'schedule_id' => $schedule_id,
                        'daily_id' => $daily_id,
                        'start' => $_date.'T'.$time_from,
                        'end' => $_date.'T'.$time_to,
                        'title' => $title2,
                        'classNames' => [],
                        'textColor' => 'black'
                    ];
                }
            }

            // start: uncomment this codes if the design is V1
            // $monthly[$emp_id][] = $x;
            // end: uncomment this codes if the design is V1

        }

        $monthlyLeaves = [];
        $weeklyLeaves = [];
        $listLeaves = [];
        $query = PharmacyStaffLeave::with('pharmacyStaff.employee')
            ->where(function  ($query) use(&$year) {
                $query->orWhere(DB::raw("YEAR(date_from)"), $year);
                $query->orWhere(DB::raw("YEAR(date_to)"), $year);
            })
            ->orderBy('pharmacy_staff_id', 'asc')
            ->orderBy('date_from', 'asc')
            ->get();

        foreach($query as $q)
        {
            $leave_id = $q->id;
            $employee = $q->pharmacyStaff->employee;
            $emp_id = $employee->id;
            $emp_number = $employee->initials_random_color;

            $title = '';
            $title2 = '';
            $avatar = '';
            $is_image = false;

            $titleStatus = '';
            $_color = '#dbab4f';
            $textColor = 'white';
            if($q->status_id == 901) {
                $titleStatus = 'Filed Leave';
                $textColor = 'red';
            }
            if($q->status_id == 902) {
                $titleStatus = 'Approved Leave';
                $_color = '#1DCA21';
            }
            if($q->status_id == 903) {
                $titleStatus = 'Rejected Leave';
                $_color = '#FF0000';
            }

            $class = 'fullcalendar-emp-leave-'.$q->status_id;

            $avatar = '';
            if(!empty($employee->image)) {
                $is_image = true;
                $avatar = '<img src="/upload/userprofile/'.$employee->image.'" width="25" height="25" class="rounded-circle" alt="">';
                $title = '
                    <div class="d-flex">
                        '.$avatar.'
                        <div class="flex-grow-1 ms-2 mt-1">
                            <b class="mb-0">'.$titleStatus.'</b>
                        </div>
                    </div>
                ';
                $title2 = '
                    <div class="d-flex">
                        '.$avatar.'
                        <div class="flex-grow-1 ms-3 mt-1">
                            <b class="mb-0">'.$employee->firstname.' '.$employee->lastname.' <span style="color: '.$_color.'">'.$titleStatus.'</span></b>
                        </div>
                    </div>
                ';
            } else {
                $avatar = '<div class="rounded-circle employee-avatar-'.$emp_number.'-initials hr-employee" style="width: 25px !important; height: 25px !important; font-size: 10px !important;" data-id="'.$employee->id.'">
                            '.strtoupper(substr($employee->firstname, 0, 1)).strtoupper(substr($employee->lastname, 0, 1)).'
                        </div>';
                $title = '
                    <div class="d-flex">
                        '.$avatar.'
                        <b class="mb-0 ms-2 mt-1">'.$titleStatus.'</b>
                    </div>
                ';
                $title2 = '
                    <div class="d-flex">
                        '.$avatar.'
                        <b class="mb-0 ms-3 mt-1">'.$employee->firstname.' '.$employee->lastname.' <span style="color: '.$_color.'">'.$titleStatus.'</span></b>
                    </div>
                ';
            }

            if(!isset($monthly[$emp_id])) {
                $monthlyLeaves[$emp_id] = [];
            }

            $date_to = date('Y-m-d', strtotime($q->date_to . ' +1 day'));

            $monthlyLeaves[$emp_id]['details'][] = [
                'leave_id' => $leave_id,
                'schedule_id' => null,
                'daily_id' => null,
                'is_present' => 0,
                'employee' => $employee,
                'date_from' => $q->date_from,
                'date_to' => $q->date_to,
                'start' => $q->date_from,
                'end' => $date_to,
                'title' => $title,
                'hover_title' => $employee->firstname.' '.$employee->lastname.' '.$titleStatus,
                'classNames' => [$class],
                'textColor' => $textColor
            ];

            $weeklyLeaves[] = [
                'hover_title' => $employee->firstname.' '.$employee->lastname.' '.$titleStatus,
                'is_present' => 0,
                'leave_id' => $leave_id,
                'schedule_id' => null,
                'daily_id' => null,
                'start' => $q->date_from,
                'end' => $date_to,
                'title' => $title,
                'classNames' => [$class],
                'textColor' => $textColor
            ];
            $listLeaves[] = [
                'hover_title' => $employee->firstname.' '.$employee->lastname.' '.$titleStatus,
                'is_present' => 0,
                'leave_id' => $leave_id,
                'schedule_id' => null,
                'daily_id' => null,
                'start' => $q->date_from,
                'end' => $date_to,
                'title' => $title2,
                'classNames' => [],
                'textColor' => $_color
            ];
        }

        $weekly = array_merge($weekly, $weeklyLeaves);
        $list = array_merge($list, $listLeaves);

        $data = [
            'monthly' => $monthly,
            'monthlyv2' => $monthlyv2,
            'weekly' => $weekly,
            'daily' => [],//$daily,
            'list' => $list,
            'leaves' => $monthlyLeaves,
        ];
        
        if($request->ajax()) {
            return json_encode([
                'data'=> $data,
                'status'=>'success',
                'message'=>'Successfully retrieved'
            ]);
        }
        return $data;

    }

    public function exportOnshoreByMonthYear($id, $date)
    {
        $monthNumber = date("n", strtotime($date));
        $month = date("F", strtotime($date));
        $year = date("Y", strtotime($date));

        $scheduleCustomArr = [];
        // $schedules = PharmacyStaffSchedule::with('dailies')->whereMonth($monthNumber)->whereYear($year)->get();
        $schedules = PharmacyStaffSchedule::with([
                'pharmacyStaff' => function($query) use ($id) {
                    $query->where('pharmacy_store_id', $id);
                },
                'dailies',
                'pharmacyStaff.employee'
            ])
            ->whereMonth('date_from',$monthNumber)->whereYear('date_from',$year)
            ->whereHas('pharmacyStaff', function ($query) use ($id){
                $query->where('pharmacy_store_id', $id);
            })
            ->orderBy('pharmacy_staff_id', 'asc')
            ->orderBy('date_from', 'asc')
            ->get();

        foreach($schedules as $schedule) {
            $staff = $schedule->pharmacyStaff ?? [];
            $employee = isset($staff->employee->id) ? $staff->employee : null;

            if(!empty($employee)) {
                $fullname = $employee->firstname.' '.$employee->lastname;

                $dailies = $schedule->dailies ?? [];

                $week = date("W", strtotime($schedule->date_from));
                    
                foreach($dailies as $daily) {
                    $week_date = date('Y-m-d', strtotime($year . 'W' . $week . $daily->week_day));
                    $formatted_week_date = date('F d, Y', strtotime($week_date));
                    $time_from = date('h:i A', strtotime($daily->time_from));
                    $time_to = date('h:i A', strtotime($daily->time_to));

                    $week_month = date('m', strtotime($week_date));

                    if($week_month != $monthNumber) {
                        continue;
                    }

                    $carbonDate = Carbon::parse($week_date);
                    $dayOfWeek = $carbonDate->format('l');

                    $scheduleCustomArr[] = [
                        $fullname,
                        $formatted_week_date,
                        $time_from,
                        $time_to,
                        $dayOfWeek,
                    ];
                }
  
            }
        }

        // dd($scheduleCustomArr);

        return Excel::download(new PharmacyStaffScheduleCustomExport($scheduleCustomArr), 'In Pharmacy Schedule - '.$month.' '.$year.'.xlsx');
    }

    public function delete(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                DB::beginTransaction();

                $daily = PharmacyStaffScheduleDaily::findOrFail($request->id);

                if(isset($daily->id)) {
                    $daily->delete();
                }

                DB::commit();

                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in ScheduleController.delete.'
                ]);
            }
        }
    }


}
