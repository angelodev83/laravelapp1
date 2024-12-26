<?php

namespace App\Http\Controllers\HumanResource;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PharmacyStaff;
use App\Models\PharmacyStaffLeave;
use App\Models\StoreDocument;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function store(Request $request)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();

                $pharmacy_staff_id = null;

                $data = json_decode($request->data);

                $employee = Employee::where('user_id', auth()->user()->id)->first();
                $staff = PharmacyStaff::where('employee_id', $employee->id)->where('pharmacy_store_id', $data->pharmacy_store_id)->first();

                if(isset($staff->id)) {
                    $pharmacy_staff_id = $staff->id;
                }

                $date_from = $data->date_from ?? null;
                $date_to = $data->date_to ?? null;

                if(!empty($date_from)) {
                    $date_from = Carbon::createFromFormat('m/d/Y', $date_from);
                    $date_from = $date_from->format('Y-m-d');
                }
                if(!empty($date_to)) {
                    $date_to = Carbon::createFromFormat('m/d/Y', $date_to);
                    $date_to = $date_to->format('Y-m-d');
                }

                if(!empty($pharmacy_staff_id)) {
                    $leave = new PharmacyStaffLeave();
                    $leave->type = $data->type ?? null;
                    $leave->date_from = $date_from;
                    $leave->date_to = $date_to;
                    $leave->reason = $data->reason ?? null;
                    $leave->is_select_half_days = $data->is_select_half_days ?? 0;
                    $leave->half_days_breakdown = json_encode($data->half_days_breakdown) ?? null;
                    $leave->pharmacy_staff_id = $pharmacy_staff_id;
                    $leave->user_id = auth()->user()->id;
                    $save = $leave->save();

                    if ($request->file('files')) {
                        $files = $request->file('files');
                        $aws_s3_path = env('AWS_S3_PATH');
                        foreach ($files as $key => $file) {

                            $document = new StoreDocument();
                            $document->user_id = auth()->user()->id;
                            $document->parent_id = $leave->id;
                            $document->category = 'pharmacyStaffLeave';
                            
                            $document->name = $file->getClientOriginalName();
                            $document->ext = $file->getClientOriginalExtension();
                            $document->mime_type = $file->getMimeType();
                            $document->last_modified = Carbon::createFromTimestamp($file->getMTime());
                            $document->size = $file->getSize()/1024;
                            $document->size_type = 'KB';
        
                            $unique = date('Ymd').'-'.rand(100,999);
                            $path = "/$aws_s3_path/stores/$data->pharmacy_store_id/human-resource/schedules/leaves/$leave->id/$unique";
                            $document->path = $path;
        
                            $save = $document->save();
        
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
                            }
                        }
                    }

                }
                
                DB::commit();

                if(empty($leave)) {
                    return json_encode([
                        'status' => 'error',
                        'message' => 'You are not an ONSHORE user employee.'
                    ]);
                }
                
                return json_encode([
                    'data'=> $leave,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in PharmacyStaff ScheduleController.upload.'
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();
                
                $id = $request->id;

                $update = [
                    'status_id' => $request->status_id,
                    'updated_by' => auth()->user()->id,
                ];

                if($request->has('reason_for_rejection')) {
                    $update['reason_for_rejection'] = $request->reason_for_rejection;
                }
                $save = PharmacyStaffLeave::where('id',$id)->update($update);
                
                DB::commit();
                
                return json_encode([
                    'data'=> $save,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in PharmacyStaff ScheduleController.upload.'
            ]);
        }
    }

    public function leave($id, Request $request)
    {
        $leave = PharmacyStaffLeave::with('pharmacyStaff.employee', 'status', 'updatedBy.employee', 'documents')->findOrFail($id);
        $employee = $leave->pharmacyStaff->employee;
        $updatedBy = $leave->updatedBy ?? null;
        
        $fullname_avatar = '';
        $emp_name = $employee->firstname.' '.$employee->lastname;

        $date_range = date('F d', strtotime($leave->date_from)).' - '.date('F d, Y', strtotime($leave->date_to));
        if($leave->date_from == $leave->date_to) {
            $date_range = date('F d, Y', strtotime($leave->date_from));
        }


        $status_label = 'Filed Leave';

        $hideU = 'd-none';
        if(auth()->user()->can('menu_store.hr.leaves.update')) {
            $hideU = '';
        }

        $button_row = '
            <div class="col-6 '.$hideU.'">
                <button type="button" class="btn btn-outline-danger w-100" id="reject_btn" onclick="updateLeave('.$id.', 903)">Reject</button>
            </div>
            <div class="col-6 '.$hideU.'">
                <button type="button" class="btn btn-success w-100" id="approve_btn" onclick="updateLeave('.$id.', 902)">Approve</button>
            </div>
        ';
        if($leave->status_id == 902) {
            $status_label = 'Approved Leave';
            $button_row = '
                <div class="col-12">
                    <i class="fa fa-check-circle me-2 text-success fa-lg"></i> <b class="text-success">Approved</b> 
                    <span class="ms-3">by: '.$updatedBy->employee->firstname.' '.$updatedBy->employee->lastname.'</span>
                </div>
            ';
        }
        if($leave->status_id == 903) {
            $status_label = 'Rejected Leave';
            $button_row = '
                <div class="col-12">
                    <i class="fa fa-times-circle me-2 text-danger fa-lg"></i> <b class="text-danger">Rejected</b>
                    <span class="ms-3">by: '.$updatedBy->employee->firstname.' '.$updatedBy->employee->lastname.'</span>
                </div>
            ';
        }

        if(!empty($employee->image)) {
            $fullname_avatar = '<div class="d-flex">
                            <img src="/upload/userprofile/'.$employee->image.'" width="50" height="50" class="rounded-circle" alt="" title="'.$employee->firstname.' '.$employee->lastname.'">
                            <div class="flex-grow-1 ms-3 mt-1">
                                <b class="font-weight-bold mb-0">'.$emp_name.'</b>
                                <p>'.$employee->position.'</p>
                            </div>
                        </div>';
        } else {
            $fullname_avatar = '<div class="d-flex">
                            <div class="rounded-circle employee-avatar-'.$employee->initials_random_color.'-initials hr-employee"
                            style="width: 50px !important; height: 50px !important; font-size: 20px !important; 
                            data-id="'.$employee->id.'" title="'.$employee->firstname.' '.$employee->lastname.'">
                                '.strtoupper(substr($employee->firstname, 0, 1)).strtoupper(substr($employee->lastname, 0, 1)).'
                            </div>
                            <p class="mb-0 ms-3 mt-1">
                                <b class="mb-0 pb-0">'.$emp_name.'</b>
                                <br>'.$employee->position.'
                            </p>
                        </div>';
        }

        $formatted_created_at = date('F d, Y', strtotime($leave->created_at));

        $computed_days = '';

        $date1 = new DateTime($leave->date_from);
        $date2 = new DateTime($leave->date_to);

        $interval = $date1->diff($date2);

        $computed_days = $interval->d;
        $computed_days += 1;
        $computed_days_text = $computed_days.' day';
        if($computed_days > 1) {
            $computed_days_text = $computed_days.' days';
        }

        $documents = $leave->documents ?? [];
        $attachments = '';
        foreach($documents as $d) {
            $path = $d->path.$d->name;
            $s3Url = Storage::disk('s3')->temporaryUrl(
                $path,
                now()->addMinutes(30)
            );
            // if(strpos($d->mime_type, "image") !== false) {
            //     $attachments .= '<div class="col col-md-3"><a href="'.$s3Url.'" target="_blank">
            //         <img id="icon" class="icon-card" src="'.$s3Url.'" width="100" height="100" alt="'.$d->name.'">
            //     </a></div>';
            // } else {
            //     $attachments .= '<div class="col col-md-3"><a href="'.$s3Url.'" target="_blank">
            //         <span class="rectangle" style="height: 100px !important; width: 100px !important; background-color: none !important; border-color: #15ca2057; background-image: url("https://home.mgmt88.com/source-images/knowledge-base/Default.png");
            // background-size: cover;
            // background-position: center; 
            // background-repeat: no-repeat;">
            //             <span class="ms-3" style="word-wrap: break-word; word-break: break-all;">'.$d->name.'</span>
            //         </span>
            //     </a></div>';
            // }

            $src = 'https://home.mgmt88.com/source-images/knowledge-base/files/How To Guide.png';
            $showTxt = '';
            if(strpos($d->mime_type, "image") !== false) {
                $src = $s3Url;
                $showTxt = 'd-none';
            }

            $attachments .= '
                <div class="attachments-container my-2">
                    <a href="'.$s3Url.'" target="_blank">
                        <img src="'.$src.'" alt="Image" class="attachments-image">
                        <div class="attachments-text-overlay '.$showTxt.'">'.$d->name.'</div>
                    </a>
                </div>
            ';
            
        }

        $data = [
            'data' => [
                'detail' => $leave,
                'formatted'  => [
                    'fullname_avatar' => $fullname_avatar,
                    'status_label' => $status_label,
                    'formatted_created_at' => $formatted_created_at,
                    'button_row' => $button_row,
                    'date_range' => $date_range,
                    'computed_days' => $computed_days_text,
                    'attachments' => $attachments,
                ]
            ],
            'status' => 'success',
            'message'=> 'Record has been retrieved.'
        ];

        if($request->ajax()) {
            return response()->json($data);
        }

        return $data;
    }
}
