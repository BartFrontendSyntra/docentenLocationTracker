<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email',
        'company_number', 'telephone', 'cellphone', 'address_id'
    ];

    public function address() {
        return $this->belongsTo(Address::class);
    }

    public function courses() {
        return $this->belongsToMany(Course::class, 'course_teacher');
    }

    public function certificates() {
        return $this->belongsToMany(Certificate::class, 'certificate_teacher');
    }
}
