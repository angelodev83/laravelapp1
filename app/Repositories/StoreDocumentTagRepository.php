<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Utils\FileIconUtil;
use App\Models\StoreDocument;
use App\Models\StoreDocumentTag;
use App\Models\StoreDocumentTagTask;
use App\Models\Tag;
use App\Models\TransactionLog;
use Carbon\Carbon;
use DateTime;
use File;
use Illuminate\Support\Facades\Storage;

ini_set('max_execution_time', '3600');

class StoreDocumentTagRepository
{
    use FileIconUtil;

    private $tags;
    private $documents;
    private $aws_s3_path;

    protected $dataTable = [];

    public function __construct(Tag $tags, StoreDocument $documents)
    {
        $this->tags = $tags;
        $this->documents = $documents;
        $this->aws_s3_path = env('AWS_S3_PATH');
    }


    public function getDataTable($request)
    {
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';
        //default field for order
        $orderByCol = $request->columns[$request->order[0]['column']]['name'] ?? 'created_at';

        $year = isset($request->year) ? $request->year : null;
        $month = isset($request->month) ? $request->month : null;
        $week = isset($request->week) ? $request->week : null;
        $day = isset($request->day) ? $request->day : null;
        $tagIDs = isset($request->tagIDs) ? $request->tagIDs : [];
        $pharmacy_store_id = isset($request->pharmacy_store_id) ? $request->pharmacy_store_id : null;

    
        $query = StoreDocumentTag::with('tag')->select('store_documents.*', 'store_document_tags.tag_id', 'store_document_tags.year', 'store_document_tags.month', 'store_document_tags.day', 'store_document_tags.week', 'store_document_tags.month_week', 'store_document_tags.custom_name')
            ->join('store_documents', function($q) {
                $q->on('store_document_tags.id', '=', 'store_documents.parent_id')
                    ->where('store_documents.category', '=', 'storeDocumentTag');
            });

        
        if(!empty($pharmacy_store_id)) {
            $query = $query->where('store_document_tags.pharmacy_store_id', $pharmacy_store_id);
        }

        if(!empty($year)) {
            $query = $query->where('store_document_tags.year', $year);
        }

        if(!empty($month)) {
            $query = $query->where('store_document_tags.month', $month);
        }

        if(!empty($week)) {
            $query = $query->where('store_document_tags.week', $week);
        }

        if(!empty($day)) {
            $query = $query->where('store_document_tags.day', $day);
        }

        if(!empty($tagIDs)) {
            $query = $query->whereIn('store_document_tags.tag_id', $tagIDs);
        }
        

        // Search //input all searchable fields
        $search = $request->search;
        $columns = $request->columns;
        

        $query = $query->where(function($query) use ($search, $columns){
            foreach ($columns as $column) {
                if($column['searchable'] === "true" && $search){
                    $query->orWhere("$column[name]", 'like', "%".$search."%");
                }  
            }
        }); 

        if($orderByCol == 'subtext') {
            // $query = $query->orderBy('user.employee.firstname', $orderBy);
            $query = $query->orderBy('store_document_tags.year', $orderBy);
            $query = $query->orderBy('store_document_tags.month', $orderBy);
            $query = $query->orderBy('store_document_tags.week', $orderBy);
            $query = $query->orderBy('store_document_tags.day', $orderBy);
        } else {
            $query = $query->orderBy($orderByCol, $orderBy);
        }
        
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $hideD = 'hidden';
        $hideAll = '';

        $permissions = $request->permissions;
        $permission_prefix = isset($permissions['prefix']) ? $permissions['prefix'] : '';
        $permission_delete = isset($permissions['delete']) ? $permissions['delete'] : [];

        if(!empty($page_code)) {
            if(auth()->user()->can($permission_prefix.$page_code.'.delete'))
            {
                $hideD = '';
            }
        } else {
            if(auth()->user()->canany($permission_delete))
            {
                $hideD = '';
            }
        }

        $icons = $this->styles();

        $newData = [];
        foreach ($data as $value) {
            $type = !empty($value->ext) ? strtolower($value->ext) : "default";
            $icon = isset($icons[$type]) ? $icons[$type]["icon"] : $icons["default"]["icon"];
            $color = isset($icons[$type]) ? $icons[$type]["color"] : $icons["default"]["color"];
            $emp = $value->user->employee;

            $tag = $value->tag ?? null;

            $subtext = '';
            if($value->custom_name == 'monthly') {
                $_date = $value->year.'-'.sprintf('%0d',$value->month).'-01';
                $subtext = date('F Y', strtotime($_date));
            }

            if(empty($tag)) {
                dd($value);
            }

            $description = '<b>'.$value->name.'</b> record from <u>'.$tag->name.'/'.$subtext.'</u>';

            $created_by = isset($value->user->employee) ? $value->user->employee->getFullName() : "NA";
            $empAvatar = '';
            if(!empty($emp->image)) {
                $empAvatar = '
                    <div class="d-flex">
                        <img src="/upload/userprofile/'.$emp->image.'" width="32" height="32" class="rounded-circle" alt="">
                        <div class="mt-2 flex-grow-1 ms-2">
                            <p class="mb-0 font-weight-bold">'.$created_by.'</p>
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
                        <p class="mt-2 mb-0 font-weight-bold ms-2">'.$created_by.'</p>
                    </div>
                ';
            }
            
            $formatted_created_at = '';
            $formatted_last_modified = '';
            if(!empty($value->created_at)) {
                $formatted_created_at = '<p class="p-0 m-0"><small>'.date('M d, Y', strtotime($value->created_at)).'</small></p>';
                $formatted_created_at .= '<p class="p-0 m-0"><small>'.date('h:iA', strtotime($value->created_at)).'</small></p>';
            }
            if(!empty($value->last_modified)) {
                $formatted_last_modified = '<p class="p-0 m-0"><small>'.date('M d, Y', strtotime($value->last_modified)).'</small></p>';
                $formatted_last_modified .= '<p class="p-0 m-0"><small>'.date('h:iA', strtotime($value->last_modified)).'</small></p>';
            }

            $filename = $value->name;

            $formatted_size = $this->custom_number_format($value->size, 2).' '.$value->size_type;
            if($value->size >= 1000)
            {
                $mbSize = $value->size/1000;
                $formatted_size = $this->custom_number_format($mbSize, 2).' MB';

                if($mbSize >= 1000) {
                    $gbSize = $value->size/1000000;
                    $formatted_size = $this->custom_number_format($gbSize, 2).' GB';
                }
            }

            $path = $value->path.$value->name;
            $s3Url = Storage::disk('s3')->temporaryUrl(
                $path,
                now()->addMinutes(30)
            );
            $createdAt = new DateTime($value->created_at->setTimezone('America/Los_Angeles'));
            $date = $createdAt->format('Y-m-d');
            $time = $createdAt->format('h:i A');
            $newData[] = [
                'id'    => $value->id,
                'subtext'  => $subtext,
                'name'  => $value->name,
                'size'  => $value->size,
                'size_type'  => $value->size_type,
                'created_by' => $created_by,
                'created_at' => $value->created_at,
                'formatted_created_at' => $formatted_created_at,
                'last_modified' => $value->last_modified,
                'formatted_last_modified' => $formatted_last_modified,
                'formatted_size' => $formatted_size,
                'empAvatar' => $empAvatar,
                'folder_name' => $tag->name,
                'permissions' => $permissions,
                'formatted_folder_name' => '
                    <span class="px-3 py-2 rounded-3" style="background-color: '.$tag->background_color.'; color: '.$tag->text_color.';"><b>'.$tag->name.'</b></span>
                ',
                'file' => '
                    <a target="_blank" href="'.$s3Url.'" class="text-black d-flex align-items-center">
                        <img class="rounded-3 me-3" width="50" height="50" style="background-color: '.$value->background_color.'; border: solid 2px '.$value->border_color.';" src="'.$value->icon_path.'" alt="">
                        <div>
                            <b class="knowledge-base-file-name">'.$filename.'</b>
                            <div class="text-body-secondary">'.$created_by.' | '.$date.' | '.$time.'</div>
                        </div>
                    </a>
                ',
                'updated_at' => ($value->updated_at === null)?'':date('M d, Y h:iA', strtotime($value->updated_at)),
                'actions' =>  '<div class="d-flex order-actions" '.$hideAll.'>
                    <a href="/admin/store-document/download/s3/'.$value->id.'"><button type="button" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-download"></i>
                    </button></a>
                    <button class="btn btn-sm btn-danger ms-2" 
                        id="btn-knowledge-base-delete-'.$value->id.'"
                        data-subject="'.htmlspecialchars(addslashes($tag->name)).'" 
                        data-description="'.htmlspecialchars(addslashes($description)).'" 
                        onclick="ShowConfirmDeleteForm(' . $value->id . ')" '.$hideD.'><i class="fa fa-trash-can"></i>
                    </button>
                </div>'
            ];
        } 

        return $this->dataTable = [
            "draw"=> $request->draw, 
            "recordsTotal"=> $recordsTotal, 
            "recordsFiltered" => $recordsFiltered, 
            'data' => $newData
        ];
    }

    private function getFirstLettersWithUnderscore($sentence)
    {
        // Split the sentence into words
        $words = explode(' ', $sentence);
        
        // Initialize an array to hold the first letters
        $firstLetters = [];
        
        // Iterate through each word
        foreach ($words as $word) {
            // Add the first letter of the current word to the result if the word is not empty
            if (!empty($word)) {
                $firstLetters[] = $word[0];
            }
        }
        
        // Join the first letters with underscores
        return implode('_', $firstLetters);
    }

    /**
     * action store
     *
     * @param [type] $request
     * @return void
     */
    public function store($request)
    {
        $data = json_decode($request->data);
        
        $tag_id = $data->folder_id;
        $tag_type = $data->tag_type ?? null;
        $tag_code = null;
        $pharmacy_store_id = $data->pharmacy_store_id;

        $default_file_bg = $data->default_file_bg ?? '#fcd0b2';

        $default_folder_bg = $data->default_folder_bg ?? '#fcd0b2';
        $default_folder_tc = $data->default_folder_tc ?? '#af5a20';

        $month_year = null;
        $month_week = null;
        $start_date = null;
        if(isset($data->month_year)) {
            $month_year = $data->month_year ?? null;
            $month_week = $data->month_week ?? null;
            $start_date = $data->start_date ?? null;
        }

        if(empty($tag_id)) {
            $tag_name = $data->new_folder;
            $tag_code = $this->getFirstLettersWithUnderscore($tag_name);

            $tag = new $this->tags;
            $tag->name = $tag_name;
            $tag->code = strtolower($tag_code);
            $tag->type = strtolower($tag_type);

            $tag->icon_path = '/source-images/knowledge-base/All Files.png';
            $tag->background_color = $default_folder_bg;
            $tag->border_color = $default_folder_bg;
            $tag->text_color = $default_folder_tc;

            $tag->user_id = auth()->user()->id;
            $tag->save();
            $tag_id = $tag->id;
        } else {
            $tag = Tag::findOrFail($tag_id);
            if(isset($tag->id)) {
                $tag_id = $tag->id;
                $tag_code = $tag->code;
                $tag_type = $tag->type;
            }
        }

        $_date = $start_date;

        if(empty($start_date)) {
            $_date = $month_year.'-01';
        }
        $_month = date('n', strtotime($_date));
        $_year = date('Y', strtotime($_date));

        $parent_id = null;

        $storeDocumentTag = StoreDocumentTag::where('tag_id', $tag_id);

        switch($tag_id) {
            // monthly
            case 1: // Monthly Pharmacy DFI/QA
            case 2: // Monthly IHS Audit Checklist
            case 3: // Monthly Self Assessment QA
            case 8: // Control Counts C2 (monthly)
            case 9: // Control Counts C3 - 5 (monthly)
                $storeDocumentTag = $storeDocumentTag->where('month', $_month)
                    ->where('year', $_year)
                    ->first();
                if(!isset($storeDocumentTag->id)) {
                    $storeDocumentTag = new StoreDocumentTag();
                    $storeDocumentTag->tag_id = $tag_id;
                    $storeDocumentTag->year = $_year;
                    $storeDocumentTag->month = $_month;
                    $storeDocumentTag->custom_name = 'monthly';
                    $storeDocumentTag->user_id = auth()->user()->id;
                    $storeDocumentTag->pharmacy_store_id = $pharmacy_store_id;
                    $storeDocumentTag->save();
                }
                $parent_id = $storeDocumentTag->id;

                break;
        }


        if ($request->file('files')) {
            $documents = $request->file('files');
            foreach ($documents as $key => $file) {
                $unique = date('Ymd').'-'.rand(100,999);
                $path = "/$this->aws_s3_path/stores/$pharmacy_store_id/$tag_code/storeDocumentTags/$parent_id/$unique";
                $filename = $file->getClientOriginalName();

                $document = new StoreDocument;
                $document->user_id = auth()->user()->id;
                $document->parent_id = $parent_id;
                $document->category = 'storeDocumentTag';
                
                $document->name = $file->getClientOriginalName();
                $document->ext = $file->getClientOriginalExtension();
                $document->mime_type = $file->getMimeType();
                $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
                $document->size = $file->getSize()/1024;
                $document->size_type = 'KB';

                $document->path = $path;

                $document->icon_path = '/source-images/knowledge-base/Default.png';
                $document->background_color = $default_file_bg;
                $document->text_color = 'black';
                $document->border_color = $default_file_bg;

                $save = $document->save();

                if($save) {
                    $pathfile = $document->path.$document->name;
                    Storage::disk('s3')->put($pathfile, file_get_contents($file));
                }
            }
        }
        return [
            'folder_id' => $tag_id,
            'month' => $_month,
            'year' => $_year,
            'page_code' => null
        ];
    }

    public function delete($id)
    {
        $document = $this->documents->findOrFail($id);

        $pharmacy_store_id = $document->pharmacy_store_id;

        $logs = [
            'store_documents' => $document
        ];
        $path = $document->path.$document->name;

        if($path != ''){
            if(Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
            $document->delete();   
        }

        $document->delete();

        //delete history
        $transactionLog = new TransactionLog();
        $transactionLog->user_id = auth()->user()->id;
        $transactionLog->pharmacy_store_id = $pharmacy_store_id;
        // $transactionLog->page_id = $page_id;
        $transactionLog->module_name = 'store_files';
        $transactionLog->module_id = $logs['store_documents']['id'];
        $transactionLog->function = 'StoreDocumentTagRepository.delete';
        $transactionLog->action = 'deleted';
        $transactionLog->subject = 'User ID: '.auth()->user()->id . ', Username: '.auth()->user()->name . ' DELETED File ID: '.$logs['store_documents']['id'].', File Name: '.$logs['store_documents']['name'];
        $transactionLog->data = json_encode($logs);
        $transactionLog->save();
    }

    public function deleteFolder($request)
    {
        $folder_id = $request->id;
        $pharmacy_store_id = $request->pharmacy_store_id ?? null;
        $menu_page = $pharmacy_store_id->menu_page ?? '';

        $page_id = null;

        $folder = Tag::with('page')->findOrFail($folder_id);

        $logs = [
            'store_folders' => $folder,
            'store_files' => []
        ];

        $save = false;

        if(isset($folder->id)) {
            $documents = StoreDocument::where('folder_id', $folder_id)
                ->where('pharmacy_store_id', $pharmacy_store_id)
                ->get();
            
            $logs['store_files'] = $documents->toArray();

            $page_code = $folder->page->code;
            $page_id = $folder->page->id;
            
            $aws_s3_path = env('AWS_S3_PATH');
            $path = "$aws_s3_path/stores/$pharmacy_store_id/$menu_page/$page_code/$folder_id/";
            
            Storage::disk('s3')->deleteDirectory($path);

            $save = StoreDocument::where('folder_id', $folder_id)
                ->where('pharmacy_store_id', $pharmacy_store_id)
                ->delete();

            $save = $folder->delete();
        }

        if($save) {
            //delete history
            $transactionLog = new TransactionLog();
            $transactionLog->user_id = auth()->user()->id;
            $transactionLog->pharmacy_store_id = $pharmacy_store_id;
            $transactionLog->page_id = $page_id;
            $transactionLog->module_name = 'store_folders';
            $transactionLog->module_id = $folder_id;
            $transactionLog->function = 'StoreDocumentTagRepository.deleteFolder';
            $transactionLog->action = 'deleted';
            $transactionLog->subject = 'User ID: '.auth()->user()->id . ', Username: '.auth()->user()->name . ' DELETED Folder ID: '.$folder_id.', Folder Name: '.$logs['store_folders']['name'] .' and ('.count($logs['store_files']).') File records inside the folder';
            $transactionLog->data = json_encode($logs);
            $transactionLog->save();
        }
        
    }

}