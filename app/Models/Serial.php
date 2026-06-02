<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serial extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'serial',
        'paket',
        'max_students_per_class',
        'active',
        'expired_at',
    ];

    /**
     * Get max students per class (configurable per serial, default 45)
     */
    public function getMaxStudentsPerClass(): int
    {
        return (int) ($this->max_students_per_class ?? 45);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function exercisePoints()
    {
        return $this->hasMany(ExercisePoint::class);
    }

    public function onlineMeetings()
    {
        return $this->hasMany(OnlineMeeting::class);
    }
}