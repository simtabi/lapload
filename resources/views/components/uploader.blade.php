@props([
    'name',
    'uploadTo',
    'uploadType',
    'multiple'     => false,
    'maxSize'      => LaploadHelper::getDefaultMaxFileSize(),
    'old'          => null,
    'disk'         => 'local',
    'imageQuality' => 65,
    'width'        => 760,
    'height'       => null,
    'refreshList'  => null,
])

<livewire:lapload
    :name="$name"
    :uploadTo="$uploadTo"
    :uploadType="$uploadType"
    :multiple="$multiple"
    :maxSize="$maxSize"
    :old="$old"
    :disk="$disk"
    :imageQuality="$imageQuality"
    :width="$width"
    :height="$height"
    :refreshList="$refreshList"
/>
