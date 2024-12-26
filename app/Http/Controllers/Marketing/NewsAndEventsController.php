<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\File;
use App\Models\NewsAndEvent;
use App\Models\StoreDocument;
use App\Models\StoreStatus;
use App\Models\UpcomingEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NewsAndEventsController extends Controller
{
    public function index(Request $request, $id)
    {
        // try {
            $this->checkStorePermission($id);
            // $newsAndEvents = NewsAndEvent::with('file')->paginate(3);
            $newsAndEvents = NewsAndEvent::with('storeDocuments')
                ->orderBy('created_at', 'desc')
                ->paginate(3);

            $today = Carbon::today();
            $endDate = $today->copy()->addDays(30);

            // Format today's date and the end date to extract month and day
            $todayMonthDay = $today->format('m-d');
            $endMonthDay = $endDate->format('m-d');

            $upcomingBirthdays = Employee::where(function ($query) use ($todayMonthDay, $endMonthDay) {
                    if ($todayMonthDay <= $endMonthDay) {
                        // Normal range within the same year
                        $query->whereBetween(DB::raw('DATE_FORMAT(date_of_birth, "%m-%d")'), [$todayMonthDay, $endMonthDay]);
                    } else {
                        // Wrap around end of year
                        $query->where(function ($query) use ($todayMonthDay) {
                            $query->where(DB::raw('DATE_FORMAT(date_of_birth, "%m-%d")'), '>=', $todayMonthDay);
                        })->orWhere(function ($query) use ($endMonthDay) {
                            $query->where(DB::raw('DATE_FORMAT(date_of_birth, "%m-%d")'), '<=', $endMonthDay);
                        });
                    }
                })
                ->whereNot('status', 'Terminated')
                ->orderByRaw('DATE_FORMAT(date_of_birth, "%m-%d")')
                ->get();
            
            //     $today = Carbon::today();
            // $thirtyDaysFromNow = $today->copy()->addDays(30);

            // // Retrieve upcoming events within the next 30 days and exclude past events
            // $upcomingEvents = UpcomingEvent::where('date', '>=', $today)
            //         ->where('date', '<=', $thirtyDaysFromNow)
            //         ->orderBy('date', 'asc')
            //         ->get(); // Adjust pagination as needed
            
            $breadCrumb = ['Marketing', 'News & Events'];

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('/stores/marketing/newsAndEvents/partials/news', compact('newsAndEvents'))->render(),
                    'pagination' => $newsAndEvents->links()->render()
                ]);
            }

            
            return view('/stores/marketing/newsAndEvents/index', compact('breadCrumb', 'upcomingBirthdays'));
        // } catch (\Throwable $th) {
        //     return response()->view('/errors/403/index', [], 403);
        // }
    }

    public function getEvents(Request $request)
    {
        if ($request->ajax()) {
                $today = Carbon::today();
                $thirtyDaysFromNow = $today->copy()->addDays(30);
                // Retrieve upcoming events within the next 30 days and exclude past events
                $upcomingEvents = UpcomingEvent::where('date', '>=', $today)
                    ->where('date', '<=', $thirtyDaysFromNow)
                    ->orderBy('date', 'asc')
                    ->get(); // Adjust pagination as needed

                return response()->json([
                    'html' => view('/stores/marketing/newsAndEvents/partials/events', compact('upcomingEvents'))->render(),
                ]);
            }
    }

    private function saveFiles($nae_id, $file, $pharmacy_store_id)
    {   
        // $fileName = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        // $fileExtension = $file->getClientOriginalExtension();
        // $mime_type = $file->getMimeType();
        
        // $newFileName = date("Ymdhis").Auth::id() .'_'. $fileName  . '.' . $fileExtension;
        // $doc_type = $fileExtension;
        
        // $path = 'marketing/news_and_events/'.$pharmacy_store_id.'/';
        
        // // Provide a dynamic path or use a specific directory in your S3 bucket
        // $path_file = $path . $newFileName;

        // // Store the file in S3
        // Storage::disk('s3')->put($path_file, file_get_contents($file));

        // // Optionally, get the URL of the uploaded file
        // $s3url = Storage::disk('s3')->url($path_file);

        // $document = new File();

        // $document->filename = $newFileName;
        // $document->path = $path;
        // $document->mime_type = $mime_type;
        // $document->document_type = $doc_type;
        // $document->save();

        // return $document->id;
        // // $eodCashFile = new EodCashFile();
        // // $eodCashFile->eod_cash_id = $id;
        // // $eodCashFile->file_id = $document->id;
        // // $eodCashFile->save();

        $pathUpload = $this->pathUpload($pharmacy_store_id, $nae_id);

        $document = new StoreDocument();
        $document->user_id = auth()->user()->id;
        $document->parent_id = $nae_id;
        $document->category = 'newsAndEvents';
        $document->ext = $file->getClientOriginalExtension();

        @unlink(public_path($pathUpload.'/'.$document->path));
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'imported_'.date('Ymd').'-1.'.$file->getClientOriginalExtension();
        $file->move(public_path($pathUpload), $fileName);
        $document->path = '/'.$pathUpload.'/'.$fileName;
        $path = '/'.$pathUpload.'/'.$fileName;

        $save = $document->save();     
        
    }

    private function pathUpload($pharmacy_store_id, $nae_id) : string
    {
        return 'upload/stores/'.$pharmacy_store_id.'/marketing/news/'.$nae_id;
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            
            $input = $request->all();
            //$inputFile = $input['file'];
            
            $fileValidation = Validator::make($input, [
                'file' => 'mimes:pdf,png,jpeg',
            ]);
            
            $input = json_decode($input['data'], true);

            if($input['status_id'] == 802){
                $validation = Validator::make($input, [
                    'name' => 'required',
                    'url' => 'required',
                ]);
            }
            else{
                $validation = Validator::make($input, [
                    'name' => 'required',
                    'status_id' => 'required',
                ]);
            }
            

            if ($fileValidation->passes() && $validation->passes()){
               

                $nae = new NewsAndEvent();
                $nae->name = $input['name'];
                $nae->caption = $input['caption'];
                $nae->content = $input['content'];
                $nae->status_id = $input['status_id'];
                $nae->url = ($input['status_id']==802)?$input['url']:'';
                $nae->pharmacy_store_id = $input['menu_store_id'];
                $nae->user_id = auth()->user()->id;
                $nae->save();

                if ($request->file('file')) {
                    
                    $file = $request->file('file');
                    
                    $file_id = $this->saveFiles($nae->id, $file, $input['menu_store_id']);
                }

                return json_encode([
                    'status'=>'success',
                    'message'=>'Record has been saved.']);
            }
            else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $fileValidation->errors()->merge($validation->errors()),
                    'message'=>'Record saving failed.'
                ]);
            }

        }
    }

    public function storeEvent(Request $request)
    {
        if($request->ajax()){
            
            $input = $request->all();
            //$inputFile = $input['file'];
            
            $input = json_decode($input['data'], true);

            
            $validation = Validator::make($input, [
                'name' => 'required',
                'date' => 'required',
                'content' => 'required'
            ]);
            
            

            if ($validation->passes()){
               
                $ue = new UpcomingEvent();
                $ue->name = $input['name'];
                $ue->content = $input['content'];
                $ue->date = $input['date'];
                $ue->pharmacy_store_id = $input['menu_store_id'];
                $ue->user_id = auth()->user()->id;
                $ue->save();

                return json_encode([
                    'status'=>'success',
                    'message'=>'Record has been saved.']);
            }
            else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Record saving failed.'
                ]);
            }

        }
    }

    public function deleteEvent(Request $request){
        if($request->ajax()){
            $ue = UpcomingEvent::where('id', $request->id);
            $ue->delete();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    public function deleteNews(Request $request){
        if($request->ajax()){

            $input = $request->all();
            $id = $input['id'];

            $doc = StoreDocument::where('parent_id', $id)->where('category', 'newsAndEvents')->first();
            if ($doc) {
                $directoryPath = dirname($doc->path);
                $directory = public_path($directoryPath);
                if (File::exists($directory)) {
                    FacadesFile::deleteDirectory($directory);
                }
                $doc->delete();
            }

            $ue = NewsAndEvent::where('id', $id);
            if($ue){
                $ue->delete();
                return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
            }
            
            return json_encode(['status'=>'error','message'=>'Record not found.']);
        }
    }

    public function getType(Request $request)
    {
        $data = StoreStatus::select("id", "name")->where('category', 'news_and_events');
        if($request->has('term')) {
            $data = $data->where('name', 'like', "%".$request->term."%");
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
