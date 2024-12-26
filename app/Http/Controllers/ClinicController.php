<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function getNames(Request $request)
    {
        $data = Clinic::select("id", "name");
        if($request->has('term')) {
            $data = $data->where('name', 'like', "%".$request->term."%");
        }
        if($request->has('limit')) {
            $data = $data->take($request->limit);
        }

        $data = $data->orderBy('name','asc')->get();
        
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Clinic $clinic)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Clinic $clinic)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Clinic $clinic)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Clinic $clinic)
    {
        //
    }
}
