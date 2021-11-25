<?php

namespace Simtabi\Lapload\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Simtabi\Lapload\Helpers\LaploadHelper;

trait ImageUploader
{
    public function getListeners()
    {
        return ['imagesUpdated', 'deleteImage'];
    }

    public function imagesUpdated($propertyName, $imagesName)
    {
        //return array of uploaded images name
        $this->$propertyName = $imagesName;
    }

    public function deleteImage($oldImage)
    {
        //delete image
        Storage::delete(LaploadHelper::getLocalDiskUploadPath() . $oldImage);
    }
}