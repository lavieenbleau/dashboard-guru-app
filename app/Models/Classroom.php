<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = [
        'serial_id', 'name', 'grade', 'code'
    ];

    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_classroom');
    }

    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'exercise_classroom');
    }
}