<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', '1800'); //300 seconds = 10 minutes

use App\Interfaces\UploadInterface;
use App\Models\Employee;
use App\Models\Oig_Exclusion_List;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class Oig_exclusion_listController extends Controller
{
    private UploadInterface $uploadRepository;

    public function __construct(UploadInterface $uploadRepository) {
        $this->uploadRepository = $uploadRepository;
        $this->middleware('permission:menu_store.cnr.oig_check.index');
    }

    public function index()
    {
        $user = Auth::user();
        $breadCrumb = ['Compliance & Regulatory', 'OIG List'];

        return view('/cs/oig_list/index', compact('user', 'breadCrumb'));
    }

    public function downloadOigCsv()
    {
        $file = "https://oig.hhs.gov/exclusions/downloadables/UPDATED.csv";

        // dd(storage_path());
        // Retrieve the file //working remote csv download.. comment for fast testing
        $current = file_get_contents($file);

        $save_name = str_replace('\\', '/' , storage_path())."/oig_hit_list.csv";
        
        file_put_contents($save_name, $current);

        //$this->uploadRepository->uploadOig($save_name);
        $this->csvSearch($save_name);
        // $this->loadCsvMysql();
        $this->updateOigStatus();

        return response()->json([
            'result' => 'done'
        ], 200);
    }

    private function csvSearch($sourceFile)
    {
        DB::statement('TRUNCATE table oig__exclusion__lists');
        $employees = Employee::get();
        $concatenatedDataArray = [];
        
        foreach ($employees as $employee) {
            // Trim and remove special characters from each attribute
            $trimmedFirstName = preg_replace('/[^A-Za-z0-9]/', '', strtolower(trim($employee->firstname)));
            $trimmedLastName = preg_replace('/[^A-Za-z0-9]/', '', strtolower(trim($employee->lastname)));
            $trimmedDateOfBirth = preg_replace('/[^0-9-]/', '', strtolower(trim($employee->date_of_birth)));

            // Remove spaces and hyphens
            $trimmedFirstName = str_replace(' ', '', $trimmedFirstName);
            $trimmedLastName = str_replace(' ', '', $trimmedLastName);
            $trimmedDateOfBirth = str_replace('-', '', $trimmedDateOfBirth);

            // Concatenate the trimmed attributes
            $concatenatedData = $trimmedLastName . $trimmedFirstName . $trimmedDateOfBirth;

            // Add the concatenated data to the array
            $concatenatedDataArray[] = $concatenatedData;
        }

        // Columns to concatenate (0-based index)
        $columnsToConcatenate = [0, 1, 8]; // Indexes for LASTNAME, FIRSTNAME, and DOB respectively

        // Open the CSV file for reading
        $fileHandle = fopen($sourceFile, "r");
        
        if ($fileHandle !== false) {
            // Read the header line
            $header = fgetcsv($fileHandle);
            
            // Get the column indexes for the specified columns
            $lastNameIndex = array_search('LASTNAME', $header);
            $firstNameIndex = array_search('FIRSTNAME', $header);
            $dobIndex = array_search('DOB', $header);
            
            // Loop through each row in the CSV file
            while (($data = fgetcsv($fileHandle)) !== false) {
                
                $trimmedFirstName = preg_replace('/[^A-Za-z0-9]/', '', strtolower(trim($data[$firstNameIndex])));
                $trimmedLastName = preg_replace('/[^A-Za-z0-9]/', '', strtolower(trim($data[$lastNameIndex])));
                $trimmedDateOfBirth = preg_replace('/[^0-9-]/', '', strtolower(trim($data[$dobIndex])));

                // Remove spaces and hyphens
                $trimmedFirstName = str_replace(' ', '', $trimmedFirstName);
                $trimmedLastName = str_replace(' ', '', $trimmedLastName);
                $trimmedDateOfBirth = str_replace('-', '', $trimmedDateOfBirth);

                $concatenatedString = $trimmedLastName . $trimmedFirstName . $trimmedDateOfBirth;
                // // Check if the search term exists in the concatenated string
                // if (stripos($concatenatedString, $searchTerm) !== false) {
                // Check if the concatenated string exists in the array
                if (in_array($concatenatedString, $concatenatedDataArray)) {
                    // Match found, do something with the row
                    Storage::disk('local')->append('file.txt', json_encode($data));
                    $matchedRows[] = [
                        'lastname' => $data[0],
                        'firstname' => $data[1],
                        'midname' => $data[2],
                        'busname' => $data[3],
                        'general' => $data[4],
                        'specialty' => $data[5],
                        'upin' => $data[6],
                        'npi' => $data[7],
                        'dob' => $data[8],
                        'address' => $data[9],
                        'city' => $data[10],
                        'state' => $data[11],
                        'zip' => $data[12],
                        'excltype' => $data[13],
                        'excldate' => $data[14],
                        'reindate' => $data[15],
                        'waiverdate' => $data[16],
                        'wvrstate' => $data[17],
                        // Add other fields as needed
                    ];
                }
            }
            
            // Close the file handle
            fclose($fileHandle);
        } else {
            echo "Failed to open the CSV file.";
        }

        // Perform bulk upload to Laravel Eloquent
        if (!empty($matchedRows)) {
            Oig_Exclusion_List::insert($matchedRows);
        }
    }

    public function reloadOigCsv()
    {
        $this->loadCsvMysql();
        $this->updateOigStatus();

        return response()->json([
            'status'=>'success',
            'message'=>'Reload OIG Check.'], 200);
    }

    public function updateOigStatus()
    {
        $update_at = DB::Select('SELECT employees.id FROM employees');
        foreach ($update_at as $update_all_id) {
            $emp1 = Employee::find($update_all_id->id);
            $emp1->updated_at = date('Y-m-d H:i:s');
            $emp1->oig_status = "No Match";
            $emp1->save();
        }
        $hit_ids = DB::Select('SELECT employees.id FROM employees
            JOIN oig__exclusion__lists
            ON employees.firstname = oig__exclusion__lists.firstname AND employees.lastname = oig__exclusion__lists.lastname
            AND employees.date_of_birth = oig__exclusion__lists.dob');
        foreach ($hit_ids as $hit_id) {
            $emp = Employee::find($hit_id->id);
            $emp->oig_status = "Match";
            $emp->save();
        }
        
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

    public function get_data(Request $request)
    {   
        $user = Auth::user();

        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        // get data from products table
        $query = DB::table('oig__exclusion__lists')->select('*')
            ->where('lastname', '!=', '')
            ->where('firstname', '!=', '');

        // Search
        $search = $request->search;
        $query = $query->where(function($query) use ($search){
            $query->orWhere('firstname', 'like', "%".$search."%");
            $query->orWhere('lastname', 'like', "%".$search."%");
            $query->orWhere('midname', 'like', "%".$search."%");
            $query->orWhere('busname', 'like', "%".$search."%");
            $query->orWhere('general', 'like', "%".$search."%");
            $query->orWhere('specialty', 'like', "%".$search."%");
            $query->orWhere('upin', 'like', "%".$search."%");
            $query->orWhere('npi', 'like', "%".$search."%");
            $query->orWhere('dob', 'like', "%".$search."%");
            $query->orWhere('address', 'like', "%".$search."%");
            $query->orWhere('city', 'like', "%".$search."%");
            $query->orWhere('state', 'like', "%".$search."%");
            $query->orWhere('zip', 'like', "%".$search."%");
            $query->orWhere('excltype', 'like', "%".$search."%");   
            $query->orWhere('excldate', 'like', "%".$search."%");
            $query->orWhere('reindate', 'like', "%".$search."%");
            $query->orWhere('waiverdate', 'like', "%".$search."%");
            $query->orWhere('wvrstate', 'like', "%".$search."%"); 
            $query->orWhereRaw('CONCAT(firstname," ", lastname) like  "%'.$search.'%"');  
            $query->orWhereRaw('CONCAT(lastname," ", firstname) like  "%'.$search.'%"');    
        });

        $orderByCol = 'lastname';
        switch($orderColumnIndex){
            case '0':
                $orderByCol = 'lastname';
                break;
            case '1':
                $orderByCol = 'firstname';
                break;
            case '2':
                $orderByCol = 'midname';
                break;
            case '3':
                $orderByCol = 'busname';
                break;
            case '4':
                $orderByCol = 'general';
                break;
            case '5':
                $orderByCol = 'specialty';
                break;
            case '6':
                $orderByCol = 'upin';
                break;
            case '7':
                $orderByCol = 'npi';
                break;
            case '8':
                $orderByCol = 'dob';
                break;
            case '9':
                $orderByCol = 'address';
                break;
            case '10':
                $orderByCol = 'city';
                break;
            case '11':
                $orderByCol = 'state';
                break;
            case '12':
                $orderByCol = 'zip';
                break;
            case '13':
                $orderByCol = 'excltype';
                break;
            case '14':
                $orderByCol = 'excldate';
                break;
            case '15':
                $orderByCol = 'reindate';
                break;
            case '16':
                $orderByCol = 'waiverdate';
                break;
            case '17':
                $orderByCol = 'wvrstate';
                break;
        
        }
        $query = $query->orderBy($orderByCol, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $newData = [];
        foreach ($data as $value) {
            $newData[] = [
                'firstname' => $value->firstname,
                'lastname' => $value->lastname,
                'midname' => $value->midname,
                'busname' => $value->busname,
                'general' => $value->general,
                'specialty' => $value->specialty,
                'upin' => $value->upin,
                'npi' => $value->npi,
                'dob' => $value->dob,
                'address' => $value->address,
                'city' => $value->city,
                'state' => $value->state,
                'zip' => $value->zip,
                'excltype' => $value->excltype,
                'excldate' => $value->excldate,
                'reindate' => $value->reindate,
                'waiverdate' => $value->waiverdate,
                'wvrstate' => $value->wvrstate,
                ];
        }   
        

        return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);       
    }
}
