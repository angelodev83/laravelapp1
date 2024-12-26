<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SFTP;
use phpseclib3\Exception\UnableToConnectException;

class SftpService
{
    protected $sftp;

    public function __construct()
    {
        $host = env('SFTP_HOST');
        $port = env('SFTP_PORT', 22);
        $username = env('SFTP_USERNAME');
        $password = env('SFTP_PASSWORD');

        $this->sftp = new SFTP($host, $port);

        if (!$this->sftp->login($username, $password)) {
            throw new UnableToConnectException('Login failed');
        }
    }

    public function listFiles($directory = '.')
    {
        return $this->sftp->nlist($directory);
    }

    public function readFile($filePath)
    {
        return $this->sftp->get($filePath);
    }

    public function downloadFile($remoteFilePath, $localFilePath)
    {
        Log::info("Attempting to download file from SFTP", ['remoteFilePath' => $remoteFilePath, 'localFilePath' => $localFilePath]);

        try {
            $success = $this->sftp->get($remoteFilePath, $localFilePath);

            if (!$success) {
                Log::error('Failed to download file from SFTP', ['remoteFilePath' => $remoteFilePath, 'localFilePath' => $localFilePath]);
                throw new \Exception('Failed to download file from SFTP');
            }

            Log::info('File downloaded successfully', ['localFilePath' => $localFilePath]);

        } catch (\Exception $e) {
            Log::error('Error downloading file from SFTP', ['exception' => $e->getMessage()]);
            throw $e; // Re-throw the exception after logging it
        }
    }

    public function readFilesInDirectory($directory)
    {
        $files = $this->listFiles($directory);
        $fileContents = [];

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $directory . '/' . $file;
                $content = $this->readFile($filePath);
                $fileContents[$file] = $content;
            }
        }

        return $fileContents;
    }

    public function moveFile($oldFilePath, $newFilePath)
    {
        if (!$this->sftp->rename($oldFilePath, $newFilePath)) {
            throw new \Exception('Failed to move file on SFTP');
        }
    }

    public function makeDirectory($directoryPath)
    {
        if (!$this->sftp->file_exists($directoryPath)) {
            if (!$this->sftp->mkdir($directoryPath, 0777, true)) {
                throw new \Exception('Failed to create directory on SFTP');
            }
        }
    }

}
