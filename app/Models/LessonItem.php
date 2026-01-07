<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonItem extends Model
{
    protected $table = 'lesson_items';
    protected $fillable = [
        'lesson_id',
        'theme_id',
        'subtheme_id',
        'number',
        'title',
        'description',
        'link',
        'embed',
        'attachment',
        'is_admin',
        'shared_to_classes'
    ];

    protected $casts = [
        'shared_to_classes' => 'array',
        'is_admin' => 'boolean'
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class, 'theme_id');
    }

    public function subtheme()
    {
        return $this->belongsTo(Subtheme::class, 'subtheme_id');
    }
}