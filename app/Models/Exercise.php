<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use SoftDeletes;
    protected $table = 'exercises';
    protected $fillable = [
        'lesson_id',
        'serial_id',
        'exercise_type_id',
        'title',
        'time_limit',
        'is_admin',
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

    public function sharedSerials()
    {
        return $this->belongsToMany(Serial::class, 'share_exercises', 'exercise_id', 'serial_id');
    }

    public function getSharedToClassesAttribute()
    {
        return $this->sharedSerials()->pluck('serial_id')->toJson();
    }
}