<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $table = 'mapels';

    protected $fillable = [
        'name'
    ];

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'mapel_id');
    }

    public function competences()
    {
        return $this->hasMany(Competence::class, 'mapel_id');
    }
}