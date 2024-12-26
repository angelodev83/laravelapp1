<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ReleaseOfInformation extends Model
{
    use HasFactory;

    public function getDecryptedPatientFirstname(): string
    {
        return Crypt::decryptString($this->patient_firstname);
    }

    public function getDecryptedPatientLastname(): string
    {
        return Crypt::decryptString($this->patient_lastname);
    }

    public function getDecryptedPatientBirthDate(): string
    {
        return date('Y-m-d', strtotime(Crypt::decryptString($this->patient_birth_date)));
    }
}
