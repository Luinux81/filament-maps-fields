<?php

namespace Lbcdev\FilamentMapField\Forms\Components;

use Filament\Forms\Components\Field;

class MapField extends Field
{
    protected string $view = 'filament-map-field::forms.components.map-field';

    protected string|null $latitudeField = null;
    protected string|null $longitudeField = null;
    protected int $height = 400;
    protected int $zoom = 15;
    protected bool $showPasteButton = false;
    protected bool $showLabel = true;
    protected bool $interactive = true;

    /**
     * Setup the field to not require validation
     * This prevents issues when using dot notation with nested fields
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Set the latitude field name
     */
    public function latitude(string $field): static
    {
        $this->latitudeField = $field;
        return $this;
    }

    /**
     * Set the longitude field name
     */
    public function longitude(string $field): static
    {
        $this->longitudeField = $field;
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
     * Set the default zoom level
     */
    public function zoom(int $zoom): static
    {
        $this->zoom = $zoom;
        return $this;
    }

    /**
     * Show the paste coordinates button
     */
    public function showPasteButton(bool $show = true): static
    {
        $this->showPasteButton = $show;
        return $this;
    }

    /**
     * Show the coordinates label
     */
    public function showLabel(bool $show = true): static
    {
        $this->showLabel = $show;
        return $this;
    }

    /**
     * Set if the map is interactive
     */
    public function interactive(bool $interactive = true): static
    {
        $this->interactive = $interactive;
        return $this;
    }

    /**
     * Make the field read-only (non-interactive)
     * This is an alias for interactive(false) to maintain compatibility with Filament's standard API
     */
    public function readOnly(bool $condition = true): static
    {
        $this->interactive = !$condition;
        return $this;
    }

    /**
     * Get the latitude field name
     */
    public function getLatitudeField(): ?string
    {
        return $this->latitudeField;
    }

    /**
     * Get the longitude field name
     */
    public function getLongitudeField(): ?string
    {
        return $this->longitudeField;
    }

    /**
     * Get the map height
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Get the zoom level
     */
    public function getZoom(): int
    {
        return $this->zoom;
    }

    /**
     * Check if paste button should be shown
     */
    public function shouldShowPasteButton(): bool
    {
        return $this->showPasteButton;
    }

    /**
     * Check if label should be shown
     */
    public function shouldShowLabel(): bool
    {
        return $this->showLabel;
    }

    /**
     * Check if map is interactive
     */
    public function isInteractive(): bool
    {
        return $this->interactive;
    }

    /**
     * Get the current coordinates from the form
     *
     * Supports both simple field names and dot notation for nested fields:
     * - Simple: 'latitude', 'longitude'
     * - Nested: 'ubicacion.latitud', 'ubicacion.longitud'
     */
    // public function getCoordinates(): array
    // {
    //     $container = $this->getContainer();

    //     if ($this->latitudeField && $this->longitudeField) {
    //         return [
    //             'latitude' => data_get($container->getState(), $this->latitudeField),
    //             'longitude' => data_get($container->getState(), $this->longitudeField),
    //         ];
    //     }

    //     return [
    //         'latitude' => null,
    //         'longitude' => null,
    //     ];
    // }
    public function getCoordinates(): array
    {
        try {
            if (!$this->latitudeField || !$this->longitudeField) {
                return [
                    'latitude' => null,
                    'longitude' => null,
                ];
            }

            $container = $this->getContainer();

            if (!$container) {
                return [
                    'latitude' => null,
                    'longitude' => null,
                ];
            }

            // Try to get state without triggering validation
            $state = null;

            // First, try to get from the record if it exists (edit mode)
            if (method_exists($container, 'getRecord') && $record = $container->getRecord()) {
                $state = $record->toArray();
            }
            // Otherwise, try to get the raw state (create mode)
            else {
                // Use getRawState if available, otherwise getState
                $state = method_exists($container, 'getRawState')
                    ? $container->getRawState()
                    : $container->getState();
            }

            $latitude = data_get($state, $this->latitudeField);
            $longitude = data_get($state, $this->longitudeField);

            // Convert empty strings to null and numeric strings to float
            $latitude = $this->normalizeCoordinate($latitude);
            $longitude = $this->normalizeCoordinate($longitude);

            return [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];
        } catch (\Throwable $e) {
            // If anything fails, return null coordinates
            return [
                'latitude' => null,
                'longitude' => null,
            ];
        }
    }

    /**
     * Normalize a coordinate value to float or null
     */
    protected function normalizeCoordinate($value): ?float
    {
        // If null or empty string, return null
        if ($value === null || $value === '') {
            return null;
        }

        // If already a float, return as is
        if (is_float($value)) {
            return $value;
        }

        // If it's a numeric string, convert to float
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Otherwise, return null
        return null;
    }
}
