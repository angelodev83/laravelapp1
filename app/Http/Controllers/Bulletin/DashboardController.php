<?php

namespace App\Http\Controllers\Bulletin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Task;
use App\Interfaces\ITaskRepository;
use App\Interfaces\Common\AnnouncementInterface;
use App\Interfaces\ITicketRepository;
use App\Models\CollectedPayment;
use App\Models\CompletedSalesConfiguration;
use App\Models\GrossRevenueAndCog;
use App\Models\GrossSale;
use App\Models\NewsAndEvent;
use App\Models\QuickLink;
use App\Models\StoreFile;
use App\Models\StoreFolder;
use App\Models\UpcomingEvent;
use App\Repositories\JotForm\PharmacyServiceSatisfactionSurveyRepository;
use App\Repositories\KnowledgeBaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    private $taskRepository;
    private $knowledgeBaseRepository;
    private $ticketRepository;
    private $announcementRepository;
    private $pharmacyServiceSatisfactionSurveyRepository;

    public function __construct(
        ITaskRepository $taskRepository,
        ITicketRepository $ticketRepository,   
        AnnouncementInterface $announcementRepository,
        KnowledgeBaseRepository $knowledgeBaseRepository,
        PharmacyServiceSatisfactionSurveyRepository $pharmacyServiceSatisfactionSurveyRepository
    ) {
        $this->taskRepository = $taskRepository;
        $this->ticketRepository = $ticketRepository;
        $this->announcementRepository = $announcementRepository;
        $this->knowledgeBaseRepository = $knowledgeBaseRepository;
        $this->pharmacyServiceSatisfactionSurveyRepository = $pharmacyServiceSatisfactionSurveyRepository;

        $this->announcementRepository->setModel('storeBulletinAnnouncement');

        $this->middleware('permission:menu_store.bulletin.dashboard.index', ['only' => ['index', 'data']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        // try {
            $this->checkStorePermission($id);

            $currentYear = Carbon::now('America/Los_Angeles')->year;
            $currentMonth = Carbon::now('America/Los_Angeles')->month;
            $currentDate = $this->getCurrentPSTDate();

            $collectedPayments = CollectedPayment::where('pharmacy_store_id', $id)
                ->whereYear('last_payment_date', $currentYear)
                ->whereMonth('last_payment_date', $currentMonth)
                ->sum('running_balance_as_of_date');
            $cogs = GrossRevenueAndCog::where('pharmacy_store_id', $id)
                ->whereYear('completed_on', $currentYear)
                ->whereMonth('completed_on', $currentMonth)
                ->sum('acquisition_cost');
            $grossRevenue = GrossRevenueAndCog::where('pharmacy_store_id', $id)
                ->whereYear('completed_on', $currentYear)
                ->whereMonth('completed_on', $currentMonth)
                ->sum('gross_profit');
            $totalRevenueMTD = GrossRevenueAndCog::where('pharmacy_store_id', $id)
                ->whereYear('completed_on', $currentYear)
                ->whereMonth('completed_on', $currentMonth)
                ->sum('total_price_submitted');

            $monthlyPrescriptionVolume = GrossSale::where('pharmacy_store_id', $id)
                ->whereYear('transaction_date', $currentYear)
                ->whereMonth('transaction_date', $currentMonth)
                ->count(DB::raw('DISTINCT CONCAT(rx_number,"-",refill_number)'));

            $rxDailyCount = GrossSale::where('pharmacy_store_id', $id)
                ->where('transaction_date', $currentDate)
                ->count(DB::raw('DISTINCT CONCAT(rx_number,"-",refill_number)'));

            $bulletinQuickLinks = QuickLink::orderBy('sort', 'asc')->get();
            $bulletinAnnouncements = $this->announcementRepository->retrieveStoreAnnouncement();
            $bulletinAnnouncementRecent = $this->announcementRepository->retrieveRecent(['pharmacy_store_id'=>$id]);
            $bulletinTaskRecent = $this->taskRepository->retrieveRecent(['pharmacy_store_id'=>$id]);
            $bulletinTicketRecent = $this->ticketRepository->retrieveRecent(['pharmacy_store_id'=>$id]);
            $bulletinMonthlyTaskRecent = $this->taskRepository->retrieveRecentMonthlyTasks(['pharmacy_store_id'=>$id]);
            $today = Carbon::today();
            $thirtyDaysFromNow = $today->copy()->addDays(30);
            $lastMonth = $today->copy()->subDays(30)->format('Y-m-d'); 
            // Retrieve upcoming events within the next 30 days and exclude past events
            $upcomingEvents = UpcomingEvent::where('date', '>=', $today)
                ->where('date', '<=', $thirtyDaysFromNow)
                ->orderBy('date', 'asc')
                ->get();


            $newsAndEvents = NewsAndEvent::with('storeDocuments')
                ->where(DB::raw('DATE(created_at)'), '>=', $lastMonth)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $newsAndEventsCount = $newsAndEvents->count();

            $patientFeedbacks = $this->recentPatientFeedbacks();

            // knowlege base
            $filesCounting = $this->knowledgeBaseRepository->getFilesCountPerPage();

            $breadCrumb = ['Bulletin', 'Dashboard'];
            return view('/stores/bulletin/dashboard/index', compact(
                'cogs',
                'breadCrumb',
                'rxDailyCount',
                'grossRevenue',
                'newsAndEvents',
                'filesCounting',
                'upcomingEvents',
                'totalRevenueMTD',
                'patientFeedbacks',
                'collectedPayments',
                'bulletinQuickLinks',
                'bulletinTaskRecent',
                'newsAndEventsCount',
                'bulletinTicketRecent',
                'bulletinAnnouncements',
                'monthlyPrescriptionVolume',
                'bulletinAnnouncementRecent',
                'bulletinMonthlyTaskRecent',
            ));
        // } catch (\Throwable $th) {
        //     return response()->view('/errors/403/index', [], 403);
        // }
    }

    private function recentPatientFeedbacks()
    {
        $data = [
            'collections' => [],
            'item' => []
        ];

        $overallChart = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0
        ];

        $dashboardReviews = $this->pharmacyServiceSatisfactionSurveyRepository->dashboardReviews(1, '242176934304456');
        foreach($dashboardReviews as $d) {
            $data['collections'][] = [
                'url' => null,
                'score' => 0,
                'data' => $d,
                'is_image' => false
            ];
        }

        $folder = StoreFolder::where('page_id', 85)->first();
        $query = StoreFile::with('tag')->where('folder_id', $folder->id)->orderBy('created_at', 'desc')->get();
        foreach($query as $q) {
            $tag = $q->tag ?? null;

            $score = 0;

            if(isset($tag->id)) {
                $custom_data = $tag->custom_data;
                if(!empty($custom_data)) {
                    $custom_data = json_decode($custom_data);
                    $score = isset($custom_data->score) ? $custom_data->score : 0;
                }
            }

            $score = (int) $score;

            $pathfile = $q->path.$q->name;
            $s3Url = Storage::disk('s3')->temporaryUrl(
                $pathfile,
                now()->addMinutes(30)
            );
            $data['collections'][] = [
                'url' => $s3Url,
                'score' => $score,
                'data' => '',
                'is_image' => true
            ];

            if($score > 0) {
                $overallChart[$score] +=1;
            }
        }

        $summaryOverallStars = $this->pharmacyServiceSatisfactionSurveyRepository->summaryOverallStars(1);

        $stars = $summaryOverallStars['stars'];

        foreach($stars as $star => $count) {
            $overallChart[$star] += $count;
        }


        $chart = [
            $overallChart[5],
            $overallChart[4],
            $overallChart[3],
            $overallChart[2],
            $overallChart[1],
        ];

        $data['item']['overallChart'] = $chart;

        return $data;
    }

    public function announcementNotificationsPerUser(Request $request)
    {   
        if($request->ajax()){
            
            $this->taskRepository->setDataTable($request);
            $data = $this->taskRepository->getDataTable();
            
            return response()->json($data, 200);
        }
    }

    public function taskReminders(Request $request)
    {   
        if($request->ajax()){
            
            $this->taskRepository->setDataTable($request);
            $data = $this->taskRepository->getDataTable();
            
            return response()->json($data, 200);
        }
    }
}
