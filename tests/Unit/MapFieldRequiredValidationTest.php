<?php

namespace LBCDev\FilamentMapsFields\Tests\Unit;

use LBCDev\FilamentMapsFields\Forms\Components\MapField;
use LBCDev\FilamentMapsFields\Tests\TestCase;

class MapFieldRequiredValidationTest extends TestCase
{
    /** @test */
    public function it_can_be_marked_as_required(): void
    {
        $field = MapField::make('ubicacion')
            ->latitude('ubicacion.latitud')
            ->longitude('ubicacion.longitud')
            ->required();

        $this->assertTrue($field->isRequired());
    }

    /** @test */
    public function it_is_not_required_by_default(): void
    {
        $field = MapField::make('ubicacion')
            ->latitude('ubicacion.latitud')
            ->longitude('ubicacion.longitud');

        $this->assertFalse($field->isRequired());
    }

    /** @test */
    public function it_has_validation_rule_when_required(): void
    {
        $field = MapField::make('ubicacion')
            ->latitude('ubicacion.latitud')
            ->longitude('ubicacion.longitud')
            ->required();

        $rules = $field->getValidationRules();

        $this->assertNotEmpty($rules);
        $this->assertContains('required', $rules);
    }
}
