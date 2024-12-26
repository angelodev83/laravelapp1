<?php

namespace App\Http\Controllers;

use App\Models\PharmacyStore;
use App\Models\PharmacyStaff;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PharmacyStoreController extends Controller
{
    private const PATH_UPLOAD = 'upload/stores';

    /**
     * Instantiate a new PharmacyStoreController instance.
     */
    public function __construct()
    {
        $this->middleware('permission:pharmacy_store.index|pharmacy_store.create|pharmacy_store.update|pharmacy_store.delete');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $breadCrumb = ['Division 2B', 'Pharmacy Store'];

        $stores = PharmacyStore::all();

        return view('/division2b/pharmacyStores/index', compact('user', 'breadCrumb', 'stores'));
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

            $query = new PharmacyStore();


            // Search //input all searchable fields
            $search = $request->search;
            $query = $query->where(function($query) use ($search){
                $query->orWhere('code', 'like', "%".$search."%");   
                $query->orWhere('name', 'like', "%".$search."%");   
            });


            $withTrashed = isset($request->withTrashed) ? $request->withTrashed : false;
            $query->withTrashed($withTrashed);   
            
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {
                $newData[] = [
                    'id' => $value->id,
                    'code' => $value->code,
                    'name' => $value->name,
                    'address' => $value->address,
                    'description' => $value->description,
                    'cover_image' => $value->cover_image,
                    'actions' =>  '<div class="d-flex order-actions">
                        <button type="button" class="btn btn-primary btn-sm me-2" onclick="showEditForm('.$value->id.',\'' . addslashes($value->code) . '\',\'' . addslashes($value->name) . '\',\'' . addslashes($value->address) . '\',\'' . addslashes($value->description) . '\',\'' . $value->cover_image . '\');"><i class="fa-solid fa-pencil"></i></button>
                        <button type="button" onclick="ShowConfirmDeleteStoreForm(' . $value->id . ')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                    </div>'
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    public function add_store(Request $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();
                try {
                    $data = json_decode($request->data);
                    $pharmacyStore = new PharmacyStore;
                    $pharmacyStore->code = $data->code;
                    $pharmacyStore->name = $data->name;
                    $pharmacyStore->address = $data->address;
                    $pharmacyStore->description = $data->description;
                    if ($request->file('cover_image')) {
                        $file = $request->file('cover_image');
                        $fileUpload = self::PATH_UPLOAD.'/'.$pharmacyStore->id.'/system-settings/pharmacy-stores';
                        @unlink(public_path($fileUpload.'/'.$pharmacyStore->cover_image));
                        $fileName = date('YmdHi').'_'.$file->getClientOriginalName();
                        $file->move(public_path($fileUpload), $fileName);
                        $pharmacyStore->cover_image = '/'.$fileUpload.'/'.$fileName;
                        $cover_image = '/'.$fileUpload.'/'.$fileName;
                    }
                    
                    $save = $pharmacyStore->save();

                    if($save) {
                        $rname = 'pharmacy-admin.'.$pharmacyStore->id;
                        $display_name = 'Owner of Store: '.$pharmacyStore->code;
                        $rsave = Role::create(['name' => $rname, 'display_name' => $display_name, 'guard_name' => 'web', 'description' => 'Can access all pages within store '.$pharmacyStore->code.' - '.$pharmacyStore->name]);

                        $pname = 'menu_store.'.$pharmacyStore->id;
                        if($rsave) {
                            $psave = Permission::create([
                                'name' => $pname, 'guard_name' => 'web', 
                                'display_name' => 'Access pages from Store: '.$pharmacyStore->code, 
                                'division_name' => 'menu_store.unique', 
                                'group_name' => $pharmacyStore->id, 
                                'description' => 'Can access all pages within store '.$pharmacyStore->code.' - '.$pharmacyStore->name
                            ]);
                            $rsave->givePermissionTo([$psave->name]);
                            $permissions = Permission::where('division_name', 'menu_store')->pluck('name');
                            $rsave->givePermissionTo($permissions);
                        }

                    }

                    if(!$save) {
                        throw "Not saved";
                    }

                    DB::commit();

                    return json_encode([
                        'data'=> $pharmacyStore,
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack(); 
                    return response()->json([
                        'error' => $e->getMessage(),
                        'message' => 'Something went wrong in PharmacyStoreController.add_store.db_transaction.'
                    ]);
                }
            }catch(\Exception $e){
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyStoreController.add_store.'
                ]);
            }
        }
    }

    public function update_store(Request $request)
    {
        if($request->ajax()){

            try {
                DB::beginTransaction();
                try {   
                    $data = json_decode($request->data);                 
                    $pharmacyStore = PharmacyStore::findOrFail($data->id);
                    $pharmacyStore->code = $data->code;
                    $pharmacyStore->name = $data->name;
                    $pharmacyStore->address = $data->address;
                    $pharmacyStore->description = $data->description;
                    if ($request->file('cover_image')) {
                        $fileUpload = self::PATH_UPLOAD.'/'.$pharmacyStore->id.'/system-settings/pharmacy-stores';
                        $file = $request->file('cover_image');
                        @unlink(public_path($fileUpload.'/'.$pharmacyStore->cover_image));
                        $fileName = date('YmdHi').'_'.$file->getClientOriginalName();
                        $file->move(public_path($fileUpload), $fileName);
                        $pharmacyStore->cover_image = '/'.$fileUpload.'/'.$fileName;
                        $cover_image = '/'.$fileUpload.'/'.$fileName;
                    }
                    $save = $pharmacyStore->save();

                    if(!$save) {
                        throw "Not saved";
                    }

                    DB::commit();

                    return json_encode([
                        'data'=> $pharmacyStore,
                        'status'=>'success',
                        'message'=>'Record has been updated.'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack(); 
                    return response()->json([
                        'error' => $e->getMessage(),
                        'message' => 'Something went wrong in PharmacyStoreController.update_store.db_transaction.'
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyStoreController.update_store.'
                ]);
            }
        }
    }

    public function delete_store(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                DB::beginTransaction();
                
                $count = PharmacyStaff::with('pharmacyStores')->count();
                if($count > 0) {
                    PharmacyStaff::where("pharmacy_store_id",$request->id)->delete();
                }

                $pharmacyStore = PharmacyStore::findOrFail($request->id);
                $save =  $pharmacyStore->delete();
                
                if(!$save) {
                    DB::rollback();
                    return response()->json([
                        'error' => "error",
                        'message' => 'Something went wrong in PharmacyStoreController.delete_store.'
                    ]);
                }

                DB::commit();

                return json_encode([
                    'data'=>$pharmacyStore,
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyStoreController.delete_store.'
                ]);
            }
        }
    }

    
}
