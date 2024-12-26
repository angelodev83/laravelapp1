<?php

namespace App\Http\Controllers;

use App\Interfaces\UploadInterface;
use Auth;
use Illuminate\Http\Request;
use App\Models\Outcome;
use Illuminate\Support\Facades\Validator;

class OutcomeController extends Controller
{
    private UploadInterface $repository;

    public function __construct(UploadInterface $repository)
    {
        $this->repository = $repository;
        // $this->middleware('auth');
        // $this->middleware('permission:division-3.outcomes.index', ['only' => ['index', 'get_data']]);
        // $this->middleware('permission:division-3.outcomes.delete', ['only' => ['delete']]);
    }

    public function index($id, Request $request, $status_id = null, $stage_id = null)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Clinical', 'MTM, Outcomes Reports'];
            
            return view('division3/outcomes/index', compact('breadCrumb'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function get_data(Request $request)
    {   
        if($request->ajax()){

            // Customizable fields
            $model = Outcome::class;
            $columns = ['id', 'date_reported', 'patients', 'tips_completed', 'cmrs_completed', 'created_at', 'updated_at'];
            $searchableColumns = ['date_reported', 'patients', 'tips_completed', 'cmrs_completed'];
            $orderByCol = 'id';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip = ($pageNumber-1) * $pageLength;

            // Page Order
            $query = $model::query();
            $query = $query->orderBy($orderByCol, $orderBy);

            // Search
            $search = $request->search;
            $query = $query->where(function($query) use ($search, $searchableColumns){
                foreach ($searchableColumns as $column) {
                    $query->orWhere($column, 'like', "%".$search."%");
                }
            });

            if($request->has('pharmacy_store_id')) {
                $query = $query->where('pharmacy_store_id', $request->pharmacy_store_id);
            }

            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();
            
            $hideD = 'hidden';
            if(auth()->user()->can('menu_store.clinical.mtm_outcomes_report.delete')) {
                $hideD = '';
            }

            $newData = [];
            foreach ($data as $value) {
                $newData[] = [
                    'id' => $value->id,
                    'date_reported' => $value->date_reported,
                    'patients' => $value->patients,
                    'tips_completed' => $value->tips_completed,
                    'tips_completion_rate' => ($value->patients > 0) ? number_format(($value->tips_completed / $value->patients) * 100, 2) . '%' : '0%',
                    'cmrs_completed' => $value->cmrs_completed,
                    'cmrs_completion_rate' => ($value->patients > 0) ? number_format(($value->cmrs_completed / $value->patients) * 100, 2) . '%' : '0%',
                    'created_at' => $value->created_at,
                    'formatted_created_at' => date('M d, Y g:i A', strtotime($value->pst_created_at)),
                    'actions' =>  '<div class="d-flex order-actions">
                    <button type="button" onclick="ShowConfirmDeleteForm(' . $value->id . ', \'' . $value->date_reported . '\')" class="btn btn-danger btn-sm me-2" '.$hideD.'><i class="fa-solid fa-trash-can"></i></button>
                    </div>'
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    public function delete(Request $request)
    {
        $user = auth()->check() ? Auth::user() : redirect()->route('login');
        $input = $request->all();
        $outcome = Outcome::find($input['report_id']);

        if($outcome == null){
            return json_encode(
                ['status'=>'error',
                'message'=>'Report deletion failed.']);
        } else {
          
            $outcome->delete();
            return json_encode(['status'=>'success','message'=>'Deletion successful.']);
        }
    }

    public function upload(Request $request)
    {
        try {
            if($request->ajax()){

                $input = $request->all();
            
                $validation = Validator::make($input, [
                    'csvFile' => 'required|mimes:xlsx',
                ]);

                if ($validation->passes()){
            
                    $this->repository->uploadOutcomes($request);
                    
                    return json_encode([
                        'data'=> [],
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ]);
                }
                else{
                
                    return json_encode(
                        ['status'=>'error',
                        'errors'=> $validation->errors(),
                        'message'=>'Check Input Fields.'
                    ]);
                
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in PioneerPatientController.upload.'
            ]);
        }
    }
   
}
