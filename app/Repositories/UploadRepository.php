<?php

namespace App\Repositories;

use App\Imports\AccountReceivableImport;
use App\Imports\CollectedPaymentsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Interfaces\UploadInterface;
use App\Services\ConvertExcelFileService;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

use App\Imports\PioneerPatientsImport;
use App\Imports\OperationOrdersImport;
use App\Imports\DrugOrderItemsImportDataImport;
use App\Imports\GrossRevenueAndCogsImport;
use App\Imports\OigListImport;
use App\Imports\OutcomesImport;
use App\Imports\PharmacyStaffScheduleImport;
use App\Imports\UpdateSupplyItemsImport;
use App\Models\ImportedExcel;
use App\Models\OperationOrder;
use App\Models\File as ModelFile;

class UploadRepository implements UploadInterface
{
    private $env;
    
    public function __construct() {
        $this->env = env('AWS_S3_PATH');
    }

    public function uploadPioneerPatient($request)
    {
        $file = $request->file('upload_file');
        $pharmacy_store_id = $request->pharmacy_store_id;
        $ext = $file->getClientOriginalExtension();
        $current = file_get_contents($file);
        $batch = rand(10,100000);
        // $batch = 12644;
        $file_name = "store_clincal_pioneer_patients.".$ext;
        $save_name = str_replace('\\', '/' , storage_path())."/$file_name";
    
        file_put_contents($save_name, $current);

        $absolute_path = str_replace('\\', '/' , storage_path());

        $filePath = $absolute_path.'/'.$file_name;

        // if(strtolower($ext) == 'csv') {
            
            
        //     $loadData = 'LOAD DATA INFILE "'.$filePath.'"
        //         INTO TABLE temp_pioneer_patients
        //             FIELDS TERMINATED BY \',\'
        //             ENCLOSED BY \'"\'
        //             IGNORE 1 ROWS
        //             (rx_number,patient_full_name_last_then_first,prescriber_full_name_last_then_first,prescribed_item,dispensed_item_name,date_filled,current_transaction_status_date,current_transaction_status,date_written,expiration_date,dea_schedule,rx_status,rx_status_changed_on,written_quantity,pay_method,pricing_method,`PRIMARY`,`secondary`,origin,priority,daw,pharmacist,dispensed_item_inventory_group,primary_remit_amount,changed_on,refills_remaining,refill_number,dispensed_quantity,days_supply,dispensing_fee_submitted,dispensing_fee_paid,patient_paid_amount,acquisition_cost,gross_profit,patient_primary_address,patient_primary_city,patient_primary_state,patient_primary_zip_code,patient_primary_phone_number,patient_date_of_birth,dispensed_item_ndc,facility_name,diagnosis_icd9_code,diagnosis_disease_id,label_type,dispensed_drug_class,prescriber_dea,prescriber_primary_address,prescriber_primary_city,prescriber_primary_state,prescriber_primary_zip,prescriber_primary_phone,refill_or_new,primary_group_number,data_entry_on,primary_third_party_bin,secondary_third_party_bin,prescriber_type,primary_third_party_pcn,prescriber_npi,pharmacy_name,pharmacy_ncpdp,prescriber_specialization,primary_copay_amount,secondary_remit_amount,completed_on,prescriber_fax_number,tracking_number,shipper_name,turnaround_time_business_days,patient_email,sale_receipt_number,patient_delivery_address,patient_delivery_city,patient_delivery_state,patient_delivery_zip,patient_days_supply_ends,prescriber_state_license_number,pharmacy_npi,completed_workflow_status,turnaround_time_hours,prescriber_email);';

        //     $insertToPatients = "INSERT INTO patients 
        //         (
        //             lastname,
        //             firstname,
        //             birthdate,
        //             `address`,
        //             city,
        //             `state`,
        //             zip_code,
        //             phone_number,
        //             source,
        //             email,
        //             created_at,
        //             updated_at,
        //             pharmacy_store_id
        //         )
                
        //         SELECT 
        //             TRIM(SUBSTRING_INDEX(patient_full_name_last_then_first, ',', 1)) AS lastname, 
        //             TRIM(SUBSTRING_INDEX(patient_full_name_last_then_first, ',', -1)) AS firstname, 
        //             if(patient_date_of_birth = '', null,STR_TO_DATE(patient_date_of_birth, '%m/%d/%Y'))  as birthdate, 
        //             patient_primary_address as `address`,
        //             patient_primary_city as city,
        //             patient_primary_state as `state`,
        //             patient_primary_zip_code as zip_code,
        //             patient_primary_phone_number as phone_number,
        //             'pioneer' as source,
        //             patient_email as email,
        //             now() as created_at,
        //             now() as updated_at,
        //             '$pharmacy_store_id'
        //         FROM temp_pioneer_patients
        //         GROUP BY patient_full_name_last_then_first, patient_date_of_birth, patient_email, patient_primary_address, patient_primary_city, patient_primary_state, patient_primary_zip_code, patient_primary_phone_number, patient_email
        //         ";

 
        //     DB::statement('TRUNCATE table temp_pioneer_patients');
        //     DB::statement($loadData);
        //     DB::statement($insertToPatients);

        //     return true;
        // }

        Excel::import(new PioneerPatientsImport($pharmacy_store_id), $filePath);
        return true;
    }

    public function uploadOutcomes($request)
    {
        $file = $request->file('csvFile');
        
        $pharmacy_store_id = $request->pharmacy_store_id;
        $ext = $file->getClientOriginalExtension();
        $current = file_get_contents($file);
        $batch = rand(10,100000);
        // $batch = 12644;
        $file_name = "store_clinical_outcomes.".$ext;
        $save_name = str_replace('\\', '/' , storage_path())."/$file_name";
    
        file_put_contents($save_name, $current);

        $absolute_path = str_replace('\\', '/' , storage_path());

        $filePath = $absolute_path.'/'.$file_name;

        Excel::import(new OutcomesImport($pharmacy_store_id), $filePath);
        return true;
    }

    public function uploadOperationOrderFST($request)
    {
        $pharmacy_store_id = $request->pharmacy_store_id;
        
        $file = $request->file('upload_file');
        $ext = $file->getClientOriginalExtension();
        $current = file_get_contents($file);
        $batch = rand(10,100000);
        $file_name = "operations_for_shipping_today_batch_".$batch.'.'.$ext;
        $path = '/upload/stores/'.$pharmacy_store_id.'/operations/imported-data';
        $publicPath = public_path().$path;
        @unlink(public_path($publicPath.'/'.$file_name));
        $file->move($publicPath, $file_name);
        $save_name = str_replace('\\', '/' , $publicPath)."/$file_name";
    
        file_put_contents($save_name, $current);

        $absolute_path = str_replace('\\', '/' , $publicPath);

        $filePath = $absolute_path.'/'.$file_name;

        $fileData = [ 'path' => $path.'/'.$file_name, 'ext' => $ext, 'category' => 'operations', 'user_id' => auth()->user()->id ];
        
        $docu = new ImportedExcel();
        foreach($fileData as $key => $value) {
            $docu->$key = $value;
        }
        $save = $docu->save();

        if(!$save) {
            throw new \Exception("File data not saved to db.");
        }

        // if(strtolower($ext) == 'csv') {
            
        //     $loadData = 'LOAD DATA INFILE "'.$filePath.'"
        //         INTO TABLE operation_orders
        //             FIELDS TERMINATED BY \',\'
        //             ENCLOSED BY \'"\'
        //             IGNORE 1 ROWS
        //             (
        //                 patient_name,
        //                 dob,
        //                 address,
        //                 city,
        //                 state,
        //                 phone_number,
        //                 email,
        //                 rx_number,
        //                 tracking_number,
        //                 @shippedDate,
        //                 shipping_label
        //             )
        //             SET user_id = "'.auth()->user()->id.'"
        //                 , created_at = "'.Carbon::now().'"
        //                 , pharmacy_store_id = "'.$pharmacy_store_id.'"
        //                 , status = "For Shipping Today"
        //                 , file_id = "'.$docu->id.'"
        //                 , shipped_date = IF(@shippedDate = "", NULL, 
        //                 STR_TO_DATE(@shippedDate, "%m/%d/%Y") || 
        //                 STR_TO_DATE(@shippedDate, "%c/%e/%Y"))       
        //             ;';

        //     DB::statement($loadData);
        //     return true;
        // }
        $params = [
            'pharmacy_store_id' => $pharmacy_store_id,
            'status' => 'For Shipping Today',
            'file_id' => $docu->id
        ];
        Excel::import(new OperationOrdersImport($params), $filePath);
        return true;
    }

    public function uploadCollectedPayments($request)
    {
        $pharmacy_store_id = $request->pharmacy_store_id;
        
        $file = $request->file('upload_file');
        $ext = $file->getClientOriginalExtension();
        $current = file_get_contents($file);
        $batch = rand(10,100000);
        $file_name = "data_insights_collected_payments".$batch.'.'.$ext;
        $path = '/upload/stores/'.$pharmacy_store_id.'/data_insights/collected_payments';
        $publicPath = public_path().$path;
        @unlink(public_path($publicPath.'/'.$file_name));
        $file->move($publicPath, $file_name);
        $save_name = str_replace('\\', '/' , $publicPath)."/$file_name";
    
        file_put_contents($save_name, $current);

        $absolute_path = str_replace('\\', '/' , $publicPath);

        $filePath = $absolute_path.'/'.$file_name;

        $fileData = [ 'path' => $path.'/'.$file_name, 'ext' => $ext, 'category' => 'data_insights', 'user_id' => auth()->user()->id ];
        
        $docu = new ImportedExcel();
        foreach($fileData as $key => $value) {
            $docu->$key = $value;
        }
        $save = $docu->save();

        if(!$save) {
            throw new \Exception("File data not saved to db.");
        }

        
        $params = [
            'pharmacy_store_id' => $pharmacy_store_id,
            'file_id' => $docu->id
        ];
        Excel::import(new CollectedPaymentsImport($params), $filePath);
        return true;
    }

    public function uploadGrossRevenueAndCogs($request)
    {
        $pharmacy_store_id = $request->pharmacy_store_id;
        
        $file = $request->file('upload_file');
        $ext = $file->getClientOriginalExtension();
        $current = file_get_contents($file);
        $batch = rand(10,100000);
        $file_name = "data_insights_gross_revenue_and_cogs".$batch.'.'.$ext;
        $path = '/upload/stores/'.$pharmacy_store_id.'/data_insights/gross_revenue_and_cogs';
        $publicPath = public_path().$path;
        @unlink(public_path($publicPath.'/'.$file_name));
        $file->move($publicPath, $file_name);
        $save_name = str_replace('\\', '/' , $publicPath)."/$file_name";
    
        file_put_contents($save_name, $current);

        $absolute_path = str_replace('\\', '/' , $publicPath);

        $filePath = $absolute_path.'/'.$file_name;

        $fileData = [ 'path' => $path.'/'.$file_name, 'ext' => $ext, 'category' => 'data_insights', 'user_id' => auth()->user()->id ];
        
        $docu = new ImportedExcel();
        foreach($fileData as $key => $value) {
            $docu->$key = $value;
        }
        $save = $docu->save();

        if(!$save) {
            throw new \Exception("File data not saved to db.");
        }

        
        $params = [
            'pharmacy_store_id' => $pharmacy_store_id,
            'file_id' => $docu->id
        ];
        Excel::import(new GrossRevenueAndCogsImport($params), $filePath);
        return true;
    }

    public function uploadSupplyItems($wholesaler)
    {   
        $absolute_path = str_replace('\\', '/' , public_path('file-to-update'));

        $filePath = $absolute_path.'/'.'staples.xlsx';

        // $filePath = $absolute_path.'/'.$file_name;

        Excel::import(new UpdateSupplyItemsImport($wholesaler), $filePath);
        return true;
    }

    public function uploadAccountReceivables($request)
    {
        $pharmacy_store_id = $request->pharmacy_store_id;
        
        $file = $request->file('upload_file');
        $ext = $file->getClientOriginalExtension();
        $current = file_get_contents($file);
        $batch = rand(10,100000);
        $file_name = "data_insights_account_receivables".$batch.'.'.$ext;
        $path = '/upload/stores/'.$pharmacy_store_id.'/data_insights/account_receivables';
        $publicPath = public_path().$path;
        @unlink(public_path($publicPath.'/'.$file_name));
        $file->move($publicPath, $file_name);
        $save_name = str_replace('\\', '/' , $publicPath)."/$file_name";
    
        file_put_contents($save_name, $current);

        $absolute_path = str_replace('\\', '/' , $publicPath);

        $filePath = $absolute_path.'/'.$file_name;

        $fileData = [ 'path' => $path.'/'.$file_name, 'ext' => $ext, 'category' => 'data_insights', 'user_id' => auth()->user()->id ];
        
        $docu = new ImportedExcel();
        foreach($fileData as $key => $value) {
            $docu->$key = $value;
        }
        $save = $docu->save();

        if(!$save) {
            throw new \Exception("File data not saved to db.");
        }

        
        $params = [
            'pharmacy_store_id' => $pharmacy_store_id,
            'file_id' => $docu->id
        ];
        Excel::import(new AccountReceivableImport($params), $filePath);
        return true;
    }

    public function uploadOperationOrderFDT($request)
    {
        $pharmacy_store_id = $request->pharmacy_store_id;
        
        $file = $request->file('upload_file');
        $ext = $file->getClientOriginalExtension();
        $current = file_get_contents($file);
        $batch = rand(10,100000);
        $file_name = "operations_for_delivery_today_batch_".$batch.'.'.$ext;
        $path = '/upload/stores/'.$pharmacy_store_id.'/operations/imported-data';
        $publicPath = public_path().$path;
        @unlink(public_path($publicPath.'/'.$file_name));
        $file->move($publicPath, $file_name);
        $save_name = str_replace('\\', '/' , $publicPath)."/$file_name";
    
        file_put_contents($save_name, $current);

        $absolute_path = str_replace('\\', '/' , $publicPath);

        $filePath = $absolute_path.'/'.$file_name;

        $fileData = [ 'path' => $path.'/'.$file_name, 'ext' => $ext, 'category' => 'operations', 'user_id' => auth()->user()->id ];
        
        $docu = new ImportedExcel();
        foreach($fileData as $key => $value) {
            $docu->$key = $value;
        }
        $save = $docu->save();

        if(!$save) {
            throw new \Exception("File data not saved to db.");
        }

        
        $params = [
            'pharmacy_store_id' => $pharmacy_store_id,
            'status' => 'For Delivery Today',
            'file_id' => $docu->id
        ];
        Excel::import(new OperationOrdersImport($params), $filePath);
        return true;
    }

    public function uploadOperationOrderShippingLabel($request, $status_type)
    {   
        $id = $request->id;
        $pharmacy_store_id = $request->pharmacy_store_id;

        $ship_by_date = isset($request->ship_by_date) ? $request->ship_by_date : null;
        
        if(empty($id) && !empty($ship_by_date)) {
            $files = $request->file('upload_bulk_fst_shipping_label_files');

            foreach($files as $file) {
                // $tracking_number = strtoupper(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                // $order = OperationOrder::where(DB::raw('UPPER(tracking_number)'), $tracking_number)->first();
                $orders = OperationOrder::where('ship_by_date', $ship_by_date)->get();

                foreach($orders as $order) {
                    if(!empty($order)) {
                        $this->saveUploadFSTShippingLabel($order, $file, $order->id, $pharmacy_store_id, $status_type);
                    }
                }

            }

            return;
        }

        $file = $request->file('upload_single_any_file');

        $order = OperationOrder::where('id',$id)->first();

        if(!empty($order)) {
            $this->saveUploadFSTShippingLabel($order, $file, $id, $pharmacy_store_id, $status_type);
        }

        return [];
    }

    private function saveUploadFSTShippingLabel($order, $file, $id, $pharmacy_store_id, $status_type)
    {   
        if(isset($order->id)) {
            if(!empty($order->file_id)) {
                $modelFile = ModelFile::where('id', $order->file_id)->first();
                $modelPath = $modelFile->path.$modelFile->filename;
                if($modelPath != ''){
                    if(Storage::disk('s3')->exists($modelPath)) {
                        Storage::disk('s3')->delete($modelPath);
                    }
                    $modelFile->delete();   
                }
            }
    
            $ext = $file->getClientOriginalExtension();
            $mime_type = $file->getMimeType();
            
            $tracking_number = strtoupper($order->tracking_number);
            // $filename = $file->getClientOriginalName();
            $filename = $tracking_number.'.'.$ext;
            
            $env = $this->env;
            if($status_type == 'For Delivery Today'){
                $path = "$env/stores/$pharmacy_store_id/operations/for-delivery-today/";
            }

            if($status_type == 'For Shipping Today'){
                $path = "$env/stores/$pharmacy_store_id/operations/for-shipping-today/";
            }
    
            // Provide a dynamic path or use a specific directory in your S3 bucket
            $path_file = $path . $filename;
    
            // Store the file in S3
            Storage::disk('s3')->put($path_file, file_get_contents($file));
    
            // Optionally, get the URL of the uploaded file
            $s3url = Storage::disk('s3')->url($path_file);
    
            $modelFile = new ModelFile();
            $modelFile->filename = $filename;
            $modelFile->path = $path;
            $modelFile->mime_type = $mime_type;
            $modelFile->document_type = $ext;
            $modelFile->save();
    
            $order->file_id = $modelFile->id;
            $order->save();
        }

    }


    public function uploadProcurementDrugOrderItems($params)
    {
        $ext = $params['ext'];
        $filePath = $params['filePath'];
        $pharmacy_store_id = $params['pharmacy_store_id'];

        $importExcelParams = $params['importExcelParams'];

        $wholesaler_id = $importExcelParams['wholesaler_id'] ?? 6;

        $mapDrugOrderExcelData = $this->mapDrugOrderExcelData($wholesaler_id);
        $importExcelParams = array_merge($importExcelParams, $mapDrugOrderExcelData);

        Excel::import(new DrugOrderItemsImportDataImport($importExcelParams), $filePath);
        return false;
    }

    private function mapDrugOrderExcelData($wholesaler_id)
    {
        // default
        $start_to_line = 1;
        $requiredRows = [2];
        $excelRows = [
            'po_number' => 0
            , 'ndc' => 1
            , 'product_description' => 2
            , 'abc_selling_size'    => 3
            , 'drug_form_pack_size' => 4
            , 'quantity_ordered'    => 5
            , 'quantity_confirmed'  => 6
            , 'quantity_shipped'    => 7
            , 'prevent_substitution_indicator'  => 8
            , 'shc_code'    => 9
            , 'department_code' => 10
            , 'gl_code' => 11
            , 'contract_number' => 12
            , 'acq_cost'    => 13
            , 'awp' => 14
            , 'retail_price'    => 15
            , 'retail_price_override'   => 16
            , 'temp_retail_price_override_indicator'    => 17
            , 'invoice_number'  => 18
            , 'invoice_date'    => 19
        ];
        // cardinal health
        if($wholesaler_id == 6) {
            $start_to_line = 18;
            $requiredRows = [19];
            $excelRows = [
                'ndc' => 7
                , 'product_description' => 19
                , 'quantity_ordered'    => 0
                , 'quantity_shipped'    => 2
                , 'acq_cost'    => 15
                , 'contract_number' => 12
                , 'invoice_number'  => 16
                , 'gl_code' => 20
                , 'expected_quantity_shipped' => 1
            ];
        }

        // McKesson
        if($wholesaler_id == 1) {
            $start_to_line = 2;
            $requiredRows = [79];
            $excelRows = [
                'product_description'   => 79,
                'quantity_ordered'  => 36,
                'expected_quantity_shipped' => 1,
                'acq_cost'  => 4,
                'ndc'   => 22
            ];
        }
        return ['excelRows' => $excelRows, 'start_to_line' => $start_to_line, 'requiredRows' => $requiredRows];
    }

    public function uploadPharmacyStaffSchedules($request)
    {
        $file = $request->file('upload_file');
        $pharmacy_store_id = $request->pharmacy_store_id;
        $ext = $file->getClientOriginalExtension();
        $current = file_get_contents($file);
        $batch = rand(10,100000);
        // $batch = 12644;
        $file_name = "pharmacy_staff_schedules.".$ext;
        $save_name = str_replace('\\', '/' , storage_path())."/$file_name";
    
        file_put_contents($save_name, $current);

        $absolute_path = str_replace('\\', '/' , storage_path());

        $filePath = $absolute_path.'/'.$file_name;

        Excel::import(new PharmacyStaffScheduleImport($pharmacy_store_id), $filePath);
        return true;
    }

}