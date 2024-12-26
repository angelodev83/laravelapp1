<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\DrugOrderItemsImportData;

class PharmacyDrugOrderItemController extends Controller
{
    public function store(Request $request)
    {
        if($request->ajax()){

            try {
                DB::beginTransaction();
                
                $item = new DrugOrderItemsImportData();
                $item->product_description = $request->product_description;
                $item->quantity_ordered = $request->quantity_ordered;
                $item->quantity_confirmed = $request->quantity_confirmed;
                $item->expected_quantity_shipped = $request->expected_quantity_shipped;
                $item->quantity_shipped = $request->quantity_shipped;
                $item->acq_cost = $request->acq_cost;
                $item->ndc = $request->ndc;
                $item->user_id = auth()->user()->id;
                $item->drug_order_id = $request->drug_order_id;
                $save = $item->save();
                
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
                    'message' => 'Something went wrong in PharmacyDrugOrderItemController.edit_drug_order_item_import_data.db_transaction.'
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

                $item = DrugOrderItemsImportData::findOrFail($id);
                $item->product_description = $request->product_description;
                $item->quantity_ordered = $request->quantity_ordered;
                $item->quantity_confirmed = $request->quantity_confirmed;
                $item->expected_quantity_shipped = $request->expected_quantity_shipped;
                $item->quantity_shipped = $request->quantity_shipped;
                $item->acq_cost = $request->acq_cost;
                $item->ndc = $request->ndc;
                $save = $item->save();
                
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
                    'message' => 'Something went wrong in PharmacyDrugOrderItemController.edit_drug_order_item_import_data.db_transaction.'
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

                $item = DrugOrderItemsImportData::findOrFail($id);
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
                    'message' => 'Something went wrong in PharmacyDrugOrderItemController.edit_drug_order_item_import_data.db_transaction.'
                ]);
            }
            
        }
    }
}
