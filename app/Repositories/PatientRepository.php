<?php

namespace App\Repositories;

use App\Imports\PioneerPatientsImport;
use App\Interfaces\IPatientRepository;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PatientRepository implements IPatientRepository
{
    private $patient;
    private $dataTable;

    public function __construct(Patient $patient)
    {
        $this->patient = $patient;
    }

    public function search($request)
    {
        return null;
    }

    public function getDataTable() : array
    {
        return $this->dataTable;
    }

    public function setDataTable($request)
    {
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        // get data from patients table
        $query = new Patient();

        if($request->has('source')) {
            $query = $query->where('source',$request->source);
        }
        if($request->has('mustHaveRelation')) {
            $query = $query->whereHas($request->mustHaveRelation);
        }
        if($request->has('pharmacy_store_id')) {
            $query = $query->where('pharmacy_store_id',$request->pharmacy_store_id);
        }

        $query = $query->whereNot('status', 'DELETED');

        if($request->has('facility_name')) {
            $facility_name = $request->facility_name;
            if(!empty($facility_name)) {
                if($facility_name == 'Unsorted') {
                    $query = $query->where(function ($query) {
                        $query->whereNotIn(DB::raw('UPPER(facility_name)'), ['CTCLUSI TM5', 'TMO5'])
                              ->orWhereNull('facility_name')
                              ->orWhere('facility_name', '');
                    });
                } else {
                    $query = $query->where(DB::raw('UPPER(facility_name)'), strtoupper($request->facility_name));
                }
            }
        }

        // Search //input all searchable fields
        $search = trim($request->search);
        $columns = $request->columns;

        $encryptedQuery = [];
        if(!empty($search)) {
            $encryptedQuery = Patient::query()->get()->filter(function ($encryptedQuery) use ($request) {
                return stristr($encryptedQuery->getDecryptedFirstname(), trim($request->search)) !== false
                    // || ($encryptedQuery->middlename ? stristr($encryptedQuery->getDecryptedMiddlename(), trim($request->search)) : $encryptedQuery->middlename)!== false
                    || stristr($encryptedQuery->getDecryptedLastname(), trim($request->search)) !== false
                    || stristr($encryptedQuery->getDecryptedBirthdate(), trim($request->search)) !== false
                    || stristr($encryptedQuery->getDecryptedAddress(), trim($request->search)) !== false
                    || stristr($encryptedQuery->getDecryptedCity(), trim($request->search)) !== false
                    || stristr($encryptedQuery->getDecryptedAddress(), trim($request->search)) !== false
                    || stristr($encryptedQuery->zip_code, trim($request->search)) !== false
                    || stristr($encryptedQuery->phone_number, trim($request->search)) !== false
                    || stristr($encryptedQuery->home_phone, trim($request->search)) !== false
                    || stristr($encryptedQuery->pioneer_id, trim($request->search)) !== false
                    || stristr($encryptedQuery->facility_name, trim($request->search)) !== false;
            })->pluck('id');
        }

        if(!empty($encryptedQuery)) {  
            $query->whereIn('id',$encryptedQuery);
        }


        // $query = $query->where(function($query) use ($search, $columns){
            // foreach ($columns as $column) {
            //     if($column['searchable'] === "true" && !empty($search)){
            //         $query->orWhere("$column[name]", 'like', "%".$search."%");
            //     }  
            // }
        // });

        $orderByCol =  'id';
        $query = $query->orderBy($orderByCol, $orderBy);

        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $newData = [];
        foreach ($data as $value) {

            // Get the current date and time
            $now = Carbon::now();

            // Get the date and time one month ago
            $oneMonthAgo = $now->subMonth();

            $statusText = 'Existing';
            if ($value->created_at > $oneMonthAgo) {
                $statusText = 'New';
            }
            

            $birthdate = Crypt::decryptString($value->birthdate);
            $newData[] = [
                'id' => $value->id,
                'pioneer_id' => $value->pioneer_id,
                'firstname' => Crypt::decryptString($value->firstname),
                'middlename' => $value->middlename ? Crypt::decryptString($value->middlename) : '',
                'lastname' => Crypt::decryptString($value->lastname),
                'suffix' => $value->suffix ? Crypt::decryptString($value->suffix) : '',
                'birthdate' =>  !empty($birthdate) ? date('M d, Y' , strtotime($birthdate)) : '',
                'created_at' => !empty($value->pst_created_at) ? date('M d, Y g:i A', strtotime($value->pst_created_at)) : '',
                'updated_at' => !empty($value->pst_created_at) ? date('M d, Y g:i A', strtotime($value->pst_created_at)) : '',
                'address' => Crypt::decryptString($value->address),
                'city' => Crypt::decryptString($value->city),
                'state' => Crypt::decryptString($value->state),
                'zip_code' => $value->zip_code,
                'phone_number' => $value->phone_number,
                'home_phone' => $value->home_phone,
                'facility_name' => $value->facility_name,
                'status_text'    => $statusText,
                // 'encrypted_info' => $value->encrypted_info,
                
                'actions' =>  '<div class="d-flex order-actions">
                    <button type="button" onclick="ShowEditForm(' . $value->id . ',\'' . $value->firstname . '\',\'' . $value->lastname . '\',\'' . $value->birthdate . '\',\'' . $value->address . '\',\'' . $value->city . '\',\'' . $value->state . '\',\'' . $value->zip_code . '\',\'' . $value->phone_number . '\')" class="btn btn-primary btn-sm me-2" ><i class="fa-solid fa-pencil"></i></button>
                    <button type="button" onclick="ShowConfirmDeleteForm(' . $value->id . ', \'' . $value->firstname . ' ' . $value->lastname . '\')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                </div>',
            ];
        }   

        $total_count = Patient::count();

        $this->dataTable = [
            "totalCount" => $data->count(), 
            "draw"=> $request->draw, 
            "recordsTotal"=> $recordsTotal, 
            "recordsFiltered" => $recordsFiltered, 
            'data' => $newData
        ];
    }

    public function store($request)
    {

    }

    public function update($request)
    {

    }

    public function delete($id)
    {

    }
    
    public function pioneerPatientCounts()
    {
        $all_count = Patient::query()->Pioneer()->whereNot('status', 'DELETED')->count();
        $ctclusi_tm5_count = Patient::query()->CtclusiTm5()->Pioneer()->whereNot('status', 'DELETED')->count();
        $tmo5_count = Patient::query()->Tmo5()->Pioneer()->whereNot('status', 'DELETED')->count();
        
        $unsorted_count = $all_count-($ctclusi_tm5_count+$tmo5_count);

        return [
            'all_count'         => $all_count,
            'ctclusi_tm5_count' => $ctclusi_tm5_count,
            'tmo5_count'        => $tmo5_count,
            'unsorted_count'    => $unsorted_count
        ];
    }

    public function sourcePatientCounts($source, $relation = null)
    {
        $all_count = Patient::query()->$source()->whereNot('status', 'DELETED');
        if(!empty($relation)) {
            $all_count = $all_count->whereHas($relation);
        }
        $all_count =  $all_count->count();
        $ctclusi_tm5_count = Patient::query()->CtclusiTm5()->$source()->whereNot('status', 'DELETED')->count();
        $tmo5_count = Patient::query()->Tmo5()->$source()->whereNot('status', 'DELETED')->count();
        
        $unsorted_count = $all_count-($ctclusi_tm5_count+$tmo5_count);

        // dd($all_count);

        return [
            'all_count'         => $all_count,
            'ctclusi_tm5_count' => $ctclusi_tm5_count,
            'tmo5_count'        => $tmo5_count,
            'unsorted_count'    => $unsorted_count
        ];
    }

    public function syncPioneerPatientMasterlist($pharmacy_store_id)
    {
        $sourceFolder = 'sms-folder/patient-masterlist';
        $destinationFolder = 'sms-folder/_completed/patient-masterlist';

        $contents = Storage::disk('s3')->files($sourceFolder);
        $count = 0;

        foreach($contents as $k => $content) {
            $file = Storage::disk('s3')->get($content);
            $name = basename($content);

            $path = 'temp/sms-folder/PATIENT_MASTERLIST_'.$name;

            if (!Storage::disk('local')->exists('temp/sms-folder')) {
                Storage::disk('local')->makeDirectory('temp/sms-folder');
            }
    
            $localFilePath = str_replace('\\', '/' , storage_path()."/app/".$path);
            file_put_contents($localFilePath, $file);

            Excel::import(new PioneerPatientsImport($pharmacy_store_id), $localFilePath);
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

        return 'Done patient syncing saved data ('.$count.') counts and moved from folder:<patient-masterlist> to folder:<_completed/patient-masterlist> '.date('Y-m-d h:ia');
    }
    
}