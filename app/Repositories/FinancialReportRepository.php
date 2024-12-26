<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Utils\FileIconUtil;

use App\Models\StorePage;
use App\Models\StoreFolder;
use App\Models\StoreFile;
use App\Models\TransactionLog;
use Carbon\Carbon;
use DateTime;
use File;
use Illuminate\Support\Facades\Storage;

class FinancialReportRepository
{
    use FileIconUtil;

    private $pages;
    private $folders;
    private $files;
    private $env;
    public $pageIds, $page_parent_id;
    public $eodReportPageIds, $eod_reports_page_id;
    public $transactionReceiptPageIds, $transaction_receipts_page_id;

    protected $dataTable = [];

    public function __construct(StorePage $pages
        , StoreFolder $folders
        , StoreFile $files
    )
    {
        $this->pages = $pages;
        $this->folders = $folders;
        $this->files = $files;

        $this->page_parent_id = 54;
        $this->pageIds = [55,56,57,58,59,61,62,63,64,72];
        
        $this->eod_reports_page_id = 60;
        $this->eodReportPageIds = [65,66,67];
        
        $this->transaction_receipts_page_id = 68;
        $this->transactionReceiptPageIds = [69,70,71];
        
        $this->env = env('AWS_S3_PATH');
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
        $orderByCol = $request->columns[$request->order[0]['column']]['name'] ?? 'created_at';

        // get data from products table
        $query = StoreFile::with('folder.page');
        

        // Search //input all searchable fields
        $search = $request->search;
        $columns = $request->columns;

        if($request->has('pharmacy_store_id')) {
            $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
        } 

        if($request->has('folder_id')) {
            $query = $query->where('folder_id', $request->folder_id);
        }

        $page_parent_id = '';
        $page_id = '';
        $page_code = '';

        if($request->has('page_parent_id')) {
            $page_parent_id = $request->page_parent_id;

            if(!empty($page_parent_id)) {
                $query = $query->where(function($query) use ($page_parent_id) {
                    $query->whereHas('folder', function($query) use ($page_parent_id) {
                        $query->whereHas('page', function($query) use ($page_parent_id) {
                            $query->where('parent_id', $page_parent_id);
                        });
                    });
                });
            }
        }

        if($request->has('page_id')) {
            $page_id = $request->page_id;
            $page_code = $request->page_code;

            if(!empty($page_id)) {
                $query = $query->where(function($query) use ($page_id) {
                    $query->whereHas('folder', function($query) use ($page_id) {
                        $query->where('page_id', $page_id);
                    });
                });
            }
        }

        // $pageIds = $this->pageIds;
        
        // if($request->has('page_id')) {
        //     $page_code = $request->page_code;
        //     $page_id = $request->page_id;
        //     // if(!empty($page_id)) {
        //     //     $pageIds = [$page_id];
        //     // }
        // }
        
        // $query = $query->where(function($query) use ($pageIds) {
        //     $query->whereHas('folder', function($query) use ($pageIds) {
        //         $query->whereIn('page_id', $pageIds);
        //     });
        // });
        

        

        $query = $query->where(function($query) use ($search, $columns){
            foreach ($columns as $column) {
                if($column['searchable'] === "true" && $search){
                    $query->orWhere("$column[name]", 'like', "%".$search."%");
                }  
            }
        });       

        if($orderByCol == 'created_by') {
            // $query = $query->orderBy('user.employee.firstname', $orderBy);
        } else {
            $query = $query->orderBy($orderByCol, $orderBy);
        }
        
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $hideD = 'hidden';
        $hideAll = '';

        if(!empty($page_code)) {
            if(auth()->user()->can('menu_store.financial_reports.'.$page_code.'.delete'))
            {
                $hideD = '';
            }
        } else {
            if(auth()->user()->canany(['menu_store.financial_reports.pharmacy_gross_revenue.delete', 'menu_store.financial_reports.payments_overview.delete', 'menu_store.financial_reports.collected_payments.delete', 'menu_store.financial_reports.gross_revenue_and_cogs.delete', 'menu_store.financial_reports.account_receivables.delete', 'menu_store.financial_reports.eod_reports.delete']))
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
                        <div class="flex-grow-1 ms-2 mt-2">
                            <p class="font-weight-bold mb-0">'.$created_by.'</p>
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
                        <p class="font-weight-bold mb-0 ms-2 mt-2">'.$created_by.'</p>
                    </div>
                ';
            }
            
            $formatted_created_at = '';
            $formatted_last_modified = '';
            if(!empty($value->created_at)) {
                $formatted_created_at = '<p class="m-0 p-0"><small>'.date('M d, Y', strtotime($value->created_at)).'</small></p>';
                $formatted_created_at .= '<p class="m-0 p-0"><small>'.date('h:iA', strtotime($value->created_at)).'</small></p>';
            }
            if(!empty($value->last_modified)) {
                $formatted_last_modified = '<p class="m-0 p-0"><small>'.date('M d, Y', strtotime($value->last_modified)).'</small></p>';
                $formatted_last_modified .= '<p class="m-0 p-0"><small>'.date('h:iA', strtotime($value->last_modified)).'</small></p>';
            }

            $filename = strlen($value->name) > 37 ? (substr($value->name, 0, 37)).'...' : $value->name;

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
                'file' => '
                    <a target="_blank" href="'.$s3Url.'" class="text-black">
                        <div class="d-flex align-items-center knowledge-base-file-name" title="'.$value->name.'">
                            <div><i class="bx '.$icon.' me-2 font-24 '.$color.'"></i></div>
                            <div>
                                <div class="font-weight-bold">'.$filename.'</div>
                                <div class="text-body-secondary">'.$created_by.' | '.$date.' | '.$time.'</div>
                            </div>
                        </div>
                    </a>
                ',
                'updated_at' => ($value->updated_at === null)?'':date('M d, Y h:iA', strtotime($value->updated_at)),
                'actions' =>  '<div class="d-flex order-actions" '.$hideAll.'>
                    <a href="/admin/store-file/download/s3/'.$value->id.'"><button type="button" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-download"></i>
                    </button></a>
                    <button class="btn btn-sm btn-danger ms-2" 
                        id="btn-financial-report-delete-'.$value->id.'"
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

    function getUsedFiles($page_id = null)
    {
        $pageIds = [$page_id];
        if(empty($page_id)) {
            $pageIds = $this->pageIds;
        }
        if($page_id == $this->page_parent_id) {
            $pageIds = $this->pageIds;
        }
        if($page_id == $this->eod_reports_page_id) {
            $pageIds = $this->eodReportPageIds;
        }
        if($page_id == $this->transaction_receipts_page_id) {
            $pageIds = $this->transactionReceiptPageIds;
        }

        return $this->getUsedFilesData($pageIds);
    }

    public function getFilesCountPerPage($page_parent_id)
    {

        $data = [];
        $pages = StorePage::with('folders.files')->where('parent_id', $page_parent_id)->get();
        foreach($pages as $page) {
            $folders = $page->folders;
            $code = $page->code;
            $data[$code] = ['count' => 0];
            foreach($folders as $folder) {
                $count = $folder->files->count();
                $data[$code]['count'] += $count;
            }

        }

        return $data;
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
        $pharmacy_store_id = $data->pharmacy_store_id;
        $page_code = $data->page_code;
        if(empty($folder_id)) {
            $folder = new $this->folders;
            $folder->name = $data->new_folder;
            $folder->page_id = $data->page_id;
            $folder->user_id = auth()->user()->id;
            $folder->save();
            $folder_id = $folder->id;
        }

        if ($request->file('files')) {
            $files = $request->file('files');
            foreach ($files as $key => $file) {
                $path = "/$this->env/stores/$pharmacy_store_id/financial-reports/$page_code/$folder_id/";
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

                $save = $document->save();

                if($save) {
                    $pathfile = $document->path.$document->name;
                    Storage::disk('s3')->put($pathfile, file_get_contents($file));
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
        $file = $this->files->with('folder.page')->findOrFail($id);

        $logs = [
            'store_files' => $file
        ];
        
        if(isset($file->folder)) {
            $pharmacy_store_id = $file->pharmacy_store_id;
            
            $page = $file->folder->page;
            $page_id = $page->id;
            
            $name = 'menu_store.financial_reports.'.$page->code.'.delete';

            if(auth()->user()->can($name)) {

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
                $transactionLog->module_id = $id;
                $transactionLog->function = 'FinancialReportRepository.delete';
                $transactionLog->action = 'deleted';
                $transactionLog->subject = 'User ID: '.auth()->user()->id . ', Username: '.auth()->user()->name . ' DELETED File ID: '.$id.', File Name: '.$logs['store_files']['name'];
                $transactionLog->data = json_encode($logs);
                $transactionLog->save();

            } else {
                throw new \Exception("Permission Denied. Not Authorized to Delete");
            }

        }

    }

}