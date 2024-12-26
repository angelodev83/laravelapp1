<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Icon;
use App\Models\StoreDocument;
use App\Models\StoreDocumentTag;
use App\Models\Tag;
use App\Repositories\StoreDocumentTagRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControlCountController extends Controller
{
    private StoreDocumentTagRepository $storeDocumentTagRepository;

    public function __construct(
        StoreDocumentTagRepository $storeDocumentTagRepository
    ) {
        $this->storeDocumentTagRepository = $storeDocumentTagRepository;

        $this->middleware('permission:menu_store.compliance.monthly_control_counts.index|menu_store.compliance.monthly_control_counts.create|menu_store.compliance.monthly_control_counts.update|menu_store.compliance.monthly_control_counts.delete');
    }

    public function index($id, $year, $month_number)
    {
        try {
            $this->checkStorePermission($id);      
            
            $icons = Icon::select(DB::raw('DISTINCT name'),'id', 'path', 'store_page_id')->get();

            $permissions = [
                'create' => ['menu_store.compliance.monthly_control_counts.create'],
                'update' => ['menu_store.compliance.monthly_control_counts.update'],
                'delete' => ['menu_store.compliance.monthly_control_counts.delete'],
            ];

            $folders = Tag::whereIn('id', [8,9])->get();

            $query = StoreDocumentTag::with('tag')->select(DB::raw('COUNT(store_documents.id) AS counted_docs'), 'store_document_tags.tag_id')
            ->join('store_documents', function($q) {
                $q->on('store_document_tags.id', '=', 'store_documents.parent_id')
                    ->where('store_documents.category', '=', 'storeDocumentTag');
            })
            ->whereIn('store_document_tags.tag_id', [8,9]);
            
            if(!empty($year)) {
                $query = $query->where('store_document_tags.year', $year);
            }
    
            if(!empty($month_number)) {
                $query = $query->where('store_document_tags.month', $month_number);
            }
            $query = $query->groupBy('.store_document_tags.tag_id')
                ->pluck('counted_docs','store_document_tags.tag_id');


            $query2 = StoreDocumentTag::with('tag')->select(DB::raw('COUNT(store_documents.id) AS counted_docs'), 'store_document_tags.year', 'store_document_tags.month')
            ->join('store_documents', function($q) {
                $q->on('store_document_tags.id', '=', 'store_documents.parent_id')
                    ->where('store_documents.category', '=', 'storeDocumentTag');
            })
            ->whereIn('store_document_tags.tag_id', [8,9]);
            
            if(!empty($year)) {
                $query2 = $query2->where('store_document_tags.year', $year);
            }
    
            if(!empty($month_number)) {
                $query2 = $query2->where('store_document_tags.month', $month_number);
            }
            $monthlyAllCountFolders = $query2->groupBy('store_document_tags.month')
                ->groupBy('store_document_tags.year')
                ->pluck('counted_docs','store_document_tags.month');

            foreach($folders as $f) {
                $monthlyCountFolders[$f->id] = isset($query[$f->id]) ? $query[$f->id] : 0;
            }


            $breadCrumb = ['Compliance & Regulatory', 'Control Counts for Year '.$year];
            return view('/stores/compliance/controlCounts/index', compact('breadCrumb', 'folders', 'permissions', 'icons', 'monthlyCountFolders', 'monthlyAllCountFolders'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            $permissions = [
                'permissions' => [
                    'prefix' => 'menu_store.compliance.',
                    'delete' => ['menu_store.compliance.monthly_control_counts.delete'],
                ]
            ];
            $request->merge($permissions);
            $data = $this->storeDocumentTagRepository->getDataTable($request);
            return response()->json($data, 200);
        }
    }

    public function store($id, Request $request)
    {
        if($request->ajax()){
            DB::beginTransaction();
            try {

                $data = $this->storeDocumentTagRepository->store($request);

                DB::commit();

                return json_encode([
                    'data'=> $data,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in ControlCountController.store.db_transaction.'
                ]);
            }
        }
    }


    public function delete(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                DB::beginTransaction();

                $this->storeDocumentTagRepository->delete($request->id);

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
                    'message' => 'Something went wrong in DeckController.delete.'
                ]);
            }
        }
    }

    public function update(Request $request)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();

                $tag = Tag::findOrFail($request->id);

                if(isset($tag->id)) {
                    $tag->name = $request->name;
                    $tag->background_color = $request->background_color;
                    $tag->text_color = $request->text_color;
                    $tag->border_color = $request->border_color;
                    $tag->icon_path = $request->icon_path;
                    $save = $tag->save();
                }
                
                DB::commit();
                
                return json_encode([
                    'data'=> $tag,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in StoreFolderController.update.'
            ]);
        }
    }

}
