<?php

namespace App\Repositories\JotForm;

ini_set('max_execution_time', '3600');

use App\Models\Patient;
use App\Models\PatientJotForm;
use App\Repositories\API\JotFormRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class PatientRepository
{
    private $jotFormRepository;

    private $apiKey = '0af9148eeac7ee7b3e7c727e9ecd4f9c';
    private $baseUrl = 'https://hipaa-api.jotform.com';
    private $formId = '241767449261464';

    public function __construct(JotFormRepository $jotFormRepository)
    {
        $this->jotFormRepository = $jotFormRepository;
        $this->jotFormRepository->setConfiguration($this->apiKey, $this->baseUrl);
    }

    public function sync()
    {
        $pharmacy_store_id = 1;

        $submissions = $this->jotFormRepository->getFormSubmissions($this->formId);

        $patient_count = Patient::max('id');

        $res = 0;

        foreach ($submissions as $submission) {
            $answers = $submission['answers'];

            // Extract details
            $id = $submission['id'];
            $fname = $answers[82]['answer']['first'] ?? null;
            $lname = $answers[82]['answer']['last'] ?? null;
            $mname = $answers[82]['answer']['middle'] ?? null;
            if(empty($mname)) {
                $mname = null;
            }
            $suffix = $answers[82]['answer']['suffix'] ?? null;
            if(empty($suffix)) {
                $suffix = null;
            }

            $home_phone = $answers[84]['prettyFormat'] ?? null; // required in jotform - home phone
            $cellphone = $answers[85]['prettyFormat'] ?? null; // cellphone

            $email = $answers[86]['answer'] ?? null;
            $address1 = $answers[87]['answer']['addr_line1'] ?? null;
            $address2 = $answers[87]['answer']['addr_line2'] ?? null;
            $address = trim($address1.' '.$address2);
            $city = $answers[87]['answer']['city'] ?? null;
            $state = $answers[87]['answer']['state'] ?? null;
            $zip = $answers[87]['answer']['postal'] ?? null;
            $sex = $answers[95]['answer'] ?? null;

            $bdate = null;
            $bdateArr = $answers[73]['answer'] ?? [];
            if(!empty($bdateArr)) {
                $bdate = $bdateArr['year'].'-'.$bdateArr['month'].'-'.$bdateArr['day'];
            }

            $policyHolderBdate = null;
            $policyHolderBdateArr = $answers[98]['answer'] ?? [];
            if(!empty($policyHolderBdateArr)) {
                $policyHolderBdate = $policyHolderBdateArr['year'].'-'.$policyHolderBdateArr['month'].'-'.$policyHolderBdateArr['day'];
            }

            // jot form
            $jotform = [
                'uid' => $id ?? null,
                'form_id' => $submission['form_id'] ?? null,
                'ip' => $submission['ip'] ?? null,
                'jf_created_at' => $submission['created_at'] ?? null,
                'status' => $submission['status'] ?? null,
                'new' => $submission['new'] ?? null,
                'current_primary_care_provider' => $answers[77]['answer'] ?? null,
                'current_preferred_pharmacy' => $answers[88]['answer'] ?? null,
                'other_current_healthcare_providers' => $answers[89]['answer'] ?? null,
                'is_current_or_past_patient_at_ctclusi_dental_clinic' => $answers[91]['answer'] ?? null,
                'is_ctclusi_tribal_member' => $answers[97]['answer'] ?? null,
                'has_health_insurance' => $answers[93]['answer'] ?? null,
                'current_insurance_provider' => $answers[94]['answer'] ?? null,
                // new
                'is_head_of_household' => $answers[92]['answer'] ?? null,
                'insurance_policy_image_url' => $answers[96]['answer'] ?? null,
                'policy_holder_birth_date' => $policyHolderBdate,
                'school_based_health_center_affiliation' => $answers[99]['answer'] ?? null,
            ];

            $checkJotForm = PatientJotForm::where('uid', $jotform['uid'])->first();

            if(isset($checkJotForm->id) || strtoupper($jotform['status']) == 'DELETED') {
                $checkJotForm->status = 'DELETED';
                $save = $checkJotForm->save();
                if($save) {
                    Patient::where('id', $checkJotForm->patient_id)->update(['status'=>'DELETED']);
                }
                continue;
            }

            $request = [
                'firstname' => $fname,
                'lastname' => $lname,
                'birthdate' => $bdate,
            ];
            $patients = Patient::all()->filter(function ($patients) use ($request) {
                return strtolower($patients->getDecryptedFirstname()) === strtolower(trim($request['firstname']))
                    && strtolower($patients->getDecryptedLastname()) === strtolower(trim($request['lastname']))
                    && strtolower($patients->getDecryptedBirthdate()) === date('Y-m-d', strtotime(strtolower(trim($request['birthdate']))));
            });

            $patientJotForm = PatientJotForm::where('uid', $jotform['uid'])->first();

            if($patients->count() === 0) {
                $patient_count += 1;
                $patientInsert = [
                    'id'            => $patient_count,
                    'firstname'     => Crypt::encryptString($fname),
                    'middlename'    => Crypt::encryptString($mname),
                    'lastname'      => Crypt::encryptString($lname),
                    'suffix'        => Crypt::encryptString($suffix),
                    'birthdate'     => Crypt::encryptString($bdate),
                    'phone_number'  => $cellphone,
                    'home_phone'    => $home_phone,
                    'address'       => Crypt::encryptString($address),
                    'city'          => Crypt::encryptString($city),
                    'state'         => Crypt::encryptString($state),
                    'zip_code'      => trim($zip),
                    'email'         => $email,
                    'patientid'     => $id,
                    'source'        => 'jotform',
                    'gender'        => $sex,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                    'pharmacy_store_id'  => $pharmacy_store_id
                ];
                $jotform['patient_id'] = $patient_count;

                Patient::insertOrIgnore($patientInsert);
                $res++;
            } else {
                foreach($patients as $p) {
                    $jotform['patient_id'] = $p['id'];
                }
            }

            if(!isset($patientJotForm->id)) {
                PatientJotForm::insertOrIgnore($jotform);
            }
        }

        return [
            'count' => $res,
            'message' => 'Synced ('.$res.') Patients from JotForm API successfully '.date('Y-m-d H:i:s')
        ];
    }
}