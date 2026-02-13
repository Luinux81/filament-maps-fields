<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $bounds = $getBounds();
        $swLat = $bounds['sw_lat'];
        $swLng = $bounds['sw_lng'];
        $neLat = $bounds['ne_lat'];
        $neLng = $bounds['ne_lng'];

        $isLegacyMode = $isLegacyMode();
        $swLatField = $getSouthWestLatField();
        $swLngField = $getSouthWestLngField();
        $neLatField = $getNorthEastLatField();
        $neLngField = $getNorthEastLngField();

        $height = $getHeight();
        $zoom = $getZoom();
        $showLabel = $shouldShowLabel();
        $defaultCenter = $getDefaultCenter();
        $statePath = $getStatePath();
        $isDisabled = $isDisabled();

        // Calculate center for the map
        if ($swLat && $swLng && $neLat && $neLng) {
            $centerLat = ($swLat + $neLat) / 2;
            $centerLng = ($swLng + $neLng) / 2;
        } else {
            $centerLat = $defaultCenter[0];
            $centerLng = $defaultCenter[1];
            // Set default bounds if not set
            $swLat = $swLat ?? $centerLat - 0.01;
            $swLng = $swLng ?? $centerLng - 0.01;
            $neLat = $neLat ?? $centerLat + 0.01;
            $neLng = $neLng ?? $centerLng + 0.01;
        }

        // Enable debug logs with ?map_debug=1 in URL or set APP_DEBUG_MAP=true in .env
        $debugMode = request()->has('map_debug') || config('app.debug_map', false);
    @endphp

    <div
        x-data="{
            bounds: {
                sw_lat: @js($swLat),
                sw_lng: @js($swLng),
                ne_lat: @js($neLat),
                ne_lng: @js($neLng),
            },
            isLegacyMode: @js($isLegacyMode),
            statePath: @js($statePath),
            swLatField: @js($swLatField),
            swLngField: @js($swLngField),
            neLatField: @js($neLatField),
            neLngField: @js($neLngField),
            centerLat: @js($centerLat),
            centerLng: @js($centerLng),
            zoom: @js($zoom),
            isDisabled: @js($isDisabled),
            debug: @js($debugMode),
            map: null,
            rectangle: null,
            drawnItems: null,
            drawControl: null,

            log(...args) {
                if (this.debug) {
                    console.log(...args);
                }
            },

            warn(...args) {
                if (this.debug) {
                    console.warn(...args);
                }
            },

            updateBounds(swLat, swLng, neLat, neLng) {
                this.log('🗺️ Actualizando bounds:', { swLat, swLng, neLat, neLng });

                // Update local state
                this.bounds = {
                    sw_lat: swLat,
                    sw_lng: swLng,
                    ne_lat: neLat,
                    ne_lng: neLng,
                };

                if (this.isLegacyMode) {
                    // Legacy mode: update 4 separate fields
                    if (this.swLatField && this.swLngField && this.neLatField && this.neLngField) {
                        $wire.$set('data.' + this.swLatField, swLat);
                        $wire.$set('data.' + this.swLngField, swLng);
                        $wire.$set('data.' + this.neLatField, neLat);
                        $wire.$set('data.' + this.neLngField, neLng);

                        this.log('✅ Bounds actualizados (Legacy Mode)');
                    }
                } else {
                    // JSON mode: update single field with object
                    $wire.$set(this.statePath, {
                        sw_lat: swLat,
                        sw_lng: swLng,
                        ne_lat: neLat,
                        ne_lng: neLng
                    });

                    this.log('✅ Bounds actualizados (JSON Mode)');
                }
            },

            init() {
                this.log('🗺️ Inicializando MapBoundsField');

                // Wait for Leaflet to be available
                if (typeof L === 'undefined') {
                    this.warn('⚠️ Leaflet no está cargado. Asegúrate de incluir Leaflet.js y Leaflet.draw.js');
                    return;
                }

                // Check if map is already initialized
                const mapContainer = document.getElementById('map-bounds-{{ $statePath }}');
                if (mapContainer._leaflet_id) {
                    this.log('⚠️ Mapa ya inicializado, omitiendo reinicialización');
                    return;
                }

                // Initialize map
                this.map = L.map('map-bounds-{{ $statePath }}').setView([this.centerLat, this.centerLng], this.zoom);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(this.map);

                // Create editable feature group
                this.drawnItems = new L.FeatureGroup();
                this.map.addLayer(this.drawnItems);

                // Configure draw control
                this.drawControl = new L.Control.Draw({
                    draw: {
                        polygon: false,
                        polyline: false,
                        circle: false,
                        circlemarker: false,
                        marker: false,
                        rectangle: false, // Don't allow creating new rectangles
                    },
                    edit: {
                        featureGroup: this.drawnItems,
                        edit: !this.isDisabled,
                        remove: false,
                    }
                });
                this.map.addControl(this.drawControl);

                // Create initial rectangle
                let southWest = L.latLng(this.bounds.sw_lat, this.bounds.sw_lng);
                let northEast = L.latLng(this.bounds.ne_lat, this.bounds.ne_lng);
                let rectangleBounds = L.latLngBounds(southWest, northEast);

                this.rectangle = L.rectangle(rectangleBounds, {
                    color: '#ff7800',
                    weight: 2,
                    fillOpacity: 0.2
                });
                this.drawnItems.addLayer(this.rectangle);

                // Fit map to rectangle
                this.map.fitBounds(rectangleBounds);

                // Enable edit mode if not disabled
                if (!this.isDisabled) {
                    setTimeout(() => {
                        if (this.drawControl._toolbars && this.drawControl._toolbars.edit) {
                            this.drawControl._toolbars.edit._modes.edit.handler.enable();
                        }
                    }, 500);
                }

                // Listen for edit events
                this.map.on('draw:edited', (e) => {
                    e.layers.eachLayer((layer) => {
                        if (layer._leaflet_id === this.rectangle._leaflet_id) {
                            let b = layer.getBounds();
                            this.updateBounds(
                                parseFloat(b.getSouthWest().lat.toFixed(6)),
                                parseFloat(b.getSouthWest().lng.toFixed(6)),
                                parseFloat(b.getNorthEast().lat.toFixed(6)),
                                parseFloat(b.getNorthEast().lng.toFixed(6))
                            );
                        }
                    });
                });

                this.log('✅ MapBoundsField inicializado');
            }
        }"
        wire:key="{{ $statePath }}-map-bounds-field"
        class="space-y-2"
    >
        @if($showLabel)
            <div class="flex items-center gap-2 text-sm">
                <x-filament::icon
                    icon="heroicon-o-map-pin"
                    class="h-5 w-5 text-gray-400 dark:text-gray-500"
                />
                <span
                    class="font-medium text-gray-700 dark:text-gray-300"
                    x-text="
                        (bounds.sw_lat && bounds.sw_lng && bounds.ne_lat && bounds.ne_lng)
                            ? `SW: ${parseFloat(bounds.sw_lat).toFixed(6)}, ${parseFloat(bounds.sw_lng).toFixed(6)} – NE: ${parseFloat(bounds.ne_lat).toFixed(6)}, ${parseFloat(bounds.ne_lng).toFixed(6)}`
                            : 'Selecciona los límites del área'
                    "
                ></span>
            </div>
        @endif

        <div
            id="map-bounds-{{ $statePath }}"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600"
            style="height: {{ $height }}px; position: relative; z-index: 0;"
            wire:ignore
        ></div>
    </div>
</x-dynamic-component>


