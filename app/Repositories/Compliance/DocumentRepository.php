<?php

namespace App\Repositories\Compliance;

use Illuminate\Http\Request;

use App\Interfaces\IDocumentRepository;
use App\Repositories\BaseStoreRepository;
use App\Models\ComplianceDocument;
use App\Models\DocumentTag;
use App\Models\Tag;
use App\Http\Utils\FileIconUtil;

use File;
use Illuminate\Support\Facades\Auth;

class DocumentRepository extends BaseStoreRepository implements IDocumentRepository
{
    use FileIconUtil;

    private $document;
    protected $dataTable = [];

    public function __construct(ComplianceDocument $document)
    {
        $this->document = $document;
    }

    public function getDataTable() : array
    {
        return $this->dataTable;
    }

    public function setDataTable($request, $is_audit)
    {
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;
        
        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? 3;
        $orderBy = $request->order[0]['dir'] ?? 'desc';
        
        $query = $this->document->with('user.employee','auditDocumentTags.tag','topic');
        if($request->topic != null){
            $query = $query->where('topic_id', $request->topic);

            if($request->has('pharmacy_store_id')) {
                $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
            }
            $query->where(function($query) use ($request){
                $query->whereHas('documentTags', function($query) use ($request) {
                    $query->whereHas('tag', function($query) use ($request) {
                        $query->where('code', $request->tag_code);
                    });
                });
            });

            $search = $request->search;
            $query = $query->where(function($query) use ($search){ 
                $query->orWhere('filename', 'like', "%".$search."%");
                
                $query->orWhereHas('user.employee', function ($query) use ($search) {
                    $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $search . '%']);
                });
            });

        }
        else {
            // Search //input all searchable fields
            $search = $request->search;
            $query = $query->where(function($query) use ($search){ 
                $query->orWhere('filename', 'like', "%".$search."%");
                
                $query->orWhereHas('user.employee', function ($query) use ($search) {
                    $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $search . '%']);
                });

                $query->orWhereHas('topic', function ($query) use ($search) {
                    $query->whereRaw("name LIKE ?", ['%' . $search . '%']);
                });
            });

            if($request->has('pharmacy_store_id')) {
                $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
            }
            
            $query->where(function($query) use ($request){
                $query->whereHas('documentTags', function($query) use ($request) {
                    $query->whereHas('tag', function($query) use ($request) {
                        $query->where('code', $request->tag_code);
                    });
                });
            });
           
        }
        

        $tag_code = $request->tag_code;
        $div = 'cnr';
        if(($tag_code == 'sop' || $tag_code == 'pnp')) {
            $div = 'sop';
            $tag_code = $tag_code . 's';
        }
        if($tag_code == 'pbm_audit') {
            $tag_code = 'audit';
        }
        $permission_delete_name = 'menu_store.'.$div.'.'.$tag_code.'.delete';
        $hide_del = auth()->user()->can($permission_delete_name) ? '' : 'hidden';
        
        $orderByCol = $request->columns[$request->order[0]['column']]['name'];
        if($orderByCol == 'file') {
            $query = $query->orderByRaw("SUBSTRING_INDEX(path, '/', -1) $orderBy");
        } else {
            $query = $query->orderBy($orderByCol, $orderBy);
        }
        
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $newData = [];

        $icons = $this->styles();

        foreach ($data as $value) {
            $type = !empty($value->ext) ? strtolower($value->ext) : "default";
            $icon = isset($icons[$type]) ? $icons[$type]["icon"] : $icons["default"]["icon"];
            $color = isset($icons[$type]) ? $icons[$type]["color"] : $icons["default"]["color"];
            $empName = isset($value->user->employee) ? $value->user->employee->getFullName() : "NA";
            $topicName = isset($value->topic->name) ? $value->topic->name : "";
            $newData[] = [
                'id' => $value->id,
                'path' => $value->path,
                'topic' => $topicName,
                'ext' => $value->ext,
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
                    <button type="button" onclick="clickDeleteBtn(' . $value->id . ')" class="btn btn-danger btn-sm me-2" '.$hide_del.'><i class="fa-solid fa-trash-can"></i></button>
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
     * @param [type] $is_audit
     * @return void
     */
    public function store($request, $pharmacy_store_id, $is_audit)
    {
        $flag = true;
        $input = $request->all();
        $dataArray = [];
        if(isset($input['data'])) {
            $dataArray = json_decode($input['data'], true);
        }

        $tag_code = isset($request->tag_code) ? $request->tag_code : 'pbm_audit';
        $tag_type = isset($request->tag_type) ? $request->tag_type : 'audit';

        $pathUpload = self::BASE_PATH.'/'.$pharmacy_store_id."/$tag_type/$tag_code".'s'; // $this->resolvePath($is_audit, $pharmacy_store_id);
        
        if ($request->file('files')) {
            $files = $request->file('files');
            foreach ($files as $key => $file) {

                $complianceDocument = new ComplianceDocument;
                $complianceDocument->user_id = auth()->user()->id;
                $complianceDocument->pharmacy_store_id = $pharmacy_store_id;
                $complianceDocument->ext = $file->getClientOriginalExtension();
                if(isset($dataArray['topic_id'])) {
                    $complianceDocument->topic_id = $dataArray['topic_id'];
                }

                @unlink(public_path($pathUpload.'/'.$complianceDocument->path));
                // $fileName = date('YmdHi').'_'.$file->getClientOriginalName();
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'_'.date('YmdHis').Auth::id().'.'.$file->getClientOriginalExtension();
                $file->move(public_path($pathUpload), $fileName);
                $complianceDocument->path = '/'.$pathUpload.'/'.$fileName;
                // $complianceDocument->path = '/'.$pathUpload.'/'.$fileName;
                
                $complianceDocument->filename = $fileName;
                $path = '/'.$pathUpload.'/'.$fileName;

                $save = $complianceDocument->save();

                if(!$save) {
                    $flag = false;
                }

                $tag = Tag::where('code',$tag_code)->first();
                if(isset($tag->id)) {
                    $dt = new DocumentTag();
                    $dt->document_id = $complianceDocument->id;
                    $dt->tag_id = $tag->id;
                    $dt->document_type = 'self';
                    $dt->tag_type = $tag_type;
                    $save = $dt->save();

                    if(!$save) {
                        $flag = false;
                        throw new \Exception("Not saved - Document Tag");
                    }
                } else {
                    $flag = false;
                    throw new \Exception("Not saved - Tag Code is not existing");
                }
            }
            
        }
        
        if(!$flag) {
            throw new \Exception("Not saved");
        }
    }

    public function delete($id)
    {
        $document = $this->document->findOrFail($id);
        @unlink(public_path('/'.$document->path));
        $save = $document->delete();

        if(!$save) {
            throw "Not Deleted";
        }
    }

    /**
     * Private functions starts here
     */
    private function pathUploadToAudits($id) : string
    {
        return self::BASE_PATH.'/'.$id.'/compliance/audits';
    }

    private function pathUploadToDocuments($id) : string
    {
        return self::BASE_PATH.'/'.$id.'/compliance/documents';
    }

    private function resolvePath($is_audit, $pharmacy_store_id)
    {
        $path = $this->pathUploadToDocuments($pharmacy_store_id);
        if($is_audit == 1) {
            $path = $this->pathUploadToAudits($pharmacy_store_id);
        }
        return $path;
    }
}