<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\DrugRecallNotificationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrugRecallNotificationItemController extends Controller
{
    public function store(Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();

                $drugRecallNotificationItem = new DrugRecallNotificationItem();
                $drugRecallNotificationItem->drug_recall_notification_id = $request->drug_recall_notification_id;
                $drugRecallNotificationItem->med_id = $request->med_id;
                $drugRecallNotificationItem->drug_name = $request->drug_name;
                $drugRecallNotificationItem->lot_number = !empty($request->lot_number) ? $request->lot_number : null;
                $drugRecallNotificationItem->qty = !empty($request->qty) ? $request->qty : null;
                $drugRecallNotificationItem->ndc = $request->ndc;
                $drugRecallNotificationItem->expiration_date = !empty($request->expiration_date) ? date('Y-m-d', strtotime($request->expiration_date)) : null;
                $drugRecallNotificationItem->user_id = auth()->user()->id;

                $save = $drugRecallNotificationItem->save();

                DB::commit();

                return json_encode([
                    'data'=> $drugRecallNotificationItem,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in DrugRecallNotificationItemController.store.db_transaction.'
                ]);
            }
        }
    }

    public function update(Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();

                $drugRecallNotificationItem = DrugRecallNotificationItem::findOrfail($request->id);
                $drugRecallNotificationItem->med_id = $request->med_id;
                $drugRecallNotificationItem->drug_name = $request->drug_name;
                $drugRecallNotificationItem->lot_number = !empty($request->lot_number) ? $request->lot_number : null;
                $drugRecallNotificationItem->qty = !empty($request->qty) ? $request->qty : null;
                $drugRecallNotificationItem->ndc = $request->ndc;
                $drugRecallNotificationItem->expiration_date = !empty($request->expiration_date) ? date('Y-m-d', strtotime($request->expiration_date)) : null;
                $drugRecallNotificationItem->user_id = auth()->user()->id;

                $save = $drugRecallNotificationItem->save();

                DB::commit();

                return json_encode([
                    'data'=> $drugRecallNotificationItem,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in DrugRecallNotificationItemController.update.db_transaction.'
                ]);
            }
        }
    }

    public function delete(Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();
                
                $drugRecallNotificationItem = DrugRecallNotificationItem::findOrfail($request->id);
                $item = $drugRecallNotificationItem;

                if(isset($drugRecallNotificationItem->id)) {
                    $drugRecallNotificationItem->delete();
                }

                DB::commit();

                return json_encode([
                    'data'=> $item,
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in DrugRecallNotificationItemController.delete.db_transaction.'
                ]);
            }
        }
    }
}
