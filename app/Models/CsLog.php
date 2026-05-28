<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsLog extends Model
{
    protected $table = 'cs_logs';

    protected $fillable = [
        'room_code',
        'question_categories_id',
        'admin_id',
        'completion_time',
        'resolution_by',
        'rating',
        'review',
        'notes',
    ];

    protected $casts = [
        'completion_time' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function questionCategory()
    {
        return $this->belongsTo(QuestionCategory::class, 'question_categories_id');
    }

    public function room()
    {
        return $this->belongsTo(CsRoom::class, 'room_code', 'room_code');
    }
}