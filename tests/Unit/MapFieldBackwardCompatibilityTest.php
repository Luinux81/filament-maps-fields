<?php

namespace Lbcdev\FilamentMapField\Tests\Unit;

use Lbcdev\FilamentMapField\Forms\Components\MapField;
use Lbcdev\FilamentMapField\Tests\Fixtures\Models\Location;
use Lbcdev\FilamentMapField\Tests\TestCase;

/**
 * Tests to ensure backward compatibility with the traditional mode (separate fields)
 */
class MapFieldBackwardCompatibilityTest extends TestCase
{
    /** @test */
    public function it_works_with_simple_field_names(): void
    {
        $field = MapField::make('map')
            ->latitude('latitude')
            ->longitude('longitude');

        $this->assertEquals('latitude', $field->getLatitudeField());
        $this->assertEquals('longitude', $field->getLongitudeField());
    }

    /** @test */
    public function it_can_get_coordinates_from_separate_fields(): void
    {
        // Create a location with separate fields
        $location = Location::create([
            'name' => 'Test Location',
            'latitude' => 40.416775,
            'longitude' => -3.703790,
        ]);

        // Verify the location was created correctly
        $this->assertNotNull($location);
        $this->assertEquals(40.416775, $location->latitude);
        $this->assertEquals(-3.703790, $location->longitude);
    }

    /** @test */
    public function it_returns_null_coordinates_when_fields_are_empty(): void
    {
        // Create a location with null coordinates
        $location = Location::create([
            'name' => 'Test Location',
            'latitude' => null,
            'longitude' => null,
        ]);

        // Verify the location was created correctly
        $this->assertNotNull($location);
        $this->assertNull($location->latitude);
        $this->assertNull($location->longitude);
    }

    /** @test */
    public function it_supports_custom_field_names_without_dots(): void
    {
        $field = MapField::make('map')
            ->latitude('lat')
            ->longitude('lng');

        $this->assertEquals('lat', $field->getLatitudeField());
        $this->assertEquals('lng', $field->getLongitudeField());
    }

    /** @test */
    public function it_supports_prefixed_field_names(): void
    {
        $field = MapField::make('map')
            ->latitude('origin_latitude')
            ->longitude('origin_longitude');

        $this->assertEquals('origin_latitude', $field->getLatitudeField());
        $this->assertEquals('origin_longitude', $field->getLongitudeField());
    }

    /** @test */
    public function traditional_mode_and_json_mode_can_coexist(): void
    {
        // Traditional mode field
        $traditionalField = MapField::make('map1')
            ->latitude('latitude')
            ->longitude('longitude');

        // JSON mode field
        $jsonField = MapField::make('map2')
            ->latitude('ubicacion.latitud')
            ->longitude('ubicacion.longitud');

        // Both should work independently
        $this->assertEquals('latitude', $traditionalField->getLatitudeField());
        $this->assertEquals('longitude', $traditionalField->getLongitudeField());

        $this->assertEquals('ubicacion.latitud', $jsonField->getLatitudeField());
        $this->assertEquals('ubicacion.longitud', $jsonField->getLongitudeField());
    }
}
