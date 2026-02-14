<?php

namespace LBCDev\FilamentMapsFields\Forms\Components;

use Filament\Forms\Components\Field;

/**
 * MapBoundsField - Select map bounds (rectangular area)
 *
 * Allows users to draw and edit a rectangular area on a map.
 * Requires Leaflet.js and Leaflet.draw plugin.
 *
 * Supports two modes:
 * - JSON Mode (default) Stores bounds as {sw_lat, sw_lng, ne_lat, ne_lng} in a single field
 * - Legacy Mode: Stores bounds in 4 separate fields
 *
 * @example JSON Mode (recommended)
 * MapBoundsField::make('bounds')
 *
 * @example Legacy Mode
 * MapBoundsField::make('area_bounds')
 *     ->southWestLat('sw_lat')
 *     ->southWestLng('sw_lng')
 *     ->northEastLat('ne_lat')
 *     ->northEastLng('ne_lng')
 */
class MapBoundsField extends Field
{
    protected string $view = 'filament-maps-fields::forms.components.map-bounds-field';

    protected string|null $southWestLatField = null;
    protected string|null $southWestLngField = null;
    protected string|null $northEastLatField = null;
    protected string|null $northEastLngField = null;
    protected int $height = 400;
    protected int $zoom = 13;
    protected bool $showLabel = true;
    protected array $defaultCenter = [36.9990019, -6.5478919]; // Default center (Spain)

    /**
     * Check if the field is in legacy mode (4 separate fields)
     */
    public function isLegacyMode(): bool
    {
        return $this->southWestLatField !== null
            && $this->southWestLngField !== null
            && $this->northEastLatField !== null
            && $this->northEastLngField !== null;
    }

    /**
     * Setup the field with dual mode support
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->default(null);

        // Configure based on mode
        $this->afterStateHydrated(function (MapBoundsField $component, $state) {
            if ($this->isLegacyMode()) {
                // Legacy mode: field is virtual, don't save it
                $component->dehydrated(false);
            } else {
                // JSON mode: ensure state is properly formatted
                if (is_array($state) && !empty($state)) {
                    $component->state($state);
                }
            }
        });

        // Handle dehydration for JSON mode
        $this->dehydrateStateUsing(function ($state) {
            if ($this->isLegacyMode()) {
                // Legacy mode: don't save anything (handled by separate fields)
                return null;
            }

            // JSON mode: save as array
            if (is_array($state) && !empty($state)) {
                return $state;
            }

            return null;
        });

        // Validation
        $this->rule(function () {
            return function (string $attribute, $value, \Closure $fail) {
                if (!$this->isRequired()) {
                    return;
                }

                if ($this->isLegacyMode()) {
                    // Legacy mode validation
                    $container = $this->getContainer();
                    if (!$container) {
                        return;
                    }

                    $state = method_exists($container, 'getRawState')
                        ? $container->getRawState()
                        : $container->getState();

                    $swLat = data_get($state, $this->southWestLatField);
                    $swLng = data_get($state, $this->southWestLngField);
                    $neLat = data_get($state, $this->northEastLatField);
                    $neLng = data_get($state, $this->northEastLngField);

                    if (
                        $swLat === null || $swLat === '' ||
                        $swLng === null || $swLng === '' ||
                        $neLat === null || $neLat === '' ||
                        $neLng === null || $neLng === ''
                    ) {
                        $fail('El campo de límites es requerido.');
                    }
                } else {
                    // JSON mode validation
                    if (!is_array($value) || empty($value)) {
                        $fail('El campo de límites es requerido.');
                        return;
                    }

                    if (
                        !isset($value['sw_lat']) || !isset($value['sw_lng']) ||
                        !isset($value['ne_lat']) || !isset($value['ne_lng'])
                    ) {
                        $fail('El campo de límites es requerido.');
                    }
                }
            };
        });
    }

    /**
     * Set the south-west latitude field name
     * Supports dot notation for nested fields (e.g., 'bounds.sw_lat')
     */
    public function southWestLat(string $field): static
    {
        $this->southWestLatField = $field;
        return $this;
    }

    /**
     * Set the south-west longitude field name
     * Supports dot notation for nested fields (e.g., 'bounds.sw_lng')
     */
    public function southWestLng(string $field): static
    {
        $this->southWestLngField = $field;
        return $this;
    }

    /**
     * Set the north-east latitude field name
     * Supports dot notation for nested fields (e.g., 'bounds.ne_lat')
     */
    public function northEastLat(string $field): static
    {
        $this->northEastLatField = $field;
        return $this;
    }

    /**
     * Set the north-east longitude field name
     * Supports dot notation for nested fields (e.g., 'bounds.ne_lng')
     */
    public function northEastLng(string $field): static
    {
        $this->northEastLngField = $field;
        return $this;
    }

    /**
     * Set the map height in pixels
     */
    public function height(int $height): static
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Set the map zoom level
     */
    public function zoom(int $zoom): static
    {
        $this->zoom = $zoom;
        return $this;
    }

    /**
     * Set whether to show the label
     */
    public function showLabel(bool $show = true): static
    {
        $this->showLabel = $show;
        return $this;
    }

    /**
     * Set the default center coordinates [lat, lng]
     */
    public function defaultCenter(float $lat, float $lng): static
    {
        $this->defaultCenter = [$lat, $lng];
        return $this;
    }

    /**
     * Get the south-west latitude field name
     */
    public function getSouthWestLatField(): ?string
    {
        return $this->southWestLatField;
    }

    /**
     * Get the south-west longitude field name
     */
    public function getSouthWestLngField(): ?string
    {
        return $this->southWestLngField;
    }

    /**
     * Get the north-east latitude field name
     */
    public function getNorthEastLatField(): ?string
    {
        return $this->northEastLatField;
    }

    /**
     * Get the north-east longitude field name
     */
    public function getNorthEastLngField(): ?string
    {
        return $this->northEastLngField;
    }

    /**
     * Get the map height
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Get the map zoom level
     */
    public function getZoom(): int
    {
        return $this->zoom;
    }

    /**
     * Get whether to show the label
     */
    public function shouldShowLabel(): bool
    {
        return $this->showLabel;
    }

    /**
     * Get the default center coordinates
     */
    public function getDefaultCenter(): array
    {
        return $this->defaultCenter;
    }

    /**
     * Get the current bounds from the form
     * Supports both JSON mode and Legacy mode
     */
    public function getBounds(): array
    {
        if ($this->isLegacyMode()) {
            // Legacy mode: read from separate fields
            $container = $this->getContainer();

            return [
                'sw_lat' => data_get($container->getState(), $this->southWestLatField),
                'sw_lng' => data_get($container->getState(), $this->southWestLngField),
                'ne_lat' => data_get($container->getState(), $this->northEastLatField),
                'ne_lng' => data_get($container->getState(), $this->northEastLngField),
            ];
        }

        // JSON mode: read from field state
        $state = $this->getState();

        if (is_array($state) && !empty($state)) {
            return [
                'sw_lat' => $state['sw_lat'] ?? null,
                'sw_lng' => $state['sw_lng'] ?? null,
                'ne_lat' => $state['ne_lat'] ?? null,
                'ne_lng' => $state['ne_lng'] ?? null,
            ];
        }

        return [
            'sw_lat' => null,
            'sw_lng' => null,
            'ne_lat' => null,
            'ne_lng' => null,
        ];
    }
}
