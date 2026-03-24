<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseType extends Model
{
   protected $fillable = [
        'name'
    ];

    public function courses()
    {
        // explicit specification because i'm not sure if course is before or after course_type in the pivot table name
        return $this->belongsToMany(Course::class, 'course_course_type');
    }
}
