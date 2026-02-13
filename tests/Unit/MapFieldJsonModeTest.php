<?php

namespace LBCDev\FilamentMapsFields\Tests\Unit;

use Illuminate\Support\Facades\Schema;
use LBCDev\FilamentMapsFields\Forms\Components\MapField;
use LBCDev\FilamentMapsFields\Tests\Fixtures\Models\Place;
use LBCDev\FilamentMapsFields\Tests\TestCase;

/**
 * Tests for MapField in JSON Mode (default mode)
 * 
 * JSON Mode stores coordinates as: {latitude: X, longitude: Y}
 * in a single JSON field, without needing separate latitude/longitude fields.
 */
class MapFieldJsonModeTest extends TestCase
{
    /** @test */
    public function it_detects_json_mode_when_no_latitude_longitude_configured(): void
    {
        $field = MapField::make('location');

        $this->assertFalse($field->isLegacyMode());
    }

    /** @test */
    public function it_detects_legacy_mode_when_latitude_longitude_configured(): void
    {
        $field = MapField::make('map')
            ->latitude('latitude')
            ->longitude('longitude');

        $this->assertTrue($field->isLegacyMode());
    }

    /** @test */
    public function it_can_create_place_with_json_coordinates(): void
    {
        $place = Place::create([
            'name' => 'Madrid',
            'location' => [
                'latitude' => 40.4168,
                'longitude' => -3.7038,
            ],
        ]);

        $this->assertNotNull($place->id);
        $this->assertEquals('Madrid', $place->name);
        $this->assertIsArray($place->location);
        $this->assertEquals(40.4168, $place->location['latitude']);
        $this->assertEquals(-3.7038, $place->location['longitude']);
    }

    /** @test */
    public function it_can_retrieve_place_with_json_coordinates(): void
    {
        $place = Place::create([
            'name' => 'Barcelona',
            'location' => [
                'latitude' => 41.3851,
                'longitude' => 2.1734,
            ],
        ]);

        $retrieved = Place::find($place->id);

        $this->assertEquals('Barcelona', $retrieved->name);
        $this->assertEquals(41.3851, $retrieved->location['latitude']);
        $this->assertEquals(2.1734, $retrieved->location['longitude']);
    }

    /** @test */
    public function it_handles_null_location_gracefully(): void
    {
        $place = Place::create([
            'name' => 'Unknown Place',
            'location' => null,
        ]);

        $this->assertNotNull($place->id);
        $this->assertNull($place->location);
    }

    /** @test */
    public function it_can_update_json_coordinates(): void
    {
        $place = Place::create([
            'name' => 'Valencia',
            'location' => [
                'latitude' => 39.4699,
                'longitude' => -0.3763,
            ],
        ]);

        // Update coordinates
        $place->update([
            'location' => [
                'latitude' => 39.5000,
                'longitude' => -0.4000,
            ],
        ]);

        $updated = Place::find($place->id);
        $this->assertEquals(39.5000, $updated->location['latitude']);
        $this->assertEquals(-0.4000, $updated->location['longitude']);
    }

    /** @test */
    public function it_verifies_places_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('places'));
        $this->assertTrue(Schema::hasColumn('places', 'location'));
    }

    /** @test */
    public function json_mode_and_legacy_mode_can_coexist(): void
    {
        // JSON Mode field
        $jsonField = MapField::make('location');
        $this->assertFalse($jsonField->isLegacyMode());

        // Legacy Mode field
        $legacyField = MapField::make('map')
            ->latitude('latitude')
            ->longitude('longitude');
        $this->assertTrue($legacyField->isLegacyMode());

        // Both can exist in the same application
        $this->assertNotEquals($jsonField->isLegacyMode(), $legacyField->isLegacyMode());
    }
}
