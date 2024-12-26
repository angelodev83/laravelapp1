<?php
namespace App\Tasks;

use App\Models\Employee;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\Interfaces\ITaskRepository;
use App\Models\StoreDocument;
use DateTime;
use DateTimeZone;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

ini_set('max_execution_time', '3600');

class SetTask
{
    protected $month;
    protected $year;
    protected $month_int;
    protected $day;
    protected $week;
    protected $weekDay;
    protected $phTimeZone;
    private $aws_s3_path;

    private ITaskRepository $taskRepository;

    public function __construct(ITaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->aws_s3_path = env('AWS_S3_PATH');

        $this->month = date('F'); // Initialize month with current month's full name
        $this->year = date('Y'); // Initialize year with current year
        $this->month_int = date('m');
        $this->day = date('d');
        $this->week = date('W'); // Changes every Monday
        $this->weekDay = date('N'); // 1 = Monday, 2 = Tuesday, 3 = Wednesday, 4 = Thursday, 5 = Friday, 6 = Sat, 7 = Sun
        $this->phTimeZone = 'Asia/Manila';
    }

    // public function task10thofMonth($subject, $store, $status_id, $tag_id)
    // {
    //     $userEmp = $this->monthlyAssigneeInventoryReconciliation();

    //     $check = DB::table('task_tag')
    //         ->where('name', 'monthly')
    //         ->where('month', $this->month_int)
    //         ->where('year', $this->year)
    //         ->where('tag_id',$tag_id)
    //         ->first();
    //     if(!isset($check->id)) {
    //         $task = new Task();
    //         $task->subject = $subject. $this->month . ' ' . $this->year;
    //         $task->pharmacy_store_id = $store;
    //         $task->user_id = $userEmp->user_id;
    //         $task->assigned_to_employee_id = $userEmp->emp_id;
    //         $task->status_id = $status_id;
    //         $task->save();

    //         $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $task->status, null);
    
    //         DB::table('task_tag')->insert([
    //             'name' => 'monthly',
    //             'task_id' => $task->id,
    //             'tag_id' => $tag_id,
    //             'month' => $this->month_int,
    //             'year' => $this->year,
    //             'created_at' => Carbon::now()
    //         ]);
    
    //         return $task;
    //     }
    //     return [];
    // }

    public function task01OfTheMonth($subject, $store, $status_id, $tag_id)
    {
        $userEmp = $this->monthlyAssigneeInventoryReconciliation($tag_id);
        
        $check = DB::table('task_tag')
            ->where('name', 'monthly')
            ->where('month', $this->month_int)
            ->where('year', $this->year)
            ->where('tag_id',$tag_id)
            ->first();
            
        if(!isset($check->id)) {
            $task = new Task();
            $task->subject = $subject. $this->month . ' ' . $this->year;
            $task->pharmacy_store_id = $store;
            $task->user_id = 1;
            $task->assigned_to_employee_id = $userEmp->emp_id;
            $task->due_date = date('Y-m').'-10';
            $task->status_id = $status_id;
            $task->is_auto = 1;
            $task->save();

            if(isset($task->id)) {
                $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $task->status, null);
            }
            
            if($tag_id == 12){
                if($task->id){

                    $absolute_path = str_replace('\\', '/', public_path('upload/taskDocuments/Retail_Drug_Outlet_Self-Inspection_Form.pdf'));
                    $file = new File($absolute_path);

                    $pathUpload = 'upload/stores/'.$store.'/task/'.$task->id;

                    $document = new StoreDocument();
                    $document->user_id = $userEmp->user_id;
                    $document->parent_id = $task->id;
                    $document->category = 'task';
                    $document->ext = $file->getExtension();

                    @unlink(public_path($pathUpload.'/'.$document->path));
                    $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME).'imported_'.date('Ymd').'-1.'.$file->getExtension();
                    $file->move(public_path($pathUpload), $fileName);
                    $document->path = '/'.$pathUpload.'/'.$fileName;
                    $path = '/'.$pathUpload.'/'.$fileName;

                    $save = $document->save(); 

                    //s3
                    // $document = new StoreDocument();
                    // $document->user_id = $userEmp->user_id;
                    // $document->parent_id = $task->id;
                    // $document->category = 'task';
                    
                    // $document->name = $file->getFilename();
                    // $document->ext = $file->getExtension();
                    // $document->mime_type = $file->getMimeType();
                    // $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
                    // $document->size = $file->getSize()/1024;
                    // $document->size_type = 'KB';

                    // $fileName = $file->getFilename();
                    // $date = date('YmdHis');
                    // $path = "/$this->aws_s3_path/stores/$store/tasks/$task->id/$date/$fileName";
                    // $document->path = $path;

                    // $save = $document->save();

                    // if($save) {
                    //     $pathfile = $document->path;
                    //     Storage::disk('s3')->put($pathfile, file_get_contents($file));
                    //     // $s3Url = Storage::disk('s3')->temporaryUrl(
                    //     //     $pathfile,
                    //     //     now()->addMinutes(30)
                    //     // );

                    //     // $array = $document->toArray();
                    //     // $array['url'] = $s3Url;
                    //     // $attachments[] = $array;
                    // }
                }
            }

            DB::table('task_tag')->insert([
                'name' => 'monthly',
                'task_id' => $task->id,
                'tag_id' => $tag_id,
                'month' => $this->month_int,
                'year' => $this->year,
                'created_at' => Carbon::now()
            ]);
    
            return $task;
        }
        return [];
    }

    public function daily1201($subject, $store, $status_id, $tag_id)
    {
        $assignee = $this->dailyAssigneeInventoryReconciliation();
        $userEmp = $this->defaultSystemUserEmployee();

        $check = DB::table('task_tag')
            ->where('name', 'daily')
            ->where('day', $this->day)
            ->where('month', $this->month_int)
            ->where('year', $this->year)
            ->where('tag_id',$tag_id)
            ->first();
        if(!isset($check->id)) {
            $task = new Task();
            $task->subject = $subject. $this->month . ' ' . $this->day . ', ' .$this->year;
            $task->pharmacy_store_id = $store;
            $task->user_id = $userEmp->user_id;
            $task->assigned_to_employee_id = $assignee->emp_id;
            $task->status_id = $status_id;
            $task->is_auto = 1;
            $task->save();

            if(isset($task->id)) {
                $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $task->status, null);
            }
    
            DB::table('task_tag')->insert([
                'name' => 'daily',
                'task_id' => $task->id,
                'tag_id' => $tag_id,
                'day' => $this->day,
                'month' => $this->month_int,
                'year' => $this->year,
                'created_at' => Carbon::now()
            ]);
    
            return $task;
        }
        return [];
    }

    public function weeklyOnMonday($subject, $store, $status_id, $tag_id)
    {
        $assignee = $this->dailyAssigneeInventoryReconciliation();
        $userEmp = $this->defaultSystemUserEmployee();

        $check = DB::table('task_tag')
            ->where('name', 'weekly')
            ->where('week', $this->week)
            ->where('year', $this->year)
            ->where('tag_id',$tag_id)
            ->first();
        if(!isset($check->id)) {
            $task = new Task();
            $mondayDate = date('Y-m-d', strtotime($this->year . 'W' . $this->week . '1'));
            $from = date('F d', strtotime($mondayDate));
            $to = date('F d, Y', strtotime($from . ' + 6 days'));
            $task->subject = $subject . 'Week ('.$from.' to '.$to.')';
            $task->pharmacy_store_id = $store;
            $task->user_id = $userEmp->user_id;
            $task->assigned_to_employee_id = $assignee->emp_id;
            $task->status_id = $status_id;
            $task->is_auto = 1;
            $task->save();

            if(isset($task->id)) {
                $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $task->status, null);
            }
    
            DB::table('task_tag')->insert([
                'name' => 'weekly',
                'task_id' => $task->id,
                'tag_id' => $tag_id,
                'day' => $this->day,
                'week' => $this->week,
                'month' => $this->month_int,
                'year' => $this->year,
                'created_at' => Carbon::now()
            ]);
    
            return $task;
        }
        return [];
    }

    public function daily1201CustomReminder()
    {
        $day = $this->weekDay;
        $employee = $this->dailyAssigneeInventoryReconciliation();
        switch($day) {
            case 1: // Monday
                break;
            case 2: // Tuesday
                break;
            case 3: // Wednesday
                break;
            case 4: // Thursday
                // return $this->taskRepository->createArAgingReportTask(1, $employee->emp_id);
                break;
            case 5: // Friday
                break;
            case 6: // Saturday
                break;
            case 7: // Sunday
                break;
        }
        return false;
    }

    public function convertToTimeZone($dateString = null, $timezone = 'America/Los_Angeles') {
        if(empty($dateString)) {
            $dateString = date('Y-m-d H:i:s');
        }
        // Create a DateTime object from the provided date string
        $date = new DateTime($dateString);
        
        // Set the timezone to Philippine Time (PHT)
        $date->setTimezone(new DateTimeZone($timezone));
        
        // Return the date in the desired format
        return $date->format('Y-m-d H:i:s');
    }

    public function daily1700CustomReminder() // 1am PH == 5pm utc
    {
        // $assignee = $this->assignee1700Tasks();
        // $userEmployee = $this->defaultSystemUserEmployee();
        // $pharmacy_store_id = 1;

        // $date = $this->convertToTimeZone();
        // $weekDay = date('N', strtotime($date));
        // $day = date('d', strtotime($date));

        // $responseArr = [];

        // $taskList = [
        //     'New Patient Report' => [
        //         'daily' => false,
        //         'weekly' => [1], // weekday 1-7
        //         'monthly' => [1], // datedays 1-30/31
        //     ],
        //     'Gross Sales Report' => [
        //         'daily' => true,
        //         'weekly' => [1], // weekday 1-7
        //         'monthly' => [5], // datedays 1-30/31
        //     ],
        //     'Completed Sales Report' => [
        //         'daily' => true,
        //         'weekly' => [1], // weekday 1-7
        //         'monthly' => [5], // datedays 1-30/31
        //     ],
        //     'Reversal Summary' => [
        //         'daily' => true,
        //         'weekly' => [1], // weekday 1-7
        //         'monthly' => [5], // datedays 1-30/31
        //     ],
        //     'Transferred In/Out Report' => [
        //         'daily' => false,
        //         'weekly' => [1], // weekday 1-7
        //         'monthly' => [], // datedays 1-30/31
        //     ],
        //     'Cash Register Report' => [
        //         'daily' => true,
        //         'weekly' => [1], // weekday 1-7
        //         'monthly' => [5], // datedays 1-30/31
        //     ],
        //     'RTS Report' => [
        //         'daily' => false,
        //         'weekly' => [], // weekday 1-7
        //         'monthly' => [1], // datedays 1-30/31
        //     ],
        //     'Que Status Report' => [
        //         'daily' => false,
        //         'weekly' => [5], // weekday 1-7
        //         'monthly' => [1], // datedays 1-30/31
        //     ],
        //     'Weekly COGS Inventory Report' => [
        //         'daily' => false,
        //         'weekly' => [5], // weekday 1-7
        //         'monthly' => [], // datedays 1-30/31
        //     ],
        // ];

        // foreach($taskList as $subject => $schedule) {
        //     $daily = $schedule['daily'];
        //     $weekly = $schedule['weekly'];
        //     $monthly = $schedule['monthly'];

            
        //     if($daily === true) {
        //         $newSubject = 'Daily '.$subject. ' for this Day - '.date('M d, Y', strtotime($date));
        //         $task = $this->taskRepository->createNewTask($pharmacy_store_id, $assignee->emp_id, $userEmployee->user_id, $newSubject);
        //         array_push($responseArr, $task->subject);
        //     }

        //     if(in_array($weekDay, $weekly)) {
        //         $newSubject = 'Weekly '.$subject. ' for this Day - '.date('M d, Y', strtotime($date));
        //         $task = $this->taskRepository->createNewTask($pharmacy_store_id, $assignee->emp_id, $userEmployee->user_id, $newSubject);
        //         array_push($responseArr, $task->subject);
        //     }

        //     if(in_array($day, $monthly)) {
        //         $newSubject = 'Monthly '.$subject. ' for this Month - '.date('M Y', strtotime($date));
        //         $task = $this->taskRepository->createNewTask($pharmacy_store_id, $assignee->emp_id, $userEmployee->user_id, $newSubject);
        //         array_push($responseArr, $task->subject);
        //     }
        // }
        
        // return $responseArr;
    }

    private function createWatchers($task_id, $watcherUserEmails)
    {
        $employeeIds = Employee::join('users', 'employees.user_id', '=', 'users.id')
                        ->select('employees.id')
                        ->whereIn('users.email', $watcherUserEmails)
                        ->pluck('employees.id');
        
        foreach($employeeIds as $employee_id)
        {
            DB::table('task_watchers')->insertOrIgnore([
                'task_id' => $task_id,
                'employee_id' => $employee_id
            ]);
        }
    }

    public function daily0800CustomReminder($email, $taskList, $timezone = 'America/Los_Angeles', $watcherUserEmails = []) // 8am utc to 1am pst
    {
        $assignee = $this->assigneeViaEmail($email);
        $userEmployee = $this->defaultSystemUserEmployee();
        $pharmacy_store_id = 1;

        $date = $this->convertToTimeZone($timezone);
        $year = date('Y', strtotime($date));
        $week = date('W', strtotime($date));
        $weekDay = date('N', strtotime($date));
        $day = date('d', strtotime($date));
        $month = date('m', strtotime($date));

        $tagDay = $day;

        $responseArr = [];
        $responseArrFail = [];

        foreach($taskList as $subject => $schedule) {
            $daily = $schedule['daily'];
            $weekly = $schedule['weekly'];
            $monthly = $schedule['monthly'];
            $yearly = $schedule['yearly'];
            $weeklyDetails = $schedule['weeklyDetails'] ?? [];

            $tag_id = null;
            
            // DAILY TASKS
            if($daily === true) {
                $newSubject = $subject. ' - '.date('F d, Y', strtotime($date));
                $task = $this->taskRepository->createNewTask($pharmacy_store_id, $assignee->emp_id, $userEmployee->user_id, $newSubject, $date);
                if(!empty($watcherUserEmails) && isset($task->id)) {
                    $this->createWatchers($task->id, $watcherUserEmails);
                }
                if(isset($task->id)) {
                    $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $task->status, null);
                    array_push($responseArr, $task->subject);
                } else {
                    array_push($responseArrFail, $newSubject);
                }
            }

            // WEEKLY TASKS
            $weekRange = '';
            $prefix = '';
            if(in_array($weekDay, $weekly)) {
                $startDate = date('Y-m-d', strtotime($year . 'W' . $week . '1'));
                if(isset($weeklyDetails['day'])) {
                    $wDay = $weeklyDetails['day'];
                    if(!empty($wDay)) {
                        if($weeklyDetails['current'] == true) {
                            $startDate = date('Y-m-d', strtotime($year . 'W' . $week . $wDay));
                        }
                        if($weeklyDetails['previous'] == true) {
                            $startDate = date('Y-m-d', strtotime($year . 'W' . ($week-1) . $wDay));
                        }
                        
                        $tagDay = date('d', strtotime($startDate));

                        $from = date('F d', strtotime($startDate));
                        $to = date('F d, Y', strtotime($from . ' + 6 days'));
                        $weekRange = ' ('.$from.' - '.$to.')';
                    }
                }
                if(isset($weeklyDetails['hasPrefix'])) {
                    if($weeklyDetails['hasPrefix'] === true) {
                        $prefix = 'Weekly ';
                    }
                }

                if(isset($weeklyDetails['tag_id'])) {
                    $tag_id = $weeklyDetails['tag_id'];
                    if(!empty($tag_id)) {
                        $check = DB::table('task_tag')
                            ->where('name', 'weekly')
                            ->where('week', $week)
                            ->where('year', $year)
                            ->where('tag_id',$tag_id)
                            ->first();
                        if(isset($check->id)) {
                           $tag_id = null; 
                        }
                    }
                }

                $due_date = null;
                if(isset($weeklyDetails['due_date'])) {
                    $due_date = $weeklyDetails['due_date'];
                }

                $description = null;
                if(isset($weeklyDetails['description'])) {
                    $description = $weeklyDetails['description'];
                }

                $weeklyWatcherUserEmailsArr = [];
                if(isset($weeklyDetails['watchers'])) {
                    $weeklyWatcherUserEmailsArr = $weeklyDetails['watchers'];
                }

                $suffix = '';
                if(isset($weeklyDetails['suffix'])) {
                    $suffix = $weeklyDetails['suffix'];
                } else {
                    $suffix = $weekRange;
                }
                
                $newSubject = $prefix.$subject. $suffix;
                $task = $this->taskRepository->createNewTask($pharmacy_store_id, $assignee->emp_id, $userEmployee->user_id, $newSubject, $due_date, $description);
                if(!empty($watcherUserEmails) && isset($task->id)) {
                    $this->createWatchers($task->id, $watcherUserEmails);
                }

                if(!empty($weeklyWatcherUserEmailsArr)) {
                    $this->createWatchers($task->id, $weeklyWatcherUserEmailsArr);
                }

                if(!empty($tag_id)) {
                    DB::table('task_tag')->insert([
                        'name' => 'weekly',
                        'task_id' => $task->id,
                        'tag_id' => $tag_id,
                        'day' => $tagDay,
                        'week' => $week,
                        'month' => $month,
                        'year' => $year,
                        'created_at' => Carbon::now()
                    ]);
                }

                if(isset($task->id)) {
                    $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $task->status, null);
                    array_push($responseArr, $task->subject);
                } else {
                    array_push($responseArrFail, $newSubject);
                }
            }

            // MONTHLY TASKS
            if(in_array($day, $monthly)) {
                $newSubject = $subject. ' - '.date('M Y', strtotime($date));
                $dateTime = new DateTime($date);
                $dateTime->modify('+9 days');
                $month_due_date = $dateTime->format('Y-m-d');
                $task = $this->taskRepository->createNewTask($pharmacy_store_id, $assignee->emp_id, $userEmployee->user_id, $newSubject, $month_due_date);
                if(!empty($watcherUserEmails) && isset($task->id)) {
                    $this->createWatchers($task->id, $watcherUserEmails);
                }
                if(isset($task->id)) {
                    $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $task->status, null);
                    array_push($responseArr, $task->subject);
                } else {
                    array_push($responseArrFail, $newSubject);
                }
            }

            // YEARLY TASKS
            if(in_array($day, $monthly) && in_array($month, $yearly)) {
                $newSubject = $subject. ' for this Year - '.date('F d, Y', strtotime($date));
                $task = $this->taskRepository->createNewTask($pharmacy_store_id, $assignee->emp_id, $userEmployee->user_id, $newSubject);
                if(!empty($watcherUserEmails)  && isset($task->id)) {
                    $this->createWatchers($task->id, $watcherUserEmails);
                }
                if(isset($task->id)) {
                    $this->taskRepository->sendNotificationStatusChanged($task->assignedTo, $task, $task->status, null);
                    array_push($responseArr, $task->subject);
                } else {
                    array_push($responseArrFail, $newSubject);
                }
            }
        }
        
        return ['success' => $responseArr, 'fail' => $responseArrFail];
    }

    public function daily1700CustomAssineeTask($email) // 1am PH == 5pm utc
    {
        // $assignee = $this->assigneeViaEmail($email);
        // $userEmployee = $this->defaultSystemUserEmployee();
        // $pharmacy_store_id = 1;

        // $date = $this->convertToTimeZone();
        // $weekDay = date('N', strtotime($date));
        // $day = date('d', strtotime($date));
        // $thisMonth = date('m', strtotime($date));

        // $responseArr = [];

        $taskList = [
            'Partial Fill Report' => [
                'daily' => true,
                'weekly' => [], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'month' => [],
            ],
            'IOU Report' => [
                'daily' => true,
                'weekly' => [], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'month' => [],
            ],
            'Future Fill Ordering' => [
                'daily' => false,
                'weekly' => [2], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'month' => [],
            ],
            'Shipping Data Profitability Matrix' => [
                'daily' => false,
                'weekly' => [], // weekday 1-7
                'monthly' => [1], // datedays 1-30/31
                'month' => [],
            ],
            'Inventory Year End Practices' => [
                'daily' => false,
                'weekly' => [], // weekday 1-7
                'monthly' => [20], // datedays 1-30/31
                'month' => [12], // month 1-12
            ],
        ];

        // foreach($taskList as $subject => $schedule) {
        //     $daily = $schedule['daily'];
        //     $weekly = $schedule['weekly'];
        //     $monthly = $schedule['monthly'];
        //     $month = $schedule['month'];
            
        //     if($daily === true) {
        //         $newSubject = 'Daily '.$subject. ' for this Day - '.date('M d, Y', strtotime($date));
        //         $task = $this->taskRepository->createNewTask($pharmacy_store_id, $assignee->emp_id, $userEmployee->user_id, $newSubject);
        //         array_push($responseArr, $task->subject);
        //     }

        //     if(in_array($weekDay, $weekly)) {
        //         $newSubject = 'Weekly '.$subject. ' for this Day - '.date('M d, Y', strtotime($date));
        //         $task = $this->taskRepository->createNewTask($pharmacy_store_id, $assignee->emp_id, $userEmployee->user_id, $newSubject);
        //         array_push($responseArr, $task->subject);
        //     }

        //     if(in_array($day, $monthly)) {
        //         $newSubject = 'Monthly '.$subject. ' for this Month - '.date('M Y', strtotime($date));
        //         $task = $this->taskRepository->createNewTask($pharmacy_store_id, $assignee->emp_id, $userEmployee->user_id, $newSubject);
        //         array_push($responseArr, $task->subject);
        //     }

        //     if(in_array($day, $monthly) && in_array($thisMonth, $month)) {
        //         $newSubject = $subject. ' for this Year - '.date('M d, Y', strtotime($date));
        //         $task = $this->taskRepository->createNewTask($pharmacy_store_id, $assignee->emp_id, $userEmployee->user_id, $newSubject);
        //         array_push($responseArr, $task->subject);
        //     }
        // }
        
        // return $responseArr;
    }

    public function defaultSystemUserEmployee()
    {
        $employees = Employee::join('users', 'employees.user_id', '=', 'users.id')
                      ->select('employees.id AS emp_id', 'users.id AS user_id')
                      ->where('users.email', 'superadmin@mgmt88.com')
                      ->first();

        return $employees;
    }

    public function dailyAssigneeInventoryReconciliation()
    {
        $employees = Employee::join('users', 'employees.user_id', '=', 'users.id')
                      ->select('employees.id AS emp_id', 'users.id AS user_id')
                      ->where('users.email', 'erwin@mgmt88.com')
                      ->first();

        return $employees;
    }

    public function assignee1700Tasks()
    {
        $employees = Employee::join('users', 'employees.user_id', '=', 'users.id')
                      ->select('employees.id AS emp_id', 'users.id AS user_id')
                      ->where('users.email', 'zandraline@mgmt88.com')
                      ->first();

        return $employees;
    }

    public function monthlyAssigneeInventoryReconciliation($tag_id)
    {

        switch ($tag_id) {
            case 12:
                 $emails =['jagee@ctclusi.org'];
                break;
            case 8:
            case 9:
                $emails = ['asalido@ctclusi.org'];
                break;
            default:
                 $emails =['jagee@ctclusi.org', 'tbottoroff@ctclusi.org'];
                break;
        }
        $currentMonth = date('n');

        if(count($emails) > 1) {
            $emailIndex = ($currentMonth - 1) % count($emails);
            $selectedEmail = $emails[$emailIndex];
        } else {
            $selectedEmail = $emails[0];
        }
        

        $employees = Employee::join('users', 'employees.user_id', '=', 'users.id')
                      ->select('employees.id AS emp_id', 'users.id AS user_id')
                      ->where('users.email', $selectedEmail)
                      ->first();

        return $employees;
    }

    public function assigneeViaEmail($email)
    {
        $employees = Employee::join('users', 'employees.user_id', '=', 'users.id')
                      ->select('employees.id AS emp_id', 'users.id AS user_id')
                      ->where('users.email', $email)
                      ->first();

        return $employees;
    }
}