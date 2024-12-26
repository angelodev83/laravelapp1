<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\SupplyOrderItem;

class PharmacySupplyOrderItemController extends Controller
{
    public function store(Request $request)
    {
        if($request->ajax()){

            try {
                DB::beginTransaction();
                $item = new SupplyOrderItem();
                if($request->url != ''){
                    
                    $item->name = null;
                    $item->description = 'url request';
                    $item->quantity = $request->quantity;
                    $item->actual_quantity = isset($request->actual_quantity) ? $request->actual_quantity : null;
                    $item->code = null;
                    $item->number = null;
                    $item->item_id = null;
                    $item->user_id = auth()->user()->id;
                    $item->order_id = $request->order_id;
                    $item->url = $request->url;
                    $item->save();
                }
                else{
                    
                    $item->name = $request->description;
                    $item->description = $request->description;
                    $item->quantity = $request->quantity;
                    $item->actual_quantity = isset($request->actual_quantity) ? $request->actual_quantity : null;
                    $item->code = $request->code;
                    $item->number = $request->number;
                    $item->item_id = $request->item_id;
                    $item->user_id = auth()->user()->id;
                    $item->order_id = $request->order_id;
                    $save = $item->save();
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
                    'message' => 'Something went wrong in PharmacySupplyOrderItemController.edit_drug_order_item_import_data.db_transaction.'
                ]);
            }
            
        }
    }

    public function update(Request $request)
    {
        if($request->ajax()){

            try {
                DB::beginTransaction();
                
                $id = $request->id;

                $item = SupplyOrderItem::findOrFail($id);

                if($request->url != ''){
                    $item->description = 'url request';
                    $item->quantity = $request->quantity;
                    $item->actual_quantity = $request->actual_quantity;
                    $item->url = $request->url;
                    $item->save();
                }
                else{
                    $item->description = $request->description;
                    $item->quantity = $request->quantity;
                    $item->actual_quantity = $request->actual_quantity;
                    $item->code = $request->code;
                    $item->number = $request->number;
                    $item->item_id = $request->item_id;
                    $save = $item->save();
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
                    'message' => 'Something went wrong in PharmacySupplyOrderItemController.edit_drug_order_item_import_data.db_transaction.'
                ]);
            }
            
        }
    }

    public function delete(Request $request)
    {
        if($request->ajax()){

            try {
                DB::beginTransaction();
                
                $id = $request->id;

                $item = SupplyOrderItem::findOrFail($id);
                $save = $item->delete();
                
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
                    'message' => 'Something went wrong in PharmacySupplyOrderItemController.edit_drug_order_item_import_data.db_transaction.'
                ]);
            }
            
        }
    }
}
