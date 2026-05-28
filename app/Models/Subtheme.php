<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtheme extends Model
{
    protected $table = 'subthemes';
    
    protected $fillable = ['lesson_id', 'theme_id', 'subtheme', 'name'];

    public function theme()
    {
        return $this->belongsTo(Theme::class, 'theme_id');
    }

    public function lessonItems()
    {
        return $this->hasMany(LessonItem::class, 'subtheme_id');
    }
}