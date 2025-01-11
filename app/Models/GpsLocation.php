<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GpsLocation extends Model
{
    protected $fillable = [
        'latitude',
        'longitude',
        'device_id'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float'
    ];
}
