<?php

namespace App\Http\Controllers\Escalation;

use App\Http\Controllers\Controller;
use App\Models\PharmacyServiceSatisfactionSurvey;
use App\Repositories\API\JotFormRepository;
use App\Repositories\JotForm\PharmacyServiceSatisfactionSurveyRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    private $jotFormRepository, $pharmacyServiceSatisfactionSurveyRepository;

    public function __construct(JotFormRepository $jotFormRepository
        , PharmacyServiceSatisfactionSurveyRepository $pharmacyServiceSatisfactionSurveyRepository
    ) {
        $this->jotFormRepository = $jotFormRepository;
        $this->pharmacyServiceSatisfactionSurveyRepository = $pharmacyServiceSatisfactionSurveyRepository;

        $this->middleware('permission:menu_store.escalation.reviews.index|menu_store.escalation.reviews.create|menu_store.escalation.reviews.update|menu_store.escalation.reviews.delete');
    }

    public function index($id, Request $request)
    {
        try {
            $this->checkStorePermission($id);

            $summaryOverallStars = $this->pharmacyServiceSatisfactionSurveyRepository->summaryOverallStars($id, '242175635430453');

            $breadCrumb = ['Escalations', 'Reviews'];
            return view('/stores/escalation/reviews/index', compact('breadCrumb', 'summaryOverallStars'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function syncJotForm(Request $request)
    {
        try{
            $formId = isset($request->form_id) ? $request->form_id : null;
            // '242175635430453' --> 1-3 (escalations only)
            // '242176934304456 --> 4-5
            $res = $this->pharmacyServiceSatisfactionSurveyRepository->sync($formId);

            $result = [
                'data'=> $res,
                'status'=>'success',
                'message'=> $res['count']. ' New Reviews/s has been synced/added.'
            ];

            if($request->ajax()) {
                return response()->json($result);
            }
            return $result;
        } catch (Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in ReviewController.sync.'
            ];
            if($request->ajax()) {
                return response()->json($result);
            }
            return $result;
        }    

    }

    public function data($id, Request $request)
    {
        try{
            $result = $this->pharmacyServiceSatisfactionSurveyRepository->dataTable($request);

            if($request->ajax()) {
                return response()->json($result, 200);
            }
            return $result;
        } catch (Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in ReviewController.data.'
            ];
            if($request->ajax()) {
                return response()->json($result);
            }
            return $result;
        }
    }

}
