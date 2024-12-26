<?php

namespace App\Http\Controllers\Clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Patient;
use App\Http\Controllers\Controller;
use App\Interfaces\UploadInterface;
use App\Interfaces\IPatientRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class PioneerPatientController extends Controller
{
    private UploadInterface $repository;
    private IPatientRepository $patientRepository;

    private $patient;

    public function __construct(
        Patient $patient
        ,   UploadInterface $repository
        ,   IPatientRepository $patientRepository
    ) {
        $this->patient = $patient;
        $this->repository = $repository;
        $this->patientRepository = $patientRepository;

        $this->middleware('permission:menu_store.clinical.pioneer_patients.index|menu_store.clinical.pioneer_patients.create|menu_store.clinical.pioneer_patients.update|menu_store.clinical.pioneer_patients.delete');
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $patientCounts = $this->patientRepository->pioneerPatientCounts();

            $breadCrumb = ['Clinical', 'Pioneer Patients'];
            return view('/stores/clinical/pioneerPatients/allFiles/index', compact('breadCrumb', 'patientCounts'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function facesheet($id, $p_id)
    {
        $user = Auth::user();
        $breadCrumb = ['Clinical', 'Pioneer Patients', 'Facesheet'];
        $breadCrumb['back'] = "/store/clinical/$id/pioneer-patients";
        $profileData = $this->profileData($p_id);
        
        return view('/stores/clinical/patients/facesheet/index', compact('user','breadCrumb', 'profileData'));
        // return view('/stores/clinical/pioneerPatients/patientData', compact('user','breadCrumb', 'profileData'));
    }

    private function profileData($id)
    {
        try {
            //code...
            $profileData = Patient::where('id',$id)->first();

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
            
            $this->patientRepository->setDataTable($request);
            $data = $this->patientRepository->getDataTable();
            
            return response()->json($data, 200);
        }
    }

    public function upload(Request $request)
    {
        try {
            if($request->ajax()){
            
                $this->repository->uploadPioneerPatient($request);
                
                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in PioneerPatientController.upload.'
            ]);
        }
    }
}
