<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizActivityLog extends Model
{
    protected $connection = 'log_db';
    
    protected $table = 'quiz_activity_logs';

    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'exercise_id',
        'event_type',
        'duration_seconds',
        'suspicious_flag',
        'device_info',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'suspicious_flag' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
