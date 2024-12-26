<?php

namespace App\Models\Traits;

use File;

trait FileTrait
{
    public function getFileSize() 
    {
        if(!empty($this->path))
            return File::size(public_path($this->path));

        return "";
    }

    public function getLastModified()
    {
        if(!empty($this->path))
            return File::lastModified(public_path($this->path));

        return "";
    }

    public function getFileSizeByType($type = "KB")
    {
        $size = (int)$this->getFileSize();
        switch($type) {
            case "KB":
                $size = $size / 1024;
                break;
            case "MB":
                $size = $size / 1024 / 1024;
                break;
            case "GB":
                $size = $size / 1024 / 1024 / 1024;
                break;
            default:
                break;
        }
        $size = round($size, 2);
        return $size.' '.$type;
    }

}