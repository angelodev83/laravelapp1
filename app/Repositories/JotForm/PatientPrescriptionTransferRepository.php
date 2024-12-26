<?php

namespace App\Repositories\JotForm;

ini_set('max_execution_time', '3600');

use App\Models\Patient;
use App\Models\PatientJotFormPrescriptionTransfer;
use App\Repositories\API\JotFormRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class PatientPrescriptionTransferRepository
{
    private $jotFormRepository;

    private $apiKey = '0af9148eeac7ee7b3e7c727e9ecd4f9c';
    private $baseUrl = 'https://hipaa-api.jotform.com';
    private $formId = '241555526435458';

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

        $patient_count = Patient::max('id');

        foreach($submissions as $submission) {
            $answers = $submission['answers'];

            $jot_form_uid = $submission['id'] ?? null;
            $jot_form_id = $submission['form_id'] ?? null;
            $jot_form_ip = $submission['ip'] ?? null;
            $jot_form_created_at = $submission['created_at'] ?? null;
            $jot_form_status = $submission['status'] ?? 'ACTIVE';
            $jot_form_new = $submission['new'] ?? null;

            if(strtoupper($jot_form_status) == 'DELETED') {
                continue;
            }

            $fname = $answers[3]['answer']['first'] ?? null;
            $lname = $answers[3]['answer']['last'] ?? null;
            $sex = $answers[4]['answer'] ?? null;

            $bdate = null;
            $bdateArr = $answers[6]['answer'] ?? [];
            if(!empty($bdateArr)) {
                $bdate = $bdateArr['year'].'-'.$bdateArr['month'].'-'.$bdateArr['day'];
            }
            $address1 = $answers[7]['answer']['addr_line1'] ?? null;
            $address2 = $answers[7]['answer']['addr_line2'] ?? null;
            $address = trim($address1.' '.$address2);
            $city = $answers[7]['answer']['city'] ?? null;
            $state = $answers[7]['answer']['state'] ?? null;
            $zip = $answers[7]['answer']['postal'] ?? null;

            $phone_number = $answers[8]['prettyFormat'] ?? null;
            $email = $answers[9]['answer'] ?? null;

            $group_affiliated = $answers[13]['answer'] ?? null;
            $preferred_form_of_communication = $answers[14]['answer'] ?? null;
            $current_pharmacy = $answers[17]['answer'] ?? null;
            $current_pharmacy_phone_number = $answers[18]['answer'] ?? null;

            $current_pharmacy_address1 = $answers[19]['answer']['addr_line1'] ?? null;
            $current_pharmacy_address2 = $answers[19]['answer']['addr_line2'] ?? null;
            $current_pharmacy_city = $answers[19]['answer']['city'] ?? null;
            $current_pharmacy_state = $answers[19]['answer']['state'] ?? null;
            $current_pharmacy_zip = $answers[19]['answer']['postal'] ?? null;

            $prescriber_firstname = $answers[22]['answer']['first'] ?? null;
            $prescriber_lastname = $answers[22]['answer']['last'] ?? null;
            $prescriber_phone_number = $answers[23]['prettyFormat'] ?? null;
            $prescriber_fax_number = $answers[24]['prettyFormat'] ?? null;

            $medication_drug_name = $answers[28]['answer'] ?? null;
            $medication_strength = $answers[29]['answer'] ?? null;


            // jot form
            $jotform = [
                'uid' => $jot_form_uid,
                'form_id' => $jot_form_id,
                'ip' => $jot_form_ip,
                'jf_created_at' => $jot_form_created_at,
                'status' => $jot_form_status,
                'new' => $jot_form_new,
                
                'group_affiliated'  => $group_affiliated,
                'preferred_form_of_communication'   => $preferred_form_of_communication,
                'current_pharmacy'  => $current_pharmacy,
                'current_pharmacy_phone_number' => $current_pharmacy_phone_number,
                'current_pharmacy_address'  => $current_pharmacy_address1,
                'current_pharmacy_address2' => $current_pharmacy_address2,
                'current_pharmacy_city' => $current_pharmacy_city,
                'current_pharmacy_state'    => $current_pharmacy_state,
                'current_pharmacy_zip'  => $current_pharmacy_zip,
                'prescriber_firstname'  => $prescriber_firstname,
                'prescriber_lastname'   => $prescriber_lastname,
                'prescriber_phone_number'   => $prescriber_phone_number,
                'prescriber_fax_number' => $prescriber_fax_number,
                'medication_drug_name'  => $medication_drug_name,
                'medication_strength'   => $medication_strength
            ];


            $checkJotForm = PatientJotFormPrescriptionTransfer::where('uid', $jotform['uid'])->first();

            if(isset($checkJotForm->id) || strtoupper($jotform['status']) == 'DELETED') {
                $checkJotForm->status = 'DELETED';
                $save = $checkJotForm->save();
                if($save) {
                    Patient::where('id', $checkJotForm->patient_id)->update(['status'=>'DELETED']);
                }
                continue;
            }

            if(empty($fname) || empty($lname)) {
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

            if($patients->count() === 0) {
                $patient_count += 1;
                
                $patientInsert = [
                    'id'            => $patient_count,
                    'firstname'     => Crypt::encryptString($fname),
                    'lastname'      => Crypt::encryptString($lname),
                    'birthdate'     => Crypt::encryptString($bdate),
                    'phone_number'  => $phone_number,
                    'address'       => Crypt::encryptString($address),
                    'city'          => Crypt::encryptString($city),
                    'state'         => Crypt::encryptString($state),
                    'zip_code'      => trim($zip),
                    'email'         => $email,
                    'patientid'     => $jot_form_uid,
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

            PatientJotFormPrescriptionTransfer::insertOrIgnore($jotform);

        }

        return [
            'count' => $res,
            'message' => 'Synced ('.$res.') Release Of Information from JotForm API successfully '.date('Y-m-d H:i:s')
        ];
    }
}