<?php

namespace App\Interfaces;

interface UploadInterface
{
    public function uploadPioneerPatient($request);
    public function uploadOperationOrderFST($request);
    public function uploadOperationOrderFDT($request);
    public function uploadOperationOrderShippingLabel($request, $status_type);
    public function uploadProcurementDrugOrderItems($params);

    public function uploadOutcomes($request);

    public function uploadPharmacyStaffSchedules($request);
}