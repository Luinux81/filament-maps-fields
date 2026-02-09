<?php

namespace Lbcdev\FilamentMapField\Tests\Unit;

use Lbcdev\FilamentMapField\Infolists\Entries\MapEntry;
use Lbcdev\FilamentMapField\Tests\TestCase;

class MapEntryTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated(): void
    {
        $entry = MapEntry::make('map');

        $this->assertInstanceOf(MapEntry::class, $entry);
    }

    /** @test */
    public function it_can_set_latitude_field(): void
    {
        $entry = MapEntry::make('map')
            ->latitude('latitude');

        $this->assertEquals('latitude', $entry->getLatitudeField());
    }

    /** @test */
    public function it_can_set_longitude_field(): void
    {
        $entry = MapEntry::make('map')
            ->longitude('longitude');

        $this->assertEquals('longitude', $entry->getLongitudeField());
    }

    /** @test */
    public function it_can_set_nested_fields_with_dot_notation(): void
    {
        $entry = MapEntry::make('ubicacion')
            ->latitude('ubicacion.latitud')
            ->longitude('ubicacion.longitud');

        $this->assertEquals('ubicacion.latitud', $entry->getLatitudeField());
        $this->assertEquals('ubicacion.longitud', $entry->getLongitudeField());
    }

    /** @test */
    public function it_can_set_height(): void
    {
        $entry = MapEntry::make('map')
            ->height(500);

        $this->assertEquals(500, $entry->getHeight());
    }

    /** @test */
    public function it_has_default_height(): void
    {
        $entry = MapEntry::make('map');

        $this->assertEquals(300, $entry->getHeight());
    }

    /** @test */
    public function it_can_set_zoom(): void
    {
        $entry = MapEntry::make('map')
            ->zoom(12);

        $this->assertEquals(12, $entry->getZoom());
    }

    /** @test */
    public function it_has_default_zoom(): void
    {
        $entry = MapEntry::make('map');

        $this->assertEquals(15, $entry->getZoom());
    }

    /** @test */
    public function it_can_show_label(): void
    {
        $entry = MapEntry::make('map')
            ->showLabel();

        $this->assertTrue($entry->shouldShowLabel());
    }

    /** @test */
    public function it_shows_label_by_default(): void
    {
        $entry = MapEntry::make('map');

        $this->assertTrue($entry->shouldShowLabel());
    }

    /** @test */
    public function it_can_hide_label(): void
    {
        $entry = MapEntry::make('map')
            ->showLabel(false);

        $this->assertFalse($entry->shouldShowLabel());
    }
}

