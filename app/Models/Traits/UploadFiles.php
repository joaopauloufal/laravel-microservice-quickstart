<?php

namespace App\Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


trait UploadFiles{

    protected abstract function uploadDir();

    public function uploadFiles(array $files){
        foreach($files as $file){
            $this->uploadFile($file);
        }
    }

    public function uploadFile($file){
        $file->store($this->uploadDir());
    }

    public function deleteFiles(array $files){
        foreach($files as $file){
            $this->deleteFile($file);
        }
    }

    public function deleteFile($file){
        $filename = $file instanceof UploadedFile? $file->hashName(): $file;
        Storage::delete("{$this->uploadDir()}/{$filename}");
    }

    public static function extracFiles(array &$attributes = []){
        $files = [];
        foreach(self::$fileFields as $file){
            if(isset($attributes[$file]) && $attributes[$file] instanceof UploadedFile){
                $files[] = $attributes[$file];
                $attributes[$file] = $attributes[$file]->hashName();
            }
        }

        return $files;
    }



}
