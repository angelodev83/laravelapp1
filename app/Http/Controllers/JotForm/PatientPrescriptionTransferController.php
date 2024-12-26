<?php

namespace App\Http\Controllers\JotForm;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Repositories\API\JotFormRepository;
use App\Repositories\JotForm\PatientPrescriptionTransferRepository;
use App\Repositories\PatientRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientPrescriptionTransferController extends Controller
{
    private $jotFormRepository;
    private $patientPrescriptionTransferRepository;
    private PatientRepository $patientRepository;

    public function __construct(JotFormRepository $jotFormRepository
    ,   PatientRepository $patientRepository
    ,   PatientPrescriptionTransferRepository $patientPrescriptionTransferRepository
    ) {
        $this->jotFormRepository = $jotFormRepository;
        $this->patientRepository = $patientRepository;
        $this->patientPrescriptionTransferRepository = $patientPrescriptionTransferRepository;
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $patientCounts = $this->patientRepository->sourcePatientCounts('JotForm', 'jotFormPrescriptionTransfer');

            $breadCrumb = ['Forms', 'Patient Prescription Transfers'];
            return view('/stores/patientPrescriptionTransfers/allFiles/index', compact('breadCrumb', 'patientCounts'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function syncJotForm(Request $request)
    {
        try{
            
            $res = $this->patientPrescriptionTransferRepository->sync();

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
                'message' => 'Something went wrong in PatientPrescriptionTransferController.sync.'
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
        $breadCrumb = ['Forms', 'Patient Prescription Transfers', 'Facesheet'];
        $breadCrumb['back'] = "/store/jot-form/$id/patient-prescription-transfers";
        $profileData = $this->profileData($p_id);
        
        return view('/stores/patientPrescriptionTransfers/facesheet/index', compact('user','breadCrumb', 'profileData'));
    }

    private function profileData($id)
    {
        try {
            //code...
            $profileData = Patient::with('jotFormPrescriptionTransfer')->where('id',$id)->first();

            return $profileData;
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in PatientPrescriptionTransferController.profileData.'
            ]);
        }
    }

    public function data(Request $request)
    {   
        if($request->ajax()){

            $request->merge(['mustHaveRelation' => 'jotFormPrescriptionTransfer']);
            
            $this->patientRepository->setDataTable($request);
            $data = $this->patientRepository->getDataTable();
            
            return response()->json($data, 200);
        }
    }

}
