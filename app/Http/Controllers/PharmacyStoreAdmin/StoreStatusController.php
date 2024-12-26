<?php

namespace App\Http\Controllers\PharmacyStoreAdmin;

use App\Models\StoreStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Interfaces\IStoreStatusRepository;

class StoreStatusController extends Controller
{
    private IStoreStatusRepository $repository;

    private $storeStatus;

    public function __construct(
        StoreStatus $storeStatus
        ,   IStoreStatusRepository $repository
    ) {
        $this->storeStatus = $storeStatus;
        $this->repository = $repository;

        // $this->middleware('permission:menu_store.escalation.tickets.index|menu_store.escalation.tickets.create|menu_store.escalation.tickets.update|menu_store.escalation.tickets.delete');
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function search(Request $request)
    {   
        $data = $this->repository->search($request);
        return $request->ajax() ? response()->json($data, 200) : $data;
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
    public function show(StoreStatus $storeStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StoreStatus $storeStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StoreStatus $storeStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StoreStatus $storeStatus)
    {
        //
    }
}
