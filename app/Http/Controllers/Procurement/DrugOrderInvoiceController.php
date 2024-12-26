<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Utils\FileIconUtil;
use App\Models\DrugOrder;
use App\Models\Icon;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DrugOrderInvoiceController extends Controller
{
    use FileIconUtil;

    public function index($id, $year, $month_number)
    {
        try {
            $this->checkStorePermission($id);      
            
            $icons = Icon::select(DB::raw('DISTINCT name'),'id', 'path', 'store_page_id')->get();
            $page_id = 79;
            $permissions = [
                'create' => ['menu_store.procurement.pharmacy.drug_order_invoice.create'],
                'update' => ['menu_store.procurement.pharmacy.drug_order_invoice.update'],
                'delete' => ['menu_store.procurement.pharmacy.drug_order_invoice.delete'],
            ];
            $folders = [];

            $monthlyCountFolders = [];
            foreach($folders as $f) {
                $count = $f->files->count();
                $monthlyCountFolders[$f->id] = $count;
            }

            $weeks = [];//$this->getWeeksStartAndEndDatesUTC($year, $month_number);

            $breadCrumb = ['Procurement', 'Pharmacy', 'Drug Order Invoices'];
            return view('/stores/procurement/pharmacy/drugOrderInvoices/index', compact('breadCrumb', 'folders', 'page_id', 'permissions', 'icons', 'monthlyCountFolders', 'weeks'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            $permissions = [
                'permissions' => [
                    'prefix' => 'menu_store.procurement.pharmacy',
                    'delete' => ['menu_store.procurement.pharmacy.drug_order_invoice.delete'],
                ]
            ];
            $request->merge($permissions);
            $data = $this->dataTable($request);
            return response()->json($data, 200);
        }
    }

    private function dataTable($request)
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
        $query = DrugOrder::with('file');
        

        // Search //input all searchable fields
        $search = $request->search;
        $columns = $request->columns;

        if($request->has('pharmacy_store_id')) {
            $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
        } 

        $query = $query->has('file');

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
                $query = $query->where(DB::raw('YEAR(order_date)'), $year);
            }
            if(!empty($month)) {
                $query = $query->where(DB::raw('MONTH(order_date)'), $month);
            }
        }
        // end filter -- if has tags

        if($orderByCol == 'created_by') {
            // $query = $query->orderBy('user.employee.firstname', $orderBy);
        } else {
            // $query = $query->orderBy($orderByCol, $orderBy);
        }

        if($orderByCol == 'folder_name') {
            $query = $query->orderBy('order_date', $orderBy);
        }
        
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $hideD = 'hidden';
        $hideAll = '';

        $permissions = $request->permissions;
        $permission_prefix = isset($permissions['prefix']) ? $permissions['prefix'] : '';
        $permission_delete = isset($permissions['delete']) ? $permissions['delete'] : [];
        
        if(auth()->user()->canany($permission_delete))
        {
            $hideD = '';
        }

        $icons = $this->styles();

        $newData = [];
        foreach ($data as $value) {
            $type = !empty($value->ext) ? strtolower($value->ext) : "default";
            $icon = isset($icons[$type]) ? $icons[$type]["icon"] : $icons["default"]["icon"];
            $color = isset($icons[$type]) ? $icons[$type]["color"] : $icons["default"]["color"];
            $emp = $value->user->employee;

            $page_name = '';
            $description = '';

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

            $filename = date('m.d.Y', strtotime($value->order_date)).' Invoice';

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

            $path = $value->file->path.$value->file->filename;
            $s3Url = Storage::disk('s3')->temporaryUrl(
                $path,
                now()->addMinutes(30)
            );

            $pstCreatedAt = Carbon::parse($value->file->created_at)->setTimezone('America/Los_Angeles');

            $createdAt = new DateTime($value->created_at->setTimezone('America/Los_Angeles'));
            $date = $createdAt->format('Y-m-d');
            $time = $createdAt->format('h:i A');
            $newData[] = [
                'id'    => $value->id,
                'order_date'    => $value->order_date,
                'po_name'    => $value->order_number,
                'name'  => $value->file->filename,
                'created_by' => $created_by,
                'created_at' => $value->created_at,
                'formatted_created_at' => $formatted_created_at,
                'empAvatar' => $empAvatar,
                'folder_name' => $value->file->created_at,
                'permissions' => $permissions,
                'formatted_folder_name' => $pstCreatedAt->format('M d, Y g:i A'),
                'file' => '
                    <a target="_blank" href="'.$s3Url.'" class="text-black d-flex align-items-center">
                        <img class="rounded-3 me-3" width="50" height="50" style="background-color:  #fcd0b2; border: solid 2px  #fcd0b2;" src="/source-images/marketing/98.png" alt="">
                        <div>
                            <b class="knowledge-base-file-name">'.$filename.'</b>
                            <div class="text-body-secondary">'.$created_by.' | '.$date.' | '.$time.'</div>
                        </div>
                    </a>
                ',
                'updated_at' => ($value->updated_at === null)?'':date('M d, Y h:iA', strtotime($value->updated_at)),
                'actions' =>  '<div class="d-flex order-actions" '.$hideAll.'>
                    <a href="/admin/file/download/'.$value->file->id.'"><button type="button" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-download"></i>
                    </button></a>
                    <button class="btn btn-sm btn-danger ms-2" 
                        id="btn-knowledge-base-delete-'.$value->file->id.'"
                        data-subject="'.htmlspecialchars(addslashes($filename)).'" 
                        data-description="'.htmlspecialchars(addslashes($filename.' File from Drug Order PO Name: '.$value->order_number)).'" 
                        onclick="ShowConfirmDeleteForm(' . $value->file->id . ')" '.$hideD.'><i class="fa fa-trash-can"></i>
                    </button>
                </div>'
            ];
        } 

        return [
            "draw"=> $request->draw, 
            "recordsTotal"=> $recordsTotal, 
            "recordsFiltered" => $recordsFiltered, 
            'data' => $newData
        ];
    }


}
