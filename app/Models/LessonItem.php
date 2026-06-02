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
        'admin_id',
        'number',
        'title',
        'embed',
    ];

    protected $casts = [
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

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function getIsAdminAttribute()
    {
        return !is_null($this->admin_id);
    }
}