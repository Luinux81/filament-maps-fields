<?php

namespace LBCDev\FilamentMapsFields\Tests\Unit;

use LBCDev\FilamentMapsFields\Forms\Components\MapBoundsField;
use LBCDev\FilamentMapsFields\Tests\Fixtures\Models\Area;
use LBCDev\FilamentMapsFields\Tests\TestCase;

class MapBoundsFieldJsonNotationTest extends TestCase
{
    /** @test */
    public function it_can_set_nested_south_west_lat_field_with_dot_notation(): void
    {
        $field = MapBoundsField::make('bounds')
            ->southWestLat('bounds.sw_lat');

        $this->assertEquals('bounds.sw_lat', $field->getSouthWestLatField());
    }

    /** @test */
    public function it_can_set_nested_south_west_lng_field_with_dot_notation(): void
    {
        $field = MapBoundsField::make('bounds')
            ->southWestLng('bounds.sw_lng');

        $this->assertEquals('bounds.sw_lng', $field->getSouthWestLngField());
    }

    /** @test */
    public function it_can_set_nested_north_east_lat_field_with_dot_notation(): void
    {
        $field = MapBoundsField::make('bounds')
            ->northEastLat('bounds.ne_lat');

        $this->assertEquals('bounds.ne_lat', $field->getNorthEastLatField());
    }

    /** @test */
    public function it_can_set_nested_north_east_lng_field_with_dot_notation(): void
    {
        $field = MapBoundsField::make('bounds')
            ->northEastLng('bounds.ne_lng');

        $this->assertEquals('bounds.ne_lng', $field->getNorthEastLngField());
    }

    /** @test */
    public function it_can_set_all_nested_fields_with_dot_notation(): void
    {
        $field = MapBoundsField::make('bounds')
            ->southWestLat('bounds.sw_lat')
            ->southWestLng('bounds.sw_lng')
            ->northEastLat('bounds.ne_lat')
            ->northEastLng('bounds.ne_lng');

        $this->assertEquals('bounds.sw_lat', $field->getSouthWestLatField());
        $this->assertEquals('bounds.sw_lng', $field->getSouthWestLngField());
        $this->assertEquals('bounds.ne_lat', $field->getNorthEastLatField());
        $this->assertEquals('bounds.ne_lng', $field->getNorthEastLngField());
    }

    /** @test */
    public function it_can_read_bounds_from_json_field_using_data_get(): void
    {
        // Create an area with JSON bounds
        $area = Area::create([
            'name' => 'Test Area',
            'bounds' => [
                'sw_lat' => 36.5,
                'sw_lng' => -6.5,
                'ne_lat' => 37.5,
                'ne_lng' => -5.5,
            ],
        ]);

        // Verify data_get works with nested fields
        $this->assertEquals(36.5, data_get($area, 'bounds.sw_lat'));
        $this->assertEquals(-6.5, data_get($area, 'bounds.sw_lng'));
        $this->assertEquals(37.5, data_get($area, 'bounds.ne_lat'));
        $this->assertEquals(-5.5, data_get($area, 'bounds.ne_lng'));
    }

    /** @test */
    public function it_supports_deeply_nested_fields(): void
    {
        $field = MapBoundsField::make('location')
            ->southWestLat('location.area.bounds.sw_lat')
            ->southWestLng('location.area.bounds.sw_lng')
            ->northEastLat('location.area.bounds.ne_lat')
            ->northEastLng('location.area.bounds.ne_lng');

        $this->assertEquals('location.area.bounds.sw_lat', $field->getSouthWestLatField());
        $this->assertEquals('location.area.bounds.sw_lng', $field->getSouthWestLngField());
        $this->assertEquals('location.area.bounds.ne_lat', $field->getNorthEastLatField());
        $this->assertEquals('location.area.bounds.ne_lng', $field->getNorthEastLngField());
    }

    /** @test */
    public function it_can_mix_simple_and_nested_field_names(): void
    {
        $field = MapBoundsField::make('area')
            ->southWestLat('sw_lat')
            ->southWestLng('sw_lng')
            ->northEastLat('bounds.ne_lat')
            ->northEastLng('bounds.ne_lng');

        $this->assertEquals('sw_lat', $field->getSouthWestLatField());
        $this->assertEquals('sw_lng', $field->getSouthWestLngField());
        $this->assertEquals('bounds.ne_lat', $field->getNorthEastLatField());
        $this->assertEquals('bounds.ne_lng', $field->getNorthEastLngField());
    }
}
