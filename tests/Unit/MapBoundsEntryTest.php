<?php

namespace Lbcdev\FilamentMapField\Tests\Unit;

use Lbcdev\FilamentMapField\Infolists\Entries\MapBoundsEntry;
use Lbcdev\FilamentMapField\Tests\Fixtures\Models\Area;
use Lbcdev\FilamentMapField\Tests\TestCase;

class MapBoundsEntryTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated(): void
    {
        $entry = MapBoundsEntry::make('area');

        $this->assertInstanceOf(MapBoundsEntry::class, $entry);
    }

    /** @test */
    public function it_can_set_south_west_lat_field(): void
    {
        $entry = MapBoundsEntry::make('area')
            ->southWestLat('sw_lat');

        $this->assertEquals('sw_lat', $entry->getSouthWestLatField());
    }

    /** @test */
    public function it_can_set_south_west_lng_field(): void
    {
        $entry = MapBoundsEntry::make('area')
            ->southWestLng('sw_lng');

        $this->assertEquals('sw_lng', $entry->getSouthWestLngField());
    }

    /** @test */
    public function it_can_set_north_east_lat_field(): void
    {
        $entry = MapBoundsEntry::make('area')
            ->northEastLat('ne_lat');

        $this->assertEquals('ne_lat', $entry->getNorthEastLatField());
    }

    /** @test */
    public function it_can_set_north_east_lng_field(): void
    {
        $entry = MapBoundsEntry::make('area')
            ->northEastLng('ne_lng');

        $this->assertEquals('ne_lng', $entry->getNorthEastLngField());
    }

    /** @test */
    public function it_can_set_height(): void
    {
        $entry = MapBoundsEntry::make('area')
            ->height(500);

        $this->assertEquals(500, $entry->getHeight());
    }

    /** @test */
    public function it_has_default_height(): void
    {
        $entry = MapBoundsEntry::make('area');

        $this->assertEquals(300, $entry->getHeight());
    }

    /** @test */
    public function it_can_set_zoom(): void
    {
        $entry = MapBoundsEntry::make('area')
            ->zoom(10);

        $this->assertEquals(10, $entry->getZoom());
    }

    /** @test */
    public function it_has_default_zoom(): void
    {
        $entry = MapBoundsEntry::make('area');

        $this->assertEquals(13, $entry->getZoom());
    }

    /** @test */
    public function it_can_set_show_label(): void
    {
        $entry = MapBoundsEntry::make('area')
            ->showLabel(false);

        $this->assertFalse($entry->shouldShowLabel());
    }

    /** @test */
    public function it_shows_label_by_default(): void
    {
        $entry = MapBoundsEntry::make('area');

        $this->assertTrue($entry->shouldShowLabel());
    }

    /** @test */
    public function it_supports_dot_notation_for_nested_fields(): void
    {
        $entry = MapBoundsEntry::make('bounds')
            ->southWestLat('bounds.sw_lat')
            ->southWestLng('bounds.sw_lng')
            ->northEastLat('bounds.ne_lat')
            ->northEastLng('bounds.ne_lng');

        $this->assertEquals('bounds.sw_lat', $entry->getSouthWestLatField());
        $this->assertEquals('bounds.sw_lng', $entry->getSouthWestLngField());
        $this->assertEquals('bounds.ne_lat', $entry->getNorthEastLatField());
        $this->assertEquals('bounds.ne_lng', $entry->getNorthEastLngField());
    }

    /** @test */
    public function it_normalizes_coordinate_values(): void
    {
        $entry = new MapBoundsEntry('area');

        // Test with reflection to access protected method
        $reflection = new \ReflectionClass($entry);
        $method = $reflection->getMethod('normalizeCoordinate');
        $method->setAccessible(true);

        // Test null
        $this->assertNull($method->invoke($entry, null));

        // Test empty string
        $this->assertNull($method->invoke($entry, ''));

        // Test float
        $this->assertEquals(36.5, $method->invoke($entry, 36.5));

        // Test numeric string
        $this->assertEquals(36.5, $method->invoke($entry, '36.5'));

        // Test non-numeric string
        $this->assertNull($method->invoke($entry, 'invalid'));
    }

    /** @test */
    public function it_supports_method_chaining(): void
    {
        $entry = MapBoundsEntry::make('area')
            ->southWestLat('sw_lat')
            ->southWestLng('sw_lng')
            ->northEastLat('ne_lat')
            ->northEastLng('ne_lng')
            ->height(500)
            ->zoom(15)
            ->showLabel(false);

        $this->assertInstanceOf(MapBoundsEntry::class, $entry);
        $this->assertEquals('sw_lat', $entry->getSouthWestLatField());
        $this->assertEquals('sw_lng', $entry->getSouthWestLngField());
        $this->assertEquals('ne_lat', $entry->getNorthEastLatField());
        $this->assertEquals('ne_lng', $entry->getNorthEastLngField());
        $this->assertEquals(500, $entry->getHeight());
        $this->assertEquals(15, $entry->getZoom());
        $this->assertFalse($entry->shouldShowLabel());
    }
}

