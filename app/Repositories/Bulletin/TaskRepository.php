<?php

namespace App\Repositories\Bulletin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Interfaces\ITaskRepository;
use App\Interfaces\IHistoriesRepository;
use App\Repositories\StoreDocumentRepository;
use App\Models\Task;
use App\Models\StoreDocument;
use App\Models\DocumentTag;
use App\Models\StoreStatus;
use App\Models\DrugOrder;
use App\Models\SupplyOrder;
use App\Models\Inmar;
use App\Models\ClinicalOrder;
use App\Models\Employee;
use App\Models\File as ModelFile;
use App\Http\Utils\FileIconUtil;
use File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Bus;

use App\Jobs\NotifyTaskToAssignee;
use App\Models\StoreDocumentTagTask;
use App\Models\TaskComment;
use App\Models\TaskCommentDocument;
use Carbon\Carbon;
use Exception;

class TaskRepository extends StoreDocumentRepository implements ITaskRepository
{
    use FileIconUtil;

    private $aws_s3_path;
    private $task;
    private $document;
    protected $dataTable = [];
    protected $documentDataTable = [];
    protected IHistoriesRepository $historiesRepository;

    public function __construct(Task $task
        , StoreDocument $document
        , IHistoriesRepository $historiesRepository
    )
    {
        $this->task = $task;
        $this->document = $document;
        $this->historiesRepository = $historiesRepository;
        $this->aws_s3_path = env('AWS_S3_PATH');
    }

    public function search($request)
    {
        $query = $this->task;
        return $query->get();
    }

    public function getDataTable() : array
    {
        return $this->dataTable;
    }

    public function setDataTable($request)
    {
        $permission = 'bulletin.task_reminders';
        if($request->has('permission')) {
            $permission = $request->permission;
        }

        $user = Auth::user();
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? 0;
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        $query = $this->task->with('user.employee', 'documents', 'assignedTo', 'status', 'tags')
            ->join('employees', 'employees.id', '=', 'tasks.assigned_to_employee_id')
            ->select('tasks.*');
        
        // $query = $query->where('assigned_to_employee_id', $user->employee->id)
        //                ->orWhere('user_id', $user->employee->id);
        if(!$user->hasRole('super-admin') && $user->cannot('menu_store.'.$permission.'.view_all')){
            $query = $query->where(function($query) use($user){
                $query->orWhere('tasks.assigned_to_employee_id', $user->employee->id);
                $query->orWhere('tasks.user_id', $user->id);
            });
        }
        // Search //input all searchable fields
        $search = $request->search;
        $query = $query->where(function($query) use ($search){ 
            $query->orWhere('number', 'like', "%".$search."%"); 
            $query->orWhere('subject', 'like', "%".$search."%"); 
            $query->orWhereHas('assignedTo', function($q) use($search){
                $q->where('firstname', 'like', '%'.$search.'%');
                $q->orWhere('lastname', 'like', '%'.$search.'%');
            });
        });
        
        if($request->has('pharmacy_store_id')) {
            $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
        }

        $filterArchive = false;

        if($request->has('status_id')) {
            if(!empty($request->status_id)) {
                if($request->status_id == 'archived') {
                    $query = $query->where('is_archived', 1);
                    $filterArchive = true;
                } else {
                    $query = $query->where('status_id', $request->status_id);
                }
            }
        }

        if($request->has('is_archived') && $filterArchive == false) {
            $query = $query->where('is_archived', $request->is_archived);
        }

        if($request->has('is_auto')) {
            $query = $query->where('is_auto', $request->is_auto);
        }
        
        $orderByCol = $request->columns[$request->order[0]['column']]['name'];

        if($orderByCol == 'assigned_to') {
            $query = $query->orderBy('employees.firstname', $orderBy)
                    ->orderBy('employees.lastname', $orderBy);
        } else {
            $query = $query->orderBy($orderByCol, $orderBy);
        }
        
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $hideU = 'hidden';
        $hideD = 'hidden';
        $hideAll = 'hidden';
        if(auth()->user()->can('menu_store.'.$permission.'.update'))
        {
            $hideU = ''; $hideAll = '';
        }
        if(auth()->user()->can('menu_store.'.$permission.'.delete'))
        {
            $hideD = ''; $hideAll = '';
        }

        $newData = [];

        $orderStatuses = StoreStatus::where('category', 'procurement_order')->get()->keyBy('id')->toArray();
        $taskStatuses = StoreStatus::where('category', 'task')->get()->keyBy('id')->toArray();
        $statuses = [];
        $prioritieStatuses = StoreStatus::where('category', 'priority')->get()->keyBy('id')->toArray();

        foreach ($data as $value) {
            $empName = isset($value->user->employee) ? $value->user->employee->getFullName() : "NA";
            $completedByName = isset($value->completedBy) ? $value->completedBy->employee->getFullName() : "";
            $documents = isset($value->documents) ? $value->documents->all() : [];
            $assignedName = isset($value->assignedTo) ? $value->assignedTo->getFullName() : "NA";
            $status = isset($value->status) ? $value->status : [];
            $priorityStatus = isset($value->priorityStatus) ? $value->priorityStatus : [];
            $drugOrder = isset($value->drugOrder->itemsImported) ? $value->drugOrder : [];
            $supplyOrder = isset($value->supplyOrder->items) ? $value->supplyOrder : [];
            $inmar = isset($value->inmar->items) ? $value->inmar : [];
            $clinicalOrder = isset($value->clinicalOrder->items) ? $value->clinicalOrder : [];
            $emp = $value->user->employee;

            $watchers = isset($value->watchers) ? $value->watchers->all() : [];

            $assignedToInitials = 'NA';
            $assignedToImage  = '';
            $assignedToInitialsRandomColor = 1;
            if(isset($value->assignedTo->id)) {
                $assignedToInitials = strtoupper(substr($value->assignedTo->firstname, 0, 1)).strtoupper(substr($value->assignedTo->lastname, 0, 1));
                $assignedToImage = !empty($value->assignedTo->image) ? $value->assignedTo->image : '';
                $assignedToInitialsRandomColor = $value->assignedTo->initials_random_color;
            }

            if(isset($drugOrder->id) || isset($supplyOrder->id) || isset($inmar->id) || isset($clinicalOrder->id)) {
                $statuses = $orderStatuses;
                $drugOrder->file ?? [];
                $supplyOrder->file ?? [];
                $inmar->file ?? [];
                $clinicalOrder->file ?? [];
            } else {
                $statuses = $taskStatuses;
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
                        <div class="employee-avatar-'.$value->assignedTo->initials_random_color.'-initials hr-employee" data-id="'.$value->assignedTo->id.'">
                        '.strtoupper(substr($value->assignedTo->firstname, 0, 1)).strtoupper(substr($value->assignedTo->lastname, 0, 1)).'
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
                $empColor = empty($emp->initials_random_color) ? 1 : $emp->initials_random_color;
                $empAvatar = '
                    <div class="d-flex">
                        <div class="employee-avatar-'.$empColor.'-initials hr-employee" data-id="'.$emp->id.'">
                        '.strtoupper(substr($emp->firstname, 0, 1)).strtoupper(substr($emp->lastname, 0, 1)).'
                        </div>
                        <p class="font-weight-bold mb-0 ms-3 mt-2">'.$empName.'</p>
                    </div>
                ';
            }

            $completedByAvatar = '';
            if(!empty($completedByName)) {
                if(!empty($value->completedBy->employee->image)) {
                    $completedByAvatar = '
                        <div class="d-flex">
                            <img src="/upload/userprofile/'.$value->completedBy->employee->image.'" width="32" height="32" class="rounded-circle" alt="">
                            <div class="flex-grow-1 ms-3 mt-2">
                                <p class="font-weight-bold mb-0">'.$completedByName.'</p>
                            </div>
                        </div>
                    ';
                } else {
                    $completedByAvatar = '
                        <div class="d-flex">
                            <div class="employee-avatar-'.$value->completedBy->employee->initials_random_color.'-initials hr-employee" data-id="'.$value->completedBy->id.'">
                            '.strtoupper(substr($value->completedBy->employee->firstname, 0, 1)).strtoupper(substr($value->completedBy->employee->lastname, 0, 1)).'
                            </div>
                            <p class="font-weight-bold mb-0 ms-3 mt-2">'.$completedByName.'</p>
                        </div>
                    ';
                }
            }

            $tags = $value->tags ?? [];
            $due_date = $value->due_date;
            $formatted_due_date = !empty($due_date) ? date('M d, Y', strtotime($due_date)) : '';

            if($due_date < date('Y-m-d') && !empty($formatted_due_date)) {
                if(strtolower($value->status->name) != "completed") {
                    $formatted_due_date = '<span class="text-danger">'.$formatted_due_date.'<i class="fa fa-warning ms-2"></i></span>';
                }
            }

            $watcherList = '';
            $watcherListImage = '';
            $watcherListInitial = '';

            foreach($watchers as $watcher) {
                $watcher_fullname = $watcher->firstname.' '.$watcher->lastname;
                if(!empty($watcher->image)) {
                    $watcherListImage .= '<img src="/upload/userprofile/'.$watcher->image.'" width="35" height="35" class="rounded-circle" alt="" title="'.$watcher_fullname.'"/>';
                } else {
                    $watcherListInitial .= '<div class="user-plus employee-avatar-'.$watcher->initials_random_color.'-initials" style="border: 1px dotted #fff;" title="'.$watcher_fullname.'"  onclick="showTaskWatcherModal('.$value->id.')">'.strtoupper(substr($watcher->firstname, 0, 1)).strtoupper(substr($watcher->lastname, 0, 1)).'</div>';
                }
            }

            $watcherList = '
                <div class="d-flex align-items-center ms-2">
                    <div class="user-groups" onclick="showTaskWatcherModal('.$value->id.')">
                        '.$watcherListImage.'
                    </div>
                    '.$watcherListInitial.'
                    <div class="user-plus" style="width: 35px !important; height: 35px !important;" title="Update Watchers"  onclick="showTaskWatcherModal('.$value->id.')">+</div>
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
                'formatted_pst_completed_at' => !empty($value->pst_completed_at) ? date('M d, Y g:i A', strtotime($value->pst_completed_at)) : '',
                'is_archived' => $value->is_archived,
                'id' => $value->id,
                'subject' => $value->subject,
                'assigned_to_employee_id' => $value->assigned_to_employee_id,
                'assigned_to' => $assignedName,
                'avatar' => $avatar,
                'empAvatar' => $empAvatar,
                'completedByAvatar' => $completedByAvatar,
                'description' => $value->description,
                'status_id' => $value->status_id,
                'due_date' => $due_date,
                'formatted_due_date' => $formatted_due_date,
                'created_at' => $value->created_at->format('M d, Y h:iA'),
                'created_by' => $empName,
                'completed_by' => $completedByName,
                'completed_at' => !empty($value->completed_at) ? date('M d, Y h:iA', strtotime($value->completed_at)) : '',
                'documents' => $documents,
                'drugOrder' => $drugOrder,
                'supplyOrder' => $supplyOrder,
                'inmar' => $inmar,
                'clinicalOrder' => $clinicalOrder,
                'orderStatuses' => $orderStatuses,
                'tags' => $tags,
                'watchers' => $watchers,
                'watcherList' => $watcherList,
                'priorityStatus' =>  '
                    <button style="min-width: 75px;" type="button" class="btn border-0 btn-outline-'.$priorityStatus->class.' btn-sm px-3" ><small><i class="fa fa-flag me-2"></i>'.$priorityStatus->name.'</small></button>',
                'status' =>  '
                    <button style="min-width: 150px;" type="button" onclick="clickStatusBtn(' . $value->id . ')" class="btn btn-'.$status->class.' btn-sm px-3" ><small>'.$status->name.'</small></button>',
                'actions' =>  '<div class="d-flex order-actions" '.$hideAll.'>
                    <button 
                        data-subject="'.htmlspecialchars(addslashes($value->subject)).'" 
                        data-description="'.htmlspecialchars(addslashes($value->description)).'" 
                        data-id="'.$value->id.'"
                        data-due_date="'.$value->due_date.'"
                        data-formatted_due_date="'.htmlspecialchars(addslashes($value->formatted_due_date)).'"
                        data-status_id="'.$value->status_id.'"
                        data-priority_status_id="'.$value->priority_status_id.'"
                        data-assigned_to_employee_id="'.$value->assigned_to_employee_id.'"
                        data-assigned_to="'.addslashes($assignedName).'" 
                        data-assigned_to_initials="'.addslashes($assignedToInitials).'" 
                        data-assigned_to_image="'.addslashes($assignedToImage).'" 
                        data-assigned_to_initials_random_color="'.addslashes($assignedToInitialsRandomColor).'" 
                        data-tags-array="'.htmlspecialchars(json_encode($tags)).'"
                        data-drug-order-array="'.htmlspecialchars(json_encode($drugOrder)).'"
                        data-supply-order-array="'.htmlspecialchars(json_encode($supplyOrder)).'"
                        data-inmar-array="'.htmlspecialchars(json_encode($inmar)).'"
                        data-clinical-order-array="'.htmlspecialchars(json_encode($clinicalOrder)).'"
                        data-documents-array="'.htmlspecialchars(json_encode($documents)).'"
                        data-status-array="'.htmlspecialchars(json_encode($status)).'"
                        data-statuses-array="'.htmlspecialchars(json_encode($statuses)).'"
                        data-priority-status-array="'.htmlspecialchars(json_encode($prioritieStatuses)).'"
                        data-priority-array="'.htmlspecialchars(json_encode($priorityStatus)).'"
                        data-watcher_list="'.htmlspecialchars($watcherList).'" 
                        data-array="'.htmlspecialchars(json_encode($value->toArray())).'"
                        id="task-edit-btn-'.$value->id.'"
                    type="button" class="btn btn-primary btn-sm me-2" onclick="showTaskEditModal(' . $value->id . ')" '.$hideU.'><i class="fa-solid fa-pencil"></i></button>

                    <button type="button" onclick="clickDeleteBtn(' . $value->id . ')" class="btn btn-danger btn-sm"  '.$hideD.'><i class="fa-solid fa-trash-can"></i></button>

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

    public function getTaskDataById($id, $relation = null)
    {
        $includes = ['user.employee', 'documents', 'assignedTo', 'status'];
        if(!empty($relation)) {
            $includes[] = $relation;
        }
        $task = $this->task->with($includes)->findOrFail($id);
        if(isset($task->id)) {

            $orderStatuses = StoreStatus::where('category', 'procurement_order')->get()->keyBy('id')->toArray();
            $taskStatuses = StoreStatus::where('category', 'task')->get()->keyBy('id')->toArray();
            $prioritieStatuses = StoreStatus::where('category', 'priority')->get()->keyBy('id')->toArray();

            $initials = strtoupper(substr($task->assignedTo->firstname, 0, 1)).strtoupper(substr($task->assignedTo->lastname, 0, 1));
            $fullname = $task->assignedTo->firstname.' '.$task->assignedTo->lastname;
            $drugOrder = isset($task->drugOrder) ? $task->drugOrder : [];

            if(isset($drugOrder->id)) {
                $statuses = $orderStatuses;
            } else {
                $statuses = $taskStatuses;
            }

            return [
                'subject' => htmlspecialchars(addslashes($task->subject))
                , 'description' => addslashes($task->description)
                , 'id' => htmlspecialchars($task->id)
                , 'status_id' => htmlspecialchars($task->status_id)
                , 'priority_status_id' => htmlspecialchars($task->priority_status_id)
                , 'assigned_to_employee_id' => htmlspecialchars($task->assigned_to_employee_id)
                , 'assigned_to' => htmlspecialchars($fullname)
                , 'assigned_to_initials' => htmlspecialchars($initials)
                , 'assigned_to_initials_random_color' => $task->assignedTo->initials_random_color
                , 'assigned_to_image' => htmlspecialchars($task->assignedTo->image)
                , 'drugOrder' => $drugOrder
                , 'documents' => $task->documents
                , 'status' => $task->status
                , 'statuses' => $statuses
                , 'prioritieStatus' => $prioritieStatuses
            ];
        }
        return [];
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

        $task = new Task();
        $task->subject = $data->subject;
        $task->description = $data->description;
        if(!empty($data->due_date)) {
            $task->due_date = $data->due_date;
        }
        $task->pharmacy_store_id = $pharmacy_store_id;
        $task->user_id = auth()->user()->id;
        $status_id = 201;

        if(isset($data->assigned_to_employee_id) && !empty($data->assigned_to_employee_id)) {
            $task->assigned_to_employee_id = $data->assigned_to_employee_id;
        }
        if(isset($data->status_id)) {
            $status_id = $data->status_id;

            if($data->status_id == 206 || $data->status_id == 706) {
                $task->completed_by = auth()->user()->id;
                $task->completed_at = Carbon::now();
            }
        }
        $task->status_id = $status_id;

        $save = $task->save();

        $document = [];
        if($save) {
            $pathUpload = $this->pathUpload($task->pharmacy_store_id, $task->id);
        
            if ($request->file('files')) {
                $files = $request->file('files');
                foreach ($files as $key => $file) {

                    // $document = new StoreDocument;
                    // $document->user_id = auth()->user()->id;
                    // $document->parent_id = $task->id;
                    // $document->category = 'task';
                    // $document->ext = $file->getClientOriginalExtension();

                    // @unlink(public_path($pathUpload.'/'.$document->path));
                    // $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).' imported_'.date('Ymd').'-'.$key.'.'.$file->getClientOriginalExtension();
                    // $file->move(public_path($pathUpload), $fileName);
                    // $document->path = '/'.$pathUpload.'/'.$fileName;
                    // $path = '/'.$pathUpload.'/'.$fileName;

                    // $save = $document->save();                    

                    $document = new StoreDocument;
                    $document->user_id = auth()->user()->id;
                    $document->parent_id = $task->id;
                    $document->category = 'task';
                    
                    $document->name = $file->getClientOriginalName();
                    $document->ext = $file->getClientOriginalExtension();
                    $document->mime_type = $file->getMimeType();
                    $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
                    $document->size = $file->getSize()/1024;
                    $document->size_type = 'KB';

                    $date = date('YmdHis').'-'.rand(10,99);
                    $path = "/$this->aws_s3_path/stores/$pharmacy_store_id/bulletin/tasks/$task->id/$date";
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

                    if(isset($data->document_tags)) {
                        $tags = $data->document_tags;
                        foreach($tags as $k => $tag_id) {
                            $documentTag = new DocumentTag();
                            $documentTag->document_id = $document->id;
                            $documentTag->tag_id = $tag_id;
                            $documentTag->document_type = 'task';
                            $documentTag->tag_type = 'audit';
                            $documentTag->save();
                        }
                    }
                }
            }
        } else {
            $flag = false;
        }
        
        if(!$flag) {
            throw "Not saved";
        }

        $this->sendNotificationStatusChanged($task->assignedTo, $task, $task->status);
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

        $task = $this->task->with('tags')->findOrFail($data->id);
        $taskOld = $task;
        $previousStatus = $task->status;
        $previousTask = [
            'id' => $task->id,
            'assigned_to_employee_id' => $task->assigned_to_employee_id,
            'status_id' => $task->status_id,
            'priority_status_id' => $task->priority_status_id,
            'due_date' => $task->due_date,
            'subject' => $task->subject,
            'description' => $task->description
        ];
        $task_new = [];
        $document = [];
        $relation = [];
        $type_name = 'task';
        $task->subject = $data->subject;
        $task->description = $data->description;

        // $documents = $task->documents()->count(); 
        
        if(isset($data->assigned_to_employee_id) && !empty($data->assigned_to_employee_id)) {
            $task->assigned_to_employee_id = $data->assigned_to_employee_id;
        }
        if(isset($data->status_id)) {
            $task->status_id = $data->status_id;

            if($data->status_id == 206 || $data->status_id == 706) {
                $task->completed_by = auth()->user()->id;
                $task->completed_at = Carbon::now();
            }
        }
        if(isset($data->priority_status_id)) {
            $task->priority_status_id = $data->priority_status_id;
        }
        if(isset($data->due_date) && !empty($data->due_date)) {
            $task->due_date = date('Y-m-d', strtotime($data->due_date));
        }

        $save = $task->save();
        
        if($save) {
            $task_new = $task;
            $pathUpload = $this->pathUpload($task->pharmacy_store_id, $task->id);
        
            if ($request->file('files')) {
                $files = $request->file('files');
                foreach ($files as $key => $file) {

                    $document = new StoreDocument;
                    $document->user_id = auth()->user()->id;
                    $document->parent_id = $task->id;
                    $document->category = 'task';
                    $document->ext = $file->getClientOriginalExtension();

                    @unlink(public_path($pathUpload.'/'.$document->path));
                    $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).' imported_'.date('Ymd').'-'.$key.'.'.$file->getClientOriginalExtension();
                    $file->move(public_path($pathUpload), $fileName);
                    $document->path = '/'.$pathUpload.'/'.$fileName;
                    $path = '/'.$pathUpload.'/'.$fileName;

                    $save = $document->save();

                    if(!$save) {
                        $flag = false;
                    }

                    if(!empty($task->tags)) {
                        foreach($task->tags as $t) {
                            $documentTag = new DocumentTag();
                            $documentTag->document_id = $document->id;
                            $documentTag->tag_id = $t->id;
                            $documentTag->document_type = 'task';
                            $documentTag->tag_type = $t->type;
                            $documentTag->save();
                        }
                    }

                }
            }
        } else {
            $flag = false;
        }
        $type_parent_id = $task->id;
        // HAS ONE relation to DRUG ORDER
        if(isset($data->drugOrder->id)) {
            $order = $data->drugOrder;
            $drugOrder = DrugOrder::findOrFail($order->id);
            if(isset($drugOrder->id)) {
                $drugOrder->status_id = $order->status_id;
                $drugOrder->save();
            }
            $relation = [
                'name' => 'drugOrder',
                'data' => $drugOrder,
            ];
            $type_name = 'drug_order';
            $type_parent_id = $drugOrder->id;
        }
        // HAS ONE relation to SUPPLY ORDER
        if(isset($data->supplyOrder->id)) {
            $order = $data->supplyOrder;
            $supplyOrder = SupplyOrder::findOrFail($order->id);
            if(isset($supplyOrder->id)) {
                $supplyOrder->status_id = $order->status_id;
                $supplyOrder->save();
            }
            $relation = [
                'name' => 'supplyOrder',
                'data' => $supplyOrder,
            ];
            $type_name = 'supply_order';
            $type_parent_id = $supplyOrder->id;
        }
        // HAS ONE relation to INMAR RETURN
        if(isset($data->inmarReturn->id)) {
            $return = $data->inmarReturn;
            $inmarReturn = Inmar::findOrFail($return->id);
            if(isset($inmarReturn->id)) {
                $inmarReturn->status_id = $return->status_id;
                $inmarReturn->save();
            }
            $relation = [
                'name' => 'inmarReturn',
                'data' => $inmarReturn,
            ];
            $type_name = 'inmar_return';
            $type_parent_id = $inmarReturn->id;
        }
        // HAS ONE relation to CLINICAL ORDER
        if(isset($data->clinicalOrder->id)) {
            $order = $data->clinicalOrder;
            $clinicalOrder = ClinicalOrder::findOrFail($order->id);
            if(isset($clinicalOrder->id)) {
                $clinicalOrder->status_id = $order->status_id;
                $clinicalOrder->save();
            }
            $relation = [
                'name' => 'clinicalOrder',
                'data' => $clinicalOrder,
            ];
            $type_name = 'clinical_order';
            $type_parent_id = $clinicalOrder->id;
        }

        /**start sending mail */;
        $event = "changed";
        $currentStatus = StoreStatus::findOrFail($task->status_id);
        if($task->due_date < date('Y-m-d')) {
            $event = "overdue";
        }
        if($task->description != $previousTask['description']) {
            $event = "description";
        }
        if($task->priority_status_id != $previousTask['priority_status_id']) {
            $event = "priority";
        }
        if($task->assigned_to_employee_id != $previousTask['assigned_to_employee_id']) {
            $event = "re-assigned";
        }

        // if changed status
        if($task->status_id != $previousStatus->id) {
            $event = "changed";
            $this->sendNotificationStatusChanged($task->assignedTo, $task, $currentStatus, $previousStatus, $event);
        } else {
            if($event!="changed") {
                $this->sendNotificationStatusChanged($task->assignedTo, $task, $currentStatus, null, $event);
            }
        }
        /**ended sending mail */
        
        if(!$flag) {           
            throw "Not saved";
        } else {
            //update history
            $history_body = array(
                'task_old' => $taskOld,
                'task_new' => $task_new,
                'document' => $document,
                'relation' => $relation
            );
            $history_header = array(
                'class' => 'TASK',
                'method' => 'UPDATED task ',
                'name' => $taskOld->name,
                'id' => $task->id
            );
            
            $this->historiesRepository->update_historyV2($history_header, $history_body, $type_name, $type_parent_id);
        }
    }

    public function delete($id)
    {
        $task = $this->task->findOrFail($id);

        if(isset($task->drugOrder)) {
            $task->drugOrder->delete();
        }
        if(isset($task->supplyOrder)) {
            $task->supplyOrder->delete();
        }
        if(isset($task->inmar)) {
            $task->inmar->delete();
        }

        $del = DocumentTag::where('document_id',$task->id)
            ->where('document_type','task')
            ->where('tag_type','audit')
            ->delete();

        $path = $this->pathUpload($task->pharmacy_store_id, $task->id);

        $docs = StoreDocument::where('category', 'task')->where('parent_id', $id)->count();
        if($docs > 0) {
            $this->document->where('category', 'task')->where('parent_id', $id)->delete();
            File::deleteDirectory(public_path('/'.$path));
        }

        $this->sendNotificationStatusChanged($task->assignedTo, $task, $task->status, null, 'deleted');

        $save = $task->delete();

        if(!$save) {
            throw "Not Deleted";
        }
    }

    public function archive($request)
    {
        $selectedIds = $request->selectedIds ?? [];

        if(count($selectedIds) > 0) {
            $save = Task::whereIn('id', $selectedIds)->update(['is_archived' => 1]);
            if(!$save) {
                throw new Exception("Not archived");
            }
        }
        return count($selectedIds);
    }

    public function unarchive($request)
    {
        $selectedIds = $request->selectedIds ?? [];

        if(count($selectedIds) > 0) {
            $save = Task::whereIn('id', $selectedIds)->update(['is_archived' => 0]);
            if(!$save) {
                throw new Exception("Not unarchived");
            }
        }

        return count($selectedIds);
    }

    public function storeDocument($request)
    { 
        $task = $this->task->findOrFail($request->task_id);

        if ($request->file('files')) {
            $pathUpload = $this->pathUpload($task->pharmacy_store_id, $task->id);
            $files = $request->file('files');

            $this->processStoringDocuments($files, $task->id, 'task', $pathUpload);
        }
    }

    public function retrieveRecent($params)
    {
        $user = Auth::user();

        $query = $this->task->with('user.employee', 'documents', 'assignedTo.department', 'status', 'priorityStatus', 'tags', 'watchers')
            ->select('*', DB::raw('TIMESTAMPDIFF(HOUR, created_at, NOW()) AS hours_difference'));
        
        if(!$user->hasRole('super-admin') && $user->cannot('menu_store.bulletin.task_reminders.view_all')){
            $query = $query->where(function($query) use($user){
                $query->orWhere('assigned_to_employee_id', $user->employee->id);
                $query->orWhere('user_id', $user->id);
            });
        }

        if(isset($params['pharmacy_store_id'])) {
            $query = $query->where('pharmacy_store_id', $params['pharmacy_store_id']);
        }

        $status = ['COMPLETED'];
        $query = $query->where(function($query) use ($status){
            $query->whereHas('status', function($query) use ($status) {
                $query->whereNotIn(DB::raw('UPPER(name)'), $status);
            });
        });

        $tagsArr = $this->getMonthlyTagCodesArray();
        $query = $query->where(function($query) use ($tagsArr){
            $query->whereHas('tags', function($query) use ($tagsArr) {
                $query->whereNotIn('code', $tagsArr);
            });
            $query->orDoesntHave('tags');
        });
        
        $query = $query->orderBy('created_at', 'desc');
        $data = $query->limit(15)->get();
        
        $newData = [];

        $orderStatuses = StoreStatus::where('category', 'procurement_order')->get()->keyBy('id')->toArray();
        $taskStatuses = StoreStatus::where('category', 'task')->get()->keyBy('id')->toArray();
        $statuses = [];
        $prioritieStatuses = StoreStatus::where('category', 'priority')->get()->keyBy('id')->toArray();
        
        foreach ($data as $value) {
            $empName = isset($value->user->employee) ? $value->user->employee->getFullName() : "NA";
            $documents = isset($value->documents) ? $value->documents->all() : [];
            $assignedName = isset($value->assignedTo) ? $value->assignedTo->getFullName() : "NA";
            $status = isset($value->status) ? $value->status : [];
            $priorityStatus = isset($value->priorityStatus) ? $value->priorityStatus : [];
            $drugOrder = isset($value->drugOrder->itemsImported) ? $value->drugOrder : [];
            $supplyOrder = isset($value->supplyOrder->items) ? $value->supplyOrder : [];
            $inmar = isset($value->inmar->items) ? $value->inmar : [];
            $clinicalOrder = isset($value->clinicalOrder->items) ? $value->clinicalOrder : [];
            $emp = $value->user->employee;

            $watchers = isset($value->watchers) ? $value->watchers->all() : [];

            $assignedToInitials = 'NA';
            $assignedToImage  = '';
            $assignedToInitialsRandomColor = 1;
            if(isset($value->assignedTo->id)) {
                $assignedToInitials = strtoupper(substr($value->assignedTo->firstname, 0, 1)).strtoupper(substr($value->assignedTo->lastname, 0, 1));
                $assignedToImage = !empty($value->assignedTo->image) ? $value->assignedTo->image : '';
                $assignedToInitialsRandomColor = $value->assignedTo->initials_random_color;
            }

            if(isset($drugOrder->id) || isset($supplyOrder->id) || isset($inmar->id) || isset($clinicalOrder->id)) {
                $drugOrder->file ?? [];
                $supplyOrder->file ?? [];
                $inmar->file ?? [];
                $clinicalOrder->file ?? [];
                $statuses = $orderStatuses;
            } else {
                $statuses = $taskStatuses;
            }

            $tags = $value->tags ?? [];

            $watcherList = '';
            $watcherListImage = '';
            $watcherListInitial = '';

            foreach($watchers as $watcher) {
                $watcher_fullname = $watcher->firstname.' '.$watcher->lastname;
                if(!empty($watcher->image)) {
                    $watcherListImage .= '<img src="/upload/userprofile/'.$watcher->image.'" width="35" height="35" class="rounded-circle" alt="" title="'.$watcher_fullname.'"/>';
                } else {
                    $watcherListInitial .= '<div class="user-plus employee-avatar-'.$watcher->initials_random_color.'-initials" style="border: 1px dotted #fff;" title="'.$watcher_fullname.'"  onclick="showTaskWatcherModal('.$value->id.')">'.strtoupper(substr($watcher->firstname, 0, 1)).strtoupper(substr($watcher->lastname, 0, 1)).'</div>';
                }
            }

            $watcherList = '
                <div class="d-flex align-items-center ms-2">
                    <div class="user-groups" onclick="showTaskWatcherModal('.$value->id.')">
                        '.$watcherListImage.'
                    </div>
                    '.$watcherListInitial.'
                    <div class="user-plus" style="width: 35px !important; height: 35px !important;" title="Update Watchers"  onclick="showTaskWatcherModal('.$value->id.')">+</div>
                </div>
            ';

            $newData[] = [
                'department' => isset($value->assignedTo->department) ? $value->assignedTo->department : null,
                'id' => $value->id,
                'subject' => $value->subject,
                'assigned_to_employee_id' => $value->assigned_to_employee_id,
                'assigned_to' => $assignedName,
                'assignedToImage' => $assignedToImage,
                'assignedToInitials' => $assignedToInitials,
                'assignedToInitialsRandomColor' => $assignedToInitialsRandomColor,
                'description' => $value->description,
                'status_id' => $value->status_id,
                'due_date' => !empty($value->due_date) ? date('M d, Y', strtotime($value->due_date)) : '',
                'created_at' => $value->created_at->format('M d, Y h:iA'),
                'created_by' => $empName,
                'documents' => $documents,
                'drugOrder' => $drugOrder,
                'supplyOrder' => $supplyOrder,
                'inmar' => $inmar,
                'clinicalOrder' => $clinicalOrder,
                'orderStatuses' => $orderStatuses,
                'hours_difference' => $value->hours_difference,
                'status' => $status,
                'priorityStatus' => $priorityStatus,
                'actions' =>  '<div class="d-flex order-actions">
                    <button 
                        data-subject="'.htmlspecialchars(addslashes($value->subject)).'" 
                        data-description="'.htmlspecialchars(addslashes($value->description)).'" 
                        data-id="'.$value->id.'"
                        data-status_id="'.$value->status_id.'"
                        data-priority_status_id="'.$value->priority_status_id.'"
                        data-assigned_to_employee_id="'.$value->assigned_to_employee_id.'"
                        data-assigned_to="'.addslashes($assignedName).'" 
                        data-assigned_to_initials="'.addslashes($assignedToInitials).'" 
                        data-assigned_to_image="'.addslashes($assignedToImage).'" 
                        data-assigned_to_initials_random_color="'.addslashes($assignedToInitialsRandomColor).'" 
                        data-drug-order-array="'.htmlspecialchars(json_encode($drugOrder)).'"
                        data-supply-order-array="'.htmlspecialchars(json_encode($supplyOrder)).'"
                        data-inmar-array="'.htmlspecialchars(json_encode($inmar)).'"
                        data-clinical-order-array="'.htmlspecialchars(json_encode($clinicalOrder)).'"
                        data-documents-array="'.htmlspecialchars(json_encode($documents)).'"
                        data-tags-array="'.htmlspecialchars(json_encode($tags)).'"
                        data-status-array="'.htmlspecialchars(json_encode($status)).'"
                        data-statuses-array="'.htmlspecialchars(json_encode($statuses)).'"
                        data-priority-status-array="'.htmlspecialchars(json_encode($prioritieStatuses)).'"
                        data-priority-array="'.htmlspecialchars(json_encode($priorityStatus)).'"
                        data-watcher_list="'.htmlspecialchars($watcherList).'" 
                        data-array="'.htmlspecialchars(json_encode($value->toArray())).'"
                        id="task-edit-btn-'.$value->id.'"
                    type="button" class="btn btn-primary btn-sm me-2" hidden><i class="fa-solid fa-pencil"></i></button>
                </div>'
            ];
        }
        return $newData;
    }

    public function retrieveRecentMonthlyTasks($params)
    {
        $user = Auth::user();

        $query = $this->task->with('user.employee', 'documents', 'assignedTo', 'status', 'priorityStatus', 'tags')
            ->select('*', DB::raw('TIMESTAMPDIFF(HOUR, created_at, NOW()) AS hours_difference'));

        if(!$user->hasRole('super-admin') && $user->cannot('menu_store.bulletin.task_reminders.view_all')){
            $query = $query->where(function($query) use($user){
                $query->orWhere('assigned_to_employee_id', $user->employee->id);
                $query->orWhere('user_id', $user->id);
            });
        }

        if(isset($params['pharmacy_store_id'])) {
            $query = $query->where('pharmacy_store_id', $params['pharmacy_store_id']);
        }

        $status = ['COMPLETED'];
        $query = $query->where(function($query) use ($status){
            $query->whereHas('status', function($query) use ($status) {
                $query->whereNotIn(DB::raw('UPPER(name)'), $status);
            });
        });

        $tagsArr = $this->getMonthlyTagCodesArray();
        $query = $query->where(function($query) use ($tagsArr){
            $query->whereHas('tags', function($query) use ($tagsArr) {
                $query->whereIn('code', $tagsArr);
            });
        });
        
        $query = $query->orderBy('created_at', 'desc');
        $data = $query->limit(15)->get();

        $newData = [];

        $orderStatuses = StoreStatus::where('category', 'procurement_order')->get()->keyBy('id')->toArray();
        $taskStatuses = StoreStatus::where('category', 'task')->get()->keyBy('id')->toArray();
        $statuses = [];
        $prioritieStatuses = StoreStatus::where('category', 'priority')->get()->keyBy('id')->toArray();

        foreach ($data as $value) {
            $empName = isset($value->user->employee) ? $value->user->employee->getFullName() : "NA";
            $documents = isset($value->documents) ? $value->documents->all() : [];
            $assignedName = isset($value->assignedTo) ? $value->assignedTo->getFullName() : "NA";
            $status = isset($value->status) ? $value->status : [];
            $priorityStatus = isset($value->priorityStatus) ? $value->priorityStatus : [];
            $drugOrder = isset($value->drugOrder->itemsImported) ? $value->drugOrder : [];
            $supplyOrder = isset($value->supplyOrder->items) ? $value->supplyOrder : [];
            $inmar = isset($value->inmar->items) ? $value->inmar : [];
            $clinicalOrder = isset($value->clinicalOrder->items) ? $value->clinicalOrder : [];
            $emp = $value->user->employee;

            $assignedToInitials = 'NA';
            $assignedToImage  = '';
            $assignedToInitialsRandomColor = 1;
            if(isset($value->assignedTo->id)) {
                $assignedToInitials = strtoupper(substr($value->assignedTo->firstname, 0, 1)).strtoupper(substr($value->assignedTo->lastname, 0, 1));
                $assignedToImage = !empty($value->assignedTo->image) ? $value->assignedTo->image : '';
                $assignedToInitialsRandomColor = $value->assignedTo->initials_random_color;
            }

            if(isset($drugOrder->id) || isset($supplyOrder->id) || isset($inmar->id) || isset($clinicalOrder->id)) {
                $drugOrder->file ?? [];
                $supplyOrder->file ?? [];
                $inmar->file ?? [];
                $clinicalOrder->file ?? [];
                $statuses = $orderStatuses;
            } else {
                $statuses = $taskStatuses;
            }

            $tags = $value->tags ?? [];

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
                'due_date' => !empty($value->due_date) ? date('M d, Y', strtotime($value->due_date)) : '',
                'created_at' => $value->created_at->format('M d, Y h:iA'),
                'created_by' => $empName,
                'documents' => $documents,
                'drugOrder' => $drugOrder,
                'supplyOrder' => $supplyOrder,
                'inmar' => $inmar,
                'clinicalOrder' => $clinicalOrder,
                'orderStatuses' => $orderStatuses,
                'hours_difference' => $value->hours_difference,
                'status' => $status,
                'priorityStatus' => $priorityStatus,
                'actions' =>  '<div class="d-flex order-actions">
                    <button 
                        data-subject="'.htmlspecialchars(addslashes($value->subject)).'" 
                        data-description="'.htmlspecialchars(addslashes($value->description)).'" 
                        data-id="'.$value->id.'"
                        data-status_id="'.$value->status_id.'"
                        data-priority_status_id="'.$value->priority_status_id.'"
                        data-assigned_to_employee_id="'.$value->assigned_to_employee_id.'"
                        data-assigned_to="'.addslashes($assignedName).'" 
                        data-assigned_to_initials="'.addslashes($assignedToInitials).'" 
                        data-assigned_to_image="'.addslashes($assignedToImage).'" 
                        data-assigned_to_initials_random_color="'.addslashes($assignedToInitialsRandomColor).'" 
                        data-drug-order-array="'.htmlspecialchars(json_encode($drugOrder)).'"
                        data-supply-order-array="'.htmlspecialchars(json_encode($supplyOrder)).'"
                        data-inmar-array="'.htmlspecialchars(json_encode($inmar)).'"
                        data-clinical-order-array="'.htmlspecialchars(json_encode($clinicalOrder)).'"
                        data-documents-array="'.htmlspecialchars(json_encode($documents)).'"
                        data-tags-array="'.htmlspecialchars(json_encode($tags)).'"
                        data-status-array="'.htmlspecialchars(json_encode($status)).'"
                        data-statuses-array="'.htmlspecialchars(json_encode($statuses)).'"
                        data-priority-status-array="'.htmlspecialchars(json_encode($prioritieStatuses)).'"
                        id="task-edit-btn-'.$value->id.'"
                    type="button" class="btn btn-primary btn-sm me-2" hidden><i class="fa-solid fa-pencil"></i></button>
                </div>'
            ];
        }
        return $newData;
    }

    /**
     * Private functions starts here
     */
    private function pathUpload($pharmacy_store_id, $task_id) : string
    {
        return self::BASE_PATH.'/'.$pharmacy_store_id.'/bulletin/tasks/'.$task_id;
    }

    public function sendNotificationStatusChanged($employee, $task, $currentStatus, $previousStatus = null, $event = "new")
    {
        if(config('mail.maintenance') != "ON") {
            Bus::dispatch(new NotifyTaskToAssignee($employee, $task, $currentStatus, $previousStatus, $event));
        }
    }

    public function sendNotificationOverDue()
    {
        $tasks = Task::whereNotNull('due_date')
            ->where('due_date', "<", date('Y-m-d'))
            ->whereNotIn('status_id', [206,706])
            ->where('is_archive', 0)
            ->get();
        foreach($tasks as $task)
        {
            $this->sendNotificationStatusChanged($task->assignedTo, $task, $task->status, null, 'overdue');
        }
    }

    private function getMonthlyTagCodesArray()
    {
        return ['m_p_dfiqa', 'm_ihs_a_c', 'm_s_a_qa', 'ir_monthly_c2', 'ir_monthly_c3_5'];
    }

    public function createArAgingReportTask($pharmacy_store_id, $assignee)
    {
        $task = new Task();
        $task->subject = 'Send AR Aging Report this Day - '.date('M d, Y');
        $task->description = '<p>This is a reminder to send AR Aging Report</p>';
        $task->user_id = 1;
        $task->assigned_to_employee_id = $assignee;
        $task->pharmacy_store_id = $pharmacy_store_id;
        $task->status_id = 201;
        $task->is_auto = 1;
        if($task->save()) {
            return $task;
        }
        return false;
    }

    public function createNewTask($pharmacy_store_id, $assignee, $user_id, $subject, $due_date = null, $description = null)
    {
        $desc = '<p><i>***This is an auto reminder to send <b>'.$subject.'</b></i></p>';
        if(!empty($description)) {
            $desc .= '<p>'.$description.'</p>';
        }
        $task = new Task();
        $task->subject = $subject;
        $task->description = $desc;
        if(!empty($due_date)) {
            $task->due_date = $due_date;
        }
        $task->user_id = $user_id;
        $task->assigned_to_employee_id = $assignee;
        $task->pharmacy_store_id = $pharmacy_store_id;
        $task->status_id = 201;
        $task->is_auto = 1;
        $task->save();
        return $task;
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
        if($request->has('task_id')) {
            if(!empty($request->task_id)) {
                $task = Task::findOrFail($request->task_id);
                // $watchers = $task->watchers()->pluck('employee_id')->all();
                $watchers = [$task->assigned_to_employee_id];
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

        $updateBtn = '';
        if(auth()->user()->cannot('menu_store.bulletin.task_reminders.update'))
        {
            $updateBtn = 'disabled';
        }

        foreach($data as $d) {
            $id = $d->id;
            $initials = strtoupper(substr($d->firstname, 0, 1)) . strtoupper(substr($d->lastname, 0, 1));
            $image = $d->image;
            $initials_random_color = $d->initials_random_color;
            $fullname = $d->firstname . ' ' . $d->lastname;
            $is_watcher = false;

            $json = ['id' => $d->id, 'firstname' => $d->firstname, 'lastname' => $d->lastname, 'initials' => $initials, 'image' => $image, 'initials_random_color' => $initials_random_color];

            $actions = '<button class="btn btn-sm btn-outline-success ms-auto" 
                    onclick="selectTaskAssignee(
                        '.$request->task_id.', 
                        '.$id.',
                        \''.htmlspecialchars(json_encode($json)).'\')"
                        '.$updateBtn.'
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
        if($request->has('task_id')) {
            if(!empty($request->task_id)) {
                $task = Task::findOrFail($request->task_id);
                $watchers = $task->watchers()->pluck('employee_id')->all();
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

            $actions = '<button class="btn btn-sm btn-success ms-auto" onclick="addTaskWatcher('.$request->task_id.', '.$id.')">Add as Watcher<i class="fa fa-check ms-2"></i></button>';
            if(in_array($id, $watchers)) {
                $actions = '<button class="btn btn-sm btn-danger ms-auto" onclick="deleteTaskWatcher('.$request->task_id.', '.$id.')">Remove Watcher<i class="fa fa-times ms-2"></i></button>';
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

    public function storeComment($request)
    {
        $flag = false;

        $data = json_decode($request->data);

        $task_id = $data->task_id ?? null;
        $comment = $data->comment ?? null;
        $pharmacy_store_id = $data->pharmacy_store_id ?? null;

        $files = [];

        if($request->file('files')) {
            $files = $request->file('files');
        }

        if(!empty($task_id) && !empty($comment)) {
            $flag = true;
        }

        if(!empty($task_id) && empty($comment) && count($files) > 0) {
            $flag = true;
        }

        // if(!empty($task_id) && !empty($comment)) {
        //     $flag = true;
        // }

        if($flag == true)
        {
            $taskComment = new TaskComment();
            $taskComment->task_id = $task_id;
            $taskComment->comment = $comment;
            $taskComment->user_id = auth()->user()->id;
            $taskComment->save();

            $employee = Employee::where('user_id', auth()->user()->id)->first();

            $pathUpload = $this->pathUpload($pharmacy_store_id, $task_id);

            $commentFiles = [];
        
            if ($request->file('files')) {
                foreach ($files as $key => $file) {
                    $document = new StoreDocument;
                    $document->user_id = auth()->user()->id;
                    $document->parent_id = $task_id;
                    $document->category = 'task';
                    
                    $document->name = $file->getClientOriginalName();
                    $document->ext = $file->getClientOriginalExtension();
                    $document->mime_type = $file->getMimeType();
                    $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
                    $document->size = $file->getSize()/1024;
                    $document->size_type = 'KB';

                    $date = date('YmdHis');
                    $path = "/$this->aws_s3_path/stores/$pharmacy_store_id/bulletin/tasks/$task_id/$date";
                    $document->path = $path;

                    $save = $document->save();

                    $commentFiles[] = $document;
                    TaskCommentDocument::insertOrIgnore([
                        'task_comment_id' => $taskComment->id,
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

            $task = Task::with('assignedTo','status','newComment')->find($task_id);
            $currentStatus = $task->status;

            $this->sendNotificationStatusChanged($task->assignedTo, $task, $currentStatus, null, 'comment');

            return [
                'comment' => $taskComment,
                'employee' => $employee,
                'files' => $commentFiles,
                'formatted_created_at' => $taskComment->formatted_pst_created_at
            ];
        }
    }

    public function storeAttachments($request)
    {
        $flag = false;

        $task_id = $request->task_id ?? null;
        $task = Task::findOrFail($task_id);

        if(isset($task->id)) {
            $flag = true;
        }

        $parent_id = $task_id;
        $categoy = 'task';
        // check if has tag
        $storeDocumentTagTask = StoreDocumentTagTask::where('task_id', $task_id)->first();
        if(isset($storeDocumentTagTask->store_document_tag_id)) {
            $parent_id = $storeDocumentTagTask->store_document_tag_id;
            $categoy = 'storeDocumentTag';
        }
        
        $pharmacy_store_id = $task->pharmacy_store_id;
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
                    $document->parent_id = $parent_id;
                    $document->category = $categoy;
                    
                    $document->name = $file->getClientOriginalName();
                    $document->ext = $file->getClientOriginalExtension();
                    $document->mime_type = $file->getMimeType();
                    $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
                    $document->size = $file->getSize()/1024;
                    $document->size_type = 'KB';

                    $document->background_color = '#fcd0b2';
                    $document->border_color = '#fcd0b2';

                    $date = date('YmdHis');
                    $path = "/$this->aws_s3_path/stores/$pharmacy_store_id/bulletin/tasks/$task_id/$date";
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

    public function deleteDocument($id)
    {
        $document = StoreDocument::findOrFail($id);

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

    public function autoArchive()
    {
        $oneWeekAgo = Carbon::now()->subWeek();
        $save = Task::where('completed_at', '<=', $oneWeekAgo)
            ->whereIn('status_id', [706, 206])
            ->update(['is_archived' => 1]);
        return $save;
    }

}