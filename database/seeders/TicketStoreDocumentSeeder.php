<?php

namespace Database\Seeders;

use App\Models\StoreDocument;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use File;
use Illuminate\Support\Facades\Storage;

class TicketStoreDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->update();
    }

    protected function update()
    {
        $storeDocs = StoreDocument::query()->Tickets()->get();
        $env = env('AWS_S3_PATH');
        foreach($storeDocs as $item) {
            $path = $item->path;

            $name = basename($path);
            $dir = dirname($path).'/';

            $s3_path = '/'.$env.substr($dir, 7).$item->id.'/';

            try {
                $size_kb = File::size(public_path($path));
                $item->size = $size_kb/1024;
                $last_modified = File::lastModified(public_path($path));
                $item->last_modified = date("Y-m-d H:i:s",$last_modified);
                $item->mime_type = File::mimeType(public_path($path));

                $pathFile = $s3_path.$name;

                Storage::disk('s3')->put($pathFile, file_get_contents(public_path($path)));

            } catch(Exception $e) {
                $item->size = 0;
            }

            $item->size_type = 'KB';
            $item->path = $s3_path;
            $item->name = $name;

            $item->save();
        }
    }

}
