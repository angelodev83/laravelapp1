<?php

namespace App\Repositories\JotForm;

ini_set('max_execution_time', '3600');

use App\Models\Patient;
use App\Models\PatientJotFormPrescriptionTransfer;
use App\Models\PharmacyServiceSatisfactionSurvey;
use App\Models\PharmacyServiceSatisfactionSurveyEvaluation;
use App\Repositories\API\JotFormRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PharmacyServiceSatisfactionSurveyRepository
{
    private $jotFormRepository;

    private $apiKey = '0af9148eeac7ee7b3e7c727e9ecd4f9c';
    private $baseUrl = 'https://hipaa-api.jotform.com';
    private $formId = '242176934304456';

    public function __construct(JotFormRepository $jotFormRepository)
    {
        $this->jotFormRepository = $jotFormRepository;
        $this->jotFormRepository->setConfiguration($this->apiKey, $this->baseUrl);
    }

    public function sync($formId = null)
    {
        $formId = !empty($formId) ? $formId : $this->formId;

        $pharmacy_store_id = 1;

        $submissions = $this->jotFormRepository->getFormSubmissions($formId);

        $res = 0;

        foreach($submissions as $submission) {
            $answers = $submission['answers'];

            $jot_form_uid = $submission['id'] ?? null;
            $jot_form_id = $submission['form_id'] ?? null;
            $jot_form_ip = $submission['ip'] ?? null;
            $jot_form_created_at = $submission['created_at'] ?? null;
            $jot_form_status = $submission['status'] ?? 'ACTIVE';
            $jot_form_new = $submission['new'] ?? null;

            $lastname = isset($answers[27]['answer']['last']) ? $answers[27]['answer']['last'] : null;
            $firstname = isset($answers[27]['answer']['first']) ? $answers[27]['answer']['first'] : null;
            $email_address = isset($answers[28]['answer']) ? $answers[28]['answer'] : null;
            $what_would_have_made_experience_5_stars = isset($answers[31]['answer']) ? $answers[31]['answer'] : null;
            $suggestions_for_improvement = isset($answers[33]['answer']) ? $answers[33]['answer'] : null;
            $pharmacist_name = isset($answers[41]['answer']) ? $answers[41]['answer'] : null;
            $reason_of_call_or_visit = isset($answers[42]['answer']) ? $answers[42]['answer'] : null;
            $how_was_service_today_score = isset($answers[50]['answer']) ? $answers[50]['answer'] : null;

            $overallStarKey = $formId == '242176934304456' ? 51 : 38;
            $how_satisfied_with_our_pharmacy_overall_score = isset($answers[$overallStarKey]['answer']) ? $answers[$overallStarKey]['answer'] : null;

            $pharmacist_or_patient_care_team_live_up_to_expectation_score = isset($answers[58]['answer']) ? $answers[58]['answer'] : null;
            $experience_feedback = isset($answers[74]['answer']) ? $answers[74]['answer'] : null;

            $evaluationArray = isset($answers[29]['answer']) ? $answers[29]['answer'] : [];

            $array = [
                'uid'           => $jot_form_uid,
                'form_id'       => $jot_form_id,
                'ip'            => $jot_form_ip,
                'jf_created_at' => $jot_form_created_at,
                'status'        => $jot_form_status,
                'new'           => $jot_form_new,
                'lastname'                                                      => $lastname,
                'firstname'                                                     => $firstname,
                'email_address'                                                 => $email_address,
                'pharmacist_name'                                               => $pharmacist_name,
                'how_was_service_today_score'                                   => $how_was_service_today_score,
                'how_satisfied_with_our_pharmacy_overall_score'                 => $how_satisfied_with_our_pharmacy_overall_score,
                'pharmacist_or_patient_care_team_live_up_to_expectation_score'  => $pharmacist_or_patient_care_team_live_up_to_expectation_score,
                'experience_feedback'                                           => $experience_feedback,
                'what_would_have_made_experience_5_stars'                       => $what_would_have_made_experience_5_stars,
                'suggestions_for_improvement'                                   => $suggestions_for_improvement,
                'reason_of_call_or_visit'                                       => $reason_of_call_or_visit
            ];

            $survey = PharmacyServiceSatisfactionSurvey::where('uid', $jot_form_uid)->first();

            if(isset($survey->id)) {
                // update
                $array['updated_at'] = Carbon::now();
                $save = PharmacyServiceSatisfactionSurvey::where('uid', $jot_form_uid)->update($array);
            } else {
                $res++;
                // create
                $array['created_at'] = Carbon::now();
                $array['user_id'] = isset(auth()->user()->id) ? auth()->user()->id : 1;
                $array['pharmacy_store_id'] = $pharmacy_store_id;

                // $save = PharmacyServiceSatisfactionSurvey::insertOrIgnore($array);

                $survey_id = DB::table('pharmacy_service_satisfaction_surveys')->insertGetId($array);

                if(count($evaluationArray) > 0 ) {
                    $insertArray = [];
                    foreach($evaluationArray as $question => $answer) {
                        $insertArray[] = [
                            "survey_id" => $survey_id,
                            "question"  => $question,
                            "answer"    => $answer,
                            "created_at" => Carbon::now()
                        ];
                    }
                    $save = PharmacyServiceSatisfactionSurveyEvaluation::insertOrIgnore($insertArray);
                }
            }
        }

        return [
            'count' => $res,
            'message' => 'Synced ('.$res.') Pharmacy Service Satisfaction Survey JotForm API successfully '.date('Y-m-d H:i:s')
        ];
    }

    public function dataTable($request)
    {
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        // get data from products table
        $query = PharmacyServiceSatisfactionSurvey::with('user.employee', 'evaluations')
            ->where('pharmacy_store_id', $request->pharmacy_store_id)
            // ->whereHas('evaluations')
            ->where('form_id', '242175635430453')
            ->whereNot('status', 'DELETED');
          
        $search = $request->search;
        $columns = $request->columns;
        
        $query = $query->where(function($query) use ($search, $columns){
            foreach ($columns as $column) {
                if($column['searchable'] === "true"){
                    if($column['name'] == 'fullname') {
                        $query->orWhere("firstname", 'like', "%".$search."%");
                        $query->orWhere("lastname", 'like', "%".$search."%");
                        $query->orWhere(DB::raw("CONCAT(firstname, ' ', lastname)"), 'like', "%".$search."%");
                        $query->orWhere(DB::raw("CONCAT(lastname, ', ', firstname)"), 'like', "%".$search."%");
                    } else {
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }
                }  
            }   
            $query->orWhereHas('user.employee', function ($query) use ($search) {
                $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $search . '%']);
            });
        });


        $orderByCol = $request->columns[$request->order[0]['column']]['name'];

        if($orderByCol == 'fullname') {
            $query = $query->orderBy('firstname', $orderBy)->orderBy('lastname', $orderBy);
        } else {
            $query = $query->orderBy($orderByCol, $orderBy);
        }


        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();
        
        $newData = [];
        foreach ($data as $value) {        
            $newData[] = [
                'id' => $value->id,
                'uid'   => $value->uid,
                'form_id'   => $value->form_id,
                'ip'    => $value->ip,
                'jf_created_at' => $value->jf_created_at,
                'status'    => $value->status,
                'new'   => $value->new,
                'stars_range'   => $value->form_id == '242176934304456' ? 'HIGH scores' : 'LOW scores',
                'lastname'  => $value->lastname,
                'firstname' => $value->firstname,
                'fullname' => $value->firstname.' '.$value->lastname,
                'email_address' => $value->email_address,
                'pharmacist_name'   => $value->pharmacist_name,
                'how_was_service_today_score'   => $value->how_was_service_today_score,
                'how_satisfied_with_our_pharmacy_overall_score' => $value->how_satisfied_with_our_pharmacy_overall_score,
                'pharmacist_or_patient_care_team_live_up_to_expectation_score'  => $value->pharmacist_or_patient_care_team_live_up_to_expectation_score,
                'experience_feedback'   => $value->experience_feedback,
                'user_id'   => $value->user_id,
                'pharmacy_store_id' => $value->pharmacy_store_id,
                'what_would_have_made_experience_5_stars'   => $value->what_would_have_made_experience_5_stars,
                'suggestions_for_improvement'   => $value->suggestions_for_improvement,
                'reason_of_call_or_visit'   => $value->reason_of_call_or_visit,
                'evaluations'   => isset($value->evaluations) ? $value->evaluations : [],
                'created_by' => $value->user->employee->firstname.' '.$value->user->employee->lastname,
                'created_at' => $value->created_at,
                'formatted_created_at' => date('M d, Y g:i A', strtotime($value->pst_created_at))
            ];
        }   
        
        return [
            "draw"=> $request->draw, 
            "recordsTotal"=> $recordsTotal, 
            "recordsFiltered" => $recordsFiltered, 
            'data' => $newData
        ];
    }

    public function summaryOverallStars($pharmacy_store_id = null, $form_id = null)
    {
        $query = PharmacyServiceSatisfactionSurvey::select(
            DB::raw('COUNT(how_satisfied_with_our_pharmacy_overall_score) AS count_stars'),
            'how_satisfied_with_our_pharmacy_overall_score'
        );

        if(!empty($pharmacy_store_id)) {
            $query = $query->where('pharmacy_store_id', $pharmacy_store_id);
        }

        if(!empty($form_id)) {
            $query = $query->where('form_id', $form_id);
        }
            
        $query = $query->whereNot('status', 'DELETED')
            ->groupBy('how_satisfied_with_our_pharmacy_overall_score')
            ->orderBy('how_satisfied_with_our_pharmacy_overall_score', 'asc')
            ->pluck('count_stars', 'how_satisfied_with_our_pharmacy_overall_score')
            ->toArray();

        $total_stars = 0;
        $total_count = 0;
        foreach($query as $k => $q) {
            $total_stars += $q * $k; 
            $total_count += $q;
        }

        $average = 0;
        
        if($total_stars > 0 && $total_count > 0) {
            $average = $total_stars/$total_count;
        }

        $summary = [
            'average' => (int) round($average),
            'stars' => $query
        ];

        return $summary;
    }

    public function dashboardReviews($pharmacy_store_id = null, $form_id = null)
    {
        $query = PharmacyServiceSatisfactionSurvey::query();

        if(!empty($pharmacy_store_id)) {
            $query = $query->where('pharmacy_store_id', $pharmacy_store_id);
        }

        if(!empty($form_id)) {
            $query = $query->where('form_id', $form_id);
        }
            
        $query = $query->whereNot('status', 'DELETED')
            ->orderBy('created_at', 'desc')
            ->get();

        return $query;
    }

}