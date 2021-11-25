<?php

namespace Simtabi\Lapload\Traits;

use Illuminate\Support\Facades\Storage;
use Simtabi\Lapload\Helpers\LaploadHelper;

trait HasLapload
{
    public $uploadedFiles;
    
    public function getListeners()
    {
        return ['imagesUpdated', 'deleteImage'];
    }

    public function imagesUpdated($propertyName, $imagesName)
    {
        //return array of uploaded images name
        $this->$propertyName = $imagesName;
        $this->uploadedFiles = $imagesName;
    }

    public function getUploadedFiles($path)
    {
        $data = null;

        if (!empty($this->uploadedFiles)) {
            if (count($this->uploadedFiles) == 1) {
                $data = $path . '/' . $this->uploadedFiles[0];
            }else{
                foreach ($this->uploadedFiles as $file){
                    $data[] = $path . '/' . $file;
                }
            }
        }

        return $data;
    }

    public function deleteImage($oldImage)
    {
        //delete image
        Storage::delete(LaploadHelper::getLocalDiskUploadPath() . $oldImage);
    }
}
