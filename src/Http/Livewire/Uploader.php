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
    public $oldImages, $multiple, $name, $size;
    public $disk       = 'public';

    protected $messages = [
        'rawImages.*.image' => 'The images format must be type of image.',
        'rawImages.*.mimes' => 'The images format must be :mimes.',
        'rawImages.*.max'   => 'The images must not be greater than :max KB.',
        'rawImages.image'   => 'The image format must be type of image.',
        'rawImages.mimes'   => 'The image format must be :mimes.',
        'rawImages.max'     => 'The image must not be greater than :max KB.',
    ];

    public function mount(string $name, bool $multiple = false, int $size=1024, array $old = null)
    {
        $this->name                  = $name;
        $this->size                  = $size;
        $this->multiple              = $multiple;
        $multiple ? $this->rawImages = []   : $this->rawImages = null;
        $old      ? $this->oldImages = $old : $this->oldImages = null;
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
                ['rawImages.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:'.$this->size.'\''],
            );
        }

        if (!$this->multiple) {
            $this->validate(
                ['rawImages' => 'image|mimes:jpeg,png,jpg,gif,svg|max:'.$this->size.'\''],
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
                Storage::delete(LaploadHelper::getLocalDiskUploadPath() . $image);
            }
            $this->imagesName = array();
        }

        foreach ($this->images as $image) {
            $image->store(laploadHelper::getLocalDiskUploadPath(), $this->disk);
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
            Storage::delete(LaploadHelper::getLocalDiskUploadPath() . $this->imagesName[$index]);
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
        return view(LaploadHelper::getPackageName().'lapload::uploader');
    }
}
