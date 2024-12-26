<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Item;

class ItemController extends Controller
{
    public function updateRXStatus(Request $request, $id)
    {
        $item = Item::find($id);

        if ($item) {
            $item->rx_status = $request->value; // Use 'value' from the request
            $item->save();

            return response()->json(['message' => 'Update successful'], 200);
        } else {
            return response()->json(['message' => 'Item not found'], 404);
        }
    }

    public function updateRXStage(Request $request, $id)
    {
        $item = Item::find($id);

        if ($item) {
            $item->rx_stage = $request->value; // Use 'value' from the request
            $item->save();

            return response()->json(['message' => 'Update successful'], 200);
        } else {
            return response()->json(['message' => 'Item not found'], 404);
        }
    }

    public function updateShipmentStatus(Request $request, $id)
    {
        $order = Order::find($id);

        if ($order) {
            $order->shipment_status_id = $request->value; // Use 'value' from the request
            $order->save();

            return response()->json(['message' => 'Update successful'], 200);
        } else {
            return response()->json(['message' => 'Item not found'], 404);
        }
    }

    public function update(Request $request)
    {
        $item = Item::find($request->id);
        if ($item) {
            $item->{$request->column} = $request->value;
            $item->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 404);
        }
    }

    public function update_item_row(Request $request)
    {
        $item = Item::find($request->id);
        if ($item) {
            $item->name = $request->name;
            $item->sig = $request->sig;
            $item->days_supply = $request->days_supply;
            $item->refills_remaining = $request->refills_left;
            $item->ndc = $request->ndc;
            $item->inventory_type = $request->inventory_type;
            $item->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 404);
        }
    }

    public function SaveNewItemRow(Request $request)
    {
        $item = new Item; // Create a new Item instance
        $item->name = $request->name;
        $item->order_id = $request->order_id;
        $item->sig = $request->sig;
        $item->days_supply = $request->days_supply;
        $item->refills_remaining = $request->refills_left;
        $item->ndc = $request->ndc;
        $item->quantity = 1;
        $item->inventory_type = $request->inventory_type;
        $item->medication_id = $request->med_id;
        $item->save(); // Save the new item to the database

        return response()->json(['success' => true, 'id' => $item->id]);
    }

    public function delete_item_via_ajax(Request $request)
    {
        $item = Item::find($request->item_id);
        if ($item) {
            $item->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 404);
        }
    }


}
