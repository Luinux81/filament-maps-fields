<?php

namespace Lbcdev\FilamentMapField\Tests\Unit;

use Lbcdev\FilamentMapField\Forms\Components\MapBoundsField;
use Lbcdev\FilamentMapField\Tests\TestCase;

class MapBoundsFieldTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated(): void
    {
        $field = MapBoundsField::make('area');

        $this->assertInstanceOf(MapBoundsField::class, $field);
    }

    /** @test */
    public function it_can_set_south_west_lat_field(): void
    {
        $field = MapBoundsField::make('area')
            ->southWestLat('sw_lat');

        $this->assertEquals('sw_lat', $field->getSouthWestLatField());
    }

    /** @test */
    public function it_can_set_south_west_lng_field(): void
    {
        $field = MapBoundsField::make('area')
            ->southWestLng('sw_lng');

        $this->assertEquals('sw_lng', $field->getSouthWestLngField());
    }

    /** @test */
    public function it_can_set_north_east_lat_field(): void
    {
        $field = MapBoundsField::make('area')
            ->northEastLat('ne_lat');

        $this->assertEquals('ne_lat', $field->getNorthEastLatField());
    }

    /** @test */
    public function it_can_set_north_east_lng_field(): void
    {
        $field = MapBoundsField::make('area')
            ->northEastLng('ne_lng');

        $this->assertEquals('ne_lng', $field->getNorthEastLngField());
    }

    /** @test */
    public function it_can_set_all_bound_fields(): void
    {
        $field = MapBoundsField::make('area')
            ->southWestLat('sw_lat')
            ->southWestLng('sw_lng')
            ->northEastLat('ne_lat')
            ->northEastLng('ne_lng');

        $this->assertEquals('sw_lat', $field->getSouthWestLatField());
        $this->assertEquals('sw_lng', $field->getSouthWestLngField());
        $this->assertEquals('ne_lat', $field->getNorthEastLatField());
        $this->assertEquals('ne_lng', $field->getNorthEastLngField());
    }

    /** @test */
    public function it_can_set_height(): void
    {
        $field = MapBoundsField::make('area')
            ->height(500);

        $this->assertEquals(500, $field->getHeight());
    }

    /** @test */
    public function it_has_default_height(): void
    {
        $field = MapBoundsField::make('area');

        $this->assertEquals(400, $field->getHeight());
    }

    /** @test */
    public function it_can_set_zoom(): void
    {
        $field = MapBoundsField::make('area')
            ->zoom(10);

        $this->assertEquals(10, $field->getZoom());
    }

    /** @test */
    public function it_has_default_zoom(): void
    {
        $field = MapBoundsField::make('area');

        $this->assertEquals(13, $field->getZoom());
    }

    /** @test */
    public function it_can_set_show_label(): void
    {
        $field = MapBoundsField::make('area')
            ->showLabel(false);

        $this->assertFalse($field->shouldShowLabel());
    }

    /** @test */
    public function it_shows_label_by_default(): void
    {
        $field = MapBoundsField::make('area');

        $this->assertTrue($field->shouldShowLabel());
    }

    /** @test */
    public function it_can_set_default_center(): void
    {
        $field = MapBoundsField::make('area')
            ->defaultCenter(40.4168, -3.7038);

        $this->assertEquals([40.4168, -3.7038], $field->getDefaultCenter());
    }

    /** @test */
    public function it_has_default_center(): void
    {
        $field = MapBoundsField::make('area');

        $this->assertEquals([36.9990019, -6.5478919], $field->getDefaultCenter());
    }

    /** @test */
    public function it_supports_method_chaining(): void
    {
        $field = MapBoundsField::make('area')
            ->southWestLat('sw_lat')
            ->southWestLng('sw_lng')
            ->northEastLat('ne_lat')
            ->northEastLng('ne_lng')
            ->height(500)
            ->zoom(15)
            ->showLabel(false)
            ->defaultCenter(40.4168, -3.7038);

        $this->assertInstanceOf(MapBoundsField::class, $field);
        $this->assertEquals('sw_lat', $field->getSouthWestLatField());
        $this->assertEquals('sw_lng', $field->getSouthWestLngField());
        $this->assertEquals('ne_lat', $field->getNorthEastLatField());
        $this->assertEquals('ne_lng', $field->getNorthEastLngField());
        $this->assertEquals(500, $field->getHeight());
        $this->assertEquals(15, $field->getZoom());
        $this->assertFalse($field->shouldShowLabel());
        $this->assertEquals([40.4168, -3.7038], $field->getDefaultCenter());
    }
}

