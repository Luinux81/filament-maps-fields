<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $coordinates = $getCoordinates();
        $latitude = $coordinates['latitude'] ?? null;
        $longitude = $coordinates['longitude'] ?? null;
        $height = $getHeight();
        $zoom = $getZoom();
        $showLabel = $shouldShowLabel();
        $statePath = $getStatePath();
    @endphp

    @if($latitude !== null && $longitude !== null)
        <div wire:key="{{ $statePath }}-map-entry">
            <livewire:livewire-map
                :latitude="$latitude"
                :longitude="$longitude"
                :interactive="false"
                :showLabel="$showLabel"
                :showPasteButton="false"
                :height="$height"
                :zoom="$zoom"
                wire:key="{{ $statePath }}-livewire-map"
            />
        </div>
    @else
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('No coordinates available') }}
        </div>
    @endif
</x-dynamic-component>

