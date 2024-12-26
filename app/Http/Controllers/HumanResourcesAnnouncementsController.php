<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Notifications\AnnouncementNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Carbon\Carbon;

class HumanResourcesAnnouncementsController extends Controller
{
    /**
     * 
     * @var Announcement
     */
    private $announcement;
    private $authUser;
    private $breadCrumb;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
        $this->authUser = Auth::user();
        $this->breadCrumb = 'Marketing';

        $this->middleware('permission:hr.announcements.index|hr.announcements.create|hr.announcements.update|hr.announcements.delete');
    }

    public function index()
    {
        $breadCrumb = [$this->breadCrumb, 'Announcements'];
        return view('/humanResources/announcements/index', compact('breadCrumb'));
    }

    public function show($id)
    {
        // update notification - tag as read
        $n = DB::table('notifications')
            ->where('data->announcement', $id)
            ->where('notifiable_id', Auth::user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);

        $announcement = $this->announcement->findOrFail($id);
        $breadCrumb = [$this->breadCrumb, 'Announcements'];
        return view('/humanResources/announcements/view', compact('breadCrumb', 'announcement'));
    }

    public function get_data(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // get data from products table
            $query = new Announcement();

            // Search //input all searchable fields
            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true" && $search){
                        $query->orWhere("$column[subject]", 'like', "%".$search."%");
                    }  
                }
            });

            //default field for order
            $orderByCol = $request->columns[$request->order[0]['column']]['created_at'] ?? 'created_at';
        
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $hideU = 'hidden';
            $hideD = 'hidden';
            $hideAll = 'hidden';
            if(auth()->user()->can('hr.announcements.update'))
            {
                $hideU = ''; $hideAll = '';
            }
            if(auth()->user()->can('hr.announcements.delete'))
            {
                $hideD = ''; $hideAll = '';
            }
            $origHideU = $hideU;
            $origHideD = $hideD;
            $origHideAll = $hideAll;

            $newData = [];
            foreach ($data as $value) {
                $emp = $value->user->employee;
                $created_by = isset($value->user->employee) ? $value->user->employee->getFullName() : "NA";
                $empAvatar = '';
                if(!empty($emp->image)) {
                    $empAvatar = '
                        <div class="d-flex">
                            <img src="/upload/userprofile/'.$emp->image.'" width="32" height="32" class="rounded-circle" alt="">
                            <div class="flex-grow-1 ms-3 mt-2">
                                <p class="font-weight-bold mb-0">'.$created_by.'</p>
                            </div>
                        </div>
                    ';
                } else {
                    $empColor = empty($emp->initials_random_color) ? 1 : $emp->initials_random_color;
                    $empAvatar = '
                        <div class="d-flex">
                            <div class="employee-avatar-'.$empColor.'-initials hr-employee" data-id="'.$emp->id.'">
                            '.strtoupper(substr($emp->firstname, 0, 1)).strtoupper(substr($emp->lastname, 0, 1)).'
                            </div>
                            <p class="font-weight-bold mb-0 ms-3 mt-2">'.$created_by.'</p>
                        </div>
                    ';
                }
                
                $created_at = ($value->created_at === null)?'':date('M d, Y h:iA', strtotime($value->created_at));

                if($value->user->id == auth()->user()->id)
                {
                    $hideU = '';
                    $hideD = '';
                    $hideAll = '';
                } else {
                    $hideU = $origHideU;
                    $hideD = $origHideD;
                    $hideAll = $origHideAll;
                }

                $newData[] = [
                    'id'      => $value->id,
                    'subject' => $value->subject,
                    'content' => json_encode([$value->content]),
                    'created_by' => $created_by,
                    'created_at' => $created_at,
                    'empAvatar' => $empAvatar,
                    'updated_at' => ($value->updated_at === null)?'':date('Y-m-d', strtotime($value->updated_at)),
                    'actions' =>  '<div class="d-flex order-actions" '.$hideAll.'>
                        <a href="/admin/human_resources/announcements/' . $value->id . '"><button class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></button></a>
                        <a data-bs-toggle="modal" data-bs-target="#updateAnnouncement_modal" 
                            data-subject="'.$value->subject.'" data-content="'.htmlspecialchars($value->content).'" data-id="'.$value->id.'"
                            class="ms-2" '.$hideU.'><button class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></button></a>
                        <a '.$hideD.'><button class="btn btn-sm btn-danger ms-2" onclick="ShowConfirmDeleteForm(' . $value->id . ')"><i class="fa fa-trash-can"></i></button></a>
                    </div>'
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }

    public function add_announcement(Request $request)
    {
        if($request->ajax()){
            
            $helper =  new Helper;
            $input = $request->all();

            $validation = Validator::make($input, [
                'subject' => 'required|max:150|min:1',
                'content' => 'required'
            ]);

            if ($validation->passes()){
                $announcement = new Announcement;
                $announcement->subject = $helper->ProperNamingCase($input['subject']);
                $announcement->content = $input['content'];
                $announcement->user_id = auth()->user()->id;
                $announcement->save();

                $users = User::all();
                Notification::send($users, new AnnouncementNotification($announcement));

                return json_encode([
                    'data'=> $announcement,
                    'status'=>'success',
                    'message'=>'Announcement Saved.']);
           }
           else{
                return json_encode(
                    ['status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Announcement saving failed.']);
            }

            
        }
    }

    public function update_announcement(Request $request)
    {
        if($request->ajax()){
            

            $helper =  new Helper;
            $input = $request->all();
                
            $validation = Validator::make($input, [
                'subject' => 'required|max:150|min:1',
                'content' => 'required'
            ]);

            if ($validation->passes()){

                $announcement = Announcement::find($input['id']);
                $announcement->subject = $helper->ProperNamingCase($input['subject']);
                $announcement->content = $input['content'];
                $announcement->save();

                return json_encode([
                    'data'=> $announcement,
                    'status'=>'success',
                    'message'=>'Announcement Updated.']);
           }
           else{
                return json_encode(
                    ['status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Announcement saving failed.']);
            }
        }
    }


    public function delete(Request $request)
    {
        if($request->ajax()){
            $id = $request->id ?? null;
            $announcement = $this->announcement->find($id);
            $announcement->delete();

            DB::table('notifications')
                ->where('type','App\Notifications\AnnouncementNotification')
                ->where('data->announcement', $id)
                ->delete();

            return json_encode([
                'data'=>$announcement,
                'status'=>'success',
                'message'=>'Announcement Deleted.']);
        }
        
        
    }

}
