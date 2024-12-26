<?php

namespace App\Http\Controllers\Marketing;

use App\Models\StoreAnnouncement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Interfaces\Common\AnnouncementInterface;

class StoreAnnouncementController extends Controller
{
    /**
     * 
     * @var StoreAnnouncement
     */
    private $announcement;

    private AnnouncementInterface $repository;

    public function __construct(StoreAnnouncement $announcement
        ,   AnnouncementInterface $repository
    )
    {
        $this->announcement = $announcement;
        $this->repository = $repository;

        $this->repository->setModel('storeBulletinAnnouncement');

        $this->middleware('permission:menu_store.marketing.announcements.index|menu_store.marketing.announcements.create|menu_store.marketing.announcements.update|menu_store.marketing.announcements.delete');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Marketing', 'Announcements'];
            return view('/stores/marketing/announcements/index', compact('breadCrumb'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function show($id)
    {
        // update notification - tag as read
        $n = DB::table('notifications')
            ->where('data->announcement', $id)
            ->where('type', 'App\Notifications\Store\AnnouncementNotification')
            ->where('notifiable_id', auth()->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);
        
        // if($request->ajax()){
            return response()->json([
                'data'=> ['n'=>$n, 'user_id'=>auth()->user()->id, 'id'=>$id],
                'status'=>'success',
                'message'=>'Record has been saved.'
            ], 200);
        // }

        // $announcement = $this->announcement->findOrFail($id);
        // $breadCrumb = ['Bulletin', 'Announcements'];
        // return view('/stores/bulletin/announcements/view', compact('breadCrumb', 'announcement'));
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            $this->repository->setDataTable($request);
            $data = $this->repository->getDataTable();
            
            return response()->json($data, 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($id, Request $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();

                $request->merge(['pharmacy_store_id' => $id]);
                $announcement = $this->repository->store($request);
                // $this->repository->sendStoreNotification($announcement);

                DB::commit();

                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in StoreAnnouncementController.store.db_transaction.'
                ]);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();

                $request->merge(['pharmacy_store_id' => $id]);
                $this->repository->update($request);
                DB::commit();

                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in StoreAnnouncementController.update.db_transaction.'
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
            
            try {
                
                DB::beginTransaction();

                $this->repository->delete($request->id);

                DB::commit();

                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in StoreAnnouncementController.delete.'
                ]);
            }
        }
    }

    public function getAnnouncement(Request $request)
    {
        if($request->ajax()){
            $id = $request->id;
            $storeAnnouncement = StoreAnnouncement::findOrFail($id);
            return response()->json($storeAnnouncement);
        }
    }
}
