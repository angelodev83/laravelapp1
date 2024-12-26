<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', '600'); //300 seconds = 5 minutes

use App\Interfaces\UploadInterface;
use Illuminate\Http\Request;
use App\Models\File;

use Auth;
use App\Models\Stage;
use App\Models\Status;
use App\Models\Medication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    private UploadInterface $repository;

    public function __construct(UploadInterface $repository)
    {
        $this->repository = $repository;
    }

    public function delete_file_via_ajax(Request $request)
    {
		$user = auth()->check() ? Auth::user() : redirect()->route('login');
        $input = $request->all();

        $file =  File::where('id','=',  $input['file_id']);
        
        if($file == null){
                return json_encode(
                ['status'=>'error',
                'message'=>'File delete failed.']);
        }else{
                      
            $file->delete();

            //TODO - delete file in AWS
            return json_encode(['status'=>'success','message'=>'File deleted succesfully.']);
            
        }
        
    }

    public function upload(Request $request)
    {
        $user = Auth::user();
        $statuses = Status::orderBy('id', 'asc')->get();
        $stages = Stage::orderBy('id', 'asc')->get();



        if ($user->userType->id == 1) {
            return view('/cs/upload', compact('user', 'statuses', 'stages'));
        }
    }

    public function csv_upload(Request $request)
    {
        $user = Auth::user();
        $statuses = Status::orderBy('id', 'asc')->get();
        $stages = Stage::orderBy('id', 'asc')->get();



        if ($user->userType->id == 1) {
            return view('/fulfillments/csv_upload', compact('user', 'statuses', 'stages'));
        }
    }

    public function uploadMedicationsCsv()
    {

    }

    public function loadMedicationsCsvMysql()
    {
       
        try {
            DB::beginTransaction();
            //code...
            $absolute_path = str_replace('\\', '/' , storage_path());

            //DB::statement('TRUNCATE table temp_medications');
            DB::statement("CREATE TEMPORARY TABLE temp_medication (
                name TEXT,
                ndc TEXT,
                upc TEXT,
                item_number TEXT,
                manufacturer TEXT,
                category TEXT,
                package_size TEXT,
                awp_price TEXT,
                rx_price TEXT,
                340b_price TEXT
            );
            ");
            DB::statement("LOAD DATA INFILE '".$absolute_path."/medications.csv'
                IGNORE INTO TABLE temp_medication
                FIELDS TERMINATED BY ','
                ENCLOSED BY '\"'
                ESCAPED BY '\"'
                LINES TERMINATED BY '\r\n'
                IGNORE 1 ROWS
                (name, ndc, upc, item_number, manufacturer, category, package_size, awp_price,rx_price, 340b_price)");
        
        // if($absolute_path.'/temp_medications.csv'){
        //     unlink($absolute_path.'/temp_medications.csv');
        // }
        // DB::statement("SELECT REPLACE( REPLACE ( REPLACE ( REPLACE ( REPLACE ( REPLACE ( REPLACE ( REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (CONCAT(ndc,upc,item_number), '/', ''), ',', ''), '.', ''), '<', ''), '>', ''), '?', ''), ';', ''), ':', ''), '\"', ''), \"'\", ''), '|', ''), '\\\', ''), '=', ''), '+', ''), '*', ''), '&', ''), '^', ''), '%', ''), '$', ''), '#', ''), '@', ''), '!', ''), '~', ''), '`', ''), '-', ''), '{', '' ), '}', '' ), '[', '' ), ']', '' ), '(', '' ), ')', '' ), '[^\\x20-\\x7E]', ''), name, ndc, upc, item_number, manufacturer,
        //     category, package_size, REPLACE(REPLACE(awp_price, '$',''), '-', '0'), REPLACE(REPLACE(rx_price, '$',''), '-', '0'), REPLACE(REPLACE(340b_price, '$',''), '-', '0')
        //     INTO OUTFILE '".$absolute_path."/temp_medications.csv'
        //     FIELDS TERMINATED BY ','
        //     ENCLOSED BY '\"'
        //     LINES TERMINATED BY '\n'
        //     FROM temp_medications");

        // DB::statement('TRUNCATE table medications');
        // DB::statement("LOAD DATA INFILE '".$absolute_path."/temp_medications.csv'
        //     INTO TABLE medications
        //     FIELDS TERMINATED BY ','
        //     ENCLOSED BY '\"'
        //     LINES TERMINATED BY '\n'
        //     (id, name, ndc, upc, item_number, manufacturer, category, package_size, aw_price, rx_price, 340b_price)");
            DB::statement('TRUNCATE table back_up_medications');
            DB::statement("INSERT INTO back_up_medications (med_id, name, ndc, upc, item_number, manufacturer, category, package_size, awp_price, rx_price, 340b_price)
                SELECT REPLACE( REPLACE ( REPLACE ( REPLACE ( REPLACE ( REPLACE ( REPLACE ( REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (CONCAT(ndc,upc,item_number), '/', ''), ',', ''), '.', ''), '<', ''), '>', ''), '?', ''), ';', ''), ':', ''), '\"', ''), \"'\", ''), '|', ''), '\\\', ''), '=', ''), '+', ''), '*', ''), '&', ''), '^', ''), '%', ''), '$', ''), '#', ''), '@', ''), '!', ''), '~', ''), '`', ''), '-', ''), '{', '' ), '}', '' ), '[', '' ), ']', '' ), '(', '' ), ')', '' ), '[^\\x20-\\x7E]', ''), name, ndc, upc, item_number, manufacturer,
                category, package_size, CAST(TRIM(REPLACE(REPLACE(REPLACE(awp_price, '$', ''), ',', ''), '-', '0')) as DECIMAL(10,2)), CAST(TRIM(REPLACE(REPLACE(REPLACE(rx_price, '$', ''), ',', ''), '-', '0')) as DECIMAL(10,2)), CAST(TRIM(REPLACE(REPLACE(REPLACE(340b_price, '$', ''), ',', ''), '-', '0')) as DECIMAL(10,2))
                FROM temp_medication"); 
                
            DB::statement('TRUNCATE table medications');
            // DB::statement("INSERT INTO medications (med_id, name, ndc, upc, item_number, manufacturer, category, package_size, awp_price, rx_price, 340b_price)
            //     SELECT REPLACE( REPLACE ( REPLACE ( REPLACE ( REPLACE ( REPLACE ( REPLACE ( REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (CONCAT(ndc,upc,item_number), '/', ''), ',', ''), '.', ''), '<', ''), '>', ''), '?', ''), ';', ''), ':', ''), '\"', ''), \"'\", ''), '|', ''), '\\\', ''), '=', ''), '+', ''), '*', ''), '&', ''), '^', ''), '%', ''), '$', ''), '#', ''), '@', ''), '!', ''), '~', ''), '`', ''), '-', ''), '{', '' ), '}', '' ), '[', '' ), ']', '' ), '(', '' ), ')', '' ), '[^\\x20-\\x7E]', ''), name, ndc, upc, item_number, manufacturer,
            //     category, package_size, CAST(TRIM(REPLACE(REPLACE(REPLACE(awp_price, '$', ''), ',', ''), '-', '0')) as DECIMAL(10,2)), CAST(TRIM(REPLACE(REPLACE(REPLACE(rx_price, '$', ''), ',', ''), '-', '0')) as DECIMAL(10,2)), CAST(TRIM(REPLACE(REPLACE(REPLACE(340b_price, '$', ''), ',', ''), '-', '0')) as DECIMAL(10,2))
            //     FROM temp_medications");  
            DB::statement("INSERT INTO medications (med_id, name, ndc, upc, item_number, manufacturer, category, package_size, awp_price, rx_price, 340b_price)
                SELECT med_id, name, ndc, upc, item_number, manufacturer, category, package_size, awp_price, rx_price, 340b_price
                FROM ( SELECT med_id, name, ndc, upc, item_number, manufacturer, category, package_size, awp_price, rx_price, 340b_price,
                ROW_NUMBER() OVER (PARTITION BY med_id ORDER BY med_id) AS row_num FROM back_up_medications ) AS t WHERE row_num = 1;
            ");   
                // WHERE NOT EXISTS 
                // (SELECT id FROM medications WHERE id = REPLACE( REPLACE ( REPLACE ( REPLACE ( REPLACE ( REPLACE ( REPLACE ( REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (CONCAT(temp_medications.ndc,temp_medications.upc,temp_medications.item_number), '/', ''), ',', ''), '.', ''), '<', ''), '>', ''), '?', ''), ';', ''), ':', ''), '\"', ''), \"'\", ''), '|', ''), '\\\', ''), '=', ''), '+', ''), '*', ''), '&', ''), '^', ''), '%', ''), '$', ''), '#', ''), '@', ''), '!', ''), '~', ''), '`', ''), '-', ''), '{', '' ), '}', '' ), '[', '' ), ']', '' ), '(', '' ), ')', '' ), '[^\\x20-\\x7E]', ''))");
                DB::commit();
        } catch (\Exception $e) {
            DB::rollBack(); 
            //Storage::disk('local')->append('file.txt', json_encode($e->getMessage()));
            $array_return = array(
                'error' => $e,
                'expand_error' => $e->getMessage()
            );
            return $array_return;
        }

    }

    public function downloadOigCsv()
    {
        $file = "https://oig.hhs.gov/exclusions/downloadables/UPDATED.csv";

        // dd(storage_path());
        // Retrieve the file //working remote csv download.. comment for fast testing
        $current = file_get_contents($file);

        $save_name = str_replace('\\', '/' , storage_path())."/oig_hit_list.csv";
        
        file_put_contents($save_name, $current);

        $this->loadCsvMysql();
        $this->updateOigStatus();

        return response()->json([
            'result' => 'done'
        ], 200);
    }

    public function download($id)
    {   
         
        $file = File::where('id', $id)->first();
        
        $headers = [
            'Content-Type'        => 'Content-Type: '.$file->mime_type.' ',
            'Content-Disposition' => 'attachment; filename="'. $file->filename .'"',
        ];
        
        $path = $file->path.$file->filename;
        
        return Response::make(Storage::disk('s3')->get($path), 200, $headers);
    }

    public function loadCsvMysql()
    {
        $absolute_path = str_replace('\\', '/' , storage_path());

        DB::statement('TRUNCATE table oig__exclusion__lists');
        DB::statement('LOAD DATA INFILE "'.$absolute_path.'/oig_hit_list.csv"
            INTO TABLE oig__exclusion__lists
            FIELDS TERMINATED BY \',\'
            ENCLOSED BY \'"\'
            LINES TERMINATED BY \'\n\'
            IGNORE 1 ROWS
            (lastname,firstname,midname,busname,general,specialty,upin,npi,dob,address,city,state,zip,excltype,excldate,reindate,waiverdate,wvrstate)');

    }

    public function loadOutcomesCsvMysql($params = [])
    {
       
        try {
            DB::beginTransaction();
            //code...
            $absolute_path = str_replace('\\', '/' , storage_path());

            // Create temporary table
            DB::statement("
                CREATE TEMPORARY TABLE temp_outcomes(
                    date_reported TEXT, patients TEXT, tips_completed TEXT, cmrs_completed TEXT, mtm_score TEXT
                )
            ");

            // Load data into temporary table
            $file = $absolute_path . "/outcomes.csv";
            DB::statement("
                LOAD DATA INFILE '$file'
                INTO TABLE temp_outcomes
                FIELDS TERMINATED BY ','
                ENCLOSED BY '\"'
                ESCAPED BY '\"'
                LINES TERMINATED BY '\r\n'
                IGNORE 1 ROWS
                (date_reported, patients, tips_completed, @dummy, cmrs_completed, @dummy, mtm_score)
            ");

            // Insert data from temporary table into main table
            $pharmacy_store_id = isset($params['pharmacy_store_id']) ? $params['pharmacy_store_id'] : null;
            DB::statement("
                INSERT INTO outcomes (date_reported, patients, tips_completed, cmrs_completed, mtm_score, created_at, updated_at, pharmacy_store_id)
                SELECT STR_TO_DATE(date_reported, '%m-%d-%Y'), patients, tips_completed, cmrs_completed, mtm_score, now(), now(), $pharmacy_store_id as pharmacy_store_id
                FROM temp_outcomes
            ");

            // Drop temporary table
            DB::statement("DROP TEMPORARY TABLE IF EXISTS temp_outcomes");
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack(); 
            Storage::disk('local')->append('file.txt', json_encode($e->getMessage()));
            $array_return = array(
                'error' => $e,
                'expand_error' => $e->getMessage()
            );
            return $array_return;
        }

    }

    public function xlsx_uploader(Request $request)
    {
        if($request->ajax()){
            $file = $request->file('csvFile');
            $input = $request->all();
           
            $validation = Validator::make($input, [
                'csvFile' => 'required|mimes:csv,xlsx',
            ]);

            if ($validation->passes()){
                switch ($request->for) {
                    case 'outcomes':

                        $this->repository->uploadOutcomes($request);
                        
                        // if($error_return == ''){
                            return response()->json([
                                'status'=>'success',
                                'message'=>'Record has been saved.'
                            ]);
                        // }
                        // else{
                        //     return response()->json([
                        //         'status'=>'error',
                        //         'errors'=>$error_return,
                        //         'message'=>'Invalid CSV Data.'
                        //     ]);
                        // }
                        break;
                    
                    default:
                        # code...
                        break;
                }
            }
            else{
                return json_encode(
                    ['status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Check Input Fields.'
                ]);
            }
        }
    }

    public function csv_uploader(Request $request)
    {
        if($request->ajax()){
            $file = $request->file('csvFile');
            $input = $request->all();
           
            $validation = Validator::make($input, [
                'csvFile' => 'required|mimes:csv',
            ]);

            if ($validation->passes()){
                switch ($request->for) {
                    case 'outcomes':
                        $params = [];
                        if($request->has('pharmacy_store_id')) {
                            $params = ['pharmacy_store_id' => $request->pharmacy_store_id];
                        }
                        $current = file_get_contents($file);
                        $save_name = str_replace('\\', '/' , storage_path())."/outcomes.csv";
                    
                        file_put_contents($save_name, $current);

                        $error_return = $this->loadOutcomesCsvMysql($params);
                        //Storage::disk('local')->append('file.txt', json_encode($error_return));
                        
                        if($error_return == ''){
                            return response()->json([
                                'status'=>'success',
                                'message'=>'Record has been saved.'
                            ]);
                        }
                        else{
                            return response()->json([
                                'status'=>'error',
                                'errors'=>$error_return,
                                'message'=>'Invalid CSV Data.'
                            ]);
                        }
                        break;
                    
                    default:
                        # code...
                        break;
                }
            }
            else{
                return json_encode(
                    ['status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Check Input Fields.'
                ]);
            }
        }
    }

    public function upload_csv(Request $request)
    {
        //if ($request->hasFile('csvFile')) {
            $file = $request->file('csvFile');
            $input = $request->all();
            // Validate file type
            // if ($file->getClientOriginalExtension() != 'csv') {
            //     return response()->json(['error' => 'Only CSV files are allowed.'], 400);
            // }
            $validation = Validator::make($input, [
                'csvFile' => 'required|mimes:csv',
            ]);

            if ($validation->passes()){
                

                $current = file_get_contents($file);
                $save_name = str_replace('\\', '/' , storage_path())."/medications.csv";
            
                file_put_contents($save_name, $current);

                $error_return = $this->loadMedicationsCsvMysql();
                //Storage::disk('local')->append('file.txt', json_encode($error_return));
                
                if($error_return['expand_error'] == 'There is no active transaction'){
                    return response()->json([
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ]);
                }
                else{
                    return response()->json([
                        'status'=>'error',
                        'errors'=>$error_return,
                        'message'=>'Invalid CSV Data.'
                    ]);
                }
            }
            else{
                return json_encode(
                    ['status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Check Input Fields.'
                ]);
            }
            // Open file for reading
            // $handle = fopen($file->getRealPath(), 'r');
            // $rows = [];

            

            // Skip the first row (header)
            //$headers = fgetcsv($handle);

            
            

            // Read all rows
            // while (($data = fgetcsv($handle)) !== FALSE) {
            //     if (empty($data[1])) { // If 'ndc' is empty
            //         continue; // Skip this iteration and move to the next row
            //     }

            //     $rows[] = $data;
            //     Medication::updateOrCreate(
            //         ['ndc' => $data[1]], // 'ndc' is the identifier
            //         [
            //             'name' => empty($data[0]) ? '-' : $data[0],
            //             'ndc' => empty($data[1]) ? '-' : $data[1],
            //             'manufacturer' => empty($data[4]) ? '-' : $data[4],
            //             'category' => empty($data[5]) ? '-' : $data[5],
            //             'package_size' => empty($data[6]) ? '-' : $data[6],
            //             'rx_price' => floatval(str_replace('$', '', $data[8])),
            //             '340b_price' => floatval(str_replace('$', '', $data[9])),
            //             // 'package_size' => empty($data[2]) ? 0 : $data[2],
            //             // 'balance_on_hand' => empty($data[3]) ? 0 : $data[3],
            //             // 'therapeutic_class' => empty($data[4]) ? '-' : $data[4],
            //             // 'category' => empty($data[5]) ? '-' : $data[5],
            //             // 'manufacturer' => empty($data[6]) ? '-' : $data[6],
            //             // 'rx_price' => floatval(str_replace('$', '', $data[7])),
            //             // '340b_price' => floatval(str_replace('$', '', $data[8])),
            //             //'last_update_date' => empty($data[10]) ? null : date('Y-m-d', strtotime($data[10])),
            //         ]
            //     );
            // }

            // Close file
            //fclose($handle);

            // Return rows
        //     return response()->json('success'); //return response()->json($rows);
        // } else {
        //     return response()->json(['error' => 'No file was uploaded.'], 400);
        // }
    }

}
