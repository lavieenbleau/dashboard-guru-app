<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $table = 'mapels';
    
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'mapel_id');
    }
}