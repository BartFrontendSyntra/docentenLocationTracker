<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['street', 'house_number', 'city_id', 'location_data'];

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function teacher() {
        return $this->hasOne(Teacher::class);
    }
}
