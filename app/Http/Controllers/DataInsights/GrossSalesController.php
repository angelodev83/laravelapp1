<?php

namespace App\Http\Controllers\DataInsights;

use App\Http\Controllers\Controller;
use App\Imports\GrossSaleImport;
use App\Models\GrossSale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class GrossSalesController extends Controller
{
    public function index($id)
    {
        try{
            $this->checkStorePermission($id);
            
            $breadCrumb = ['Data Insights', 'Gross Sales'];
            
            $months = $this->getMonths();
            $years = $this->getYears();

            return view('/stores/dataInsights/grossSales/index', compact('breadCrumb', 'months', 'years'));
        }
        catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function data(Request $request)
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

            $query = GrossSale::with('user.employee')
                ->where('pharmacy_store_id', $request->pharmacy_store_id);

            // Filter by year and month
            if ($selectedYear !== null) {
                $query->whereYear('transaction_date', $selectedYear);
            }

            if ($selectedMonth !== null) {
                $query->whereMonth('transaction_date', $selectedMonth);
            }
            
            $search = trim($request->search);
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }  
                }  
                $query->orWhere(DB::raw('CONCAT(rx_number, "-", refill_number)'), 'like', "%".$search."%"); 
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
                // if(auth()->user()->can('menu_store.data_insights.gross_sales.update')) {
                //     $actions .= '<a title="Edit" href="javascript:void(0)" data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"  class="me-1"
                //                 id="data-show-btn-'.$value->id.'"><button class="btn btn-sm btn-primary" onclick="showEditForm('.$value->id.');"><i class="fa fa-pencil"></i></button></a>';
                // }
                if(auth()->user()->can('menu_store.data_insights.gross_sales.delete')) {
                    $actions .= '<a title="Delete" href="javascript:void(0)" class="me-1"><button class="btn btn-sm btn-danger" onclick="ShowConfirmDeleteForm(' . $value->id . ')"><i class="fa fa-trash-can"></i></button></a>';
                }             
                $actions .= '</div>';
                

                $newData[] = [
                    'id' => $value->id,
                    'rx_number' => $value->rx_number,
                    'refill_number' => $value->refill_number,
                    'transaction_date' => date('M d, Y', strtotime($value->transaction_date)),
                    'user_id' => $value->user->employee->firstname.' '.$value->user->employee->lastname,
                    'date_filled' => date('M d, Y', strtotime($value->date_filled)),
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

    public function import($id, Request $request)
    {
        try {
            $data = $request->data ? json_decode($request->data) : [];

            $params = [
                'pharmacy_store_id' => $id,
                'user_id' => auth()->user()->id
            ];

            $file = $request->file('upload_file');
            $ext = $file->getClientOriginalExtension();
            $current = file_get_contents($file);
            $file_name = "data_insight_gross_sales.".$ext;
            $save_name = str_replace('\\', '/' , storage_path())."/$file_name";
        
            file_put_contents($save_name, $current);

            $absolute_path = str_replace('\\', '/' , storage_path());

            $filePath = $absolute_path.'/'.$file_name;

            Excel::import(new GrossSaleImport($params), $request->file('upload_file'));

            $response = [
                'data' => $params,
                'status'=>'success',
                'message'=>'Record has been imported.'
            ];

            if($request->ajax()){
                return json_encode($response);
            }
            return $response;
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in GrossSalesController.import.'
            ]);
        }
    }

    public function chartData(Request $request)
    {
        try {
            DB::beginTransaction();

            $year = $request->has('year') ? $request->year : $this->getCurrentPSTDate('Y');
            $currentMonth = $this->getCurrentPSTDate('n');

            $pharmacy_store_id = $request->pharmacy_store_id ?? null;

            $monthlyPrescriptionVolume = [];

            $monthlyGrossSales = GrossSale::select([
                DB::raw('MONTH(transaction_date) as month_number'), 
                DB::raw('COUNT(id) as count_unique_rx_refill')
            ])
            ->where('pharmacy_store_id', $pharmacy_store_id)
            ->whereYear('transaction_date', $year)
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->get()
            ->pluck('count_unique_rx_refill', 'month_number')
            ->toArray();     

            $monthsArray = $this->getMonths();
            $monthlyPrescriptionVolumeCategories = [];

            for($i = 1; $i <= $currentMonth; $i++) {
                $monthlyPrescriptionVolume[] = isset($monthlyGrossSales[$i]) ? $monthlyGrossSales[$i] : 0;
                $monthlyPrescriptionVolumeCategories[] = $monthsArray[$i];
            }

            $dailyGrossSales = GrossSale::select([
                DB::raw('transaction_date'), 
                DB::raw('COUNT(id) as count_unique_rx_refill')
            ])
            ->where('pharmacy_store_id', $pharmacy_store_id)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $currentMonth)
            ->groupBy('transaction_date')
            ->get();
            

            $data = [
                'monthlyPrescriptionVolume' => [
                    'data' => $monthlyPrescriptionVolume,
                    'categories' => $monthlyPrescriptionVolumeCategories,
                ],
                'rxDailyCount' => [
                    'data' => $dailyGrossSales->pluck('count_unique_rx_refill')->toArray(),
                    'categories' => $dailyGrossSales->pluck('transaction_date')->toArray()
                ]
            ];

            DB::commit();
            
            if($request->ajax()){    
                return json_encode([
                    'data'=>$data,
                    'status'=>'success',
                    'message'=>'Record has been retrieved.'
                ]);
            }

            return [
                'data'=>$data,
                'status'=>'success',
                'message'=>'Record has been retrieved.'
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in GrossSalesController.chartData.'
            ]);
        }
    }

    public function summary(Request $request)
    {
        try {
            DB::beginTransaction();

            $transaction_date = $request->has('transaction_date') ? $request->transaction_date : null;
            $month_number = $request->has('month_number') ? $request->month_number : null;
            $year = $request->has('year') ? $request->year : null;
            $pharmacy_store_id = $request->pharmacy_store_id;

            $uniquesObject = GrossSale::select(
                    DB::raw('COUNT(DISTINCT CONCAT(rx_number,"-",refill_number)) as count_unique_rx_refill_numbers')
                )->where('pharmacy_store_id', $pharmacy_store_id);

            if(!empty($year)) {
                $uniquesObject = $uniquesObject->whereYear('transaction_date', $year);
            }

            if(!empty($month_number)) {
                $uniquesObject = $uniquesObject->whereMonth('transaction_date', $month_number);
            }

            if(!empty($transaction_date)) {
                $uniquesObject = $uniquesObject->where('transaction_date', $transaction_date);
            }

            $uniquesObject = $uniquesObject->first();

            $data = [
                'current_date' => $this->getCurrentPSTDate('Y-m-d'),
                'formatted_current_date' => $this->getCurrentPSTDate('F d, Y'),
                'filter' => [
                    'transaction_date'  => $transaction_date,
                    'month_number'      => $month_number,
                    'year'              => $year,
                    'pharmacy_store_id' => $pharmacy_store_id,
                    'formatted_transaction_date'    => !empty($transaction_date) ? date('F d, Y', strtotime($transaction_date)) : '',
                    'formatted_month_name'          => !empty($month_number) ? $this->getMonths()[$month_number] : '',
                ],
                'count_unique_rx_refill_numbers' => isset($uniquesObject->count_unique_rx_refill_numbers) ? $uniquesObject->count_unique_rx_refill_numbers : 0
            ];

            DB::commit();
            
            if($request->ajax()){    
                return json_encode([
                    'data'=>$data,
                    'status'=>'success',
                    'message'=>'Record has been retrieved.'
                ]);
            }

            return [
                'data'=>$data,
                'status'=>'success',
                'message'=>'Record has been retrieved.'
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in GrossSalesController.summary.'
            ]);
        }
    }


    public function delete(Request $request)
    {
        if($request->ajax()){
            $input = $request->all();

            $id = $input['id'];
            $cp = GrossSale::where('id', $id)->first();
            $cp->delete();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }
}
