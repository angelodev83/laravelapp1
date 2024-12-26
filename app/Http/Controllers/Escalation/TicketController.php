<?php

namespace App\Http\Controllers\Escalation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Ticket;
use App\Http\Controllers\Controller;
use App\Interfaces\ITicketRepository;
use App\Models\StoreDocument;
use App\Models\StoreStatus;
use App\Models\TicketComment;
use App\Models\TicketStatusLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    private ITicketRepository $repository;

    private $ticket;

    public function __construct(
        Ticket $ticket
        ,   ITicketRepository $repository
    ) {
        $this->ticket = $ticket;
        $this->repository = $repository;

        $this->middleware('permission:menu_store.escalation.tickets.index|menu_store.escalation.tickets.create|menu_store.escalation.tickets.update|menu_store.escalation.tickets.delete');
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $this->checkStorePermission($id);
            $years = $this->getYears();
            $months = $this->getMonths();
            $currentYear = now()->year;
            $currentMonth = now()->month;
            $breadCrumb = ['Escalation', 'Tickets'];
            return view('/stores/escalation/tickets/index', [
                'breadCrumb' => $breadCrumb,
                'years' => $years,
                'months' => $months,
                'currentYear' => $currentYear,
                'currentMonth' => $currentMonth,
            ]);
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function filteredData(Request $request) {
        if (request()->ajax()) {
            $selectedYear = $request->input('year');
            $selectedMonth = $request->input('month');
            $is_archived = $request->input('is_archived');

            $ticketStatuses = $this->ticketStatuses();
            
            $params = [
                'selectedYear' => $selectedYear,
                'selectedMonth' => $selectedMonth,
                'is_archived' => $is_archived,
            ];

            $data = [
                'selectedYear' => $selectedYear,
                'selectedMonth' => $selectedMonth
            ];

            foreach ($ticketStatuses as $statusId => $statusName) {
                $data['ticket' . $statusId] = $this->getTasksByStatusId($statusId, $params);
            }

            return response()->json(['data' => $data], 200);
        }
    }

    private function getTasksByStatusId($statusId, $params = []) {
        $year = isset($params['selectedYear']) ? $params['selectedYear'] : date('Y');
        $month = isset($params['selectedMonth']) ? $params['selectedMonth'] : null;
        $is_archived = isset($params['is_archived']) ? $params['is_archived'] : 0;

        $query = Ticket::with('assignedTo', 'statusLogs.status', 'priority', 'watchers', 'comments', 'user.employee')
                ->where('status_id', $statusId)
                ->where('is_archived', $is_archived)
                ->whereYear('created_at', $year);
        
        if(!empty($month)) {
            $query = $query->whereMonth('created_at', $month);
        }

        $values = $query->get();

        $newData = [];
        $hideU = 'hidden';
        $hideSU = 'hidden';
        $hideD = 'hidden';
        $hideAll = 'hidden';
        $hideUBoth = 'hidden';
        $menuClass = 'menu_permission_update_semi';

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
        
        foreach ($values as $value) {
            $watchers = $value->watchers;
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

            $due_date = $value->due_date;
            $formatted_due_date = !empty($due_date) ? date('M d, Y', strtotime($due_date)) : '';

            if($due_date < date('Y-m-d') && !empty($formatted_due_date)) {
                if(strtolower($value->status->name) != "completed") {
                    $formatted_due_date = '<span class="text-danger">'.$formatted_due_date.'<i class="fa fa-warning ms-2"></i></span>';
                }
            }

            $isAssignedToHeadCategory = false;
            if(isset($value->user->employee->supportHeadCategory->category_id)) {
                $isAssignedToHeadCategory = $value->user->employee->supportHeadCategory->category_id == $value->support_category_id ? true:false;
            }

            if(auth()->user()->cannot('super-admin')) {
                if($isAssignedToHeadCategory === true || $value->user->can('menu_store.escalation.tickets.view_all')) {
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

            $newData[] = [
                'is_archived' => $value->is_archived,
                'id' => $value->id,
                'code' => $value->code,
                'subject' => $value->subject,
                'assigned_to_employee_id' => $value->assigned_to,
                'assigned_to' => $value->assignedTo->firstname.' '.$value->assignedTo->lastname,
                'image' => $value->assignedTo->image,
                'initials' => $value->assignedTo->firstname[0].$value->assignedTo->lastname[0],
                'initials_random_color' => $value->assignedTo->initials_random_color,
                'description' => $value->description,
                'status_id' => $value->status_id,
                'status_logs' => $value->statusLogs,
                'priority_color' => $value->priority->color,
                'priority_name' => $value->priority->name,
                'priority_status_id' => $value->priority_status_id,
                'due_date' => $due_date,
                'watchers' => $watchers,
                'watcherList' => $watcherList,
                'formatted_due_date' => $formatted_due_date,
                'menuClass' => $menuClass,
                'user' => $value->user
            ];
        }
        return $newData;
    }

    protected function ticketStatuses() {
        return [
            '201' => 'ToDo',
            '202' => 'InProgress',
            '203' => 'ToAnalyze',
            '204' => 'ToVerify',
            '205' => 'Waiting',
            '206' => 'Completed',
        ];
    }

    protected function getMonths() {
        return [
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];
    }

    protected function getYears() {
        return [
            '2024' => '2024',
            '2023' => '2023',
            '2022' => '2022',
            '2021' => '2021',
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show($id, $tix_id)
    {
        $ticket = $this->ticket->with('user.employee', 'status', 'assignedTo')->findOrFail($tix_id);
        $title = 'Details';//'Tix #'.sprintf("%06d", $tix_id);

        $breadCrumb = ['Escalation', 'Tickets', $title];
        $breadCrumb['back'] = "/store/escalation/$ticket->pharmacy_store_id/tickets";
        return view('/stores/escalation/tickets/view', compact('breadCrumb', 'ticket'));
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            
            $this->repository->setDataTable($request);
            $data = $this->repository->getDataTable();

            return response()->json($data, 200);
        }
    }

    public function assignees(Request $request)
    {   
        $data = $this->repository->assignees($request);
        
        if($request->ajax()){
            return response()->json($data, 200);
        }
        return $data;
    }

    public function watchers(Request $request)
    {   
        $data = $this->repository->watchers($request);
        
        if($request->ajax()){
            return response()->json($data, 200);
        }
        return $data;
    }

    public function addWatcher(Request $request)
    {   
        if($request->ajax()){
            try {
                DB::beginTransaction();

                $ticketWatcher = DB::table('ticket_watchers')->insertOrIgnore([
                    'ticket_id' => $request->ticket_id,
                    'employee_id' => $request->employee_id,
                ]);
                
                DB::commit();

                return json_encode([
                    'data'=> [
                        $ticketWatcher
                    ],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TicketController.addWatcher.db_transaction.'
                ]);
            }
        }
    }

    public function deleteWatcher(Request $request)
    {   
        if($request->ajax()){
            try {
                DB::beginTransaction();

                $ticketWatcher = DB::table('ticket_watchers')
                    ->where('ticket_id', $request->ticket_id)
                    ->where('employee_id', $request->employee_id)
                    ->delete();
                
                DB::commit();

                return json_encode([
                    'data'=> [
                        $ticketWatcher
                    ],
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TicketController.deleteWatcher.db_transaction.'
                ]);
            }
        }
    }

    public function archive(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                DB::beginTransaction();

                $this->repository->archive($request);

                DB::commit();

                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been archived.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TicketController.archive.'
                ]);
            }
        }
    }

    public function unarchive(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                DB::beginTransaction();

                $this->repository->unarchive($request);

                DB::commit();

                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been unarchived.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TicketController.unarchive.'
                ]);
            }
        }
    }

    public function updateDetails(Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();
                
                $id = $request->id;
                $event = '';
                $ticket = Ticket::with('assignedTo', 'user.employee', 'status', 'priority', 'watchers')->findOrFail($id);
                $previousStatus = $ticket->status;

                $data = $request->except('id');
                foreach($data as $field => $value) {
                    $ticket->$field = $value;
                    $event = $this->repository->getEventByColumn($field);

                    if($field == 'status_id') {
                        $currentStatus = StoreStatus::findOrFail($value);
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
                    } else {
                        $currentStatus = $previousStatus;
                    }
                }
                $ticket->save();
                
                DB::commit();

                if(!empty($event)) {
                    /* */
                    if($event!="changed") {
                        $previousStatus = null;
                    }
                    $this->repository->sendNotificationStatusChanged($ticket->assignedTo, $ticket, $currentStatus, $previousStatus, $event);
                } else {
                    if($ticket->due_date < date('Y-m-d')) {
                        $event = "overdue";
                        $this->repository->sendNotificationStatusChanged($ticket->assignedTo, $ticket, $currentStatus, null, $event);
                    }
                }

                return json_encode([
                    'data'=> $ticket,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TicketController.updateDetails.db_transaction.'
                ]);
            }
        }
    }

    public function loadAttachments($ticket_id, Request $request)
    {
        $documents = StoreDocument::query()->Tickets()->where('parent_id', $ticket_id)->get();
        $data = [];
        foreach($documents as $d)
        {
            $array = $d->toArray();
            $array['url'] = Storage::disk('s3')->temporaryUrl(
                $d->path.$d->name,
                now()->addMinutes(30)
            );
            $data[] = $array;
        }
        if($request->ajax()){
            return json_encode([
                'data'=> $data,
                'status'=>'success',
                'message'=>'Record has been retrieved.'
            ]); 
        }

        return $data;
    }

    public function loadComments($ticket_id, Request $request)
    {
        $comments = TicketComment::with('user.employee')->where('ticket_id', $ticket_id)->get();
        $data = [];
        foreach($comments as $c)
        {
            $array1 = $c->toArray();
            $array1['formatted_pst_created_at'] = $c->formatted_pst_created_at;

            $documents = $c->documents;
            $array1['documents'] = [];

            foreach($documents as $d) {
                $array = $d->toArray();
                $array['url'] = Storage::disk('s3')->temporaryUrl(
                    $d->path.$d->name,
                    now()->addMinutes(30)
                );
                $array1['documents'][] = $array;
            }
            $data[] = $array1;
        }
        if($request->ajax()){
            return json_encode([
                'data'=> $data,
                'status'=>'success',
                'message'=>'Record has been retrieved.'
            ]); 
        }

        return $data;
    }

    public function storeAttachments(Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();

                $attachments = $this->repository->storeAttachments($request);
                DB::commit();

                return json_encode([
                    'data'=> $attachments,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TicketController.storeAttachments.db_transaction.'
                ]);
            }
        }
    }

    /**
     * Add comment the specified resource in storage.
     */
    public function storeComment(Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();

                $resComment = $this->repository->storeComment($request);
                DB::commit();

                return json_encode([
                    'data'=> $resComment,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TicketController.storeComment.db_transaction.'
                ]);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($id, Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();

                $ticket = $this->repository->store($request, $id);
                DB::commit();

                return json_encode([
                    'data'=> [
                        $ticket
                    ],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TicketController.store.db_transaction.'
                ]);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();

                $this->repository->update($request, $id);
                DB::commit();

                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TicketController.update.db_transaction.'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                DB::beginTransaction();

                $this->repository->delete($request->id);

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
                    'message' => 'Something went wrong in TicketController.delete.db_transaction'
                ]);
            }
        }
    }
}
