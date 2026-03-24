<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'postal_code',
        'name'
    ];
    public function addresses() {
    return $this->hasMany(Address::class);
}
}
