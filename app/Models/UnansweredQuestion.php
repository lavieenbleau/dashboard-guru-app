<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnansweredQuestion extends Model
{
    protected $table = 'unanswered_questions';

    protected $fillable = [
        'question',
        'keyword',
        'solution_text',
        'count',
    ];
}