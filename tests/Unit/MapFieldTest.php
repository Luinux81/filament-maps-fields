<?php

namespace LBCDev\FilamentMapsFields\Tests\Unit;

use LBCDev\FilamentMapsFields\Forms\Components\MapField;
use LBCDev\FilamentMapsFields\Tests\TestCase;

class MapFieldTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated(): void
    {
        $field = MapField::make('map');

        $this->assertInstanceOf(MapField::class, $field);
    }

    /** @test */
    public function it_can_set_latitude_field(): void
    {
        $field = MapField::make('map')
            ->latitude('latitude');

        $this->assertEquals('latitude', $field->getLatitudeField());
    }

    /** @test */
    public function it_can_set_longitude_field(): void
    {
        $field = MapField::make('map')
            ->longitude('longitude');

        $this->assertEquals('longitude', $field->getLongitudeField());
    }

    /** @test */
    public function it_can_set_height(): void
    {
        $field = MapField::make('map')
            ->height(500);

        $this->assertEquals(500, $field->getHeight());
    }

    /** @test */
    public function it_has_default_height(): void
    {
        $field = MapField::make('map');

        $this->assertEquals(400, $field->getHeight());
    }

    /** @test */
    public function it_can_set_zoom(): void
    {
        $field = MapField::make('map')
            ->zoom(12);

        $this->assertEquals(12, $field->getZoom());
    }

    /** @test */
    public function it_has_default_zoom(): void
    {
        $field = MapField::make('map');

        $this->assertEquals(15, $field->getZoom());
    }

    /** @test */
    public function it_can_show_paste_button(): void
    {
        $field = MapField::make('map')
            ->showPasteButton();

        $this->assertTrue($field->shouldShowPasteButton());
    }

    /** @test */
    public function it_hides_paste_button_by_default(): void
    {
        $field = MapField::make('map');

        $this->assertFalse($field->shouldShowPasteButton());
    }

    /** @test */
    public function it_can_show_label(): void
    {
        $field = MapField::make('map')
            ->showLabel();

        $this->assertTrue($field->shouldShowLabel());
    }

    /** @test */
    public function it_shows_label_by_default(): void
    {
        $field = MapField::make('map');

        $this->assertTrue($field->shouldShowLabel());
    }

    /** @test */
    public function it_can_hide_label(): void
    {
        $field = MapField::make('map')
            ->showLabel(false);

        $this->assertFalse($field->shouldShowLabel());
    }

    /** @test */
    public function it_is_interactive_by_default(): void
    {
        $field = MapField::make('map');

        $this->assertTrue($field->isInteractive());
    }

    /** @test */
    public function it_can_be_set_to_non_interactive(): void
    {
        $field = MapField::make('map')
            ->interactive(false);

        $this->assertFalse($field->isInteractive());
    }

    /** @test */
    public function it_can_be_set_to_read_only(): void
    {
        $field = MapField::make('map')
            ->readOnly();

        $this->assertFalse($field->isInteractive());
    }

    /** @test */
    public function it_can_be_set_to_read_only_with_condition(): void
    {
        $field = MapField::make('map')
            ->readOnly(true);

        $this->assertFalse($field->isInteractive());

        $field = MapField::make('map')
            ->readOnly(false);

        $this->assertTrue($field->isInteractive());
    }
}
