<?php

namespace Lbcdev\FilamentMapField\Infolists\Entries;

use Filament\Infolists\Components\Entry;

class MapEntry extends Entry
{
    protected string $view = 'filament-map-field::infolists.entries.map-entry';

    protected string|null $latitudeField = null;
    protected string|null $longitudeField = null;
    protected int $height = 300;
    protected int $zoom = 15;
    protected bool $showLabel = true;

    public function latitude(string $field): static
    {
        $this->latitudeField = $field;
        return $this;
    }

    public function longitude(string $field): static
    {
        $this->longitudeField = $field;
        return $this;
    }

    public function height(int $height): static
    {
        $this->height = $height;
        return $this;
    }

    public function zoom(int $zoom): static
    {
        $this->zoom = $zoom;
        return $this;
    }

    public function showLabel(bool $show = true): static
    {
        $this->showLabel = $show;
        return $this;
    }

    public function getLatitudeField(): ?string
    {
        return $this->latitudeField;
    }

    public function getLongitudeField(): ?string
    {
        return $this->longitudeField;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getZoom(): int
    {
        return $this->zoom;
    }

    public function shouldShowLabel(): bool
    {
        return $this->showLabel;
    }

    public function getCoordinates(): ?array
    {
        try {
            if (!$this->latitudeField || !$this->longitudeField) {
                return null;
            }

            $record = $this->getRecord();

            if (!$record) {
                return null;
            }

            $latitude = data_get($record, $this->latitudeField);
            $longitude = data_get($record, $this->longitudeField);

            // Convert empty strings to null and numeric strings to float
            $latitude = $this->normalizeCoordinate($latitude);
            $longitude = $this->normalizeCoordinate($longitude);

            // Only return if both coordinates are valid
            if ($latitude !== null && $longitude !== null) {
                return [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ];
            }

            return null;
        } catch (\Throwable $e) {
            // If anything fails, return null
            return null;
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
