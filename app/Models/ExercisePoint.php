<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExercisePoint extends Model
{
    use HasFactory;

    protected $table = 'exercise_points';

    protected $fillable = [
        'serial_id',
        'exercise_id',
        'student_id',
        'answer',
        'competence_point',
        'exercise_point',
    ];

    /**
     * Relationship to Exercise
     */
    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    /**
     * Relationship to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship to Serial
     */
    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }
}