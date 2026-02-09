<?php

namespace Lbcdev\FilamentMapField\Tests\Unit;

use Lbcdev\FilamentMapField\Forms\Components\MapBoundsField;
use Lbcdev\FilamentMapField\Tests\TestCase;

class MapBoundsFieldRequiredValidationTest extends TestCase
{
    /** @test */
    public function it_can_be_marked_as_required(): void
    {
        $field = MapBoundsField::make('limites')
            ->southWestLat('limites.latitud_min')
            ->southWestLng('limites.longitud_min')
            ->northEastLat('limites.latitud_max')
            ->northEastLng('limites.longitud_max')
            ->required();

        $this->assertTrue($field->isRequired());
    }

    /** @test */
    public function it_is_not_required_by_default(): void
    {
        $field = MapBoundsField::make('limites')
            ->southWestLat('limites.latitud_min')
            ->southWestLng('limites.longitud_min')
            ->northEastLat('limites.latitud_max')
            ->northEastLng('limites.longitud_max');

        $this->assertFalse($field->isRequired());
    }

    /** @test */
    public function it_has_validation_rule_when_required(): void
    {
        $field = MapBoundsField::make('limites')
            ->southWestLat('limites.latitud_min')
            ->southWestLng('limites.longitud_min')
            ->northEastLat('limites.latitud_max')
            ->northEastLng('limites.longitud_max')
            ->required();

        $rules = $field->getValidationRules();
        
        $this->assertNotEmpty($rules);
        $this->assertContains('required', $rules);
    }
}

