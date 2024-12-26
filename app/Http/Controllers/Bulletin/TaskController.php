<?php

namespace App\Http\Controllers\Bulletin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Task;
use App\Models\StoreDocument;
use App\Models\StoreStatus;
use App\Http\Controllers\Controller;
use App\Interfaces\ITaskRepository;
use App\Models\StoreDocumentTagTask;
use App\Models\TaskComment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    private ITaskRepository $repository;

    private $task;
    public $relationships = ['priorityStatus', 'assignedTo', 'drugOrder' ,'supplyOrder', 'inmar', 'clinicalOrder'];
    public function __construct(
        Task $task
        ,   ITaskRepository $repository
    ) {
        $this->task = $task;
        $this->repository = $repository;

        $this->middleware('permission:menu_store.bulletin.task_reminders.index|menu_store.bulletin.task_reminders.create|menu_store.bulletin.task_reminders.update|menu_store.bulletin.task_reminders.delete|menu_store.bulletin.task_reminders.view_all');
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id) {
        try {
            $this->checkStorePermission($id);

            $years = $this->getYears();
            $months = $this->getMonths();
            $currentYear = now()->year;
            $currentMonth = now()->month;
            $breadCrumb = ['Bulletin', 'Tasks'];

            return view('/stores/bulletin/tasks/index', [
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

    private function taskStatuses() {
        return [
            '201' => 'ToDo',
            '202' => 'InProgress',
            '203' => 'ToAnalyze',
            '204' => 'ToVerify',
            '205' => 'Waiting',
            '206' => 'Complete',
        ];
    }

    private function orderStatuses() {
        return [
            '701' => 'NewRequests',
            '702' => 'Received',
            '703' => 'InTransit',
            '704' => 'Submitted',
            '705' => 'MissingOrder',
            '706' => 'Completed',
        ];
    }

    public function filteredData(Request $request) {
        if ($request->ajax()) {
            $selectedYear = $request->input('year');
            $selectedMonth = $request->input('month');

            $taskStatuses = $this->taskStatuses();
            $orderStatuses = $this->orderStatuses();
            
            $data = [
                'selectedYear' => $selectedYear,
                'selectedMonth' => $selectedMonth,
            ];

            foreach ($taskStatuses as $statusId => $statusName) {
                $data['task' . $statusId] = $this->getTasksByStatusId($statusId, $selectedYear, $selectedMonth);
            }

            foreach ($orderStatuses as $statusId => $statusName) {
                $data['order' . $statusId] = $this->getTasksByStatusId($statusId, $selectedYear, $selectedMonth);
            }
            return response()->json(['data' => $data], 200);
        }
    }

    private function getTasksByStatusId($statusId, $year = null, $month = null) {
        $query = Task::with($this->relationships)
            ->where('status_id', $statusId)
            ->where('is_archived', 0);

        if ($year && $month) {
            $query->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);
        } elseif ($year) {
            $query->whereYear('created_at', $year);
        } elseif ($month) {
            $query->whereMonth('created_at', $month);
        }

        $values = $query->latest()->get();
        $newData = [];
        foreach ($values as $value) {
            $newData[] = [
            'id' => $value->id,
            'subject' => $value->subject,
            'due_date' => $value->due_date,
            'drug_orders' => $value->drugOrder ?? null,
            'supply_orders' => $value->supplyOrder ?? null,
            'inmars' => $value->inmar ?? null,
            'clinical_orders' => $value->clinicalOrder ?? null,
            'assigned_to' => $value->assignedTo->firstname.' '.$value->assignedTo->lastname,
            'image' => $value->assignedTo->image,
            'initials' => $value->assignedTo->firstname[0].$value->assignedTo->lastname[0],
            'initials_random_color' => $value->assignedTo->initials_random_color,
            'priority_name' => $value->priorityStatus->name,
            'priority_color' => $value->priorityStatus->color
            ];
        }
        return $newData;
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
    public function show($id, $tix_id, Request $request)
    {
        if($request->ajax()){
            $relation = 'drugOrder';
            if($request->has('relation')) {
                $relation = $request->relation;
            }
            $data = $this->repository->getTaskDataById($tix_id, $relation);
            return response()->json($data, 200);
        }

        $task = $this->task->with('user.employee', 'status', 'assignedTo')->findOrFail($tix_id);
        $title = 'Details';//'Tix #'.sprintf("%06d", $tix_id);

        $breadCrumb = ['Bulletin', 'Tasks', $title];
        $breadCrumb['back'] = "/store/bulletin/$task->pharmacy_store_id/task-reminders";
        return view('/stores/bulletin/tasks/view', compact('breadCrumb', 'task'));
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

                $taskWatcher = DB::table('task_watchers')->insertOrIgnore([
                    'task_id' => $request->task_id,
                    'employee_id' => $request->employee_id,
                ]);
                
                DB::commit();

                return json_encode([
                    'data'=> [
                        $taskWatcher
                    ],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TaskController.addWatcher.db_transaction.'
                ]);
            }
        }
    }

    public function deleteWatcher(Request $request)
    {   
        if($request->ajax()){
            try {
                DB::beginTransaction();

                $taskWatcher = DB::table('task_watchers')
                    ->where('task_id', $request->task_id)
                    ->where('employee_id', $request->employee_id)
                    ->delete();
                
                DB::commit();

                return json_encode([
                    'data'=> [
                        $taskWatcher
                    ],
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TaskController.deleteWatcher.db_transaction.'
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

                $this->repository->store($request, $id);
                DB::commit();

                return json_encode([
                    'data'=> [],
                    'status'=> 'success',
                    'message'=> 'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TaskController.store.db_transaction.'
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
                    'message' => 'Something went wrong in TaskController.update.db_transaction.'
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
                    'message' => 'Something went wrong in TaskController.delete.'
                ]);
            }
        }
    }

    public function archive(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                DB::beginTransaction();

                $count = $this->repository->archive($request);

                DB::commit();

                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>$count.' Record/s has been archived.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TaskController.archive.'
                ]);
            }
        }
    }

    public function unarchive(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                DB::beginTransaction();

                $count = $this->repository->unarchive($request);

                DB::commit();

                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=> $count.' Record/s has been unarchived.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TaskController.unarchive.'
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
                $task = Task::with('assignedTo'
                    , 'user.employee'
                    , 'status'
                    , 'priorityStatus'
                    , 'watchers'
                    , 'drugOrder'
                    , 'supplyOrder'
                    , 'inmar'
                    , 'clinicalOrder'
                )->findOrFail($id);
                $previousStatus = $task->status;

                $data = $request->except('id');
                foreach($data as $field => $value) {
                    $task->$field = $value;
                    $event = $this->repository->getEventByColumn($field);

                    if($field == 'status_id') {
                        $currentStatus = StoreStatus::findOrFail($value);

                        $value = (int) $value;
                        if($value > 699 && $value < 800) {
                            $drugOrder = isset($task->drugOrder->id) ? $task->drugOrder : null;
                            $supplyOrder = isset($task->supplyOrder->id) ? $task->supplyOrder : null;
                            $inmarReturn = isset($task->inmar->id) ? $task->inmar : null;
                            $clinicalOrder = isset($task->clinicalOrder->id) ? $task->clinicalOrder : null;

                            if(!empty($drugOrder)) {
                                $drugOrder->status_id = $value;
                                $drugOrder->save();
                            }
                            if(!empty($supplyOrder)) {
                                $supplyOrder->status_id = $value;
                                $supplyOrder->save();
                            }
                            if(!empty($inmarReturn)) {
                                $inmarReturn->status_id = $value;
                                $inmarReturn->save();
                            }
                            if(!empty($clinicalOrder)) {
                                $clinicalOrder->status_id = $value;
                                $clinicalOrder->save();
                            }
                        }

                        if($value == 206 || $value == 706) {
                            $task->completed_by = auth()->user()->id;
                            $task->completed_at = Carbon::now();
                        }
                    } else {
                        $currentStatus = $previousStatus;
                    }
                }
                $task->save();
                
                DB::commit();

                if(!empty($event)) {
                    /* */
                    if($event!="changed") {
                        $previousStatus = null;
                    }
                    $this->repository->sendNotificationStatusChanged($task->assignedTo, $task, $currentStatus, $previousStatus, $event);
                } else {
                    if($task->due_date < date('Y-m-d')) {
                        $event = "overdue";
                        $this->repository->sendNotificationStatusChanged($task->assignedTo, $task, $currentStatus, null, $event);
                    }
                }

                return json_encode([
                    'data'=> $task,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TaskController.updateDetails.db_transaction.'
                ]);
            }
        }
    }

    public function loadAttachments($task_id, Request $request)
    {
        $documents = StoreDocument::query()->Tasks()->where('parent_id', $task_id)->get();
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

        $storeDocumentTagTask = StoreDocumentTagTask::with('documentTag.documents')->where('task_id', $task_id)->get();

        foreach($storeDocumentTagTask as $s) {
            $documents = $s->documentTag->documents ?? [];

            foreach($documents as $d)
            {
                $array = $d->toArray();
                $array['url'] = Storage::disk('s3')->temporaryUrl(
                    $d->path.$d->name,
                    now()->addMinutes(30)
                );
                $data[] = $array;
            }
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

    public function loadComments($task_id, Request $request)
    {
        $comments = TaskComment::with('user.employee')->where('task_id', $task_id)->get();
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
                    'message' => 'Something went wrong in TaskController.storeAttachments.db_transaction.'
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
                    'message' => 'Something went wrong in TaskController.storeComment.db_transaction.'
                ]);
            }
        }
    }


    public function loadTask($task_id, Request $request)
    {
        $task = Task::with(
            'assignedTo',
            'user.employee',
            'completedBy.employee',
            'documents',
            'status',
            'priorityStatus',
            'drugOrder.file',
            'drugOrder.itemsImported',
            'drugOrder.status',
            // 'supplyOrder.file',
            'supplyOrder.items',
            'supplyOrder.status',
            // 'inmar.file',
            'inmar.items',
            'inmar.status',
            // 'clinicalOrder.file',
            'clinicalOrder.items',
            'clinicalOrder.status',
            'watchers'
        )->where('id',$task_id)->first();

        $data = [
            'item' => null,
            'custom' => []
        ];

        if(isset($task->id)) {
            $data['item'] = $task;

            $type = 'task';
            $status_type = 'task';
            $drugOrder = isset($task->drugOrder->itemsImported) ? $task->drugOrder : [];
            $supplyOrder = isset($task->supplyOrder->items) ? $task->supplyOrder : [];
            $inmar = isset($task->inmar->items) ? $task->inmar : [];
            $clinicalOrder = isset($task->clinicalOrder->items) ? $task->clinicalOrder : [];
            if(!empty($drugOrder)) {
                $type = 'drug_order';
                $status_type = 'procurement_order';
            }
            if(!empty($supplyOrder)) {
                $type = 'supply_order';
                $status_type = 'procurement_order';
            }
            if(!empty($inmar)) {
                $type = 'inmar_return';
                $status_type = 'procurement_order';
            }
            if(!empty($clinicalOrder)) {
                $type = 'clinical_order';
                $status_type = 'procurement_order';
            }
            
            $watchers = $task->watchers;
            $watcherList = '';
            $watcherListImage = '';
            $watcherListInitial = '';

            foreach($watchers as $watcher) {
                $watcher_fullname = $watcher->firstname.' '.$watcher->lastname;
                if(!empty($watcher->image)) {
                    $watcherListImage .= '<img src="/upload/userprofile/'.$watcher->image.'" width="35" height="35" class="rounded-circle" alt="" title="'.$watcher_fullname.'"/>';
                } else {
                    $watcherListInitial .= '<div class="user-plus employee-avatar-'.$watcher->initials_random_color.'-initials" style="border: 1px dotted #fff;" title="'.$watcher_fullname.'">'.strtoupper(substr($watcher->firstname, 0, 1)).strtoupper(substr($watcher->lastname, 0, 1)).'</div>';
                }
            }

            $watcherList = '
                <div class="d-flex align-items-center ms-2" onclick="showTaskWatcherModal('.$task->id.')">
                    <div class="user-groups">
                        '.$watcherListImage.'
                    </div>
                    '.$watcherListInitial.'
                    <div class="user-plus" style="width: 35px !important; height: 35px !important;" title="Update Watchers">+</div>
                </div>
            ';

            $due_date = $task->due_date;
            $formatted_due_date = !empty($due_date) ? date('M d, Y', strtotime($due_date)) : '';

            if($due_date < date('Y-m-d') && !empty($formatted_due_date)) {
                if(strtolower($task->status->name) != "completed") {
                    $formatted_due_date = '<span class="text-danger">'.$formatted_due_date.'<i class="fa fa-warning ms-2"></i></span>';
                }
            }

            $data['custom'] = [
                'watcherList' => $watcherList,
                'formatted_due_date' => $formatted_due_date,
                'type' => $type,
                'status_type' => $status_type
            ];
        }

        if($request->ajax()) {
            return json_encode($data);
        }

        return $data;

    }
}
