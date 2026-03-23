<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['name'];

    public function teachers() {
        return $this->belongsToMany(Teacher::class, 'course_teacher');
    }

    public function types() {
        return $this->belongsToMany(CourseType::class, 'course_course_type');
    }
}
