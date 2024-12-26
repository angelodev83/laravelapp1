<?php

namespace App\Http\Controllers\Clinical;

use App\Exports\ClinicalRenewalCustomExport;
use App\Http\Controllers\Controller;
use App\Imports\RenewalImport;
use App\Models\ClinicalRenewal;
use App\Models\ClinicalRenewalComment;
use App\Models\ClinicalRenewalCommentDocument;
use App\Models\Patient;
use App\Models\StoreDocument;
use App\Models\StoreStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class RenewalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:menu_store.clinical.renewals.index|menu_store.clinical.renewals.create|menu_store.clinical.renewals.update|menu_store.clinical.renewals.delete|menu_store.clinical.renewals.archive|menu_store.clinical.renewals.export');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $renewalStatus = StoreStatus::where('category', 'renewal')->orderBy('sort', 'asc')->get()->toArray();
            $breadCrumb = ['Clinical', 'Renewal'];

            return view('/stores/clinical/renewals/index', compact('breadCrumb', 'renewalStatus'));
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

            $query = ClinicalRenewal::query()->with('comments.documents'
                , 'comments.user.employee'
                , 'user.employee'
                , 'patient'
            )->where('is_archived', $is_archived);

            if(!empty($id)) {
                $query = $query->where('pharmacy_store_id', $id);
            }

            if(!empty($year)) {
                $query = $query->whereRaw('YEAR(renew_date)');
            }

            if(!empty($month_number)) {
                $query = $query->whereRaw('MONTH(renew_date)');
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

            $groups = StoreStatus::where('category', 'renewal')->get();

            $data = [];
            foreach($groups as $g) {
                $data[$g->id] = [
                    'item'          => $g,
                    'collection'    => []
                ];
            }
            foreach($query as $q) {
                $date1 = Carbon::createFromFormat('Y-m-d', $q->renew_date);
                $date2 = Carbon::createFromFormat('Y-m-d', $date_today);

                $days = $date1->diffInDays($date2);

                $patient = $q->patient ?? null;

                $patient_name = $patient ? $patient->getDecryptedFirstname().' '.$patient->getDecryptedLastname() : '';

                $data[$q->status_id]['collection'][] = [
                    'raw' => $q,
                    'formatted' => [
                        'renew_date' => date('m/d/Y', strtotime($q->renew_date)),
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
                'message' => 'Something went wrong in RenewalController.boardData.db_transaction.'
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
            $file_name = "store_clinical_renewal.".$ext;
            $save_name = str_replace('\\', '/' , storage_path())."/$file_name";
        
            file_put_contents($save_name, $current);

            $absolute_path = str_replace('\\', '/' , storage_path());

            $filePath = $absolute_path.'/'.$file_name;
            Excel::import(new RenewalImport($params), $filePath);

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
                'message' => 'Something went wrong in RenewalController.import.db_transaction.'
            ]);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $renewal_id = $request->renewal_id ?? null;
            $field = $request->field ?? null;
            $value = $request->value ?? null;

            $renewal = ClinicalRenewal::findOrFail($renewal_id);
            if(isset($renewal->id)) {
                if($field == 'renew_date') {
                    $date = Carbon::createFromFormat('m/d/Y', $value);
                    $value = $date->format('Y-m-d');

                    if($renewal->status_id == 921 || $renewal->status_id == 922) {
                        $date_today = $this->getCurrentPSTDate('Y-m-d');
                        $date1 = Carbon::createFromFormat('Y-m-d', $value);
                        $date2 = Carbon::createFromFormat('Y-m-d', $date_today);
                        $days = $date1->diffInDays($date2);

                        $status_id = $days > 7 ? 922 : 921;
                        $renewal->status_id = $status_id;
                    }
                }
                $renewal->$field = $value;
                $renewal->save();
            }

            $response = [
                'data' => $renewal,
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
                'message' => 'Something went wrong in RenewalController.update.db_transaction.'
            ]);
        }
    }

    public function delete($id, Request $request)
    {
        try {
            $renewal_id = $request->renewal_id ?? null;

            $renewal = ClinicalRenewal::findOrFail($renewal_id);
            $oldRenewal = clone $renewal;

            if(isset($renewal->id)) {
                $comments = ClinicalRenewalComment::where('clinical_renewal_id', $renewal->id)->pluck('id')->toArray();
                if(count($comments) > 0) {
                    // delete comment documents rel
                    ClinicalRenewalCommentDocument::whereIn('clinical_renewal_comment_id', $comments)->delete();
                    // delete comments
                    ClinicalRenewalComment::where('clinical_renewal_id', $renewal->id)->delete();
                    // delete files
                    StoreDocument::where('category', 'renewal')->where('parent_id', $renewal->id)->delete();
                    // delete s3 folder of item and subfolder and files inside
                    $aws_s3_path = env('AWS_S3_PATH');
                    $folderPath = "$aws_s3_path/stores/$renewal->pharmacy_store_id/clinical/renewals/$renewal->id/";
                    $save = Storage::disk('s3')->deleteDirectory($folderPath);

                }
                $renewal->delete();
            }


            $response = [
                'data' => $oldRenewal,
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
                'message' => 'Something went wrong in RenewalController.delete.db_transaction.'
            ]);
        }
    }

    function patientData($id, $renewal_id, Request $request)
    {
        try {
            
            $renewal = ClinicalRenewal::findOrFail($renewal_id);
            $date_today = $this->getCurrentPSTDate('Y-m-d');

            $data = [];

            if(isset($renewal->id)) {
                $query = ClinicalRenewal::query()->with(
                        'comments.documents', 'comments.user.employee', 'status', 'patient'
                    )->where('patient_id', $renewal->patient_id)->where('is_archived', 0);

                if(!empty($id)) {
                    $query = $query->where('pharmacy_store_id', $id);
                }
                $query = $query->get();

                foreach($query as $q) {
                    $date1 = Carbon::createFromFormat('Y-m-d', $q->renew_date);
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
                            'renew_date' => date('m/d/Y', strtotime($q->renew_date)),
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
                'message' => 'Something went wrong in RenewalController.patientData.db_transaction.'
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

        return Excel::download(new ClinicalRenewalCustomExport($filterArray), 'Clinical Renewal - Intranet '.$date_today.'.xlsx');
    }

}
