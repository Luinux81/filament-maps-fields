<?php

namespace Lbcdev\FilamentMapField\Tests\Unit;

use Lbcdev\FilamentMapField\Forms\Components\MapField;
use Lbcdev\FilamentMapField\Tests\Fixtures\Models\Store;
use Lbcdev\FilamentMapField\Tests\TestCase;

class MapFieldJsonNotationTest extends TestCase
{
    /** @test */
    public function it_can_set_nested_latitude_field_with_dot_notation(): void
    {
        $field = MapField::make('ubicacion')
            ->latitude('ubicacion.latitud');

        $this->assertEquals('ubicacion.latitud', $field->getLatitudeField());
    }

    /** @test */
    public function it_can_set_nested_longitude_field_with_dot_notation(): void
    {
        $field = MapField::make('ubicacion')
            ->longitude('ubicacion.longitud');

        $this->assertEquals('ubicacion.longitud', $field->getLongitudeField());
    }

    /** @test */
    public function it_can_get_coordinates_from_nested_json_fields(): void
    {
        // Create a store with nested coordinates
        $store = Store::create([
            'name' => 'Test Store',
            'ubicacion' => [
                'latitud' => '40.416775',
                'longitud' => '-3.703790',
            ],
        ]);

        // Verify the store was created correctly
        $this->assertNotNull($store);
        $this->assertEquals('40.416775', $store->ubicacion['latitud']);
        $this->assertEquals('-3.703790', $store->ubicacion['longitud']);

        // Verify accessors work
        $this->assertEquals(40.416775, $store->latitud);
        $this->assertEquals(-3.703790, $store->longitud);
    }

    /** @test */
    public function it_returns_null_coordinates_when_nested_fields_are_empty(): void
    {
        // Create a store with null ubicacion
        $store = Store::create([
            'name' => 'Test Store',
            'ubicacion' => null,
        ]);

        // Verify the store was created correctly
        $this->assertNotNull($store);
        $this->assertNull($store->ubicacion);

        // Verify accessors return null
        $this->assertNull($store->latitud);
        $this->assertNull($store->longitud);
    }

    /** @test */
    public function it_supports_different_nested_field_names(): void
    {
        // Test with different naming conventions
        $field = MapField::make('location')
            ->latitude('location.lat')
            ->longitude('location.lng');

        $this->assertEquals('location.lat', $field->getLatitudeField());
        $this->assertEquals('location.lng', $field->getLongitudeField());
    }

    /** @test */
    public function it_supports_deeply_nested_fields(): void
    {
        // Test with deeply nested structure
        $field = MapField::make('address')
            ->latitude('address.coordinates.latitude')
            ->longitude('address.coordinates.longitude');

        $this->assertEquals('address.coordinates.latitude', $field->getLatitudeField());
        $this->assertEquals('address.coordinates.longitude', $field->getLongitudeField());
    }
}
