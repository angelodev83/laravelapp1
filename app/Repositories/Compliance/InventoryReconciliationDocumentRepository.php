<?php

namespace App\Repositories\Compliance;

use Illuminate\Http\Request;

use App\Interfaces\IInventoryReconciliationDocumentRepository;
use App\Repositories\BaseStoreRepository;
use App\Models\InventoryReconciliationDocument;
use App\Models\ComplianceDocument;
use App\Models\StoreDocument;
use App\Models\DocumentTag;
use App\Models\Tag;
use App\Http\Utils\FileIconUtil;
use App\Models\StoreDocumentTag;
use App\Models\Task;
use App\Models\TaskTag;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use File;

class InventoryReconciliationDocumentRepository extends BaseStoreRepository implements IInventoryReconciliationDocumentRepository
{
    use FileIconUtil;

    private $document;
    private $complianceDocument;
    private $storeDocument;
    private $documentTag;
    private $tag;
    protected $dataTable = [];
    private $aws_s3_path;

    public function __construct(ComplianceDocument $complianceDocument
        , StoreDocument $storeDocument
        , DocumentTag $documentTag
        , Tag $tag)
    {
        $this->document = $complianceDocument;
        $this->complianceDocument = $complianceDocument;
        $this->storeDocument = $storeDocument;
        $this->documentTag = $documentTag;
        $this->tag = $tag;
        $this->aws_s3_path = env('AWS_S3_PATH');
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
        $orderColumnIndex = $request->order[0]['column'] ?? 3;
        $orderBy = $request->order[0]['dir'] ?? 'desc';
        $orderByCol = $request->columns[$request->order[0]['column']]['name'];

        $query = $this->documentTag
            ->with('selfDocument', 'taskDocument.task', 'taskDocument', 'tag');
        // Search //input all searchable fields
        
        
        if($request->has('tag_code')) {
            $query = $query->where(function($query) use ($request){
                $query->whereHas('tag', function($query) use ($request) {
                    $query->where('code', $request->tag_code);
                });
            });
        }

        $query = $query->where(function($query) use ($request){
            $query->orWhereHas('selfDocument', function($query) use ($request) {
                $query->where('pharmacy_store_id', $request->pharmacy_store_id);
                if(!empty($request->search)) {
                    $query = $query->orWhere(function($query) use ($request){ 
                        $query->where('path', 'like', "%".$request->search."%");   
                        $query->orWhere('name', 'like', "%".$request->search."%");   
                    });
                }
            });
            $query->orWhereHas('taskDocument', function($query) use ($request) {
                $query->whereHas('task', function($query) use ($request) {
                    $query->where('pharmacy_store_id', $request->pharmacy_store_id);
                });
                if(!empty($request->search)) {
                    $query = $query->orWhere(function($query) use ($request){ 
                        $query->where('path', 'like', "%".$request->search."%");   
                        $query->orWhere('name', 'like', "%".$request->search."%");   
                    });
                }
            });
        });

        $query = $query->where(function($query) use ($orderByCol, $orderBy){
            $query->orWhereHas('selfDocument', function($query) use ($orderByCol, $orderBy) {
                if($orderByCol == 'file')
                {
                    // $query->orderByRaw("SUBSTRING_INDEX(path, '/', -1) ".$orderBy);
                    $query->orderBy('name', $orderBy);
                }
            });
            $query->orWhereHas('taskDocument', function($query) use ($orderByCol, $orderBy) {
                if($orderByCol == 'file')
                {
                    // $query->orderByRaw("SUBSTRING_INDEX(paths, '/', -1) ".$orderBy);
                    $query->orderBy('name', $orderBy);
                }
            });
        });

        if($orderByCol !== 'file') {
            $query = $query->orderBy($orderByCol, $orderBy);
        }
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        // dd($data);

        $newData = [];
        
        $icons = $this->styles();

        $hideD = 'hidden';
        if($request->has('tag_code')) {
            $permission = [
                'ir_daily' => 'daily',
                'ir_weekly' => 'weekly',
                'ir_monthly_c2' => 'monthly.c2',
                'ir_monthly_c3_5' => 'monthly.c3_5'
            ];
            if(auth()->user()->can('menu_store.inventory_reconciliation.'.$permission[$request->tag_code].'.delete'))
            {
                $hideD = '';
            }
        }

        foreach ($data as $d) {
            $self = isset($d->selfDocument) ? $d->selfDocument : [];
            $task = isset($d->taskDocument) ? $d->taskDocument : [];
            $value = !empty($self) ? $self : $task;
            $type = !empty($value->ext) ? strtolower($value->ext) : "default";
            $icon = isset($icons[$type]) ? $icons[$type]["icon"] : $icons["default"]["icon"];
            $color = isset($icons[$type]) ? $icons[$type]["color"] : $icons["default"]["color"];
            $empName = isset($value->user->employee) ? $value->user->employee->getFullName() : "NA";
            
            $taskMonth = '';
            $taskDate = '';
            $taskWeek = '';
            $taskTag = [];
            if(isset($task->parent_id)) {
                $taskTag = DB::table('task_tag')->where('task_id', $task->parent_id)->first();
                $day = empty($taskTag->day) ? '01' : $taskTag->day;
                $taskMonth = date("M Y", strtotime($taskTag->year.'-'.$taskTag->month.'-'.$day));
                $taskDate = date("M d, Y", strtotime($taskTag->year.'-'.$taskTag->month.'-'.$day));
                $taskWeek = date("W", strtotime($taskTag->year.'-'.$taskTag->month.'-'.$day));

                // $startDate = date('M d', strtotime($taskTag->year . 'W' . $taskWeek . '1'));
                $startDate = date('M d', strtotime($taskTag->year.'-'.$taskTag->month.'-'.$day));
                $startDateTo = date('M d, Y', strtotime($startDate . ' + 6 days'));
                $taskWeekRange = $startDate.' - '.$startDateTo;
            }
            
            Storage::disk('local')->append('file.txt', json_encode($taskTag));
            $path = $value->path.$value->name;
            $s3Url = Storage::disk('s3')->temporaryUrl(
                $path,
                now()->addMinutes(30)
            );
            $filename = strlen($value->name) > 37 ? (substr($value->name, 0, 37)).'...' : $value->name;
            $createdAt = new DateTime($value->created_at->setTimezone('America/Los_Angeles'));
            $date = $createdAt->format('Y-m-d');
            $time = $createdAt->format('h:i A');
            $newData[] = [
                'id' => $d->id,
                'path' => $value->path,
                'ext' => $value->ext,
                'task_month' => $taskMonth,
                'task_date' => $taskDate,
                'task_week' => $taskWeekRange,
                // 'size' => $value->getFileSizeByType("KB"),
                // 'last_modified' => date("M d, Y h:iA",$value->getLastModified()),
                'created_at' => $value->created_at->format('M d, Y h:iA'),
                'created_by' => $empName,
                'file' => '
                    <a target="_blank" href="'.$s3Url.'" class="text-black">
                        <div class="d-flex align-items-center knowledge-base-file-name" title="'.$value->name.'">
                            <div><i class="bx '.$icon.' me-2 font-24 '.$color.'"></i></div>
                            <div>
                                <div class="font-weight-bold">'.$filename.'</div>
                                <div class="text-body-secondary">'.$empName.' | '.$date.' | '.$time.'</div>
                            </div>
                        </div>
                    </a>
                ',
                'actions' =>  '<div class="d-flex order-actions">
                    <a href="/admin/store-document/download/s3/'.$value->id.'"><button type="button" class="btn btn-primary btn-sm me-2">
                        <i class="fa-solid fa-download"></i>
                    </button></a>       
                    <button type="button" onclick="clickDeleteBtn(' . $value->id . ',' . $d->id . ')" class="btn btn-danger btn-sm me-2" '.$hideD.'><i class="fa-solid fa-trash-can"></i></button>
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
    public function store($request)
    {
        $flag = true;
        $data = json_decode($request->data);

        $pharmacy_store_id = 1;
        $tag_type = 'inventory_reconciliation';
        
        if(isset($data->pharmacy_store_id) && !empty($data->pharmacy_store_id)) {
            $pharmacy_store_id = $data->pharmacy_store_id;
        }
        
        $tag_code = '';
        if(isset($data->tag_code) && !empty($data->tag_code)) {
            $tag_code = $data->tag_code;
        }
        if(isset($data->tag_type) && !empty($data->tag_type)) {
            $tag_type = $data->tag_type;
        }

        $tag_name = 'monthly';
        if(isset($data->tag_name)) {
            $tag_name = !empty($data->tag_name) ? $data->tag_name : $tag_name;
        }
        $tag_name = strtolower($tag_name);

        $parent_id = null;
        $category = 'task';

        $tag_id = null;
        $task = null;
        $date = '';
        $err_text = 'Month Year';
        if(isset($data->month) && !empty($data->month)) {
            $dateSelected = $data->month;
            $dateParts = explode("-", $dateSelected);
            switch($tag_name) {
                case 'daily':
                    $err_text = 'Date';
                    $task = Task::select('tasks.id', 'task_tag.tag_id')
                        ->join('task_tag', 'tasks.id', '=', 'task_tag.task_id')
                        ->join('tags', 'tags.id', '=', 'task_tag.tag_id')
                        ->where('task_tag.year', $dateParts[0])
                        ->where('task_tag.month', $dateParts[1])
                        ->where('task_tag.day', $dateParts[2])
                        ->where('task_tag.name', $tag_name)
                        ->where('tags.code', $tag_code)
                        ->first();
                    $date = date('Ymd', strtotime($dateSelected));

                    if(!isset($task->id)) {
                        $tag = Tag::where('code', $tag_code)->first();
                        if(isset($tag->id)) {
                            $tag_id = $tag->id;
                            $taskTag = TaskTag::whereNull('task_id')
                                        ->where('tag_id', $tag_id)                                        
                                        ->where('year', $dateParts[0])
                                        ->where('month', $dateParts[1])
                                        ->where('day', $dateParts[2])
                                        ->where('name', $tag_name)
                                        ->first();
                            if(!isset($taskTag->id)) {
                                $taskTag = new TaskTag();
                                $taskTag->tag_id = $tag_id;
                                $taskTag->year = $dateParts[0];
                                $taskTag->month = $dateParts[1];
                                $taskTag->day = $dateParts[2];
                                $taskTag->name = $tag_name;
                                $taskTag->save();
                            }
                            
                            $parent_id = $taskTag->id;
                            $category = 'tag';
                        }
                    }
                    
                    break;
                case 'weekly':
                    $err_text = 'Week';
                    $week = date('W', strtotime($dateSelected));
                    $mondayDate = date('Y-m-d', strtotime($dateParts[0] . 'W' . $week . '1'));
                    $task = Task::select('tasks.id', 'task_tag.tag_id')
                        ->join('task_tag', 'tasks.id', '=', 'task_tag.task_id')
                        ->join('tags', 'tags.id', '=', 'task_tag.tag_id')
                        ->where('task_tag.year', $dateParts[0])
                        ->where('task_tag.week', $week)
                        ->where('task_tag.name', $tag_name)
                        ->where('tags.code', $tag_code)
                        ->first();
                    $date = date('Ymd', strtotime($mondayDate));

                    if(!isset($task->id)) {
                        $tag = Tag::where('code', $tag_code)->first();
                        if(isset($tag->id)) {
                            $tag_id = $tag->id;
                            $taskTag = TaskTag::whereNull('task_id')
                                        ->where('tag_id', $tag_id)                                        
                                        ->where('year', $dateParts[0])
                                        ->where('week', $week)
                                        ->where('name', $tag_name)
                                        ->first();
                            if(!isset($taskTag->id)) {
                                $taskTag = new TaskTag();
                                $taskTag->tag_id = $tag_id;
                                $taskTag->year = $dateParts[0];
                                $taskTag->week = $week;
                                $taskTag->name = $tag_name;
                                $taskTag->save();
                            }
                            
                            $parent_id = $taskTag->id;
                            $category = 'tag';
                        }
                    }

                    break;
                case 'monthly':
                    $err_text = 'Month Year';
                    $task = Task::select('tasks.id', 'task_tag.tag_id')
                        ->join('task_tag', 'tasks.id', '=', 'task_tag.task_id')
                        ->join('tags', 'tags.id', '=', 'task_tag.tag_id')
                        ->where('task_tag.year', $dateParts[0])
                        ->where('task_tag.month', $dateParts[1])
                        ->where('task_tag.name', $tag_name)
                        ->where('tags.code', $tag_code)
                        ->first();
                    $date = date('Ymd', strtotime($dateSelected.'-10'));

                    if(!isset($task->id)) {
                        $tag = Tag::where('code', $tag_code)->first();
                        if(isset($tag->id)) {
                            $tag_id = $tag->id;
                            $taskTag = TaskTag::whereNull('task_id')
                                        ->where('tag_id', $tag_id)                                        
                                        ->where('year', $dateParts[0])
                                        ->where('month', $dateParts[1])
                                        ->where('name', $tag_name)
                                        ->first();
                            if(!isset($taskTag->id)) {
                                $taskTag = new TaskTag();
                                $taskTag->tag_id = $tag_id;
                                $taskTag->year = $dateParts[0];
                                $taskTag->month = $dateParts[1];
                                $taskTag->name = $tag_name;
                                $taskTag->save();
                            }
                            
                            $parent_id = $taskTag->id;
                            $category = 'tag';
                        }
                    }
                    break;
                default:
                    $err_text = 'Month Year';
                    $task = Task::select('tasks.id', 'task_tag.tag_id')
                        ->join('task_tag', 'tasks.id', '=', 'task_tag.task_id')
                        ->join('tags', 'tags.id', '=', 'task_tag.tag_id')
                        ->where('task_tag.year', $dateParts[0])
                        ->where('task_tag.month', $dateParts[1])
                        ->where('task_tag.name', $tag_name)
                        ->where('tags.code', $tag_code)
                        ->first();
                    $date = date('Ymd', strtotime($dateSelected.'-10'));

                    if(!isset($task->id)) {
                        $tag = Tag::where('code', $tag_code)->first();
                        if(isset($tag->id)) {
                            $tag_id = $tag->id;
                            $taskTag = TaskTag::whereNull('task_id')
                                        ->where('tag_id', $tag_id)                                        
                                        ->where('year', $dateParts[0])
                                        ->where('month', $dateParts[1])
                                        ->where('name', $tag_name)
                                        ->first();
                            if(!isset($taskTag->id)) {
                                $taskTag = new TaskTag();
                                $taskTag->tag_id = $tag_id;
                                $taskTag->year = $dateParts[0];
                                $taskTag->month = $dateParts[1];
                                $taskTag->name = $tag_name;
                                $taskTag->save();
                            }
                            
                            $parent_id = $taskTag->id;
                            $category = 'tag';
                        }
                    }
                    break;
            }
        }
        
        if($parent_id == null){
            throw new \Exception("No Created Task for this ".$err_text, 422);
            // return [
            //     'status' => 'error',
            //     'message' => 'No Created Task for this month.'
            // ];
        }
        
        if ($request->file('files')) {
            $files = $request->file('files');
            foreach ($files as $key => $file) {

                $document = new StoreDocument;
                $document->user_id = auth()->user()->id;
                $document->parent_id = $parent_id;
                $document->category = $category;
                
                $document->name = $file->getClientOriginalName();
                $document->ext = $file->getClientOriginalExtension();
                $document->mime_type = $file->getMimeType();
                $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
                $document->size = $file->getSize()/1024;
                $document->size_type = 'KB';

                $date = date('YmdHis').'-'.rand(10,99);
                $path = "/$this->aws_s3_path/stores/$pharmacy_store_id/bulletin/$category"."s/$parent_id/$date";
                $document->path = $path;

                $save = $document->save();

                if(!$save) {
                    $flag = false;
                }

                $documentTag = new DocumentTag();
                $documentTag->document_id = $document->id;
                $documentTag->tag_id = $tag_id;
                $documentTag->document_type = $category;
                $documentTag->tag_type = $tag_type;
                $documentTag->save();
                
                if($save) {
                    $pathfile = $document->path.$document->name;
                    Storage::disk('s3')->put($pathfile, file_get_contents($file));
                }
            
            }
        }
        else{
            return [
                'status' => 'error',
                'message' => 'No selected file(s).'
            ];
        }
        
        if(!$flag) {
            throw new \Exception("Not saved");
        }

    
        return [
            'status' => 'success',
            'message' => 'Record has beed saved.'
        ];
    }

    // public function store($request)
    // {
    //     $flag = true;
    //     $data = json_decode($request->data);

    //     $pharmacy_store_id = 1;
        
    //     if(isset($data->pharmacy_store_id) && !empty($data->pharmacy_store_id)) {
    //         $pharmacy_store_id = $data->pharmacy_store_id;
    //     }
        
    //     $tag_code = '';
    //     if(isset($data->tag_code) && !empty($data->tag_code)) {
    //         $tag_code = $data->tag_code;
    //     }

    //     $tag_name = 'monthly';
    //     if(isset($data->tag_name)) {
    //         $tag_name = !empty($data->tag_name) ? $data->tag_name : $tag_name;
    //     }
    //     $tag_name = strtolower($tag_name);

    //     $parent_id = null;
    //     $category = 'storeDocumentTag';

    //     $tag_id = null;
    //     $date = '';
    //     if(isset($data->month) && !empty($data->month)) {
    //         $dateSelected = $data->month;
    //         $dateParts = explode("-", $dateSelected);

    //         $tag = Tag::where('code', $tag_code)->first();
    //         $tag_id = $tag->id;
            
    //         switch($tag_name) {
    //             case 'daily':

    //                 $_year = (int) $dateParts[0];
    //                 $_month = (int) $dateParts[1];
    //                 $_day = (int) $dateParts[2];

    //                 $storeDocumentTag = StoreDocumentTag::where('tag_id', $tag_id)
    //                     ->where('year', $_year)
    //                     ->where('month', $_month)
    //                     ->where('day', $_day)
    //                     ->first();

    //                 if(!isset($storeDocumentTag->id)) {
    //                     $storeDocumentTag = new StoreDocumentTag();
    //                     $storeDocumentTag->tag_id = $tag_id;
    //                     $storeDocumentTag->day = $_day;
    //                     $storeDocumentTag->month = $_month;
    //                     $storeDocumentTag->year = $_year;
    //                     $storeDocumentTag->custom_name = $tag_name;
    //                     $storeDocumentTag->save();
    //                 }
                    
    //                 break;
    //             case 'weekly':

    //                 $_week = date('W', strtotime($dateSelected));
    //                 $_year = (int) $dateParts[0];

    //                 $startDate = new DateTime();
    //                 $startDate->setISODate($_year, $_week);
    //                 $endDate = clone $startDate;
    //                 $endDate->modify('+6 days');

    //                 $storeDocumentTag = StoreDocumentTag::where('tag_id', $tag_id)
    //                     ->where('year', $_year)
    //                     ->where('week', $_week)
    //                     ->first();

    //                 if(!isset($storeDocumentTag->id)) {
    //                     $storeDocumentTag = new StoreDocumentTag();
    //                     $storeDocumentTag->tag_id = $tag_id;
    //                     $storeDocumentTag->week = $_week;
    //                     $storeDocumentTag->year = $_year;
    //                     $storeDocumentTag->custom_name = $tag_name;
    //                     $storeDocumentTag->custom_data = json_encode([
    //                         'start_date' => $startDate->format('Y-m-d'),
    //                         'end_date' => $endDate->format('Y-m-d')
    //                     ]);
    //                     $storeDocumentTag->save();
    //                 }

    //                 break;
    //             case 'monthly':

    //                 $_year = (int) $dateParts[0];
    //                 $_month = (int) $dateParts[1];

    //                 $storeDocumentTag = StoreDocumentTag::where('tag_id', $tag_id)
    //                     ->where('year', $_year)
    //                     ->where('month', $_month)
    //                     ->first();

    //                 if(!isset($storeDocumentTag->id)) {
    //                     $storeDocumentTag = new StoreDocumentTag();
    //                     $storeDocumentTag->tag_id = $tag_id;
    //                     $storeDocumentTag->month = $_month;
    //                     $storeDocumentTag->year = $_year;
    //                     $storeDocumentTag->custom_name = $tag_name;
    //                     $storeDocumentTag->save();
    //                 }

    //                 break;
    //             default:
    //                 $err_text = 'Month Year';
                    
    //                 $_year = (int) $dateParts[0];
    //                 $_month = (int) $dateParts[1];

    //                 $storeDocumentTag = StoreDocumentTag::where('tag_id', $tag_id)
    //                     ->where('year', $_year)
    //                     ->where('month', $_month)
    //                     ->first();

    //                 if(!isset($storeDocumentTag->id)) {
    //                     $storeDocumentTag = new StoreDocumentTag();
    //                     $storeDocumentTag->tag_id = $tag_id;
    //                     $storeDocumentTag->month = $_month;
    //                     $storeDocumentTag->year = $_year;
    //                     $storeDocumentTag->custom_name = $tag_name;
    //                     $storeDocumentTag->save();
    //                 }
    //                 break;
    //         }

    //         $parent_id = $storeDocumentTag->id;
    //     }
        

    //     if ($request->file('files')) {
    //         $files = $request->file('files');
    //         foreach ($files as $key => $file) {

    //             $document = new StoreDocument;
    //             $document->user_id = auth()->user()->id;
    //             $document->parent_id = $parent_id;
    //             $document->category = $category;
                
    //             $document->name = $file->getClientOriginalName();
    //             $document->ext = $file->getClientOriginalExtension();
    //             $document->mime_type = $file->getMimeType();
    //             $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
    //             $document->size = $file->getSize()/1024;
    //             $document->size_type = 'KB';

    //             $date = date('YmdHis').'-'.rand(10,99);
    //             $path = "/$this->aws_s3_path/stores/$pharmacy_store_id/$tag_code/$category"."s/$parent_id/$date";
    //             $document->path = $path;

    //             $save = $document->save();

    //             if($save) {
    //                 $pathfile = $document->path.$document->name;
    //                 Storage::disk('s3')->put($pathfile, file_get_contents($file));
    //             }

    //             if(!$save) {
    //                 $flag = false;
    //             }
            
    //         }
    //     }
    //     else{
    //         return [
    //             'status' => 'error',
    //             'message' => 'No selected file(s).'
    //         ];
    //     }
        
    //     if(!$flag) {
    //         throw new \Exception("Not saved");
    //     }

    
    //     return [
    //         'status' => 'success',
    //         'message' => 'Record has beed saved.'
    //     ];
    // }

    public function delete($id)
    {
        $dTag = DocumentTag::findOrFail($id);
        $document_id = $dTag->document_id;
        $document_type = $dTag->document_type;
        $tag_type = $dTag->tag_type;
        
        if($document_type == 'self') {
            $document = ComplianceDocument::findOrFail($document_id);
        } else {
            $document = StoreDocument::findOrFail($document_id);
        }
        
        @unlink(public_path('/'.$document->path));
        $save = $document->delete();

        if(!$save) {
            throw "Not Deleted";
        }
        $del = $this->documentTag
            ->where('document_id',$document_id)
            ->where('document_type',$document_type)
            ->where('tag_type',$tag_type)
            ->delete();
    }

    private function pathUpload($pharmacy_store_id, $task_id) : string
    {
        return self::BASE_PATH.'/'.$pharmacy_store_id.'/bulletin/tasks/'.$task_id;
    }

    private function pathUploadToDocuments($id) : string
    {
        return self::BASE_PATH.'/'.$id.'/compliance/inventory-reconciliations';
    }

}