<?php

namespace App\Interfaces;

use App\Interfaces\IBaseRepository;

interface IPatientRepository extends IBaseRepository
{
    public function pioneerPatientCounts();
}