<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CsvService
{
    public function loadPatientsCsv()
    {
       
        try {
            DB::beginTransaction();
            //code...
            $absolute_path = str_replace('\\', '/' , storage_path());

            // Create temporary table
            DB::statement("
                CREATE TEMPORARY TABLE temp_patients(
                    id TEXT, 
                    firstname TEXT, 
                    lastname TEXT, 
                    birthdate TEXT, 
                    address TEXT, 
                    city TEXT, 
                    state TEXT, 
                    zipcode TEXT, 
                    created_at TEXT, 
                    phone_number TEXT, 
                    source TEXT,
                    age TEXT, 
                    marital_status TEXT, 
                    gender TEXT, 
                    email TEXT,
                    ssn TEXT, 
                    mrn TEXT, 
                    home_phone TEXT, 
                    work_phone TEXT, 
                    prefix TEXT, 
                    suffix TEXT, 
                    referral_source TEXT, 
                    referring_provider_fullname TEXT, 
                    referring_provider_id TEXT, 
                    practice_name TEXT, 
                    practice_id TEXT, 
                    middlename TEXT, 
                    last_appointment_date TEXT, 
                    last_diagnosis TEXT, 
                    last_encounter_date TEXT, 
                    emergency_phone TEXT, 
                    emergency_name TEXT, 
                    employer_name TEXT, 
                    employment_status TEXT, 
                    default_case_id TEXT, 
                    default_case_name TEXT, 
                    default_case_payer_scenario TEXT, 
                    default_case_send_patient_statement TEXT, 
                    dcc_related_to_abuse TEXT, 
                    dcc_related_to_auto_accident TEXT, 
                    dcc_related_to_auto_accident_state TEXT, 
                    dcc_related_to_epsdt TEXT, 
                    dcc_related_to_emergency TEXT, 
                    dcc_related_to_employment TEXT, 
                    dcc_related_to_family_planning TEXT, 
                    dcc_related_to_other TEXT, 
                    dcc_related_to_pregnancy TEXT
                )

            ");

            // Load data into temporary table
            $file = $absolute_path . "/tebra_patients.csv";
            DB::statement("
                LOAD DATA INFILE '$file'
                    INTO TABLE temp_patients
                    FIELDS TERMINATED BY ','
                    ENCLOSED BY '\"'
                    ESCAPED BY '\"'
                    LINES TERMINATED BY '\n'
                    IGNORE 1 ROWS
                    (id, firstname, lastname, birthdate, age, marital_status, gender, email, address, city, state, zipcode, created_at, phone_number, source,
                    ssn, mrn, home_phone, work_phone, prefix, suffix, referral_source, referring_provider_fullname, referring_provider_id, practice_name, practice_id,
                    middlename, last_appointment_date, last_diagnosis, last_encounter_date, emergency_phone, emergency_name, employer_name, employment_status,
                    default_case_id, default_case_name, default_case_payer_scenario, default_case_send_patient_statement, dcc_related_to_abuse, dcc_related_to_auto_accident,
                    dcc_related_to_auto_accident_state, dcc_related_to_epsdt, dcc_related_to_emergency, dcc_related_to_employment, dcc_related_to_family_planning,
                    dcc_related_to_other, dcc_related_to_pregnancy)
            ");

            // Insert data from temporary table into main table
            DB::statement("
                INSERT INTO patients (tebra_id, firstname, lastname, birthdate, age, marital_status, gender, email, address, city, state, zip_code, created_at, phone_number, source, updated_at, ssn, mrn, home_phone, work_phone, prefix, suffix, referral_source, referring_provider_fullname, referring_provider_id, practice_name, practice_id, middlename, last_appointment_date, last_diagnosis, last_encounter_date, emergency_phone, emergency_name, employer_name, employment_status, default_case_id, default_case_name, default_case_payer_scenario, default_case_send_patient_statement, dcc_related_to_abuse, dcc_related_to_auto_accident, dcc_related_to_auto_accident_state, dcc_related_to_epsdt, dcc_related_to_emergency, dcc_related_to_employment, dcc_related_to_family_planning, dcc_related_to_other, dcc_related_to_pregnancy)
                SELECT id, firstname, lastname, STR_TO_DATE(birthdate, '%m/%d/%Y'), age, marital_status, gender, email, address, city, state, zipcode, STR_TO_DATE(created_at, '%m/%d/%Y %h:%i:%s %p'), phone_number, source, NOW(), ssn, mrn, home_phone, work_phone, prefix, suffix, referral_source, referring_provider_fullname, referring_provider_id, practice_name, practice_id, middlename, last_appointment_date, last_diagnosis, last_encounter_date, emergency_phone, emergency_name, employer_name, employment_status, default_case_id, default_case_name, default_case_payer_scenario, default_case_send_patient_statement, dcc_related_to_abuse, dcc_related_to_auto_accident, dcc_related_to_auto_accident_state, dcc_related_to_epsdt, dcc_related_to_emergency, dcc_related_to_employment, dcc_related_to_family_planning, dcc_related_to_other, dcc_related_to_pregnancy
                FROM temp_patients
            ");

            // Drop temporary table
            DB::statement("DROP TEMPORARY TABLE IF EXISTS temp_patients");
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack(); 
            Storage::disk('local')->append('file.txt', json_encode($e));
            $array_return = array(
                'error' => $e,
                'expand_error' => $e->getMessage()
            );
            return $array_return;
        }

    }
}