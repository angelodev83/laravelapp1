<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\CURL\TebraController;
use App\Http\Helpers\Helper;
use App\Models\Order;
use App\Models\Item;
use App\Models\Patient;
use App\Models\Employee;
use App\Models\File;
use App\Models\Status;
use App\Models\ShipmentStatus;
use App\Models\ShipmentStatusLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MailOrderController extends Controller
{
    public function __construct() {
        
        $this->middleware('permission:menu_store.operations.mail_orders.index|menu_store.operations.mail_orders.create|menu_store.operations.mail_orders.update|menu_store.operations.mail_orders.delete');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Operations', 'Mail Orders'];
            $shipment_statuses = ShipmentStatus::orderBy('sort', 'asc')->get();
            $stores = explode(',', env('STORES'));

            $logs = ShipmentStatusLog::where('order_id', 62)
                ->orderBy('id', 'asc')
                ->get();

            $durations = [];

            for ($i = 0; $i < count($logs); $i++) {
                $currentLog = $logs[$i];
                $nextLog = isset($logs[$i + 1]) ? $logs[$i + 1] : null;

                $duration = $nextLog 
                    ? \Carbon\Carbon::parse($currentLog->changed_at)->diffInMinutes(\Carbon\Carbon::parse($nextLog->changed_at))
                    : \Carbon\Carbon::parse($currentLog->changed_at)->diffInMinutes(\Carbon\Carbon::now());

                $durations[] = [
                    'order_id' => $currentLog->order_id,
                    'changed_at' => \Carbon\Carbon::parse($currentLog->changed_at)->format('M d, h:i A'),
                    'status_name' => $currentLog->shipmentStatus->name,
                    'duration_from_previous_status' => $duration,
                ];
            }

            return view('/stores/operations/mailOrders/index', compact('breadCrumb','shipment_statuses','stores'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function file_upload(Request $request)
    {
        if($request->ajax()){
            $file = $request->file('file');

            $input = $request->all();
            
            $validation = Validator::make($input, [
                'file' => 'required|mimes:pdf',
            ]);
            if ($validation->passes()){
                $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $fileExtension = $file->getClientOriginalExtension();
                $mime_type = $file->getMimeType();
                
                $newFileName = date("Ymdhis").Auth::id() .'_'. $fileName  . '.' . $fileExtension;
                $doc_type = $fileExtension;
                
                $path = 'operations/mailOrders/';
                
                // Provide a dynamic path or use a specific directory in your S3 bucket
                $path_file = 'operations/mailOrders/'  . $newFileName;

                // Store the file in S3
                Storage::disk('s3')->put($path_file, file_get_contents($file));

                // Optionally, get the URL of the uploaded file
                $s3url = Storage::disk('s3')->url($path_file);

                $save_file = new File();

                $save_file->filename = $newFileName;
                $save_file->path = $path;
                $save_file->mime_type = $mime_type;
                $save_file->document_type = $doc_type;
                $save_file->save();

                $file_id = $save_file->id;

                if($input['order_id']){
                    $order = Order::where('id', $input['order_id'])->first();
                    $order->file_id = $file_id;
                    $order->save();
                }

                return response()->json([
                    'file' => $file_id,
                    'fileName' => $fileName,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ], 201);

            }
            else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Check input fields.'
                ], 422);
            }

        }
    }
}
