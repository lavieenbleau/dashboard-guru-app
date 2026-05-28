<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competence extends Model
{
    protected $fillable = [
        'lesson_id',
        'mapel_id',
        'point',
        'description',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }
}