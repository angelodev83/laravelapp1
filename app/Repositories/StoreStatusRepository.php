<?php

namespace App\Repositories;

use Illuminate\Http\Request;

use App\Interfaces\IStoreStatusRepository;
use App\Repositories\BaseStoreRepository;
use App\Models\StoreStatus;

class StoreStatusRepository extends BaseStoreRepository implements IStoreStatusRepository
{
    private $storeStatus;
    protected $dataTable = [];

    public function __construct(StoreStatus $storeStatus)
    {
        $this->storeStatus = $storeStatus;
    }

    public function search($request)
    {
        $query = $this->storeStatus->orderBy('sort');
        if(isset($request->category)) {
            $query = $query->where('category',$request->category);
        }
        return $query->get();
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
        $orderColumnIndex = $request->order[0]['column'] ?? 0;
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        $query = $this->storeStatus->with('user.employee', 'storeStatusDocuments', 'assignedTo', 'status');

        // Search //input all searchable fields
        $search = $request->search;
        $query = $query->where(function($query) use ($search){ 
            $query->orWhere('subject', 'like', "%".$search."%");   
        });

        if($request->has('pharmacy_store_id')) {
            $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
        }
        
        $orderByCol = $request->columns[$request->order[0]['column']]['name'];
        
        $query = $query->orderBy($orderByCol, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $newData = [];

        foreach ($data as $value) {
            $empName = isset($value->user->employee) ? $value->user->employee->getFullName() : "NA";
            $documents = isset($value->storeStatusDocuments) ? $value->storeStatusDocuments->all() : [];
            $assignedName = isset($value->assignedTo) ? $value->assignedTo->getFullName() : "NA";
            $status = isset($value->status) ? $value->status : [];

            $newData[] = [
                'id' => $value->id,
                'subject' => $value->subject,
                'assigned_to_employee_id' => $value->assigned_to_employee_id,
                'assigned_to' => $assignedName,
                'description' => $value->description,
                'status_id' => $value->status_id,
                'created_at' => $value->created_at->format('M d, Y h:iA'),
                'created_by' => $empName,
                'documents' => $documents,
                'status' =>  '
                    <button type="button" onclick="clickStatusBtn(' . $value->id . ')" class="btn btn-'.$status->color.' btn-sm radius-15 px-3" ><small>'.$status->name.'</small></button>',
                'actions' =>  '<div class="d-flex order-actions">
                    <a href="storeStatuss/show/'.$value->id.'">
                        <button type="button" class="btn btn-outline-primary btn-sm me-2"><i class="fa-solid fa-eye"></i></button>
                    </a>
                    <a data-bs-toggle="modal" data-bs-target="#edit_storeStatus_modal" 
                            data-subject="'.$value->subject.'" 
                            data-description="'.htmlspecialchars($value->description).'" 
                            data-id="'.$value->id.'"
                            data-status_id="'.$value->status_id.'"
                            data-assigned_to_employee_id="'.$value->assigned_to_employee_id.'"
                            data-assigned_to="'.addslashes($assignedName).'"
                            class="btn-primary me-1" style="background-color:#8833ff; color: #ffffff; cursor: pointer;"><i class="bx bxs-edit"></i></a>
                    <button type="button" onclick="clickDeleteBtn(' . $value->id . ')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                </div>'
            ];
        }

        $this->dataTable = [
            "draw"=> $request->draw, 
            "recordsTotal"=> $recordsTotal, 
            "recordsFiltered" => $recordsFiltered, 
            'data' => $newData
        ];
    }

    /**
     * action store
     *
     * @param [type] $request
     * @param [type] $pharmacy_store_id
     * @return void
     */
    public function store($request, $pharmacy_store_id)
    {
        $flag = true;

        $data = json_decode($request->data);

        $storeStatus = new StoreStatus();
        $storeStatus->subject = $data->subject;
        $storeStatus->description = $data->description;
        $storeStatus->pharmacy_store_id = $pharmacy_store_id;
        $storeStatus->user_id = auth()->user()->id;
        $status_id = 1;

        if(isset($data->assigned_to_employee_id) && !empty($data->assigned_to_employee_id)) {
            $storeStatus->assigned_to_employee_id = $data->assigned_to_employee_id;
        }
        if(isset($data->status_id)) {
            $status_id = $data->status_id;
        }
        $storeStatus->status_id = $status_id;

        $save = $storeStatus->save();

        if($save) {
            $pathUpload = $this->pathUpload($storeStatus->pharmacy_store_id, $storeStatus->id);
        
            if ($request->file('files')) {
                $files = $request->file('files');
                foreach ($files as $key => $file) {

                    $document = new StoreStatusDocument;
                    $document->user_id = auth()->user()->id;
                    $document->storeStatus_id = $storeStatus->id;
                    $document->ext = $file->getClientOriginalExtension();

                    @unlink(public_path($pathUpload.'/'.$document->path));
                    $fileName = date('YmdHi').'_'.$file->getClientOriginalName();
                    $file->move(public_path($pathUpload), $fileName);
                    $document->path = '/'.$pathUpload.'/'.$fileName;
                    $path = '/'.$pathUpload.'/'.$fileName;

                    $save = $document->save();

                    if(!$save) {
                        $flag = false;
                    }
                }
            }
        } else {
            $flag = false;
        }
        
        if(!$flag) {
            throw "Not saved";
        }
    }

    /**
     * action udpate
     *
     * @param [type] $request
     * @param [type] $pharmacy_store_id
     * @return void
     */
    public function update($request, $pharmacy_store_id)
    {
        $flag = true;

        $storeStatus = $this->storeStatus->findOrFail($request->id);
        $storeStatus->subject = $request->subject;
        $storeStatus->description = $request->description;
        $storeStatus->user_id = auth()->user()->id;

        if(isset($request->assigned_to_employee_id) && !empty($request->assigned_to_employee_id)) {
            $storeStatus->assigned_to_employee_id = $request->assigned_to_employee_id;
        }
        if(isset($request->status_id)) {
            $storeStatus->status_id = $request->status_id;
        }

        $save = $storeStatus->save();

        if(!$save) {
            $flag = false;
        }
        
        if(!$flag) {
            throw "Not saved";
        }
    }

    public function delete($id)
    {
        $storeStatus = $this->storeStatus->findOrFail($id);
        $path = $this->pathUpload($storeStatus->pharmacy_store_id, $storeStatus->id);

        File::deleteDirectory(public_path('/'.$path));
        $save = $storeStatus->delete();

        if(!$save) {
            throw "Not Deleted";
        }
    }
}