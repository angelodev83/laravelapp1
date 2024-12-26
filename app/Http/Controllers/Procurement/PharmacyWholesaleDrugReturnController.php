<?php

namespace App\Http\Controllers\Procurement;

use App\Models\WholesaleDrugReturn;
use App\Models\WholesaleDrugReturnItem;
use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PharmacyWholesaleDrugReturnController extends Controller
{
    public function __construct() {
        $this->middleware('permission:menu_store.procurement.pharmacy.wholesale_drug_returns.index|menu_store.procurement.pharmacy.wholesale_drug_returns.create|menu_store.procurement.pharmacy.wholesale_drug_returns.update|menu_store.procurement.pharmacy.wholesale_drug_returns.delete');
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $this->checkStorePermission($id);
            $years = $this->getYears();
            $months = $this->getMonths();
            $currentYear = now()->year;
            $currentMonth = now()->month;
            $breadCrumb = ['Procurement', 'Pharmacy', 'Wholesale Drug Returns'];
            return view('/stores/procurement/pharmacy/wholesaleDrugReturns/index', [
                'breadCrumb' => $breadCrumb,
                'years' => $years,
                'months' => $months,
                'currentYear' => $currentYear,
                'currentMonth' => $currentMonth,
            ]);
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function filteredData(Request $request) {
        if (request()->ajax()) {
            $selectedYear = $request->input('year');
            $selectedMonth = $request->input('month');

            $returnStatuses = $this->returnStatuses();
            
            $data = [
                'selectedYear' => $selectedYear,
                'selectedMonth' => $selectedMonth,
            ];

            foreach ($returnStatuses as $statusId => $statusName) {
                $data['return' . $statusId] = $this->getTasksByStatusId($statusId, $selectedYear, $selectedMonth);
            }

            return response()->json(['data' => $data], 200);
        }
    }
    
    private function getTasksByStatusId($statusId, $year = null, $month = null) {
        $query = WholesaleDrugReturn::with('status','user.employee','items.medication', 'patient')
                ->where('shipment_status_id', $statusId);

        if ($year && $month) {
            $query->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);
        } elseif ($year) {
            $query->whereYear('created_at', $year);
        } elseif ($month) {
            $query->whereMonth('created_at', $month);
        }

        $value = $query->latest()->get();
        $newData = [];
        foreach ($value as $val) {
            $newData[] = [
                'id' => $val->id,
                'items' => $val->items,
                'prescriber_name' => $val->prescriber_name,
                'patient_name' => ($val->patient ? $val->patient->firstname . ' ' . $val->patient->lastname : null),
                'reference_number' => $val->reference_number,
                'reject_comments' => $val->reject_comments,
                'shipment_status_id' => $val->shipment_status_id,
                'pharmacy_store_id' => $val->pharmacy_store_id
            ];
        }
        return $newData;
    }

    protected function returnStatuses() {
        return [
            '301' => 'LabelToBeCreated',
            '302' => 'LabelCreated',
            '303' => 'LabelPrinted',
            '304' => 'PickedUpOrders',
            '305' => 'InTransitOrders',
            '306' => 'PendingOrders',
            '307' => 'OnHoldOrders',
            '308' => 'DeliveredOrders',
        ];
    }
    
    protected function getMonths() {
        return [
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];
    }

    protected function getYears() {
        return [
            '2024' => '2024',
            '2023' => '2023',
            '2022' => '2022',
            '2021' => '2021',
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
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

            $query = DB::table('view_wholesale_drug_returns');
            
            // Search //input all searchable fields
            $search = trim($request->search);

            if($request->has('pharmacy_store_id')) {
                $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
            }
            
            if(!empty($search)) {
                $query = $query->where(function($query) use (&$search){ 
                    $query->orWhere('reference_number', 'like', "%".$search."%");
                    $query->orWhere('date_filed', 'like', "%".$search."%");
                    $query->orWhere('drugname', 'like', "%".$search."%");   
                    $query->orWhere('dispense_quantity', 'like', "%".$search."%");
                    $query->orWhere('inventory_type', 'like', "%".$search."%");
                    $query->orWhere('shipment_status', 'like', "%".$search."%");
                    $query->orWhere('prescriber_name', 'like', "%".$search."%");
                    $query->orWhere('patient_firstname', 'like', "%".$search."%");
                    $query->orWhere('patient_lastname', 'like', "%".$search."%");
                    $query->orWhere('shipment_tracking_number', 'like', "%".$search."%");
                });
            }

            
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {

                $hidden='';
                $s3Url='';
                $download_hidden='';
                if($value->file_name != ""){
                    $s3Url = Storage::disk('s3')->temporaryUrl(
                        $value->file_path.$value->file_name,
                        now()->addMinutes(30)
                    );
                    ($value->mime_type != 'application/pdf')?$hidden="d-none":'';
                }
                else{
                    $hidden = "d-none";
                    $download_hidden = "d-none";
                }

                $actions = '<div class="d-flex order-actions">';
                if(Auth::user()->can('menu_store.procurement.pharmacy.wholesale_drug_returns.update')) {
                    $actions .= '<a title="Edit" href="javascript:void(0)" class="me-1"><button type="button" class="btn btn-primary btn-sm" 
                        id="wholesale-drug-return-edit-btn-'.$value->id.'"
                        data-array="'.htmlspecialchars(json_encode($value)).'"
                        onclick="showEditModal('.$value->id.');"><i class="fa-solid fa-pencil"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.pharmacy.wholesale_drug_returns.delete')) {
                    $actions .= '<a title="Delete" href="javascript:void(0)" class="me-1"><button type="button" 
                        onclick="ShowConfirmDeleteForm(' . $value->id . ')" 
                        class="btn btn-danger btn-sm" ><i class="fa-solid fa-trash-can"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.pharmacy.wholesale_drug_returns.upload')) {
                    $actions .= '<a title="Upload" href="javascript:void(0)" class="me-1"><button data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"
                        id="upload-show-btn-'.$value->id.'" onclick="showUploadForm(' . $value->id . ')" 
                        class="btn btn-sm btn-secondary" ><i class="fa-solid fa-cloud-arrow-up"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.pharmacy.wholesale_drug_returns.download')) {
                    $actions .= '<a class="'.$download_hidden.' me-1" href="/admin/file/download/'.$value->file_id.'" title="Download File"><button class="btn btn-sm btn-secondary"><i class="fa fa-download"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.procurement.pharmacy.wholesale_drug_returns.pdfview')) {
                    $actions .= '<a target="_blank" href="'.$s3Url.'" class="'.$hidden.' me-1"
                        title="View PDF"><button class="btn btn-sm btn-secondary"><i class="fa-regular fa-file-pdf"></i></button></a>';
                }
                $actions .= '</div>';

                $newData[] = [
                    'id'            => $value->id,
                    'reference_number' => $value->reference_number,
                    'dispense_quantity' => $value->dispense_quantity,
                    'date_filed'    => $value->date_filed,
                    'reject_comments'   => $value->reject_comments,
                    //
                    'ndc'           => $value->ndc,
                    'shipment_tracking_number'  => $value->shipment_tracking_number,
                    'pharmacy_store_id' => $value->pharmacy_store_id,
                    'shipment_status'   => $value->shipment_status,
                    'color'         => $value->color,
                    'shipment_status_id'    => $value->shipment_status_id,
                    'statuses_class'        => $value->statuses_class,
                    'drugname'      => $value->drugname,
                    'drug_id'       => $value->drug_id,
                    'price' => ($value->inventory_type == 'RX')?$value->rx_price:$value->price_340b,
                    'inventory_type'    => $value->inventory_type,
                    'prescriber_name'    => $value->prescriber_name,
                    'patient_firstname' => $value->patient_firstname,
                    'patient_lastname'  => $value->patient_lastname,
                    'status' =>  '<button type="button" onclick="clickStatusBtn(' . $value->id . ')" class="btn btn-'.$value->statuses_class.' btn-sm radius-15 px-3" ><small>'.$value->shipment_status.'</small></button>',
                    'actions' =>  $actions,
                    // 'actions' =>  '<div class="d-flex order-actions">
                    //     <button type="button" class="btn btn-primary btn-sm me-2" 
                    //     id="wholesale-drug-return-edit-btn-'.$value->id.'"
                    //     data-array="'.htmlspecialchars(json_encode($value)).'"
                    //     onclick="showEditModal('.$value->id.');"><i class="fa-solid fa-pencil"></i></button>
                    //     <button type="button" 
                    //     onclick="ShowConfirmDeleteForm(' . $value->id . ')" 
                    //     class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                    // </div>'
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    public function file_upload(Request $request)
    {
        if($request->ajax()){
            $file = $request->file('file');

            $input = $request->all();
            
            $validation = Validator::make($input, [
                // 'file' => 'required|mimes:pdf',
                'id' => 'required',
            ]);
            if ($validation->passes()){
                $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $fileExtension = $file->getClientOriginalExtension();
                $mime_type = $file->getMimeType();
                
                $newFileName = date("Ymdhis").Auth::id() .'_'. $fileName  . '.' . $fileExtension;
                $doc_type = $fileExtension;
                
                $path = 'procurement/wholesale_drug_returns/';
                
                // Provide a dynamic path or use a specific directory in your S3 bucket
                $path_file = 'procurement/wholesale_drug_returns/'  . $newFileName;

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

                $document = WholesaleDrugReturn::where('id', $input['id'])->first();
                $document->file_id = $save_file->id;
                $document->save();
                

                return response()->json([
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


    public function delete_file(Request $request)
    {
        if($request->ajax()){

            $input = $request->all();
            $id = $input['id'];
            
            $file = File::where('id', $id)->first();
            $file_id = $file->id;
            $path = $file->path.$file->filename;

            if($path != ''){
                if(Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }

                $file->delete();   
            }
            
            $order = WholesaleDrugReturn::where('file_id', $file_id)->first();
            $order->file_id = null;
            $order->save();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();
                try {
                    $flag = true;
                    $order = $request->order;
                    $items = $request->items;

                    $wdReturn = new WholesaleDrugReturn();
                    foreach($order as $k => $v) {
                        $wdReturn->$k = $v;
                    }
                    $wdReturn->user_id = auth()->user()->id;
                    $flag = $wdReturn->save();

                    $wdReturnItems = [];
                    if($flag) {
                        for($i = 0; $i < count($items['med_id']); $i++) {
                            if(!empty($items['med_id'][$i]) 
                                // && !empty($items['inventory_type'][$i]) 
                                // && !empty($items['ndc'][$i]) 
                                && !empty($items['dispense_quantity'][$i])
                            ) {
                                $item = [
                                    'med_id' => $items['med_id'][$i],
                                    'inventory_type' => $items['inventory_type'][$i],
                                    'ndc' => $items['ndc'][$i],
                                    'dispense_quantity' => $items['dispense_quantity'][$i],
                                    'return_id' => $wdReturn->id, 
                                    'user_id' => auth()->user()->id,
                                    'created_at' => Carbon::now()
                                ];
                                $wdReturnItems[] = $item;
                            }
                        }
                        $save = WholesaleDrugReturnItem::insert($wdReturnItems);
                        if(!$save) {
                            $flag = false;
                        }
                    } else {
                        $flag = false;
                    }
                    
                    if(!$flag) {
                        throw new \Exception("Something went wrong in PharmacyWholesaleDrugReturnController.add_wholesale_drug_return.db_transaction.");
                    }

                    DB::commit();

                    return json_encode([
                        'data'=> $wdReturnItems,
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack(); 
                    return response()->json([
                        'error' => $e->getMessage(),
                        'message' => 'Something went wrong in PharmacyWholesaleDrugReturnController.add_wholesale_drug_return.db_transaction.'
                    ]);
                }
            }catch(\Exception $e){
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyWholesaleDrugReturnController.add_wholesale_drug_return.'
                ]);
            }
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();
                try {
                    $flag = true;
                    $order = $request->order;
                    $items = $request->items;

                    $wdReturnItem = WholesaleDrugReturnItem::findOrFail($items['id']);
                    if(isset($wdReturnItem->id)) {
                        foreach($items as $k => $v) {
                            if(!empty($v) && $v != 'undefined' && $v != '' && $v != null) {
                                $wdReturnItem->$k = $v;
                            }
                        }
                        $save = $wdReturnItem->save();
                        if(!$save) {
                            throw new \Exception("Something went wrong in PharmacyWholesaleDrugReturnrController.edit_wholesale_drug_return.db_transaction.");
                        }
                    }

                    $wdReturn = WholesaleDrugReturn::findOrFail($wdReturnItem->return_id);
                    foreach($order as $k => $v) {
                        $wdReturn->$k = $v;
                    }
                    $save = $wdReturn->save();

                    if(!$save) {
                        throw new \Exception("Something went wrong in PharmacyWholesaleDrugReturnrController.edit_wholesale_drug_return.db_transaction.");
                    }
                    
                    if(!$flag) {
                        throw new \Exception("Something went wrong in PharmacyWholesaleDrugReturnrController.edit_wholesale_drug_return.db_transaction.");
                    }

                    DB::commit();

                    return json_encode([
                        'data'=> $wdReturn,
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack(); 
                    return response()->json([
                        'error' => $e->getMessage(),
                        'message' => 'Something went wrong in PharmacyWholesaleDrugReturnrController.edit_wholesale_drug_return.db_transaction.'
                    ]);
                }
            }catch(\Exception $e){
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyWholesaleDrugReturnrController.edit_wholesale_drug_return.'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();
                try {
                    
                    $item = WholesaleDrugReturnItem::findOrFail($request->id);
                    $flag = $item->delete();
                    
                    if(!$flag) {
                        throw new \Exception("Something went wrong in PharmacyWholesaleDrugReturnController.delete_wholesale_drug_return.db_transaction.");
                    }

                    DB::commit();

                    return json_encode([
                        'data'=> $item,
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack(); 
                    return response()->json([
                        'error' => $e->getMessage(),
                        'message' => 'Something went wrong in PharmacyWholesaleDrugReturnController.delete_wholesale_drug_return.db_transaction.'
                    ]);
                }
            }catch(\Exception $e){
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyWholesaleDrugReturnController.delete_wholesale_drug_return.'
                ]);
            }
        }
    }
}
