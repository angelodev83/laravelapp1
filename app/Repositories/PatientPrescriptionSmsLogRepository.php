<?php

namespace App\Repositories;

use App\Imports\PatientPrescriptionSmsLogImport;
use App\Models\Patient;
use App\Models\PatientPrescriptionSmsLog;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;

ini_set('max_execution_time', '3600');

class PatientPrescriptionSmsLogRepository
{
    protected SMSRepository $smsRepository;

    private $s3Created, $s3Others;
    private $s3Sending, $s3Sent;
    
    private $statuses = [];

    private $localPath;

    public function __construct(SMSRepository $smsRepository)
    {
        $this->smsRepository = $smsRepository;

        $this->s3Created = 'sms-folder/prescriptions/created';
        $this->s3Others = 'sms-folder/prescriptions/others';

        $this->s3Sending = 'sms-folder/prescriptions/_sending';
        $this->s3Sent = 'sms-folder/prescriptions/_sent';

        $this->statuses = [
            'CREATED', 'WAITING FOR FILL', 'WAITING FOR PICK UP'
        ];

        $this->localPath = 'temp/sms-folder';
    }

    public function saveQueuing($folder = 'others')
    {
        $sourceFolder = $folder == 'others' ? $this->s3Others : $this->s3Created;
        $destinationFolder = $this->s3Sending;

        $contents = Storage::disk('s3')->files($sourceFolder);
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

            $params = [
                'folder' => $folder,
                'statuses' => $this->statuses
            ];
            
            Excel::import(new PatientPrescriptionSmsLogImport($params), $localFilePath);
            $count+=1;

            $destinationPath = str_replace($sourceFolder, $destinationFolder, $content);

            // Copy the file to the new location
            Storage::disk('s3')->copy($content, $destinationPath);

            // Delete the original file
            Storage::disk('s3')->delete($content);

            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }
        }

        return 'Done queuing saved data ('.$count.') counts and moved from folder:'.$folder.' to folder:_sending '.date('Y-m-d h:ia');
    }

    public function sendSMSAlert()
    {
        $logs = PatientPrescriptionSmsLog::with('patient')->whereNull('rc_id')->get();
        foreach($logs as $log) {
            $patient = $log->patient;
            $phone = $patient->phone_number;

            $subject = $this->smsCustomMessageByStatus($patient, $log->status);
            $arr = $this->sendSms($phone, $subject);

            if(isset($arr['message'])) {
                $message = $arr['message'];
    
                $log->rc_id = $message->id;
                $log->sms_response = json_encode($arr);
                $log->save();
            }
        }

        $sourceFolder = $this->s3Sending;
        $destinationFolder = $this->s3Sent;

        $contents = Storage::disk('s3')->files($sourceFolder);
        $count = 0;
        foreach($contents as $k => $content) {
            $destinationPath = str_replace($sourceFolder, $destinationFolder, $content);
            // Copy the file to the new location
            Storage::disk('s3')->copy($content, $destinationPath);
            // Delete the original file
            Storage::disk('s3')->delete($content);
            $count +=1;
        }

        return 'Done sending sms ('.$count.') counts to queing data and moved from folder:_sending to folder:_sent '.date('Y-m-d h:ia');
    }

    public function smsCustomMessageByStatus(Patient $patient, string $status) : string
    {
        $message = '';
        if(env('AWS_S3_PATH') != 'production') {
            $message .= "This is a TEST only for SMS AUTOMATION. ";
        }
        // $message .= 'Good day '.$patient->getDecryptedFirstname().' '.$patient->getDecryptedLastname().'. Your prescription order status is: '.strtoupper($status);

        $statusMessages = [
            'CREATED' => [ // 0 - 4
                "Hi, we've received your prescription from your prescriber. We're preparing it now and will notify you when it's ready for pickup. Thank you! – Three Rivers Pharmacy",
                "Hello, your prescription has arrived at Three Rivers Pharmacy. We'll let you know once it's ready for collection. Have a great day!", 
                "Hello, we received your prescription, and we'll notify you when it's ready for pickup. Thank you for choosing us. – Three Rivers Pharmacy",
                "Hi, just to inform you that we've got your prescription. We'll update you shortly on its status. Best, Three Rivers Pharmacy",
                "Hello, your prescription is now with us at Three Rivers Pharmacy. We'll keep you posted on when it's ready. Thanks for your trust in us!"
            ],
            'WAITING FOR FILL' => [ // 0 - 4
                "Your Rx at Three Rivers Pharmacy is being filled. We'll text you again when it's ready for pickup.", 
                "Hi, a refill for your prescription is being processed at Three Rivers Pharmacy.",
                "Hi, your prescription is being processed at Three Rivers Pharmacy. We'll let you know when it's ready for pickup.",
                "Hello, your medication at Three Rivers Pharmacy is being filled. We'll send another text when it's available for pick-up.",
                "Hi, your prescription is being prepared at Three Rivers Pharmacy. We'll let you know as soon as it's ready."
            ],
            'WAITING FOR PICK UP' => [ // 0 -2
                "Hello, your prescription at Three Rivers Pharmacy is ready for pickup.",  
                "Your prescription is ready for pickup at Three Rivers Pharmacy. We're here to answer any questions you might have.",
                "Your Rx at Three Rivers Pharmacy is filled and available for pickup today. See you soon!"
            ]
        ];

        $status = strtoupper($status);
        $getMessages = isset($statusMessages[$status]) ? $statusMessages[$status] : [];
        $countMessages = count($getMessages);
        if($countMessages > 0) {
            $random = rand(0, ($countMessages-1));
            $message .= $getMessages[$random];
        }

        return $message;
    }

    public function sendSms($phone, $subject)
    {
        $request = [
            'phone_number' => $phone,
            'subject' => $subject,
        ];
        $request = (object) $request;

        return $this->smsRepository->sendSms($request);
    }

    public function getAllFiles()
    {
        $s3Created = $this->s3Created;
        $s3Others = $this->s3Others;
        $s3Sending = $this->s3Sending;
        $s3Sent = $this->s3Sent;

        $contents = Storage::disk('s3')->files($s3Sent);

        $list = [];

        foreach($contents as $k => $content) {
            // Storage::disk('s3')->delete($content);
            // $file = Storage::disk('s3')->get($content);
            $list[] = $content;
        }
        return json_encode($list);
    }

}