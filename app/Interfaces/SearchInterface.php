<?php

namespace App\Interfaces;

interface SearchInterface
{
    public function searchEmployee($request);
    public function searchMedication($request);
    public function searchPatient($request);

    public function searchStoreStatus($request);
    public function searchPharmacyStaff($request);
    public function searchDrugOrder($request);
    public function searchSupplyItem($request);
    
}