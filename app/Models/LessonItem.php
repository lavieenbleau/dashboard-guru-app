<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonItem extends Model
{
    protected $table = 'lesson_items';
    protected $fillable = ['lesson_id','theme_id','subtheme_id','number','title','embed'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }
}