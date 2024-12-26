<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StoreFile;
use App\Models\StorePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

ini_set('max_execution_time', '3600');

class StoreFileController extends Controller
{
    public function downloadS3($id)
    {
        $file = StoreFile::findOrFail($id);
        
        $headers = [
            'Content-Type'        => 'Content-Type: '.$file->mime_type.' ',
            'Content-Disposition' => 'attachment; filename="'. $file->name .'"',
        ];
        
        $path = $file->path.$file->name;
        
        return Response::make(Storage::disk('s3')->get($path), 200, $headers);
    }

    public function recentPerPage(Request $request)
    {
        $page_id = $request->page_id ?? null;
        $parent_page_id = $request->parent_page_id ?? null;
        $pharmacy_store_id = $request->pharmacy_store_id ?? null;
        $limit = $request->limit ?? 5;

        // $pageIds = [];
        // if(!empty($page_id)) {
        //     $pageIds = StorePage::where('parent_id', $page_id)->pluck('id');
        // }

        $query = StoreFile::with('folder.page');
        $query = $query->whereHas('folder', function($query) use ($page_id, $parent_page_id){
            if(!empty($parent_page_id)) {
                $query->whereHas('page', function($query) use ($page_id, $parent_page_id){
                    $query->where('parent_id', $parent_page_id);
                });
            }
            if(!empty($page_id)) {
                $query->where('page_id', $page_id);
            }
        });
        $query = $query->where('pharmacy_store_id', $pharmacy_store_id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $data = [
            'data' => $query,
            'status' => 'status',
            'message' => 'Recent Store Files'
        ];

        if($request->ajax()) {
            return json_encode($data);
        }

        return $data;
    }
}
