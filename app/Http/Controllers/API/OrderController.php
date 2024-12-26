<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Helpers\Helper;
use App\Models\Patient;
use App\Models\Order;
use App\Models\Item;
use App\Models\Status;
use App\Models\Stage;
use App\Models\File;
use App\Rules\MatchesUserApiKey;
use League\Flysystem\Filesystem;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    //
    public function addOrder(Request $request)
    {
        try {
            $helper =  new Helper;
            $input = $request->all();

            $username = $request->header('username');
            $apikey = $request->header('apikey');

            $validator = Validator::make($request->headers->all(), [
                'apikey' => 'required|exists:api_keys,key',
                'username' => ['required', new MatchesUserApiKey],
            ]);

            $validator2 = Validator::make($input, [
                'firstname' => 'required|max:30',
                'lastname' => 'required|max:30',
                'birthdate' => 'required|max:30',
                'order_number' => 'required',
            ]);

            if ($validator->fails() || $validator2->fails()) {
                return response()->json([
                    'status' => 'Order creation unsuccessful due to validation errors',
                    'errors' => array_merge($validator->errors()->all(), $validator2->errors()->all())
                ], 422);
            }

            $patient = Patient::create([
                'firstname' => $helper->ProperNamingCase($input['firstname']),
                'lastname' => $helper->ProperNamingCase($input['lastname']),
                'birthdate' => date('Y-m-d', strtotime($input['birthdate'])),
                'address' => $input['address'],
                'city' => $input['city'],
                'state' => $input['state'],
                'zip_code' => $input['zip_code'],
                'phone_number' => $input['phone_number'],
                'patientid' => $input['patientid'],
                'withorder' => 1,
            ]);

            $order = Order::create([
                'patient_id' => $patient->id,
                'order_number' => $input['order_number'],
                'shipment_status_id' => 1,
            ]);

            $items = json_decode($input['items'], true);

            foreach($items as $item_input){
                $item = Item::create([
                    'order_id' => $order->id,
                    'name' => $item_input['name'],
                    'sig' => $item_input['sig'],
                    'days_supply' => $item_input['days_supply'],
                    'refills_remaining' => $item_input['refills_remaining'],
                    'ndc' => $item_input['ndc'],
                    'rx_stage' => 1,
                    'rx_status' => 1,
                    'inventory_type' => $item_input['inventory_type'],
                ]);
            }


            if (!$request->hasFile('rx_image') || !$request->hasFile('intake_form')) {
                return response()->json(['error' => 'All files (rx_image, intake_form, pod_proof_of_delivery) are required'], 400);
            }

            $files = [
                'rx_image' => $request->file('rx_image'),
                'intake_form' => $request->file('intake_form'),
            ];

            foreach ($files as $key => $uploadedFile) {
                if ($uploadedFile) {
                    
                        // Get the original filename without extension
                        $filename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);

                        // Get the extension of the file
                        $extension = $uploadedFile->getClientOriginalExtension();

                        // Create a new filename with timestamp
                        $newFilename = $filename . '_' . time() . '.' . $extension;

                        //$path = $uploadedFile->storeAs('public/uploads/pdfs', $newFilename);

                        // Provide a dynamic path or use a specific directory in your S3 bucket
                        $s3Path = 'orders/files/';

                        // Store the file in S3 with the new filename
                        $uploadSuccessful = Storage::disk('s3')->putFileAs($s3Path, $uploadedFile, $newFilename);

                        if (!$uploadSuccessful) {
                            return response()->json(['error' => 'File upload failed'], 500);
                        }

                   

                    //insert into database, use model File
                    $file = new File;
                    $file->order_id = $order->id;
                    $file->filename = $newFilename;
                    $file->path = '/orders/attachments/' . $newFilename;
                    $file->path = $s3Path;
                    $file->mime_type = $uploadedFile->getClientMimeType();
                    $file->document_type = $key; // 'rx_image' or 'intake_form'
                    $file->save();

                    if ($key == 'rx_image') {
                        $order->rx_image = $file->id;
                    } elseif ($key == 'intake_form') {
                        $order->intake_form = $file->id;
                    }

                    $order->save();

                }
            }

        
            return response()->json([
                'status' => 'API Insert request succeeded',
                'patient' => $patient,
                'order' => $order,
                'items' => $order->items
            ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            // Handle other types of exceptions
            $errorMessage = $e->getMessage();
            return response()->json(['error' => $errorMessage], 500);
        }
    }
}
