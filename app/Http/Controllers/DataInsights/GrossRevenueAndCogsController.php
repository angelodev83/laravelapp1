<?php

namespace App\Http\Controllers\DataInsights;

use App\Http\Controllers\Controller;
use App\Interfaces\UploadInterface;
use App\Models\GrossRevenueAndCog;
use App\Models\CollectedPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GrossRevenueAndCogsController extends Controller
{
    private UploadInterface $repository;

    public function __construct(UploadInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index($id)
    {
        try{
            $this->checkStorePermission($id);
            
            $user = Auth::user();

            $breadCrumb = ['Data Insights', 'Completed Sales'];
            
            $months = $this->getMonths();
            $years = $this->getYears();

            return view('/stores/dataInsights/grossRevenueAndCogs/index', compact('user','breadCrumb', 'months', 'years'));
        }
        catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function chartData(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();
            // Get the current year and month
            $currentYear = Carbon::now('America/Los_Angeles')->year;
            $currentMonth = Carbon::now('America/Los_Angeles')->month;
            $storeId = $data['pharmacy_store_id'];

            $dataArray = GrossRevenueAndCog::where('pharmacy_store_id', $storeId)
                ->whereYear('completed_on', $currentYear)
                ->whereMonth('completed_on', $currentMonth)
                ->orderBy('completed_on', 'asc')
                ->pluck('gross_profit')
                ->toArray();

            $cogsDataArray = GrossRevenueAndCog::where('pharmacy_store_id', $storeId)
                ->whereYear('completed_on', $currentYear)
                ->whereMonth('completed_on', $currentMonth)
                ->orderBy('completed_on', 'asc')
                ->pluck('acquisition_cost')
                ->toArray();

            $paymentDataArray = CollectedPayment::where('pharmacy_store_id', $storeId)
                ->whereYear('last_payment_date', $currentYear)
                ->whereMonth('last_payment_date', $currentMonth)
                ->orderBy('last_payment_date', 'asc')
                ->pluck('running_balance_as_of_date')
                ->toArray();

            // Total Revenue MTD
            $totalRevenueMTD = GrossRevenueAndCog::where('pharmacy_store_id', $storeId)
                ->whereYear('completed_on', $currentYear)
                ->whereMonth('completed_on', $currentMonth)
                ->selectRaw('DATE_FORMAT(completed_on, "%b %d, %Y") AS date')
                ->selectRaw('SUM(total_price_submitted) AS total')
                ->groupBy('date')
                ->get();
            $totalRevenueDate = $totalRevenueMTD->pluck('date')->toArray();
            $totalRevenueTotal = $totalRevenueMTD->pluck('total')->toArray();

            // Monthly Prescription Volume
            $currentMonthNumber = date('n'); // Get the current month number (1-12)
            $monthlyData = GrossRevenueAndCog::where('pharmacy_store_id', $storeId)
                ->whereYear('completed_on', $currentYear)
                ->selectRaw('MONTH(completed_on) as month, COUNT(*) as rx_count')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(function ($item) use ($currentMonthNumber) {
                    $item->month_name = $this->getMonthName($item->month);
                    $item->months_elapsed = $item->month <= $currentMonthNumber
                        ? $currentMonthNumber - $item->month
                        : 12 - $item->month + $currentMonthNumber;
                    return $item;
                });
            
            $monthlyRx = $monthlyData->pluck('month_name')->toArray();
            $monthlyRxTotalCounts = $monthlyData->pluck('rx_count')->toArray();
        
            // RX Daily Count
            $dailyData = GrossRevenueAndCog::where('pharmacy_store_id', $storeId)
                ->whereYear('completed_on', $currentYear)
                ->selectRaw('MONTH(completed_on) as month, COUNT(*) as total_rx_count')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(function ($item) use ($currentMonthNumber, $currentYear) {
                    $daysInMonth = Carbon::createFromDate($currentYear, $item->month, 1)->daysInMonth;
                    $daysElapsed = $item->month < $currentMonthNumber
                        ? $daysInMonth
                        : now()->day;
            
                    return round(($item->total_rx_count / $daysElapsed),2);
                })
                ->toArray();
                
            return response()->json([
                'totalRevenueDate' => $totalRevenueDate,
                'totalRevenueTotal' => $totalRevenueTotal,
                'monthlyPrescriptionVolume' => $monthlyRx,
                'monthlyCountPrescriptionVolume' => $monthlyRxTotalCounts,
                'dailyRxCount' => $dailyData,
                'gross_profit_data' => $dataArray,
                'cogs_data' => $cogsDataArray,
                'colleted_payment_data' => $paymentDataArray
            ]);
        }
    }

    private function getMonthName($monthNumber)
    {
        return date('F', mktime(0, 0, 0, $monthNumber, 1));
    }

    public function upload(Request $request)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();
                $this->repository->uploadGrossRevenueAndCogs($request);
                DB::commit();
                
                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in OperationOrderController.upload.'
            ]);
        }
    }

    public function update(Request $request)
    {
        if($request->ajax()){
            
            $input = $request->all();
            
            $dataArray = json_decode($input['data'], true);
            
            $validation = Validator::make($dataArray, [
                'gross_profit' => 'numeric|decimal:0,2',
                'acquisition_cost' => 'numeric|decimal:0,2',
            ]);

            if ($validation->passes()){

                $cp = GrossRevenueAndCog::where('id', $dataArray['id'])->first();
                $cp->gross_profit = ($dataArray['gross_profit'] == '')?null:$dataArray['gross_profit'];
                $cp->acquisition_cost = ($dataArray['acquisition_cost'] == '')?null:$dataArray['acquisition_cost'];
                $cp->save();
                
                return json_encode([
                    'status'=> 'success',
                    'message'=> 'Record has been updated.'
                ]);
            } else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Record saving failed.'
                ]);
            }

        }
    } 

    public function destroy(Request $request)
    {
        if($request->ajax()){
            $input = $request->all();

            $id = $input['id'];
            $cp = GrossRevenueAndCog::where('id', $id)->first();
            $cp->delete();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    public function get_data(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            $selectedYear = $request->input('year');
            $selectedMonth = $request->input('month');

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // get data from products table
            // $query = DB::table('view_inmars')->where('pharmacy_store_id', $id)->groupBy('name');
            $query = GrossRevenueAndCog::with('user.employee')->where('pharmacy_store_id', $request->pharmacy_store_id);

            // Filter by year and month
            if ($selectedYear !== null) {
                $query->whereYear('completed_on', $selectedYear);
            }

            if ($selectedMonth !== null) {
                $query->whereMonth('completed_on', $selectedMonth);
            }
            
            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }  
                }   
                $query->orWhereHas('user.employee', function ($query) use ($search) {
                        $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $search . '%']);
                    });
            });

            $orderByCol =  $columns[$orderColumnIndex]['name'];

            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();
            
            $newData = [];
            foreach ($data as $value) {
                

                $actions = '<div class="d-flex order-actions">';
                // if(Auth::user()->can('menu_store.eod_register_report.register.index')) {
                //     $actions .= '<a title="View" href="javascript:void(0)" data-id="'.$value->id.'" 
                //                 data-array="'.htmlspecialchars(json_encode($value)).'" class="me-1"
                //                 id="inmar-show-btn-'.$value->id.'"><button class="btn btn-sm btn-primary" onclick="showViewForm('.$value->id.');"><i class="fa fa-eye"></i></button></a>';
                // }
                // if(Auth::user()->can('menu_store.eod_register_report.register.update')) {
                //     $actions .= '<a title="Files" href="javascript:void(0)" data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"  class="me-1"
                //                 id="data-file-btn-'.$value->id.'"><button class="btn btn-sm btn-secondary" onclick="showFileModal('.$value->id.');"><i style="padding-left: 2px; padding-right: 2px;" class="fa-solid fa-file"></i></button></a>';
                // } 
                if(Auth::user()->can('menu_store.data_insights.gross_revenue_and_cogs.update')) {
                    $actions .= '<a title="Edit" href="javascript:void(0)" data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"  class="me-1"
                                id="data-show-btn-'.$value->id.'"><button class="btn btn-sm btn-primary" onclick="showEditForm('.$value->id.');"><i class="fa fa-pencil"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.data_insights.gross_revenue_and_cogs.delete')) {
                    $actions .= '<a title="Delete" href="javascript:void(0)" class="me-1"><button class="btn btn-sm btn-danger" onclick="ShowConfirmDeleteForm(' . $value->id . ')"><i class="fa fa-trash-can"></i></button></a>';
                }             
                $actions .= '</div>';
                
                $dateString = $value->completed_on;
                $formattedDate = Carbon::parse($dateString);
                $value->completed_on = $formattedDate;

                $newData[] = [
                    'id' => $value->id,
                    'rx_number' => $value->rx_number,
                    'gross_profit' => $value->gross_profit,
                    'acquisition_cost' => $value->acquisition_cost,
                    'user_id' => $value->user->employee->firstname.' '.$value->user->employee->lastname,
                    'total_price_submitted' => $value->total_price_submitted,
                    'completed_on' => $value->completed_on->format('M d, Y g:i A'),
                    'created_at' => date('M d, Y g:i A', strtotime($value->pst_created_at)),
                    'actions' =>  $actions
                ];
            }   
            
            return response()->json([
                'draw'=> $request->draw, 
                'recordsTotal'=> $recordsTotal, 
                'recordsFiltered' => $recordsFiltered, 
                'data' => $newData], 200);
        }

    }
}
