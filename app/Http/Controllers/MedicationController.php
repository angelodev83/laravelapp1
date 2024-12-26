<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medication;
use Exception;
use DataTables;
use Illuminate\Support\Facades\Auth;

class MedicationController extends Controller
{
    // public function index(Request $request)
    // {
    //     $user = Auth::user();
       
    //     $medicationsQuery = Medication::orderBy('name', 'asc');
    //     $search = $request->input('search');
    
    //     if ($search !== null) {
    //         $medicationsQuery->where(function ($query) use ($search) {
    //             $query->whereRaw("CONCAT(name, ' ', IFNULL(ndc, ''), ' ', IFNULL(package_size, ''), ' ', IFNULL(balance_on_hand, ''), ' ', IFNULL(therapeutic_class, ''), ' ', IFNULL(category, ''), ' ', IFNULL(manufacturer, ''), ' ', IFNULL(rx_price, ''), ' ', IFNULL(340b_price, '')) LIKE ?", ["%$search%"]);
    //         });
    //     }
    
    //     $medications = $medicationsQuery->paginate(100);
    
    
    // switch ($user->userType->id) {
    //     case 1:
    //     case 2:
    //         return view('cs/medications/index', compact('user', 'medications'));
    //     case 3:
    //         return view('/medications/index', compact('user', 'medications'));
    //     default:
    //         // Handle other cases or throw an error
    //         throw new Exception('Invalid user type');
    // }
        
 
    // }

    public function index()
    {
        $user = Auth::user();

        $breadCrumb = ['Division 3', 'Medications'];
        return view('/division3/medications/index', compact('user', 'breadCrumb'));
    }

    public function get_data(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;
            
            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // get data from products table
            $query = new Medication();

            // Search //input all searchable fields
            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                // $query->orWhere('name', 'like', "%".$search."%");
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        $query->orWhere($column['name'], 'like', "%".$search."%");
                    }  
                }
            });

            //default field for order
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            
            

            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $val) {

                $last_update_date = ($val->last_update_date)?date('Y-m-d', strtotime($val->last_update_date)):'';
                
                $newData[] = [
                    'med_id' => $val->med_id,
                    'name' => $val->name,
                    'ndc' => $val->ndc,
                    'package_size' => $val->package_size,
                    'balance_on_hand' => $val->balance_on_hand,
                    'therapeutic_class' => $val->therapeutic_class,
                    'category' => $val->category,
                    'manufacturer' => $val->manufacturer,
                    'rx_price' => $val->rx_price,
                    '340b_price' => $val['340b_price'],
                    'last_update_date' => $last_update_date,
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }

    public function data(Request $request)
    {
        // Query the patients table
        $model = Medication::select('*');

        // Initialize DataTables with the query
        $dataTable = DataTables::of($model);

        // Return the DataTable as JSON
        return $dataTable->toJson();
    }

    public function suggest(Request $request)
    {
            
            $input = $request->all();
    
            $medications =  Medication::orderBy('name', 'ASC')
            ->orWhereRaw("name like '".$input['name']."%' ")
            ->take(10)->get();
    
            if($medications != null){
                    return json_encode([
                        'status' => 'success',
                        'medications' => $medications->toArray(),
                    ]);
            }
    
    }

    public function getNames(Request $request)
    {
        $data = Medication::select("med_id", "name");
        if($request->has('term')) {
            $data = $data->where('name', 'like', "%".$request->term."%");
        }
        
        if($request->has('med_id')) {
            if($request->has('not')) {
                $data = $data->where('med_id', '!=',$request->med_id);
            }
        }

        if($request->has('limit')) {
            $data = $data->take($request->limit);
        }

        $data = $data->orderBy('name','asc')->get();
        
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }

}
