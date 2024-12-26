<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\OperationOrder;
use App\Models\File as ModelFile;
use App\Interfaces\UploadInterface;
use Carbon\Carbon;

class OperationOrderController extends Controller
{
    private UploadInterface $repository;

    public function __construct(UploadInterface $repository)
    {
        $this->repository = $repository;
        $this->middleware('permission:menu_store.operations.for_shipping_today.index');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Operations', 'For Shipping Today'];
            return view('/stores/operations/forShippingToday/index', compact('breadCrumb'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
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

            $query = OperationOrder::with('file')->where('status', 'For Shipping Today');
            
            // Search //input all searchable fields
            $search = $request->search;

            $search = trim($request->search);

            if($request->has('pharmacy_store_id')) {
                $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
            }

            if($request->has('shipped_date')) {
                $shipped_date = $request->shipped_date;
                if(!empty($shipped_date)) {
                    $shipped_date = date('Y-m-d', strtotime($shipped_date));
                    $query = $query->where('shipped_date', $shipped_date);
                }
            }

            if($request->has('ship_by_date')) {
                $ship_by_date = $request->ship_by_date;
                if(!empty($ship_by_date)) {
                    $ship_by_date = date('Y-m-d', strtotime($ship_by_date));
                    $query = $query->where('ship_by_date', $ship_by_date);
                }
            }
            
            if(!empty($search)) {
                $query = $query->where(function($query) use ($search){ 
                    // $query->orWhere('patient_name', 'like', "%".$search."%");
                    $query->orWhere('firstname', 'like', "%".$search."%");
                    $query->orWhere('lastname', 'like', "%".$search."%");
                    $query->orWhere('address', 'like', "%".$search."%");
                    $query->orWhere('email', 'like', "%".$search."%");
                    $query->orWhere('rx_number', 'like', "%".$search."%");
                    $query->orWhere('tracking_number', 'like', "%".$search."%");
                });
            }

            
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $hideU = 'hidden';
            $hideD = 'hidden';
            $hideAll = 'hidden';
            if(auth()->user()->can('menu_store.operations.for_shipping_today.update')) {
                $hideU = ''; $hideAll = '';
            }
            if(auth()->user()->can('menu_store.operations.for_shipping_today.delete')) {
                $hideD = ''; $hideAll = '';
            }

            $newData = [];
            foreach ($data as $value) {
                $file = isset($value->file->id) ? $value->file : null;
                $shippingLabel = '<button class="btn btn-info2 btn-sm" title="Upload Shipping Label File" onclick="clickUploadFileIcon('.$value->id.')"><i class="fa fa-cloud-arrow-up"></i></button>';
                $s3Url='';
                if(!empty($file)) {
                    $path = $file->path.$file->filename;
                    $shippingLabel = '<button class="btn btn-info2 btn-sm" title="Replace Shipping Label File" onclick="clickUploadFileIcon('.$value->id.', \''.$file->filename.'\', '.$file->id.')"><i class="fa fa-cloud-arrow-up"></i></button>';
                    $shippingLabel .= '<a class="ms-2" href="/admin/file/download/'.$file->id.'" title="Download File"><button class="btn btn-sm btn-secondary"><i class="fa fa-download"></i></button></a>';
                    $s3Url = Storage::disk('s3')->temporaryUrl(
                        $path,
                        now()->addMinutes(30)
                    );
                    if(strtolower($file->document_type) == 'pdf') {
                        $shippingLabel .= '<a target="_blank" href="'.$s3Url.'" class="ms-2" title="View PDF"><button class="btn btn-sm btn-danger"><i class="fa-regular fa-file-pdf"></i></button></a>';
                    }
                }

                $formatted_tracking_number = $value->tracking_number;
                if(!empty($s3Url)) {
                    $formatted_tracking_number = '<a target="_blank" href="'.$s3Url.'" class="text-primary">'.$value->tracking_number.'</a>';
                }
                
                $newData[] = [
                    'is_completed' => $value->is_completed,
                    'id' => $value->id,
                    'patient_name' => $value->lastname.', '.$value->firstname,
                    'firstname' => $value->firstname,
                    'lastname' => $value->lastname,
                    'dob' => $value->dob,
                    'address' => $value->address,
                    'city' => $value->city,
                    'state' => $value->state,
                    'phone_number' => $value->phone_number,
                    'email' => $value->email,
                    'rx_number' => $value->rx_number,
                    'tracking_number' => $value->tracking_number,
                    'formatted_tracking_number' => $formatted_tracking_number,
                    'shipped_date' => $value->shipped_date,
                    'status' => $value->is_completed,
                    'labeled_date' => $value->labeled_date,
                    'ship_by_date' => $value->ship_by_date,
                    'formatted_ship_by_date' => date('F d, Y', strtotime($value->ship_by_date)),
                    // 'shipping_label' => $value->shipping_label,
                    'shipping_label' => $shippingLabel,
                    'file' => $file,
                    // 'created_by' => $value->user->name,
                    'actions' =>  '<div class="d-flex order-actions" '.$hideAll.'>
                        <button type="button" class="btn btn-primary btn-sm me-2" 
                        id="for-shipping-today-edit-btn-'.$value->id.'"
                        data-array="'.htmlspecialchars(json_encode($value)).'"
                        onclick="showEditModal('.$value->id.');" '.$hideU.'><i class="fa-solid fa-pencil"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="ShowConfirmDeleteForm(' . $value->id . ')" '.$hideD.'><i class="fa fa-trash-can"></i></button>
                    </div>'
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    public function updateCompleted(Request $request)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();
                $checkedIds = $request->checked_ids;
                $uncheckedIds = $request->unselected_ids;

                foreach ($checkedIds as $id) {
                    $order = OperationOrder::find($id);
                    if ($order && $order->is_completed == 0) {
                        $today = Carbon::now('America/Los_Angeles')->format('Y-m-d');
                        $order->update(['is_completed' => 1, 'shipped_date' => $today]);
                    }
                }

                foreach ($uncheckedIds as $id) {
                    $order = OperationOrder::find($id);
                    if ($order && $order->is_completed == 1) {
                        $order->update(['is_completed' => 0, 'shipped_date' => null]);
                    }
                }

                DB::commit();
                
                return json_encode([
                    'status'=>'success',
                    'message'=>'Successfully marked as shipped.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in ForDeliveryTodayController.updateCompleted.'
            ]);
        }
    }

    public function upload(Request $request)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();
                $this->repository->uploadOperationOrderFST($request);
                DB::commit();
                
                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in OperationOrderController.upload.'
            ]);
        }
    }

    public function uploadShippingLabel(Request $request, $id = null)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();
                $request->merge(['id' => $id]);
                $status_type = 'For Shipping Today';
                $this->repository->uploadOperationOrderShippingLabel($request, $status_type);
                DB::commit();
                
                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in OperationOrderController.upload.'
            ]);
        }
    }

    public function deleteShippingLabel(Request $request)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();
                
                $id = $request->id;
                $file = ModelFile::where('id', $id)->first();
                $path = $file->path.$file->filename;

                if($path != ''){
                    if(Storage::disk('s3')->exists($path)) {
                        Storage::disk('s3')->delete($path);
                    }
                    $file->delete();   
                }

                $order = OperationOrder::where('file_id', $id)->first();
                $order->file_id = null;
                $order->save();

                DB::commit();
                
                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in OperationOrderController.upload.'
            ]);
        }
    }

    public function uploadBulkShippingLabel(Request $request)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();
                $request->merge(['id' => null]);
                $status_type = 'For Shipping Today';
                $this->repository->uploadOperationOrderShippingLabel($request, $status_type);
                DB::commit();
                
                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in OperationOrderController.upload.'
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();
                
                $id = $request->id;
                $order = OperationOrder::findOrFail($id);
                if(isset($order->id))
                {
                    $order->update($request->all());
                }

                DB::commit();
                
                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in OperationOrderController.upload.'
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();
            if($request->ajax()){
            
                $order = OperationOrder::findOrFail($request->id);

                $file = ModelFile::where('id', $order->file_id)->first();
                if(isset($file->id)) {
                    $path = $file->path.$file->filename;
    
                    if($path != ''){
                        if(Storage::disk('s3')->exists($path)) {
                            Storage::disk('s3')->delete($path);
                        }
                        $file->delete();   
                    }
                }

                $order->delete();

                DB::commit();
    
                return json_encode([
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
            }
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in OperationOrderController.delete.'
            ]);
        }
    }

}
