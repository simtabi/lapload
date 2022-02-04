<?php

namespace Simtabi\Lapload\Http\Livewire;

use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Livewire\Component;
use Livewire\WithFileUploads;
use Simtabi\Lapload\Helpers\LaploadHelper;

class Uploader extends Component
{
    use WithFileUploads;

    public $rawFiles;
    public $files        = [];
    public $fileNames    = [];
    public $oldFiles, $multiple, $name, $maxSize, $uploadTo, $uploadType, $label, $current;
    public $disk         = 'local';
    public $imageQuality = 65;
    public $width, $height;
    public $refreshList;

    public function getListeners()
    {
        return [
            'refreshComponent'  => '$refresh',
        ];
    }

    public function getMessages(): array
    {
       return match ($this->uploadType) {
            'image' => [
                'rawImages.*.image' => 'The upload file must be an image.',
                'rawImages.*.mimes' => 'The upload must be of :mimes type.',
                'rawImages.*.max'   => 'The images must not be greater than :max KB.',
                'rawImages.image'   => 'The upload must be of :mimes type.',
                'rawImages.mimes'   => 'The image format must be :mimes.',
                'rawImages.max'     => 'The image size must not be greater than :max KB.',
            ],
            'file'  => [
                'rawFiles.*.file'  => 'The upload file must be an file.',
                'rawFiles.*.mimes' => 'The upload must be of :mimes type.',
                'rawFiles.*.max'   => 'The files must not be greater than :max KB.',
                'rawFiles.file'    => 'The upload must be of :mimes type.',
                'rawFiles.mimes'   => 'The file format must be :mimes.',
                'rawFiles.max'     => 'The file size must not be greater than :max KB.',
            ],
            default => [],
        };
    }

    public function mount(string $name, string $uploadTo, string $uploadType, string $label, ?string $current = null, ?array $refreshList = [], bool $multiple = false, int $maxSize = LaploadHelper::MAX_FILE_SIZE, array $old = null, $disk = 'local', $imageQuality = 65, $width = 720, $height = null)
    {
        $this->name                 = $name;
        $this->uploadTo             = $uploadTo;
        $this->uploadType           = $uploadType;
        $this->label                = $label;
        $this->maxSize              = $maxSize ?? LaploadHelper::getDefaultMaxFileSize();
        $this->multiple             = $multiple;
        $this->disk                 = $disk;
        $this->imageQuality         = $imageQuality;
        $this->refreshList          = $refreshList;
        $multiple ? $this->rawFiles = []   : $this->rawFiles = null;
        $old      ? $this->oldFiles = $old : $this->oldFiles = null;

        if ($width) {
            $this->setWidth($width);
        }

        if ($width) {
            $this->setHeight($height);
        }

        $this->setLabel($label);

        $this->setCurrent($current);
    }

    public function setWidth($width): self
    {
        $this->width = $width;

        return $this;
    }

    public function setHeight($height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getUploadTo()
    {
        return LaploadHelper::getLocalUploadPath($this->uploadTo);
    }

    public function updatingRawFiles()
    {
        $this->multiple ? $this->rawFiles = [] : $this->rawFiles = null;
        $this->files                      = [];
    }

    public function setLabel($label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setCurrent($current): self
    {
        if (!is_array($current) && is_string($current)) {
            $current = explode(",", $current);
        }
        $this->current = $current;
        
        return $this;
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function getTitle()
    {
        return $this->name . ' ' . $this->uploadType . ($this->multiple ? '(s)' : '');
    }

    public function updatedRawFiles($value)
    {
        if ($this->multiple) {
            $this->validate(
                ['rawFiles.*' => $this->uploadType . '|mimes:'.LaploadHelper::getFileMimeTypes() .'|max:'.$this->maxSize],
            );
        }

        if (!$this->multiple) {
            $this->validate(
                ['rawFiles.*' => $this->uploadType . '|mimes:'.LaploadHelper::getFileMimeTypes() .'|max:'.$this->maxSize],
                []
            );
        }

        // $this->files = $value;
        $this->multiple ? $this->files = $value : $this->files = array($value);

        $this->uploadFiles();
    }


    public function uploadFiles()
    {
        if (!empty($this->fileNames)) {
            foreach ($this->fileNames as $file) {
                LaploadHelper::getStorage($this->disk)->delete($this->getUploadTo() . $file);
            }
            $this->fileNames = [];
        }

        foreach ($this->files as $file) {
            $file->store($this->getUploadTo());
            $this->cropImage($file);
            array_push($this->fileNames, $file->hashName());
        }
        return $this->handleUploadedFiles();
    }

    public function handleRemoveFile($index, $old = false)
    {
        if ($old) {
            $this->emitUp('deleteFile', $this->oldFiles[$index]);
            // $this->oldFiles[$index]->delete();
            unset($this->oldFiles[$index]);
        } else {
            LaploadHelper::getStorage($this->disk)->delete($this->getUploadTo() . $this->fileNames[$index]);
            unset($this->files[$index]);
            unset($this->fileNames[$index]);
            return $this->handleUploadedFiles();
        }
        $this->emitSelf('refreshComponent');
    }

    public function handleUploadedFiles()
    {
        $this->emit('uploadedFiles', $this->name, $this->fileNames);

        if (!empty($this->refreshList) && is_array($this->refreshList)) {
            foreach ($this->refreshList as  $item) {
                $this->refreshAnotherComponent($item);
            }
        }
    }

    public function cropImage($image)
    {
        $img = (new ImageManager())->make($image->getRealPath())->encode($image->getClientOriginalExtension(), $this->imageQuality)->fit($this->width, $this->height, function ($c) {
            $c->aspectRatio();
            $c->upsize();
        });
        $img->stream();

        LaploadHelper::getStorage($this->disk)->put('public/'. $this->uploadTo . '/' . $image->hashName(), $img, 'public');
    }

    public function refreshComponent(...$args){
        $this->emit('refreshComponent', $args);
    }

    public function refreshAnotherComponent(string $component, ...$args){
        $this->emitTo($component, 'refreshComponent', $args);
    }

    public function render()
    {
        return view(LaploadHelper::getPackageName().'::livewire.uploader');
    }

}
