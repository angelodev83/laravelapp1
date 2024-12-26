<?php

namespace App\Repositories;

use Illuminate\Http\Request;

use App\Models\StoreDocument;
use App\Repositories\BaseStoreRepository;
use App\Http\Utils\FileIconUtil;
use App\Models\TicketCommentDocument;
use File;

class StoreDocumentRepository extends BaseStoreRepository
{
    private $document;
    protected $documentDataTable = [];

    public function __construct(StoreDocument $document)
    {
        $this->document = $document;
    }

    public function getDocumentDataTable() : array
    {
        return $this->documentDataTable;
    }

    public function setDocumentDataTable($request)
    {
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? 0;
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        $model = 'task';
        if(isset($request->category)) {
            $model = $request->category;
        }
        $tags = $model.'AuditDocumentTags';

        $query = StoreDocument::with('user.employee', $model, $tags.'.tag')->where('category', $model);

        // Search //input all searchable fields
        $search = $request->search;
        $query = $query->where(function($query) use ($search){ 
            $query->orWhere('path', 'like', "%".$search."%");   
        });

        if($request->has('parent_id')) {
            $query = $query->where('parent_id', $request->parent_id);
        }
        
        $orderByCol = $request->columns[$request->order[0]['column']]['name'];
        
        $query = $query->orderBy($orderByCol, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $newData = [];

        $icons = $this->styles();

        foreach ($data as $value) {
            $type = !empty($value->ext) ? strtolower($value->ext) : "default";
            $icon = isset($icons[$type]) ? $icons[$type]["icon"] : $icons["default"]["icon"];
            $color = isset($icons[$type]) ? $icons[$type]["color"] : $icons["default"]["color"];
            $empName = isset($value->user->employee) ? $value->user->employee->getFullName() : "NA";
            $documentTags = isset($value->$tags) ? $value->$tags : [];

            $tagLabels = '';
            if(!empty($documentTags)) {
                foreach($documentTags as $dTag) {
                    $tagLabels .= '
                        <button type="button" class="btn btn-sm btn-secondary radius-15">'.$dTag->tag->name.' <span class="badge bg-dark ms-3 radius-15">X</span></button>
                    ';
                }
            }

            $newData[] = [
                'v' => $value,
                'id' => $value->id,
                'path' => $value->path,
                'ext' => $value->ext,
                'size' => $value->getFileSizeByType("KB"),
                'last_modified' => date("M d, Y h:iA",$value->getLastModified()),
                'created_at' => $value->created_at->format('M d, Y h:iA'),
                'created_by' => $empName,
                'tag_labels' => $tagLabels,
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
                    <button type="button" onclick="clickDeleteBtn(' . $value->id . ')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                </div>'
            ];
        }

        $this->documentDataTable = [
            "draw"=> $request->draw, 
            "recordsTotal"=> $recordsTotal, 
            "recordsFiltered" => $recordsFiltered, 
            'data' => $newData
        ];
    }

    protected function processStoringDocuments($files, $parent_id, $category, $pathUpload)
    {
        $flag = true;
        
        foreach ($files as $key => $file) {

            $document = new StoreDocument;
            $document->user_id = auth()->user()->id;
            $document->parent_id = $parent_id;
            $document->category = $category;
            $document->ext = $file->getClientOriginalExtension();

            @unlink(public_path($pathUpload.'/'.$document->path));
            // $fileName = date('YmdHi').'_'.$file->getClientOriginalName();
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'_'.date('YmdHi').'.'.$file->getClientOriginalExtension();
            $file->move(public_path($pathUpload), $fileName);
            $document->path = '/'.$pathUpload.'/'.$fileName;
            $path = '/'.$pathUpload.'/'.$fileName;

            $save = $document->save();

            if(!$save) {
                $flag = false;
            }
        }

        if(!$flag) {
            throw "Not saved";
        }
    }

    public function deleteDocument($id)
    {
        $document = StoreDocument::findOrFail($id);
        if($document->category == 'ticket') {
            $commentDocument = TicketCommentDocument::where('ticket_id', $id)->first();
            if(isset($commentDocument->id)) {
                $commentDocument->delete();
            }
        }
        @unlink(public_path('/'.$document->path));
        $save = $document->delete();

        if(!$save) {
            throw "Not Deleted";
        }
    }
}