<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class Address extends Model
{
    use HasSpatial;
    protected $fillable = ['street', 'house_number', 'city_id', 'location_data'];

    protected $casts = [
        'location_data' => Point::class,
    ];

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function teacher() {
        return $this->hasOne(Teacher::class);
    }
}
