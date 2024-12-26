<?php

namespace App\Repositories;

use App\Imports\ManualTesterImport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

ini_set('max_execution_time', '3600');

class ManualTesterRepository
{
    public function run()
    {
        $contents = Storage::disk('s3')->files('sms-folder/patient-masterlist');
        $count = 0;

        foreach($contents as $k => $content) {
            $file = Storage::disk('s3')->get($content);
            $name = basename($content);

            $path = 'temp/sms-folder/'.$name;

            if (!Storage::disk('local')->exists('temp/sms-folder')) {
                Storage::disk('local')->makeDirectory('temp/sms-folder');
            }
    
            $localFilePath = str_replace('\\', '/' , storage_path()."/app/".$path);
            file_put_contents($localFilePath, $file);
            
            Excel::import(new ManualTesterImport(), $localFilePath);
            $count+=1;

            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }
        }

        return 'done updating patient_id for all patients';
    }
}