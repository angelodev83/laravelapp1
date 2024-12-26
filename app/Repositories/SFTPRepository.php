<?php

namespace App\Repositories;

use App\Imports\SFTP\PioneerPatientImport;
use App\Imports\SFTP\RTSImport;
use App\Imports\RenewalImport; // Add the RenewalImport class
use App\Services\SftpService;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

ini_set('max_execution_time', '3600');

class SFTPRepository
{
    protected $sftpService;

    protected $directories = [
        'operation_rts' => '/uploads/operations/rts',
        'renewals' => '/uploads/clinical/renewals' // Add the renewals directory
    ];

    public function __construct(SftpService $sftpService)
    {
        $this->sftpService = $sftpService;
    }

    public function run()
    {
        $directories = $this->directories;

        $doneFiles = [];

        foreach($directories as $dir_key => $directory)
{
           $files = $this->sftpService->listFiles($directory);

           if ($files === false) {
            // Log the error and skip to the next directory
               echo "Error: Failed to list files in directory: $directory\n";
               continue;
    }



            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && $file !== '_done') {
                    $filePath = $directory . '/' . $file;
                    $content = $this->sftpService->readFile($filePath);

                    $path = 'temp/sftp-downloads/'.$file;
                    if (!Storage::disk('local')->exists('temp/sftp-downloads')) {
                        Storage::disk('local')->makeDirectory('temp/sftp-downloads');
                    }
                    $localFilePath = str_replace('\\', '/', storage_path()."/app/".$path);
                    file_put_contents($localFilePath, $content);

                    // Add a case for renewals
                    switch($dir_key) {
                        case 'operation_rts':
                            Excel::import(new RTSImport(), $localFilePath);
                            break;
                        case 'renewals': // Handle the renewals import
                            Excel::import(new RenewalImport(['pharmacy_store_id' => 1]), $localFilePath);
                            break;
                    }

                    if (Storage::disk('local')->exists($path)) {
                        Storage::disk('local')->delete($path);
                        $doneFiles[] = $filePath;

                        try {
                            $oldFilePath = $filePath;
                            $newFilePath = "$directory/_done/$file";

                            $this->sftpService->makeDirectory($directory.'/_done');
                            $this->sftpService->moveFile($oldFilePath, $newFilePath);
                        } catch (\Exception $e) {
                            echo "Error: " . $e->getMessage();
                        }
                    }
                }
            }

        }

        return $doneFiles;
    }
}

