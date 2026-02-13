<?php

namespace LBCDev\FilamentMapsFields\Tests\Unit;

use LBCDev\FilamentMapsFields\Forms\Components\MapBoundsField;
use LBCDev\FilamentMapsFields\Tests\Fixtures\Models\Area;
use LBCDev\FilamentMapsFields\Tests\TestCase;

class MapBoundsFieldJsonModeTest extends TestCase
{
    /** @test */
    public function it_detects_json_mode_when_no_fields_configured(): void
    {
        $field = MapBoundsField::make('bounds');

        $this->assertFalse($field->isLegacyMode());
    }

    /** @test */
    public function it_detects_legacy_mode_when_all_fields_configured(): void
    {
        $field = MapBoundsField::make('area_bounds')
            ->southWestLat('sw_lat')
            ->southWestLng('sw_lng')
            ->northEastLat('ne_lat')
            ->northEastLng('ne_lng');

        $this->assertTrue($field->isLegacyMode());
    }

    /** @test */
    public function it_can_create_area_with_json_bounds(): void
    {
        $area = Area::create([
            'name' => 'Test Area',
            'bounds' => [
                'sw_lat' => 40.0,
                'sw_lng' => -4.0,
                'ne_lat' => 41.0,
                'ne_lng' => -3.0,
            ],
        ]);

        $this->assertDatabaseHas('areas', [
            'name' => 'Test Area',
        ]);

        $this->assertEquals(40.0, $area->bounds['sw_lat']);
        $this->assertEquals(-4.0, $area->bounds['sw_lng']);
        $this->assertEquals(41.0, $area->bounds['ne_lat']);
        $this->assertEquals(-3.0, $area->bounds['ne_lng']);
    }

    /** @test */
    public function it_can_retrieve_area_with_json_bounds(): void
    {
        $area = Area::create([
            'name' => 'Madrid Area',
            'bounds' => [
                'sw_lat' => 40.3,
                'sw_lng' => -3.8,
                'ne_lat' => 40.5,
                'ne_lng' => -3.6,
            ],
        ]);

        $retrieved = Area::find($area->id);

        $this->assertEquals('Madrid Area', $retrieved->name);
        $this->assertEquals(40.3, $retrieved->bounds['sw_lat']);
        $this->assertEquals(-3.8, $retrieved->bounds['sw_lng']);
        $this->assertEquals(40.5, $retrieved->bounds['ne_lat']);
        $this->assertEquals(-3.6, $retrieved->bounds['ne_lng']);
    }

    /** @test */
    public function it_can_update_json_bounds(): void
    {
        $area = Area::create([
            'name' => 'Test Area',
            'bounds' => [
                'sw_lat' => 40.0,
                'sw_lng' => -4.0,
                'ne_lat' => 41.0,
                'ne_lng' => -3.0,
            ],
        ]);

        $area->update([
            'bounds' => [
                'sw_lat' => 42.0,
                'sw_lng' => -5.0,
                'ne_lat' => 43.0,
                'ne_lng' => -4.0,
            ],
        ]);

        $this->assertEquals(42.0, $area->fresh()->bounds['sw_lat']);
        $this->assertEquals(-5.0, $area->fresh()->bounds['sw_lng']);
        $this->assertEquals(43.0, $area->fresh()->bounds['ne_lat']);
        $this->assertEquals(-4.0, $area->fresh()->bounds['ne_lng']);
    }

    /** @test */
    public function it_handles_null_bounds_gracefully(): void
    {
        $area = Area::create([
            'name' => 'No Bounds Area',
            'bounds' => null,
        ]);

        $this->assertNull($area->bounds);
    }

    /** @test */
    public function it_verifies_areas_table_exists(): void
    {
        $this->assertTrue(\Schema::hasTable('areas'));
        $this->assertTrue(\Schema::hasColumn('areas', 'bounds'));
    }

    /** @test */
    public function json_mode_and_legacy_mode_can_coexist(): void
    {
        // Create area with JSON bounds
        $jsonArea = Area::create([
            'name' => 'JSON Area',
            'bounds' => [
                'sw_lat' => 40.0,
                'sw_lng' => -4.0,
                'ne_lat' => 41.0,
                'ne_lng' => -3.0,
            ],
        ]);

        // Create area with legacy fields
        $legacyArea = Area::create([
            'name' => 'Legacy Area',
            'sw_lat' => 42.0,
            'sw_lng' => -5.0,
            'ne_lat' => 43.0,
            'ne_lng' => -4.0,
        ]);

        $this->assertNotNull($jsonArea->bounds);
        $this->assertNotNull($legacyArea->sw_lat);
        $this->assertEquals(2, Area::count());
    }
}

