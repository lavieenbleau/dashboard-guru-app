<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionCategory extends Model
{
    protected $table = 'question_categories';

    protected $fillable = [
        'name',
        'level',
        'solution_text',
        'guide_file',
        'guide_video',
        'category_status',
    ];

    public function csRooms()
    {
        return $this->hasMany(CsRoom::class, 'question_categories_id');
    }

    public function csLogs()
    {
        return $this->hasMany(CsLog::class, 'question_categories_id');
    }
}