<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $coordinates = $getCoordinates();
        $latitude = $coordinates['latitude'];
        $longitude = $coordinates['longitude'];
        $latitudeField = $getLatitudeField();
        $longitudeField = $getLongitudeField();
        $height = $getHeight();
        $zoom = $getZoom();
        $showPasteButton = $shouldShowPasteButton();
        $showLabel = $shouldShowLabel();
        $interactive = $isInteractive();
        $statePath = $getStatePath();
        $isDisabled = $isDisabled();
        // Enable debug logs with ?map_debug=1 in URL or set APP_DEBUG_MAP=true in .env
        $debugMode = request()->has('map_debug') || config('app.debug_map', false);
    @endphp

    <div
        x-data="{
            latitude: @js($latitude),
            longitude: @js($longitude),
            latitudeField: @js($latitudeField),
            longitudeField: @js($longitudeField),
            debug: @js($debugMode),

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

            updateCoordinates(lat, lng) {
                if (this.latitudeField && this.longitudeField) {
                    // Support both simple fields ('latitude') and nested fields ('ubicacion.latitud')
                    // The dot notation is automatically handled by Livewire's $set method
                    const latPath = 'data.' + this.latitudeField;
                    const lngPath = 'data.' + this.longitudeField;

                    this.log('🗺️ Actualizando paths:', { latPath, lngPath });
                    this.log('🗺️ Valores a guardar:', { latitude: lat, longitude: lng });

                    $wire.$set(latPath, lat);
                    $wire.$set(lngPath, lng);

                    // Update local state
                    this.latitude = lat;
                    this.longitude = lng;

                    this.log('✅ Coordenadas actualizadas correctamente');
                }
            },

            init() {
                // Listen for coordinate updates from the livewire map component
                window.addEventListener('map-coordinates-updated', (event) => {
                    const data = event.detail;
                    this.log('🗺️ Evento window map-coordinates-updated recibido:', data);

                    if (data && data.latitude !== undefined && data.longitude !== undefined) {
                        this.updateCoordinates(data.latitude, data.longitude);
                    }
                });

                // Also listen for Livewire events (for backward compatibility)
                Livewire.on('map-coordinates-updated', (eventData) => {
                    this.log('🗺️ Evento Livewire map-coordinates-updated recibido (raw):', eventData);

                    // Livewire 3 wraps parameters in an array, so we need to extract the first element
                    const data = Array.isArray(eventData) ? eventData[0] : eventData;

                    this.log('🗺️ Datos extraídos:', data);
                    this.log('🗺️ Campos configurados:', {
                        latitudeField: this.latitudeField,
                        longitudeField: this.longitudeField
                    });

                    if (data && data.latitude !== undefined && data.longitude !== undefined) {
                        this.updateCoordinates(data.latitude, data.longitude);
                    } else {
                        this.warn('⚠️ Faltan datos:', {
                            hasLatitudeField: !!this.latitudeField,
                            hasLongitudeField: !!this.longitudeField,
                            hasData: !!data
                        });
                    }
                });
            }
        }"
        wire:key="{{ $statePath }}-map-field"
    >
        <livewire:lbcdev-map
            :latitude="$latitude"
            :longitude="$longitude"
            :interactive="$interactive && !$isDisabled"
            :showLabel="$showLabel"
            :showPasteButton="$showPasteButton"
            :height="$height"
            :zoom="$zoom"
            wire:key="{{ $statePath }}-lbcdev-map"
        />
    </div>
</x-dynamic-component>

