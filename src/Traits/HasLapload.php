<?php

namespace Simtabi\Lapload\Traits;

use Illuminate\Support\Facades\Storage;
use Simtabi\Lapload\Helpers\LaploadHelper;

trait HasLapload
{
    public $uploadedFiles;

    public function getListeners()
    {
        return [
            'uploadedFiles',
            'deleteFile',
        ];
    }

    public function uploadedFiles($propertyName, $itemNames)
    {
        // capture array of uploaded images name
        $this->$propertyName = $itemNames;
        $this->setUploadedFiles($itemNames);
    }

    public function deleteFile($oldImage, $directory)
    {
        Storage::delete(LaploadHelper::getLocalDiskUploadPath($directory) . $oldImage);
    }

    public function setUploadedFiles($uploadedFiles): self
    {
        $this->uploadedFiles = $uploadedFiles;

        return $this;
    }

    public function getUploadedFiles($path)
    {
        $files = null;

        if (!empty($this->uploadedFiles)) {
            if (count($this->uploadedFiles) == 1) {
                $files = $path . '/' . $this->uploadedFiles[0];
            }else{
                foreach ($this->uploadedFiles as $file){
                    $files[] = $path . '/' . $file;
                }
            }
        }

        return $files;
    }

}
