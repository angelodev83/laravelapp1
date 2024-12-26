<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Utils\FileIconUtil;

use App\Models\StorePage;
use App\Models\StoreFolder;
use App\Models\StoreFile;
use App\Models\StoreFileTag;
use App\Models\TransactionLog;
use Carbon\Carbon;
use DateTime;
use File;
use Illuminate\Support\Facades\Storage;

ini_set('max_execution_time', '3600');

class StoreFolderFileRepository
{
    use FileIconUtil;

    private $pages;
    private $folders;
    private $files;
    private $aws_s3_path;

    protected $dataTable = [];

    public function __construct(StorePage $pages, StoreFolder $folders, StoreFile $files)
    {
        $this->pages = $pages;
        $this->folders = $folders;
        $this->files = $files;
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

        // get data from products table
        $query = StoreFile::with('folder.page', 'tag');
        

        // Search //input all searchable fields
        $search = $request->search;
        $columns = $request->columns;

        if($request->has('pharmacy_store_id')) {
            $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
        } 

        $parent_page_id = $request->parent_page_id ?? null;

        $page_id = '';
        $page_code = '';
        if($request->has('page_id')) {
            $page_code = $request->page_code;
            $page_id = $request->page_id;
        }

        $query = $query->where(function($query) use ($page_id,$parent_page_id) {
            $query->whereHas('folder', function($query) use ($page_id,$parent_page_id) {

                $query->where('page_id', $page_id);

                if(!empty($parent_page_id)) {
                    $query->whereHas('page', function($query) use ($parent_page_id) {
                        $query->where('parent_id', $parent_page_id);
                    });
                }

            });
        });

        if($request->has('folder_id')) {
            $query = $query->where('folder_id', $request->folder_id);
        }

        $query = $query->where(function($query) use ($search, $columns){
            foreach ($columns as $column) {
                if($column['searchable'] === "true" && $search){
                    $query->orWhere("$column[name]", 'like', "%".$search."%");
                }  
            }
        }); 
        
        
        // start filter -- if has tags
        $year = null;
        $month = null;
        $month_week = null;
        if($request->has('year')) {
            $year = $request->year;
            $month = $request->month ?? null;
            $month_week = $request->month_week ?? null;

            if(!empty($year)) {
                $query = $query->whereHas('tag', function($q) use ($year, $month, $month_week) {
                    $q->where('year', $year);
                    if(!empty($month)) {
                        $q->where('month', $month);
                    }
                    if(!empty($month_week)) {
                        $q->where('month_week', $month_week);
                    }
                });
            }
        }
        // end filter -- if has tags

        if($orderByCol == 'created_by') {
            // $query = $query->orderBy('user.employee.firstname', $orderBy);
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

            $page_name = $value->folder->page->name;
            $description = '<b>'.$value->name.'</b> record from <u>'.$page_name.'/'.$value->folder->name.'</u>';

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

            $tag = $value->tag ?? null;
            $custom_data = '';
            if(!empty($tag)) {
                $custom_data = $tag->custom_data;
            }

            $newData[] = [
                'id'    => $value->id,
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
                'page_name' => '<b>'.$page_name.'</b>',
                'folder_name' => $value->folder->name,
                'permissions' => $permissions,
                'custom_data' => $custom_data,
                'formatted_folder_name' => '
                    <span class="px-3 py-2 rounded-3" style="background-color: '.$value->folder->background_color.'; color: '.$value->folder->text_color.';"><b>'.$value->folder->name.'</b></span>
                ',
                'file' => '
                    <a target="_blank" href="'.$s3Url.'" class="text-black d-flex align-items-center">
                        <img class="rounded-3 me-3" width="50" height="50" style="background-color: '.$value->background_color.'; border: solid 2px '.$value->border_color.';" src="'.$value->icon_path.'" alt="">
                        <div>
                            <b class="knowledge-base-file-name mb-2">'.$filename.'</b>
                            <div class="text-body-secondary">'.$created_by.' | '.$date.' | '.$time.'</div>
                        </div>
                    </a>
                ',
                'updated_at' => ($value->updated_at === null)?'':date('M d, Y h:iA', strtotime($value->updated_at)),
                'actions' =>  '<div class="d-flex order-actions" '.$hideAll.'>
                    <a href="/admin/store-file/download/s3/'.$value->id.'"><button type="button" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-download"></i>
                    </button></a>
                    <button class="btn btn-sm btn-danger ms-2" 
                        id="btn-knowledge-base-delete-'.$value->id.'"
                        data-subject="'.htmlspecialchars(addslashes($page_name)).'" 
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


    /**
     * action store
     *
     * @param [type] $request
     * @return void
     */
    public function store($request)
    {
        $data = json_decode($request->data);
        
        $folder_id = $data->folder_id;
        $pharmacy_store_id = $data->pharmacy_store_id ?? null;
        $page_code = $data->page_code;

        $menu_page = $data->menu_page;
        $default_file_bg = $data->default_file_bg ?? '#e2acdf';

        $default_folder_bg = $data->default_folder_bg ?? '#fcd0b2';
        $default_folder_tc = $data->default_folder_tc ?? '#af5a20';

        $month_year = null;
        $month_week = null;
        $start_date = null;
        if(isset($data->month_year)) {
            $month_year = $data->month_year;
            $month_week = $data->month_week ?? null;
            $start_date = $data->start_date ?? null;
        }

        if(empty($folder_id)) {
            $folder = new $this->folders;
            $folder->name = $data->new_folder;
            $folder->page_id = $data->page_id;

            $folder->icon_path = '/source-images/knowledge-base/All Files.png';
            $folder->background_color = $default_folder_bg;
            $folder->border_color = $default_folder_bg;
            $folder->text_color = $default_folder_tc;

            $folder->user_id = auth()->user()->id;
            $folder->save();
            $folder_id = $folder->id;
        }

        if ($request->file('files')) {
            $files = $request->file('files');
            foreach ($files as $key => $file) {
                $unique = date('Ymd').'-'.rand(100,999);
                if(empty($pharmacy_store_id)) {
                    $path = "/$this->aws_s3_path/$menu_page/$page_code/$folder_id/$unique/";
                } else {
                    $path = "/$this->aws_s3_path/stores/$pharmacy_store_id/$menu_page/$page_code/$folder_id/$unique/";
                }
                $filename = $file->getClientOriginalName();

                $document = new $this->files;
                $document->user_id = auth()->user()->id;
                $document->name = $filename;
                $document->folder_id = $folder_id;
                $document->pharmacy_store_id = $pharmacy_store_id;
                $document->mime_type = $file->getMimeType();
                $document->ext = $file->getClientOriginalExtension();
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

                    if(!empty($month_year)) {
                        if(empty($start_date)) {
                            $date = $month_year."-01";
                        } else {
                            $date = $month_year."-".sprintf("%02d", $start_date);
                        }
                        
                        $tag = new StoreFileTag();
                        $tag->file_id = $document->id;
                        $tag->day = !empty($start_date) ? $start_date : null;
                        $tag->month = date('m', strtotime($date));
                        $tag->year = date('Y', strtotime($date));
                        $tag->week = date('W', strtotime($date));
                        $tag->month_week = !empty($month_week) ? $month_week : null;
                        $tag->save();
                    }

                    if(isset($data->score)) {
                        $score = $data->score;
                        $_date = $this->getCurrentPSTDate('Y-m-d');
                        $tag = new StoreFileTag();
                        $tag->file_id = $document->id;
                        $tag->month = date('m', strtotime($_date));
                        $tag->year = date('Y', strtotime($_date));
                        $tag->custom_data = json_encode(['score' => $score]);
                        $tag->save();
                    }
                }
            }
        }
        return [
            'folder_id' => $folder_id,
            'page_code' => $page_code
        ];
    }

    public function delete($id)
    {
        $file = $this->files->with('folder')->findOrFail($id);

        $pharmacy_store_id = $file->pharmacy_store_id;
        $page_id = $file->folder->page_id;
        $folder_id = $file->folder->id;

        $logs = [
            'store_files' => $file
        ];
        $path = $file->path.$file->name;

        if($path != ''){
            if(Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
            $file->delete();   
        }

        $file->delete();

        //delete history
        $transactionLog = new TransactionLog();
        $transactionLog->user_id = auth()->user()->id;
        $transactionLog->pharmacy_store_id = $pharmacy_store_id;
        $transactionLog->page_id = $page_id;
        $transactionLog->module_name = 'store_files';
        $transactionLog->module_id = $logs['store_files']['id'];
        $transactionLog->function = 'StoreFolderFileRepository.delete';
        $transactionLog->action = 'deleted';
        $transactionLog->subject = 'User ID: '.auth()->user()->id . ', Username: '.auth()->user()->name . ' DELETED File ID: '.$logs['store_files']['id'].', File Name: '.$logs['store_files']['name'];
        $transactionLog->data = json_encode($logs);
        $transactionLog->save();
    }

    public function deleteFolder($request)
    {
        $folder_id = $request->id;
        $pharmacy_store_id = $request->pharmacy_store_id ?? null;
        $menu_page = $pharmacy_store_id->menu_page ?? '';

        $page_id = null;

        $folder = StoreFolder::with('page')->findOrFail($folder_id);

        $logs = [
            'store_folders' => $folder,
            'store_files' => []
        ];

        $save = false;

        if(isset($folder->id)) {
            $files = StoreFile::where('folder_id', $folder_id)
                ->where('pharmacy_store_id', $pharmacy_store_id)
                ->get();
            
            $logs['store_files'] = $files->toArray();

            $page_code = $folder->page->code;
            $page_id = $folder->page->id;
            
            $aws_s3_path = env('AWS_S3_PATH');
            $path = "$aws_s3_path/stores/$pharmacy_store_id/$menu_page/$page_code/$folder_id/";
            
            Storage::disk('s3')->deleteDirectory($path);

            $save = StoreFile::where('folder_id', $folder_id)
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
            $transactionLog->function = 'StoreFolderFileRepository.deleteFolder';
            $transactionLog->action = 'deleted';
            $transactionLog->subject = 'User ID: '.auth()->user()->id . ', Username: '.auth()->user()->name . ' DELETED Folder ID: '.$folder_id.', Folder Name: '.$logs['store_folders']['name'] .' and ('.count($logs['store_files']).') File records inside the folder';
            $transactionLog->data = json_encode($logs);
            $transactionLog->save();
        }
        
    }

    private function getCurrentPSTDate($format = 'Y-m-d', $date = null)
    {

        if(!empty($date)) {
            $pst = Carbon::createFromFormat('Y-m-d', $date);
            $pst = $pst->setTimezone('America/Los_Angeles');
        }else {
            $pst = Carbon::now('America/Los_Angeles');
        }
        
        return $pst->format($format);
    }

}