<?php

namespace App\Http\Controllers;

use App\Models\ReleaseOfInformation;
use App\Repositories\API\JotFormRepository;
use App\Repositories\JotForm\ReleaseOfInformationRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ReleaseOfInformationController extends Controller
{
    private $jotFormRepository, $releaseOfInformationRepository;

    public function __construct(JotFormRepository $jotFormRepository
        , ReleaseOfInformationRepository $releaseOfInformationRepository
    ) {
        $this->jotFormRepository = $jotFormRepository;
        $this->releaseOfInformationRepository = $releaseOfInformationRepository;
    }


    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $patientCounts = [
                'all_count'         => ReleaseOfInformation::count(),
            ];

            $breadCrumb = ['Forms', 'Records Release'];
            return view('/stores/releaseOfInformation/allFiles/index', compact('breadCrumb', 'patientCounts'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function syncJotForm(Request $request)
    {
        try{

            $res = $this->releaseOfInformationRepository->sync();

            $result = [
                'data'=> [],
                'status'=>'success',
                'message'=>'Record has been saved.'
            ];

            if($request->ajax()) {
                return response()->json($result);
            }
            return $result;
        } catch (Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in ReleaseOfInformationController.sync.'
            ];
            if($request->ajax()) {
                return response()->json($result);
            }
            return $result;
        }    

    }

    public function show($id, Request $request) {
        $query = ReleaseOfInformation::findOrFail($id);
        $data = [];
        if(isset($query->id)) {
            $data = [
                'id' => $query->id,
                'jot_form_uid' => $query->jot_form_uid,
                'jot_form_id' => $query->jot_form_id,
                'jot_form_ip' => $query->jot_form_ip,
                'jot_form_created_at' => $query->jot_form_created_at,
                'jot_form_status' => $query->jot_form_status,
                'hereby_authorize_person' => $query->hereby_authorize_person,
                'hereby_authorize_person_name' => $query->hereby_authorize_person_name,
                'hereby_authorize_person_address' => $query->hereby_authorize_person_address,
                'hereby_authorize_person_phone_number' => $query->hereby_authorize_person_phone_number,
                'hereby_authorize_person_fax_number' => $query->hereby_authorize_person_fax_number,
                'to_person' => $query->to_person,
                'to_person_name' => $query->to_person_name,
                'to_person_address' => $query->to_person_address,
                'to_person_phone_number' => $query->to_person_phone_number,
                'to_person_fax_number' => $query->to_person_fax_number,
                'information_to_data' => $query->information_to_data,
                'purpose' => $query->purpose,
                'expiration_date' => $query->expiration_date,
                'patient_firstname' => $query->patient_firstname,
                'patient_lastname' => $query->patient_lastname,
                'patient_birth_date' => $query->patient_birth_date,
                'signed_date' => $query->signed_date,
                'relationship_to_patient' => $query->relationship_to_patient,
            ];
        }
        return $data;
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // get data from patients table
            $query = ReleaseOfInformation::query();

            // Search //input all searchable fields
            $search = trim($request->search);
            $columns = $request->columns;

            $encryptedQuery = [];
            if(!empty($search)) {
                $encryptedQuery = ReleaseOfInformation::query()->get()->filter(function ($encryptedQuery) use ($request) {
                    return 
                        stristr($encryptedQuery->getDecryptedPatientFirstname(), trim($request->search)) !== false
                        || stristr($encryptedQuery->getDecryptedPatientLastname(), trim($request->search)) !== false
                        || stristr($encryptedQuery->getDecryptedPatientBirthDate(), trim($request->search)) !== false

                        // || stristr($encryptedQuery->hereby_authorize_person, trim($request->search)) !== false
                        // || stristr($encryptedQuery->hereby_authorize_person_name, trim($request->search)) !== false
                        // || stristr($encryptedQuery->hereby_authorize_person_address, trim($request->search)) !== false
                        // || stristr($encryptedQuery->hereby_authorize_person_phone_number, trim($request->search)) !== false
                        // || stristr($encryptedQuery->hereby_authorize_person_fax_number, trim($request->search)) !== false
                        // || stristr($encryptedQuery->to_person, trim($request->search)) !== false
                        // || stristr($encryptedQuery->to_person_name, trim($request->search)) !== false
                        // || stristr($encryptedQuery->to_person_address, trim($request->search)) !== false
                        // || stristr($encryptedQuery->to_person_phone_number, trim($request->search)) !== false
                        // || stristr($encryptedQuery->to_person_fax_number, trim($request->search)) !== false
                        // || stristr($encryptedQuery->purpose, trim($request->search)) !== false
                        // || stristr($encryptedQuery->expiration_date, trim($request->search)) !== false
                        || stristr($encryptedQuery->signed_date, trim($request->search)) !== false
                        || stristr($encryptedQuery->relationship_to_patient, trim($request->search)) !== false
                        || stristr($encryptedQuery->jot_form_uid, trim($request->search)) !== false;
                })->pluck('id')->toArray();
            }

            if(!empty($encryptedQuery)) {  
                $query->whereIn('id',$encryptedQuery);
            }

            $orderByCol =  'id';
            $query = $query->orderBy($orderByCol, $orderBy);

            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {
                $birthdate = Crypt::decryptString($value->patient_birth_date);
                $newData[] = [
                    'id' => $value->id,
                    'jot_form_uid' => $value->jot_form_uid,
                    'patient_firstname' => Crypt::decryptString($value->patient_firstname),
                    'patient_lastname' => Crypt::decryptString($value->patient_lastname),
                    'patient_birth_date' =>  !empty($birthdate) ? date('M d, Y' , strtotime($birthdate)) : '',
                    'created_at' => !empty($value->created_at) ? $value->created_at->format('M d, Y h:iA') : '',
                    'updated_at' => !empty($value->updated_at) ? $value->updated_at->format('M d, Y h:iA') : '',

                    'signed_date' => $value->signed_date,
                    'formatted_signed_date' => date('F d, Y', strtotime($value->signed_date)),
                    'relationship_to_patient' => $value->relationship_to_patient,
                    'jot_form_created_at' => $value->jot_form_created_at,
                    'formatted_jot_form_created_at' => date('F d, Y h:iA', strtotime($value->jot_form_created_at)),
                    'created_at' => $value->created_at,
                    'updated_at' => $value->updated_at,
                ];
            }   

            $total_count = ReleaseOfInformation::count();

            $data = [
                "totalCount" => $data->count(), 
                "draw"=> $request->draw, 
                "recordsTotal"=> $recordsTotal, 
                "recordsFiltered" => $recordsFiltered, 
                'data' => $newData
            ];
            
            return response()->json($data, 200);
        }
    }


    public function details($id, $p_id)
    {
        $user = auth()->user();
        $breadCrumb = ['Forms', 'Records Release', 'Details'];
        $breadCrumb['back'] = "/store/jot-form/$id/release-of-information";
        
        $details = ReleaseOfInformation::findOrFail($p_id);

        $items = json_decode($details->information_to_data);
        
        return view('/stores/releaseOfInformation/details/index', compact('user','breadCrumb', 'details','items'));
    }

}
