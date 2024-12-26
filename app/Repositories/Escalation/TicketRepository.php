<?php

namespace App\Repositories\Escalation;

use Illuminate\Http\Request;

use App\Interfaces\ITicketRepository;
use App\Repositories\StoreDocumentRepository;
use App\Models\User;
use App\Models\Ticket;
use App\Models\StoreDocument;
use App\Models\SupportEmployee;
use App\Http\Utils\FileIconUtil;
use App\Jobs\NotifyTicketToAssignee;
use App\Models\Employee;
use App\Models\StoreStatus;
use App\Models\TicketComment;
use App\Models\TicketCommentDocument;
use App\Models\TicketStatusLog;
use Carbon\Carbon;
use DateTime;
use Exception;
use File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TicketRepository extends StoreDocumentRepository implements ITicketRepository
{
    use FileIconUtil;

    private $aws_s3_path;
    private $ticket;
    private $document;
    protected $dataTable = [];
    protected $documentDataTable = [];

    public function __construct(Ticket $ticket, StoreDocument $document)
    {
        $this->ticket = $ticket;
        $this->document = $document;
        $this->aws_s3_path = env('AWS_S3_PATH');
    }

    public function search($request)
    {
        $query = $this->ticket;
        return $query->get();
    }

    public function getDataTable() : array
    {
        return $this->dataTable;
    }

    public function setDataTable($request)
    {
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? 0;
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        $user = Auth::user();

        $query = $this->ticket->with('user.employee'
            , 'documents'
            , 'assignedTo.supportHeadCategory'
            , 'status', 'comments.user.employee'
            , 'comments.documents'
            , 'currentStatusLog'
            , 'statusLogs.status'
            , 'watchers'
        );
        // $query = $query->with(['comments' => function ($query) {
        //     // Use the custom accessor to format the created_at attribute
        //     $query->get()->transform(function ($comment) {
        //         $comment->formatted_created_at = $comment->getFormattedCreatedAtAttribute();
        //         return $comment;
        //     });
        // }]);
        // $query = $query->where('assigned_to_employee_id', $user->employee->id)
        //         ->orWhere('user_id', $user->id)
        //         ->orWhere(function ($q) use ($user) {
        //            $q->whereExists(function ($query) use ($user) {
        //                $query->select(DB::raw(1))
        //                      ->from('support_employees')
        //                      ->join('support_categories', 'support_categories.id', '=', 'support_employees.category_id')
        //                      ->whereColumn('support_categories.id', 'tickets.support_category_id')
        //                      ->where('support_employees.employee_id', $user->employee->id)
        //                      ->where('support_employees.is_head_support', 1);
        //            });
        //        }); 

    
        $origCheckRole = !$user->hasRole('super-admin') && $user->cannot('menu_store.escalation.tickets.view_all');
        $checkRole = !$user->hasRole('super-admin') && $user->cannot('menu_store.escalation.tickets.view_all');
        if($request->has('filter_mine')) {
            $filter_mine = $request->filter_mine;

            if($filter_mine === true || $filter_mine === 'true') {
                $checkRole = true;
            } else {
                $checkRole = $origCheckRole;
            }
        }
        
        if($checkRole){
            $query = $query->where(function($query) use($user){
                $query->orWhere('assigned_to_employee_id', $user->employee->id);
                $query->orWhere('user_id', $user->id);
                $query->orWhere(function ($q) use ($user) {
                    $q->whereExists(function ($query) use ($user) {
                        $query->select(DB::raw(1))
                                ->from('support_employees')
                                ->join('support_categories', 'support_categories.id', '=', 'support_employees.category_id')
                                ->whereColumn('support_categories.id', 'tickets.support_category_id')
                                ->where('support_employees.employee_id', $user->employee->id)
                                ->where('support_employees.is_head_support', 1);
                    });
                });  
            });
        }
                


        // Search //input all searchable fields
        $search = $request->search;
        $query = $query->where(function($query) use ($search){ 
            $query->orWhere('subject', 'like', "%".$search."%");   
            $query->orWhere('code', 'like', "%".$search."%");   
        });

        if($request->has('pharmacy_store_id')) {
            $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
        }

        if($request->has('is_archived')) {
            $query = $query->where('is_archived', $request->is_archived);
        }
        
        $orderByCol = $request->columns[$request->order[0]['column']]['name'];
        
        $query = $query->orderBy($orderByCol, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $newData = [];

        $hideU = 'hidden';
        $hideSU = 'hidden';
        $hideD = 'hidden';
        $hideAll = 'hidden';
        $menuClass = 'menu_permission_update_semi';
        $hideUBoth = 'hidden';
        if(auth()->user()->can('menu_store.escalation.tickets.semi_update'))
        {
            $hideSU = ''; $hideAll = '';
            $menuClass = 'menu_permission_update_semi';
            $hideUBoth = '';
        }
        if(auth()->user()->can('menu_store.escalation.tickets.update'))
        {
            $hideU = ''; $hideAll = '';
            $menuClass = 'menu_permission_update_all';
            $hideUBoth = '';
        }
        if(auth()->user()->can('menu_store.escalation.tickets.delete'))
        {
            $hideD = ''; $hideAll = '';
        }
        $originalHideU = $hideU;
        $originalHideSU = $hideSU;
        $originalHideAll = $hideAll;
        $originalMenuClass = $menuClass;

        $user = User::with('employee.supportHeadCategory')->findOrFail(auth()->user()->id);

        foreach ($data as $value) {
            $empName = isset($value->user->employee) ? $value->user->employee->getFullName() : "NA";
            $documents = isset($value->ticketDocuments) ? $value->ticketDocuments->all() : [];
            $comments = isset($value->comments) ? $value->comments->all() : [];
            $assignedTo = isset($value->assignedTo) ? $value->assignedTo : null;
            $assignedName = !empty($assignedTo) ? $assignedTo->getFullName() : "NA";
            $status = isset($value->status) ? $value->status : [];
            $priority = isset($value->priority) ? $value->priority : [];
            $emp = $value->user->employee;

            $watchers = isset($value->watchers) ? $value->watchers->all() : [];

            $currentStatusLog = isset($value->currentStatusLog) ? $value->currentStatusLog : null;
            $statusLogs = isset($value->statusLogs) ? $value->statusLogs->all() : [];

            $isAssignedToHeadCategory = false;
            if(isset($user->employee->supportHeadCategory->category_id)) {
                $isAssignedToHeadCategory = $user->employee->supportHeadCategory->category_id == $value->support_category_id ? true:false;
            }


            $assignedToInitials = 'NA';
            $assignedToImage  = '';
            $assignedToInitialsRandomColor = 1;
            if(isset($assignedTo->id)) {
                $assignedToInitials = strtoupper(substr($assignedTo->firstname, 0, 1)).strtoupper(substr($assignedTo->lastname, 0, 1));
                $assignedToImage = !empty($assignedTo->image) ? $assignedTo->image : '';
                $assignedToInitialsRandomColor = $assignedTo->initials_random_color;
            }
            
            $avatar = $assignedName;
            if(!empty($assignedToImage)) {
                $avatar = '
                    <div class="d-flex">
                        <img src="/upload/userprofile/'.$assignedToImage.'" width="32" height="32" class="rounded-circle" alt="">
                        <div class="flex-grow-1 ms-3 mt-2">
                            <p class="font-weight-bold mb-0">'.$assignedName.'</p>
                        </div>
                    </div>
                ';
            } else {
                $avatar = '
                    <div class="d-flex">
                        <div class="employee-avatar-'.$assignedToInitialsRandomColor.'-initials hr-employee">
                        '.$assignedToInitials.'
                        </div>
                        <p class="font-weight-bold mb-0 ms-3 mt-2">'.$assignedName.'</p>
                    </div>
                ';
            }

            $empAvatar = '';
            if(!empty($emp->image)) {
                $empAvatar = '
                    <div class="d-flex">
                        <img src="/upload/userprofile/'.$emp->image.'" width="32" height="32" class="rounded-circle" alt="">
                        <div class="flex-grow-1 ms-3 mt-2">
                            <p class="font-weight-bold mb-0">'.$empName.'</p>
                        </div>
                    </div>
                ';
            } else {
                $empAvatar = '
                    <div class="d-flex">
                        <div class="employee-avatar-'.$emp->initials_random_color.'-initials hr-employee" data-id="'.$emp->id.'">
                        '.strtoupper(substr($emp->firstname, 0, 1)).strtoupper(substr($emp->lastname, 0, 1)).'
                        </div>
                        <p class="font-weight-bold mb-0 ms-3 mt-2">'.$empName.'</p>
                    </div>
                ';
            }

            if(auth()->user()->cannot('super-admin')) {
                if($isAssignedToHeadCategory === true || $user->can('menu_store.escalation.tickets.view_all')) {
                    $hideU = ''; $hideAll = '';
                    $hideSU = 'hidden';
                    $menuClass = 'menu_permission_update_all';
                } else {
                    $hideU = $originalHideU;
                    $hideSU = $originalHideSU;
                    $hideAll = $originalHideAll;
                    $menuClass = $originalMenuClass;
                }
            }

            $due_date = $value->due_date;
            $formatted_due_date = !empty($due_date) ? date('M d, Y', strtotime($due_date)) : '';

            if($due_date < date('Y-m-d') && !empty($formatted_due_date)) {
                if(strtolower($value->status->name) != "completed") {
                    $formatted_due_date = '<span class="text-danger">'.$formatted_due_date.'<i class="fa fa-warning ms-2"></i></span>';
                }
            }

            $currentStatusLogSpentTimeMinutes = '';
            $totalStatusLogSpentTimeMinutes = 0;
            $interval = null;
            if(!empty($currentStatusLog)) {
                $currentDateTime = new DateTime();
                if(strtolower($status->name) == 'completed') {
                    $currentDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $currentStatusLog->time_end);
                }

                // Custom datetime (replace 'YYYY-MM-DD HH:MM:SS' with your custom datetime string)
                // $customDateTimeString = $currentStatusLog->time_start; // start date is current status time_start
                $customDateTimeString = $value->created_at; // start date is when ticket is created_at
                $customDateTime = new DateTime($customDateTimeString);

                // Calculate the difference (interval) between current datetime and custom datetime
                $interval = $currentDateTime->diff($customDateTime);

                // Extract the difference components
                $hours = $interval->h; // Hours difference
                $minutes = $interval->i; // Minutes difference
                $seconds = $interval->s; // Seconds difference

                if($interval->days > 0)
                {
                    $hours += $interval->days*24;
                }

                // Format the difference as hours:minutes:seconds
                $currentStatusLogSpentTimeMinutes = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }

            if(!empty($currentStatusLogSpentTimeMinutes)) {
                $currentStatusLogSpentTimeMinutes = '<i class="fa fa-clock text-'.$status->class.' me-2"></i>'.$currentStatusLogSpentTimeMinutes;
            }

            $watcherList = '';
            $watcherListImage = '';
            $watcherListInitial = '';

            foreach($watchers as $watcher) {
                $watcher_fullname = $watcher->firstname.' '.$watcher->lastname;
                if(!empty($watcher->image)) {
                    $watcherListImage .= '<img src="/upload/userprofile/'.$watcher->image.'" width="35" height="35" class="rounded-circle" alt="" title="'.$watcher_fullname.'"/>';
                } else {
                    $watcherListInitial .= '<div class="user-plus employee-avatar-'.$watcher->initials_random_color.'-initials" style="border: 1px dotted #fff;" title="'.$watcher_fullname.'"  onclick="showWatcherModal('.$value->id.')">'.strtoupper(substr($watcher->firstname, 0, 1)).strtoupper(substr($watcher->lastname, 0, 1)).'</div>';
                }
            }

            $watcherList = '
                <div class="d-flex align-items-center ms-2">
                    <div class="user-groups" onclick="showWatcherModal('.$value->id.')">
                        '.$watcherListImage.'
                    </div>
                    '.$watcherListInitial.'
                    <div class="user-plus" style="width: 35px !important; height: 35px !important;" title="Update Watchers"  onclick="showWatcherModal('.$value->id.')">+</div>
                </div>
            ';

            $archiveBtn = '';

            if($value->is_archived == 0) {
                $archiveBtn = '<button type="button" onclick="clickArchiveBtn(' . $value->id . ')" class="btn btn-outline-danger btn-sm ms-2"  '.$hideD.' title="Archive"><i class="fa-solid fa-box-archive"></i></button>';
            } else {
                $archiveBtn = '<button type="button" onclick="clickUnarchiveBtn(' . $value->id . ')" class="btn btn-success btn-sm ms-2"  '.$hideD.' title="Un-archive"><i class="fa-solid fa-arrow-rotate-left"></i></button>';
            }

            $newData[] = [
                'formatted_pst_created_at' => date('M d, Y g:i A', strtotime($value->pst_created_at)),
                'is_archived' => $value->is_archived,
                'id' => $value->id,
                'code' => $value->code,
                'subject' => $value->subject,
                'assigned_to_employee_id' => $value->assigned_to_employee_id,
                'assigned_to' => $assignedName,
                'description' => $value->description,
                'status_id' => $value->status_id,
                'priority_status_id' => $value->priority_status_id,
                'due_date' => $due_date,
                'formatted_due_date' => $formatted_due_date,
                'created_at' => $value->created_at->format('M d, Y h:iA'),
                'created_by' => $empName,
                'avatar' => $avatar,
                'empAvatar' => $empAvatar,
                'documents' => $documents,
                'comments' => $comments,
                'watchers' => $watchers,
                'currentStatusLog' => $currentStatusLog,
                'interval' => $interval,
                'currentStatusLogSpentTimeMinutes' => $currentStatusLogSpentTimeMinutes,
                'statusLogs' => $statusLogs,
                'is_assigned_to_head_category' => $isAssignedToHeadCategory,
                'menuClass' => $menuClass,
                'watcherList' => $watcherList,
                'priority' =>  '<button type="button" onclick="clickPriorityBtn(' . $value->id . ')" class="btn btn-outline-'.$priority->class.' border-0 btn-sm px-3" ><i class="fa fa-flag me-2"></i><small>'.$priority->name.'</small></button>',
                'status' =>  '<button style="min-width: 150px;" type="button" onclick="clickStatusBtn(' . $value->id . ')" class="btn btn-'.$status->class.' btn-sm px-3" ><small>'.$status->name.'</small></button>',
                'actions' =>  '<div class="d-flex order-actions" '.$hideAll.'>
                    <button id="ticket-edit-btn-'.$value->id.'"
                    data-due_date="'.$due_date.'"
                    data-formatted_due_date="'.(empty($due_date) ? '' : date('F d, Y', strtotime($due_date))).'"
                    data-subject="'.$value->subject.'" 
                    data-description="'.htmlspecialchars($value->description).'" 
                    data-id="'.$value->id.'"
                    data-code="'.$value->code.'"
                    data-status_id="'.$value->status_id.'"
                    data-priority_status_id="'.$value->priority_status_id.'"
                    data-assigned_to_employee_id="'.$value->assigned_to_employee_id.'"
                    data-assigned_to="'.addslashes($assignedName).'"
                    data-menuclass="'.htmlspecialchars($menuClass).'" 
                    data-watcher_list="'.htmlspecialchars($watcherList).'" 
                    data-array="'.htmlspecialchars(json_encode($value->toArray())).'" '.$hideUBoth.' type="button" class="btn btn-primary btn-sm me-2"  onclick="showTicketEditModal('.$value->id.')"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="clickDeleteBtn(' . $value->id . ')" class="btn btn-danger btn-sm" '.$hideD.'><i class="fa-solid fa-trash-can"></i></button>

                    '.$archiveBtn.'
                </div>'
            ];
        }

        $this->dataTable = [
            "draw"=> $request->draw, 
            "recordsTotal"=> $recordsTotal, 
            "recordsFiltered" => $recordsFiltered, 
            'data' => $newData
        ];
    }

    /**
     * action store
     *
     * @param [type] $request
     * @param [type] $pharmacy_store_id
     * @return void
     */
    public function store($request, $pharmacy_store_id)
    {
        $flag = true;

        $data = json_decode($request->data);

        $ticket = new Ticket();
        $ticket->subject = $data->subject;
        $ticket->description = $data->description;
        $ticket->pharmacy_store_id = $pharmacy_store_id;
        $ticket->user_id = auth()->user()->id;
        $status_id = 201;
        $ticket->support_category_id = $data->category_id;

        if(isset($data->category_id)) {
            $support = SupportEmployee::where('category_id',$data->category_id)->where('is_head_support',1)->first();
            $supportEmpID = 15;
            if(isset($support->employee_id)) {
                $supportEmpID = $support->employee_id;
            }
            $ticket->assigned_to_employee_id = $supportEmpID;
        }
        if(isset($data->assigned_to_employee_id) && !empty($data->assigned_to_employee_id)) {
            $ticket->assigned_to_employee_id = $data->assigned_to_employee_id;
        }
        if(isset($data->status_id)) {
            $status_id = $data->status_id;
        }
        $ticket->status_id = $status_id;

        if(isset($data->due_date) && !empty($data->due_date)) {
            $ticket->due_date = date('Y-m-d', strtotime($data->due_date));
        }

        $save = $ticket->save();

        /** save status logs */
        $ticketStatusLog = new TicketStatusLog();
        $ticketStatusLog->ticket_id = $ticket->id;
        $ticketStatusLog->status_id = $ticket->status_id;
        $ticketStatusLog->time_start = $ticket->created_at;
        $ticketStatusLog->user_id = $ticket->user_id;
        $ticketStatusLog->save();
        /* */

        if($save) {
            $pathUpload = $this->pathUpload($ticket->pharmacy_store_id, $ticket->id);
        
            if ($request->file('files')) {
                $files = $request->file('files');
                foreach ($files as $key => $file) {

                    // $document = new StoreDocument;
                    // $document->user_id = auth()->user()->id;
                    // $document->parent_id = $ticket->id;
                    // $document->category = 'ticket';
                    // $document->ext = $file->getClientOriginalExtension();

                    // @unlink(public_path($pathUpload.'/'.$document->path));
                    // $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).' '.date('Ymd').'.'.$file->getClientOriginalExtension();
                    // $file->move(public_path($pathUpload), $fileName);
                    // $document->path = '/'.$pathUpload.'/'.$fileName;
                    // $path = '/'.$pathUpload.'/'.$fileName;

                    // $save = $document->save();

                    $document = new StoreDocument;
                    $document->user_id = auth()->user()->id;
                    $document->parent_id = $ticket->id;
                    $document->category = 'ticket';
                    
                    $document->name = $file->getClientOriginalName();
                    $document->ext = $file->getClientOriginalExtension();
                    $document->mime_type = $file->getMimeType();
                    $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
                    $document->size = $file->getSize()/1024;
                    $document->size_type = 'KB';

                    $date = date('YmdHis');
                    $path = "/$this->aws_s3_path/stores/$ticket->pharmacy_store_id/escalation/tickets/$ticket->id/$date";
                    $document->path = $path;

                    $save = $document->save();

                    if(!$save) {
                        $flag = false;
                    }

                    if($save) {
                        $pathfile = $document->path.$document->name;
                        Storage::disk('s3')->put($pathfile, file_get_contents($file));
                    }
                }
            }
        } else {
            $flag = false;
        }
        
        if(!$flag) {
            throw new Exception("Not saved");
        }

        $this->sendNotificationStatusChanged($ticket->assignedTo, $ticket, $ticket->status, null);

        return $ticket;
    }

    /**
     * action udpate
     *
     * @param [type] $request
     * @param [type] $pharmacy_store_id
     * @return void
     */
    public function update($request, $pharmacy_store_id)
    {
        $flag = true;

        $data = json_decode($request->data);

        $ticket = $this->ticket->findOrFail($data->id);
        $previousStatus = $ticket->status;
        $previousTicket = [
            'id' => $ticket->id,
            'assigned_to_employee_id' => $ticket->assigned_to_employee_id,
            'status_id' => $ticket->status_id,
            'priority_status_id' => $ticket->priority_status_id,
            'due_date' => $ticket->due_date,
            'subject' => $ticket->subject,
            'description' => $ticket->description
        ];

        $ticket->subject = $data->subject;
        $ticket->description = $data->description;

        if(isset($data->assigned_to_employee_id) && !empty($data->assigned_to_employee_id)) {
            $ticket->assigned_to_employee_id = $data->assigned_to_employee_id;
        }
        if(isset($data->status_id)) {
            $ticket->status_id = $data->status_id;
        }
        if(isset($data->priority_status_id)) {
            $ticket->priority_status_id = $data->priority_status_id;
        }
        if(isset($data->due_date) && !empty($data->due_date)) {
            $ticket->due_date = date('Y-m-d', strtotime($data->due_date));
        }

        $save = $ticket->save();

        if($save) {
            $pathUpload = $this->pathUpload($ticket->pharmacy_store_id, $ticket->id);
        
            if ($request->file('files')) {
                $files = $request->file('files');
                foreach ($files as $key => $file) {

                    $document = new StoreDocument;
                    $document->user_id = auth()->user()->id;
                    $document->parent_id = $ticket->id;
                    $document->category = 'ticket';
                    $document->ext = $file->getClientOriginalExtension();

                    @unlink(public_path($pathUpload.'/'.$document->path));
                    $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).' '.date('Ymd').'.'.$file->getClientOriginalExtension();
                    $file->move(public_path($pathUpload), $fileName);
                    $document->path = '/'.$pathUpload.'/'.$fileName;
                    $path = '/'.$pathUpload.'/'.$fileName;

                    $save = $document->save();

                    if(!$save) {
                        $flag = false;
                    }
                }
            }
        } else {
            $flag = false;
        }
        
        if(!$flag) {
            throw "Not saved";
        }

        /**start sending mail */;
        $event = "changed";
        $currentStatus = StoreStatus::findOrFail($ticket->status_id);
        if($ticket->due_date < date('Y-m-d')) {
            $event = "overdue";
        }
        if($ticket->description != $previousTicket['description']) {
            $event = "description";
        }
        if($ticket->priority_status_id != $previousTicket['priority_status_id']) {
            $event = "priority";
        }
        if($ticket->assigned_to_employee_id != $previousTicket['assigned_to_employee_id']) {
            $event = "re-assigned";
        }

        // if changed status
        if($ticket->status_id != $previousStatus->id) {
            /** save status logs */
            $recentTicketStatusLog = TicketStatusLog::where('ticket_id',$ticket->id)->orderBy('created_at', 'desc')->first();
            if(isset($recentTicketStatusLog)) {
                $recentTicketStatusLog->time_end = date('Y-m-d H:i:s');
                $recentTicketStatusLog->save();
            }
            $ticketStatusLog = new TicketStatusLog();
            $ticketStatusLog->ticket_id = $ticket->id;
            $ticketStatusLog->status_id = $currentStatus->id;
            $ticketStatusLog->time_start = date('Y-m-d H:i:s');
            $ticketStatusLog->user_id = auth()->user()->id;
            $ticketStatusLog->save();
            /* */

            $event = "changed";
            $this->sendNotificationStatusChanged($ticket->assignedTo, $ticket, $currentStatus, $previousStatus, $event);
        } else {
            if($event!="changed") {
                $this->sendNotificationStatusChanged($ticket->assignedTo, $ticket, $currentStatus, null, $event);
            }
        }
        /**ended sending mail */

    }

    public function storeComment($request)
    {
        $flag = false;

        $data = json_decode($request->data);

        $ticket_id = $data->ticket_id ?? null;
        $comment = $data->comment ?? null;
        $pharmacy_store_id = $data->pharmacy_store_id ?? null;

        $files = [];

        if($request->file('files')) {
            $files = $request->file('files');
        }

        if(!empty($ticket_id) && !empty($comment)) {
            $flag = true;
        }

        if(!empty($ticket_id) && empty($comment) && count($files) > 0) {
            $flag = true;
        }

        // if(!empty($ticket_id) && !empty($comment)) {
        //     $flag = true;
        // }

        if($flag == true)
        {
            $ticketComment = new TicketComment();
            $ticketComment->ticket_id = $ticket_id;
            $ticketComment->comment = $comment;
            $ticketComment->user_id = auth()->user()->id;
            $ticketComment->save();

            $employee = Employee::where('user_id', auth()->user()->id)->first();

            $pathUpload = $this->pathUpload($pharmacy_store_id, $ticket_id);

            $commentFiles = [];
        
            if ($request->file('files')) {
                foreach ($files as $key => $file) {
                    $document = new StoreDocument;
                    $document->user_id = auth()->user()->id;
                    $document->parent_id = $ticket_id;
                    $document->category = 'ticket';
                    
                    $document->name = $file->getClientOriginalName();
                    $document->ext = $file->getClientOriginalExtension();
                    $document->mime_type = $file->getMimeType();
                    $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
                    $document->size = $file->getSize()/1024;
                    $document->size_type = 'KB';

                    $date = date('YmdHis');
                    $path = "/$this->aws_s3_path/stores/$pharmacy_store_id/escalation/tickets/$ticket_id/$date";
                    $document->path = $path;

                    $save = $document->save();

                    $commentFiles[] = $document;
                    TicketCommentDocument::insertOrIgnore([
                        'ticket_comment_id' => $ticketComment->id,
                        'document_id' => $document->id
                    ]);

                    if(!$save) {
                        $flag = false;
                    }

                    if($save) {
                        $pathfile = $document->path.$document->name;
                        Storage::disk('s3')->put($pathfile, file_get_contents($file));
                        $s3Url = Storage::disk('s3')->temporaryUrl(
                            $pathfile,
                            now()->addMinutes(30)
                        );
                    }
                }
            }

            $ticket = Ticket::with('assignedTo','status','newComment')->find($ticket_id);
            $currentStatus = $ticket->status;

            $this->sendNotificationStatusChanged($ticket->assignedTo, $ticket, $currentStatus, null, 'comment');

            return [
                'comment' => $ticketComment,
                'employee' => $employee,
                'files' => $commentFiles,
                'formatted_created_at' => $ticketComment->formatted_pst_created_at
            ];
        }
    }

    public function storeAttachments($request)
    {
        $flag = false;

        $ticket_id = $request->ticket_id ?? null;
        $ticket = Ticket::findOrFail($ticket_id);

        if(isset($ticket->id)) {
            $flag = true;
        }
        
        $pharmacy_store_id = $ticket->pharmacy_store_id;
        if($flag == true)
        {
            $employee = Employee::where('user_id', auth()->user()->id)->first();
            $attachments = [];
        
            if ($request->file('files')) {
                $files = $request->file('files');
                foreach ($files as $key => $file) {
                    // dd(count($file), count($files));
                    $document = new StoreDocument;
                    $document->user_id = auth()->user()->id;
                    $document->parent_id = $ticket_id;
                    $document->category = 'ticket';
                    
                    $document->name = $file->getClientOriginalName();
                    $document->ext = $file->getClientOriginalExtension();
                    $document->mime_type = $file->getMimeType();
                    $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
                    $document->size = $file->getSize()/1024;
                    $document->size_type = 'KB';

                    $date = date('YmdHis');
                    $path = "/$this->aws_s3_path/stores/$pharmacy_store_id/escalation/tickets/$ticket_id/$date";
                    $document->path = $path;

                    $save = $document->save();

                    if(!$save) {
                        $flag = false;
                    }

                    if($save) {
                        $pathfile = $document->path.$document->name;
                        Storage::disk('s3')->put($pathfile, file_get_contents($file));
                        $s3Url = Storage::disk('s3')->temporaryUrl(
                            $pathfile,
                            now()->addMinutes(30)
                        );

                        $array = $document->toArray();
                        $array['url'] = $s3Url;
                        $attachments[] = $array;
                    }
                }
            }

            return $attachments;
        }
    }

    public function delete($id)
    {
        $ticket = $this->ticket->findOrFail($id);
        $path = $this->pathUpload($ticket->pharmacy_store_id, $ticket->id);

        $docs = $this->document->where('category', 'ticket')->where('parent_id', $id)->count();
        if($docs > 0) {
            $this->document->where('category', 'ticket')->where('parent_id', $id)->delete();
            File::deleteDirectory(public_path('/'.$path));
        }

        $this->sendNotificationStatusChanged($ticket->assignedTo, $ticket, $ticket->status, null, 'deleted');

        $save = $ticket->delete();

        if(!$save) {
            throw new Exception("Not Deleted");
        }
    }

    public function archive($request)
    {
        $selectedIds = $request->selectedIds ?? [];

        if(count($selectedIds) > 0) {
            $save = Ticket::whereIn('id', $selectedIds)->update(['is_archived' => 1]);
            if(!$save) {
                throw new Exception("Not archived");
            }
        }
    }

    public function unarchive($request)
    {
        $selectedIds = $request->selectedIds ?? [];

        if(count($selectedIds) > 0) {
            $save = Ticket::whereIn('id', $selectedIds)->update(['is_archived' => 0]);
            if(!$save) {
                throw new Exception("Not unarchived");
            }
        }
    }

    public function storeDocument($request)
    {
        $ticket = $this->ticket->findOrFail($request->ticket_id);

        if ($request->file('files')) {
            $pathUpload = $this->pathUpload($ticket->pharmacy_store_id, $ticket->id);
            $files = $request->file('files');

            $this->processStoringDocuments($files, $ticket->id, 'ticket', $pathUpload);
        }
    }

    public function deleteDocument($id)
    {
        $document = $this->document->findOrFail($id);

        $path = $document->path.$document->name;

        $save =  false;
        if($path != ''){
            if(Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
            @unlink(public_path('/'.$document->path));
            $save = $document->delete();   
        }

        if(!$save) {
            throw "Not Deleted";
        }
    }

    public function retrieveRecent($params)
    {
        $user = Auth::user();

        $query = $this->ticket->with('user.employee'
            , 'documents'
            , 'assignedTo.supportHeadCategory'
            , 'status', 'comments.user.employee'
            , 'comments.documents'
            , 'currentStatusLog'
            , 'statusLogs.status'
            , 'watchers'
        )->select('*', DB::raw('TIMESTAMPDIFF(HOUR, created_at, NOW()) AS hours_difference'));

        if(!$user->hasRole('super-admin') && $user->cannot('menu_store.escalation.tickets.view_all')){
            $query = $query->where(function($query) use($user){
                $query->orWhere('assigned_to_employee_id', $user->employee->id);
                $query->orWhere('user_id', $user->id);
                $query->orWhere(function ($q) use ($user) {
                    $q->whereExists(function ($query) use ($user) {
                        $query->select(DB::raw(1))
                                ->from('support_employees')
                                ->join('support_categories', 'support_categories.id', '=', 'support_employees.category_id')
                                ->whereColumn('support_categories.id', 'tickets.support_category_id')
                                ->where('support_employees.employee_id', $user->employee->id)
                                ->where('support_employees.is_head_support', 1);
                    });
                });  
            });
        }

        // $query = $query->where('assigned_to_employee_id', $user->employee->id)
        //     ->orWhere('user_id', $user->id)
        //     ->orWhere(function ($q) use ($user) {
        //         $q->whereExists(function ($query) use ($user) {
        //             $query->select(DB::raw(1))
        //                     ->from('support_employees')
        //                     ->join('support_categories', 'support_categories.id', '=', 'support_employees.category_id')
        //                     ->whereColumn('support_categories.id', 'tickets.support_category_id')
        //                     ->where('support_employees.employee_id', $user->employee->id)
        //                     ->where('support_employees.is_head_support', 1);
        //         });
        //     });  

        if(isset($params['pharmacy_store_id'])) {
            $query = $query->where('pharmacy_store_id', $params['pharmacy_store_id']);
        }

        $status = ['COMPLETED'];
        $query = $query->where(function($query) use ($status){
            $query->whereHas('status', function($query) use ($status) {
                $query->whereNotIn(DB::raw('UPPER(name)'), $status);
            });
        });
        
        $query = $query->orderBy('created_at', 'desc');
        $data = $query->limit(15)->get();

        $menuClass = 'menu_permission_update_semi';
        if(auth()->user()->can('menu_store.escalation.tickets.semi_update'))
        {
            $menuClass = 'menu_permission_update_semi';
        }
        if(auth()->user()->can('menu_store.escalation.tickets.update'))
        {
            $menuClass = 'menu_permission_update_all';
        }
        $originalMenuClass = $menuClass;

        $user = User::with('employee.supportHeadCategory')->findOrFail(auth()->user()->id);

        $newData = [];

        foreach ($data as $value) {
            $empName = isset($value->user->employee) ? $value->user->employee->getFullName() : "NA";
            $documents = isset($value->ticketDocuments) ? $value->ticketDocuments->all() : [];
            $comments = isset($value->comments) ? $value->comments->all() : [];
            $assignedTo = isset($value->assignedTo) ? $value->assignedTo : null;
            $assignedName = !empty($assignedTo) ? $assignedTo->getFullName() : "NA";
            $status = isset($value->status) ? $value->status : [];
            $priority = isset($value->priority) ? $value->priority : [];
            $emp = $value->user->employee;

            $watchers = isset($value->watchers) ? $value->watchers->all() : [];

            $currentStatusLog = isset($value->currentStatusLog) ? $value->currentStatusLog : null;
            $statusLogs = isset($value->statusLogs) ? $value->statusLogs->all() : [];

            $isAssignedToHeadCategory = false;
            if(isset($user->employee->supportHeadCategory->category_id)) {
                $isAssignedToHeadCategory = $user->employee->supportHeadCategory->category_id == $value->support_category_id ? true:false;
            }


            $assignedToInitials = 'NA';
            $assignedToImage  = '';
            $assignedToInitialsRandomColor = 1;
            if(isset($assignedTo->id)) {
                $assignedToInitials = strtoupper(substr($assignedTo->firstname, 0, 1)).strtoupper(substr($assignedTo->lastname, 0, 1));
                $assignedToImage = !empty($assignedTo->image) ? $assignedTo->image : '';
                $assignedToInitialsRandomColor = $assignedTo->initials_random_color;
            }
            
            $avatar = $assignedName;
            if(!empty($assignedToImage)) {
                $avatar = '
                    <div class="d-flex">
                        <img src="/upload/userprofile/'.$assignedToImage.'" width="32" height="32" class="rounded-circle" alt="">
                        <div class="flex-grow-1 ms-3 mt-2">
                            <p class="font-weight-bold mb-0">'.$assignedName.'</p>
                        </div>
                    </div>
                ';
            } else {
                $avatar = '
                    <div class="d-flex">
                        <div class="employee-avatar-'.$assignedToInitialsRandomColor.'-initials hr-employee">
                        '.$assignedToInitials.'
                        </div>
                        <p class="font-weight-bold mb-0 ms-3 mt-2">'.$assignedName.'</p>
                    </div>
                ';
            }

            $empAvatar = '';
            if(!empty($emp->image)) {
                $empAvatar = '
                    <div class="d-flex">
                        <img src="/upload/userprofile/'.$emp->image.'" width="32" height="32" class="rounded-circle" alt="">
                        <div class="flex-grow-1 ms-3 mt-2">
                            <p class="font-weight-bold mb-0">'.$empName.'</p>
                        </div>
                    </div>
                ';
            } else {
                $empAvatar = '
                    <div class="d-flex">
                        <div class="employee-avatar-'.$emp->initials_random_color.'-initials hr-employee" data-id="'.$emp->id.'">
                        '.strtoupper(substr($emp->firstname, 0, 1)).strtoupper(substr($emp->lastname, 0, 1)).'
                        </div>
                        <p class="font-weight-bold mb-0 ms-3 mt-2">'.$empName.'</p>
                    </div>
                ';
            }

            if(auth()->user()->cannot('super-admin')) {
                if($isAssignedToHeadCategory === true || $user->can('menu_store.escalation.tickets.view_all')) {
                    $menuClass = 'menu_permission_update_all';
                } else {
                    $menuClass = $originalMenuClass;
                }
            }

            $due_date = $value->due_date;
            $formatted_due_date = !empty($due_date) ? date('M d, Y', strtotime($due_date)) : '';

            if($due_date < date('Y-m-d') && !empty($formatted_due_date)) {
                if(strtolower($value->status->name) != "completed") {
                    $formatted_due_date = '<span class="text-danger">'.$formatted_due_date.'<i class="fa fa-warning ms-2"></i></span>';
                }
            }

            $currentStatusLogSpentTimeMinutes = '';
            $totalStatusLogSpentTimeMinutes = 0;
            $interval = null;
            if(!empty($currentStatusLog)) {
                $currentDateTime = new DateTime();
                if(strtolower($status->name) == 'completed') {
                    $currentDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $currentStatusLog->time_end);
                }

                // Custom datetime (replace 'YYYY-MM-DD HH:MM:SS' with your custom datetime string)
                // $customDateTimeString = $currentStatusLog->time_start; // start date is current status time_start
                $customDateTimeString = $value->created_at; // start date is when ticket is created_at
                $customDateTime = new DateTime($customDateTimeString);

                // Calculate the difference (interval) between current datetime and custom datetime
                $interval = $currentDateTime->diff($customDateTime);

                // Extract the difference components
                $hours = $interval->h; // Hours difference
                $minutes = $interval->i; // Minutes difference
                $seconds = $interval->s; // Seconds difference

                if($interval->days > 0)
                {
                    $hours += $interval->days*24;
                }

                // Format the difference as hours:minutes:seconds
                $currentStatusLogSpentTimeMinutes = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }

            if(!empty($currentStatusLogSpentTimeMinutes)) {
                $currentStatusLogSpentTimeMinutes = '<i class="fa fa-clock text-'.$status->class.' me-2"></i>'.$currentStatusLogSpentTimeMinutes;
            }

            $watcherList = '';
            $watcherListImage = '';
            $watcherListInitial = '';

            foreach($watchers as $watcher) {
                $watcher_fullname = $watcher->firstname.' '.$watcher->lastname;
                if(!empty($watcher->image)) {
                    $watcherListImage .= '<img src="/upload/userprofile/'.$watcher->image.'" width="35" height="35" class="rounded-circle" alt="" title="'.$watcher_fullname.'"/>';
                } else {
                    $watcherListInitial .= '<div class="user-plus employee-avatar-'.$watcher->initials_random_color.'-initials" style="border: 1px dotted #fff;" title="'.$watcher_fullname.'"  onclick="showWatcherModal('.$value->id.')">'.strtoupper(substr($watcher->firstname, 0, 1)).strtoupper(substr($watcher->lastname, 0, 1)).'</div>';
                }
            }

            $watcherList = '
                <div class="d-flex align-items-center ms-2">
                    <div class="user-groups" onclick="showWatcherModal('.$value->id.')">
                        '.$watcherListImage.'
                    </div>
                    '.$watcherListInitial.'
                    <div class="user-plus" style="width: 35px !important; height: 35px !important;" title="Update Watchers"  onclick="showWatcherModal('.$value->id.')">+</div>
                </div>
            ';

            $newData[] = [
                'id' => $value->id,
                'subject' => $value->subject,
                'assigned_to_employee_id' => $value->assigned_to_employee_id,
                'assigned_to' => $assignedName,
                'assignedToImage' => $assignedToImage,
                'assignedToInitials' => $assignedToInitials,
                'assignedToInitialsRandomColor' => $assignedToInitialsRandomColor,
                'description' => $value->description,
                'status_id' => $value->status_id,
                'priority_status_id' => $value->priority_status_id,
                'due_date' => $due_date,
                'formatted_due_date' => $formatted_due_date,
                'created_at' => $value->created_at->format('M d, Y h:iA'),
                'created_by' => $empName,
                'avatar' => $avatar,
                'empAvatar' => $empAvatar,
                'documents' => $documents,
                'comments' => $comments,
                'currentStatusLog' => $currentStatusLog,
                'interval' => $interval,
                'currentStatusLogSpentTimeMinutes' => $currentStatusLogSpentTimeMinutes,
                'statusLogs' => $statusLogs,
                'is_assigned_to_head_category' => $isAssignedToHeadCategory,
                'menuClass' => $menuClass,
                'watcherList' => $watcherList,
                'hours_difference' => $value->hours_difference,
                'priorityStatus' => $priority,
                'status' => $status,
                'priority' =>  '<button type="button" onclick="clickPriorityBtn(' . $value->id . ')" class="btn btn-outline-'.$priority->class.' border-0 btn-sm px-3" ><i class="fa fa-flag me-2"></i><small>'.$priority->name.'</small></button>',
                'actions' =>  '<div class="d-flex order-actions" style="display:none !important;">
                    <button id="ticket-edit-btn-'.$value->id.'"
                    data-due_date="'.$due_date.'"
                    data-formatted_due_date="'.(empty($due_date) ? '' : date('F d, Y', strtotime($due_date))).'"
                    data-subject="'.$value->subject.'" 
                    data-description="'.htmlspecialchars($value->description).'" 
                    data-id="'.$value->id.'"
                    data-status_id="'.$value->status_id.'"
                    data-priority_status_id="'.$value->priority_status_id.'"
                    data-assigned_to_employee_id="'.$value->assigned_to_employee_id.'"
                    data-assigned_to="'.addslashes($assignedName).'"
                    data-menuclass="'.htmlspecialchars($menuClass).'" 
                    data-watcher_list="'.htmlspecialchars($watcherList).'" 
                    data-array="'.htmlspecialchars(json_encode($value->toArray())).'" type="button" class="btn btn-primary btn-sm me-2"  onclick="showTicketEditModal('.$value->id.')"><i class="fa fa-pencil"></i></button>
                </div>'
            ];
        }
        return $newData;
    }

    public function getEventByColumn($column)
    {
        $event = '';
        switch($column) {
            case 'status_id':
                $event = 'changed';
                break;
            case 'assigned_to_employee_id':
                $event = 're-assigned';
                break;
            case 'priority_status_id':
                $event = 'priority';
                break;
            case 'subject':
            case 'description':
                $event = 'description';
                break;
        }
        return $event;
    }

    /**
     * Private functions starts here
     */
    private function pathUpload($pharmacy_store_id, $ticket_id) : string
    {
        return self::BASE_PATH.'/'.$pharmacy_store_id.'/escalation/tickets/'.$ticket_id;
    }

    public function sendNotificationStatusChanged($employee, $ticket, $currentStatus, $previousStatus = null, $event = "new")
    {
        if(config('mail.maintenance') != "ON") {
            Bus::dispatch(new NotifyTicketToAssignee($employee, $ticket, $currentStatus, $previousStatus, $event));
        }
    }

    public function sendNotificationOverDue()
    {
        $tickets = Ticket::whereNotNull('due_date')
            ->where('due_date', "<", date('Y-m-d'))
            ->whereNotIn('status_id', [206])
            ->where('is_archive', 0)
            ->get();
        foreach($tickets as $ticket)
        {
            $this->sendNotificationStatusChanged($ticket->assignedTo, $ticket, $ticket->status, null, 'overdue');
        }
    }

    public function assignees($request)
    {
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        $query = Employee::select('employees.*')
                    ->join('users', 'employees.user_id', '=', 'users.id')
                    ->leftJoin('pharmacy_staff', 'employees.id', '=', 'pharmacy_staff.employee_id')
                    ->where("employees.id", ">", 19)
                    ->whereNot("employees.status", "Terminated");

        // Search //input all searchable fields
        $search = trim($request->search);

        if($request->has('pharmacy_store_id')) {
            $query = $query->where(function ($query) use ($request){
                $query->orWhere('pharmacy_staff.pharmacy_store_id', $request->pharmacy_store_id);
                $query->orWhereNull('pharmacy_staff.pharmacy_store_id');
            });
        }

        if(!empty($search)) {
            $query = $query->where(function($query) use ($search){
                $query = $query->orWhere('employees.firstname', 'like', "%".$search."%");
                $query = $query->orWhere('employees.lastname', 'like', "%".$search."%");
            });
        }

        $watchers = [];
        if($request->has('ticket_id')) {
            if(!empty($request->ticket_id)) {
                $ticket = Ticket::findOrFail($request->ticket_id);
                // $watchers = $ticket->watchers()->pluck('employee_id')->all();
                $watchers = [$ticket->assigned_to_employee_id];
            } else {
                $query = $query->whereRaw(0);
            }
        }
        if(!empty($watchers)) {
            $priorityIdsString = implode(',', $watchers);
            $query = $query->orderBy(DB::raw('FIELD(employees.id, '.$priorityIdsString.') DESC, employees.firstname, employees.lastname'));
        } else {
            $query = $query->orderBy('firstname', 'asc')->orderBy('lastname', 'asc');
        }
        
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $newData = [];

        foreach($data as $d) {
            $id = $d->id;
            $initials = strtoupper(substr($d->firstname, 0, 1)) . strtoupper(substr($d->lastname, 0, 1));
            $image = $d->image;
            $initials_random_color = $d->initials_random_color;
            $fullname = $d->firstname . ' ' . $d->lastname;
            $is_watcher = false;

            $json = ['id' => $d->id, 'firstname' => $d->firstname, 'lastname' => $d->lastname, 'initials' => $initials, 'image' => $image, 'initials_random_color' => $initials_random_color];

            $actions = '<button class="btn btn-sm btn-outline-success ms-auto" 
                    onclick="selectAssignee(
                        '.$request->ticket_id.', 
                        '.$id.',
                        \''.htmlspecialchars(json_encode($json)).'\')"
                >Select<i class="fa fa-check ms-2"></i></button>';
            if(in_array($id, $watchers)) {
                $actions = '<button class="btn btn-sm btn-success ms-auto" disabled>Selected<i class="fa fa-circle ms-2"></i></button>';
                $is_watcher = true;
            }

            $fullname = $is_watcher === true ? '<b>'.$fullname.'<i class="fa fa-check-double text-success ms-2"></i></b>' : $fullname;

            if(!empty($image)) {
                $fullname = '
                    <div class="d-flex">
                        <img src="/upload/userprofile/'.$image.'" width="32" height="32" class="rounded-circle" alt="">
                        <div class="flex-grow-1 ms-3 mt-2">
                            <p class="font-weight-bold mb-0">'.$fullname.'</p>
                        </div>
                    </div>
                ';
            } else {
                $fullname = '
                    <div class="d-flex">
                        <div class="employee-avatar-'.$initials_random_color.'-initials hr-employee">
                        '.$initials.'
                        </div>
                        <p class="font-weight-bold mb-0 ms-3 mt-2">'.$fullname.'</p>
                    </div>
                ';
            }

            $newData[] = [
                'id' => $id,
                'firstname' => $d->firstname,
                'lastname' => $d->lastname,
                'fullname' => $fullname,
                'is_watcher' => $is_watcher,
                'actions' => $actions
            ];
        }

        return [
            "draw"=> $request->draw, 
            "recordsTotal"=> $recordsTotal, 
            "recordsFiltered" => $recordsFiltered, 
            'data' => $newData
        ];
    }

    public function watchers($request)
    {
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        $query = Employee::select('employees.*')
                    ->join('users', 'employees.user_id', '=', 'users.id')
                    ->leftJoin('pharmacy_staff', 'employees.id', '=', 'pharmacy_staff.employee_id')
                    ->where("employees.id", ">", 19)
                    ->whereNot("employees.status", "Terminated");

        // Search //input all searchable fields
        $search = trim($request->search);

        if($request->has('pharmacy_store_id')) {
            $query = $query->where(function ($query) use ($request){
                $query->orWhere('pharmacy_staff.pharmacy_store_id', $request->pharmacy_store_id);
                $query->orWhereNull('pharmacy_staff.pharmacy_store_id');
            });
        }

        if(!empty($search)) {
            $query = $query->where(function($query) use ($search){
                $query = $query->orWhere('employees.firstname', 'like', "%".$search."%");
                $query = $query->orWhere('employees.lastname', 'like', "%".$search."%");
            });
        }

        $watchers = [];
        if($request->has('ticket_id')) {
            if(!empty($request->ticket_id)) {
                $ticket = Ticket::findOrFail($request->ticket_id);
                $watchers = $ticket->watchers()->pluck('employee_id')->all();
            } else {
                $query = $query->whereRaw(0);
            }
        }
        if(!empty($watchers)) {
            $priorityIdsString = implode(',', $watchers);
            $query = $query->orderBy(DB::raw('FIELD(employees.id, '.$priorityIdsString.') DESC, employees.firstname, employees.lastname'));
        } else {
            $query = $query->orderBy('firstname', 'asc')->orderBy('lastname', 'asc');
        }
        
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $newData = [];

        foreach($data as $d) {
            $id = $d->id;
            $initials = strtoupper(substr($d->firstname, 0, 1)) . strtoupper(substr($d->lastname, 0, 1));
            $image = $d->image;
            $initials_random_color = $d->initials_random_color;
            $fullname = $d->firstname . ' ' . $d->lastname;
            $is_watcher = false;

            $actions = '<button class="btn btn-sm btn-success ms-auto" onclick="addWatcher('.$request->ticket_id.', '.$id.')">Add as Watcher<i class="fa fa-check ms-2"></i></button>';
            if(in_array($id, $watchers)) {
                $actions = '<button class="btn btn-sm btn-danger ms-auto" onclick="deleteWatcher('.$request->ticket_id.', '.$id.')">Remove Watcher<i class="fa fa-times ms-2"></i></button>';
                $is_watcher = true;
            }

            $actionAssignee = '<button class="btn btn-sm btn-success ms-auto" onclick="selectAssignee('.$request->ticket_id.', '.$id.')">Select<i class="fa fa-check ms-2"></i></button>';

            $fullname = $is_watcher === true ? '<b>'.$fullname.'<i class="fa fa-check-double text-success ms-2"></i></b>' : $fullname;

            if(!empty($image)) {
                $fullname = '
                    <div class="d-flex">
                        <img src="/upload/userprofile/'.$image.'" width="32" height="32" class="rounded-circle" alt="">
                        <div class="flex-grow-1 ms-3 mt-2">
                            <p class="font-weight-bold mb-0">'.$fullname.'</p>
                        </div>
                    </div>
                ';
            } else {
                $fullname = '
                    <div class="d-flex">
                        <div class="employee-avatar-'.$initials_random_color.'-initials hr-employee">
                        '.$initials.'
                        </div>
                        <p class="font-weight-bold mb-0 ms-3 mt-2">'.$fullname.'</p>
                    </div>
                ';
            }

            $newData[] = [
                'id' => $id,
                'firstname' => $d->firstname,
                'lastname' => $d->lastname,
                'fullname' => $fullname,
                'is_watcher' => $is_watcher,
                'actions' => $actions,
                'action_select' => $actionAssignee
            ];
        }

        return [
            "draw"=> $request->draw, 
            "recordsTotal"=> $recordsTotal, 
            "recordsFiltered" => $recordsFiltered, 
            'data' => $newData
        ];
    }

    public function autoArchive()
    {
        $oneWeekAgo = Carbon::now()->subWeek();
        $save = Ticket::where('created_at', '<=', $oneWeekAgo)
            ->whereIn('status_id', [206])
            ->update(['is_archived' => 1]);
        return $save;
    }

}