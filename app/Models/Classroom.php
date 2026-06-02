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
     * Check if classroom has reached maximum capacity
     */
    public function isFull(): bool
    {
        return $this->students()->count() >= self::MAX_STUDENTS;
    }

    /**
     * Check if classroom exceeds maximum capacity (legacy data)
     */
    public function isOverCapacity(): bool
    {
        return $this->students()->count() > self::MAX_STUDENTS;
    }

    /**
     * Get remaining capacity
     */
    public function remainingCapacity(): int
    {
        return max(0, self::MAX_STUDENTS - $this->students()->count());
    }
}