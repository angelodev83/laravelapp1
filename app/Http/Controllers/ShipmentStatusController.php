<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShipmentStatus;

class ShipmentStatusController extends Controller
{

    public function data(Request $request)
    {
        $data = ShipmentStatus::select('id', 'name', 'color', 'text_color')->get();
        if($request->ajax()){

            return json_encode([
                'data'=> $data,
            ]);
        }
        return $data;

    }
}
