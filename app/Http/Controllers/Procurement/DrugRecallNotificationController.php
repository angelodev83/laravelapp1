<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\DrugRecallNotification;
use App\Models\DrugRecallNotificationItem;
use App\Models\StoreDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DrugRecallNotificationController extends Controller
{

    public function __construct() {
        $this->middleware('permission:menu_store.procurement.drug_recall_notifications.index|menu_store.procurement.drug_recall_notifications.create|menu_store.procurement.drug_recall_notifications.update|menu_store.procurement.drug_recall_notifications.delete|menu_store.procurement.drug_recall_notifications.view_all');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Procurement', 'Drug Recall Returns'];
            return view('/stores/procurement/drugRecallNotifications/index', compact('breadCrumb'));
        } catch (\Exception $e) {
            if($e->getCode()==403) {
                return response()->view('/errors/403/index', [], 403);
            }
            return [
                'code' => $e->getCode(),
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            $query = DrugRecallNotification::with('items', 'wholesaler', 'user', 'documents');
            
            // Search //input all searchable fields
            $search = $request->search;

            $search = trim($request->search);

            if($request->has('pharmacy_store_id')) {
                $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
            }
            
            if(!empty($search)) {
                $query = $query->where(function($query) use ($search){ 
                    $query->orWhere('reference_number', 'like', "%".$search."%");
                    $query = $query->whereHas('wholesaler', function($query) use ($search){ 
                        $query->orWhere('name', 'like', "%".$search."%");
                    });
                });
            }

            
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {
                $actions = '<button type="button" class="btn btn-primary btn-sm me-1" 
                    id="notification-btn-'.$value->id.'"
                    data-array="'.htmlspecialchars(json_encode($value)).'"
                onclick="showViewDrugRecallNotificationModal('.$value->id.');"><i class="fa-solid fa-eye"></i></button>';
                if(auth()->user()->canany(['menu_store.procurement.drug_recall_notifications.update'])) {
                    $actions .= '<button type="button" class="btn btn-primary btn-sm me-1" 
                            onclick="showEditDrugRecallNotificationModal('.$value->id.');"><i class="fa-solid fa-pencil"></i></button>';
                }
                if(auth()->user()->can('menu_store.procurement.drug_recall_notifications.delete')) {
                    $actions .= '<button type="button" 
                        onclick="ShowConfirmDeleteForm(' . $value->id . ')" 
                        class="btn btn-danger btn-sm me-1" ><i class="fa-solid fa-trash-can"></i></button>';
                }
                $actions .= '';


                $newData[] = [
                    'id' => $value->id,
                    'reference_number'  => $value->reference_number,
                    'notice_date'       => $value->notice_date,
                    'formatted_notice_date' => !empty($value->notice_date) ? date('F d, Y', strtotime($value->notice_date)) : '',
                    'supplier_name'     => $value->supplier_name,
                    'count_drugs'       => $value->items->count(),
                    'count_documents'   => $value->documents->count(),
                    'wholesaler'        => isset($value->wholesaler) ? $value->wholesaler->name : '',
                    'created_at'        => date('M d, Y g:i A', strtotime($value->pst_created_at)),
                    'actions'           =>  $actions,
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();

                $data = json_decode($request->data);

                $flag = true;
                $detail = (array) $data->detail;
                $items = (array) $data->items;
                $aws_s3_path = env('AWS_S3_PATH');

                $notice_date = $detail['notice_date'] ?? null;
                if(!empty($notice_date)) {
                    $notice_date = date('Y-m-d', strtotime($notice_date));
                }

                $drugRecallNotification = new DrugRecallNotification();
                $drugRecallNotification->pharmacy_store_id = $detail['pharmacy_store_id'];
                $drugRecallNotification->reference_number = $detail['reference_number'] ?? null;
                $drugRecallNotification->notice_date = $notice_date;
                $drugRecallNotification->supplier_name = $detail['supplier_name'] ?? null;
                $drugRecallNotification->comments = $detail['comments'] ?? null;
                $drugRecallNotification->wholesaler_id = isset($detail['wholesaler_id']) ? $detail['wholesaler_id'] : 2;
                $drugRecallNotification->user_id = auth()->user()->id;

                $flag = $drugRecallNotification->save();

                $drugRecallNotificationItems = [];
                if($flag) {
                    for($i = 0; $i < count($items['med_id']); $i++) {
                        if(!empty($items['med_id'][$i])) {
                            $expiration_date = !empty($items['expiration_date'][$i]) ? $items['expiration_date'][$i] : null;
                            $item = [
                                'med_id' => $items['med_id'][$i] ?? null,
                                'drug_name' => $items['drug_name'][$i] ?? null,
                                'lot_number' => $items['lot_number'][$i] ?? null,
                                'qty' => $items['qty'][$i] ?? null,
                                'ndc' => $items['ndc'][$i] ?? null,
                                'expiration_date' => $expiration_date ?? null, 
                                'drug_recall_notification_id' => $drugRecallNotification->id, 
                                'user_id' => auth()->user()->id,
                                'created_at' => Carbon::now()
                            ];
                            $drugRecallNotificationItems[] = $item;
                        }
                    }
                    $save = DrugRecallNotificationItem::insertOrIgnore($drugRecallNotificationItems);
                    if(!$save) {
                        $flag = false;
                    }
                    if ($request->file('files')) {
                        $files = $request->file('files');
                        foreach ($files as $key => $file) {
                            $document = new StoreDocument();
                            $document->user_id = auth()->user()->id;
                            $document->parent_id = $drugRecallNotification->id;
                            $document->category = 'drugRecallNotification';
                            
                            $document->name = $file->getClientOriginalName();
                            $document->ext = $file->getClientOriginalExtension();
                            $document->mime_type = $file->getMimeType();
                            $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
                            $document->size = $file->getSize()/1024;
                            $document->size_type = 'KB';
        
                            $date = date('YmdHis');
                            $path = "/$aws_s3_path/stores/$drugRecallNotification->pharmacy_store_id/procurement/drugRecallNotifications/$drugRecallNotification->id/$date";
                            $document->path = $path;
        
                            $save = $document->save();
        
                            if(!$save) {
                                $flag = false;
                            }
        
                            if($save) {
                                $pathfile = $document->path.$document->name;
                                Storage::disk('s3')->put($pathfile, file_get_contents($file));
                            }
                        }
                    }
                } else {
                    $flag = false;
                }
                
                if(!$flag) {
                    throw new \Exception("Something went wrong in DrugRecallNotificationController.store.db_transaction.");
                }

                DB::commit();

                return json_encode([
                    'data'=> $drugRecallNotification,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in DrugRecallNotificationController.store.db_transaction.'
                ]);
            }
        }
    }

    public function update(Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();

                $drugRecallNotification = DrugRecallNotification::findOrfail($request->id);
                $drugRecallNotification->wholesaler_id = $request->wholesaler_id;
                $drugRecallNotification->supplier_name = !empty($request->supplier_name) ? $request->supplier_name : null;
                $drugRecallNotification->comments = !empty($request->comments) ? $request->comments : null;
                $drugRecallNotification->notice_date = !empty($request->notice_date) ? date('Y-m-d', strtotime($request->notice_date)) : null;

                $save = $drugRecallNotification->save();

                DB::commit();

                return json_encode([
                    'data'=> $drugRecallNotification,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in DrugRecallNotificationController.update.db_transaction.'
                ]);
            }
        }
    }

    public function delete(Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();
                
                $files = StoreDocument::where('parent_id',$request->id)->where('category', 'drugRecallNotification')->get();
                
                foreach($files as $file) {
                    $path = $file->path.$file->name;
                    
                    if($path != ''){
                        if(Storage::disk('s3')->exists($path)) {
                            Storage::disk('s3')->delete($path);
                        }
                        
                        $file->delete();   
                    }
                }

                $notification = DrugRecallNotification::findOrFail($request->id);
                $flag = DrugRecallNotificationItem::where('drug_recall_notification_id', $notification->id)->delete();
                $notification->delete();

                DB::commit();

                return json_encode([
                    'data'=> $notification,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in DrugRecallNotificationController.delete.db_transaction.'
                ]);
            }
        }
    }

    public function documents(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            $query = StoreDocument::with('user')->where('category', 'drugRecallNotification');
            
            // Search //input all searchable fields
            $search = $request->search;

            $search = trim($request->search);

            if($request->has('parent_id')) {
                $parent_id = $request->parent_id;
                if(!empty($parent_id)) {
                    $query = $query->where('parent_id', $parent_id);
                } else {
                    $query = $query->whereRaw(0);
                }
            }
            
            if(!empty($search)) {
                $query = $query->where(function($query) use ($search){ 
                    $query->orWhere('name', 'like', "%".$search."%");
                });
            }

            
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {
                $actions = '<a href="/admin/store-document/download/s3/'.$value->id.'"><button type="button" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-download"></i>
                        </button></a>';
                if(auth()->user()->can('menu_store.procurement.drug_recall_notifications.update')) {
                    $actions .= '
                        <button type="button" 
                        onclick="clickDeleteDocumentBtn(event, ' . $value->id . ')" 
                        class="btn btn-danger btn-sm me-1" ><i class="fa-solid fa-trash-can"></i></button>';
                }
                $actions .= '';

                $s3Url = Storage::disk('s3')->temporaryUrl(
                    $value->path.$value->name,
                    now()->addMinutes(30)
                );


                $newData[] = [
                    'id'                => $value->id,
                    'name'              => $value->name,
                    'path'              => $value->path,
                    's3Url'             => $s3Url,
                    'created_at'        => $value->created_at->format('M d, Y h:iA'),
                    'actions'           => $actions,
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    public function upload(Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();

                $parent_id = $request->parent_id;
                $pharmacy_store_id = $request->pharmacy_store_id;
                $aws_s3_path = env('AWS_S3_PATH');

                $save = false;

                if ($request->file('files')) {
                    $files = $request->file('files');
                    foreach ($files as $key => $file) {
                        $document = new StoreDocument();
                        $document->user_id = auth()->user()->id;
                        $document->parent_id = $parent_id;
                        $document->category = 'drugRecallNotification';
                        
                        $document->name = $file->getClientOriginalName();
                        $document->ext = $file->getClientOriginalExtension();
                        $document->mime_type = $file->getMimeType();
                        $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
                        $document->size = $file->getSize()/1024;
                        $document->size_type = 'KB';
    
                        $date = date('YmdHis');
                        $path = "/$aws_s3_path/stores/$pharmacy_store_id/procurement/drugRecallNotifications/$parent_id/$date";
                        $document->path = $path;
    
                        $save = $document->save();
    
                        if(!$save) {
                            $flag = false;
                        }
    
                        if($save) {
                            $pathfile = $document->path.$document->name;
                            Storage::disk('s3')->put($pathfile, file_get_contents($file));
                        }
                    }
                }

                DB::commit();

                return json_encode([
                    'data'=> $save,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in DrugRecallNotificationController.upload.db_transaction.'
                ]);
            }
        }
    }

}
