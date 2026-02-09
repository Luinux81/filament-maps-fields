<?php

namespace Lbcdev\FilamentMapField\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Test model with JSON field for nested coordinates (new JSON mode)
 */
class Store extends Model
{
    protected $table = 'stores';

    protected $fillable = [
        'name',
        'ubicacion',
    ];

    protected $casts = [
        'ubicacion' => 'array',
    ];

    public $timestamps = false;

    /**
     * Accessor for latitude from JSON field
     */
    public function getLatitudAttribute(): ?float
    {
        return isset($this->ubicacion['latitud']) ? (float) $this->ubicacion['latitud'] : null;
    }

    /**
     * Accessor for longitude from JSON field
     */
    public function getLongitudAttribute(): ?float
    {
        return isset($this->ubicacion['longitud']) ? (float) $this->ubicacion['longitud'] : null;
    }
}

