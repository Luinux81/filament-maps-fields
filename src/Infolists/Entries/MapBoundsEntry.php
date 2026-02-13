<?php

namespace LBCDev\FilamentMapsFields\Infolists\Entries;

use Filament\Infolists\Components\Entry;

/**
 * MapBoundsEntry - Display map bounds in an infolist
 *
 * Shows a rectangular area on a map (read-only).
 * Requires Leaflet.js and Leaflet.draw plugin.
 *
 * Supports two modes:
 * - JSON Mode (default): Reads bounds from {sw_lat, sw_lng, ne_lat, ne_lng} in a single field
 * - Legacy Mode: Reads bounds from 4 separate fields
 *
 * @example JSON Mode (recommended)
 * MapBoundsEntry::make('bounds')
 *
 * @example Legacy Mode
 * MapBoundsEntry::make('area_bounds')
 *     ->southWestLat('sw_lat')
 *     ->southWestLng('sw_lng')
 *     ->northEastLat('ne_lat')
 *     ->northEastLng('ne_lng')
 */
class MapBoundsEntry extends Entry
{
    protected string $view = 'filament-maps-fields::infolists.entries.map-bounds-entry';

    protected string|null $southWestLatField = null;
    protected string|null $southWestLngField = null;
    protected string|null $northEastLatField = null;
    protected string|null $northEastLngField = null;
    protected int $height = 300;
    protected int $zoom = 13;
    protected bool $showLabel = true;

    /**
     * Check if the entry is in legacy mode (4 separate fields)
     */
    public function isLegacyMode(): bool
    {
        return $this->southWestLatField !== null
            && $this->southWestLngField !== null
            && $this->northEastLatField !== null
            && $this->northEastLngField !== null;
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
     * Get the current bounds from the record
     * Supports both JSON mode and Legacy mode
     */
    public function getBounds(): ?array
    {
        try {
            $record = $this->getRecord();

            if (!$record) {
                return null;
            }

            if ($this->isLegacyMode()) {
                // Legacy mode: read from separate fields
                $swLat = $this->normalizeCoordinate(data_get($record, $this->southWestLatField));
                $swLng = $this->normalizeCoordinate(data_get($record, $this->southWestLngField));
                $neLat = $this->normalizeCoordinate(data_get($record, $this->northEastLatField));
                $neLng = $this->normalizeCoordinate(data_get($record, $this->northEastLngField));

                // Only return if all coordinates are valid
                if ($swLat !== null && $swLng !== null && $neLat !== null && $neLng !== null) {
                    return [
                        'sw_lat' => $swLat,
                        'sw_lng' => $swLng,
                        'ne_lat' => $neLat,
                        'ne_lng' => $neLng,
                    ];
                }

                return null;
            }

            // JSON mode: read from state
            $state = $this->getState();

            if (is_array($state) && !empty($state)) {
                $swLat = $this->normalizeCoordinate($state['sw_lat'] ?? null);
                $swLng = $this->normalizeCoordinate($state['sw_lng'] ?? null);
                $neLat = $this->normalizeCoordinate($state['ne_lat'] ?? null);
                $neLng = $this->normalizeCoordinate($state['ne_lng'] ?? null);

                if ($swLat !== null && $swLng !== null && $neLat !== null && $neLng !== null) {
                    return [
                        'sw_lat' => $swLat,
                        'sw_lng' => $swLng,
                        'ne_lat' => $neLat,
                        'ne_lng' => $neLng,
                    ];
                }
            }

            return null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Normalize a coordinate value to float or null
     */
    protected function normalizeCoordinate($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_float($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }
}
