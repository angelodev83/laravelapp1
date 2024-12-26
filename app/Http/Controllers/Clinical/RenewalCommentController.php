<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Jobs\NotifyRenewalCommentToCreator;
use App\Models\ClinicalRenewalComment;
use App\Models\ClinicalRenewalCommentDocument;
use App\Models\Employee;
use App\Models\StoreDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class RenewalCommentController extends Controller
{
    public function store($id = null, Request $request)
    {
        try {
            $response = [];

            $flag = false;

            $data = json_decode($request->data);

            $renewal_id = $data->renewal_id ?? null;
            $comment = $data->comment ?? null;
            $pharmacy_store_id = $id;

            $files = [];

            if($request->file('files')) {
                $files = $request->file('files');
            }

            if(!empty($renewal_id) && !empty($comment)) {
                $flag = true;
            }

            if(!empty($renewal_id) && empty($comment) && count($files) > 0) {
                $flag = true;
            }

            if($flag == true)
            {
                $aws_s3_path = env('AWS_S3_PATH');

                $renewalComment = new ClinicalRenewalComment();
                $renewalComment->clinical_renewal_id = $renewal_id;
                $renewalComment->comment = $comment;
                $renewalComment->user_id = auth()->user()->id;
                $renewalComment->save();

                $employee = Employee::where('user_id', auth()->user()->id)->first();

                $commentFiles = [];
            
                if ($request->file('files')) {
                    foreach ($files as $key => $file) {
                        $document = new StoreDocument();
                        $document->user_id = auth()->user()->id;
                        $document->parent_id = $renewal_id;
                        $document->category = 'renewal';
                        
                        $document->name = $file->getClientOriginalName();
                        $document->ext = $file->getClientOriginalExtension();
                        $document->mime_type = $file->getMimeType();
                        $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
                        $document->size = $file->getSize()/1024;
                        $document->size_type = 'KB';

                        $date = date('YmdHis');
                        $path = "/$aws_s3_path/stores/$pharmacy_store_id/clinical/renewals/$renewal_id/$date";
                        $document->path = $path;

                        $save = $document->save();

                        $commentFiles[$document->id] = $document;
                        ClinicalRenewalCommentDocument::insertOrIgnore([
                            'clinical_renewal_comment_id' => $renewalComment->id,
                            'document_id' => $document->id
                        ]);

                        if(!$save) {
                            $flag = false;
                        }

                        if($save) {
                            $pathfile = $document->path.$document->name;
                            Storage::disk('s3')->put($pathfile, file_get_contents($file));
                            $s3Url = Storage::disk('s3')->temporaryUrl(
                                $pathfile,
                                now()->addMinutes(30)
                            );
                            $commentFiles[$document->id]['s3_url'] = $s3Url;
                        }
                    }
                }

                $response =  [
                    'comment' => $renewalComment,
                    'employee' => $employee,
                    'files' => $commentFiles,
                    'formatted_pst_created_at' => $renewalComment->formatted_pst_created_at
                ];
            }

            $renewalComment->with('renewal.user.employee', 'renewal.patient', 'renewal.status');
            $this->sendCommentNotification($renewalComment);

            $response = [
                'data' => $response,
                'status'=>'success',
                'message'=>'Comment has been created.'
            ];

            if($request->ajax()){
                return json_encode($response);
            }
            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in RenewalCommentController.store.db_transaction.'
            ]);
        }
    }


    public function sendCommentNotification(ClinicalRenewalComment $clinicalRenewalComment)
    {
        if(config('mail.maintenance') != "ON") {
            Bus::dispatch(new NotifyRenewalCommentToCreator($clinicalRenewalComment));
        }
    }

}
