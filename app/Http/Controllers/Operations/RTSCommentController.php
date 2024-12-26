<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Jobs\NotifyRTSCommentToCreator;
use App\Models\Employee;
use App\Models\OperationRtsComment;
use App\Models\OperationRtsCommentDocument;
use App\Models\StoreDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class RTSCommentController extends Controller
{
    public function store($id = null, Request $request)
    {
        try {
            $response = [];

            $flag = false;

            $data = json_decode($request->data);

            $rts_id = $data->rts_id ?? null;
            $comment = $data->comment ?? null;
            $pharmacy_store_id = $id;

            $files = [];

            if($request->file('files')) {
                $files = $request->file('files');
            }

            if(!empty($rts_id) && !empty($comment)) {
                $flag = true;
            }

            if(!empty($rts_id) && empty($comment) && count($files) > 0) {
                $flag = true;
            }

            if($flag == true)
            {
                $aws_s3_path = env('AWS_S3_PATH');

                $rtsComment = new OperationRtsComment();
                $rtsComment->operation_rts_id = $rts_id;
                $rtsComment->comment = $comment;
                $rtsComment->user_id = auth()->user()->id;
                $rtsComment->save();

                $employee = Employee::where('user_id', auth()->user()->id)->first();

                $commentFiles = [];
            
                if ($request->file('files')) {
                    foreach ($files as $key => $file) {
                        $document = new StoreDocument();
                        $document->user_id = auth()->user()->id;
                        $document->parent_id = $rts_id;
                        $document->category = 'rts';
                        
                        $document->name = $file->getClientOriginalName();
                        $document->ext = $file->getClientOriginalExtension();
                        $document->mime_type = $file->getMimeType();
                        $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
                        $document->size = $file->getSize()/1024;
                        $document->size_type = 'KB';

                        $date = date('YmdHis');
                        $path = "/$aws_s3_path/stores/$pharmacy_store_id/operations/rts/$rts_id/$date";
                        $document->path = $path;

                        $save = $document->save();

                        $commentFiles[$document->id] = $document;
                        OperationRtsCommentDocument::insertOrIgnore([
                            'operation_rts_comment_id' => $rtsComment->id,
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
                    'comment' => $rtsComment,
                    'employee' => $employee,
                    'files' => $commentFiles,
                    'formatted_pst_created_at' => $rtsComment->formatted_pst_created_at
                ];
            }

            $rtsComment->with('rts.user.employee', 'rts.patient', 'rts.status');
            $this->sendCommentNotification($rtsComment);

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
                'message' => 'Something went wrong in RTSCommentController.store.db_transaction.'
            ]);
        }
    }


    public function sendCommentNotification(OperationRtsComment $operationRtsComment)
    {
        if(config('mail.maintenance') != "ON") {
            Bus::dispatch(new NotifyRTSCommentToCreator($operationRtsComment));
        }
    }

}
