<?php

namespace LBCDev\FilamentMapsFields\Forms\Components;

use Filament\Forms\Components\Field;

/**
 * MapField - Filament form field for interactive maps
 *
 * This component integrates the livewire-maps-core component into Filament forms,
 * allowing users to select coordinates on an interactive map.
 *
 * Supports two modes:
 *
 * 1. JSON Mode (Default):
 *    MapField::make('location')
 *    Stores coordinates as JSON: {latitude: X, longitude: Y}
 *
 * 2. Legacy Mode (Backward Compatible):
 *    MapField::make('map')->latitude('latitude')->longitude('longitude')
 *    Stores coordinates in separate fields
 *
 * @package LBCDev\FilamentMapsFields
 */
class MapField extends Field
{
    /**
     * The Blade view for this field
     */
    protected string $view = 'filament-maps-fields::forms.components.map-field';

    /**
     * Name of the latitude field in the form (Legacy Mode only)
     */
    protected ?string $latitudeField = null;

    /**
     * Name of the longitude field in the form (Legacy Mode only)
     */
    protected ?string $longitudeField = null;

    /**
     * Map height in pixels
     */
    protected int $height = 400;

    /**
     * Default zoom level for the map
     */
    protected int $zoom = 15;

    /**
     * Whether to show the paste coordinates button
     */
    protected bool $showPasteButton = false;

    /**
     * Whether to show the coordinates label
     */
    protected bool $showLabel = true;

    /**
     * Whether the map is interactive (clickable)
     */
    protected bool $interactive = true;

    /**
     * Setup the field
     *
     * Configures the field based on the mode:
     * - Legacy Mode: If latitude/longitude fields are configured, mark as dehydrated(false)
     * - JSON Mode: If no latitude/longitude fields, store coordinates as JSON in this field
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Configure dehydration and state management
        $this->afterStateHydrated(function (MapField $component, $state): void {
            // In JSON mode, ensure state is properly formatted
            if (!$component->isLegacyMode()) {
                // If state is already an array with lat/lng, keep it
                // Otherwise, initialize as null
                if (!is_array($state) || !isset($state['latitude'], $state['longitude'])) {
                    $component->state(null);
                }
            }
        });

        $this->dehydrateStateUsing(function (MapField $component, $state) {
            // In Legacy Mode, don't save this field (it's virtual)
            if ($component->isLegacyMode()) {
                return null;
            }

            // In JSON Mode, save the coordinates as JSON
            return $state;
        });
    }

    /**
     * Check if the field is in Legacy Mode
     *
     * Legacy Mode is active when both latitude and longitude fields are configured
     *
     * @return bool
     */
    public function isLegacyMode(): bool
    {
        return $this->latitudeField !== null && $this->longitudeField !== null;
    }

    /**
     * Set the latitude field name
     * 
     * Supports both simple field names ('latitude') and dot notation
     * for nested fields ('location.latitude')
     * 
     * @param string $field The field name
     * @return static
     */
    public function latitude(string $field): static
    {
        $this->latitudeField = $field;
        return $this;
    }

    /**
     * Set the longitude field name
     * 
     * Supports both simple field names ('longitude') and dot notation
     * for nested fields ('location.longitude')
     * 
     * @param string $field The field name
     * @return static
     */
    public function longitude(string $field): static
    {
        $this->longitudeField = $field;
        return $this;
    }

    /**
     * Set the map height in pixels
     * 
     * @param int $height Height in pixels
     * @return static
     */
    public function height(int $height): static
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Set the default zoom level
     * 
     * @param int $zoom Zoom level (typically 1-20)
     * @return static
     */
    public function zoom(int $zoom): static
    {
        $this->zoom = $zoom;
        return $this;
    }

    /**
     * Show or hide the paste coordinates button
     * 
     * @param bool $show Whether to show the button
     * @return static
     */
    public function showPasteButton(bool $show = true): static
    {
        $this->showPasteButton = $show;
        return $this;
    }

    /**
     * Show or hide the coordinates label
     * 
     * @param bool $show Whether to show the label
     * @return static
     */
    public function showLabel(bool $show = true): static
    {
        $this->showLabel = $show;
        return $this;
    }

    /**
     * Set whether the map is interactive
     * 
     * When false, the map is read-only and cannot be clicked
     * 
     * @param bool $interactive Whether the map is interactive
     * @return static
     */
    public function interactive(bool $interactive = true): static
    {
        $this->interactive = $interactive;
        return $this;
    }

    /**
     * Make the field read-only (non-interactive)
     * 
     * This is an alias for interactive(false) to maintain compatibility
     * with Filament's standard API
     * 
     * @param bool $condition Whether to make it read-only
     * @return static
     */
    public function readOnly(bool $condition = true): static
    {
        $this->interactive = !$condition;
        return $this;
    }

    /**
     * Get the latitude field name
     * 
     * @return string|null
     */
    public function getLatitudeField(): ?string
    {
        return $this->latitudeField;
    }

    /**
     * Get the longitude field name
     * 
     * @return string|null
     */
    public function getLongitudeField(): ?string
    {
        return $this->longitudeField;
    }

    /**
     * Get the map height
     * 
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Get the zoom level
     * 
     * @return int
     */
    public function getZoom(): int
    {
        return $this->zoom;
    }

    /**
     * Check if paste button should be shown
     * 
     * @return bool
     */
    public function shouldShowPasteButton(): bool
    {
        return $this->showPasteButton;
    }

    /**
     * Check if label should be shown
     * 
     * @return bool
     */
    public function shouldShowLabel(): bool
    {
        return $this->showLabel;
    }

    /**
     * Check if map is interactive
     * 
     * @return bool
     */
    public function isInteractive(): bool
    {
        return $this->interactive;
    }

    /**
     * Get the current coordinates from the form state
     *
     * This method safely retrieves the latitude and longitude values
     * from the form, supporting both modes:
     *
     * JSON Mode:
     * - Reads from the field's own state: {latitude: X, longitude: Y}
     *
     * Legacy Mode:
     * - Reads from separate fields using dot notation support
     * - Examples: 'latitude', 'longitude' or 'location.latitude', 'location.longitude'
     *
     * @return array{latitude: float|null, longitude: float|null}
     */
    public function getCoordinates(): array
    {
        try {
            // JSON Mode: Read from this field's state
            if (!$this->isLegacyMode()) {
                $state = $this->getState();

                // If state is an array with latitude and longitude
                if (is_array($state) && isset($state['latitude'], $state['longitude'])) {
                    return [
                        'latitude' => $this->normalizeCoordinate($state['latitude']),
                        'longitude' => $this->normalizeCoordinate($state['longitude']),
                    ];
                }

                // No coordinates set yet
                return [
                    'latitude' => null,
                    'longitude' => null,
                ];
            }

            // Legacy Mode: Read from separate fields
            $container = $this->getContainer();

            if (!$container) {
                return [
                    'latitude' => null,
                    'longitude' => null,
                ];
            }

            $state = null;

            // In edit mode, try to get from the record
            if (method_exists($container, 'getRecord') && $record = $container->getRecord()) {
                $state = $record->toArray();
            }
            // In create mode, get the raw state
            else {
                $state = method_exists($container, 'getRawState')
                    ? $container->getRawState()
                    : $container->getState();
            }

            // Use Laravel's data_get helper to support dot notation
            $latitude = data_get($state, $this->latitudeField);
            $longitude = data_get($state, $this->longitudeField);

            // Normalize the values
            $latitude = $this->normalizeCoordinate($latitude);
            $longitude = $this->normalizeCoordinate($longitude);

            return [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];
        } catch (\Throwable) {
            // If anything fails, safely return null coordinates
            // This prevents the form from breaking if there are issues
            return [
                'latitude' => null,
                'longitude' => null,
            ];
        }
    }

    /**
     * Normalize a coordinate value to float or null
     * 
     * Handles various input types and converts them to the appropriate format:
     * - null or empty string → null
     * - float → returned as-is
     * - numeric string → converted to float
     * - non-numeric → null
     * 
     * @param mixed $value The value to normalize
     * @return float|null
     */
    protected function normalizeCoordinate(mixed $value): ?float
    {
        // Null or empty string becomes null
        if ($value === null || $value === '') {
            return null;
        }

        // Already a float, return as-is
        if (is_float($value)) {
            return $value;
        }

        // Numeric string, convert to float
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Invalid value, return null
        return null;
    }
}
