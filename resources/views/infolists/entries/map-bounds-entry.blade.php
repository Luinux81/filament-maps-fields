<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $bounds = $getBounds();
        $height = $getHeight();
        $zoom = $getZoom();
        $showLabel = $shouldShowLabel();
        $statePath = $getStatePath();
    @endphp

    @if($bounds !== null)
        @php
            $swLat = $bounds['sw_lat'];
            $swLng = $bounds['sw_lng'];
            $neLat = $bounds['ne_lat'];
            $neLng = $bounds['ne_lng'];
            
            // Calculate center
            $centerLat = ($swLat + $neLat) / 2;
            $centerLng = ($swLng + $neLng) / 2;
        @endphp

        <div
            x-data="{
                bounds: {
                    sw_lat: @js($swLat),
                    sw_lng: @js($swLng),
                    ne_lat: @js($neLat),
                    ne_lng: @js($neLng),
                },
                centerLat: @js($centerLat),
                centerLng: @js($centerLng),
                zoom: @js($zoom),
                map: null,
                rectangle: null,

                init() {
                    // Wait for Leaflet to be available
                    if (typeof L === 'undefined') {
                        console.warn('⚠️ Leaflet no está cargado. Asegúrate de incluir Leaflet.js');
                        return;
                    }

                    // Initialize map
                    this.map = L.map('map-bounds-entry-{{ $statePath }}', {
                        dragging: false,
                        touchZoom: false,
                        scrollWheelZoom: false,
                        doubleClickZoom: false,
                        boxZoom: false,
                        keyboard: false,
                        zoomControl: false
                    }).setView([this.centerLat, this.centerLng], this.zoom);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(this.map);

                    // Create rectangle
                    let southWest = L.latLng(this.bounds.sw_lat, this.bounds.sw_lng);
                    let northEast = L.latLng(this.bounds.ne_lat, this.bounds.ne_lng);
                    let rectangleBounds = L.latLngBounds(southWest, northEast);

                    this.rectangle = L.rectangle(rectangleBounds, {
                        color: '#3b82f6',
                        weight: 2,
                        fillOpacity: 0.2
                    }).addTo(this.map);

                    // Fit map to rectangle
                    this.map.fitBounds(rectangleBounds);
                }
            }"
            wire:key="{{ $statePath }}-map-bounds-entry"
            class="space-y-2"
        >
            @if($showLabel)
                <div class="flex items-center gap-2 text-sm">
                    <x-filament::icon
                        icon="heroicon-o-map-pin"
                        class="h-5 w-5 text-gray-400 dark:text-gray-500"
                    />
                    <span class="font-medium text-gray-700 dark:text-gray-300">
                        SW: {{ number_format($swLat, 6) }}, {{ number_format($swLng, 6) }} – 
                        NE: {{ number_format($neLat, 6) }}, {{ number_format($neLng, 6) }}
                    </span>
                </div>
            @endif

            <div 
                id="map-bounds-entry-{{ $statePath }}" 
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600"
                style="height: {{ $height }}px;"
                wire:ignore
            ></div>
        </div>
    @else
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('No bounds available') }}
        </div>
    @endif
</x-dynamic-component>

