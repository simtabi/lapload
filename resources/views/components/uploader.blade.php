@props([
    'name',
    'uploadTo',
    'uploadType',
    'multiple'  => false,
    'maxSize'   => LaploadHelper::getDefaultMaxFileSize(),
    'old'       => null,
])

@if($uploadType == 'image')
    <livewire:lapload-image :name="$name" :uploadTo="$uploadTo" :multiple="$multiple" :maxSize="$maxSize" :old="$old" />
@else
    <livewire:lapload-file :name="$name" :uploadTo="$uploadTo" :multiple="$multiple" :maxSize="$maxSize" :old="$old" />
@endif
