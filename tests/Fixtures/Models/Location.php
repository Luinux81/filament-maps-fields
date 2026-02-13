<?php

namespace LBCDev\FilamentMapsFields\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Test model with separate latitude/longitude fields (traditional mode)
 */
class Location extends Model
{
    protected $table = 'locations';

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public $timestamps = false;
}
