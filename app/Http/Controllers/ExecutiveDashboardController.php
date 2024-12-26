<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\ClinicalRepository;
use App\Repositories\DataInsightsRepository;
use Illuminate\Http\Request;

class ExecutiveDashboardController extends Controller
{
    private $dataInsightsRepository, $clinicalRepository;

    public function __construct(
        DataInsightsRepository $dataInsightsRepository
        , ClinicalRepository $clinicalRepository
    )
    {
        $this->dataInsightsRepository = $dataInsightsRepository;
        $this->clinicalRepository = $clinicalRepository;
    }

    public function dataInsightsCharts(Request $request)
    {
        try {

            $date_from = $request->date_from ?? null;
            $date_to = $request->date_to ?? null;
            $pharmacy_store_id = $request->pharmacy_store_id ?? null;

            $params = [];

            if(!empty($pharmacy_store_id)) {
                $params['pharmacy_store_id'] = $pharmacy_store_id;
            }

            if(!empty($date_from)) {
                $params['date_from'] = $date_from;
            }

            if(!empty($date_to)) {
                $params['date_to'] = $date_to;
            }

            $current_year = (int) date('Y');
            $current_month_number = (int) date('n');

            $dataArray = $this->dataInsightsRepository->computeDataInsightsByYear($current_year, $params);

            $data = [
                'filter' => [
                    'current_year' => $current_year,
                    'current_month' => $current_month_number,
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'pharmacy_store_id' => $pharmacy_store_id
                ],
                'monthly' => $dataArray
            ];

            $response = [
                'data'      =>  $data,
                'status'    =>  'success',
                'message'   =>  'Record has been computed.'
            ];

            if($request->ajax()){
                return json_encode($response);
            }

            return $response;
            
        } catch (\Exception $e) {
            
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in ExecutiveDashboardController.dataInsightsCharts.db_transaction.'
            ]);
        }
    }

    public function clinicalCharts(Request $request)
    {
        try {

            $date_from = $request->date_from ?? '';
            $date_to = $request->date_to ?? '';
            $pharmacy_store_id = $request->pharmacy_store_id ?? '';

            $params = [];

            if(!empty($pharmacy_store_id)) {
                $params['pharmacy_store_id'] = $pharmacy_store_id;
            }

            if(!empty($date_from)) {
                $params['date_from'] = $date_from;
            }

            if(!empty($date_to)) {
                $params['date_to'] = $date_to;
            }

            $current_year = (int) date('Y');
            $current_month_number = (int) date('n');

            $dataArray = $this->clinicalRepository->computeClinicalByYear($current_year, $params);

            $data = [
                'filter' => [
                    'current_year' => $current_year,
                    'current_month' => $current_month_number,
                    'date_from' => $date_from,
                    'date_to' => $date_to
                ],
                'monthly' => $dataArray
            ];

            $response = [
                'data'      =>  $data,
                'status'    =>  'success',
                'message'   =>  'Record has been computed.'
            ];

            if($request->ajax()){
                return json_encode($response);
            }

            return $response;
            
        } catch (\Exception $e) {
            
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in ExecutiveDashboardController.clinicalCharts.db_transaction.'
            ]);
        }
    }

}
