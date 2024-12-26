<?php

namespace App\Models\Traits;

use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

trait PatientEncryptions
{
    public function getDecryptedFirstname(): string
    {
        return Crypt::decryptString($this->firstname);
    }

    public function getDecryptedMiddlename(): string
    {
        return Crypt::decryptString($this->middlename);
    }

    public function getDecryptedSuffix(): string
    {
        return Crypt::decryptString($this->suffix);
    }

    public function getDecryptedLastname(): string
    {
        return Crypt::decryptString($this->lastname);
    }

    public function getDecryptedBirthdate(): string
    {
        return date('Y-m-d', strtotime(Crypt::decryptString($this->birthdate)));
    }

    public function getDecryptedAddress(): string
    {
        return Crypt::decryptString($this->address);
    }

    public function getDecryptedCity(): string
    {
        return Crypt::decryptString($this->city);
    }

    public function getDecryptedState(): string
    {
        return Crypt::decryptString($this->state);
    }

}