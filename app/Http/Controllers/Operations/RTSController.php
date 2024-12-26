<?php

namespace App\Http\Controllers\Operations;

use App\Exports\OperationRtsCustomExport;
use App\Http\Controllers\Controller;
use App\Imports\RTSImport;
use App\Models\OperationRts;
use App\Models\OperationRtsComment;
use App\Models\OperationRtsCommentDocument;
use App\Models\Patient;
use App\Models\StoreDocument;
use App\Models\StoreStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class RTSController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:menu_store.operations.rts.index|menu_store.operations.rts.create|menu_store.operations.rts.update|menu_store.operations.rts.delete|menu_store.operations.rts.export');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $rtsStatus = StoreStatus::where('category', 'rts')->orderBy('sort', 'asc')->get()->toArray();
            $breadCrumb = ['Operations', 'Return To Stock'];

            return view('/stores/operations/rtsV2/index', compact('breadCrumb', 'rtsStatus'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }


    public function filterBoardData($id, Request $request)
    {
        try {
            $year = $request->year ?? $this->getCurrentPSTDate('Y');
            $month_number = $request->month_number ?? $this->getCurrentPSTDate('n');
            $date_today = $this->getCurrentPSTDate('Y-m-d');
            $is_archived = $request->is_archived ?? 0;
            $search = $request->search ?? null;

            $query = OperationRts::query()->with('comments.documents'
                , 'comments.user.employee'
                , 'user.employee'
                , 'patient'
            )->where('is_archived', $is_archived);

            if(!empty($id)) {
                $query = $query->where('pharmacy_store_id', $id);
            }

            if(!empty($year)) {
                $query = $query->whereRaw('YEAR(fill_date)');
            }

            if(!empty($month_number)) {
                $query = $query->whereRaw('MONTH(fill_date)');
            }

            $search = trim($search);
            if(!empty($search)) {
                $encryptedPatientIds = [];
                if(!empty($search)) {
                    $encryptedPatientIds = Patient::query()->get()->filter(function ($eq) use ($search) {
                        return stristr($eq->getDecryptedFirstname(), trim($search)) !== false
                            || stristr($eq->getDecryptedLastname(), trim($search)) !== false
                            || stristr($eq->getDecryptedFirstname().' '.$eq->getDecryptedLastname(), trim($search)) !== false;
                    })->pluck('id');
                }

                $query = $query->where(function($q) use($search, $encryptedPatientIds) {
                    $q->orWhere('rx_number', 'like', '%'.$search.'%');
                    $q->orWhereIn('patient_id', $encryptedPatientIds);
                });
            }

            $query = $query->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->get();

            $groups = StoreStatus::where('category', 'rts')->get();

            $data = [];
            foreach($groups as $g) {
                $data[$g->id] = [
                    'item'          => $g,
                    'collection'    => []
                ];
            }
            foreach($query as $q) {
                $date1 = Carbon::createFromFormat('Y-m-d', $q->fill_date);
                $date2 = Carbon::createFromFormat('Y-m-d', $date_today);

                $days = $date1->diffInDays($date2);

                $patient = $q->patient ?? null;

                $patient_name = $patient ? $patient->getDecryptedFirstname().' '.$patient->getDecryptedLastname() : '';

                $data[$q->status_id]['collection'][] = [
                    'raw' => $q,
                    'formatted' => [
                        'fill_date' => date('m/d/Y', strtotime($q->fill_date)),
                        'days_in_queue' => $days,
                        'patient_fullname' => $patient_name,
                        'days_in_queue_bg_color' => $days >= 14 ? '#ff3131' : '#e9eef5',
                        'days_in_queue_text_color' => $days >= 14 ? 'white' : 'black',
                    ]
                ];
            }

            if($request->ajax()){
                return json_encode([
                    'data'=> $data,
                    'filter' => [
                        'year' => $year,
                        'month_number' => $month_number
                    ],
                    'status'=>'success',
                    'message'=>'Record has been retrieved.'
                ]);
            }
            return $data;
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in RTSController.boardData.db_transaction.'
            ]);
        }
    }

    public function import($id, Request $request)
    {
        try {
            $params = [
                'pharmacy_store_id' => $id
            ];

            $file = $request->file('upload_file');
            $ext = $file->getClientOriginalExtension();
            $current = file_get_contents($file);
            $file_name = "store_operation_rts.".$ext;
            $save_name = str_replace('\\', '/' , storage_path())."/$file_name";
        
            file_put_contents($save_name, $current);

            $absolute_path = str_replace('\\', '/' , storage_path());

            $filePath = $absolute_path.'/'.$file_name;
            Excel::import(new RTSImport($params), $filePath);

            $response = [
                'data' => $params,
                'status'=>'success',
                'message'=>'Record has been saved.'
            ];

            if($request->ajax()){
                return json_encode($response);
            }
            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in RTSController.import.db_transaction.'
            ]);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $rts_id = $request->rts_id ?? null;
            $field = $request->field ?? null;
            $value = $request->value ?? null;

            $rts = OperationRts::findOrFail($rts_id);
            if(isset($rts->id)) {
                if($field == 'fill_date') {
                    $date = Carbon::createFromFormat('m/d/Y', $value);
                    $value = $date->format('Y-m-d');

                    if($rts->status_id == 921 || $rts->status_id == 922) {
                        $date_today = $this->getCurrentPSTDate('Y-m-d');
                        $date1 = Carbon::createFromFormat('Y-m-d', $value);
                        $date2 = Carbon::createFromFormat('Y-m-d', $date_today);
                        $days = $date1->diffInDays($date2);

                        $status_id = $days > 7 ? 922 : 921;
                        $rts->status_id = $status_id;
                    }
                }
                $rts->$field = $value;
                $rts->save();
            }

            $response = [
                'data' => $rts,
                'status'=>'success',
                'message'=>'Record has been updated.'
            ];

            if($request->ajax()){
                return json_encode($response);
            }
            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in RTSController.update.db_transaction.'
            ]);
        }
    }

    public function delete($id, Request $request)
    {
        try {
            $rts_id = $request->rts_id ?? null;

            $rts = OperationRts::findOrFail($rts_id);
            $oldRts = clone $rts;

            if(isset($rts->id)) {
                $comments = OperationRtsComment::where('operation_rts_id', $rts->id)->pluck('id')->toArray();
                if(count($comments) > 0) {
                    // delete comment documents rel
                    OperationRtsCommentDocument::whereIn('operation_rts_comment_id', $comments)->delete();
                    // delete comments
                    OperationRtsComment::where('operation_rts_id', $rts->id)->delete();
                    // delete files
                    StoreDocument::where('category', 'rts')->where('parent_id', $rts->id)->delete();
                    // delete s3 folder of item and subfolder and files inside
                    $aws_s3_path = env('AWS_S3_PATH');
                    $folderPath = "$aws_s3_path/stores/$rts->pharmacy_store_id/operations/rts/$rts->id/";
                    $save = Storage::disk('s3')->deleteDirectory($folderPath);
                }
                $rts->delete();
            }


            $response = [
                'data' => $oldRts,
                'status'=>'success',
                'message'=>'Record has been deleted.'
            ];

            if($request->ajax()){
                return json_encode($response);
            }
            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in RTSController.delete.db_transaction.'
            ]);
        }
    }

    function patientData($id, $rts_id, Request $request)
    {
        try {
            
            $rts = OperationRts::findOrFail($rts_id);
            $date_today = $this->getCurrentPSTDate('Y-m-d');

            $data = [];

            if(isset($rts->id)) {
                $query = OperationRts::query()->with(
                        'comments.documents', 'comments.user.employee', 'status', 'patient'
                    )->where('patient_id', $rts->patient_id)->where('is_archived', 0);

                if(!empty($id)) {
                    $query = $query->where('pharmacy_store_id', $id);
                }
                $query = $query->get();

                foreach($query as $q) {
                    $date1 = Carbon::createFromFormat('Y-m-d', $q->fill_date);
                    $date2 = Carbon::createFromFormat('Y-m-d', $date_today);

                    $days = $date1->diffInDays($date2);

                    $patient = $q->patient ?? null;

                    $patient_name = $patient ? $patient->getDecryptedFirstname().' '.$patient->getDecryptedLastname() : '';

                    $comments = $q->comments ?? null;

                    $formattedComments = [];
                    foreach($comments as $comment) {
                        $documents = $comment->documents ?? [];

                        $formattedComments[$comment->id] = [
                            'formatted_pst_created_at' => $comment->formatted_pst_created_at,
                            'documents' => []
                        ];
                        foreach($documents as $document) {
                            $pathfile = $document->path.$document->name;
                            $s3Url = Storage::disk('s3')->temporaryUrl(
                                $pathfile,
                                now()->addMinutes(30)
                            );
                            $formattedComments[$comment->id]['documents'][$document->id] = [
                                's3_url' => $s3Url
                            ];
                        }
                    }
                    
                    $data[] = [
                        'raw' => $q,
                        'formatted' => [
                            'fill_date' => date('m/d/Y', strtotime($q->fill_date)),
                            'days_in_queue' => $days,
                            'patient_fullname' => $patient_name,
                            'days_in_queue_bg_color' => $days >= 14 ? '#ff3131' : '#ffffff',
                            'days_in_queue_text_color' => $days >= 14 ? 'white' : 'black',
                            'comments' => $formattedComments
                        ]
                    ];
                }
            }

            $response = [
                'data' => $data,
                'status'=>'success',
                'message'=>'Record has been retrieved.'
            ];

            if($request->ajax()){
                return json_encode($response);
            }
            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in RTSController.patientData.db_transaction.'
            ]);
        }
    }

    public function export(Request $request)
    {
        $is_archived = $request->query('is_archived', 0);
        $search = $request->query('search', '');
        $pharmacy_store_id = $request->id ? $request->id : null;

        $filterArray = [
            'is_archived' => $is_archived
        ];

        if(!empty($search)) {
            $filterArray['search'] = $search;
        }

        if(!empty($pharmacy_store_id)) {
            $filterArray['pharmacy_store_id'] = $pharmacy_store_id;
        }

        $date_today = $this->getCurrentPSTDate('Y-m-d H:i');

        return Excel::download(new OperationRtsCustomExport($filterArray), 'Operation RTS - Intranet '.$date_today.'.xlsx');
    }


}
