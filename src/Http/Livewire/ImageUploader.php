<?php

namespace Simtabi\Lapload\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Simtabi\Lapload\Helpers\LaploadHelper;

class ImageUploader extends Component
{
    use WithFileUploads;

    public $rawImages;
    public $images     = [];
    public $imageNames = [];
    public $oldImages, $multiple, $name, $maxSize, $uploadTo;

    protected $messages = [
        'rawImages.*.image' => 'The upload file must be an image.',
        'rawImages.*.mimes' => 'The upload must be of :mimes type.',
        'rawImages.*.max'   => 'The images must not be greater than :max KB.',
        'rawImages.image'   => 'The upload must be of :mimes type.',
        'rawImages.mimes'   => 'The image format must be :mimes.',
        'rawImages.max'     => 'The image size must not be greater than :max KB.',
    ];

    public function mount(string $name, string $uploadTo, bool $multiple = false, int $maxSize = LaploadHelper::MAX_FILE_SIZE, array $old = null)
    {
        $this->name                  = $name;
        $this->uploadTo              = $uploadTo;
        $this->maxSize               = $maxSize ?? LaploadHelper::getDefaultMaxFileSize();
        $this->multiple              = $multiple;
        $multiple ? $this->rawImages = []   : $this->rawImages = null;
        $old      ? $this->oldImages = $old : $this->oldImages = null;
    }

    public function getUploadTo()
    {
        return LaploadHelper::getLocalUploadPath($this->uploadTo);
    }

    public function updatingRawImages()
    {
        $this->multiple ? $this->rawImages=[] : $this->rawImages = null;
        $this->images = array();
    }

    public function updatedRawImages($value)
    {
        if ($this->multiple) {
            $this->validate(
                ['rawImages.*' => 'image|mimes:'.LaploadHelper::getImageMimeTypes() .'|max:'.$this->maxSize],
            );
        }

        if (!$this->multiple) {
            $this->validate(
                ['rawImages.*' => 'image|mimes:'.LaploadHelper::getImageMimeTypes() .'|max:'.$this->maxSize],
                []
            );
        }

        // $this->images = $value;
        $this->multiple ? $this->images = $value : $this->images = array($value);

        $this->uploadImages();
    }


    public function uploadImages()
    {
        if (!empty($this->imageNames)) {
            foreach ($this->imageNames as $image) {
                Storage::delete($this->getUploadTo() . $image);
            }
            $this->imageNames = array();
        }

        foreach ($this->images as $image) {
            $image->store($this->getUploadTo());
            array_push($this->imageNames, $image->hashName());
        }
        return $this->handleUpdatedImages();
    }

    public function handleRemoveImage($index, $old = false)
    {
        if ($old) {
            $this->emitUp('deleteItem', $this->oldImages[$index]);
            // $this->oldImages[$index]->delete();
            unset($this->oldImages[$index]);
        } else {
            Storage::delete($this->getUploadTo() . $this->imageNames[$index]);
            unset($this->images[$index]);
            unset($this->imageNames[$index]);
            return $this->handleUpdatedImages();
        }
    }

    public function handleUpdatedImages()
    {
        $this->emit('updatedItems', $this->name, $this->imageNames);
    }

    public function render()
    {
        return view(LaploadHelper::getPackageName().'::livewire.image');
    }
}
