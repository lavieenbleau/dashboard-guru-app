<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $table = 'lessons';

    protected $fillable = [
        'mapel_id',
        'name',
        'description',
        'questions',
        'file',
        'grade',
        'semester',
        'category',
        'deadline',
    ];

    // Category constants
    const CATEGORY_MATERI = 1;
    const CATEGORY_TUGAS = 2;
    const CATEGORY_SOAL = 3;
    const CATEGORY_ONLINE_CLASS = 4;
    const CATEGORY_LAPORAN_HARIAN = 5;

    public function item()
    {
        return $this->hasMany(LessonItem::class, 'lesson_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function themes()
    {
        return $this->hasMany(Theme::class, 'lesson_id');
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'lesson_classroom');
    }
}