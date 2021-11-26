<?php

namespace Simtabi\Lapload\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Simtabi\Lapload\Helpers\LaploadHelper;

class FileUploader extends Component
{
    use WithFileUploads;

    public $rawFiles;
    public $files     = [];
    public $fileNames = [];
    public $oldFiles, $multiple, $name, $maxSize, $uploadTo;

    protected $messages = [
        'rawFiles.*.file'  => 'The upload file must be an file.',
        'rawFiles.*.mimes' => 'The upload must be of :mimes type.',
        'rawFiles.*.max'   => 'The files must not be greater than :max KB.',
        'rawFiles.file'    => 'The upload must be of :mimes type.',
        'rawFiles.mimes'   => 'The file format must be :mimes.',
        'rawFiles.max'     => 'The file size must not be greater than :max KB.',
    ];

    public function mount(string $name, string $uploadTo, bool $multiple = false, int $maxSize = LaploadHelper::MAX_FILE_SIZE, array $old = null)
    {
        $this->name                 = $name;
        $this->uploadTo             = $uploadTo;
        $this->maxSize              = $maxSize ?? LaploadHelper::getDefaultMaxFileSize();
        $this->multiple             = $multiple;
        $multiple ? $this->rawFiles = []   : $this->rawFiles = null;
        $old      ? $this->oldFiles = $old : $this->oldFiles = null;
    }

    public function getUploadTo()
    {
        return LaploadHelper::getLocalUploadPath($this->uploadTo);
    }

    public function updatingRawFiles()
    {
        $this->multiple ? $this->rawFiles=[] : $this->rawFiles = null;
        $this->files = array();
    }

    public function updatedRawFiles($value)
    {
        if ($this->multiple) {
            $this->validate(
                ['rawFiles.*' => 'file|mimes:'.LaploadHelper::getFileMimeTypes() .'|max:'.$this->maxSize],
            );
        }

        if (!$this->multiple) {
            $this->validate(
                ['rawFiles.*' => 'file|mimes:'.LaploadHelper::getFileMimeTypes() .'|max:'.$this->maxSize],
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
                Storage::delete($this->getUploadTo() . $file);
            }
            $this->fileNames = array();
        }

        foreach ($this->files as $file) {
            $file->store($this->getUploadTo());
            array_push($this->fileNames, $file->hashName());
        }
        return $this->handleUpdatedFiles();
    }

    public function handleRemoveFile($index, $old = false)
    {
        if ($old) {
            $this->emitUp('deleteItem', $this->oldFiles[$index]);
            // $this->oldFiles[$index]->delete();
            unset($this->oldFiles[$index]);
        } else {
            Storage::delete($this->getUploadTo() . $this->fileNames[$index]);
            unset($this->files[$index]);
            unset($this->fileNames[$index]);
            return $this->handleUpdatedFiles();
        }
    }

    public function handleUpdatedFiles()
    {
        $this->emit('updatedItems', $this->name, $this->fileNames);
    }

    public function render()
    {
        return view(LaploadHelper::getPackageName().'::livewire.file');
    }
}
