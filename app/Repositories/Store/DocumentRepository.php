<?php

namespace App\Repositories\Store;

use Illuminate\Http\Request;

use App\Interfaces\DocumentInterface;
use App\Repositories\StoreDocumentRepository;
use App\Models\Ticket;
use App\Models\StoreDocument;
use App\Http\Utils\FileIconUtil;

use File;

class DocumentRepository extends StoreDocumentRepository implements DocumentInterface
{
    use FileIconUtil;

    private $ticket;
    private $document;
    protected $dataTable = [];
    protected $documentDataTable = [];

    public function __construct(Ticket $ticket, StoreDocument $document)
    {
        $this->ticket = $ticket;
        $this->document = $document;
    }

    public function search($request)
    {
        $query = $this->ticket;
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

        $query = $this->ticket->with('user.employee', 'documents', 'assignedTo', 'status');

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
            $documents = isset($value->ticketDocuments) ? $value->ticketDocuments->all() : [];
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
                    <a href="tickets/show/'.$value->id.'">
                        <button type="button" class="btn btn-outline-primary btn-sm me-2"><i class="fa-solid fa-eye"></i></button>
                    </a>
                    <a data-bs-toggle="modal" data-bs-target="#edit_ticket_modal" 
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

        $ticket = new Ticket();
        $ticket->subject = $data->subject;
        $ticket->description = $data->description;
        $ticket->pharmacy_store_id = $pharmacy_store_id;
        $ticket->user_id = auth()->user()->id;
        $status_id = 1;

        if(isset($data->assigned_to_employee_id) && !empty($data->assigned_to_employee_id)) {
            $ticket->assigned_to_employee_id = $data->assigned_to_employee_id;
        }
        if(isset($data->status_id)) {
            $status_id = $data->status_id;
        }
        $ticket->status_id = $status_id;

        $save = $ticket->save();

        if($save) {
            $pathUpload = $this->pathUpload($ticket->pharmacy_store_id, $ticket->id);
        
            if ($request->file('files')) {
                $files = $request->file('files');
                foreach ($files as $key => $file) {

                    $document = new StoreDocument;
                    $document->user_id = auth()->user()->id;
                    $document->parent_id = $ticket->id;
                    $document->category = 'ticket';
                    $document->ext = $file->getClientOriginalExtension();

                    @unlink(public_path($pathUpload.'/'.$document->path));
                    $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'_'.date('YmdHi').'.'.$file->getClientOriginalExtension();
                    // $fileName = date('YmdHi').'_'.$file->getClientOriginalName();
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
            throw new Exception("Not saved");
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

        $ticket = $this->ticket->findOrFail($request->id);
        $ticket->subject = $request->subject;
        $ticket->description = $request->description;
        $ticket->user_id = auth()->user()->id;

        if(isset($request->assigned_to_employee_id) && !empty($request->assigned_to_employee_id)) {
            $ticket->assigned_to_employee_id = $request->assigned_to_employee_id;
        }
        if(isset($request->status_id)) {
            $ticket->status_id = $request->status_id;
        }

        $save = $ticket->save();

        if(!$save) {
            $flag = false;
        }
        
        if(!$flag) {
            throw "Not saved";
        }
    }

    public function delete($id)
    {
        $ticket = $this->ticket->findOrFail($id);
        $path = $this->pathUpload($ticket->pharmacy_store_id, $ticket->id);

        File::deleteDirectory(public_path('/'.$path));
        $save = $ticket->delete();

        if(!$save) {
            throw "Not Deleted";
        }
    }

    public function storeDocument($request)
    {
        $ticket = $this->ticket->findOrFail($request->ticket_id);

        if ($request->file('files')) {
            $pathUpload = $this->pathUpload($ticket->pharmacy_store_id, $ticket->id);
            $files = $request->file('files');

            $this->processStoringDocuments($files, $ticket->id, 'ticket', $pathUpload);
        }
    }

    public function deleteDocument($id)
    {
        $document = $this->document->findOrFail($id);
        @unlink(public_path('/'.$document->path));
        $save = $document->delete();

        if(!$save) {
            throw "Not Deleted";
        }
    }

    /**
     * Private functions starts here
     */
    private function pathUpload($pharmacy_store_id, $ticket_id) : string
    {
        return self::BASE_PATH.'/'.$pharmacy_store_id.'/escalation/tickets/'.$ticket_id;
    }
}