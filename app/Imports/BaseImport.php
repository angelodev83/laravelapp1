<?php

namespace App\Imports;

use App\Models\Patient;
use Carbon\Carbon;
use DateTime;

class BaseImport
{
    protected function getCurrentPSTDate($format = 'Y-m-d', $date = null)
    {

        if(!empty($date)) {
            $pst = Carbon::createFromFormat('Y-m-d', $date);
            $pst = $pst->setTimezone('America/Los_Angeles');
        }else {
            $pst = Carbon::now('America/Los_Angeles');
        }
        
        return $pst->format($format);
    }

    protected function resolveDate($date, $original_format = 'm/d/Y')
    {
        if(!empty($date)) {
            if(is_numeric($date)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
            } else {
                $dateString = DateTime::createFromFormat($original_format, $date);
                $errors = DateTime::getLastErrors();
                if ($errors['warning_count'] === 0 && $errors['error_count'] === 0) {
                    $date = $dateString->format('Y-m-d');
                } else {
                    $date = null;
                }
            }
        } else {
            $date = null;
        }
        return $date;
    }

    protected function resolveFloatNumber($amount)
    {
        $amount = preg_replace('/[^\d.-]/', '', $amount);
        
        if (preg_match('/^-?\d*(\.\d+)?$/', $amount)) { 
            $amount = (float) $amount;
        }

        if ($amount === '') {
            $amount = null;
        }
        
        return $amount;
    }

    protected function getPatientIdByFirstnameLastname($patient_name)
    {
        $patient_name = trim($patient_name);
        $patient_name = str_replace('  ', ' ',$patient_name);

        $patients = Patient::all()->filter(function ($patients) use ($patient_name) {
            return strtolower($patients->getDecryptedFirstname().' '.$patients->getDecryptedLastname()) === strtolower($patient_name);
        });
        $patient_id = null;
        if($patients->count() > 0) {
            foreach($patients as $p) {
                $patient_id = $p->id;
            }
        }
        return $patient_id;
    }


}
