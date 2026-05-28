<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseItem extends Model
{
    protected $fillable = [
        'admin_id',
        'user_id',
        'competence_id',
        'exercise_id',
        'exercise_type_id',
        'exercise_model_id',
        'exercise_choice',
        'exercise_number',
        'question',
        'selection',
        'answer',
        'is_user'
    ];

    protected $casts = [
        'selection' => 'array',
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    public function exerciseType()
    {
        return $this->belongsTo(ExerciseType::class, 'exercise_type_id');
    }

    public function exerciseModel()
    {
        return $this->belongsTo(ExerciseModel::class, 'exercise_model_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function competence()
    {
        return $this->belongsTo(Competence::class, 'competence_id');
    }
}
