<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $table = 'exercises';
    protected $fillable = [
        'lesson_id',
        'serial_id',
        'exercise_type_id',
        'title',
        'description',
        'is_admin',
        'shared_to_classes',
    ];

    protected $casts = [
        'shared_to_classes' => 'array',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function exerciseType()
    {
        return $this->belongsTo(ExerciseType::class);
    }

    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }

    public function exerciseItems()
    {
        return $this->hasMany(ExerciseItem::class, 'exercise_id');
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'exercise_classroom');
    }
}