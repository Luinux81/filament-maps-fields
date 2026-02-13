<?php

namespace LBCDev\FilamentMapsFields\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'name',
        'sw_lat',
        'sw_lng',
        'ne_lat',
        'ne_lng',
        'bounds',
    ];

    protected $casts = [
        'bounds' => 'array',
    ];
}
