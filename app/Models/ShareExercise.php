<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ShareExercise extends Pivot
{
    protected $table = 'share_exercises';

    public $incrementing = false;

    protected $fillable = [
        'serial_id',
        'exercise_id',
    ];

    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}