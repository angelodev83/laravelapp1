<?php

namespace App\Repositories\Compliance;

use Illuminate\Http\Request;

use App\Interfaces\ISelfAuditDocumentRepository;
use App\Repositories\BaseStoreRepository;
use App\Models\ComplianceDocument;
use App\Models\StoreDocument;
use App\Models\DocumentTag;
use App\Models\Tag;
use App\Http\Utils\FileIconUtil;
use App\Models\Task;
use File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SelfAuditDocumentRepository extends BaseStoreRepository implements ISelfAuditDocumentRepository
{
    use FileIconUtil;

    private $document;
    private $complianceDocument;
    private $storeDocument;
    private $documentTag;
    private $tag;
    protected $dataTable = [];

    public function __construct(ComplianceDocument $complianceDocument
        , StoreDocument $storeDocument
        , DocumentTag $documentTag
        , Tag $tag
    )
    {
        $this->document = $complianceDocument;
        $this->complianceDocument = $complianceDocument;
        $this->storeDocument = $storeDocument;
        $this->documentTag = $documentTag;
        $this->tag = $tag;
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

        $query = $this->documentTag
            ->with('selfAuditDocument', 'taskDocument.task', 'taskDocument', 'tag');
        // Search //input all searchable fields
        
        
        if($request->has('tag_code')) {
            $query = $query->where(function($query) use ($request){
                $query->whereHas('tag', function($query) use ($request) {
                    $query->where('code', $request->tag_code);
                });
            });
        }

        $query = $query->where(function($query) use ($request){
            $query->orWhereHas('selfAuditDocument', function($query) use ($request) {
                $query->where('pharmacy_store_id', $request->pharmacy_store_id);
                if(!empty($request->search)) {
                    $query = $query->orWhere(function($query) use ($request){ 
                        $query->where('path', 'like', "%".$request->search."%");   
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
                    });
                }
            });
        });

        
        $orderByCol = $request->columns[$request->order[0]['column']]['name'];
        
        $query = $query->orderBy($orderByCol, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $newData = [];
        
        $icons = $this->styles();

        foreach ($data as $d) {
            $self = isset($d->selfAuditDocument) ? $d->selfAuditDocument : [];
            $task = isset($d->taskDocument) ? $d->taskDocument : [];
            $value = !empty($self) ? $self : $task;
            $type = !empty($value->ext) ? strtolower($value->ext) : "default";
            $icon = isset($icons[$type]) ? $icons[$type]["icon"] : $icons["default"]["icon"];
            $color = isset($icons[$type]) ? $icons[$type]["color"] : $icons["default"]["color"];
            $empName = isset($value->user->employee) ? $value->user->employee->getFullName() : "NA";
            
            $taskMonth = '';
            $taskTag = [];
            if(isset($task->parent_id)) {
                $taskTag= DB::table('task_tag')->where('task_id', $task->parent_id)->first();
                $taskMonth = date("M, Y", strtotime($taskTag->year.'-'.$taskTag->month.'-01'));
            }
            
            Storage::disk('local')->append('file.txt', json_encode($taskTag));
            $newData[] = [
                'id' => $d->id,
                'path' => $value->path,
                'ext' => $value->ext,
                'task_month' => $taskMonth,
                'size' => $value->getFileSizeByType("KB"),
                'last_modified' => date("M d, Y h:iA",$value->getLastModified()),
                'created_at' => $value->created_at->format('M d, Y h:iA'),
                'created_by' => $empName,
                'file' => '
                    <div class="d-flex align-items-center">
                        <div><i class="bx '.$icon.' me-2 font-24 '.$color.'"></i></div>
                        <div class="font-weight-bold">'.substr($value->path, strrpos($value->path, '/') + 1).'</div>
                    </div>
                ',
                'actions' =>  '<div class="d-flex order-actions">
                    <a target="_new" href="'.$value->path.'">
                        <button type="button" class="btn btn-primary btn-sm me-2" ><i class="fa-solid fa-download"></i></button>
                    </a>         
                    <button type="button" onclick="clickDeleteBtn(' . $value->id . ',' . $d->id . ')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
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
        
        if(isset($data->pharmacy_store_id) && !empty($data->pharmacy_store_id)) {
            $pharmacy_store_id = $request->pharmacy_store_id;
        }
        
        $tag_code = '';
        if(isset($data->tag_code) && !empty($data->tag_code)) {
            $tag_code = $data->tag_code;
        }

        if(isset($data->month) && !empty($data->month)) {
            $dateParts = explode("-", $data->month);
            $task = Task::select('tasks.id', 'task_tag.tag_id')
                ->join('task_tag', 'tasks.id', '=', 'task_tag.task_id')
                ->join('tags', 'tags.id', '=', 'task_tag.tag_id')
                ->where('task_tag.month', $dateParts[1])
                ->where('tags.code', $tag_code)
                ->first();
        }

        if($task == null){
            return [
                'status' => 'error',
                'message' => 'No Created Task for this month.'
            ];
        }
        
        $pathUpload = $this->pathUpload($pharmacy_store_id, $task->id);
        
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

                $documentTag = new DocumentTag();
                $documentTag->document_id = $document->id;
                $documentTag->tag_id = $task->tag_id;
                $documentTag->document_type = 'task';
                $documentTag->tag_type = 'audit';
                $documentTag->save();
                
            
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

    public function store2($request)
    {
        $flag = true;

        if($request->has('pharmacy_store_id')) {
            $pharmacy_store_id = $request->pharmacy_store_id;
        }

        $tag_code = '';
        if($request->has('tag_code')) {
            $tag_code = $request->tag_code;
        }

        $pathUpload = $this->pathUploadToDocuments($pharmacy_store_id);
        
        if ($request->file('files')) {
            $files = $request->file('files');
            foreach ($files as $key => $file) {

                $complianceDocument = new ComplianceDocument;
                $complianceDocument->user_id = auth()->user()->id;
                $complianceDocument->pharmacy_store_id = $pharmacy_store_id;
                $complianceDocument->ext = $file->getClientOriginalExtension();

                @unlink(public_path($pathUpload.'/'.$complianceDocument->path));
                // $fileName = date('YmdHi').'_'.$file->getClientOriginalName();
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'_'.date('YmdHi').'.'.$file->getClientOriginalExtension();
                $file->move(public_path($pathUpload), $fileName);
                $complianceDocument->path = '/'.$pathUpload.'/'.$fileName;
                $path = '/'.$pathUpload.'/'.$fileName;

                $save = $complianceDocument->save();

                if(!$save) {
                    $flag = false;
                    throw new \Exception("Not saved");
                }

                $tag = Tag::where('code',$tag_code)->where('type','audit')->first();
                if(isset($tag->id)) {
                    $documentTag = new DocumentTag();
                    $documentTag->document_id = $complianceDocument->id;
                    $documentTag->tag_id = $tag->id;
                    $documentTag->document_type = 'self';
                    $documentTag->tag_type = 'audit';
                    $save = $documentTag->save();

                    if(!$save) {
                        $flag = false;
                    }
                } else {
                    $flag = false;
                }

                if(!$flag) {
                    throw new \Exception("Not saved");
                }
            }
            
        }
        
        if(!$flag) {
            throw new \Exception("Not saved");
        }
    }

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
        return self::BASE_PATH.'/'.$id.'/compliance/self-audit-documents';
    }

}