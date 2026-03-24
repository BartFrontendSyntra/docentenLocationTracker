<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'name'
    ];


    public function teachers()
    {
        return $this->belongsToMany(Teacher::class);
    }
}
