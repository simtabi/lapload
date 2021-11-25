<?php

namespace Simtabi\Lapload\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Simtabi\Lapload\Helpers\LaploadHelper;

class Uploader extends Component
{
    use WithFileUploads;

    public $rawImages;
    public $images     = [];
    public $imagesName = [];
    public $oldImages;
    public $multiple;
    public $name;
    public $uploadTo   = null;
    public $maxSize;
    public $disk       = 'public';

    protected $messages = [
        'rawImages.*.image' => 'The images format must be type of image.',
        'rawImages.*.mimes' => 'The images format must be :mimes.',
        'rawImages.*.max'   => 'The images must not be greater than :max KB.',
        'rawImages.image'   => 'The image format must be type of image.',
        'rawImages.mimes'   => 'The image format must be :mimes.',
        'rawImages.max'     => 'The image must not be greater than :max KB.',
    ];

    public function mount(string $name, string $uploadTo, bool $multiple = false, int $maxSize = LaploadHelper::AVATAR_SIZE, array $old = null)
    {
        $this->name                  = $name;
        $this->uploadTo              = $uploadTo;
        $this->maxSize               = $maxSize ?? LaploadHelper::getDefaultAvatarSize();
        $this->multiple              = $multiple;
        $multiple ? $this->rawImages = []   : $this->rawImages = null;
        $old      ? $this->oldImages = $old : $this->oldImages = null;
    }

    public function getUploadTo()
    {
        return empty($this->uploadTo) ? LaploadHelper::getLocalDiskUploadToPath() : $this->uploadTo;
    }

    public function updatingRawImages()
    {
        $this->multiple ? $this->rawImages = [] : $this->rawImages = null;
        $this->images                      = [];
    }

    public function updatedRawImages($value)
    {
        if ($this->multiple) {
            $this->validate(
                ['rawImages.*' => 'image|mimes:'.LaploadHelper::getImageMimes().'|max:'.(int) $this->maxSize.'\''],
            );
        }

        if (!$this->multiple) {
            $this->validate(
                ['rawImages' => 'image|mimes:'.LaploadHelper::getImageMimes().'|max:'.(int) $this->maxSize.'\''],
                []
            );
        }

        // $this->images = $value;
        $this->multiple ? $this->images = $value : $this->images = [$value];

        $this->uploadImages();
    }

    public function uploadImages()
    {
        if (!empty($this->imagesName)) {
            foreach ($this->imagesName as $image) {
                Storage::delete(LaploadHelper::getLocalDiskUploadToPath() . $image);
            }
            $this->imagesName = [];
        }

        foreach ($this->images as $image) {
            $image->store($this->getUploadTo(), $this->disk);
            array_push($this->imagesName, $image->hashName());
        }
        return $this->handleImagesUpdated();
    }

    public function handleRemoveImage($index, $old = false)
    {
        if ($old) {
            $this->emitUp('deleteImage', $this->oldImages[$index]);
            // $this->oldImages[$index]->delete();
            unset($this->oldImages[$index]);
        } else {
            Storage::delete(LaploadHelper::getLocalDiskUploadToPath() . $this->imagesName[$index]);
            unset($this->images[$index]);
            unset($this->imagesName[$index]);
            return $this->handleImagesUpdated();
        }
    }

    public function handleImagesUpdated()
    {
        $this->emit('imagesUpdated', $this->name, $this->imagesName);
    }

    public function render()
    {
        return view(LaploadHelper::getPackageName().'::livewire.uploader');
    }
}
