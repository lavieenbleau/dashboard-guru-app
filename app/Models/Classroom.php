<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    const MAX_STUDENTS = 45;

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

    /**
     * Get the max students limit from this classroom's serial, or fallback to constant
     */
    public function getMaxStudents(): int
    {
        if ($this->serial) {
            return $this->serial->getMaxStudentsPerClass();
        }
        return self::MAX_STUDENTS;
    }

    /**
     * Check if classroom has reached maximum capacity
     */
    public function isFull(): bool
    {
        return $this->students()->count() >= $this->getMaxStudents();
    }

    /**
     * Check if classroom exceeds maximum capacity (legacy data)
     */
    public function isOverCapacity(): bool
    {
        return $this->students()->count() > $this->getMaxStudents();
    }

    /**
     * Get remaining capacity
     */
    public function remainingCapacity(): int
    {
        return max(0, $this->getMaxStudents() - $this->students()->count());
    }
}