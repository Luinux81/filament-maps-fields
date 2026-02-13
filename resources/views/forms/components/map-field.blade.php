<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        // Get current coordinates from the form
        $coordinates = $getCoordinates();
        $latitude = $coordinates['latitude'];
        $longitude = $coordinates['longitude'];
        
        // Get field configuration
        $latitudeField = $getLatitudeField();
        $longitudeField = $getLongitudeField();
        $height = $getHeight();
        $zoom = $getZoom();
        $showPasteButton = $shouldShowPasteButton();
        $showLabel = $shouldShowLabel();
        $interactive = $isInteractive();
        $isDisabled = $isDisabled();
        $statePath = $getStatePath();
        
        // Debug mode can be enabled with ?map_debug=1 in URL or APP_DEBUG_MAP=true in .env
        $debugMode = request()->has('map_debug') || config('app.debug_map', false);
    @endphp

    <div
        x-data="{
            latitude: @js($latitude),
            longitude: @js($longitude),
            latitudeField: @js($latitudeField),
            longitudeField: @js($longitudeField),
            debug: @js($debugMode),

            /**
             * Log a message to console if debug mode is enabled
             */
            log(...args) {
                if (this.debug) {
                    console.log('[MapField Debug]', ...args);
                }
            },

            /**
             * Log a warning to console if debug mode is enabled
             */
            warn(...args) {
                if (this.debug) {
                    console.warn('[MapField Warning]', ...args);
                }
            },

            /**
             * Update the form fields with new coordinates
             * 
             * This method handles both simple field names ('latitude')
             * and dot notation for nested fields ('location.latitude')
             */
            updateCoordinates(lat, lng) {
                if (!this.latitudeField || !this.longitudeField) {
                    this.warn('Latitude or longitude field not configured');
                    return;
                }

                // Build the full path for Livewire's $set method
                // The 'data.' prefix is required by Filament forms
                const latPath = 'data.' + this.latitudeField;
                const lngPath = 'data.' + this.longitudeField;

                this.log('Updating coordinates:', {
                    latPath,
                    lngPath,
                    latitude: lat,
                    longitude: lng
                });

                // Update the form fields via Livewire
                $wire.$set(latPath, lat);
                $wire.$set(lngPath, lng);

                // Update local state for reactivity
                this.latitude = lat;
                this.longitude = lng;

                this.log('Coordinates updated successfully');
            },

            /**
             * Initialize the component
             * 
             * Sets up event listeners for coordinate updates from the map component
             */
            init() {
                // Listen for coordinate updates via window events
                // This is the primary method used by livewire-maps-core
                window.addEventListener('map-coordinates-updated', (event) => {
                    const data = event.detail;
                    
                    this.log('Received window event:', data);

                    if (data && data.latitude !== undefined && data.longitude !== undefined) {
                        this.updateCoordinates(data.latitude, data.longitude);
                    } else {
                        this.warn('Invalid event data:', data);
                    }
                });

                // Also listen for Livewire events (for backward compatibility)
                Livewire.on('map-coordinates-updated', (eventData) => {
                    this.log('Received Livewire event (raw):', eventData);

                    // Livewire 3 wraps event parameters in an array
                    const data = Array.isArray(eventData) ? eventData[0] : eventData;

                    this.log('Extracted event data:', data);

                    if (data && data.latitude !== undefined && data.longitude !== undefined) {
                        this.updateCoordinates(data.latitude, data.longitude);
                    } else {
                        this.warn('Invalid Livewire event data:', {
                            hasLatitudeField: !!this.latitudeField,
                            hasLongitudeField: !!this.longitudeField,
                            data
                        });
                    }
                });
            }
        }"
        wire:key="{{ $statePath }}-map-field"
    >
        {{-- 
            Render the livewire-maps-core component
            
            Note: We use 'livewire:livewire-map' which is the correct component name
            from the lbcdev/livewire-maps-core package
        --}}
        <livewire:livewire-map
            :latitude="$latitude"
            :longitude="$longitude"
            :interactive="$interactive && !$isDisabled"
            :showLabel="$showLabel"
            :showPasteButton="$showPasteButton"
            :height="$height"
            :zoom="$zoom"
            wire:key="{{ $statePath }}-livewire-map"
        />
    </div>
</x-dynamic-component>