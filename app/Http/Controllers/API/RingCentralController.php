<?php

namespace App\Http\Controllers\API;

ini_set('max_execution_time', '3600');

use App\Http\Controllers\Controller;
use App\Models\RcSmsRecord;
use App\Models\RcSyncInfo;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RingCentral\SDK\SDK;

class RingCentralController extends Controller
{
    protected $platform;

    public function __construct(SDK $rcsdk)
    {
        $this->platform = $rcsdk->platform();
    }

    public function test_messaging()
    {
        $sendSmsResponse = $this->prepareSms("(415) 233-7104");//$this->prepareSms("9264373620");

        return $sendSmsResponse;
    }

    public function sendSms(Request $request)
    {   
        // $phone_number = "(415) 979-2253";
        // $sendSmsResponse = $this->prepareSms($phone_number);

        // list($headers, $json) = explode("\r\n\r\n", $sendSmsResponse, 2);
        // $data = json_decode($json, true);

        // // Check if decoding was successful and "status" exists
        // if ($data !== null && isset($data['status'])) {
        //     if($data['status'] == "success"){
        //         sleep(3);
        //         $response = $this->sync();
        //     }
        // } 
        
        $sendSmsResponse = $this->prepareSms($request->phone_number, $request->subject);//$this->prepareSms("9264373620");

        return $sendSmsResponse;
    }

    private function convertPhoneNumber($phone_number)
    {
        $phoneNumber = $phone_number;//"(206) 775-6500";

        // Remove everything inside parentheses, dashes, and whitespace characters
        $cleanedPhoneNumber = preg_replace('/[\s()-]+/', '', $phoneNumber);

        $convertedPhoneNumber = "+1".$cleanedPhoneNumber;

        return $convertedPhoneNumber;
    }

    private function getConversationId($phone_number)
    {
        $conversationId = RcSmsRecord::select('conversation_id')
            ->where('to_phonenumber', $phone_number)
            ->where('direction', 'Outbound')
            ->groupBy('conversation_id')
            ->first();

        return $conversationId;
    }

    public function getList2($phone, $page)
    {
        $convertedPhoneNumber = $this->convertPhoneNumber($phone);
        $record = $this->getConversationId($convertedPhoneNumber);
        
        try {
            if ($record) {
                $accountId = '~';
                $extensionId = '~';
                $queryParams = array(
                    'availability' => array( 'Alive' ),
                    'conversationId' => $record->conversation_id,
                    'dateFrom' => '2016-03-10T18:07:52.534Z',
                    //'dateTo' => '<ENTER VALUE>',
                    'direction' => array( 'Inbound', 'Outbound' ),
                    //'distinctConversations' => true,
                    'messageType' => array( 'SMS' ),
                    //'readStatus' => array(  ),
                    'page' => $page,
                    'perPage' => 10,
                    //'phoneNumber' => $convertedPhoneNumber,//'+14152337104'
                );
                $this->jwtLogin();
                $response = $this->platform->get("/restapi/v1.0/account/{$accountId}/extension/{$extensionId}/message-store", $queryParams);
                
                return response()->json($response->json());
            }
            else{
                return response()->json(['message' => 'No conversation!']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }   
    }

    public function getList($phone, $page)
    {
        $convertedPhoneNumber = $this->convertPhoneNumber($phone);
        $record = $this->getConversationId($convertedPhoneNumber);
        
        try {
            if ($record) {
                $r = $this->unreadStatus($record->conversation_id);
                if(count($r) > 0){
                    $this->readStatusChange($r);
                    
                    $syncResponse = $this->sync();
                    $attempts = 0; // Initialize attempt counter

                    while ($attempts < 5) {
                        if (empty($syncResponse->records)) {
                            // Attempt to sync
                            $syncResponse = $this->sync();
                            sleep(1); // Sleep for 1 second before next attempt
                            $attempts++; // Increment the attempt counter
                        } else {
                            break;
                        }
                    }

                    if ($attempts >= 5) {
                        // Maximum attempts reached, return an error
                        return response()->json(['error' => 'Maximum attempts reached.']);
                    }
                }
                $response = RcSmsRecord::with('user.employee')
                    ->where('conversation_id', $record->conversation_id)
                    ->orderBy('creation_time', 'desc')
                    ->paginate(10, ['*'], 'page', $page);
                
                return response()->json($response);
            }
            else{
                return response()->json(['message' => 'No conversation!']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }   
    }

    private function unreadStatus($conversation_id)
    {
        try {
            $r = RcSmsRecord::where('conversation_id', $conversation_id)
                ->where('read_status', 'Unread')
                ->where('direction','Inbound')
                ->get();
            $records = $r->map->getAttributes()->all();
            $count = count($records);
            
            return $records;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } 
    }

    private function readStatusChange($records)
    {
        try {
            $this->jwtLogin();
            foreach ($records as $record){
                $messageId = $record['rc_id'];
                $params['readStatus'] = "Read";
                $r = $this->platform->put("/account/~/extension/~/message-store/{$messageId}", $params);
                //dd($r); // Check the response
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function prepareSms($client_phonenumber, $subject)
    {
        try {
            //$this->platform->login(['jwt' => env('RC_JWT')]);
            $this->jwtLogin();
            $phoneNumber = $this->getUserPhoneNumberWithSmsCapability();
            if ($phoneNumber) {
                $to = $this->convertPhoneNumber($client_phonenumber);
                $response = $this->sendSmsFromPhoneNumber($phoneNumber,$to, $subject);
                
                if($response['status'] == 'success'){
                    $response = $this->check_message_status($response['data']->id);
        
                    if($response['message']->messageStatus == 'Delivered'){
                        $syncResponse = $this->sync();
                        $attempts = 0; // Initialize attempt counter

                        while ($attempts < 20) {
                            if (empty($syncResponse->records)) {
                                // Attempt to sync
                                $syncResponse = $this->sync();
                                sleep(1); // Sleep for 1 second before next attempt
                                $attempts++; // Increment the attempt counter
                            } else {
                                $sms = RcSmsRecord::where('rc_id', $response['message']->id)->first();
                                $sms->user_id = Auth::id();
                                $sms->save();
                                break;
                            }
                        }

                        if ($attempts >= 20) {
                            // Maximum attempts reached, return an error
                            return response()->json(['error' => 'Maximum attempts reached.']);
                        }
                        // while (empty($syncResponse->records)) {
                        //     //Storage::disk('local')->append('file.txt', json_encode(1));
                        //     $syncResponse = $this->sync();
                        //     sleep(1);
                        // }
                    }
                    // Return the final response after records are not empty
                    return $response;
                }
                else{
                    return $response;
                }
                
            } else {
                return "None of the user's phone numbers have the SMS capability.";
            }
        } catch (\RingCentral\SDK\Http\ApiException $e) {
            return "Unable to authenticate to platform. Check credentials.";
        }
    }

    protected function getUserPhoneNumberWithSmsCapability()
    {
        $endpoint = "/restapi/v1.0/account/~/extension/~/phone-number";
        $resp = $this->platform->get($endpoint);
        $phoneNumbers = $resp->json()->records;

        foreach ($phoneNumbers as $phoneNumber) {
            foreach ($phoneNumber->features as $feature) {
                if ($feature == "SmsSender") {
                    return $phoneNumber->phoneNumber;
                }
            }
        }

        return null;
    }

    protected function sendSmsFromPhoneNumber($fromNumber, $to, $subject)
    {
        $recipient = $to; //env('SMS_RECIPIENT');//pjno.//'+19542987882';

        try {
            $requestBody = [
                'from' => ['phoneNumber' => $fromNumber],
                'to' => [['phoneNumber' => $recipient]],
                //'text' => 'This is a test SMS message sent from the RingCentral SMS API code sample - Mark',
                //'text' => "Good day, This is a test SMS from MGMT88 Intranet's SMS API",
                'text' => $subject
            ];

            $endpoint = "/account/~/extension/~/sms";
            $resp = $this->platform->post($endpoint, $requestBody);
            $jsonObj = $resp->json();
           
            return [
                "status" => "success",
                "data" => $jsonObj
            ];
            //return "SMS sent. Message id: " . $jsonObj->id;
        } catch (\RingCentral\SDK\Http\ApiException $e) {
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    private function check_message_status($messageId)
    {
        try {
            $endpoint = "/restapi/v1.0/account/~/extension/~/message-store/".$messageId;
            $resp = $this->platform->get($endpoint);
            $jsonObj = $resp->json();
            
            if ($jsonObj->messageStatus == "Queued"){
                sleep(2);
                return $this->check_message_status($jsonObj->id);
            }
            else{
                //return $jsonObj->messageStatus;
                //record push sms
                return [
                    'status' => 'success',
                    'message' => $resp->json()
                ];
            }            
            
        } catch (\RingCentral\SDK\Http\ApiException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function sync()
    {
        $recordsCount = RcSmsRecord::count();

        if($recordsCount > 0){
            $lastSync = RcSyncInfo::orderBy('updated_at', 'desc')->first();
            $response = $this->iSync($lastSync->sync_token);
        }
        else{
            $response = $this->fullSync();
        }

        return $response;
    }

    private function dbSync($data)
    {   
        $jsonString = $data;
        if (!is_string($jsonString)) {
            $jsonString = json_encode($jsonString);
        }
        $decodedJson = json_decode($jsonString, true);
        
        if (isset($decodedJson['records'])) {
            $recordCount = count($decodedJson['records']);

            if ($recordCount > 0) {
                $reverseRecords = array_reverse($data->records);
                foreach ($reverseRecords as $val) {
                    $recordCount = RcSmsRecord::where('rc_id', $val->id)->count();
                    $updatedRecord = RcSmsRecord::where('rc_id', $val->id)->first();
                    if ($recordCount > 0) {
                        $updatedRecord->read_status = ($val->readStatus)?$val->readStatus:null;
                        $updatedRecord->subject = ($val->subject)?$val->subject:null;
                        $updatedRecord->last_modified_time = ($val->lastModifiedTime)?$val->lastModifiedTime:null;
                        $updatedRecord->save();
                    }
                    else{
                        $records = new RcSmsRecord();
                        $records->uri = $val->uri;
                        $records->rc_id = $val->id;
                        $records->to_phonenumber = $val->to[0]->phoneNumber;
                        $records->to_name = $val->to[0]->name ?? null;
                        $records->to_location = $val->to[0]->location ?? null;
                        $records->from_phonenumber = $val->from->phoneNumber;
                        $records->from_name = $val->from->name ?? null;
                        $records->from_location =$val->from->location ?? null;
                        $records->type = ($val->type)?$val->type:null;
                        $creation_time = new DateTime($val->creationTime);
                        $mysql_creation_time = $creation_time->format('Y-m-d H:i:s');
                        $records->creation_time = $mysql_creation_time;
                        $records->read_status = ($val->readStatus)?$val->readStatus:null;
                        $records->priority = ($val->priority)?$val->priority:null;
                        $records->attachments_id = ($val->attachments[0]->id)?$val->attachments[0]->id:null;
                        $records->direction = ($val->direction)?$val->direction:null;
                        $records->availability = ($val->availability)?$val->availability:null;
                        $records->subject = ($val->subject)?$val->subject:null;
                        $records->message_status = ($val->messageStatus)?$val->messageStatus:null;
                        $records->sms_sending_attempts_count = $val->smsSendingAttemptsCount ?? null;
                        $records->conversation_id = ($val->conversationId)?$val->conversationId:null;
                        $records->last_modified_time = ($val->lastModifiedTime)?$val->lastModifiedTime:null;
                        $records->save();
                    }
                }
                
                $syncInfo = new RcSyncInfo();
                $sync_time = new DateTime($data->syncInfo->syncTime);
                $mysql_sync_time = $sync_time->format('Y-m-d H:i:s');
                $syncInfo->sync_type = $data->syncInfo->syncType ?? null;
                $syncInfo->sync_token = $data->syncInfo->syncToken ?? null;
                $syncInfo->syncTime = $mysql_sync_time;
                $syncInfo->type = 'sms';
                $syncInfo->save();
            }
        }
        else {
            // Handle the case where the 'records' key is not found in the decoded JSON array
            echo "The 'records' key is not found in the decoded JSON array.";
        }
    }

    public function fullSync()
    {
        $queryParams = array(
            //'conversationId' => 000,
            'dateFrom' => '2016-03-10T18:07:52.534Z',
            //'dateTo' => '<ENTER VALUE>',
            //'direction' => array(  ),
            //'distinctConversations' => true,
            'messageType' => array('SMS'),
            //'recordCount' => 20,
            //'syncToken' => '<ENTER VALUE>',
            'syncType' => 'FSync',
            //'voicemailOwner' => array( string )
        );
        
        $response = $this->getMessageSync('~', '~', $queryParams);
        $this->dbSync($response);

        return $response; 
    }

    public function iSync($token)
    {
        $queryParams = array(
        
            //'distinctConversations' => true,
            
            'syncToken' => $token,//'AQEAAAAQAwAAAY7grD0ABgAAABQFAAABju4oq6oTAAgAAAAA6R24PAkBChEZAAABju4oqW_9MrZ7',
            'syncType' => 'ISync',
            
        );
        
        $response = $this->getMessageSync('~', '~', $queryParams);
        $this->dbSync($response);

        return $response; 
    }

    public function getMessageSync($accountId, $extensionId, $queryParams = [])
    {
        try {
            //$this->platform->login(['jwt' => env('RC_JWT')]);
            $this->jwtLogin();
            $response = $this->platform->get("/restapi/v1.0/account/{$accountId}/extension/{$extensionId}/message-sync", $queryParams);
            // Process response here if needed
            
            return $response->json(); // Return JSON response
        } catch (\RingCentral\SDK\Http\ApiException $e) {
            return response()->json(['error' => 'Unable to fetch messages.'], 500);
        }
    }

    private function jwtLogin()
    {
        try {
            $this->platform->login(['jwt' => env('RC_JWT')]);
        } catch (\RingCentral\SDK\Http\ApiException $e) {
            return response()->json(['error' => 'Unable to fetch messages.'], 500);
        }
    }
}
