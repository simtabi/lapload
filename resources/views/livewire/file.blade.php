<div class="lapload-container">
    @if (!is_null($oldFiles) && !empty($oldFiles))
        <h4>Current Files</h4>
        <div class="file-wrapper mb-4">
            @foreach ($oldFiles as $index => $file)
                <div
                    class="single-file">
                    <img src="{{ asset('storage/'.LaploadHelper::getPackageName().'/'. $file) }}"
                         width="" alt="">
                    <button type="button"
                            wire:loading.attr="disabled" wire:target="handleRemoveFile({{ $index }}, true)"
                            wire:click.prevent="handleRemoveFile({{ $index }}, true)">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    @endif
    <h4 class=" mb-4">Selected file(s)</h4>
    @if (empty($files))
        <div class="empty-uploader">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <label>No file selected</label>
        </div>
    @else
        <div class="file-wrapper">
            @foreach ($files as $index => $file)
                <div class="single-file mb-4">
                    <img src="{{ $file->temporaryUrl() }}" alt="{{ $file->getClientOriginalName() }}">
                    <label class="file-name">{{ $file->getClientOriginalName() }}</label>
                    <button type="button"
                            wire:loading.attr="disabled" wire:target="handleRemoveFile({{ $index }})"
                            wire:click.prevent="handleRemoveFile({{ $index }})">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    @endif
    <div class="input-wrapper">
        <input id="uploadsInput" type="file" accept="file/*" wire:model="rawFiles" {{ $multiple ? 'multiple' : null }}>
        <div class="drop-zone">
            <div class="" wire:loading wire:target="rawFiles">
                Uploading...
            </div>

            <p wire:loading.remove wire:target="rawFiles" class="text-gray-400">
                @if ($multiple)
                    Drop files anywhere to upload
                    <br />
                    or <br> Select Files
                @else
                    Drop file anywhere to upload
                    <br />
                    or <br> Select File
                @endif
            </p>
        </div>
    </div>
    @error('rawFiles.*') <span class="error-msg">{{ $message }}</span>@enderror
    @error('rawFiles') <span class="error-msg">{{ $message }}</span>@enderror
</div>
