<?php

namespace Database\Seeders;

use App\Models\StoreDocument;
use App\Models\StoreDocumentTag;
use App\Models\StoreDocumentTagTask;
use App\Models\TaskTag;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use File;
use Illuminate\Support\Facades\Storage;

class ControlCountTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taskTags = TaskTag::whereIn('tag_id', [1, 2, 3, 8, 9])->get();

        $env = env('AWS_S3_PATH');


        foreach($taskTags as $t) {

            if(empty($t->task_id)) {
                continue;
            }

            $_year = (int) $t->year;
            $_month = (int) $t->month;
            $_week = !empty($t->week) ? (int) $t->week : null;
            $_day = !empty($t->day) ? (int) $t->day : null;

            $storeDocumentTag = StoreDocumentTag::where('tag_id', $t->tag_id)
                    ->where('year', $_year)
                    ->where('month', $_month)
                    ->where('week', $_week)
                    ->where('day', $_day)
                    ->first();

            if(!isset($storeDocumentTag->id)) {
                $storeDocumentTag = new StoreDocumentTag();
                $storeDocumentTag->tag_id = $t->tag_id;
                $storeDocumentTag->year = $_year;
                $storeDocumentTag->month = $_month;
                $storeDocumentTag->week = $_week;
                $storeDocumentTag->day = $_day;
                $storeDocumentTag->custom_name = $t->name;
                $storeDocumentTag->pharmacy_store_id = 1;
                $storeDocumentTag->save();
            }

            $check = StoreDocumentTagTask::where('store_document_tag_id', $storeDocumentTag->id)
                ->where('task_id', $t->task_id)->first();

            if(!isset($check->task_id)) {
                StoreDocumentTagTask::insertOrIgnore([
                    'store_document_tag_id' => $storeDocumentTag->id,
                    'task_id' => $t->task_id
                ]);
            }

            $document = StoreDocument::where('category', 'task')->where('parent_id', $t->task_id)->first();

            if(isset($document->id)) {
                if(empty($document->size) && empty($document->name)) {
                    

                    $path = $document->path;

                    $name = basename($path);
                    $dir = dirname($path).'/';
                    $s3_path = '/'.$env.substr($dir, 7).$document->id.'/';

                    try {
                        $size_kb = File::size(public_path($path));
                        $document->size = $size_kb/1024;
                        $last_modified = File::lastModified(public_path($path));
                        $document->last_modified = date("Y-m-d H:i:s",$last_modified);
                        $document->mime_type = File::mimeType(public_path($path));
        
                        $pathFile = $s3_path.$name;
        
                        Storage::disk('s3')->put($pathFile, file_get_contents(public_path($path)));
        
                    } catch(Exception $e) {
                        $document->size = 0;
                    }

                    $document->size_type = 'KB';
                    $document->path = $s3_path;
                    $document->name = $name;

                    $document->background_color = '#fcd0b2';
                    $document->parent_id = $storeDocumentTag->id;
                    $document->category = 'storeDocumentTag';

                    $save = $document->save();
                }
                
            }

        }
    }
}
