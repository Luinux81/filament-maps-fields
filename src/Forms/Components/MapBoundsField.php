<?php

namespace Lbcdev\FilamentMapField\Forms\Components;

use Filament\Forms\Components\Field;

class MapBoundsField extends Field
{
    protected string $view = 'filament-map-field::forms.components.map-bounds-field';

    protected string|null $southWestLatField = null;
    protected string|null $southWestLngField = null;
    protected string|null $northEastLatField = null;
    protected string|null $northEastLngField = null;
    protected int $height = 400;
    protected int $zoom = 13;
    protected bool $showLabel = true;
    protected array $defaultCenter = [36.9990019, -6.5478919]; // Default center (Spain)

    /**
     * Setup the field to not require validation
     * This prevents issues when using dot notation with nested fields
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Make the field not required by default
        // This prevents validation errors in create mode when using dot notation
        $this->default(null);
        $this->dehydrated(false); // Don't save this field itself, only the nested fields

        // Add custom validation rules when the field is marked as required
        $this->rule(function () {
            return function (string $attribute, $value, \Closure $fail) {
                // Only validate if the field is marked as required
                if (!$this->isRequired()) {
                    return;
                }

                // Get the container to access form state
                $container = $this->getContainer();
                if (!$container) {
                    return;
                }

                // Get the state (works in both create and edit modes)
                $state = method_exists($container, 'getRawState')
                    ? $container->getRawState()
                    : $container->getState();

                // Check if all bound fields are set and have values
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
     * Supports both simple field names and dot notation for nested fields
     */
    public function getBounds(): array
    {
        $container = $this->getContainer();

        if (
            $this->southWestLatField && $this->southWestLngField &&
            $this->northEastLatField && $this->northEastLngField
        ) {
            return [
                'sw_lat' => data_get($container->getState(), $this->southWestLatField),
                'sw_lng' => data_get($container->getState(), $this->southWestLngField),
                'ne_lat' => data_get($container->getState(), $this->northEastLatField),
                'ne_lng' => data_get($container->getState(), $this->northEastLngField),
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
