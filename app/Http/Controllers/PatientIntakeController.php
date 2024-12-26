<?php

namespace App\Http\Controllers;

use App\Interfaces\IPatientRepository;
use App\Models\Patient;
use App\Models\PatientJotForm;
use Illuminate\Http\Request;
use App\Repositories\API\JotFormRepository;
use App\Repositories\JotForm\PatientRepository as JotFormPatientRepository;
use App\Repositories\PatientRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class PatientIntakeController extends Controller
{
    private $jotFormRepository;
    private $jotFormPatientRepository;
    private PatientRepository $patientRepository;

    public function __construct(JotFormRepository $jotFormRepository
    ,   PatientRepository $patientRepository
    ,   JotFormPatientRepository $jotFormPatientRepository
    ) {
        $this->jotFormRepository = $jotFormRepository;
        $this->patientRepository = $patientRepository;
        $this->jotFormPatientRepository = $jotFormPatientRepository;
    }


    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $patientCounts = $this->patientRepository->sourcePatientCounts('JotForm', 'jotForm');

            $breadCrumb = ['Forms', 'New Patients'];
            return view('/stores/patientIntakes/allFiles/index', compact('breadCrumb', 'patientCounts'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function syncJotForm(Request $request)
    {
        try{
            
            $res = $this->jotFormPatientRepository->sync();

            $result = [
                'data'=> $res,
                'status'=>'success',
                'message'=> $res['count']. ' New Patient/s has been synced/added.'
            ];

            if($request->ajax()) {
                return response()->json($result);
            }
            return $result;
        } catch (Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in PatientIntakeController.sync.'
            ];
            if($request->ajax()) {
                return response()->json($result);
            }
            return $result;
        }    

    }

    public function facesheet($id, $p_id)
    {
        $user = Auth::user();
        $breadCrumb = ['Forms', 'New Patients', 'Facesheet'];
        $breadCrumb['back'] = "/store/jot-form/$id/patient-intakes";
        $profileData = $this->profileData($p_id);
        
        return view('/stores/patientIntakes/facesheet/index', compact('user','breadCrumb', 'profileData'));
    }

    private function profileData($id)
    {
        try {
            //code...
            $profileData = Patient::with('jotForm')->where('id',$id)->first();

            return $profileData;
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in PatientController.profileData.'
            ]);
        }
    }

    public function data(Request $request)
    {   
        if($request->ajax()){

            $request->merge(['mustHaveRelation' => 'jotForm']);
            
            $this->patientRepository->setDataTable($request);
            $data = $this->patientRepository->getDataTable();
            
            return response()->json($data, 200);
        }
    }

}
