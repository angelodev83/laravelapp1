<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\File;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Stage;
use App\Models\Status;
use App\Models\Medication;
use App\Models\Order;
use App\Models\OperationReturn;
use App\Models\PharmacyStore;
use App\Models\StoreAnnouncement;
use Auth;
use Aws\S3\S3Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use View;


class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // $this->middleware('permission:executive_dashboard.index');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $statuses = Status::withCount(['prescriptions' => function ($query) use ($request) {
            $query->has('patient');
            if ($request->has(['start_date', 'end_date'])) {
                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');
                $query->whereBetween('submitted_at', [$startDate, $endDate]);
            }
        }])->orderBy('id', 'asc')->get();

        $stages = Stage::withCount(['prescriptions' => function ($query) use ($request) {
            $query->has('patient');
            if ($request->has(['start_date', 'end_date'])) {
                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');
                $query->whereBetween('submitted_at', [$startDate, $endDate]);
            }
        }])->orderBy('id', 'asc')->get();

        $totalPrescriptions = Prescription::count();
        $prescriptions = Prescription::has('patient')->orderBy('id', 'desc')->paginate(10);

        $patientsCount = Patient::count();
        $mailOrdersCount = Order::count();
        $returnToStockCount = OperationReturn::count();

        return view('/admin/dashboard/admin', compact('user', 'statuses', 'stages','prescriptions','totalPrescriptions', 'patientsCount', 'mailOrdersCount', 'returnToStockCount'));
       
        switch ($user->role->name) {
            case 'Admin':
                return view('/admin/index', compact('user', 'statuses', 'stages','prescriptions','totalPrescriptions'));
            case 'Compliance':
                return view('/compliance/index', compact('user', 'statuses', 'stages','prescriptions','totalPrescriptions'));
            default:
                return view('/admin/index', compact('user', 'statuses', 'stages'));
        }
    }
        
       
 

    public function prescriptions(Request $request, $status_id = null, $stage_id = null)
    {
        $user = Auth::user();
        $statuses = Status::orderBy('id', 'asc')->get();
        $stages = Stage::orderBy('id', 'asc')->get();

        $prescriptionsQuery = Prescription::query();

        // Check if the URL is accessing a specific status
        if ($status_id !== null) {
          
            $prescriptionsQuery->where('status_id', $status_id);
        }

        if ($stage_id !== null) {
            $prescriptionsQuery->where('stage_id', $stage_id);
        }

        // Check for date range inputs
        if ($request->has(['start_date', 'end_date'])) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $prescriptionsQuery->whereBetween('submitted_at', [$startDate, $endDate]);
        }

        // Check if a search term is present
        $search = $request->input('search');
        if ($search !== null) {
            $prescriptionsQuery->where(function ($query) use ($search) {
                $query->where('medications', 'like', '%' . $search . '%')
                    ->orWhere('prescriber_name', 'like', '%' . $search . '%')
                    ->orWhere('order_number', 'like', '%' . $search . '%')
                    ->orWhere('npi', 'like', '%' . $search . '%')
                    ->orWhere('request_type', 'like', '%' . $search . '%')
                    ->orWhereHas('patient', function ($query) use ($search) {
                        $query->where('firstname', 'like', '%' . $search . '%')
                            ->orWhere('lastname', 'like', '%' . $search . '%');
                    });
            });
        }

        // Rest of logic to filter and paginate prescriptions...
        $prescriptions = $prescriptionsQuery->has('patient')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        // Loop through prescriptions and assign secure_path to files
        $prescriptions->each(function ($prescription) {
            if ($prescription->file) {
                $file = $prescription->file; // Retrieve the file
                if ($file->path) {
                    $presignedUrl = Storage::disk('s3')->temporaryUrl(
                        $file->path,
                        now()->addMinutes(30)
                    );
                    $file->secure_path = $presignedUrl;
                }
            }
        });
        

        switch ($user->userType->id) {
            case 1:
                return view('/divisionOne/prescriptions/index', compact('user', 'prescriptions', 'statuses', 'stages', 'search'));
            case 2:
                return view('/clients/prescriptions/index', compact('user', 'prescriptions', 'statuses', 'stages', 'search'));
            
            default:
                return view('/divisionOne/prescriptions/index', compact('user', 'prescriptions', 'statuses', 'stages', 'search'));
            break;
        }
    }

    

    public function addpatient(Request $request)
    {
        $user = Auth::user();
        if ($user->userType->id == 1) {
            return view('/divisionOne/addpatient', compact('user'));
        }
    }

    public function files(Request $request)
    {
        $user = Auth::user();
        $statuses = Status::orderBy('id', 'asc')->get();
        $stages = Stage::orderBy('id', 'asc')->get();

        $search = $request->input('search');

        if ($search !== null) {
            $files = File::whereHas('prescription', function ($query) use ($search) {
                $query->where('order_number', 'like', '%' . $search . '%');
            })->orWhere('filename', 'like', '%' . $search . '%')
            ->orderByDesc('created_at')
            ->paginate(25);
        } else {
            $files = File::has('prescription')
                ->orderByDesc('created_at')
                ->paginate(25);
        }

        // Initialize S3Client once instead of per file
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_REGION'), // Use AWS region from .env file
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'), // Use AWS key from .env file
                'secret' => env('AWS_SECRET_ACCESS_KEY'), // Use AWS secret from .env file
            ],
        ]);

        $files->getCollection()->transform(function ($file) use ($s3Client) {
            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => env('AWS_BUCKET'), // Use AWS bucket from .env file
                'Key' => 'prescriptions/'.$file->filename,
            ]);

            $request = $s3Client->createPresignedRequest($cmd, '+1 hour');
            $presignedUrl = (string) $request->getUri();

            $file->secure_path = $presignedUrl;
            return $file;
        });

        switch ($user->userType->id) {
        case 1:
                $viewPath = '/cs/files';
                break;
        case 2:
            $viewPath = '/clients/files';
                break;
            default:
                $viewPath = '/clients/files';
        }

        return view($viewPath, compact('user', 'statuses', 'stages', 'files'));
    }

    public function getAllUnreadNotificationsByUserId(Request $request)
    {
        if($request->ajax()){
            $user = auth()->user() ?? null;
            $unreadAnnouncements = [];
            
            $hasPermission = $user->can('executive_dashboard.index') || $user->hasRole('super-admin') ?? false;
            $notifications = [];
            if($hasPermission) {
                $notifications = $user->notifications()
                    ->where('type', 'App\Notifications\AnnouncementNotification')
                    ->whereNull('read_at')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            foreach($notifications as $notification) {
                $data = $notification->data;
                $announcement = Announcement::with('user.employee')
                    ->where('id',$data['announcement'])
                    ->first();
                if(!empty($announcement)) {
                    $announcement['formatted_subject'] = $announcement->formatted_subject;
                    $announcement['formatted_created_at'] = $announcement->formatted_created_at;
                    $announcement['formatted_pst_created_at'] = $announcement->formatted_pst_created_at;
                    $announcement['type'] = 'admin';
                    $unreadAnnouncements[] = $announcement;
                }
            }

            $menuStores = PharmacyStore::get()->keyBy('id');
            $permissionsToCheck = [];
            foreach($menuStores as $ms) {
                $name = 'menu_store.'.$ms->id;
                $permissionsToCheck[$ms->id] = $name;
            }

            $storeIDs = [];
            foreach($permissionsToCheck as $pid => $pname) {
                if($user->can($pname)) {
                    $storeIDs[] = $pid;
                }
            }
            $storeNotifications = $user->notifications()
                ->where('type', 'App\Notifications\Store\AnnouncementNotification')
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->get();
            
            foreach($storeNotifications as $storeNotification) {
                $data = $storeNotification->data;
                $announcement = StoreAnnouncement::with('user.employee', 'pharmacyStore')
                    ->whereIn('pharmacy_store_id', $storeIDs)
                    ->where('id',$data['announcement'])
                    ->first();
                if(!empty($announcement)) {
                    $announcement['formatted_subject'] = $announcement->formatted_subject;
                    $announcement['formatted_created_at'] = $announcement->formatted_created_at;
                    $announcement['formatted_pst_created_at'] = $announcement->formatted_pst_created_at;
                    $announcement['type'] = 'store';
                    $unreadAnnouncements[] = $announcement;
                }
            }

            array_multisort( array_column($unreadAnnouncements, "created_at"), SORT_DESC, $unreadAnnouncements );

            return json_encode([
                'data'=>$unreadAnnouncements,
                'count' => count($unreadAnnouncements),
                'status'=>'success',
                'message'=>'Announcement Notifications Retrieved.']);
        }
    }

    public function showAnnouncement(Request $request)
    {
        $id = $request->id ?? null;
        $type = $request->type ?? null;
        
        $announcement = null;

        if(!empty($id) && !empty($type)) {
            $notificationType = "";
            if($type == 'store') {
                $announcement = StoreAnnouncement::with('user.employee','pharmacyStore')->findOrFail($id);
                $notificationType = 'App\Notifications\Store\AnnouncementNotification';
            } else {
                $announcement = Announcement::with('user.employee')->findOrFail($id);
                $notificationType = 'App\Notifications\AnnouncementNotification';
            }
            if(!empty($announcement)) {
                $announcement['formatted_subject'] = $announcement->formatted_subject;
                $announcement['formatted_created_at'] = $announcement->formatted_created_at;
                $announcement['formatted_pst_created_at'] = $announcement->formatted_pst_created_at;

                DB::table('notifications')
                    ->where('data->announcement', $id)
                    ->where('type', $notificationType)
                    ->where('notifiable_id', auth()->user()->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => Carbon::now()]);
            }
        }

        if($request->ajax()){            
            return json_encode([
                'data'      =>  $announcement,
                'status'    =>  'success',
                'message'   =>  'Announcement Retrieved.'
            ]);
        }

        return [
            'data'      =>  $announcement,
            'status'    =>  'success',
            'message'   =>  'Announcement Retrieved.'
        ];
    }

}
