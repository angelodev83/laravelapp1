<?php

namespace App\Repositories\Common;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Interfaces\Common\AnnouncementInterface;
use App\Repositories\BaseRepository;
use App\Http\Utils\FileIconUtil;

use App\Models\ComplianceDocument;
use App\Models\Announcement;
use App\Models\Employee;
use App\Models\StoreAnnouncement;
use App\Models\User;
use App\Models\PharmacyStaff;

use App\Notifications\AnnouncementNotification;
use App\Notifications\BulkAnnouncementNotification;
use App\Notifications\Store\AnnouncementNotification as StoreAnnouncementNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

use File;

class AnnouncementRepository extends BaseRepository implements AnnouncementInterface
{
    use FileIconUtil;

    private $document;
    private $announcement;

    private $humanResourceAnnouncement;
    private $storeBulletinAnnouncement;

    protected $dataTable = [];

    public function __construct(ComplianceDocument $document
        , Announcement $humanResourceAnnouncement
        , StoreAnnouncement $storeBulletinAnnouncement
    )
    {
        $this->document = $document;
        // DEFAULT MODEL
        $this->announcement = $humanResourceAnnouncement;

        $this->humanResourceAnnouncement = $humanResourceAnnouncement;
        $this->storeBulletinAnnouncement = $storeBulletinAnnouncement;
    }

    public function getDataTable() : array
    {
        return $this->dataTable;
    }

    public function setDataTable($request)
    {
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';
        //default field for order
        $orderByCol = $request->columns[$request->order[0]['column']]['name'] ?? 'created_at';

        // get data from products table
        $query = $this->announcement->with('user.employee');
        

        // Define the columns to select
        $columnsToSelect = ['id', 'subject', 'pharmacy_store_id', 'created_at', 'updated_at', 'user_id'];

        // Modify the query to select only the specified columns
        $query = $query->select($columnsToSelect);

        // Search //input all searchable fields
        $search = trim($request->search);
        $columns = $request->columns;
        $query = $query->where(function($query) use ($search, $columns){
            foreach ($columns as $column) {
                if($column['searchable'] === "true" && $search){
                    $query->orWhere($column['name'], 'like', "%".$search."%");
                }  
            }
        });

        $url = '/admin/human_resources/announcements/';
        if($request->has('pharmacy_store_id')) {
            $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
            $url = '/store/bulletin/announcements/view/';
        }        

        if($orderByCol == 'created_by') {
            // $query = $query->orderBy('user.employee.firstname', $orderBy);
        } else {
            $query = $query->orderBy($orderByCol, $orderBy);
        }
        
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $hideU = 'hidden';
        $hideD = 'hidden';
        $hideAll = 'hidden';
        if(auth()->user()->can('menu_store.marketing.announcements.update'))
        {
            $hideU = ''; $hideAll = '';
        }
        if(auth()->user()->can('menu_store.marketing.announcements.delete'))
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
            
            $created_at = ($value->pst_created_at === null)?'':date('M d, Y g:i A', strtotime($value->pst_created_at));

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
                'updated_at' => ($value->updated_at === null)?'':date('M d, Y h:iA', strtotime($value->updated_at)),
                'actions' =>  '<div class="d-flex order-actions" '.$hideAll.'>
                    <button class="btn btn-sm btn-primary"
                        data-bs-toggle="modal" data-bs-target="#updateAnnouncement_modal" '.$hideU.'
                        id="marketing-announcement-edit-btn-' . $value->id . '"
                        data-subject="'.htmlspecialchars($value->subject).'" 
                        
                        data-id="'.$value->id.'" data-created-at="'.$created_at.'" 
                        ><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-sm btn-danger ms-2" onclick="ShowConfirmDeleteForm(' . $value->id . ')" '.$hideD.'><i class="fa fa-trash-can"></i></button>
                </div>'
            ];
        } 

        $this->dataTable = [
            "draw"=> $request->draw, 
            "recordsTotal"=> $recordsTotal, 
            "recordsFiltered" => $recordsFiltered, 
            'data' => $newData
        ];
    }

    public function setModel($model)
    {
        $this->announcement = $this->$model;
    }

    public function search($request)
    {
        $query = $this->ticket;
        return $query->get();
    }

    /**
     * action store
     *
     * @param [type] $request
     * @return void
     */
    public function store($request)
    {
        $this->announcement = new $this->announcement;
        $this->announcement->subject = $request->subject;
        $this->announcement->content = $request->content;
        $this->announcement->user_id = auth()->user()->id;
        if($request->has('pharmacy_store_id')) {
            $this->announcement->pharmacy_store_id = $request->pharmacy_store_id;
        }
        if(!$this->announcement->save()) {
            throw new \Exception("Inserting of announcement failed", 500);
        }
        $this->sendStoreNotification($this->announcement);
        return $this->announcement;
    }

    public function currentAnnouncement()
    {
        return $this->announcement;
    }

    /**
     * action update
     *
     * @param [type] $request
     * @return void
     */
    public function update($request)
    {
        $this->announcement = $this->announcement->findOrFail($request->id);
        $this->announcement->subject = $request->subject;
        $this->announcement->content = $request->content;
        if(!$this->announcement->save()) {
            throw new \Exception("Updating of announcement failed", 500);
        }
        return $this->announcement;
    }

    public function delete($id)
    {
        $announcement = $this->announcement->findOrFail($id);
        DB::table('notifications')
            ->where('type','App\Notifications\Store\AnnouncementNotification')
            ->where('data->announcement', $id)
            ->delete();
        if(!$announcement->delete()) {
            throw new \Exception("Deleting of announcement failed", 500);
        }
    }

    public function sendNotification()
    {
        $users = User::all();
        Notification::send($users, new AnnouncementNotification($this->announcement));
    }

    public function sendStoreNotification($announcement)
    {
        $data = User::join('employees', 'employees.user_id', '=', 'users.id')
                ->leftJoin('pharmacy_staff', 'employees.id', '=', 'pharmacy_staff.employee_id')
                ->select("users.*")
                ->where("employees.id", ">", 19)
                ->whereNot("employees.status", "Terminated");
        $data = $data->where(function ($data) use ($announcement){
            $data->orWhere('pharmacy_staff.pharmacy_store_id', $announcement->pharmacy_store_id);
            $data->orWhereNull('pharmacy_staff.pharmacy_store_id');
        });

        $data = $data->orderBy('employees.lastname','asc')->orderBy('employees.firstname','asc')->get();

        $emails = [];
        foreach($data as $user) {
            $emails[] = $user->email;
            $user->notify(new StoreAnnouncementNotification($announcement));
        }

        if(config('mail.maintenance') != "ON") {
            $user = User::with('employee')->findOrFail(auth()->user()->id);
            $user->notify(new BulkAnnouncementNotification($announcement, $emails));
        }


    }

    public function retrieveStoreAnnouncement()
    {
        $notifications = auth()->user()->notifications()
            ->where('type', 'App\Notifications\Store\AnnouncementNotification')
            ->where('read_at', null)
            ->orderBy('created_at', 'desc')
            ->get();
        $announcements = [];
        foreach($notifications as $notification) {
            $data = $notification->data;
            $announcement = StoreAnnouncement::with('user.employee')->where('id',$data['announcement'])->first();
            if(!empty($announcement)) {
                $announcement['formatted_pst_created_at'] = $announcement->formatted_pst_created_at;
                $announcements[] = $announcement;
            }
        }
        return $announcements;
    }

    public function retrieveRecent($params)
    {
        $today = Carbon::today();
        $lastMonth = $today->copy()->subDays(30)->format('Y-m-d');       

        $data = $this->announcement->with('user.employee')->select('*', DB::raw('TIMESTAMPDIFF(HOUR, created_at, NOW()) AS hours_difference'));
        if(isset($params['pharmacy_store_id'])) {
            $data = $data->where('pharmacy_store_id', $params['pharmacy_store_id']);
        }
        $data = $data->where(DB::raw('DATE(created_at)'), '>=', $lastMonth);
        return $data->orderBy('created_at', 'desc')->get();
    }

}