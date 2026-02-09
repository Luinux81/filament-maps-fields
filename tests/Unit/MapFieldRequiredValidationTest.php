<?php

namespace Lbcdev\FilamentMapField\Tests\Unit;

use Lbcdev\FilamentMapField\Forms\Components\MapField;
use Lbcdev\FilamentMapField\Tests\TestCase;

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

