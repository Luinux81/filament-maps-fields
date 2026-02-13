<?php

namespace LBCDev\FilamentMapsFields\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Test model for JSON Mode testing
 * 
 * This model uses a JSON field 'location' to store coordinates
 * in the format: {latitude: X, longitude: Y}
 */
class Place extends Model
{
    protected $table = 'places';

    protected $fillable = [
        'name',
        'location',
    ];

    protected $casts = [
        'location' => 'array',
    ];

    public $timestamps = false;
}

