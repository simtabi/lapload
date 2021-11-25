@props([
    'name',
    'uploadTo',
    'multiple'  => false,
    'maxSize'   => LaploadHelper::getDefaultAvatarSize(),
    'old'       => null,
])

<livewire:lapload :name="$name" :uploadTo="$uploadTo" :multiple="$multiple" :maxSize="$maxSize" :old="$old" wire:key="{{uniqid() . time() . '_lapload'}}" />
