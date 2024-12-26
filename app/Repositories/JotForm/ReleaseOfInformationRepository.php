<?php

namespace App\Repositories\JotForm;

ini_set('max_execution_time', '3600');

use App\Models\ReleaseOfInformation;
use App\Repositories\API\JotFormRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class ReleaseOfInformationRepository
{
    private $jotFormRepository;

    private $apiKey = '0af9148eeac7ee7b3e7c727e9ecd4f9c';
    private $baseUrl = 'https://hipaa-api.jotform.com';
    private $formId = '241759116016454';

    public function __construct(JotFormRepository $jotFormRepository)
    {
        $this->jotFormRepository = $jotFormRepository;
        $this->jotFormRepository->setConfiguration($this->apiKey, $this->baseUrl);
    }

    public function sync()
    {
        $pharmacy_store_id = 1;

        $submissions = $this->jotFormRepository->getFormSubmissions($this->formId);

        $res = 0;

        foreach($submissions as $submission) {
            $s = $submission['answers'];
            $jot_form_uid = $submission['id'];
            $jot_form_id = $submission['form_id'];
            $jot_form_ip = $submission['ip'];
            $jot_form_created_at = $submission['created_at'];
            $jot_form_status = $submission['status'];

            if(strtoupper($jot_form_status) == 'DELETED') {
                continue;
            }


            $hereby_authorize_person = $s[47]['answer'] ?? null;
            $hereby_authorize_person_name = $s[4]['answer'] ?? null;
            if(empty($hereby_authorize_person_name)) {
                $hereby_authorize_person_name = $hereby_authorize_person;
            }
            $hereby_authorize_person_address = $s[49]['answer'] ?? null;
            $hereby_authorize_person_phone_number = $s[23]['prettyFormat'] ?? null;
            $hereby_authorize_person_fax_number = $s[55]['prettyFormat'] ?? null;

            $to_person = $s[64]['answer'] ?? null;
            $to_person_name = $s[60]['answer'] ?? null;
            if(empty($to_person_name)) {
                $to_person_name = $to_person;
            }
            $to_person_address = $s[61]['answer'] ?? null;
            $to_person_phone_number = $s[62]['prettyFormat'] ?? null;
            $to_person_fax_number = $s[63]['prettyFormat'] ?? null;

            $checkboxes = $s[65]['answer'] ?? [];
            $information_to_data = json_encode($checkboxes);
            
            $purpose = $s[66]['answer'] ?? null;
            $patient_firstname = $s[72]['answer']['first'] ?? null;
            $patient_lastname = $s[72]['answer']['last'] ?? null;
            $relationship_to_patient = $s[77]['answer'] ?? null;
            
            $expirationDateAnswer = $s[68]['answer'] ?? null;
            $expiration_date = null;
            if(!empty($expirationDateAnswer)) {
                $expiration_date = $expirationDateAnswer['year'].'-'.$expirationDateAnswer['month'].'-'.$expirationDateAnswer['day'];
            }
            $patientBirthdateAnswer = $s[73]['answer'] ?? null;
            $patient_birth_date = null;
            if(!empty($patientBirthdateAnswer)) {
                $patient_birth_date = $patientBirthdateAnswer['year'].'-'.$patientBirthdateAnswer['month'].'-'.$patientBirthdateAnswer['day'];
            }
            $signedDateAnswer = $s[75]['answer'] ?? null;
            $signed_date = null;
            if(!empty($signedDateAnswer)) {
                $signed_date = $signedDateAnswer['year'].'-'.$signedDateAnswer['month'].'-'.$signedDateAnswer['day'];
            }

            $check = ReleaseOfInformation::where('jot_form_uid', $jot_form_uid)->first();

            if(!isset($check->id) && (!empty($patient_firstname)) && !empty($patient_lastname)) {
                $save = ReleaseOfInformation::insertOrIgnore([
                    'jot_form_uid'  => $jot_form_uid,
                    'jot_form_id'   => $jot_form_id,
                    'jot_form_ip'   => $jot_form_ip,
                    'jot_form_created_at'   => $jot_form_created_at,
                    'jot_form_status'   => $jot_form_status,
                    'hereby_authorize_person'   => $hereby_authorize_person,
                    'hereby_authorize_person_name'  => $hereby_authorize_person_name,
                    'hereby_authorize_person_address'   => $hereby_authorize_person_address,
                    'hereby_authorize_person_phone_number'  => $hereby_authorize_person_phone_number,
                    'hereby_authorize_person_fax_number'    => $hereby_authorize_person_fax_number,
                    'to_person' => $to_person,
                    'to_person_name'    => $to_person_name,
                    'to_person_address' => $to_person_address,
                    'to_person_phone_number'    => $to_person_phone_number,
                    'to_person_fax_number'  => $to_person_fax_number,
                    'information_to_data'   => $information_to_data,
                    'purpose'   => $purpose,
                    'expiration_date'   => $expiration_date,
                    'patient_firstname' => Crypt::encryptString($patient_firstname),
                    'patient_lastname' => Crypt::encryptString($patient_lastname),
                    'patient_birth_date'    => Crypt::encryptString($patient_birth_date),
                    'signed_date'   => $signed_date,
                    'relationship_to_patient'   => $relationship_to_patient,
                    'pharmacy_store_id'   => $pharmacy_store_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                $res+=1;
            }
        }

        return [
            'count' => $res,
            'message' => 'Synced ('.$res.') Release Of Information from JotForm API successfully '.date('Y-m-d H:i:s')
        ];
    }
}