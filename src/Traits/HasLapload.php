<?php

namespace Simtabi\Lapload\Traits;

use Illuminate\Support\Facades\Storage;
use Simtabi\Lapload\Helpers\LaploadHelper;

trait HasLapload
{
    public $successfulUploads;

    public function getListeners()
    {
        return [
            'updatedItems',
            'deleteItem',
        ];
    }

    public function updatedItems($propertyName, $itemNames)
    {
        // capture array of uploaded images name
        $this->$propertyName = $itemNames;
        $this->setSuccessfulUploads($itemNames);
    }

    public function deleteItem($oldImage, $directory)
    {
        Storage::delete(LaploadHelper::getLocalDiskUploadPath($directory) . $oldImage);
    }

    public function setSuccessfulUploads($successfulUploads): self
    {
        $this->successfulUploads = $successfulUploads;

        return $this;
    }

    public function getSuccessfulUploads($path)
    {
        $files = null;

        if (!empty($this->successfulUploads)) {
            if (count($this->successfulUploads) == 1) {
                $files = $path . '/' . $this->successfulUploads[0];
            }else{
                foreach ($this->successfulUploads as $file){
                    $files[] = $path . '/' . $file;
                }
            }
        }

        return $files;
    }

}
