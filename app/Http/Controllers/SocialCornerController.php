<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SocialCorner;
use App\Models\SocialCornerReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocialCornerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();

                $sc = new SocialCorner();
                $sc->post = $request->post ?? null;
                $sc->user_id = auth()->user()->id;
                $sc->save();

                DB::commit();

                return json_encode([
                    'data'=> $sc,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in SocialCornerController.store.db_transaction.'
                ]);
            }
        }
    }

    public function loadMore(Request $request)
    {
        try{

            $limit = $request->limit ?? 2;

            $sc = SocialCorner::with(['hearts', 'user.employee'])->limit($limit)->orderBy('created_at', 'desc')->get();

            $result = [
                'data'=> $sc,
                'status'=>'success',
                'message'=> $sc->count().' Record/s has been retrieved.'
            ];

            if($request->ajax()) {
                return json_encode($result);
            }

            return $result;

        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in SocialCornerController.loadMore.db_transaction.'
            ]);
        }
    }


    public function react(Request $request)
    {
        try{
            $id = $request->id ?? null;
            $user_id = auth()->user()->id;

            $scr = SocialCornerReaction::where('social_corner_id', $id)
                ->where('user_id', $user_id)
                ->where('reaction', 'heart')
                ->first();

            $msg = '';

            if(isset($scr->id)) {
                $scr->delete();
                $msg = 'Post has been unliked.';
            } else {
                $scr = new SocialCornerReaction();
                $scr->user_id = $user_id;
                $scr->social_corner_id = $id;
                $scr->save();
                $msg = 'Post has been liked.';
            }

            $result = [
                'data'=> $scr,
                'status'=>'success',
                'message'=> $msg
            ];

            if($request->ajax()) {
                return json_encode($result);
            }

            return $result;

        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in SocialCornerController.react.db_transaction.'
            ]);
        }
    }

}
